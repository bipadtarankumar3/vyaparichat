<?php

namespace App\Services\WhatsApp;

use App\Models\Vendor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppApiService
{
    protected string $apiBase = 'https://graph.facebook.com/v20.0';

    /**
     * Get the Vendor WhatsApp access token.
     */
    protected function getAccessToken(Vendor $vendor): ?string
    {
        return $vendor->getSetting('whatsapp_access_token');
    }

    /**
     * Get the Vendor WhatsApp Phone Number ID.
     */
    protected function getPhoneNumberId(Vendor $vendor): ?string
    {
        return $vendor->whatsapp_phone_number_id ?: $vendor->getSetting('whatsapp_phone_number_id');
    }

    /**
     * Get the Vendor WhatsApp Business Account ID (WABA ID).
     */
    protected function getWabaId(Vendor $vendor): ?string
    {
        return $vendor->whatsapp_business_account_id ?: $vendor->getSetting('whatsapp_business_account_id');
    }

    /**
     * Send standard Text message to customer.
     */
    public function sendTextMessage(Vendor $vendor, string $toPhoneNumber, string $messageText): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->cleanPhoneNumber($toPhoneNumber),
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $messageText,
            ],
        ];

        return $this->postMessage($vendor, $payload);
    }

    /**
     * Send Media message (image, video, audio, document).
     */
    public function sendMediaMessage(
        Vendor $vendor,
        string $toPhoneNumber,
        string $mediaType,
        string $mediaUrl,
        ?string $caption = null,
        ?string $filename = null
    ): array {
        $mediaPayload = [
            'link' => $mediaUrl,
        ];

        if ($caption && in_array($mediaType, ['image', 'video', 'document'])) {
            $mediaPayload['caption'] = $caption;
        }

        if ($filename && $mediaType === 'document') {
            $mediaPayload['filename'] = $filename;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->cleanPhoneNumber($toPhoneNumber),
            'type' => $mediaType,
            $mediaType => $mediaPayload,
        ];

        return $this->postMessage($vendor, $payload);
    }

    /**
     * Send approved Template message.
     */
    public function sendTemplateMessage(
        Vendor $vendor,
        string $toPhoneNumber,
        string $templateName,
        string $languageCode = 'en',
        array $components = []
    ): array {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->cleanPhoneNumber($toPhoneNumber),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode,
                ],
                'components' => $components,
            ],
        ];

        return $this->postMessage($vendor, $payload);
    }

    /**
     * Send Interactive Quick Reply Buttons message.
     */
    public function sendInteractiveButtonsMessage(
        Vendor $vendor,
        string $toPhoneNumber,
        string $bodyText,
        array $buttons, // Array of ['id' => 'btn_1', 'title' => 'Yes']
        ?string $headerText = null,
        ?string $footerText = null
    ): array {
        $interactiveButtons = [];
        foreach ($buttons as $button) {
            $interactiveButtons[] = [
                'type' => 'reply',
                'reply' => [
                    'id' => (string) ($button['id'] ?? uniqid()),
                    'title' => substr($button['title'] ?? 'Option', 0, 20),
                ],
            ];
        }

        $interactive = [
            'type' => 'button',
            'body' => ['text' => $bodyText],
            'action' => ['buttons' => $interactiveButtons],
        ];

        if ($headerText) {
            $interactive['header'] = ['type' => 'text', 'text' => $headerText];
        }

        if ($footerText) {
            $interactive['footer'] = ['text' => $footerText];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->cleanPhoneNumber($toPhoneNumber),
            'type' => 'interactive',
            'interactive' => $interactive,
        ];

        return $this->postMessage($vendor, $payload);
    }

    /**
     * Send Interactive List message.
     */
    public function sendInteractiveListMessage(
        Vendor $vendor,
        string $toPhoneNumber,
        string $bodyText,
        string $buttonText,
        array $sections,
        ?string $headerText = null,
        ?string $footerText = null
    ): array {
        $interactive = [
            'type' => 'list',
            'body' => ['text' => $bodyText],
            'action' => [
                'button' => substr($buttonText, 0, 20),
                'sections' => $sections,
            ],
        ];

        if ($headerText) {
            $interactive['header'] = ['type' => 'text', 'text' => $headerText];
        }

        if ($footerText) {
            $interactive['footer'] = ['text' => $footerText];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->cleanPhoneNumber($toPhoneNumber),
            'type' => 'interactive',
            'interactive' => $interactive,
        ];

        return $this->postMessage($vendor, $payload);
    }

    /**
     * Fetch WhatsApp Templates from Meta Graph API.
     */
    public function fetchTemplates(Vendor $vendor, int $limit = 100): array
    {
        $wabaId = $this->getWabaId($vendor);
        $token = $this->getAccessToken($vendor);

        if (!$wabaId || !$token) {
            return ['success' => false, 'error' => 'WhatsApp Cloud API credentials not configured.'];
        }

        try {
            $response = Http::withToken($token)
                ->get("{$this->apiBase}/{$wabaId}/message_templates", [
                    'limit' => $limit,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json('data', []),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('error.message', 'Failed to fetch templates from Meta.'),
            ];
        } catch (\Throwable $e) {
            Log::error('Fetch WhatsApp Templates Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get temporary media URL from Meta Media ID.
     */
    public function getMediaUrl(Vendor $vendor, string $mediaId): ?string
    {
        $token = $this->getAccessToken($vendor);
        if (!$token) {
            return null;
        }

        try {
            $response = Http::withToken($token)->get("{$this->apiBase}/{$mediaId}");
            if ($response->successful()) {
                return $response->json('url');
            }
        } catch (\Throwable $e) {
            Log::error('Fetch Media URL Error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Core POST request to Meta Messages API.
     */
    protected function postMessage(Vendor $vendor, array $payload): array
    {
        $phoneNumberId = $this->getPhoneNumberId($vendor);
        $token = $this->getAccessToken($vendor);

        if (!$phoneNumberId || !$token) {
            return [
                'success' => false,
                'error' => 'WhatsApp Cloud API Phone Number ID or Access Token is missing.',
            ];
        }

        try {
            $response = Http::withToken($token)
                ->asJson()
                ->post("{$this->apiBase}/{$phoneNumberId}/messages", $payload);

            $body = $response->json();

            if ($response->successful()) {
                $wamid = $body['messages'][0]['id'] ?? null;
                return [
                    'success' => true,
                    'wamid' => $wamid,
                    'data' => $body,
                ];
            }

            $errorMessage = $body['error']['message'] ?? 'Failed to send WhatsApp message.';
            Log::warning('WhatsApp Send Error: ' . json_encode($body));

            return [
                'success' => false,
                'error' => $errorMessage,
                'data' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp API Connection Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Clean and normalize phone number into standard international format without '+' or leading zeros.
     */
    public function cleanPhoneNumber(string $number): string
    {
        return preg_replace('/[^0-9]/', '', $number);
    }
}
