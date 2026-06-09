<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');
        $isProduction = config('services.midtrans.is_production', false);
        $this->baseUrl = $isProduction
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    /**
     * Create a QRIS charge via Midtrans Core API.
     *
     * @param string $orderId  Unique order/transaction ID
     * @param int    $amount   Gross amount in IDR (integer, no decimals)
     * @param array  $itemDetails  Optional line items
     * @return array  ['success' => bool, 'qr_string' => string|null, 'qr_url' => string|null, 'transaction_id' => string|null, 'error' => string|null]
     */
    public function createQrisCharge(string $orderId, int $amount, array $itemDetails = []): array
    {
        $payload = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'qris' => [
                'acquirer' => 'gopay',
            ],
        ];

        if (!empty($itemDetails)) {
            $payload['item_details'] = $itemDetails;
        }

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->acceptJson()
                ->post("{$this->baseUrl}/v2/charge", $payload);

            $data = $response->json();

            Log::info('Midtrans QRIS Charge Response', ['order_id' => $orderId, 'status_code' => $response->status(), 'body' => $data]);

            if ($response->successful() && isset($data['actions'])) {
                // Extract QR string and image URL from actions
                $qrString = null;
                $qrUrl = null;

                foreach ($data['actions'] as $action) {
                    if ($action['name'] === 'generate-qr-code') {
                        $qrUrl = $action['url'];
                    }
                }

                // The qr_string is at the root level of the response
                $qrString = $data['qr_string'] ?? null;

                return [
                    'success' => true,
                    'qr_string' => $qrString,
                    'qr_url' => $qrUrl,
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'order_id' => $data['order_id'] ?? $orderId,
                    'expiry_time' => $data['expiry_time'] ?? null,
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'qr_string' => null,
                'qr_url' => null,
                'transaction_id' => null,
                'order_id' => $orderId,
                'expiry_time' => null,
                'error' => $data['status_message'] ?? ($data['error_messages'][0] ?? 'Unknown error from Midtrans'),
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans QRIS Charge Error', ['order_id' => $orderId, 'error' => $e->getMessage()]);

            return [
                'success' => false,
                'qr_string' => null,
                'qr_url' => null,
                'transaction_id' => null,
                'order_id' => $orderId,
                'expiry_time' => null,
                'error' => 'Gagal terhubung ke payment gateway: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check the status of a transaction via Midtrans Core API.
     *
     * @param string $orderId  The order_id used during charge
     * @return array  ['status' => string, 'transaction_id' => string|null, 'raw' => array]
     */
    public function checkTransactionStatus(string $orderId): array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->acceptJson()
                ->get("{$this->baseUrl}/v2/{$orderId}/status");

            $data = $response->json();

            Log::info('Midtrans Status Check', ['order_id' => $orderId, 'status' => $data['transaction_status'] ?? 'unknown']);

            $transactionStatus = $data['transaction_status'] ?? 'unknown';
            $fraudStatus = $data['fraud_status'] ?? 'accept';

            // Determine if payment is settled
            $isPaid = false;
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                if ($transactionStatus === 'capture' && $fraudStatus !== 'accept') {
                    $isPaid = false;
                } else {
                    $isPaid = true;
                }
            }

            return [
                'status' => $transactionStatus,
                'is_paid' => $isPaid,
                'transaction_id' => $data['transaction_id'] ?? null,
                'payment_type' => $data['payment_type'] ?? null,
                'raw' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Status Check Error', ['order_id' => $orderId, 'error' => $e->getMessage()]);

            return [
                'status' => 'error',
                'is_paid' => false,
                'transaction_id' => null,
                'payment_type' => null,
                'raw' => [],
            ];
        }
    }

    /**
     * Cancel a pending Midtrans transaction.
     *
     * @param string $orderId
     * @return bool
     */
    public function cancelTransaction(string $orderId): bool
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->acceptJson()
                ->post("{$this->baseUrl}/v2/{$orderId}/cancel");

            $data = $response->json();
            Log::info('Midtrans Cancel', ['order_id' => $orderId, 'response' => $data]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Midtrans Cancel Error', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return false;
        }
    }
}
