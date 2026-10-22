<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorUser;
use App\Models\WhatsAppMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminVendorController extends Controller
{
    /**
     * Super Admin Console: Dashboard & Vendors Directory.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $vendorsQuery = Vendor::with(['users', 'activeSubscription.plan'])
            ->withCount(['contacts', 'messages', 'campaigns'])
            ->latest();

        if ($search) {
            $vendorsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $vendors = $vendorsQuery->paginate(15)->withQueryString();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        $metrics = [
            'total_vendors' => Vendor::count(),
            'active_vendors' => Vendor::where('status', 'active')->count(),
            'suspended_vendors' => Vendor::where('status', 'suspended')->count(),
            'total_contacts' => Contact::count(),
            'total_messages' => WhatsAppMessage::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
        ];

        return view('admin.vendors', [
            'vendors' => $vendors,
            'plans' => $plans,
            'metrics' => $metrics,
            'search' => $search,
        ]);
    }

    /**
     * Create a new Vendor and assign owner & custom section permissions.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:vendors,slug',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'owner_name' => 'required|string|max:255',
            'owner_password' => 'required|string|min:6',
            'plan_id' => 'nullable|exists:subscription_plans,id',
            'permissions' => 'nullable|array',
        ]);

        $slug = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        if (Vendor::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . Str::lower(Str::random(4));
        }

        // Default all permissions, then overlay selected extra accesses
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

        // Generate vendor API Key
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

        // Link Owner to Vendor
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
     * Toggle vendor status (active vs suspended).
     */
    public function toggleStatus(Vendor $vendor): RedirectResponse
    {
        $vendor->status = $vendor->status === 'active' ? 'suspended' : 'active';
        $vendor->save();

        return back()->with('success', "Vendor \"{$vendor->name}\" status updated to {$vendor->status}.");
    }

    /**
     * Update custom section permissions for a specific vendor.
     */
    public function updatePermissions(Request $request, Vendor $vendor): RedirectResponse
    {
        $allSections = ['chat', 'contacts', 'campaigns', 'templates', 'bot', 'integrations', 'billing'];
        $selectedSections = $request->input('permissions', []);

        $customPermissions = [];
        foreach ($allSections as $sec) {
            $customPermissions[$sec] = in_array($sec, $selectedSections);
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
     * Leave impersonation and return to Super Admin Console.
     */
    public function leaveImpersonation(Request $request): RedirectResponse
    {
        $request->session()->forget(['impersonated_vendor_id', 'admin_impersonator_id']);

        return redirect()->route('admin.vendors')->with('success', 'Returned to Super Admin Central Console.');
    }
}
