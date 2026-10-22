<?php

namespace App\Services\Ecommerce;

use App\Models\Contact;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppApiService;
use Illuminate\Support\Facades\Log;

class EcommerceAutomationService
{
    public function __construct(
        protected WhatsAppApiService $apiService
    ) {}

    /**
     * Handle Shopify Order Creation Webhook.
     */
    public function handleShopifyOrderCreated(Vendor $vendor, array $orderData): ?WhatsAppMessage
    {
        $rawPhone = $orderData['customer']['phone']
            ?? ($orderData['shipping_address']['phone']
            ?? ($orderData['billing_address']['phone'] ?? ''));

        $phone = $this->apiService->cleanPhoneNumber($rawPhone);
        if (strlen($phone) < 8) {
            Log::info("Shopify Order Created: No valid phone found for order #" . ($orderData['name'] ?? ''));
            return null;
        }

        $firstName = $orderData['customer']['first_name'] ?? 'Valued Customer';
        $orderNumber = $orderData['name'] ?? ('#' . ($orderData['order_number'] ?? ''));
        $total = $orderData['total_price'] ?? '0.00';
        $currency = $orderData['currency'] ?? 'INR';

        // Upsert contact
        $contact = Contact::updateOrCreate(
            ['vendor_id' => $vendor->id, 'phone_number' => $phone],
            ['first_name' => $firstName, 'email' => $orderData['customer']['email'] ?? null]
        );

        $text = "Hi {$firstName}, thank you for shopping with us! 🎉 Your order {$orderNumber} for {$currency} {$total} has been confirmed. We will notify you as soon as it ships!";

        $apiResult = $this->apiService->sendTextMessage($vendor, $phone, $text);

        if ($apiResult['success']) {
            return WhatsAppMessage::create([
                'vendor_id' => $vendor->id,
                'contact_id' => $contact->id,
                'wamid' => $apiResult['wamid'] ?? null,
                'direction' => 'outbound',
                'message_type' => 'text',
                'message_text' => $text,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        return null;
    }

    /**
     * Handle Shopify Order Fulfillment / Shipping Webhook.
     */
    public function handleShopifyOrderFulfilled(Vendor $vendor, array $fulfillmentData): ?WhatsAppMessage
    {
        $rawPhone = $fulfillmentData['destination']['phone']
            ?? ($fulfillmentData['order']['customer']['phone'] ?? '');

        $phone = $this->apiService->cleanPhoneNumber($rawPhone);
        if (strlen($phone) < 8) {
            return null;
        }

        $trackingNumber = $fulfillmentData['tracking_number'] ?? '';
        $trackingUrl = $fulfillmentData['tracking_url'] ?? '';
        $company = $fulfillmentData['tracking_company'] ?? 'Courier';

        $contact = Contact::firstOrCreate(
            ['vendor_id' => $vendor->id, 'phone_number' => $phone],
            ['first_name' => 'Customer']
        );

        $text = "Great news! 🚚 Your order has shipped via {$company}.\nTracking Number: {$trackingNumber}";
        if ($trackingUrl) {
            $text .= "\nTrack your parcel here: {$trackingUrl}";
        }

        $apiResult = $this->apiService->sendTextMessage($vendor, $phone, $text);

        if ($apiResult['success']) {
            return WhatsAppMessage::create([
                'vendor_id' => $vendor->id,
                'contact_id' => $contact->id,
                'wamid' => $apiResult['wamid'] ?? null,
                'direction' => 'outbound',
                'message_type' => 'text',
                'message_text' => $text,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        return null;
    }

    /**
     * Handle WooCommerce Order Webhooks.
     */
    public function handleWooCommerceOrder(Vendor $vendor, array $orderData, string $event = 'created'): ?WhatsAppMessage
    {
        $rawPhone = $orderData['billing']['phone'] ?? ($orderData['shipping']['phone'] ?? '');
        $phone = $this->apiService->cleanPhoneNumber($rawPhone);

        if (strlen($phone) < 8) {
            return null;
        }

        $firstName = $orderData['billing']['first_name'] ?? 'Customer';
        $orderId = $orderData['id'] ?? '';
        $total = $orderData['total'] ?? '0.00';
        $status = $orderData['status'] ?? 'processing';

        $contact = Contact::updateOrCreate(
            ['vendor_id' => $vendor->id, 'phone_number' => $phone],
            ['first_name' => $firstName, 'email' => $orderData['billing']['email'] ?? null]
        );

        if ($event === 'created' || $status === 'processing') {
            $text = "Hello {$firstName}! We received your order #{$orderId} (Total: ₹{$total}). We're preparing it now!";
        } elseif ($status === 'completed') {
            $text = "Hello {$firstName}! Your order #{$orderId} has been successfully completed. Thank you for choosing us!";
        } else {
            $text = "Hello {$firstName}! Your order #{$orderId} status has been updated to: " . ucfirst($status) . ".";
        }

        $apiResult = $this->apiService->sendTextMessage($vendor, $phone, $text);

        if ($apiResult['success']) {
            return WhatsAppMessage::create([
                'vendor_id' => $vendor->id,
                'contact_id' => $contact->id,
                'wamid' => $apiResult['wamid'] ?? null,
                'direction' => 'outbound',
                'message_type' => 'text',
                'message_text' => $text,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        return null;
    }
}
