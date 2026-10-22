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
     * Test home page renders dashboard view successfully.
     */
    public function test_home_page_renders_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertSee('VyapariChat');
        $response->assertSee('Demo SuperStore');
        $response->assertSee('Total Contacts');
    }

    /**
     * Test /dashboard route renders dashboard view successfully.
     */
    public function test_dashboard_route_renders(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }
}
