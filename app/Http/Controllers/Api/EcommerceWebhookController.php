<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Services\Ecommerce\EcommerceAutomationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EcommerceWebhookController extends Controller
{
    public function __construct(
        protected EcommerceAutomationService $ecommerceService
    ) {}

    /**
     * Handle Shopify new order webhook.
     */
    public function shopifyOrderCreated(Request $request, string $vendorUuid): JsonResponse
    {
        $vendor = Vendor::where('uuid', $vendorUuid)->firstOrFail();
        $message = $this->ecommerceService->handleShopifyOrderCreated($vendor, $request->all());

        return response()->json([
            'success' => true,
            'message_dispatched' => (bool) $message,
        ]);
    }

    /**
     * Handle Shopify order fulfillment / tracking webhook.
     */
    public function shopifyOrderFulfilled(Request $request, string $vendorUuid): JsonResponse
    {
        $vendor = Vendor::where('uuid', $vendorUuid)->firstOrFail();
        $message = $this->ecommerceService->handleShopifyOrderFulfilled($vendor, $request->all());

        return response()->json([
            'success' => true,
            'message_dispatched' => (bool) $message,
        ]);
    }

    /**
     * Handle WooCommerce order webhooks.
     */
    public function woocommerceOrder(Request $request, string $vendorUuid): JsonResponse
    {
        $vendor = Vendor::where('uuid', $vendorUuid)->firstOrFail();
        $event = $request->header('X-WC-Webhook-Event', 'created');
        $message = $this->ecommerceService->handleWooCommerceOrder($vendor, $request->all(), $event);

        return response()->json([
            'success' => true,
            'message_dispatched' => (bool) $message,
        ]);
    }
}
