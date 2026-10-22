<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicApiController extends Controller
{
    public function __construct(
        protected WhatsAppApiService $apiService
    ) {}

    /**
     * Helper to get authenticated vendor from request attribute.
     */
    protected function getVendor(Request $request): Vendor
    {
        return $request->attributes->get('vendor');
    }

    /**
     * Send direct message via Public API.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $vendor = $this->getVendor($request);

        $validated = $request->validate([
            'phone_number' => 'required|string|min:8|max:20',
            'type' => 'required|string|in:text,media',
            'message' => 'required_if:type,text|nullable|string',
            'media_type' => 'required_if:type,media|nullable|string|in:image,video,audio,document',
            'media_url' => 'required_if:type,media|nullable|url',
            'caption' => 'nullable|string',
        ]);

        $phone = $this->apiService->cleanPhoneNumber($validated['phone_number']);
        $contact = Contact::firstOrCreate(
            ['vendor_id' => $vendor->id, 'phone_number' => $phone],
            ['first_name' => 'Customer']
        );

        if ($validated['type'] === 'text') {
            $apiResult = $this->apiService->sendTextMessage($vendor, $phone, $validated['message']);
            $text = $validated['message'];
            $mediaUrl = null;
        } else {
            $apiResult = $this->apiService->sendMediaMessage(
                $vendor,
                $phone,
                $validated['media_type'],
                $validated['media_url'],
                $validated['caption'] ?? null
            );
            $text = $validated['caption'] ?? null;
            $mediaUrl = $validated['media_url'];
        }

        if (!$apiResult['success']) {
            return response()->json([
                'success' => false,
                'error' => $apiResult['error'] ?? 'Failed to send message via WhatsApp Cloud API.',
            ], 422);
        }

        $message = WhatsAppMessage::create([
            'vendor_id' => $vendor->id,
            'contact_id' => $contact->id,
            'wamid' => $apiResult['wamid'] ?? null,
            'direction' => 'outbound',
            'message_type' => $validated['type'] === 'text' ? 'text' : $validated['media_type'],
            'message_text' => $text,
            'media_url' => $mediaUrl,
            'status' => 'sent',
            'sent_at' => now(),
            'raw_payload' => $apiResult['data'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message_id' => $message->uuid,
            'wamid' => $message->wamid,
            'status' => 'sent',
        ], 201);
    }

    /**
     * Send approved template message via Public API.
     */
    public function sendTemplate(Request $request): JsonResponse
    {
        $vendor = $this->getVendor($request);

        $validated = $request->validate([
            'phone_number' => 'required|string|min:8|max:20',
            'template_name' => 'required|string',
            'language_code' => 'nullable|string|max:10',
            'components' => 'nullable|array',
        ]);

        $phone = $this->apiService->cleanPhoneNumber($validated['phone_number']);
        $contact = Contact::firstOrCreate(
            ['vendor_id' => $vendor->id, 'phone_number' => $phone],
            ['first_name' => 'Customer']
        );

        $lang = $validated['language_code'] ?? 'en';
        $components = $validated['components'] ?? [];

        $apiResult = $this->apiService->sendTemplateMessage(
            $vendor,
            $phone,
            $validated['template_name'],
            $lang,
            $components
        );

        if (!$apiResult['success']) {
            return response()->json([
                'success' => false,
                'error' => $apiResult['error'] ?? 'Failed to send template message via WhatsApp Cloud API.',
            ], 422);
        }

        $message = WhatsAppMessage::create([
            'vendor_id' => $vendor->id,
            'contact_id' => $contact->id,
            'wamid' => $apiResult['wamid'] ?? null,
            'direction' => 'outbound',
            'message_type' => 'template',
            'status' => 'sent',
            'sent_at' => now(),
            'raw_payload' => [
                'template_name' => $validated['template_name'],
                'components' => $components,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message_id' => $message->uuid,
            'wamid' => $message->wamid,
            'status' => 'sent',
        ], 201);
    }

    /**
     * Upsert contact from external CRM / ERP via Public API.
     */
    public function upsertContact(Request $request): JsonResponse
    {
        $vendor = $this->getVendor($request);

        $validated = $request->validate([
            'phone_number' => 'required|string|min:8|max:20',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
            'notes' => 'nullable|string',
        ]);

        $phone = $this->apiService->cleanPhoneNumber($validated['phone_number']);
        $validated['phone_number'] = $phone;

        $contact = Contact::updateOrCreate(
            ['vendor_id' => $vendor->id, 'phone_number' => $phone],
            array_filter($validated)
        );

        return response()->json([
            'success' => true,
            'data' => $contact,
        ]);
    }
}
