<?php

namespace Tests\Feature;

use App\Jobs\ProcessCampaignDispatchJob;
use App\Jobs\SendCampaignMessageJob;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    protected Vendor $vendor;
    protected User $user;
    protected WhatsAppTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendor = Vendor::create([
            'name' => 'Mega Retailers',
            'slug' => 'mega-retailers',
            'status' => 'active',
            'whatsapp_status' => 'connected',
            'whatsapp_phone_number_id' => '10987654321',
        ]);

        $this->vendor->setSetting('whatsapp_access_token', 'EAAG_TEST_TOKEN');
        $this->vendor->setSetting('whatsapp_business_account_id', '123456789012345');

        $this->user = User::create([
            'name' => 'Campaign Manager',
            'email' => 'campaign@mega.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $this->vendor->users()->attach($this->user->id, [
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->template = WhatsAppTemplate::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'festive_discount_offer',
            'language' => 'en',
            'category' => 'MARKETING',
            'status' => 'APPROVED',
            'components' => [
                [
                    'type' => 'BODY',
                    'text' => 'Hi {{1}}, get {{2}} off on all items today!',
                ],
            ],
        ]);
    }

    /**
     * Test creating a campaign.
     */
    public function test_can_create_campaign(): void
    {
        $group = ContactGroup::create([
            'vendor_id' => $this->vendor->id,
            'title' => 'Festive Shoppers',
        ]);

        // Add 3 contacts (2 active, 1 opted-out)
        $c1 = Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876500001',
            'first_name' => 'Arun',
            'is_opted_out' => false,
        ]);
        $c1->groups()->attach($group->id);

        $c2 = Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876500002',
            'first_name' => 'Sneha',
            'is_opted_out' => false,
        ]);
        $c2->groups()->attach($group->id);

        $c3 = Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876500003',
            'first_name' => 'OptedOut',
            'is_opted_out' => true,
        ]);
        $c3->groups()->attach($group->id);

        $response = $this->actingAs($this->user)->postJson(route('api.campaigns.store'), [
            'title' => 'Diwali Grand Sale 2026',
            'whatsapp_template_id' => $this->template->id,
            'group_ids' => [$group->id],
            'variables_mapping' => [
                'body' => ['first_name', 'fixed:40% OFF'],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Diwali Grand Sale 2026',
                    'status' => 'draft',
                    'total_contacts' => 2, // Only 2 non-opted-out contacts
                ],
            ]);

        $this->assertDatabaseHas('campaigns', [
            'vendor_id' => $this->vendor->id,
            'title' => 'Diwali Grand Sale 2026',
            'total_contacts' => 2,
        ]);
    }

    /**
     * Test launching a broadcast campaign and job dispatching.
     */
    public function test_can_launch_campaign_and_process_dispatch(): void
    {
        $group = ContactGroup::create([
            'vendor_id' => $this->vendor->id,
            'title' => 'VIP Members',
        ]);

        $c1 = Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876500011',
            'first_name' => 'Kavita',
            'is_opted_out' => false,
        ]);
        $c1->groups()->attach($group->id);

        $campaign = Campaign::create([
            'vendor_id' => $this->vendor->id,
            'whatsapp_template_id' => $this->template->id,
            'title' => 'VIP Blast',
            'status' => 'draft',
            'total_contacts' => 1,
            'variables_mapping' => [
                'body' => ['first_name', 'fixed:50% OFF'],
            ],
        ]);
        $campaign->targetGroups()->attach($group->id);

        // Fake Meta API response for sending template message
        Http::fake([
            'https://graph.facebook.com/v20.0/10987654321/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'contacts' => [['input' => '919876500011', 'wa_id' => '919876500011']],
                'messages' => [['id' => 'wamid.HBgLOTE5ODc2NTAwMDExFQIAERgSMDAxMTIyMzM0NAA=']],
            ], 200),
        ]);

        // Launch via controller (with sync queue, this dispatches and executes ProcessCampaignDispatchJob & SendCampaignMessageJob)
        $response = $this->actingAs($this->user)->postJson(route('api.campaigns.launch', ['campaign' => $campaign->id]));
        $response->assertStatus(200);

        // Verify message was created and sent
        $this->assertDatabaseHas('whatsapp_messages', [
            'vendor_id' => $this->vendor->id,
            'contact_id' => $c1->id,
            'campaign_id' => $campaign->id,
            'status' => 'sent',
            'wamid' => 'wamid.HBgLOTE5ODc2NTAwMDExFQIAERgSMDAxMTIyMzM0NAA=',
        ]);

        $campaign->refresh();
        $this->assertEquals(1, $campaign->sent_count);
        $this->assertEquals('completed', $campaign->status);
    }

    /**
     * Test pausing, resuming, and cancelling campaign.
     */
    public function test_campaign_pause_resume_cancel_controls(): void
    {
        Queue::fake();

        $campaign = Campaign::create([
            'vendor_id' => $this->vendor->id,
            'whatsapp_template_id' => $this->template->id,
            'title' => 'Controllable Campaign',
            'status' => 'processing',
            'total_contacts' => 500,
        ]);

        // Pause
        $this->actingAs($this->user)
            ->postJson(route('api.campaigns.pause', ['campaign' => $campaign->id]))
            ->assertStatus(200);
        $this->assertEquals('paused', $campaign->fresh()->status);

        // Resume
        $this->actingAs($this->user)
            ->postJson(route('api.campaigns.resume', ['campaign' => $campaign->id]))
            ->assertStatus(200);
        $this->assertEquals('processing', $campaign->fresh()->status);
        Queue::assertPushed(ProcessCampaignDispatchJob::class);

        // Cancel
        $this->actingAs($this->user)
            ->postJson(route('api.campaigns.cancel', ['campaign' => $campaign->id]))
            ->assertStatus(200);
        $this->assertEquals('cancelled', $campaign->fresh()->status);
    }

    /**
     * Test campaign statistics endpoint.
     */
    public function test_campaign_stats_endpoint(): void
    {
        $campaign = Campaign::create([
            'vendor_id' => $this->vendor->id,
            'whatsapp_template_id' => $this->template->id,
            'title' => 'Stats Campaign',
            'status' => 'processing',
            'total_contacts' => 100,
            'sent_count' => 80,
            'delivered_count' => 70,
            'read_count' => 35,
            'failed_count' => 5,
        ]);

        $response = $this->actingAs($this->user)->getJson(route('api.campaigns.stats', ['campaign' => $campaign->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'processing',
                    'total_contacts' => 100,
                    'sent' => 80,
                    'delivered' => 70,
                    'read' => 35,
                    'failed' => 5,
                    'progress_percent' => 85.0,
                    'delivery_rate_percent' => 87.5,
                    'read_rate_percent' => 50.0,
                ],
            ]);
    }

    /**
     * Test rejecting launch of unapproved template.
     */
    public function test_cannot_launch_with_unapproved_template(): void
    {
        $pendingTemplate = WhatsAppTemplate::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'pending_review_template',
            'language' => 'en',
            'category' => 'MARKETING',
            'status' => 'PENDING',
            'components' => [['type' => 'BODY', 'text' => 'Pending']],
        ]);

        $campaign = Campaign::create([
            'vendor_id' => $this->vendor->id,
            'whatsapp_template_id' => $pendingTemplate->id,
            'title' => 'Should Fail Launch',
            'status' => 'draft',
            'total_contacts' => 10,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('api.campaigns.launch', ['campaign' => $campaign->id]));

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertEquals('draft', $campaign->fresh()->status);
    }
}
