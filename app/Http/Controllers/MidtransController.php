<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');
        Config::$isSanitized = config('midtrans.isSanitized');
        Config::$is3ds = config('midtrans.is3ds');
    }

    /**
     * ACARA 37: Generate Snap Token
     */
    public function checkout(Request $request)
    {
        $orderId = 'ORDER-' . time();
        $price = $request->input('gross_amount', 50000); // Menerima total belanja dinamis dari frontend

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $price,
            ],
            'customer_details' => [
                'first_name' => $request->input('customer_name', 'Pelanggan POS'),
                'email' => 'john@example.com',
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'snap_token' => $snapToken,
            'order_id' => $orderId
        ]);
    }

    /**
     * ACARA 38: Webhook Notification Handler
     */
    public function notification(Request $request)
    {
        $notif = new \Midtrans\Notification();
        $transaction = $notif->transaction_status;
        $order_id = $notif->order_id;

        if ($transaction == 'settlement') {
            // Update status pesanan di database jadi "PAID"
        } elseif ($transaction == 'pending') {
            // Status pending
        } elseif ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
            // Gagal bayar
        }

        return response()->json(['message' => 'Notification handled']);
    }
}
