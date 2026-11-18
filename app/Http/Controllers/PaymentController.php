use Midtrans\Notification;

    /**
     * ACARA 38: Handle Webhook Notification dari Midtrans
     */
    public function notificationCallback(Request $request)
    {
        try {
            $notif = new Notification();

            $transactionStatus = $notif->transaction_status;
            $type = $notif->payment_type;
            $orderId = $notif->order_id;
            $fraud = $notif->fraud_status;

            // Logika Perubahan Status Transaksi di Database
            if ($transactionStatus == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        // Status: Challenge Fraud
                    } else {
                        // Status: Lunas / Success
                    }
                }
            } else if ($transactionStatus == 'settlement') {
                // Status: Lunas / Success (Pembayaran Berhasil)
                // Contoh: Transaction::where('order_id', $orderId)->update(['status' => 'paid']);
            } else if ($transactionStatus == 'pending') {
                // Status: Menunggu Pembayaran
            } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                // Status: Gagal / Kadaluarsa
            }

            return response()->json(['status' => 'success', 'message' => 'Notification processed']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
