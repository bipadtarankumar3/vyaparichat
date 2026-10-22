<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EcommerceAndPublicApiTest extends TestCase
{
    use RefreshDatabase;

    protected Vendor $vendor;
    protected SubscriptionPlan $proPlan;
    protected string $apiKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendor = Vendor::create([
            'name' => 'Fashion Hub',
            'slug' => 'fashion-hub',
            'status' => 'active',
            'whatsapp_status' => 'connected',
            'whatsapp_phone_number_id' => '1029384756',
        ]);

        $this->vendor->setSetting('whatsapp_access_token', 'EAAG_TEST_TOKEN');
        $this->vendor->setSetting('whatsapp_business_account_id', '123456789012345');

        $this->apiKey = $this->vendor->generateApiKey();

        $this->proPlan = SubscriptionPlan::create([
            'title' => 'Enterprise Pro',
            'slug' => 'enterprise-pro',
            'price_monthly' => 4999.00,
            'price_yearly' => 49990.00,
            'max_contacts' => 50000,
            'max_campaigns_per_month' => 1000,
            'max_staff_members' => 20,
            'has_ai_bot' => true,
            'has_api_access' => true,
            'is_active' => true,
        ]);

        Subscription::create([
            'vendor_id' => $this->vendor->id,
            'subscription_plan_id' => $this->proPlan->id,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);
    }

    /**
     * Test Shopify order creation webhook sends confirmation WhatsApp message.
     */
    public function test_shopify_order_created_webhook(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/1029384756/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.SHOPIFY_ORDER_001']],
            ], 200),
        ]);

        $payload = [
            'name' => '#1001',
            'total_price' => '1499.00',
            'currency' => 'INR',
            'customer' => [
                'first_name' => 'Aarav',
                'email' => 'aarav@example.com',
                'phone' => '+91 9822334455',
            ],
        ];

        $response = $this->postJson(
            route('api.ecommerce.shopify.order_created', ['vendorUuid' => $this->vendor->uuid]),
            $payload
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message_dispatched' => true,
            ]);

        $this->assertDatabaseHas('contacts', [
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919822334455',
            'first_name' => 'Aarav',
        ]);

        $this->assertDatabaseHas('whatsapp_messages', [
            'vendor_id' => $this->vendor->id,
            'wamid' => 'wamid.SHOPIFY_ORDER_001',
            'status' => 'sent',
        ]);
    }

    /**
     * Test Shopify order fulfillment webhook sends tracking link.
     */
    public function test_shopify_order_fulfilled_webhook(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/1029384756/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.SHOPIFY_FULFILL_001']],
            ], 200),
        ]);

        $payload = [
            'tracking_number' => 'AWB9988776655',
            'tracking_company' => 'BlueDart Express',
            'tracking_url' => 'https://bluedart.com/track/AWB9988776655',
            'destination' => [
                'phone' => '+91 9822334455',
            ],
        ];

        $response = $this->postJson(
            route('api.ecommerce.shopify.order_fulfilled', ['vendorUuid' => $this->vendor->uuid]),
            $payload
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message_dispatched' => true,
            ]);

        $this->assertDatabaseHas('whatsapp_messages', [
            'vendor_id' => $this->vendor->id,
            'wamid' => 'wamid.SHOPIFY_FULFILL_001',
            'status' => 'sent',
        ]);
    }

    /**
     * Test WooCommerce order webhook.
     */
    public function test_woocommerce_order_webhook(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/1029384756/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.WC_ORDER_001']],
            ], 200),
        ]);

        $payload = [
            'id' => 54321,
            'total' => '2500.00',
            'status' => 'processing',
            'billing' => [
                'first_name' => 'Meera',
                'phone' => '919833445566',
                'email' => 'meera@example.com',
            ],
        ];

        $response = $this->postJson(
            route('api.ecommerce.woocommerce.order', ['vendorUuid' => $this->vendor->uuid]),
            $payload
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message_dispatched' => true,
            ]);

        $this->assertDatabaseHas('contacts', [
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919833445566',
            'first_name' => 'Meera',
        ]);
    }

    /**
     * Test Public API rejects requests without API key.
     */
    public function test_public_api_requires_api_key(): void
    {
        $response = $this->postJson(route('api.v1.send_message'), [
            'phone_number' => '919800000001',
            'type' => 'text',
            'message' => 'Hello',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test Public API sends text message with valid API key.
     */
    public function test_public_api_can_send_text_message(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/1029384756/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.PUBLIC_API_MSG_001']],
            ], 200),
        ]);

        $response = $this->withHeaders([
            'X-API-KEY' => $this->apiKey,
        ])->postJson(route('api.v1.send_message'), [
            'phone_number' => '+91 98777 66554',
            'type' => 'text',
            'message' => 'Your OTP for Zoho CRM verification is 482910.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'wamid' => 'wamid.PUBLIC_API_MSG_001',
                'status' => 'sent',
            ]);

        $this->assertDatabaseHas('whatsapp_messages', [
            'vendor_id' => $this->vendor->id,
            'wamid' => 'wamid.PUBLIC_API_MSG_001',
            'message_text' => 'Your OTP for Zoho CRM verification is 482910.',
        ]);
    }

    /**
     * Test Public API can upsert contact from external CRM.
     */
    public function test_public_api_can_upsert_contact(): void
    {
        $response = $this->withHeaders([
            'X-API-KEY' => $this->apiKey,
        ])->postJson(route('api.v1.contacts_upsert'), [
            'phone_number' => '+91 98888 77777',
            'first_name' => 'Vikram',
            'last_name' => 'Singhania',
            'email' => 'vikram@singhania.com',
            'notes' => 'Imported from Salesforce Lead ID 9920',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'phone_number' => '919888877777',
                    'first_name' => 'Vikram',
                    'last_name' => 'Singhania',
                ],
            ]);

        $this->assertDatabaseHas('contacts', [
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919888877777',
            'notes' => 'Imported from Salesforce Lead ID 9920',
        ]);
    }
}
