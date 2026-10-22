<?php

namespace Tests\Feature;

use App\Models\ManualPaymentRequest;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Vendor;
use App\Services\Billing\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    protected Vendor $vendor;
    protected User $user;
    protected SubscriptionPlan $starterPlan;
    protected SubscriptionPlan $proPlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendor = Vendor::create([
            'name' => 'Growth Retailers',
            'slug' => 'growth-retailers',
            'status' => 'active',
            'whatsapp_status' => 'connected',
        ]);

        $this->user = User::create([
            'name' => 'Billing Admin',
            'email' => 'admin@growth.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $this->vendor->users()->attach($this->user->id, [
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->starterPlan = SubscriptionPlan::create([
            'title' => 'Starter Plan',
            'slug' => 'starter',
            'description' => 'For small businesses',
            'price_monthly' => 999.00,
            'price_yearly' => 9990.00,
            'max_contacts' => 1000,
            'max_campaigns_per_month' => 20,
            'max_staff_members' => 2,
            'has_ai_bot' => false,
            'has_api_access' => false,
            'is_active' => true,
        ]);

        $this->proPlan = SubscriptionPlan::create([
            'title' => 'Pro Enterprise',
            'slug' => 'pro-enterprise',
            'description' => 'For scaling teams',
            'price_monthly' => 2999.00,
            'price_yearly' => 29990.00,
            'max_contacts' => 10000,
            'max_campaigns_per_month' => 200,
            'max_staff_members' => 10,
            'has_ai_bot' => true,
            'has_api_access' => true,
            'is_active' => true,
        ]);
    }

    /**
     * Test listing public plans.
     */
    public function test_can_list_plans(): void
    {
        $response = $this->getJson(route('api.billing.plans'));

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
        $this->assertEquals('Starter Plan', $response->json('data.0.title'));
    }

    /**
     * Test checkout session creation.
     */
    public function test_can_create_checkout_session(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('api.billing.checkout'), [
            'plan_id' => $this->proPlan->id,
            'billing_cycle' => 'monthly',
            'gateway' => 'stripe',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'gateway' => 'stripe',
                    'amount' => 2999,
                ],
            ]);

        $this->assertDatabaseHas('payment_transactions', [
            'vendor_id' => $this->vendor->id,
            'gateway' => 'stripe',
            'status' => 'pending',
            'amount' => 2999.00,
        ]);
    }

    /**
     * Test Stripe webhook activates subscription.
     */
    public function test_stripe_webhook_activates_subscription(): void
    {
        $tx = PaymentTransaction::create([
            'vendor_id' => $this->vendor->id,
            'gateway' => 'stripe',
            'transaction_reference' => 'cs_test_stripe_123',
            'amount' => 2999.00,
            'currency' => 'INR',
            'status' => 'pending',
            'payload' => [
                'plan_id' => $this->proPlan->id,
                'billing_cycle' => 'monthly',
            ],
        ]);

        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'client_reference_id' => 'cs_test_stripe_123',
                    'amount_total' => 299900,
                    'currency' => 'inr',
                ],
            ],
        ];

        $response = $this->postJson(route('api.payment.webhook.stripe'), $payload);
        $response->assertStatus(200);

        $this->assertDatabaseHas('subscriptions', [
            'vendor_id' => $this->vendor->id,
            'subscription_plan_id' => $this->proPlan->id,
            'status' => 'active',
            'billing_cycle' => 'monthly',
        ]);

        $this->assertEquals('successful', $tx->fresh()->status);
    }

    /**
     * Test Razorpay webhook activates subscription.
     */
    public function test_razorpay_webhook_activates_subscription(): void
    {
        $tx = PaymentTransaction::create([
            'vendor_id' => $this->vendor->id,
            'gateway' => 'razorpay',
            'transaction_reference' => 'order_rzp_999',
            'amount' => 999.00,
            'currency' => 'INR',
            'status' => 'pending',
            'payload' => [
                'plan_id' => $this->starterPlan->id,
                'billing_cycle' => 'monthly',
            ],
        ]);

        $payload = [
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'order_id' => 'order_rzp_999',
                        'amount' => 99900,
                        'currency' => 'INR',
                    ],
                ],
            ],
        ];

        $response = $this->postJson(route('api.payment.webhook.razorpay'), $payload);
        $response->assertStatus(200);

        $this->assertDatabaseHas('subscriptions', [
            'vendor_id' => $this->vendor->id,
            'subscription_plan_id' => $this->starterPlan->id,
            'status' => 'active',
        ]);

        $this->assertEquals('successful', $tx->fresh()->status);
    }

    /**
     * Test manual bank transfer submission and admin approval.
     */
    public function test_manual_bank_transfer_workflow(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('api.billing.manual_payment'), [
            'plan_id' => $this->proPlan->id,
            'amount' => 2999.00,
            'proof_image_url' => 'https://example.com/receipts/bank_wire_123.jpg',
            'notes' => 'Transferred via IMPS Ref #99887766',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'amount' => '2999.00',
                    'status' => 'pending',
                ],
            ]);

        $requestId = $response->json('data.id');
        $req = ManualPaymentRequest::findOrFail($requestId);

        // Admin approves request
        $billingService = app(BillingService::class);
        $approved = $billingService->reviewManualPaymentRequest($req, true, $this->user);

        $this->assertTrue($approved);
        $this->assertEquals('approved', $req->fresh()->status);
        $this->assertDatabaseHas('subscriptions', [
            'vendor_id' => $this->vendor->id,
            'subscription_plan_id' => $this->proPlan->id,
            'status' => 'active',
        ]);
    }

    /**
     * Test feature limit checking and gating.
     */
    public function test_feature_limit_checking(): void
    {
        // Give vendor active Starter plan (max 1000 contacts, no AI bot)
        Subscription::create([
            'vendor_id' => $this->vendor->id,
            'subscription_plan_id' => $this->starterPlan->id,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        $billingService = app(BillingService::class);

        // Under contact limit
        $check1 = $billingService->checkFeatureLimit($this->vendor, 'contacts', 500);
        $this->assertTrue($check1['allowed']);

        // Exceeded contact limit
        $check2 = $billingService->checkFeatureLimit($this->vendor, 'contacts', 1000);
        $this->assertFalse($check2['allowed']);

        // AI bot not allowed on starter
        $checkBot = $billingService->checkFeatureLimit($this->vendor, 'ai_bot');
        $this->assertFalse($checkBot['allowed']);
    }
}
