<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use App\Models\WhatsAppTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected Vendor $vendor;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendor = Vendor::create([
            'name' => 'Demo Vendor',
            'slug' => 'demo-vendor',
            'status' => 'active',
            'whatsapp_status' => 'connected',
        ]);

        $this->vendor->setSetting('whatsapp_waba_id', '123456789012345');
        $this->vendor->setSetting('whatsapp_access_token', 'EAAG_MOCK_TOKEN');

        $this->user = User::create([
            'name' => 'Template Manager',
            'email' => 'manager@demo.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $this->vendor->users()->attach($this->user->id, [
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    /**
     * Test syncing templates from Meta Graph API.
     */
    public function test_can_sync_templates_from_meta(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/123456789012345/message_templates*' => Http::response([
                'data' => [
                    [
                        'id' => 'meta_hsm_101',
                        'name' => 'seasonal_sale_alert',
                        'language' => 'en',
                        'category' => 'MARKETING',
                        'status' => 'APPROVED',
                        'components' => [
                            [
                                'type' => 'HEADER',
                                'format' => 'IMAGE',
                            ],
                            [
                                'type' => 'BODY',
                                'text' => 'Hello {{1}}, our Diwali sale is live! Get {{2}} off.',
                            ],
                            [
                                'type' => 'FOOTER',
                                'text' => 'Reply STOP to unsubscribe',
                            ],
                        ],
                    ],
                    [
                        'id' => 'meta_hsm_102',
                        'name' => 'order_delivery_update',
                        'language' => 'en_US',
                        'category' => 'UTILITY',
                        'status' => 'APPROVED',
                        'components' => [
                            [
                                'type' => 'BODY',
                                'text' => 'Hi {{1}}, your order #{{2}} is out for delivery.',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->postJson(route('api.whatsapp_templates.sync'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'synced_count' => 2,
                    'total_from_meta' => 2,
                ],
            ]);

        $this->assertDatabaseHas('whatsapp_templates', [
            'vendor_id' => $this->vendor->id,
            'name' => 'seasonal_sale_alert',
            'language' => 'en',
            'status' => 'APPROVED',
            'header_type' => 'image',
        ]);

        $this->assertDatabaseHas('whatsapp_templates', [
            'vendor_id' => $this->vendor->id,
            'name' => 'order_delivery_update',
            'language' => 'en_US',
            'status' => 'APPROVED',
            'header_type' => 'none',
        ]);
    }

    /**
     * Test creating a template and submitting to Meta.
     */
    public function test_can_create_template_and_submit_to_meta(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/123456789012345/message_templates' => Http::response([
                'id' => 'meta_hsm_201',
                'status' => 'PENDING',
                'category' => 'MARKETING',
            ], 200),
        ]);

        $payload = [
            'name' => 'summer_flash_discount',
            'language' => 'en',
            'category' => 'MARKETING',
            'components' => [
                [
                    'type' => 'HEADER',
                    'format' => 'TEXT',
                    'text' => 'Flash Offer: {{1}}!',
                ],
                [
                    'type' => 'BODY',
                    'text' => 'Dear {{1}}, enjoy exclusive discounts up to {{2}} today only.',
                ],
                [
                    'type' => 'FOOTER',
                    'text' => 'Terms apply',
                ],
                [
                    'type' => 'BUTTONS',
                    'buttons' => [
                        [
                            'type' => 'QUICK_REPLY',
                            'text' => 'Claim Offer',
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->postJson(route('api.whatsapp_templates.store'), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'summer_flash_discount',
                    'category' => 'MARKETING',
                    'status' => 'PENDING',
                    'header_type' => 'text',
                ],
            ]);

        $this->assertDatabaseHas('whatsapp_templates', [
            'vendor_id' => $this->vendor->id,
            'name' => 'summer_flash_discount',
            'meta_template_id' => 'meta_hsm_201',
            'status' => 'PENDING',
        ]);
    }

    /**
     * Test listing and filtering templates.
     */
    public function test_can_list_and_filter_templates(): void
    {
        WhatsAppTemplate::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'welcome_pack',
            'language' => 'en',
            'category' => 'MARKETING',
            'status' => 'APPROVED',
            'components' => [['type' => 'BODY', 'text' => 'Welcome to Vyapari!']],
        ]);

        WhatsAppTemplate::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'otp_verification',
            'language' => 'en',
            'category' => 'AUTHENTICATION',
            'status' => 'APPROVED',
            'components' => [['type' => 'BODY', 'text' => 'Your OTP is {{1}}.']],
        ]);

        // Filter by category AUTHENTICATION
        $response = $this->actingAs($this->user)->getJson(route('api.whatsapp_templates.index', ['category' => 'AUTHENTICATION']));
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('otp_verification', $response->json('data.data.0.name'));

        // Search for 'welcome'
        $response = $this->actingAs($this->user)->getJson(route('api.whatsapp_templates.index', ['search' => 'welcome']));
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('welcome_pack', $response->json('data.data.0.name'));
    }

    /**
     * Test template preview rendering with variables.
     */
    public function test_can_render_template_preview(): void
    {
        $template = WhatsAppTemplate::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'appointment_reminder',
            'language' => 'en',
            'category' => 'UTILITY',
            'status' => 'APPROVED',
            'components' => [
                [
                    'type' => 'HEADER',
                    'format' => 'TEXT',
                    'text' => 'Hello {{1}}',
                ],
                [
                    'type' => 'BODY',
                    'text' => 'Your appointment with Dr. {{1}} is confirmed for {{2}} at {{3}}.',
                ],
                [
                    'type' => 'FOOTER',
                    'text' => 'Vyapari Health Care',
                ],
            ],
        ]);

        $response = $this->actingAs($this->user)->postJson(
            route('api.whatsapp_templates.preview', ['template' => $template->id]),
            [
                'sample_parameters' => [
                    'header' => ['Sarah'],
                    'body' => ['Smith', 'Monday', '10:00 AM'],
                ],
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'preview' => [
                    'header' => [
                        'format' => 'TEXT',
                        'text' => 'Hello Sarah',
                    ],
                    'body' => 'Your appointment with Dr. Smith is confirmed for Monday at 10:00 AM.',
                    'footer' => 'Vyapari Health Care',
                ],
            ]);
    }

    /**
     * Test deleting a template from Meta and DB.
     */
    public function test_can_delete_template(): void
    {
        $template = WhatsAppTemplate::create([
            'vendor_id' => $this->vendor->id,
            'meta_template_id' => 'meta_hsm_301',
            'name' => 'to_be_deleted',
            'language' => 'en',
            'category' => 'MARKETING',
            'status' => 'APPROVED',
            'components' => [['type' => 'BODY', 'text' => 'Delete me']],
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/123456789012345/message_templates*' => Http::response([
                'success' => true,
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->deleteJson(
            route('api.whatsapp_templates.destroy', ['template' => $template->id])
        );

        $response->assertStatus(200);
        $this->assertDatabaseMissing('whatsapp_templates', ['id' => $template->id]);
    }
}
