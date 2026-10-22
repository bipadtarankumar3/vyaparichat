<?php

namespace App\Services\Campaign;

use App\Jobs\ProcessCampaignDispatchJob;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Vendor;
use App\Models\WhatsAppTemplate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CampaignService
{
    /**
     * List campaigns with filtering, status, and search.
     */
    public function listCampaigns(Vendor $vendor, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Campaign::with(['template', 'targetGroups'])
            ->where('vendor_id', $vendor->id);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where('title', 'like', "%{$search}%");
        }

        if (!empty($filters['status'])) {
            $query->where('status', strtolower($filters['status']));
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new campaign.
     */
    public function createCampaign(Vendor $vendor, array $data): Campaign
    {
        $template = WhatsAppTemplate::where('vendor_id', $vendor->id)
            ->findOrFail($data['whatsapp_template_id']);

        $campaign = Campaign::create([
            'vendor_id' => $vendor->id,
            'whatsapp_template_id' => $template->id,
            'title' => $data['title'],
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'status' => 'draft',
            'variables_mapping' => $data['variables_mapping'] ?? [],
        ]);

        if (!empty($data['group_ids']) && is_array($data['group_ids'])) {
            $campaign->targetGroups()->sync($data['group_ids']);
        }

        // Calculate initial matching contacts
        $totalContacts = $this->calculateAudienceCount($campaign);
        $campaign->update(['total_contacts' => $totalContacts]);

        return $campaign->load(['template', 'targetGroups']);
    }

    /**
     * Update campaign details.
     */
    public function updateCampaign(Campaign $campaign, array $data): Campaign
    {
        $campaign->update([
            'title' => $data['title'] ?? $campaign->title,
            'whatsapp_template_id' => $data['whatsapp_template_id'] ?? $campaign->whatsapp_template_id,
            'scheduled_at' => $data['scheduled_at'] ?? $campaign->scheduled_at,
            'variables_mapping' => $data['variables_mapping'] ?? $campaign->variables_mapping,
        ]);

        if (isset($data['group_ids']) && is_array($data['group_ids'])) {
            $campaign->targetGroups()->sync($data['group_ids']);
        }

        $campaign->update(['total_contacts' => $this->calculateAudienceCount($campaign)]);

        return $campaign->load(['template', 'targetGroups']);
    }

    /**
     * Launch a broadcast campaign.
     */
    public function launchCampaign(Campaign $campaign): array
    {
        if (in_array($campaign->status, ['completed', 'cancelled'])) {
            return [
                'success' => false,
                'error' => "Cannot launch campaign with status: {$campaign->status}.",
            ];
        }

        $template = $campaign->template;
        if ($template && $template->status !== 'APPROVED') {
            return [
                'success' => false,
                'error' => "WhatsApp Template '{$template->name}' is not APPROVED by Meta (Current status: {$template->status}).",
            ];
        }

        $campaign->update(['status' => 'processing']);

        ProcessCampaignDispatchJob::dispatch($campaign->id);

        return [
            'success' => true,
            'message' => 'Campaign queued and broadcasting started.',
            'campaign' => $campaign->fresh(),
        ];
    }

    /**
     * Pause an in-flight campaign.
     */
    public function pauseCampaign(Campaign $campaign): bool
    {
        if ($campaign->status !== 'processing') {
            return false;
        }

        $campaign->update(['status' => 'paused']);
        return true;
    }

    /**
     * Resume a paused campaign.
     */
    public function resumeCampaign(Campaign $campaign): bool
    {
        if ($campaign->status !== 'paused') {
            return false;
        }

        $campaign->update(['status' => 'processing']);
        ProcessCampaignDispatchJob::dispatch($campaign->id);

        return true;
    }

    /**
     * Cancel a campaign.
     */
    public function cancelCampaign(Campaign $campaign): bool
    {
        if ($campaign->status === 'completed') {
            return false;
        }

        $campaign->update(['status' => 'cancelled']);
        return true;
    }

    /**
     * Calculate total contacts matching campaign audience criteria.
     */
    public function calculateAudienceCount(Campaign $campaign): int
    {
        $query = Contact::where('vendor_id', $campaign->vendor_id)
            ->where('is_opted_out', false);

        if ($campaign->targetGroups()->exists()) {
            $groupIds = $campaign->targetGroups->pluck('id')->toArray();
            $query->whereHas('groups', function ($q) use ($groupIds) {
                $q->whereIn('contact_groups.id', $groupIds);
            });
        }

        return $query->count();
    }

    /**
     * Get detailed campaign statistics and analytics.
     */
    public function getCampaignStats(Campaign $campaign): array
    {
        $total = $campaign->total_contacts;
        $sent = $campaign->sent_count;
        $delivered = $campaign->delivered_count;
        $read = $campaign->read_count;
        $failed = $campaign->failed_count;

        $progress = $total > 0 ? round((($sent + $failed) / $total) * 100, 1) : 0;
        $deliveryRate = $sent > 0 ? round(($delivered / $sent) * 100, 1) : 0;
        $readRate = $delivered > 0 ? round(($read / $delivered) * 100, 1) : 0;

        return [
            'status' => $campaign->status,
            'total_contacts' => $total,
            'sent' => $sent,
            'delivered' => $delivered,
            'read' => $read,
            'failed' => $failed,
            'progress_percent' => min(100, $progress),
            'delivery_rate_percent' => $deliveryRate,
            'read_rate_percent' => $readRate,
        ];
    }
}
