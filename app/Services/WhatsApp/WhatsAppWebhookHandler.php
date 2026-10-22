<?php

namespace App\Services\WhatsApp;

use App\Models\BotReply;
use App\Models\Contact;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookHandler
{
    public function __construct(
        protected WhatsAppApiService $apiService,
        protected \App\Services\Bot\BotAutomationService $botService
    ) {}

    /**
     * Process incoming webhook payload for a vendor.
     */
    public function handle(Vendor $vendor, array $payload): void
    {
        $entry = $payload['entry'][0] ?? null;
        if (!$entry) {
            return;
        }

        $changes = $entry['changes'][0]['value'] ?? null;
        if (!$changes) {
            return;
        }

        // 1. Process Message Status Updates (sent, delivered, read, failed)
        if (!empty($changes['statuses'])) {
            foreach ($changes['statuses'] as $statusUpdate) {
                $this->handleStatusUpdate($vendor, $statusUpdate);
            }
        }

        // 2. Process Inbound Messages from Customers
        if (!empty($changes['messages'])) {
            $senderProfile = $changes['contacts'][0] ?? [];
            foreach ($changes['messages'] as $inboundMessage) {
                $this->handleInboundMessage($vendor, $inboundMessage, $senderProfile);
            }
        }
    }

    /**
     * Handle delivery status update.
     */
    protected function handleStatusUpdate(Vendor $vendor, array $statusUpdate): void
    {
        $wamid = $statusUpdate['id'] ?? null;
        $status = $statusUpdate['status'] ?? null; // sent, delivered, read, failed
        $timestamp = isset($statusUpdate['timestamp']) ? Carbon::createFromTimestamp($statusUpdate['timestamp']) : now();

        if (!$wamid || !$status) {
            return;
        }

        $message = WhatsAppMessage::where('vendor_id', $vendor->id)
            ->where('wamid', $wamid)
            ->first();

        if (!$message) {
            return;
        }

        $updateData = [
            'status' => $status,
        ];

        if ($status === 'delivered') {
            $updateData['delivered_at'] = $timestamp;
            if ($message->campaign_id) {
                $message->campaign()->increment('delivered_count');
            }
        } elseif ($status === 'read') {
            $updateData['read_at'] = $timestamp;
            if ($message->campaign_id) {
                $message->campaign()->increment('read_count');
            }
        } elseif ($status === 'failed') {
            $errorMessage = $statusUpdate['errors'][0]['message'] ?? 'Message delivery failed.';
            $updateData['error_message'] = $errorMessage;
            if ($message->campaign_id) {
                $message->campaign()->increment('failed_count');
            }
        }

        $message->update($updateData);
    }

    /**
     * Handle incoming customer message.
     */
    protected function handleInboundMessage(Vendor $vendor, array $messageData, array $senderProfile): void
    {
        $fromNumber = $messageData['from'] ?? null;
        $wamid = $messageData['id'] ?? null;
        $type = $messageData['type'] ?? 'text';
        $timestamp = isset($messageData['timestamp']) ? Carbon::createFromTimestamp($messageData['timestamp']) : now();

        if (!$fromNumber || !$wamid) {
            return;
        }

        // Prevent duplicate processing
        if (WhatsAppMessage::where('vendor_id', $vendor->id)->where('wamid', $wamid)->exists()) {
            return;
        }

        // 1. Find or create Contact
        $contact = Contact::firstOrCreate(
            [
                'vendor_id' => $vendor->id,
                'phone_number' => $fromNumber,
            ],
            [
                'first_name' => $senderProfile['profile']['name'] ?? 'Customer',
                'language_code' => 'en',
            ]
        );

        // 2. Extract message text or media URL
        $messageText = null;
        $mediaUrl = null;

        if ($type === 'text') {
            $messageText = $messageData['text']['body'] ?? '';
        } elseif (in_array($type, ['image', 'video', 'audio', 'document'])) {
            $mediaId = $messageData[$type]['id'] ?? null;
            $messageText = $messageData[$type]['caption'] ?? null;
            if ($mediaId) {
                $mediaUrl = $this->apiService->getMediaUrl($vendor, $mediaId);
            }
        } elseif ($type === 'interactive') {
            $interactive = $messageData['interactive'] ?? [];
            if (isset($interactive['button_reply'])) {
                $messageText = $interactive['button_reply']['title'] ?? '';
            } elseif (isset($interactive['list_reply'])) {
                $messageText = $interactive['list_reply']['title'] ?? '';
            }
        } elseif ($type === 'button') {
            $messageText = $messageData['button']['text'] ?? '';
        }

        // 3. Save Inbound Message
        $inbound = WhatsAppMessage::create([
            'vendor_id' => $vendor->id,
            'contact_id' => $contact->id,
            'wamid' => $wamid,
            'direction' => 'inbound',
            'message_type' => $type,
            'message_text' => $messageText,
            'media_url' => $mediaUrl,
            'raw_payload' => $messageData,
            'status' => 'delivered',
            'sent_at' => $timestamp,
            'delivered_at' => $timestamp,
        ]);

        // 4. Trigger Bot Auto-Responder / AI Copilot
        if (!$contact->is_ai_bot_disabled && !empty($messageText)) {
            $this->botService->matchAndReply($vendor, $contact, trim($messageText));
        }
    }
}
