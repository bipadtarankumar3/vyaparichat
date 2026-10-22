<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\BotFlow;
use App\Models\BotRule;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\CustomField;
use App\Models\Language;
use App\Models\ManualPaymentRequest;
use App\Models\Page;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CrudOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $vendorUser;
    protected Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@vyaparichat.test',
            'password' => Hash::make('password123'),
            'is_super_admin' => true,
            'status' => 'active',
        ]);

        $this->vendorUser = User::create([
            'name' => 'Vendor Merchant',
            'email' => 'merchant@vyaparichat.test',
            'password' => Hash::make('password123'),
            'is_super_admin' => false,
            'status' => 'active',
        ]);

        $this->vendor = Vendor::create([
            'name' => 'Vyapari Test Store',
            'slug' => 'vyapari-test-store',
            'email' => 'merchant@vyaparichat.test',
            'phone' => '+919876543210',
            'status' => 'active',
        ]);

        $this->vendorUser->vendors()->attach($this->vendor->id, [
            'role' => 'owner',
            'permissions' => json_encode(['*']),
            'status' => 'active',
        ]);
    }

    public function test_contact_crud_operations(): void
    {
        $this->actingAs($this->vendorUser);

        // 1. Create Contact
        $response = $this->post(route('web.contacts.store'), [
            'first_name' => 'Rahul',
            'last_name' => 'Sharma',
            'phone' => '+919811122233',
            'email' => 'rahul@example.com',
            'lead_status' => 'warm',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contacts', [
            'vendor_id' => $this->vendor->id,
            'first_name' => 'Rahul',
            'phone_number' => '+919811122233',
        ]);

        $contact = Contact::where('phone_number', '+919811122233')->firstOrFail();

        // 2. Update Contact
        $response = $this->post(route('web.contacts.update', $contact), [
            'first_name' => 'Rahul Updated',
            'last_name' => 'Sharma',
            'phone' => '+919811122233',
            'lead_status' => 'won',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => 'Rahul Updated',
        ]);

        // 3. Delete Contact
        $response = $this->post(route('web.contacts.delete', $contact));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('contacts', [
            'id' => $contact->id,
        ]);
    }

    public function test_contact_group_crud_operations(): void
    {
        $this->actingAs($this->vendorUser);

        // 1. Create Group
        $response = $this->post(route('web.contacts.groups.store'), [
            'title' => 'VIP Buyers',
            'description' => 'High lifetime value repeat customers',
            'color' => '#8b5cf6',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_groups', [
            'vendor_id' => $this->vendor->id,
            'title' => 'VIP Buyers',
        ]);

        $group = ContactGroup::where('title', 'VIP Buyers')->firstOrFail();

        // 2. Update Group
        $response = $this->post(route('web.contacts.groups.update', $group), [
            'title' => 'VIP Buyers Elite',
            'description' => 'Updated tier description',
            'color' => '#10b981',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_groups', [
            'id' => $group->id,
            'title' => 'VIP Buyers Elite',
        ]);

        // 3. Delete Group
        $response = $this->post(route('web.contacts.groups.delete', $group));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('contact_groups', [
            'id' => $group->id,
        ]);
    }

    public function test_custom_field_crud_operations(): void
    {
        $this->actingAs($this->vendorUser);

        // 1. Create Custom Field
        $response = $this->post(route('web.contacts.custom-fields.store'), [
            'name' => 'GST Identification Number',
            'field_key' => 'gst_number',
            'field_type' => 'text',
            'description' => 'Official tax identifier',
            'is_required' => true,
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_custom_fields', [
            'vendor_id' => $this->vendor->id,
            'field_key' => 'gst_number',
            'field_name' => 'GST Identification Number',
        ]);

        $field = \App\Models\ContactCustomField::where('field_key', 'gst_number')->firstOrFail();

        // 2. Update Custom Field
        $response = $this->post(route('web.contacts.custom-fields.update', $field), [
            'name' => 'GSTIN Number (Verified)',
            'description' => 'Updated description',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_custom_fields', [
            'id' => $field->id,
            'field_name' => 'GSTIN Number (Verified)',
        ]);

        // 3. Delete Custom Field
        $response = $this->post(route('web.contacts.custom-fields.delete', $field));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('contact_custom_fields', [
            'id' => $field->id,
        ]);
    }

    public function test_bot_rule_crud_operations(): void
    {
        $this->actingAs($this->vendorUser);

        // 1. Create Bot Rule
        $response = $this->post(route('web.bot.rules.store'), [
            'name' => 'Price Inquiries',
            'trigger_type' => 'contains',
            'keywords' => 'price, cost, rate, charges',
            'reply_type' => 'text',
            'reply_content' => 'Our starting plan begins at ₹999/mo with unlimited WhatsApp automation.',
            'is_active' => true,
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bot_replies', [
            'vendor_id' => $this->vendor->id,
            'name' => 'Price Inquiries',
        ]);

        $rule = \App\Models\BotReply::where('name', 'Price Inquiries')->firstOrFail();

        // 2. Update Bot Rule
        $response = $this->post(route('web.bot.rules.update', $rule), [
            'name' => 'Price & Catalog Inquiries',
            'reply_content' => 'Updated prices: Standard ₹999, Pro ₹2499.',
            'is_active' => true,
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bot_replies', [
            'id' => $rule->id,
            'name' => 'Price & Catalog Inquiries',
        ]);

        // 3. Delete Bot Rule
        $response = $this->post(route('web.bot.rules.delete', $rule));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('bot_replies', [
            'id' => $rule->id,
        ]);
    }

    public function test_bot_flow_crud_operations(): void
    {
        $this->actingAs($this->vendorUser);

        // 1. Create Bot Flow
        $response = $this->post(route('web.bot.flows.store'), [
            'title' => 'Onboarding Welcome Flow',
            'trigger_keyword' => 'start',
            'description' => 'Multi-step welcome survey',
            'status' => 'active',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bot_flows', [
            'vendor_id' => $this->vendor->id,
            'title' => 'Onboarding Welcome Flow',
        ]);

        $flow = BotFlow::where('title', 'Onboarding Welcome Flow')->firstOrFail();

        // 2. Update Bot Flow
        $response = $this->post(route('web.bot.flows.update', $flow), [
            'title' => 'Onboarding Welcome Flow V2',
            'trigger_keyword' => 'welcome',
            'description' => 'Updated steps',
            'status' => 'draft',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bot_flows', [
            'id' => $flow->id,
            'title' => 'Onboarding Welcome Flow V2',
            'is_active' => false,
        ]);

        // 3. Delete Bot Flow
        $response = $this->post(route('web.bot.flows.delete', $flow));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('bot_flows', [
            'id' => $flow->id,
        ]);
    }

    public function test_campaign_crud_operations(): void
    {
        $this->actingAs($this->vendorUser);

        // 1. Create Campaign
        $response = $this->post(route('web.campaigns.store'), [
            'title' => 'Diwali Mega Sale 2026',
            'type' => 'broadcast',
            'template_name' => 'festival_offer_v1',
            'scheduled_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('campaigns', [
            'vendor_id' => $this->vendor->id,
            'title' => 'Diwali Mega Sale 2026',
        ]);

        $campaign = Campaign::where('title', 'Diwali Mega Sale 2026')->firstOrFail();

        // 2. Action Campaign (pause / cancel)
        $response = $this->post(route('web.campaigns.action', [$campaign, 'pause']));
        $response->assertSessionHas('success');
        $this->assertEquals('paused', $campaign->fresh()->status);

        // 3. Delete Campaign
        $response = $this->post(route('web.campaigns.delete', $campaign));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('campaigns', [
            'id' => $campaign->id,
        ]);
    }

    public function test_agent_crud_operations(): void
    {
        $this->actingAs($this->vendorUser);

        // 1. Create Agent
        $response = $this->post(route('web.agents.store'), [
            'name' => 'Pooja Support',
            'email' => 'pooja@support.test',
            'role' => 'agent',
            'password' => 'secretPass123',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('vendor_users', [
            'vendor_id' => $this->vendor->id,
            'role' => 'agent',
        ]);

        $agentUser = User::where('email', 'pooja@support.test')->firstOrFail();

        // 2. Update Agent
        $response = $this->post(route('web.agents.update', $agentUser), [
            'role' => 'admin',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('vendor_users', [
            'vendor_id' => $this->vendor->id,
            'user_id' => $agentUser->id,
            'role' => 'admin',
        ]);

        // 3. Delete Agent
        $response = $this->post(route('web.agents.delete', $agentUser));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('vendor_users', [
            'vendor_id' => $this->vendor->id,
            'user_id' => $agentUser->id,
        ]);
    }

    public function test_admin_vendor_crud_operations(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Create Vendor
        $response = $this->post(route('admin.vendors.store'), [
            'name' => 'New Electronics Hub',
            'email' => 'contact@newelectronicshub.test',
            'owner_name' => 'Electronics Hub Owner',
            'owner_password' => 'SecurePass123!',
            'phone' => '+919000011111',
            'status' => 'active',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('vendors', [
            'name' => 'New Electronics Hub',
            'email' => 'contact@newelectronicshub.test',
        ]);

        $newVendor = Vendor::where('name', 'New Electronics Hub')->firstOrFail();

        // 2. Toggle Status
        $response = $this->post(route('admin.vendors.status', $newVendor));
        $response->assertSessionHas('success');
        $this->assertEquals('suspended', $newVendor->fresh()->status);

        // 3. Update Custom Permissions
        $response = $this->post(route('admin.vendors.permissions', $newVendor), [
            'permissions' => [
                'bot',
                'campaigns',
            ],
        ]);
        $response->assertSessionHas('success');
        $this->assertTrue($newVendor->fresh()->custom_permissions['bot']);

        // 4. Delete / Archive Vendor
        $response = $this->post(route('admin.vendors.delete', $newVendor));
        $response->assertSessionHas('success');
        $this->assertEquals('suspended', $newVendor->fresh()->status);
    }

    public function test_admin_plan_crud_operations(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Create Plan
        $response = $this->post(route('admin.plans.store'), [
            'title' => 'Enterprise Mega Tier',
            'price_monthly' => 9999,
            'max_contacts' => 50000,
            'max_messages_monthly' => 200000,
            'max_team_members' => 25,
            'description' => 'Unlimited scale plan for large enterprises',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('subscription_plans', [
            'title' => 'Enterprise Mega Tier',
            'price_monthly' => 9999,
        ]);

        $plan = SubscriptionPlan::where('title', 'Enterprise Mega Tier')->firstOrFail();

        // 2. Update Plan
        $response = $this->post(route('admin.plans.update', $plan), [
            'title' => 'Enterprise Mega Tier Gold',
            'price_monthly' => 11999,
            'max_contacts' => 75000,
            'max_messages_monthly' => 300000,
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('subscription_plans', [
            'id' => $plan->id,
            'title' => 'Enterprise Mega Tier Gold',
            'price_monthly' => 11999,
        ]);

        // 3. Delete Plan
        $response = $this->post(route('admin.plans.delete', $plan));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('subscription_plans', [
            'id' => $plan->id,
        ]);
    }

    public function test_admin_manual_subscription_actions(): void
    {
        $this->actingAs($this->superAdmin);

        $plan = SubscriptionPlan::create([
            'title' => 'Pro Plan',
            'slug' => 'pro-plan',
            'price_monthly' => 2499,
            'price_yearly' => 24990,
            'max_contacts' => 10000,
            'max_messages_monthly' => 50000,
        ]);

        $req1 = ManualPaymentRequest::create([
            'vendor_id' => $this->vendor->id,
            'subscription_plan_id' => $plan->id,
            'amount' => 2499,
            'currency' => 'INR',
            'payment_method' => 'bank_wire',
            'reference_number' => 'NEFT-12345678',
            'status' => 'pending',
        ]);

        // Approve
        $response = $this->post(route('admin.manual-subscriptions.approve', $req1));
        $response->assertSessionHas('success');
        $this->assertEquals('approved', $req1->fresh()->status);

        $req2 = ManualPaymentRequest::create([
            'vendor_id' => $this->vendor->id,
            'subscription_plan_id' => $plan->id,
            'amount' => 2499,
            'currency' => 'INR',
            'payment_method' => 'upi',
            'reference_number' => 'UPI-88889999',
            'status' => 'pending',
        ]);

        // Reject
        $response = $this->post(route('admin.manual-subscriptions.reject', $req2));
        $response->assertSessionHas('success');
        $this->assertEquals('rejected', $req2->fresh()->status);
    }

    public function test_admin_page_crud_operations(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Create Page
        $response = $this->post(route('admin.pages.store'), [
            'title' => 'Security & Encryption Policy',
            'slug' => 'security-encryption-policy',
            'type' => 'Legal / Policy',
            'status' => 'published',
            'content' => '<p>All WhatsApp messages are encrypted end-to-end.</p>',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pages', [
            'slug' => 'security-encryption-policy',
            'title' => 'Security & Encryption Policy',
        ]);

        $page = Page::where('slug', 'security-encryption-policy')->firstOrFail();

        // 2. Update Page
        $response = $this->post(route('admin.pages.update', $page), [
            'title' => 'Platform Security Standards',
            'type' => 'Documentation',
            'status' => 'published',
            'content' => '<p>Updated security framework.</p>',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Platform Security Standards',
        ]);

        // 3. Delete Page
        $response = $this->post(route('admin.pages.delete', $page));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('pages', [
            'id' => $page->id,
        ]);
    }

    public function test_admin_language_crud_operations(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Create Language
        $response = $this->post(route('admin.languages.store'), [
            'name' => 'German (Deutsch)',
            'code' => 'de',
            'direction' => 'ltr',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('languages', [
            'code' => 'de',
            'name' => 'German (Deutsch)',
        ]);

        $deLang = Language::where('code', 'de')->firstOrFail();

        // 2. Set as Default Language
        $response = $this->post(route('admin.languages.default', $deLang));
        $response->assertSessionHas('success');
        $this->assertTrue((bool)$deLang->fresh()->is_default);

        // 3. Try to delete default language -> should fail
        $response = $this->post(route('admin.languages.delete', $deLang));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('languages', ['id' => $deLang->id]);

        // Create secondary and delete it
        $frLang = Language::create([
            'name' => 'French',
            'code' => 'fr',
            'direction' => 'ltr',
            'is_default' => false,
        ]);

        $response = $this->post(route('admin.languages.delete', $frLang));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('languages', ['id' => $frLang->id]);
    }
}
