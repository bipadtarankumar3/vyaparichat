<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardWebTest extends TestCase
{
    use RefreshDatabase;

    protected User $vendorUser;
    protected Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendor = Vendor::create([
            'name' => 'Demo SuperStore',
            'slug' => 'demo-superstore',
            'status' => 'active',
            'whatsapp_status' => 'connected',
        ]);

        $this->vendorUser = User::create([
            'name' => 'Store Merchant',
            'email' => 'merchant@demostore.test',
            'password' => Hash::make('secret123'),
            'is_super_admin' => false,
            'status' => 'active',
        ]);

        $this->vendorUser->vendors()->attach($this->vendor->id, [
            'role' => 'owner',
            'permissions' => json_encode(['*']),
            'status' => 'active',
        ]);

        $this->actingAs($this->vendorUser);
    }

    /**
     * Test home & dashboard routes render dedicated vendor.dashboard.index view successfully.
     */
    public function test_dashboard_routes_render_dedicated_blade_view(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.dashboard.index');
        $response->assertSee('Vyapari', false);
        $response->assertSee('Demo SuperStore');
        $response->assertSee('Total Contacts');

        $responseDash = $this->get('/dashboard');
        $responseDash->assertStatus(200);
        $responseDash->assertViewIs('vendor.dashboard.index');
    }

    /**
     * Test live chat page renders dedicated vendor.chat.index blade view.
     */
    public function test_chat_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/chat');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.chat.index');
        $response->assertSee('Search chats');
        $response->assertSee('Select a Conversation');
    }

    /**
     * Test contacts page renders dedicated vendor.contacts.index blade view.
     */
    public function test_contacts_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/contacts');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.contacts.index');
        $response->assertSee('Audience CRM', false);
        $response->assertSee('Excel Template');
    }

    /**
     * Test campaigns page renders dedicated vendor.campaigns.index blade view.
     */
    public function test_campaigns_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/campaigns');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.campaigns.index');
        $response->assertSee('Broadcast Campaigns');
        $response->assertSee('Create Broadcast');
    }

    /**
     * Test templates page renders dedicated vendor.templates.index blade view.
     */
    public function test_templates_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/templates');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.templates.index');
        $response->assertSee('WhatsApp Templates (Meta HSM)');
        $response->assertSee('Sync with Meta');
    }

    /**
     * Test bot automations page renders dedicated vendor.bot.index blade view.
     */
    public function test_bot_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/bot');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.bot.index');
        $response->assertSee('Bot Automations', false);
        $response->assertSee('Dry-Run Simulator');
    }

    /**
     * Test integrations & API page renders dedicated vendor.integrations.index blade view.
     */
    public function test_integrations_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/integrations');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.integrations.index');
        $response->assertSee('Developer REST API v1');
        $response->assertSee('Store Automation Webhooks');
    }

    /**
     * Test billing page renders dedicated vendor.billing.index blade view.
     */
    public function test_billing_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/billing');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.billing.index');
        $response->assertSee('Subscription Plans', false);
        $response->assertSee('Billing', false);
    }

    /**
     * Test settings page renders dedicated vendor.settings.index blade view.
     */
    public function test_settings_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/settings');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.settings.index');
        $response->assertSee('Settings', false);
        $response->assertSee('WhatsApp Cloud API', false);
        $response->assertSee('Meta WhatsApp Cloud API Configuration');
    }

    /**
     * Test agents page renders dedicated vendor.agents.index blade view.
     */
    public function test_agents_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/agents');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.agents.index');
        $response->assertSee('Agents & Team Members', false);
        $response->assertSee('Add New Agent');
    }

    /**
     * Test QR Code page renders dedicated vendor.qr-code.index blade view.
     */
    public function test_qr_code_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/qr-code');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.qr-code.index');
        $response->assertSee('WhatsApp Click-to-Chat QR Code', false);
        $response->assertSee('Scan to Chat');
    }

    /**
     * Test contact groups page renders dedicated vendor.contact-groups.index blade view.
     */
    public function test_contact_groups_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/contacts/groups');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.contact-groups.index');
        $response->assertSee('Audience Contact Groups');
    }

    /**
     * Test custom fields (Add Input) page renders dedicated vendor.custom-fields.index blade view.
     */
    public function test_custom_fields_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/contacts/custom-fields');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.custom-fields.index');
        $response->assertSee('Custom Input Fields', false);
    }

    /**
     * Test bot flows (Flow Maker) page renders dedicated vendor.bot-flows.index blade view.
     */
    public function test_bot_flows_page_renders_dedicated_blade_view(): void
    {
        $response = $this->get('/bot/flows');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.bot-flows.index');
        $response->assertSee('Chatbot Flow Maker');
    }
}
