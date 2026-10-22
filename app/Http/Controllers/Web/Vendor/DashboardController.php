<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends BaseVendorController
{
    /**
     * Vendor Dashboard Overview.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);

        $stats = [
            'total_contacts' => Contact::where('vendor_id', $vendor->id)->count(),
            'total_messages_sent' => WhatsAppMessage::where('vendor_id', $vendor->id)->where('direction', 'outgoing')->count(),
            'total_messages_received' => WhatsAppMessage::where('vendor_id', $vendor->id)->where('direction', 'incoming')->count(),
            'approved_templates' => \App\Models\WhatsAppTemplate::where('vendor_id', $vendor->id)->where('status', 'APPROVED')->count(),
            'total_campaigns' => Campaign::where('vendor_id', $vendor->id)->count(),
            'incoming_messages' => WhatsAppMessage::where('vendor_id', $vendor->id)->where('direction', 'incoming')->count(),
            'outgoing_messages' => WhatsAppMessage::where('vendor_id', $vendor->id)->where('direction', 'outgoing')->count(),
            'active_chats' => WhatsAppMessage::where('vendor_id', $vendor->id)->distinct('contact_id')->count('contact_id'),
        ];

        $recentChats = WhatsAppMessage::where('vendor_id', $vendor->id)
            ->with('contact')
            ->latest()
            ->take(8)
            ->get();

        $recentCampaigns = Campaign::where('vendor_id', $vendor->id)
            ->latest()
            ->take(5)
            ->get();

        return view('vendor.dashboard.index', [
            'vendor' => $vendor,
            'metrics' => $stats,
            'stats' => $stats,
            'recentChats' => $recentChats,
            'recentCampaigns' => $recentCampaigns,
            'activeNav' => 'dashboard',
        ]);
    }
}
