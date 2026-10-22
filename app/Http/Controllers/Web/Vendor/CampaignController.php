<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Models\Campaign;
use App\Models\ContactGroup;
use App\Models\WhatsAppTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CampaignController extends BaseVendorController
{
    /**
     * Listing: Campaigns.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $campaigns = Campaign::where('vendor_id', $vendor->id)
            ->latest()
            ->paginate(15);

        $templates = WhatsAppTemplate::where('vendor_id', $vendor->id)->get();
        $groups = ContactGroup::where('vendor_id', $vendor->id)->get();

        return view('vendor.campaigns.index', [
            'vendor' => $vendor,
            'campaigns' => $campaigns,
            'templates' => $templates,
            'groups' => $groups,
            'activeNav' => 'campaigns',
        ]);
    }

    /**
     * Add: Store new Broadcast Campaign.
     */
    public function store(Request $request): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'title' => 'nullable|string|max:150',
            'name' => 'nullable|string|max:150',
            'whatsapp_template_id' => 'nullable',
            'template_name' => 'nullable|string',
            'type' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
        ]);

        $title = $validated['title'] ?? $validated['name'] ?? 'Broadcast ' . now()->format('M d, H:i');

        // Resolve or create fallback template if template_name passed as string
        $templateId = $validated['whatsapp_template_id'] ?? null;
        if (!$templateId && !empty($validated['template_name'])) {
            $tpl = WhatsAppTemplate::firstOrCreate(
                ['vendor_id' => $vendor->id, 'name' => Str::snake(strtolower($validated['template_name']))],
                ['category' => 'MARKETING', 'language' => 'en', 'status' => 'APPROVED', 'components' => [['type' => 'BODY', 'text' => 'Broadcast message']]]
            );
            $templateId = $tpl->id;
        }

        if (!$templateId) {
            $tpl = WhatsAppTemplate::where('vendor_id', $vendor->id)->first();
            if (!$tpl) {
                $tpl = WhatsAppTemplate::create([
                    'vendor_id' => $vendor->id,
                    'name' => 'default_campaign_notice',
                    'category' => 'MARKETING',
                    'language' => 'en',
                    'status' => 'APPROVED',
                    'components' => [['type' => 'BODY', 'text' => 'Campaign announcement']],
                ]);
            }
            $templateId = $tpl->id;
        }

        Campaign::create([
            'vendor_id' => $vendor->id,
            'title' => $title,
            'whatsapp_template_id' => $templateId,
            'status' => 'scheduled',
            'scheduled_at' => !empty($validated['scheduled_at']) ? $validated['scheduled_at'] : now()->addMinutes(15),
            'total_recipients' => 0,
            'sent_count' => 0,
            'delivered_count' => 0,
            'read_count' => 0,
            'failed_count' => 0,
        ]);

        return back()->with('success', "Campaign \"{$title}\" scheduled successfully.");
    }

    /**
     * Action: Pause, Resume, or Cancel Campaign.
     */
    public function action(Request $request, Campaign $campaign, string $action): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($campaign->vendor_id !== $vendor->id) {
            abort(403);
        }

        if ($action === 'pause') {
            $campaign->update(['status' => 'paused']);
            $msg = 'Campaign has been paused.';
        } elseif ($action === 'resume') {
            $campaign->update(['status' => 'scheduled']);
            $msg = 'Campaign resumed.';
        } elseif ($action === 'cancel') {
            $campaign->update(['status' => 'cancelled']);
            $msg = 'Campaign cancelled.';
        } else {
            return back()->with('error', 'Invalid action requested.');
        }

        return back()->with('success', $msg);
    }

    /**
     * Delete: Remove Campaign.
     */
    public function destroy(Request $request, Campaign $campaign): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($campaign->vendor_id !== $vendor->id) {
            abort(403);
        }

        $campaign->delete();
        return back()->with('success', 'Campaign deleted successfully.');
    }
}
