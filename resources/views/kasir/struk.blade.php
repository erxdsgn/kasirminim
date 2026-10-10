<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $transaction->transaction_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Courier New", monospace;
            font-size: 12px;
            color: #000;
            background: #e9ecef;
        }

        .toolbar {
            display: flex;
            gap: 8px;
            justify-content: center;
            padding: 12px;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .toolbar a, .toolbar button {
            padding: 8px 14px;
            font-size: 13px;
            color: #212529;
            text-decoration: none;
            background: #fff;
            border: 1px solid #ced4da;
            border-radius: 4px;
            cursor: pointer;
        }

        .toolbar .primary { color: #fff; background: #0d6efd; border-color: #0d6efd; }

        .receipt {
            margin: 0 auto 20px;
            padding: 12px;
            background: #fff;
        }

        .receipt.size-80 { width: 80mm; }
        .receipt.size-58 { width: 58mm; font-size: 11px; }

        .center { text-align: center; }
        .bold { font-weight: 700; }
        .store { font-size: 15px; font-weight: 700; }
        hr { margin: 6px 0; border: 0; border-top: 1px dashed #000; }

        .row { display: flex; justify-content: space-between; gap: 8px; }
        .item-name { margin-top: 4px; }

        .total-row { font-size: 14px; font-weight: 700; }

        @media print {
            @page { size: {{ $size }}mm auto; margin: 0; }
            body { background: #fff; }
            .toolbar { display: none; }
            .receipt { margin: 0; width: 100%; padding: 4mm; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button class="primary" onclick="window.print()">Cetak</button>
    <a href="{{ route('kasir.receipt', ['transaction' => $transaction->id, 'ukuran' => $size === '80' ? '58' : '80']) }}">
        Ubah ke {{ $size === '80' ? '58' : '80' }} mm
    </a>
    <a href="{{ route('kasir.index') }}">Transaksi Baru</a>
</div>

@php
    $rp = fn ($value) => number_format($value, 0, ',', '.');
    $methods = ['tunai' => 'Tunai', 'qris' => 'QRIS', 'debit' => 'Debit', 'kredit' => 'Kredit', 'ewallet' => 'E-Wallet', 'transfer' => 'Transfer'];
@endphp

<div class="receipt size-{{ $size }}">
    <div class="center">
        <div class="store">TOKO RETAIL MAKMUR</div>
        <div>Jl. Contoh No. 123 • Jember</div>
        <div>Telp. 0812-xxxx-xxxx</div>
    </div>

    <hr>

    <div class="row"><span>No</span><span>{{ $transaction->transaction_number }}</span></div>
    <div class="row"><span>Tanggal</span><span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span></div>
    <div class="row"><span>Kasir</span><span>{{ $transaction->user->name ?? 'Admin' }}</span></div>
    <div class="row"><span>Pelanggan</span><span>{{ $transaction->customer->name ?? 'Umum' }}</span></div>

    <hr>

    @foreach ($transaction->details as $detail)
        <div class="item-name">{{ $detail->product_name }}</div>
        <div class="row">
            <span>{{ $detail->qty }} x {{ $rp($detail->price) }}</span>
            <span>{{ $rp($detail->subtotal) }}</span>
        </div>
    @endforeach

    <hr>

    <div class="row"><span>Subtotal</span><span>{{ $rp($transaction->subtotal) }}</span></div>
    @if ($transaction->discount > 0)
        <div class="row"><span>Diskon</span><span>-{{ $rp($transaction->discount) }}</span></div>
    @endif
    @if ($transaction->tax > 0)
        <div class="row"><span>PPN</span><span>{{ $rp($transaction->tax) }}</span></div>
    @endif
    @if ($transaction->other_fee > 0)
        <div class="row"><span>Biaya Lain</span><span>{{ $rp($transaction->other_fee) }}</span></div>
    @endif

    <hr>

    <div class="row total-row"><span>TOTAL</span><span>{{ $rp($transaction->grand_total) }}</span></div>
    <div class="row"><span>Bayar ({{ $methods[$transaction->payment_method] ?? '-' }})</span><span>{{ $rp($transaction->paid_amount) }}</span></div>
    <div class="row"><span>Kembali</span><span>{{ $rp($transaction->change_amount) }}</span></div>

    @if ($transaction->status === 'cancelled')
        <hr>
        <div class="center bold">*** TRANSAKSI DIBATALKAN ***</div>
    @endif

    <hr>

    <div class="center">
        Terima kasih atas kunjungan Anda<br>
        Barang yang sudah dibeli tidak dapat ditukar
    </div>
</div>

@if ($autoPrint)
    <script>
        window.addEventListener('load', function () { window.print(); });
    </script>
@endif

</body>
</html>
