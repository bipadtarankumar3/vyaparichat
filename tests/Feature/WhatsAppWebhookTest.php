<?php

namespace Tests\Feature;

use App\Models\BotReply;
use App\Models\Contact;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendor = Vendor::firstOrCreate(
            ['slug' => 'test-vendor'],
            [
                'name' => 'Test Vendor',
                'status' => 'active',
                'whatsapp_status' => 'connected',
            ]
        );

        $this->vendor->setSetting('whatsapp_webhook_verify_token', 'test_secret_token');
    }

    /**
     * Test webhook handshake verification.
     */
    public function test_webhook_verification_handshake(): void
    {
        $challenge = 'challenge_code_123456';

        $url = route('api.whatsapp.webhook.verify', ['vendorUuid' => $this->vendor->uuid]);

        $response = $this->get($url . '?' . http_build_query([
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'test_secret_token',
            'hub_challenge' => $challenge,
        ]));

        $response->assertStatus(200);
        $this->assertEquals($challenge, $response->getContent());
    }

    /**
     * Test inbound message creation and contact auto-creation.
     */
    public function test_inbound_message_creates_contact_and_message(): void
    {
        $payload = [
            'entry' => [
                [
                    'changes' => [
                        [
                            'value' => [
                                'contacts' => [
                                    [
                                        'profile' => ['name' => 'Jane Doe'],
                                        'wa_id' => '919876543210',
                                    ]
                                ],
                                'messages' => [
                                    [
                                        'from' => '919876543210',
                                        'id' => 'wamid.HBgLMTIzNDU2Nzg5MA==',
                                        'timestamp' => time(),
                                        'type' => 'text',
                                        'text' => [
                                            'body' => 'Hello, I want to inquire about pricing.',
                                        ],
                                    ]
                                ],
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson(route('api.whatsapp.webhook.handle', ['vendorUuid' => $this->vendor->uuid]), $payload);
        $response->assertStatus(200);

        // Check contact created
        $contact = Contact::where('vendor_id', $this->vendor->id)
            ->where('phone_number', '919876543210')
            ->first();

        $this->assertNotNull($contact);
        $this->assertEquals('Jane Doe', $contact->first_name);

        // Check message logged
        $message = WhatsAppMessage::where('vendor_id', $this->vendor->id)
            ->where('wamid', 'wamid.HBgLMTIzNDU2Nzg5MA==')
            ->first();

        $this->assertNotNull($message);
        $this->assertEquals('inbound', $message->direction);
        $this->assertEquals('Hello, I want to inquire about pricing.', $message->message_text);
    }

    /**
     * Test delivery receipt status update.
     */
    public function test_status_update_updates_message(): void
    {
        $contact = Contact::firstOrCreate([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876543211',
        ]);

        $message = WhatsAppMessage::create([
            'vendor_id' => $this->vendor->id,
            'contact_id' => $contact->id,
            'wamid' => 'wamid.OUTBOUND_12345',
            'direction' => 'outbound',
            'message_type' => 'text',
            'message_text' => 'Outbound test message',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $payload = [
            'entry' => [
                [
                    'changes' => [
                        [
                            'value' => [
                                'statuses' => [
                                    [
                                        'id' => 'wamid.OUTBOUND_12345',
                                        'status' => 'delivered',
                                        'timestamp' => time(),
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson(route('api.whatsapp.webhook.handle', ['vendorUuid' => $this->vendor->uuid]), $payload);
        $response->assertStatus(200);

        $message->refresh();
        $this->assertEquals('delivered', $message->status);
        $this->assertNotNull($message->delivered_at);
    }

    /**
     * Test keyword bot reply triggers auto-response.
     */
    public function test_bot_reply_auto_responds_to_keyword(): void
    {
        // Setup bot reply for keyword "pricing"
        BotReply::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'Pricing Bot',
            'trigger_type' => 'contains',
            'trigger_keywords' => ['pricing', 'cost', 'rates'],
            'reply_type' => 'text',
            'reply_payload' => ['text' => 'Our starter plan is 499 INR/month.'],
            'is_active' => true,
            'priority' => 10,
        ]);

        // Mock WhatsAppApiService to simulate outgoing response
        $mockApi = Mockery::mock(WhatsAppApiService::class);
        $mockApi->shouldReceive('sendTextMessage')
            ->once()
            ->with(Mockery::any(), '919876543210', 'Our starter plan is 499 INR/month.')
            ->andReturn([
                'success' => true,
                'wamid' => 'wamid.BOT_AUTO_REPLY_1',
                'data' => [],
            ]);

        $this->app->instance(WhatsAppApiService::class, $mockApi);

        $payload = [
            'entry' => [
                [
                    'changes' => [
                        [
                            'value' => [
                                'contacts' => [
                                    ['profile' => ['name' => 'Alice'], 'wa_id' => '919876543210']
                                ],
                                'messages' => [
                                    [
                                        'from' => '919876543210',
                                        'id' => 'wamid.INCOMING_QUESTION_1',
                                        'timestamp' => time(),
                                        'type' => 'text',
                                        'text' => ['body' => 'What is your pricing?'],
                                    ]
                                ],
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson(route('api.whatsapp.webhook.handle', ['vendorUuid' => $this->vendor->uuid]), $payload);
        $response->assertStatus(200);

        // Verify outbound bot reply was recorded in database
        $botMessage = WhatsAppMessage::where('vendor_id', $this->vendor->id)
            ->where('wamid', 'wamid.BOT_AUTO_REPLY_1')
            ->first();

        $this->assertNotNull($botMessage);
        $this->assertEquals('outbound', $botMessage->direction);
        $this->assertEquals('Our starter plan is 499 INR/month.', $botMessage->message_text);
    }
}

