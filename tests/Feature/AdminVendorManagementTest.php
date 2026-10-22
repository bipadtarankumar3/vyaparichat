<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminVendorManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $regularUser;
    protected Vendor $testVendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name' => 'Master Admin',
            'email' => 'admin@vyaparichat.com',
            'password' => Hash::make('secret123'),
            'is_super_admin' => true,
            'status' => 'active',
        ]);

        $this->regularUser = User::create([
            'name' => 'Store Owner',
            'email' => 'store@vendor.com',
            'password' => Hash::make('secret123'),
            'is_super_admin' => false,
            'status' => 'active',
        ]);

        $this->testVendor = Vendor::create([
            'name' => 'Acme Retail',
            'slug' => 'acme-retail',
            'email' => 'store@vendor.com',
            'phone' => '+919999999999',
            'status' => 'active',
            'whatsapp_status' => 'connected',
            'custom_permissions' => [
                'chat' => true,
                'contacts' => true,
                'bot' => false,
            ],
        ]);

        $this->regularUser->vendors()->attach($this->testVendor->id, [
            'role' => 'owner',
            'permissions' => json_encode(['*']),
            'status' => 'active',
        ]);
    }

    /**
     * Test Super Admin can access Central Console.
     */
    public function test_super_admin_can_access_admin_vendors_console(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/vendors');

        $response->assertStatus(200);
        $response->assertSee('Vendors & Client Businesses', false);
        $response->assertSee('Create New Vendor');
        $response->assertSee('Acme Retail');
    }

    /**
     * Test regular vendor user cannot access Central Console.
     */
    public function test_non_super_admin_cannot_access_admin_vendors_console(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/admin/vendors');

        $response->assertStatus(403);
    }

    /**
     * Test guest cannot access Central Console.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/vendors');

        $response->assertRedirect('/login');
    }

    /**
     * Test Super Admin can create a new vendor with owner credentials and custom section permissions.
     */
    public function test_super_admin_can_create_new_vendor(): void
    {
        $plan = SubscriptionPlan::create([
            'title' => 'Growth Plan',
            'slug' => 'growth',
            'description' => 'Growth tier',
            'price_monthly' => 1499.00,
            'price_yearly' => 14990.00,
            'max_contacts' => 5000,
            'max_campaigns_per_month' => 50,
            'max_staff_members' => 3,
            'has_ai_bot' => true,
            'has_api_access' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->post('/admin/vendors', [
            'name' => 'Metro Hypermarket',
            'slug' => 'metro-hypermarket',
            'email' => 'metro@business.com',
            'phone' => '+918888888888',
            'owner_name' => 'Sunil Gupta',
            'owner_password' => 'metro123456',
            'plan_id' => $plan->id,
            'permissions' => ['chat', 'contacts', 'campaigns', 'bot'],
        ]);

        $response->assertRedirect('/admin/vendors');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vendors', [
            'name' => 'Metro Hypermarket',
            'slug' => 'metro-hypermarket',
            'email' => 'metro@business.com',
        ]);

        $createdVendor = Vendor::where('slug', 'metro-hypermarket')->first();
        $this->assertNotNull($createdVendor);
        $this->assertTrue($createdVendor->hasSectionAccess('chat'));
        $this->assertTrue($createdVendor->hasSectionAccess('bot'));
        $this->assertFalse($createdVendor->hasSectionAccess('integrations'));

        // Owner user created
        $this->assertDatabaseHas('users', [
            'email' => 'metro@business.com',
            'name' => 'Sunil Gupta',
        ]);
    }

    /**
     * Test Super Admin can update custom section permissions for a vendor.
     */
    public function test_super_admin_can_update_vendor_permissions(): void
    {
        $response = $this->actingAs($this->superAdmin)->post("/admin/vendors/{$this->testVendor->id}/permissions", [
            'permissions' => ['chat', 'bot'],
        ]);

        $response->assertSessionHas('success');

        $this->testVendor->refresh();
        $this->assertTrue($this->testVendor->hasSectionAccess('chat'));
        $this->assertTrue($this->testVendor->hasSectionAccess('bot'));
        $this->assertFalse($this->testVendor->hasSectionAccess('campaigns'));
    }

    /**
     * Test Super Admin can switch to vendor console and return to admin dashboard.
     */
    public function test_super_admin_can_impersonate_vendor_and_leave(): void
    {
        // 1. Super Admin clicks Visit Console
        $response = $this->actingAs($this->superAdmin)->post("/admin/vendors/{$this->testVendor->id}/impersonate");

        $response->assertRedirect('/dashboard');
        $this->assertEquals($this->testVendor->id, session('impersonated_vendor_id'));

        // 2. View dashboard as vendor
        $dashResponse = $this->actingAs($this->superAdmin)->get('/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Acme Retail');
        $dashResponse->assertSee('Super Admin Session');
        $dashResponse->assertSee('Return to Admin Dashboard');

        // 3. Return back to Super Admin Dashboard
        $leaveResponse = $this->actingAs($this->superAdmin)->post('/admin/impersonate/leave');
        $leaveResponse->assertRedirect('/admin/vendors');
        $this->assertFalse(session()->has('impersonated_vendor_id'));
    }

    /**
     * Test Super Admin can access Central Executive Dashboard.
     */
    public function test_super_admin_can_access_executive_dashboard(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Executive Dashboard');
        $response->assertSee('Platform MRR');
        $response->assertSee('User Onboarding Analytics');
        $response->assertSee('Infrastructure Health');
    }

    /**
     * Test Super Admin can access User Plans (Auto & Manual).
     */
    public function test_super_admin_can_access_user_plans_and_manual_queue(): void
    {
        $autoResponse = $this->actingAs($this->superAdmin)->get('/admin/plans/subscriptions');
        $autoResponse->assertStatus(200);
        $autoResponse->assertSee('Auto Subscriptions');
        $autoResponse->assertSee('Automated Gateway Recurring');

        $manualResponse = $this->actingAs($this->superAdmin)->get('/admin/plans/manual');
        $manualResponse->assertStatus(200);
        $manualResponse->assertSee('Manual & Prepaid Subscriptions', false);
        $manualResponse->assertSee('Pending Approval');
    }

    /**
     * Test Super Admin can access CMS Pages, Languages, Setup, Embedded Signup, and Settings.
     */
    public function test_super_admin_can_access_all_admin_management_menus(): void
    {
        // 4. Pages
        $pagesRes = $this->actingAs($this->superAdmin)->get('/admin/pages');
        $pagesRes->assertStatus(200);
        $pagesRes->assertSee('CMS & Landing Pages', false);
        $pagesRes->assertSee('Terms of Service');

        // 5. Languages
        $langRes = $this->actingAs($this->superAdmin)->get('/admin/languages');
        $langRes->assertStatus(200);
        $langRes->assertSee('Languages & Localization', false);
        $langRes->assertSee('English (US / UK)', false);

        // 6. Setup
        $setupRes = $this->actingAs($this->superAdmin)->get('/admin/setup');
        $setupRes->assertStatus(200);
        $setupRes->assertSee('System Core Setup');
        $setupRes->assertSee('Application Identity');

        // 7. Embedded Signup
        $embeddedRes = $this->actingAs($this->superAdmin)->get('/admin/embedded-signup');
        $embeddedRes->assertStatus(200);
        $embeddedRes->assertSee('Meta Embedded Signup');
        $embeddedRes->assertSee('Meta App ID');

        // 8. Settings (with tabs)
        $settingsRes = $this->actingAs($this->superAdmin)->get('/admin/settings/payment');
        $settingsRes->assertStatus(200);
        $settingsRes->assertSee('Razorpay Gateway');
        $settingsRes->assertSee('Stripe Recurring Subscriptions');
    }
}

