<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends BaseVendorController
{
    /**
     * Listing: Vendor Billing & Subscription.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $subscription = $vendor->currentSubscription()->with('plan')->first();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('vendor.billing.index', [
            'vendor' => $vendor,
            'subscription' => $subscription,
            'plans' => $plans,
            'activeNav' => 'billing',
        ]);
    }
}
