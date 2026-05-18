<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Request payment URL from payment gateway
     */
    public function createPaymentUrl(Order $order): string
    {
        // Check if Midtrans or another gateway is configured
        $merchantId = env('MIDTRANS_MERCHANT_ID');
        $clientKey = env('MIDTRANS_CLIENT_KEY');
        $serverKey = env('MIDTRANS_SERVER_KEY');

        if ($merchantId && $serverKey) {
            return $this->requestMidtransPaymentUrl($order, $serverKey);
        }

        // Fallback: Return a simulated payment URL for seamless local testing
        return route('payment.mock', ['reference' => $order->reference_number]);
    }

    /**
     * Handle real Midtrans Snap token request
     */
    protected function requestMidtransPaymentUrl(Order $order, string $serverKey): string
    {
        $isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        $baseUrl = $isProduction 
            ? 'https://app.midtrans.com/snap/v1/transactions' 
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        try {
            $payload = [
                'transaction_details' => [
                    'order_id' => $order->reference_number,
                    'gross_amount' => (int) $order->amount,
                ],
                'customer_details' => [
                    'first_name' => $order->user->name,
                    'email' => $order->user->email,
                ],
                'item_details' => [
                    [
                        'id' => $order->course_id,
                        'price' => (int) $order->amount,
                        'quantity' => 1,
                        'name' => substr($order->course->title, 0, 50),
                    ]
                ]
            ];

            $response = Http::withBasicAuth($serverKey, '')
                ->acceptJson()
                ->post($baseUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['redirect_url'])) {
                    return $data['redirect_url'];
                }
            }

            Log::error('Midtrans Snap request failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage());
        }

        // Graceful fallback to mock URL if real API call fails
        return route('payment.mock', ['reference' => $order->reference_number]);
    }
}
