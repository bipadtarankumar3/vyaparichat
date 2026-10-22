<?php

namespace App\Services\Bot;

use App\Models\BotReply;
use App\Models\Contact;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BotAutomationService
{
    public function __construct(
        protected WhatsAppApiService $apiService
    ) {}

    /**
     * Match inbound message against active bot rules and dispatch reply.
     */
    public function matchAndReply(Vendor $vendor, Contact $contact, string $incomingText): ?WhatsAppMessage
    {
        if ($contact->is_ai_bot_disabled) {
            return null;
        }

        $incomingTrimmed = trim($incomingText);
        if ($incomingTrimmed === '') {
            return null;
        }

        // 1. Find matching rule by priority
        $matchedBot = $this->findMatchingRule($vendor, $incomingTrimmed);

        if ($matchedBot) {
            return $this->dispatchBotReply($vendor, $contact, $matchedBot);
        }

        // 2. If no keyword rule matched, check if AI Copilot is enabled for this vendor
        if ($vendor->getSetting('ai_copilot_enabled') === 'true' || $vendor->getSetting('ai_copilot_enabled') === true) {
            return $this->dispatchAiCopilotReply($vendor, $contact, $incomingTrimmed);
        }

        return null;
    }

    /**
     * Find highest priority matching BotReply for vendor.
     */
    public function findMatchingRule(Vendor $vendor, string $incomingText): ?BotReply
    {
        $botReplies = BotReply::where('vendor_id', $vendor->id)
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        $incomingLower = strtolower($incomingText);
        $fallbackRule = null;

        foreach ($botReplies as $bot) {
            if ($bot->trigger_type === 'fallback') {
                $fallbackRule = $bot;
                continue;
            }

            $keywords = array_map('strtolower', (array) ($bot->trigger_keywords ?: []));

            foreach ($keywords as $kw) {
                $kw = trim($kw);
                if ($kw === '') {
                    continue;
                }

                if ($bot->trigger_type === 'exact' && $incomingLower === $kw) {
                    return $bot;
                } elseif ($bot->trigger_type === 'starts_with' && str_starts_with($incomingLower, $kw)) {
                    return $bot;
                } elseif ($bot->trigger_type === 'contains' && str_contains($incomingLower, $kw)) {
                    return $bot;
                } elseif ($bot->trigger_type === 'regex') {
                    if (@preg_match('/' . $kw . '/i', $incomingText)) {
                        return $bot;
                    }
                }
            }
        }

        return $fallbackRule;
    }

    /**
     * Dispatch bot reply to customer.
     */
    public function dispatchBotReply(Vendor $vendor, Contact $contact, BotReply $bot): ?WhatsAppMessage
    {
        $payload = (array) ($bot->reply_payload ?: []);
        $replyType = $bot->reply_type ?: 'text';
        $apiResult = ['success' => false];
        $sentText = '';
        $mediaUrl = null;

        if ($replyType === 'text') {
            $rawText = $payload['text'] ?? '';
            $sentText = $this->interpolateVariables($rawText, $contact);

            if (!empty($sentText)) {
                $apiResult = $this->apiService->sendTextMessage($vendor, $contact->phone_number, $sentText);
            }
        } elseif ($replyType === 'interactive') {
            $rawText = $payload['body_text'] ?? ($payload['text'] ?? '');
            $sentText = $this->interpolateVariables($rawText, $contact);
            $buttons = $payload['buttons'] ?? [];
            $headerText = $payload['header_text'] ?? null;
            $footerText = $payload['footer_text'] ?? null;

            if (!empty($sentText) && !empty($buttons)) {
                $apiResult = $this->apiService->sendInteractiveButtonsMessage(
                    $vendor,
                    $contact->phone_number,
                    $sentText,
                    $buttons,
                    $headerText,
                    $footerText
                );
            }
        } elseif ($replyType === 'media') {
            $mediaType = $payload['media_type'] ?? 'image';
            $mediaUrl = $payload['media_url'] ?? '';
            $caption = $this->interpolateVariables($payload['caption'] ?? '', $contact);
            $sentText = $caption;

            if (!empty($mediaUrl)) {
                $apiResult = $this->apiService->sendMediaMessage(
                    $vendor,
                    $contact->phone_number,
                    $mediaType,
                    $mediaUrl,
                    $caption
                );
            }
        }

        if (!empty($apiResult['success'])) {
            return WhatsAppMessage::create([
                'vendor_id' => $vendor->id,
                'contact_id' => $contact->id,
                'wamid' => $apiResult['wamid'] ?? null,
                'direction' => 'outbound',
                'message_type' => $replyType,
                'message_text' => $sentText,
                'media_url' => $mediaUrl,
                'status' => 'sent',
                'raw_payload' => $apiResult['data'] ?? null,
                'sent_at' => now(),
            ]);
        }

        return null;
    }

    /**
     * Dispatch AI Copilot generated smart contextual reply.
     */
    public function dispatchAiCopilotReply(Vendor $vendor, Contact $contact, string $incomingText): ?WhatsAppMessage
    {
        $businessBio = $vendor->getSetting('ai_copilot_prompt') ?: "You are a helpful customer support assistant for {$vendor->name}. Respond concisely in the customer's language.";
        $apiKey = $vendor->getSetting('openai_api_key') ?: env('OPENAI_API_KEY');

        $generatedText = null;

        if ($apiKey) {
            try {
                $response = Http::withToken($apiKey)
                    ->timeout(10)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => $businessBio],
                            ['role' => 'user', 'content' => "Customer ({$contact->full_name}) says: {$incomingText}"],
                        ],
                        'max_tokens' => 200,
                    ]);

                if ($response->successful()) {
                    $generatedText = $response->json('choices.0.message.content');
                }
            } catch (\Throwable $e) {
                Log::error('AI Copilot Exception: ' . $e->getMessage());
            }
        }

        // Fallback default message if API not configured or failed
        if (!$generatedText) {
            $generatedText = "Thank you for reaching out to {$vendor->name}! An agent will get back to you shortly.";
        }

        $res = $this->apiService->sendTextMessage($vendor, $contact->phone_number, $generatedText);

        if (!empty($res['success'])) {
            return WhatsAppMessage::create([
                'vendor_id' => $vendor->id,
                'contact_id' => $contact->id,
                'wamid' => $res['wamid'] ?? null,
                'direction' => 'outbound',
                'message_type' => 'text',
                'message_text' => $generatedText,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        return null;
    }

    /**
     * Simulate bot match without sending actual WhatsApp messages.
     */
    public function simulate(Vendor $vendor, string $testMessage): array
    {
        $rule = $this->findMatchingRule($vendor, $testMessage);

        if (!$rule) {
            return [
                'matched' => false,
                'rule' => null,
                'simulated_reply' => null,
            ];
        }

        $payload = (array) ($rule->reply_payload ?: []);
        $previewText = $payload['text'] ?? ($payload['body_text'] ?? '');

        return [
            'matched' => true,
            'rule' => [
                'id' => $rule->id,
                'name' => $rule->name,
                'trigger_type' => $rule->trigger_type,
                'reply_type' => $rule->reply_type,
                'priority' => $rule->priority,
            ],
            'simulated_reply' => [
                'type' => $rule->reply_type,
                'text' => $previewText,
                'payload' => $payload,
            ],
        ];
    }

    /**
     * Interpolate contact fields into text (e.g. {{first_name}}, {{name}}).
     */
    protected function interpolateVariables(string $text, Contact $contact): string
    {
        $replacements = [
            '{{first_name}}' => $contact->first_name ?: 'Customer',
            '{{last_name}}' => $contact->last_name ?: '',
            '{{name}}' => $contact->full_name,
            '{{phone}}' => $contact->phone_number,
            '{{email}}' => $contact->email ?: '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
}
