<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BotFlow;
use App\Models\BotReply;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Label;
use App\Models\SubscriptionPlan;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppTemplate;
use App\Services\Billing\BillingService;
use App\Services\Chat\ChatInboxService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected BillingService $billingService,
        protected ChatInboxService $inboxService
    ) {}

    /**
     * Get active vendor for current session.
     */
    protected function getActiveVendor(Request $request): Vendor
    {
        if ($request->session()->has('impersonated_vendor_id')) {
            $impersonated = Vendor::find($request->session()->get('impersonated_vendor_id'));
            if ($impersonated) {
                return $impersonated;
            }
        }

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
     * Main Overview Dashboard.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);

        $metrics = [
            'total_contacts' => Contact::where('vendor_id', $vendor->id)->count(),
            'total_messages_sent' => WhatsAppMessage::where('vendor_id', $vendor->id)->where('direction', 'outbound')->count(),
            'total_messages_received' => WhatsAppMessage::where('vendor_id', $vendor->id)->where('direction', 'inbound')->count(),
            'active_campaigns' => Campaign::where('vendor_id', $vendor->id)->whereIn('status', ['processing', 'scheduled'])->count(),
            'approved_templates' => WhatsAppTemplate::where('vendor_id', $vendor->id)->where('status', 'APPROVED')->count(),
        ];

        return view('pages.dashboard', [
            'vendor' => $vendor,
            'metrics' => $metrics,
            'activeNav' => 'overview',
        ]);
    }

    /**
     * Dedicated Live Chat Inbox Page.
     */
    public function chat(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);

        return view('pages.chat', [
            'vendor' => $vendor,
            'activeNav' => 'chat',
        ]);
    }

    /**
     * Dedicated Contacts & Audience CRM Page.
     */
    public function contacts(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $groups = ContactGroup::where('vendor_id', $vendor->id)->get();
        $labels = Label::where('vendor_id', $vendor->id)->get();

        return view('pages.contacts', [
            'vendor' => $vendor,
            'groups' => $groups,
            'labels' => $labels,
            'activeNav' => 'contacts',
        ]);
    }

    /**
     * Dedicated Broadcast Campaigns Page.
     */
    public function campaigns(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $templates = WhatsAppTemplate::where('vendor_id', $vendor->id)->where('status', 'APPROVED')->get();
        $groups = ContactGroup::where('vendor_id', $vendor->id)->get();

        return view('pages.campaigns', [
            'vendor' => $vendor,
            'templates' => $templates,
            'groups' => $groups,
            'activeNav' => 'campaigns',
        ]);
    }

    /**
     * Dedicated WhatsApp Templates Gallery Page.
     */
    public function templates(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);

        return view('pages.templates', [
            'vendor' => $vendor,
            'activeNav' => 'templates',
        ]);
    }

    /**
     * Dedicated Bot Automations & Simulator Page.
     */
    public function bot(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);

        return view('pages.bot', [
            'vendor' => $vendor,
            'activeNav' => 'automations',
        ]);
    }

    /**
     * Dedicated E-Commerce Webhooks & Developer API Page.
     */
    public function integrations(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);

        return view('pages.integrations', [
            'vendor' => $vendor,
            'activeNav' => 'integrations',
        ]);
    }

    /**
     * Dedicated SaaS Subscriptions & Billing Page.
     */
    public function billing(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $plans = $this->billingService->listPlans();
        $currentSubscription = $this->billingService->getCurrentSubscription($vendor);

        return view('pages.billing', [
            'vendor' => $vendor,
            'plans' => $plans,
            'currentSubscription' => $currentSubscription,
            'activeNav' => 'billing',
        ]);
    }

    /**
     * Dedicated Settings & Cloud API Credentials Page.
     */
    public function settings(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);

        return view('pages.settings', [
            'vendor' => $vendor,
            'activeNav' => 'settings',
        ]);
    }
}
