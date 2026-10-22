<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlanController extends Controller
{
    /**
     * Listing: Subscription Plans.
     */
    public function index(Request $request): View
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->get();
        $subscriptions = \App\Models\Subscription::with(['vendor', 'plan'])->latest()->paginate(15);

        return view('admin.plans.index', [
            'plans' => $plans,
            'subscriptions' => $subscriptions,
            'activeNav' => 'plans_sub',
        ]);
    }

    /**
     * Add: Store new Plan.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'nullable|numeric|min:0',
            'max_contacts' => 'required|integer|min:1',
            'max_messages_monthly' => 'required|integer|min:1',
            'max_team_members' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:1000',
        ]);

        SubscriptionPlan::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'price_monthly' => $validated['price_monthly'],
            'price_yearly' => $validated['price_yearly'] ?? ($validated['price_monthly'] * 10),
            'max_contacts' => $validated['max_contacts'],
            'max_messages_monthly' => $validated['max_messages_monthly'],
            'max_campaigns_per_month' => 50,
            'max_staff_members' => $validated['max_team_members'] ?? 5,
            'description' => $validated['description'] ?? null,
            'has_ai_bot' => true,
            'has_api_access' => true,
            'is_active' => true,
        ]);

        return back()->with('success', "Subscription Plan \"{$validated['title']}\" created.");
    }

    /**
     * Update: Update Plan.
     */
    public function update(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'price_monthly' => 'required|numeric|min:0',
            'max_contacts' => 'required|integer|min:1',
            'max_messages_monthly' => 'required|integer|min:1',
        ]);

        $plan->update($validated);

        return back()->with('success', "Subscription Plan \"{$plan->title}\" updated.");
    }

    /**
     * Delete: Remove Plan.
     */
    public function destroy(SubscriptionPlan $plan): RedirectResponse
    {
        $plan->delete();
        return back()->with('success', "Subscription Plan deleted.");
    }
}
