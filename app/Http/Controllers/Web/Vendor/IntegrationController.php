<?php

namespace App\Http\Controllers\Web\Vendor;

use Illuminate\Http\Request;
use Illuminate\View\View;

class IntegrationController extends BaseVendorController
{
    /**
     * View: Vendor E-commerce & Webhook Integrations.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);

        return view('vendor.integrations.index', [
            'vendor' => $vendor,
            'activeNav' => 'integrations',
        ]);
    }
}
