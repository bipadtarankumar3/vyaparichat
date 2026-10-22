<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\SubscriptionPlan;
use App\Models\Vendor;
use App\Services\Billing\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __construct(
        protected BillingService $billingService
    ) {}

    protected function getActiveVendor(Request $request): Vendor
    {
        $user = $request->user();
        if ($user && $user->vendors()->exists()) {
            return $user->vendors()->first();
        }

        return Vendor::firstOrFail();
    }

    /**
     * List all public subscription plans.
     */
    public function plans(): JsonResponse
    {
        $plans = $this->billingService->listPlans();

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    /**
     * Get vendor's current active subscription & usage breakdown.
     */
    public function currentSubscription(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);
        $subscription = $this->billingService->getCurrentSubscription($vendor);

        $contactsCount = $vendor->contacts()->count();
        $campaignsThisMonth = $vendor->campaigns()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $teamCount = $vendor->users()->count();

        $limits = [
            'contacts' => $this->billingService->checkFeatureLimit($vendor, 'contacts', $contactsCount),
            'campaigns' => $this->billingService->checkFeatureLimit($vendor, 'campaigns', $campaignsThisMonth),
            'staff' => $this->billingService->checkFeatureLimit($vendor, 'staff', $teamCount),
            'ai_bot' => $this->billingService->checkFeatureLimit($vendor, 'ai_bot'),
            'api_access' => $this->billingService->checkFeatureLimit($vendor, 'api_access'),
        ];

        return response()->json([
            'success' => true,
            'subscription' => $subscription,
            'limits' => $limits,
        ]);
    }

    /**
     * Create checkout session for purchasing/upgrading subscription.
     */
    public function checkout(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|string|in:monthly,yearly',
            'gateway' => 'required|string|in:stripe,razorpay,manual',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        $session = $this->billingService->createCheckoutSession(
            $vendor,
            $plan,
            $validated['billing_cycle'],
            $validated['gateway']
        );

        return response()->json([
            'success' => true,
            'data' => $session,
        ]);
    }

    /**
     * Submit offline manual bank transfer payment request.
     */
    public function submitManualPayment(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'amount' => 'required|numeric|min:1',
            'proof_image_url' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        $paymentRequest = $this->billingService->submitManualPaymentRequest(
            $vendor,
            $plan,
            (float) $validated['amount'],
            $validated['proof_image_url'] ?? null,
            $validated['notes'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Manual payment request submitted. Our team will verify and activate your subscription.',
            'data' => $paymentRequest,
        ], 201);
    }

    /**
     * List payment transactions history.
     */
    public function transactions(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $transactions = PaymentTransaction::where('vendor_id', $vendor->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }
}
