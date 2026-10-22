<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCampaignDispatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    public function __construct(
        public int $campaignId
    ) {}

    public function handle(): void
    {
        $campaign = Campaign::with(['template', 'targetGroups', 'vendor'])->find($this->campaignId);

        if (!$campaign || in_array($campaign->status, ['paused', 'cancelled', 'completed'])) {
            return;
        }

        $campaign->update(['status' => 'processing']);

        // Build target audience query
        $query = Contact::where('vendor_id', $campaign->vendor_id)
            ->where('is_opted_out', false);

        if ($campaign->targetGroups()->exists()) {
            $groupIds = $campaign->targetGroups->pluck('id')->toArray();
            $query->whereHas('groups', function ($q) use ($groupIds) {
                $q->whereIn('contact_groups.id', $groupIds);
            });
        }

        // Avoid duplicate dispatch for contacts already sent in this campaign
        $query->whereDoesntHave('messages', function ($q) use ($campaign) {
            $q->where('campaign_id', $campaign->id);
        });

        $totalMatching = $query->count();

        if ($campaign->total_contacts === 0) {
            $campaign->update(['total_contacts' => $totalMatching]);
        }

        if ($totalMatching === 0) {
            $campaign->update(['status' => 'completed']);
            return;
        }

        $mapping = $campaign->variables_mapping ?? [];

        // Chunk through contacts and dispatch single send jobs
        $query->chunk(100, function ($contacts) use ($campaign, $mapping) {
            // Re-check campaign status between chunks to support pause/cancel mid-flight
            $freshCampaign = Campaign::find($campaign->id);
            if (!$freshCampaign || in_array($freshCampaign->status, ['paused', 'cancelled'])) {
                return false; // Break chunk loop
            }

            foreach ($contacts as $contact) {
                $components = $this->buildTemplateComponents($contact, $mapping);
                SendCampaignMessageJob::dispatch($campaign->id, $contact->id, $components);
            }
        });
    }

    /**
     * Resolve dynamic variables and build Meta template components array.
     */
    protected function buildTemplateComponents(Contact $contact, array $mapping): array
    {
        $components = [];

        // Header parameters
        if (!empty($mapping['header']) && is_array($mapping['header'])) {
            $headerParams = [];
            foreach ($mapping['header'] as $sourceField) {
                $headerParams[] = [
                    'type' => 'text',
                    'text' => $this->resolveFieldValue($contact, $sourceField),
                ];
            }
            if (!empty($headerParams)) {
                $components[] = [
                    'type' => 'header',
                    'parameters' => $headerParams,
                ];
            }
        }

        // Body parameters
        if (!empty($mapping['body']) && is_array($mapping['body'])) {
            $bodyParams = [];
            foreach ($mapping['body'] as $sourceField) {
                $bodyParams[] = [
                    'type' => 'text',
                    'text' => $this->resolveFieldValue($contact, $sourceField),
                ];
            }
            if (!empty($bodyParams)) {
                $components[] = [
                    'type' => 'body',
                    'parameters' => $bodyParams,
                ];
            }
        }

        return $components;
    }

    /**
     * Resolve dynamic value from contact fields or custom fields.
     */
    protected function resolveFieldValue(Contact $contact, string $source): string
    {
        $source = trim($source);

        // Fixed string prefix, e.g. "fixed:50% OFF"
        if (str_starts_with($source, 'fixed:')) {
            return substr($source, 6);
        }

        // Contact standard attributes
        switch ($source) {
            case 'first_name':
                return $contact->first_name ?: 'Customer';
            case 'last_name':
                return $contact->last_name ?: '';
            case 'full_name':
                return trim("{$contact->first_name} {$contact->last_name}") ?: 'Customer';
            case 'phone_number':
                return $contact->phone_number;
            case 'email':
                return $contact->email ?: '';
        }

        // Contact custom field, e.g. "custom:tier" or "cf:points"
        if (str_starts_with($source, 'custom:') || str_starts_with($source, 'cf:')) {
            $parts = explode(':', $source, 2);
            $fieldKey = $parts[1] ?? '';

            $cfValue = $contact->customFieldValues()
                ->whereHas('customField', fn($q) => $q->where('field_key', $fieldKey))
                ->value('value');

            if ($cfValue !== null) {
                return (string) $cfValue;
            }
        }

        return $source;
    }
}
