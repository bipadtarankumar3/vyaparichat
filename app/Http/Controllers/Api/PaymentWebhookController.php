<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Billing\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected BillingService $billingService
    ) {}

    /**
     * Handle Stripe Webhook events (e.g. checkout.session.completed).
     */
    public function stripeWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        $event = $payload['type'] ?? '';

        Log::info("Stripe Webhook Event Received: {$event}");

        if ($event === 'checkout.session.completed' || !empty($payload['data']['object'])) {
            $object = $payload['data']['object'] ?? $payload;
            $clientRef = $object['client_reference_id'] ?? ($object['metadata']['transaction_reference'] ?? ($object['id'] ?? null));
            $amount = ($object['amount_total'] ?? 0) / 100;
            $currency = strtoupper($object['currency'] ?? 'INR');

            if ($clientRef) {
                $this->billingService->handlePaymentSuccess(
                    'stripe',
                    $clientRef,
                    (float) $amount,
                    $currency,
                    $object
                );
            }
        }

        return response()->json(['received' => true]);
    }

    /**
     * Handle Razorpay Webhook events (e.g. payment.captured).
     */
    public function razorpayWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        $event = $payload['event'] ?? '';

        Log::info("Razorpay Webhook Event Received: {$event}");

        if ($event === 'payment.captured' || isset($payload['payload']['payment']['entity'])) {
            $payment = $payload['payload']['payment']['entity'] ?? $payload;
            $orderId = $payment['order_id'] ?? ($payment['id'] ?? null);
            $amount = ($payment['amount'] ?? 0) / 100;
            $currency = strtoupper($payment['currency'] ?? 'INR');

            if ($orderId) {
                $this->billingService->handlePaymentSuccess(
                    'razorpay',
                    $orderId,
                    (float) $amount,
                    $currency,
                    $payment
                );
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
