<?php

namespace App\Http\Controllers\Web\Vendor;

use Illuminate\Http\Request;
use Illuminate\View\View;

class QrCodeController extends BaseVendorController
{
    /**
     * View: WhatsApp QR Code & Click to Chat generator.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $phone = preg_replace('/[^0-9]/', '', $vendor->phone ?? '919999999999');
        $waLink = "https://wa.me/{$phone}?text=" . urlencode("Hi, I want to inquire about your services!");

        return view('vendor.qr-code.index', [
            'vendor' => $vendor,
            'waLink' => $waLink,
            'activeNav' => 'qr_code',
        ]);
    }
}
