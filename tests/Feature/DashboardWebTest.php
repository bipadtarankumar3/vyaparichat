<?php

namespace Tests\Feature;

use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardWebTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Vendor::create([
            'name' => 'Demo SuperStore',
            'slug' => 'demo-superstore',
            'status' => 'active',
            'whatsapp_status' => 'connected',
        ]);
    }

    /**
     * Test home & dashboard routes render dedicated pages.dashboard view successfully.
     */
    public function test_dashboard_routes_render_dedicated_blade_view(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('pages.dashboard');
        $response->assertSee('Vyapari', false);
        $response->assertSee('Demo SuperStore');
        $response->assertSee('Total Contacts');

        $responseDash = $this->get('/dashboard');
        $responseDash->assertStatus(200);
        $responseDash->assertViewIs('pages.dashboard');
    }

    /**
     * Test live chat page renders dedicated pages.chat blade view.
     */
    public function test_chat_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/chat');
        $response->assertStatus(200);
        $response->assertViewIs('pages.chat');
        $response->assertSee('Search chats');
        $response->assertSee('Select a Conversation');
    }

    /**
     * Test contacts page renders dedicated pages.contacts blade view.
     */
    public function test_contacts_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/contacts');
        $response->assertStatus(200);
        $response->assertViewIs('pages.contacts');
        $response->assertSee('Audience CRM', false);
        $response->assertSee('Excel Template');
    }

    /**
     * Test campaigns page renders dedicated pages.campaigns blade view.
     */
    public function test_campaigns_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/campaigns');
        $response->assertStatus(200);
        $response->assertViewIs('pages.campaigns');
        $response->assertSee('Broadcast Campaigns');
        $response->assertSee('Create Broadcast');
    }

    /**
     * Test templates page renders dedicated pages.templates blade view.
     */
    public function test_templates_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/templates');
        $response->assertStatus(200);
        $response->assertViewIs('pages.templates');
        $response->assertSee('WhatsApp Templates (Meta HSM)');
        $response->assertSee('Sync with Meta');
    }

    /**
     * Test bot automations page renders dedicated pages.bot blade view.
     */
    public function test_bot_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/bot');
        $response->assertStatus(200);
        $response->assertViewIs('pages.bot');
        $response->assertSee('Bot Automations', false);
        $response->assertSee('Dry-Run Simulator');
    }

    /**
     * Test integrations & API page renders dedicated pages.integrations blade view.
     */
    public function test_integrations_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/integrations');
        $response->assertStatus(200);
        $response->assertViewIs('pages.integrations');
        $response->assertSee('Developer REST API v1');
        $response->assertSee('Store Automation Webhooks');
    }

    /**
     * Test billing page renders dedicated pages.billing blade view.
     */
    public function test_billing_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/billing');
        $response->assertStatus(200);
        $response->assertViewIs('pages.billing');
        $response->assertSee('Subscription Plans', false);
        $response->assertSee('Billing', false);
    }

    /**
     * Test settings page renders dedicated pages.settings blade view.
     */
    public function test_settings_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/settings');
        $response->assertStatus(200);
        $response->assertViewIs('pages.settings');
        $response->assertSee('Settings', false);
        $response->assertSee('WhatsApp Cloud API', false);
        $response->assertSee('Meta WhatsApp Cloud API Configuration');
    }

    /**
     * Test agents page renders dedicated pages.agents blade view.
     */
    public function test_agents_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/agents');
        $response->assertStatus(200);
        $response->assertViewIs('pages.agents');
        $response->assertSee('Agents & Team Members', false);
        $response->assertSee('Add New Agent');
    }

    /**
     * Test QR Code page renders dedicated pages.qr-code blade view.
     */
    public function test_qr_code_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/qr-code');
        $response->assertStatus(200);
        $response->assertViewIs('pages.qr-code');
        $response->assertSee('WhatsApp Click-to-Chat QR Code', false);
        $response->assertSee('Scan to Chat');
    }

    /**
     * Test contact groups page renders dedicated pages.contact-groups blade view.
     */
    public function test_contact_groups_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/contacts/groups');
        $response->assertStatus(200);
        $response->assertViewIs('pages.contact-groups');
        $response->assertSee('Audience Contact Groups');
    }

    /**
     * Test custom fields (Add Input) page renders dedicated pages.custom-fields blade view.
     */
    public function test_custom_fields_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/contacts/custom-fields');
        $response->assertStatus(200);
        $response->assertViewIs('pages.custom-fields');
        $response->assertSee('Custom Input Fields', false);
    }

    /**
     * Test bot flows (Flow Maker) page renders dedicated pages.bot-flows blade view.
     */
    public function test_bot_flows_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/bot/flows');
        $response->assertStatus(200);
        $response->assertViewIs('pages.bot-flows');
        $response->assertSee('Chatbot Flow Maker');
    }
}
