<?php

namespace Tests\Feature;

use App\Models\BotFlow;
use App\Models\BotReply;
use App\Models\Contact;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use App\Services\Bot\BotAutomationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BotAutomationTest extends TestCase
{
    use RefreshDatabase;

    protected Vendor $vendor;
    protected User $user;
    protected Contact $contact;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendor = Vendor::create([
            'name' => 'AutoRetail Corp',
            'slug' => 'auto-retail',
            'status' => 'active',
            'whatsapp_status' => 'connected',
            'whatsapp_phone_number_id' => '109876543210',
        ]);

        $this->vendor->setSetting('whatsapp_access_token', 'EAAG_TEST_TOKEN');
        $this->vendor->setSetting('whatsapp_business_account_id', '123456789012345');

        $this->user = User::create([
            'name' => 'Bot Admin',
            'email' => 'botadmin@retail.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $this->vendor->users()->attach($this->user->id, [
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->contact = Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919800112233',
            'first_name' => 'Kiran',
            'last_name' => 'Rao',
            'is_ai_bot_disabled' => false,
        ]);
    }

    /**
     * Test creating a BotReply rule via API.
     */
    public function test_can_create_bot_reply(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('api.bot_replies.store'), [
            'name' => 'Pricing Inquiry Bot',
            'trigger_type' => 'contains',
            'trigger_keywords' => ['price', 'pricing', 'cost', 'rate'],
            'reply_type' => 'text',
            'reply_payload' => [
                'text' => 'Hello {{first_name}}, our basic plan starts at ₹499/mo.',
            ],
            'is_active' => true,
            'priority' => 10,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Pricing Inquiry Bot',
                    'trigger_type' => 'contains',
                    'priority' => 10,
                ],
            ]);

        $this->assertDatabaseHas('bot_replies', [
            'vendor_id' => $this->vendor->id,
            'name' => 'Pricing Inquiry Bot',
            'trigger_type' => 'contains',
        ]);
    }

    /**
     * Test priority matching and variable interpolation.
     */
    public function test_priority_matching_and_variable_interpolation(): void
    {
        // Lower priority contains rule
        BotReply::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'General Greeting',
            'trigger_type' => 'contains',
            'trigger_keywords' => ['hello', 'hi'],
            'reply_type' => 'text',
            'reply_payload' => ['text' => 'General response'],
            'priority' => 5,
        ]);

        // Higher priority exact rule with interpolation
        BotReply::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'Exact Hi Match',
            'trigger_type' => 'exact',
            'trigger_keywords' => ['hi'],
            'reply_type' => 'text',
            'reply_payload' => ['text' => 'Welcome {{first_name}} to AutoRetail!'],
            'priority' => 50,
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/109876543210/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.BOT_REPLY_123']],
            ], 200),
        ]);

        $botService = app(BotAutomationService::class);
        $message = $botService->matchAndReply($this->vendor, $this->contact, 'Hi');

        $this->assertNotNull($message);
        $this->assertEquals('Welcome Kiran to AutoRetail!', $message->message_text);
        $this->assertEquals('outbound', $message->direction);
        $this->assertEquals('wamid.BOT_REPLY_123', $message->wamid);
    }

    /**
     * Test bot reply simulator endpoint.
     */
    public function test_bot_simulator_endpoint(): void
    {
        BotReply::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'Support Hours',
            'trigger_type' => 'contains',
            'trigger_keywords' => ['hours', 'timings'],
            'reply_type' => 'text',
            'reply_payload' => ['text' => 'We are open Monday to Saturday from 9 AM to 7 PM.'],
            'priority' => 20,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('api.bot_replies.simulate'), [
            'message' => 'What are your working hours?',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'matched' => true,
                    'rule' => [
                        'name' => 'Support Hours',
                    ],
                    'simulated_reply' => [
                        'text' => 'We are open Monday to Saturday from 9 AM to 7 PM.',
                    ],
                ],
            ]);
    }

    /**
     * Test fallback rule when no keywords match.
     */
    public function test_fallback_rule_matches_when_no_keywords_match(): void
    {
        BotReply::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'Default Fallback',
            'trigger_type' => 'fallback',
            'trigger_keywords' => [],
            'reply_type' => 'text',
            'reply_payload' => ['text' => 'I did not understand that. Please type MENU.'],
            'priority' => 0,
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/109876543210/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.FALLBACK_123']],
            ], 200),
        ]);

        $botService = app(BotAutomationService::class);
        $message = $botService->matchAndReply($this->vendor, $this->contact, 'Unknown query xyz 123');

        $this->assertNotNull($message);
        $this->assertEquals('I did not understand that. Please type MENU.', $message->message_text);
    }

    /**
     * Test AI Copilot integration fallback.
     */
    public function test_ai_copilot_fallback(): void
    {
        $this->vendor->setSetting('ai_copilot_enabled', 'true');
        $this->vendor->setSetting('openai_api_key', 'sk-mock-openai-key');

        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Hello Kiran! Yes, we have express delivery options available.']],
                ],
            ], 200),
            'https://graph.facebook.com/v20.0/109876543210/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.AI_COPILOT_123']],
            ], 200),
        ]);

        $botService = app(BotAutomationService::class);
        $message = $botService->matchAndReply($this->vendor, $this->contact, 'Do you offer same day shipping?');

        $this->assertNotNull($message);
        $this->assertEquals('Hello Kiran! Yes, we have express delivery options available.', $message->message_text);
    }

    /**
     * Test visual bot flow CRUD.
     */
    public function test_visual_bot_flow_crud(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('api.bot_flows.store'), [
            'title' => 'Customer Onboarding Journey',
            'flow_data' => [
                'nodes' => [
                    ['id' => '1', 'type' => 'trigger', 'data' => ['keyword' => 'start']],
                    ['id' => '2', 'type' => 'message', 'data' => ['text' => 'Welcome!']],
                ],
                'edges' => [
                    ['source' => '1', 'target' => '2'],
                ],
            ],
            'is_active' => true,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Customer Onboarding Journey',
                ],
            ]);

        $flowId = $response->json('data.id');

        $this->actingAs($this->user)
            ->deleteJson(route('api.bot_flows.destroy', ['bot_flow' => $flowId]))
            ->assertStatus(200);

        $this->assertDatabaseMissing('bot_flows', ['id' => $flowId]);
    }
}
