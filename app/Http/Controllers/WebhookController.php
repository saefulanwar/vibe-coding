<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected EnrollmentService $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Handle Midtrans/Payment Gateway Webhook notification
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('Payment Webhook received:', $payload);

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $order = Order::where('reference_number', $orderId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Validate Midtrans signature
        $serverKey = env('MIDTRANS_SERVER_KEY');
        if ($serverKey && $signatureKey) {
            $inputString = $orderId . $statusCode . $grossAmount . $serverKey;
            $localSignature = hash('sha512', $inputString);

            if ($localSignature !== $signatureKey) {
                Log::warning("Invalid webhook signature for Order: {$orderId}");
                return response()->json(['message' => 'Invalid signature'], 403);
            }
        }

        // Update status according to transaction status
        $orderStatus = 'pending';
        $isPaid = false;

        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $orderStatus = 'paid';
                $isPaid = true;
                break;
            case 'pending':
                $orderStatus = 'pending';
                break;
            case 'deny':
            case 'expire':
            case 'cancel':
                $orderStatus = 'failed';
                break;
        }

        $order->update([
            'status' => $orderStatus,
            'gateway_response' => $payload,
        ]);

        if ($isPaid) {
            $this->enrollmentService->activateOrderAccess($order);
        }

        return response()->json(['message' => 'Webhook processed successfully']);
    }
}
