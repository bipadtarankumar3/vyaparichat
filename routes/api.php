<?php

use App\Http\Controllers\Api\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Clean REST API and Webhook endpoints for VyapariChat V2.
|
*/

// Meta WhatsApp Cloud API Webhook per Vendor
Route::prefix('webhook/whatsapp/{vendorUuid}')->group(function () {
    Route::get('', [WhatsAppWebhookController::class, 'verify'])->name('api.whatsapp.webhook.verify');
    Route::post('', [WhatsAppWebhookController::class, 'handle'])->name('api.whatsapp.webhook.handle');
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});
