<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class ReceiptController extends Controller
{
    /**
     * ACARA 35: Tes Cetak Dasar ke File (storage/app/public/receipt.txt)
     */
    public function printTest()
    {
        try {
            $filePath = storage_path('app/public/receipt.txt');

            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            $connector = new FilePrintConnector($filePath);
            $printer = new Printer($connector);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("TEST PRINT POS KASIR\n");
            $printer->setEmphasis(false);
            $printer->text("Jl. Mawar No. 123\n");
            $printer->text("==============================\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Barang A      2 x 5.000  10.000\n");
            $printer->text("Barang B      1 x 10.000 10.000\n");
            $printer->text("------------------------------\n");
            $printer->text("Total:                 20.000\n");
            $printer->text("==============================\n");

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima kasih telah berbelanja!\n");

            $printer->cut();
            $printer->close();

            return response()->json([
                'status' => 'success',
                'message' => 'Struk tes berhasil dicetak ke file',
                'file_path' => $filePath
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ACARA 36: Studi Kasus Cetak Struk Transaksi POS Kasir ke File
     */
    public function printTransaction(Request $request)
    {
        try {
            $filePath = storage_path('app/public/receipt.txt');

            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            $connector = new FilePrintConnector($filePath);
            $printer = new Printer($connector);

            // 1. Header Struk
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("POS KASIR UTAMA\n");
            $printer->setEmphasis(false);
            $printer->text("Tanggal: " . date('d-m-Y H:i:s') . "\n");
            $printer->text("--------------------------------\n");

            // 2. Daftar Item Transaksi
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $items = $request->input('items', [
                ['name' => 'Produk A', 'qty' => 2, 'price' => 5000],
                ['name' => 'Produk B', 'qty' => 1, 'price' => 10000],
            ]);

            $subtotal = 0;
            foreach ($items as $item) {
                $itemTotal = $item['qty'] * $item['price'];
                $subtotal += $itemTotal;

                $printer->text($item['name'] . "\n");
                $printer->text("   " . $item['qty'] . " x " . number_format($item['price']) . " = " . number_format($itemTotal) . "\n");
            }

            // 3. Diskon & Total
            $diskon = $request->input('diskon', 0);
            $total = $subtotal - $diskon;

            $printer->text("--------------------------------\n");
            $printer->text("Subtotal : Rp " . number_format($subtotal) . "\n");
            $printer->text("Diskon   : Rp " . number_format($diskon) . "\n");
            $printer->setEmphasis(true);
            $printer->text("TOTAL    : Rp " . number_format($total) . "\n");
            $printer->setEmphasis(false);
            $printer->text("================================\n");

            // 4. Footer Struk
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima Kasih Atas Kunjungan Anda!\n\n");

            $printer->cut();
            $printer->close();

            return response()->json([
                'status' => 'success',
                'message' => 'Struk transaksi berhasil dicetak ke file',
                'file_path' => $filePath
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
