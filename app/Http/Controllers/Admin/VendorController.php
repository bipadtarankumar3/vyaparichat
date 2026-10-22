<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VendorController extends Controller
{
    /**
     * Listing: Vendors.
     */
    public function index(Request $request): View
    {
        $query = Vendor::query()
            ->withCount(['contacts', 'campaigns'])
            ->with(['users', 'currentSubscription.plan']);

        $search = $request->input('search', '');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $vendors = $query->latest()->paginate(15)->withQueryString();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        $metrics = [
            'total_vendors' => Vendor::count(),
            'active_vendors' => Vendor::where('status', 'active')->count(),
            'connected_whatsapp' => Vendor::where('whatsapp_status', 'connected')->count(),
            'suspended_vendors' => Vendor::where('status', 'suspended')->count(),
            'total_contacts' => \App\Models\Contact::count(),
            'total_messages' => \App\Models\WhatsAppMessage::count(),
            'active_subscriptions' => \App\Models\Subscription::where('status', 'active')->count(),
        ];

        return view('admin.vendors.index', [
            'vendors' => $vendors,
            'plans' => $plans,
            'metrics' => $metrics,
            'search' => $search,
            'activeNav' => 'vendors',
        ]);
    }

    /**
     * Add: Store new Vendor with Owner credentials.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:vendors,slug',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'owner_name' => 'required|string|max:255',
            'owner_password' => 'required|string|min:6',
            'plan_id' => 'nullable|exists:subscription_plans,id',
            'permissions' => 'nullable|array',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        if (Vendor::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . Str::lower(Str::random(4));
        }

        $allSections = ['chat', 'contacts', 'campaigns', 'templates', 'bot', 'integrations', 'billing'];
        $customPermissions = [];
        $selectedSections = $request->input('permissions', $allSections);

        foreach ($allSections as $sec) {
            $customPermissions[$sec] = in_array($sec, $selectedSections);
        }

        // 1. Create Vendor
        $vendor = Vendor::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => 'active',
            'whatsapp_status' => 'disconnected',
            'custom_permissions' => $customPermissions,
        ]);

        $vendor->generateApiKey();

        // 2. Create or find Owner User
        $owner = User::where('email', $validated['email'])->first();
        if (!$owner) {
            $owner = User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['owner_password']),
                'is_super_admin' => false,
                'status' => 'active',
            ]);
        }

        VendorUser::firstOrCreate(
            ['vendor_id' => $vendor->id, 'user_id' => $owner->id],
            [
                'role' => 'owner',
                'permissions' => ['*'],
                'status' => 'active',
            ]
        );

        // 3. Attach Subscription Plan if selected
        if (!empty($validated['plan_id'])) {
            $plan = SubscriptionPlan::find($validated['plan_id']);
            if ($plan) {
                Subscription::create([
                    'vendor_id' => $vendor->id,
                    'subscription_plan_id' => $plan->id,
                    'status' => 'active',
                    'billing_cycle' => 'monthly',
                    'amount' => $plan->price_monthly,
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                ]);
            }
        }

        return redirect()->route('admin.vendors')->with('success', "Vendor \"{$vendor->name}\" created successfully with owner credentials.");
    }

    /**
     * Update: Toggle Vendor active/suspended status.
     */
    public function toggleStatus(Vendor $vendor): RedirectResponse
    {
        $newStatus = $vendor->status === 'active' ? 'suspended' : 'active';
        $vendor->update(['status' => $newStatus]);

        return back()->with('success', "Vendor \"{$vendor->name}\" status updated to {$newStatus}.");
    }

    /**
     * Update: Update Extra Access Permissions.
     */
    public function updatePermissions(Request $request, Vendor $vendor): RedirectResponse
    {
        $allSections = ['chat', 'contacts', 'campaigns', 'templates', 'bot', 'integrations', 'billing'];
        $submitted = $request->input('permissions', []);

        $customPermissions = [];
        foreach ($allSections as $section) {
            $customPermissions[$section] = in_array($section, $submitted) || ($submitted[$section] ?? false) == '1';
        }

        $vendor->custom_permissions = $customPermissions;
        $vendor->save();

        return back()->with('success', "Extra access permissions updated for \"{$vendor->name}\".");
    }

    /**
     * Super Admin visits/impersonates a specific Vendor Console.
     */
    public function impersonate(Request $request, Vendor $vendor): RedirectResponse
    {
        $request->session()->put('impersonated_vendor_id', $vendor->id);
        $request->session()->put('admin_impersonator_id', $request->user()?->id);

        return redirect()->route('dashboard')->with('success', "Switched to {$vendor->name} Console.");
    }

    /**
     * Return back to Super Admin Central Console.
     */
    public function leaveImpersonation(Request $request): RedirectResponse
    {
        $request->session()->forget(['impersonated_vendor_id', 'admin_impersonator_id']);

        return redirect()->route('admin.vendors')->with('success', 'Returned to Super Admin Central Console.');
    }

    /**
     * Delete: Archive Vendor.
     */
    public function destroy(Vendor $vendor): RedirectResponse
    {
        $vendor->update(['status' => 'suspended']);
        return back()->with('success', "Vendor \"{$vendor->name}\" archived.");
    }
}
