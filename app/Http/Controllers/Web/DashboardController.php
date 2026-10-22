<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppTemplate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Get active vendor for current session.
     */
    protected function getActiveVendor(Request $request): Vendor
    {
        $user = $request->user();
        if ($user && $user->vendors()->exists()) {
            return $user->vendors()->first();
        }

        return Vendor::first() ?: Vendor::create([
            'name' => 'Vyapari Demo Store',
            'slug' => 'vyapari-demo',
            'status' => 'active',
            'whatsapp_status' => 'connected',
        ]);
    }

    /**
     * Main SPA Dashboard page.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);

        // Fetch initial high-level metrics
        $metrics = [
            'total_contacts' => Contact::where('vendor_id', $vendor->id)->count(),
            'total_messages_sent' => WhatsAppMessage::where('vendor_id', $vendor->id)->where('direction', 'outbound')->count(),
            'total_messages_received' => WhatsAppMessage::where('vendor_id', $vendor->id)->where('direction', 'inbound')->count(),
            'active_campaigns' => Campaign::where('vendor_id', $vendor->id)->whereIn('status', ['processing', 'scheduled'])->count(),
            'approved_templates' => WhatsAppTemplate::where('vendor_id', $vendor->id)->where('status', 'APPROVED')->count(),
        ];

        return view('dashboard', [
            'vendor' => $vendor,
            'metrics' => $metrics,
        ]);
    }
}
