<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KasirController extends Controller
{
    /* ============================================
       HALAMAN KASIR (TRANSAKSI BARU / LANJUTKAN)
    ============================================ */
    public function index(Request $request)
    {
        $resume = null;

        if ($request->filled('lanjut')) {
            $held = Transaction::with('details')
                ->where('status', 'held')
                ->findOrFail($request->query('lanjut'));

            // Diskon disimpan sebagai total rupiah, jadi dimuat ke kolom Diskon (Rp)
            $resume = [
                'id'              => $held->id,
                'customer_id'     => $held->customer_id,
                'discount_amount' => $held->discount,
                'tax'             => $held->tax,
                'other_fee'       => $held->other_fee,
                'items'           => $held->details
                    ->whereNotNull('product_id')
                    ->map(fn ($d) => ['id' => $d->product_id, 'qty' => $d->qty])
                    ->values(),
            ];

            $transactionNumber = $held->transaction_number;
        } else {
            $transactionNumber = Transaction::generateNumber();
        }

        return view('kasir.index', [
            'products'          => Product::orderBy('name')->get(['id', 'code', 'barcode', 'name', 'price', 'stock']),
            'customers'         => Customer::orderBy('name')->get(['id', 'name', 'phone', 'member_no', 'type']),
            'resume'            => $resume,
            'transactionNumber' => $transactionNumber,
            'heldCount'         => Transaction::where('status', 'held')->count(),
            'cashierName'       => Auth::user()?->name ?? 'Admin',
        ]);
    }

    /* ============================================
       BAYAR & CETAK
    ============================================ */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validateCart($request);

        $transaction = $this->persist($data, 'paid');

        return response()->json([
            'redirect' => route('kasir.receipt', ['transaction' => $transaction->id, 'print' => 1]),
        ]);
    }

    /* ============================================
       TAHAN TRANSAKSI
    ============================================ */
    public function hold(Request $request): JsonResponse
    {
        $data = $this->validateCart($request);

        $transaction = $this->persist($data, 'held');

        return response()->json([
            'message'     => "Transaksi {$transaction->transaction_number} berhasil ditahan.",
            'held_count'  => Transaction::where('status', 'held')->count(),
            'next_number' => Transaction::generateNumber(),
        ]);
    }

    public function held()
    {
        $transactions = Transaction::withCount('details')
            ->withSum('details as total_qty', 'qty')
            ->where('status', 'held')
            ->latest()
            ->get();

        return view('kasir.ditahan', compact('transactions'));
    }

    public function destroyHeld(Transaction $transaction)
    {
        abort_unless($transaction->status === 'held', 404);

        $number = $transaction->transaction_number;
        $transaction->delete(); // detail ikut terhapus (cascade)

        return back()->with('success', "Transaksi ditahan {$number} dihapus.");
    }

    /* ============================================
       RIWAYAT & PEMBATALAN
    ============================================ */
    public function history(Request $request)
    {
        $date = $request->query('tanggal', now()->toDateString());

        $transactions = Transaction::with('user')
            ->withSum('details as total_qty', 'qty')
            ->whereIn('status', ['paid', 'cancelled'])
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        return view('kasir.riwayat', compact('transactions', 'date'));
    }

    public function cancel(Transaction $transaction)
    {
        abort_unless($transaction->status === 'paid', 404);

        DB::transaction(function () use ($transaction) {
            // Kembalikan stok barang
            foreach ($transaction->details as $detail) {
                if ($detail->product_id) {
                    Product::whereKey($detail->product_id)->increment('stock', $detail->qty);
                }
            }

            $transaction->update(['status' => 'cancelled']);
        });

        return back()->with('success', "Transaksi {$transaction->transaction_number} dibatalkan dan stok dikembalikan.");
    }

    /* ============================================
       STRUK
    ============================================ */
    public function receipt(Request $request, Transaction $transaction)
    {
        $transaction->load('details', 'user', 'customer');

        return view('kasir.struk', [
            'transaction' => $transaction,
            'size'        => $request->query('ukuran') === '58' ? '58' : '80',
            'autoPrint'   => $request->boolean('print'),
        ]);
    }

    /* ============================================
       TUTUP KASIR
    ============================================ */
    public function closing(Request $request)
    {
        $openingBalance = max(0, (int) $request->query('saldo_awal', 0));

        $paid = Transaction::where('status', 'paid')
            ->whereDate('created_at', now()->toDateString());

        $byMethod = (clone $paid)
            ->selectRaw('payment_method, COUNT(*) as total_transactions, SUM(grand_total) as total')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $cashSales = (int) ($byMethod['tunai']->total ?? 0);

        return view('kasir.tutup', [
            'openingBalance' => $openingBalance,
            'byMethod'       => $byMethod,
            'totalSales'     => (int) $byMethod->sum('total'),
            'totalCount'     => (int) $byMethod->sum('total_transactions'),
            'cashSales'      => $cashSales,
            'closingBalance' => $openingBalance + $cashSales,
            'cancelledCount' => Transaction::where('status', 'cancelled')
                ->whereDate('created_at', now()->toDateString())->count(),
            'methods'        => [
                'tunai' => 'Tunai', 'qris' => 'QRIS', 'debit' => 'Debit',
                'kredit' => 'Kredit', 'ewallet' => 'E-Wallet', 'transfer' => 'Transfer',
            ],
        ]);
    }

    /* ============================================
       HELPER
    ============================================ */
    private function validateCart(Request $request): array
    {
        return $request->validate([
            'transaction_id'   => ['nullable', 'integer'],
            'customer_id'      => ['nullable', 'exists:customers,id'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.id'       => ['required', 'integer', 'exists:products,id'],
            'items.*.qty'      => ['required', 'integer', 'min:1'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount'  => ['nullable', 'numeric', 'min:0'],
            'tax'              => ['nullable', 'numeric', 'min:0'],
            'other_fee'        => ['nullable', 'numeric', 'min:0'],
            'paid_amount'      => ['nullable', 'numeric', 'min:0'],
            'payment_method'   => ['nullable', 'in:tunai,qris,debit,kredit,ewallet,transfer'],
        ]);
    }

    /**
     * Simpan transaksi + detailnya. Harga dan total dihitung ulang di server
     * (tidak mempercayai angka dari browser).
     */
    private function persist(array $data, string $status): Transaction
    {
        return DB::transaction(function () use ($data, $status) {
            // Gabungkan baris dengan produk yang sama
            $qtyById = collect($data['items'])
                ->groupBy('id')
                ->map(fn ($rows) => (int) $rows->sum('qty'));

            $products = Product::whereIn('id', $qtyById->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $lines = [];

            foreach ($qtyById as $id => $qty) {
                $product = $products[$id];

                if ($status === 'paid' && $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup (sisa {$product->stock}).",
                    ]);
                }

                $lineSubtotal = $product->price * $qty;
                $subtotal += $lineSubtotal;

                $lines[] = [
                    'product_id'   => $product->id,
                    'product_code' => $product->code,
                    'product_name' => $product->name,
                    'price'        => $product->price,
                    'qty'          => $qty,
                    'discount'     => 0,
                    'subtotal'     => $lineSubtotal,
                ];
            }

            $percent  = (float) ($data['discount_percent'] ?? 0);
            $discount = (int) round($subtotal * ($percent / 100) + (float) ($data['discount_amount'] ?? 0));
            $tax      = (int) round((float) ($data['tax'] ?? 0));
            $otherFee = (int) round((float) ($data['other_fee'] ?? 0));
            $grand    = max(0, $subtotal - $discount + $tax + $otherFee);

            $paid = 0;
            if ($status === 'paid') {
                $paid = (int) round((float) ($data['paid_amount'] ?? 0));

                if ($paid < $grand) {
                    throw ValidationException::withMessages([
                        'paid_amount' => 'Uang pembayaran masih kurang.',
                    ]);
                }
            }

            // Lanjutkan transaksi yang ditahan, atau buat baru
            if (! empty($data['transaction_id'])) {
                $transaction = Transaction::where('status', 'held')
                    ->lockForUpdate()
                    ->findOrFail($data['transaction_id']);
            } else {
                $transaction = new Transaction([
                    'transaction_number' => Transaction::generateNumber(),
                ]);
            }

            $transaction->fill([
                'user_id'        => Auth::id(),
                'customer_id'    => $data['customer_id'] ?? null,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'tax'            => $tax,
                'other_fee'      => $otherFee,
                'grand_total'    => $grand,
                'paid_amount'    => $paid,
                'change_amount'  => $status === 'paid' ? $paid - $grand : 0,
                'payment_method' => $data['payment_method'] ?? null,
                'status'         => $status,
            ])->save();

            $transaction->details()->delete();
            $transaction->details()->createMany($lines);

            // Stok hanya berkurang saat transaksi dibayar
            if ($status === 'paid') {
                foreach ($lines as $line) {
                    Product::whereKey($line['product_id'])->decrement('stock', $line['qty']);
                }
            }

            return $transaction;
        });
    }
}
