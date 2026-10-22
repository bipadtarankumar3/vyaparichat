<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

abstract class BaseVendorController extends Controller
{
    /**
     * Resolve the active vendor from request attributes (set by VendorAccessMiddleware)
     * or session/auth fallback.
     */
    protected function getActiveVendor(Request $request): Vendor
    {
        if ($vendor = $request->attributes->get('active_vendor')) {
            return $vendor;
        }

        if ($impersonatedId = $request->session()->get('impersonated_vendor_id')) {
            $vendor = Vendor::find($impersonatedId);
            if ($vendor) {
                return $vendor;
            }
        }

        $user = $request->user();
        if ($user && $user->vendors()->exists()) {
            return $user->vendors()->first();
        }

        return Vendor::firstOrCreate(
            ['slug' => 'default-vendor'],
            [
                'name' => 'Vyapari Store',
                'email' => $user?->email ?? 'store@vyaparichat.com',
                'phone' => '+919999999999',
                'status' => 'active',
                'whatsapp_status' => 'disconnected',
            ]
        );
    }
}
