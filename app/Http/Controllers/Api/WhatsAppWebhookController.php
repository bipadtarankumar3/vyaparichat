<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Services\WhatsApp\WhatsAppWebhookHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected WhatsAppWebhookHandler $webhookHandler
    ) {}

    /**
     * Webhook Handshake Verification (Meta calls this on setup).
     */
    public function verify(Request $request, string $vendorUuid): Response
    {
        $vendor = Vendor::where('uuid', $vendorUuid)->first();

        if (!$vendor) {
            return response('Vendor not found', 404);
        }

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $configuredToken = $vendor->getSetting('whatsapp_webhook_verify_token') ?: 'vyapari_webhook_secret';

        if ($mode === 'subscribe' && $token === $configuredToken) {
            Log::info("WhatsApp Webhook verified for vendor: {$vendor->name}");
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning("WhatsApp Webhook verification failed for vendor: {$vendor->name}");
        return response('Forbidden', 403);
    }

    /**
     * Inbound Webhook Event Processor (Meta posts events here).
     */
    public function handle(Request $request, string $vendorUuid): Response
    {
        $vendor = Vendor::where('uuid', $vendorUuid)->first();

        if (!$vendor) {
            return response('Vendor not found', 404);
        }

        try {
            $this->webhookHandler->handle($vendor, $request->all());
        } catch (\Throwable $e) {
            Log::error("WhatsApp Webhook Handling Exception [{$vendor->name}]: " . $e->getMessage());
        }

        // Meta requires a fast 200 OK response
        return response('EVENT_RECEIVED', 200);
    }
}
