<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCampaignMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public int $campaignId,
        public int $contactId,
        public array $templateComponents = []
    ) {}

    public function handle(WhatsAppApiService $apiService): void
    {
        $campaign = Campaign::find($this->campaignId);
        $contact = Contact::find($this->contactId);

        if (!$campaign || !$contact) {
            return;
        }

        // Abort if campaign was paused or cancelled while in queue
        if (in_array($campaign->status, ['paused', 'cancelled'])) {
            return;
        }

        $template = $campaign->template;
        $vendor = $campaign->vendor;

        if (!$template || !$vendor) {
            return;
        }

        // 1. Create outbound WhatsApp message record in DB
        $message = WhatsAppMessage::create([
            'vendor_id' => $vendor->id,
            'contact_id' => $contact->id,
            'campaign_id' => $campaign->id,
            'direction' => 'outbound',
            'message_type' => 'template',
            'status' => 'queued',
            'raw_payload' => [
                'template_name' => $template->name,
                'components' => $this->templateComponents,
            ],
        ]);

        // 2. Dispatch via Meta WhatsApp Cloud API
        $response = $apiService->sendTemplateMessage(
            $vendor,
            $contact->phone_number,
            $template->name,
            $template->language,
            $this->templateComponents
        );

        // 3. Update record & campaign statistics
        if ($response['success']) {
            $message->update([
                'status' => 'sent',
                'wamid' => $response['wamid'] ?? null,
                'sent_at' => now(),
            ]);

            $campaign->increment('sent_count');
        } else {
            $message->update([
                'status' => 'failed',
                'error_message' => $response['error'] ?? 'Failed to send WhatsApp template message.',
            ]);

            $campaign->increment('failed_count');
            Log::warning("Campaign {$campaign->id} message to {$contact->phone_number} failed: " . ($response['error'] ?? 'Unknown'));
        }

        // 4. Mark campaign as completed if all contacts processed
        $campaign->refresh();
        if (($campaign->sent_count + $campaign->failed_count) >= $campaign->total_contacts) {
            $campaign->update(['status' => 'completed']);
        }
    }
}
