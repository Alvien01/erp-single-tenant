<?php

namespace App\Http\Controllers;

use App\Models\PosTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Handle incoming Midtrans payment notification (webhook).
     * Midtrans sends POST to this endpoint when payment status changes.
     */
    public function handle(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');

        // Verify signature key from Midtrans
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $expectedSignature) {
            Log::warning('Midtrans Webhook: Invalid signature', ['order_id' => $orderId]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status', 'accept');
        $paymentType = $request->input('payment_type');
        $transactionId = $request->input('transaction_id');

        Log::info('Midtrans Webhook Received', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'payment_type' => $paymentType,
        ]);

        // Find the POS transaction by the midtrans order_id stored in qris_reference
        $posTransaction = PosTransaction::where('transaction_number', $orderId)->first();

        if (!$posTransaction) {
            Log::warning('Midtrans Webhook: Transaction not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Update transaction based on status
        if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
            $posTransaction->update([
                'qris_reference' => $transactionId,
                'status' => 'completed',
            ]);
            Log::info("Midtrans Webhook: Payment settled for {$orderId}");
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $posTransaction->update([
                'status' => 'voided',
                'notes' => "Payment {$transactionStatus} via Midtrans",
            ]);
            Log::info("Midtrans Webhook: Payment {$transactionStatus} for {$orderId}");
        } elseif ($transactionStatus === 'pending') {
            Log::info("Midtrans Webhook: Payment still pending for {$orderId}");
        }

        return response()->json(['message' => 'OK']);
    }
}
