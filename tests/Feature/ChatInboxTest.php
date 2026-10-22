<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Label;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatInboxTest extends TestCase
{
    use RefreshDatabase;

    protected Vendor $vendor;
    protected User $agent1;
    protected User $agent2;
    protected Contact $contact;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendor = Vendor::create([
            'name' => 'FastTrack Logistics',
            'slug' => 'fasttrack',
            'status' => 'active',
            'whatsapp_status' => 'connected',
            'whatsapp_phone_number_id' => '101122334455',
        ]);

        $this->vendor->setSetting('whatsapp_access_token', 'EAAG_TEST_TOKEN');
        $this->vendor->setSetting('whatsapp_business_account_id', '998877665544');

        $this->agent1 = User::create([
            'name' => 'Agent Alpha',
            'email' => 'alpha@fasttrack.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $this->agent2 = User::create([
            'name' => 'Agent Beta',
            'email' => 'beta@fasttrack.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $this->vendor->users()->attach([
            $this->agent1->id => ['role' => 'agent', 'status' => 'active'],
            $this->agent2->id => ['role' => 'agent', 'status' => 'active'],
        ]);

        $this->contact = Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919811223344',
            'first_name' => 'Deepak',
            'last_name' => 'Chopra',
            'email' => 'deepak@example.com',
            'assigned_user_id' => $this->agent1->id,
            'is_ai_bot_disabled' => false,
        ]);
    }

    /**
     * Test listing conversations with unread counts and latest message.
     */
    public function test_can_list_conversations_with_stats(): void
    {
        // Add inbound unread message
        WhatsAppMessage::create([
            'vendor_id' => $this->vendor->id,
            'contact_id' => $this->contact->id,
            'direction' => 'inbound',
            'message_type' => 'text',
            'message_text' => 'Hello, where is my package?',
            'status' => 'delivered',
            'read_at' => null,
            'created_at' => now()->subMinutes(10),
        ]);

        $response = $this->actingAs($this->agent1)->getJson(route('api.chat.conversations', ['tab' => 'mine']));

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals(1, $response->json('data.data.0.unread_count'));
        $this->assertEquals('Hello, where is my package?', $response->json('data.data.0.latest_message.text'));
    }

    /**
     * Test conversation filtering by tabs (unread, unassigned).
     */
    public function test_conversation_tabs_filtering(): void
    {
        // Unassigned contact with no messages
        Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919811223399',
            'first_name' => 'Unassigned',
            'assigned_user_id' => null,
        ]);

        // Tab: unassigned
        $response = $this->actingAs($this->agent1)->getJson(route('api.chat.conversations', ['tab' => 'unassigned']));
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('Unassigned', $response->json('data.data.0.first_name'));
    }

    /**
     * Test fetching message thread history.
     */
    public function test_can_get_message_thread(): void
    {
        WhatsAppMessage::create([
            'vendor_id' => $this->vendor->id,
            'contact_id' => $this->contact->id,
            'direction' => 'inbound',
            'message_type' => 'text',
            'message_text' => 'Inquiry about pricing',
            'status' => 'delivered',
        ]);

        WhatsAppMessage::create([
            'vendor_id' => $this->vendor->id,
            'contact_id' => $this->contact->id,
            'assigned_user_id' => $this->agent1->id,
            'direction' => 'outbound',
            'message_type' => 'text',
            'message_text' => 'Pricing details sent to your email.',
            'status' => 'sent',
        ]);

        $response = $this->actingAs($this->agent1)->getJson(
            route('api.chat.messages', ['contact' => $this->contact->id])
        );

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.data'));
    }

    /**
     * Test sending real-time 1-on-1 text message.
     */
    public function test_can_send_text_message(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/101122334455/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'contacts' => [['input' => '919811223344', 'wa_id' => '919811223344']],
                'messages' => [['id' => 'wamid.HBgLOTE5ODExMjIzMzQ0FQIAERgSMTAyMDMwNDA1MAA=']],
            ], 200),
        ]);

        $response = $this->actingAs($this->agent1)->postJson(
            route('api.chat.send', ['contact' => $this->contact->id]),
            [
                'type' => 'text',
                'message' => 'Hello Deepak, your order is ready for pickup!',
            ]
        );

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'direction' => 'outbound',
                    'message_type' => 'text',
                    'message_text' => 'Hello Deepak, your order is ready for pickup!',
                    'status' => 'sent',
                    'wamid' => 'wamid.HBgLOTE5ODExMjIzMzQ0FQIAERgSMTAyMDMwNDA1MAA=',
                ],
            ]);

        $this->assertDatabaseHas('whatsapp_messages', [
            'vendor_id' => $this->vendor->id,
            'contact_id' => $this->contact->id,
            'assigned_user_id' => $this->agent1->id,
            'direction' => 'outbound',
            'message_text' => 'Hello Deepak, your order is ready for pickup!',
            'status' => 'sent',
        ]);
    }

    /**
     * Test sending interactive button message.
     */
    public function test_can_send_interactive_buttons_message(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/101122334455/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.BUTTON_MSG_123']],
            ], 200),
        ]);

        $response = $this->actingAs($this->agent1)->postJson(
            route('api.chat.send', ['contact' => $this->contact->id]),
            [
                'type' => 'interactive',
                'body_text' => 'Would you like us to schedule delivery for tomorrow?',
                'buttons' => [
                    ['id' => 'yes_delivery', 'title' => 'Yes, please'],
                    ['id' => 'no_delivery', 'title' => 'No, later'],
                ],
            ]
        );

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'message_type' => 'interactive',
                    'status' => 'sent',
                ],
            ]);
    }

    /**
     * Test reassigning conversation to another agent.
     */
    public function test_can_reassign_agent(): void
    {
        $response = $this->actingAs($this->agent1)->postJson(
            route('api.chat.assign', ['contact' => $this->contact->id]),
            ['user_id' => $this->agent2->id]
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'assigned_user_id' => $this->agent2->id,
                ],
            ]);

        $this->assertEquals($this->agent2->id, $this->contact->fresh()->assigned_user_id);
    }

    /**
     * Test marking unread inbound messages as read.
     */
    public function test_can_mark_conversation_as_read(): void
    {
        $msg = WhatsAppMessage::create([
            'vendor_id' => $this->vendor->id,
            'contact_id' => $this->contact->id,
            'direction' => 'inbound',
            'message_type' => 'text',
            'message_text' => 'Unread question',
            'status' => 'delivered',
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->agent1)->postJson(
            route('api.chat.mark_read', ['contact' => $this->contact->id])
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'marked_count' => 1,
            ]);

        $this->assertNotNull($msg->fresh()->read_at);
        $this->assertEquals('read', $msg->fresh()->status);
    }

    /**
     * Test toggling AI Bot responder for a contact.
     */
    public function test_can_toggle_ai_bot(): void
    {
        $response = $this->actingAs($this->agent1)->postJson(
            route('api.chat.toggle_bot', ['contact' => $this->contact->id]),
            ['disabled' => true]
        );

        $response->assertStatus(200);
        $this->assertTrue($this->contact->fresh()->is_ai_bot_disabled);

        $this->actingAs($this->agent1)->postJson(
            route('api.chat.toggle_bot', ['contact' => $this->contact->id]),
            ['disabled' => false]
        );
        $this->assertFalse($this->contact->fresh()->is_ai_bot_disabled);
    }

    /**
     * Test updating internal notes.
     */
    public function test_can_update_contact_notes(): void
    {
        $response = $this->actingAs($this->agent1)->postJson(
            route('api.chat.notes', ['contact' => $this->contact->id]),
            ['notes' => 'Customer requested callbacks after 5 PM IST.']
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'notes' => 'Customer requested callbacks after 5 PM IST.',
                ],
            ]);

        $this->assertEquals('Customer requested callbacks after 5 PM IST.', $this->contact->fresh()->notes);
    }
}
