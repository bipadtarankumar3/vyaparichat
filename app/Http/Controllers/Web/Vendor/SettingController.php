<?php

namespace App\Http\Controllers\Web\Vendor;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends BaseVendorController
{
    /**
     * View: Vendor Meta Cloud API and Bot Settings.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);

        return view('vendor.settings.index', [
            'vendor' => $vendor,
            'activeNav' => 'settings',
        ]);
    }

    /**
     * Update: Save Vendor Meta Cloud API credentials.
     */
    public function save(Request $request): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'meta_app_id' => 'nullable|string|max:100',
            'meta_app_secret' => 'nullable|string|max:150',
            'whatsapp_phone_number_id' => 'nullable|string|max:100',
            'whatsapp_business_account_id' => 'nullable|string|max:100',
            'meta_access_token' => 'nullable|string|max:500',
            'bot_status' => 'nullable|in:enabled,disabled',
        ]);

        $vendor->update([
            'meta_app_id' => $validated['meta_app_id'] ?? $vendor->meta_app_id,
            'meta_app_secret' => $validated['meta_app_secret'] ?? $vendor->meta_app_secret,
            'whatsapp_phone_number_id' => $validated['whatsapp_phone_number_id'] ?? $vendor->whatsapp_phone_number_id,
            'whatsapp_business_account_id' => $validated['whatsapp_business_account_id'] ?? $vendor->whatsapp_business_account_id,
            'meta_access_token' => $validated['meta_access_token'] ?? $vendor->meta_access_token,
            'whatsapp_status' => !empty($validated['whatsapp_phone_number_id']) ? 'connected' : $vendor->whatsapp_status,
        ]);

        return back()->with('success', 'WhatsApp Cloud API settings saved successfully.');
    }
}
