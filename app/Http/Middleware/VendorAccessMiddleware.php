<?php

namespace App\Http\Middleware;

use App\Models\Vendor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorAccessMiddleware
{
    /**
     * Handle an incoming request for Vendor Portal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $activeVendor = null;

        // 1. Check if Super Admin is impersonating a Vendor
        $impersonatedVendorId = $request->session()->get('impersonated_vendor_id');
        if ($impersonatedVendorId) {
            $activeVendor = Vendor::find($impersonatedVendorId);
        }

        // 2. If not impersonating, get user's primary linked Vendor
        if (!$activeVendor && $user->vendors()->exists()) {
            $activeVendor = $user->vendors()->first();
        }

        // 3. Fallback for Super Admin accessing Vendor routes without explicit impersonation
        if (!$activeVendor && $user->is_super_admin) {
            $activeVendor = Vendor::first();
        }

        // 4. Auto-provision vendor if user is a valid merchant without one
        if (!$activeVendor) {
            $activeVendor = Vendor::create([
                'name' => $user->name . ' Store',
                'slug' => \Illuminate\Support\Str::slug($user->name . '-store-' . \Illuminate\Support\Str::random(4)),
                'email' => $user->email,
                'status' => 'active',
                'whatsapp_status' => 'disconnected',
            ]);

            $user->vendors()->attach($activeVendor->id, [
                'role' => 'owner',
                'permissions' => json_encode(['*']),
                'status' => 'active',
            ]);
        }

        // Check if vendor account is suspended
        if ($activeVendor->status === 'suspended' && !$request->session()->has('admin_impersonator_id')) {
            abort(403, 'Your merchant account has been suspended by system administration. Please contact support.');
        }

        // Store on request attributes & share with all views
        $request->attributes->set('active_vendor', $activeVendor);
        view()->share('activeVendor', $activeVendor);

        return $next($request);
    }
}
