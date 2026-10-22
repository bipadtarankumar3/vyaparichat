<?php

namespace App\Services\Billing;

use App\Models\ManualPaymentRequest;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BillingService
{
    /**
     * List all public subscription plans.
     */
    public function listPlans(bool $onlyActive = true): Collection
    {
        $query = SubscriptionPlan::query();
        if ($onlyActive) {
            $query->where('is_active', true);
        }

        return $query->orderBy('price_monthly', 'asc')->get();
    }

    /**
     * Get vendor's current active subscription.
     */
    public function getCurrentSubscription(Vendor $vendor): ?Subscription
    {
        return Subscription::with('plan')
            ->where('vendor_id', $vendor->id)
            ->where('status', 'active')
            ->where('ends_at', '>=', now())
            ->latest('ends_at')
            ->first();
    }

    /**
     * Check if vendor is within their plan's limits for a given feature.
     */
    public function checkFeatureLimit(Vendor $vendor, string $featureKey, int $currentUsage = 0): array
    {
        $subscription = $this->getCurrentSubscription($vendor);
        $plan = $subscription?->plan;

        // If no active subscription, return trial or fallback restriction
        if (!$plan) {
            return [
                'allowed' => false,
                'limit' => 0,
                'current' => $currentUsage,
                'reason' => 'No active subscription plan found. Please subscribe to continue.',
            ];
        }

        switch ($featureKey) {
            case 'contacts':
                $limit = $plan->max_contacts;
                $allowed = ($limit === 0) || ($currentUsage < $limit);
                return [
                    'allowed' => $allowed,
                    'limit' => $limit,
                    'current' => $currentUsage,
                    'reason' => $allowed ? null : "Contact limit of {$limit} reached for {$plan->title} plan.",
                ];

            case 'campaigns':
                $limit = $plan->max_campaigns_per_month;
                $allowed = ($limit === 0) || ($currentUsage < $limit);
                return [
                    'allowed' => $allowed,
                    'limit' => $limit,
                    'current' => $currentUsage,
                    'reason' => $allowed ? null : "Monthly campaign limit of {$limit} reached.",
                ];

            case 'staff':
                $limit = $plan->max_staff_members;
                $allowed = ($limit === 0) || ($currentUsage < $limit);
                return [
                    'allowed' => $allowed,
                    'limit' => $limit,
                    'current' => $currentUsage,
                    'reason' => $allowed ? null : "Staff limit of {$limit} reached.",
                ];

            case 'ai_bot':
                return [
                    'allowed' => (bool) $plan->has_ai_bot,
                    'limit' => $plan->has_ai_bot ? 1 : 0,
                    'current' => 0,
                    'reason' => $plan->has_ai_bot ? null : "AI Bot is not included in {$plan->title} plan.",
                ];

            case 'api_access':
                return [
                    'allowed' => (bool) $plan->has_api_access,
                    'limit' => $plan->has_api_access ? 1 : 0,
                    'current' => 0,
                    'reason' => $plan->has_api_access ? null : "API Access is not included in {$plan->title} plan.",
                ];

            default:
                return ['allowed' => true, 'limit' => 0, 'current' => $currentUsage, 'reason' => null];
        }
    }

    /**
     * Create checkout session for Stripe, Razorpay, or Manual Wire.
     */
    public function createCheckoutSession(
        Vendor $vendor,
        SubscriptionPlan $plan,
        string $billingCycle = 'monthly',
        string $gateway = 'stripe'
    ): array {
        $amount = ($billingCycle === 'yearly') ? $plan->price_yearly : $plan->price_monthly;
        $currency = 'INR';

        $txRef = 'TXN_' . strtoupper(Str::random(12));

        $transaction = PaymentTransaction::create([
            'vendor_id' => $vendor->id,
            'gateway' => $gateway,
            'transaction_reference' => $txRef,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
            'payload' => [
                'plan_id' => $plan->id,
                'billing_cycle' => $billingCycle,
            ],
        ]);

        if ($gateway === 'stripe') {
            return [
                'success' => true,
                'gateway' => 'stripe',
                'checkout_url' => "https://checkout.stripe.com/pay/{$txRef}",
                'session_id' => 'cs_test_' . Str::random(24),
                'transaction_reference' => $txRef,
                'amount' => $amount,
            ];
        } elseif ($gateway === 'razorpay') {
            return [
                'success' => true,
                'gateway' => 'razorpay',
                'order_id' => 'order_' . Str::random(14),
                'key_id' => env('RAZORPAY_KEY_ID', 'rzp_test_placeholder'),
                'transaction_reference' => $txRef,
                'amount' => $amount * 100, // in paise
                'currency' => $currency,
            ];
        }

        return [
            'success' => true,
            'gateway' => 'manual',
            'transaction_reference' => $txRef,
            'amount' => $amount,
            'instructions' => 'Please transfer to Bank Account: 1234567890, IFSC: HDFC0001234',
        ];
    }

    /**
     * Handle successful payment confirmation from webhook and activate subscription.
     */
    public function handlePaymentSuccess(
        string $gateway,
        string $transactionRef,
        float $amount,
        string $currency = 'INR',
        array $metadata = []
    ): Subscription {
        $transaction = PaymentTransaction::where('transaction_reference', $transactionRef)->first();

        if ($transaction) {
            $transaction->update([
                'status' => 'successful',
                'payload' => array_merge($transaction->payload ?? [], $metadata),
            ]);
            $vendorId = $transaction->vendor_id;
            $planId = $transaction->payload['plan_id'] ?? null;
            $cycle = $transaction->payload['billing_cycle'] ?? 'monthly';
        } else {
            $vendorId = $metadata['vendor_id'] ?? Vendor::firstOrFail()->id;
            $planId = $metadata['plan_id'] ?? SubscriptionPlan::firstOrFail()->id;
            $cycle = $metadata['billing_cycle'] ?? 'monthly';

            $transaction = PaymentTransaction::create([
                'vendor_id' => $vendorId,
                'gateway' => $gateway,
                'transaction_reference' => $transactionRef,
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'successful',
                'payload' => $metadata,
            ]);
        }

        $startsAt = now();
        $endsAt = ($cycle === 'yearly') ? now()->addYear() : now()->addMonth();

        // Expire older active subscriptions
        Subscription::where('vendor_id', $vendorId)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        // Create new active subscription
        $subscription = Subscription::create([
            'vendor_id' => $vendorId,
            'subscription_plan_id' => $planId,
            'billing_cycle' => $cycle,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        $transaction->update(['subscription_id' => $subscription->id]);

        return $subscription->load('plan');
    }

    /**
     * Submit offline manual bank wire payment request with proof screenshot.
     */
    public function submitManualPaymentRequest(
        Vendor $vendor,
        SubscriptionPlan $plan,
        float $amount,
        ?string $proofUrl = null,
        ?string $notes = null
    ): ManualPaymentRequest {
        return ManualPaymentRequest::create([
            'vendor_id' => $vendor->id,
            'subscription_plan_id' => $plan->id,
            'amount' => $amount,
            'currency' => 'INR',
            'proof_image_url' => $proofUrl,
            'notes' => $notes,
            'status' => 'pending',
        ]);
    }

    /**
     * SuperAdmin review of manual bank wire payment request.
     */
    public function reviewManualPaymentRequest(
        ManualPaymentRequest $request,
        bool $approve,
        ?User $reviewer = null
    ): bool {
        if ($request->status !== 'pending') {
            return false;
        }

        if ($approve) {
            $request->update([
                'status' => 'approved',
                'reviewed_by_user_id' => $reviewer?->id,
                'reviewed_at' => now(),
            ]);

            // Activate subscription
            $this->handlePaymentSuccess(
                'manual',
                'MANUAL_' . $request->id,
                (float) $request->amount,
                $request->currency,
                [
                    'vendor_id' => $request->vendor_id,
                    'plan_id' => $request->subscription_plan_id,
                    'billing_cycle' => 'monthly',
                ]
            );

            return true;
        }

        $request->update([
            'status' => 'rejected',
            'reviewed_by_user_id' => $reviewer?->id,
            'reviewed_at' => now(),
        ]);

        return true;
    }
}
