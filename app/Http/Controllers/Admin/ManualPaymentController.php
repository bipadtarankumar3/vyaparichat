<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualPaymentRequest;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManualPaymentController extends Controller
{
    /**
     * Listing: Manual Subscriptions.
     */
    public function index(Request $request): View
    {
        $requests = ManualPaymentRequest::with(['vendor', 'plan'])
            ->latest()
            ->paginate(15);

        return view('admin.plans-manual.index', [
            'requests' => $requests,
            'activeNav' => 'plans_manual',
        ]);
    }

    /**
     * Action: Approve Manual / Wire Subscription.
     */
    public function approve(ManualPaymentRequest $manualRequest): RedirectResponse
    {
        $manualRequest->update([
            'status' => 'approved',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        if ($manualRequest->plan && $manualRequest->vendor) {
            Subscription::create([
                'vendor_id' => $manualRequest->vendor_id,
                'subscription_plan_id' => $manualRequest->subscription_plan_id,
                'status' => 'active',
                'billing_cycle' => 'monthly',
                'amount' => $manualRequest->amount,
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
            ]);
        }

        return back()->with('success', "Manual payment request approved and plan activated for {$manualRequest->vendor?->name}.");
    }

    /**
     * Action: Reject Manual / Wire Subscription.
     */
    public function reject(ManualPaymentRequest $manualRequest): RedirectResponse
    {
        $manualRequest->update([
            'status' => 'rejected',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Manual payment request rejected.");
    }
}
