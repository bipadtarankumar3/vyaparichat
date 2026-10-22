<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ManualPaymentRequest;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Admin Central Console Dashboard Overview.
     */
    public function index(Request $request): View
    {
        $stats = [
            'total_vendors' => Vendor::count(),
            'active_vendors' => Vendor::where('status', 'active')->count(),
            'suspended_vendors' => Vendor::where('status', 'suspended')->count(),
            'connected_whatsapp' => Vendor::where('whatsapp_status', 'connected')->count(),
            'total_contacts' => Contact::count(),
            'total_messages' => WhatsAppMessage::count(),
            'total_users' => User::count(),
            'pending_manual_payments' => ManualPaymentRequest::where('status', 'pending')->count(),
        ];

        $recentVendors = Vendor::withCount(['contacts', 'campaigns'])
            ->latest()
            ->take(6)
            ->get();

        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('admin.dashboard.index', [
            'stats' => $stats,
            'recentVendors' => $recentVendors,
            'plans' => $plans,
            'activeNav' => 'dashboard',
        ]);
    }
}
