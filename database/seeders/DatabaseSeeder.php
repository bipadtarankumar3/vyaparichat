<?php

namespace Database\Seeders;

use App\Models\ContactGroup;
use App\Models\Country;
use App\Models\Label;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('123456'),
                'is_super_admin' => true,
                'status' => 'active',
            ]
        );

        // 2. Create Default Demo Vendor
        $vendor = Vendor::firstOrCreate(
            ['slug' => 'vyapari-demo'],
            [
                'name' => 'Vyapari Demo Business',
                'email' => 'support@vyaparichat.com',
                'phone' => '+919876543210',
                'status' => 'active',
                'timezone' => 'Asia/Kolkata',
                'whatsapp_status' => 'disconnected',
            ]
        );

        // 3. Create Vendor Admin User
        $vendorUser = User::firstOrCreate(
            ['email' => 'support@vyaparichat.com'],
            [
                'name' => 'Demo Business Owner',
                'password' => Hash::make('123456'),
                'is_super_admin' => false,
                'status' => 'active',
            ]
        );

        // Link to Vendor
        VendorUser::firstOrCreate(
            ['vendor_id' => $vendor->id, 'user_id' => $vendorUser->id],
            [
                'role' => 'admin',
                'permissions' => ['messaging', 'contacts', 'campaigns', 'settings', 'bots'],
                'status' => 'active',
            ]
        );

        // 4. Create Standard Subscription Plans
        $plans = [
            [
                'title' => 'Starter Plan',
                'slug' => 'starter',
                'description' => 'Ideal for small businesses starting out with WhatsApp marketing.',
                'price_monthly' => 499.00,
                'price_yearly' => 4990.00,
                'max_contacts' => 1000,
                'max_campaigns_per_month' => 30,
                'max_staff_members' => 2,
                'has_ai_bot' => false,
                'has_api_access' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Growth Plan',
                'slug' => 'growth',
                'description' => 'For growing teams that need broadcast automation & team collaboration.',
                'price_monthly' => 1499.00,
                'price_yearly' => 14990.00,
                'max_contacts' => 10000,
                'max_campaigns_per_month' => 150,
                'max_staff_members' => 5,
                'has_ai_bot' => true,
                'has_api_access' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Enterprise Plan',
                'slug' => 'enterprise',
                'description' => 'High-throughput broadcasts, unlimited contacts, and priority WhatsApp Cloud API support.',
                'price_monthly' => 3999.00,
                'price_yearly' => 39990.00,
                'max_contacts' => 0, // unlimited
                'max_campaigns_per_month' => 0, // unlimited
                'max_staff_members' => 20,
                'has_ai_bot' => true,
                'has_api_access' => true,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $p) {
            SubscriptionPlan::firstOrCreate(['slug' => $p['slug']], $p);
        }

        // 5. Seed Major Countries
        $countries = [
            ['name' => 'India', 'iso_code' => 'IN', 'phone_code' => '91'],
            ['name' => 'United States', 'iso_code' => 'US', 'phone_code' => '1'],
            ['name' => 'United Kingdom', 'iso_code' => 'GB', 'phone_code' => '44'],
            ['name' => 'United Arab Emirates', 'iso_code' => 'AE', 'phone_code' => '971'],
            ['name' => 'Saudi Arabia', 'iso_code' => 'SA', 'phone_code' => '966'],
            ['name' => 'Singapore', 'iso_code' => 'SG', 'phone_code' => '65'],
            ['name' => 'Australia', 'iso_code' => 'AU', 'phone_code' => '61'],
            ['name' => 'Canada', 'iso_code' => 'CA', 'phone_code' => '1'],
        ];

        foreach ($countries as $c) {
            Country::firstOrCreate(['iso_code' => $c['iso_code']], $c);
        }

        // 6. Default Labels for Demo Vendor
        $labels = [
            ['title' => 'VIP', 'color' => '#f59e0b', 'text_color' => '#ffffff'],
            ['title' => 'New Lead', 'color' => '#10b981', 'text_color' => '#ffffff'],
            ['title' => 'Customer', 'color' => '#3b82f6', 'text_color' => '#ffffff'],
            ['title' => 'Urgent', 'color' => '#ef4444', 'text_color' => '#ffffff'],
        ];

        foreach ($labels as $l) {
            Label::firstOrCreate(['vendor_id' => $vendor->id, 'title' => $l['title']], $l);
        }

        // 7. Default Contact Groups for Demo Vendor
        ContactGroup::firstOrCreate(
            ['vendor_id' => $vendor->id, 'title' => 'All Customers'],
            ['description' => 'Default segment for all incoming customers']
        );
        ContactGroup::firstOrCreate(
            ['vendor_id' => $vendor->id, 'title' => 'Imported Leads'],
            ['description' => 'Contacts imported via Excel spreadsheets']
        );
    }
}
