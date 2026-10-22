<?php

use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Controllers\Vendor\ContactController;
use App\Http\Controllers\Vendor\ContactCustomFieldController;
use App\Http\Controllers\Vendor\ContactGroupController;
use App\Http\Controllers\Vendor\LabelController;
use App\Http\Controllers\Vendor\WhatsAppTemplateController;
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

// Vendor Audience & CRM Module
Route::prefix('contacts')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('api.contacts.index');
    Route::post('/', [ContactController::class, 'store'])->name('api.contacts.store');
    Route::get('/dummy-template', [ContactController::class, 'downloadDummyTemplate'])->name('api.contacts.dummy_template');
    Route::post('/preview-import', [ContactController::class, 'previewImport'])->name('api.contacts.preview_import');
    Route::post('/process-import', [ContactController::class, 'processImport'])->name('api.contacts.process_import');
    Route::get('/export', [ContactController::class, 'export'])->name('api.contacts.export');
    Route::post('/bulk-delete', [ContactController::class, 'bulkDelete'])->name('api.contacts.bulk_delete');
    Route::post('/bulk-assign-groups', [ContactController::class, 'bulkAssignGroups'])->name('api.contacts.bulk_assign_groups');
    Route::get('/{contact}', [ContactController::class, 'show'])->name('api.contacts.show');
    Route::put('/{contact}', [ContactController::class, 'update'])->name('api.contacts.update');
    Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('api.contacts.destroy');
});

// Contact Groups (Audience Segments)
Route::apiResource('contact-groups', ContactGroupController::class)->names('api.contact_groups');

// Labels / Tags
Route::apiResource('labels', LabelController::class)->names('api.labels');

// Custom Fields
Route::apiResource('custom-fields', ContactCustomFieldController::class)->names('api.custom_fields');

// WhatsApp Templates Management
Route::prefix('whatsapp/templates')->group(function () {
    Route::get('/', [WhatsAppTemplateController::class, 'index'])->name('api.whatsapp_templates.index');
    Route::post('/', [WhatsAppTemplateController::class, 'store'])->name('api.whatsapp_templates.store');
    Route::post('/sync', [WhatsAppTemplateController::class, 'sync'])->name('api.whatsapp_templates.sync');
    Route::get('/{template}', [WhatsAppTemplateController::class, 'show'])->name('api.whatsapp_templates.show');
    Route::post('/{template}/preview', [WhatsAppTemplateController::class, 'preview'])->name('api.whatsapp_templates.preview');
    Route::delete('/{template}', [WhatsAppTemplateController::class, 'destroy'])->name('api.whatsapp_templates.destroy');
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});
