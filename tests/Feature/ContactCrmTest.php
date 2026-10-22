<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactCustomField;
use App\Models\ContactGroup;
use App\Models\Country;
use App\Models\Label;
use App\Models\User;
use App\Models\Vendor;
use App\Services\Contact\ContactService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ContactCrmTest extends TestCase
{
    use RefreshDatabase;

    protected Vendor $vendor;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendor = Vendor::create([
            'name' => 'Acme Corporation',
            'slug' => 'acme-corp',
            'status' => 'active',
            'whatsapp_status' => 'connected',
        ]);

        $this->user = User::create([
            'name' => 'Alice Agent',
            'email' => 'alice@acme.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        // Attach user to vendor
        $this->vendor->users()->attach($this->user->id, [
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    /**
     * Test creating a contact via API.
     */
    public function test_can_create_contact_with_groups_and_labels(): void
    {
        $group = ContactGroup::create([
            'vendor_id' => $this->vendor->id,
            'title' => 'VIP Clients',
        ]);

        $label = Label::create([
            'vendor_id' => $this->vendor->id,
            'title' => 'Hot Lead',
            'color' => '#ff0000',
        ]);

        $country = Country::create([
            'name' => 'India',
            'iso_code' => 'IN',
            'phone_code' => '91',
        ]);

        $response = $this->actingAs($this->user)->postJson(route('api.contacts.store'), [
            'phone_number' => '+91 98765 43210',
            'first_name' => 'Rajesh',
            'last_name' => 'Sharma',
            'email' => 'rajesh@example.com',
            'country_id' => $country->id,
            'group_ids' => [$group->id],
            'label_ids' => [$label->id],
            'notes' => 'Key decision maker.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'phone_number' => '919876543210',
                    'first_name' => 'Rajesh',
                    'last_name' => 'Sharma',
                    'email' => 'rajesh@example.com',
                ],
            ]);

        $this->assertDatabaseHas('contacts', [
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876543210',
            'first_name' => 'Rajesh',
        ]);

        $contact = Contact::where('phone_number', '919876543210')->first();
        $this->assertTrue($contact->groups->contains($group->id));
        $this->assertTrue($contact->labels->contains($label->id));
    }

    /**
     * Test listing and filtering contacts.
     */
    public function test_can_list_and_search_contacts(): void
    {
        Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876543211',
            'first_name' => 'Priya',
            'last_name' => 'Patel',
            'email' => 'priya@example.com',
        ]);

        Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876543212',
            'first_name' => 'Rahul',
            'last_name' => 'Verma',
            'email' => 'rahul@example.com',
        ]);

        // Search for 'Priya'
        $response = $this->actingAs($this->user)->getJson(route('api.contacts.index', ['search' => 'Priya']));

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('Priya', $response->json('data.data.0.first_name'));

        // Search for '9876543212'
        $response = $this->actingAs($this->user)->getJson(route('api.contacts.index', ['search' => '9876543212']));
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('Rahul', $response->json('data.data.0.first_name'));
    }

    /**
     * Test updating a contact.
     */
    public function test_can_update_contact(): void
    {
        $contact = Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876543213',
            'first_name' => 'Amit',
            'last_name' => 'Roy',
        ]);

        $response = $this->actingAs($this->user)->putJson(route('api.contacts.update', ['contact' => $contact->id]), [
            'first_name' => 'Amitabh',
            'notes' => 'Promoted to Director',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'first_name' => 'Amitabh',
                    'notes' => 'Promoted to Director',
                ],
            ]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => 'Amitabh',
        ]);
    }

    /**
     * Test soft-deleting a contact.
     */
    public function test_can_delete_contact(): void
    {
        $contact = Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876543214',
            'first_name' => 'Delete',
            'last_name' => 'Me',
        ]);

        $response = $this->actingAs($this->user)->deleteJson(route('api.contacts.destroy', ['contact' => $contact->id]));

        $response->assertStatus(200);
        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }

    /**
     * Test bulk delete contacts.
     */
    public function test_can_bulk_delete_contacts(): void
    {
        $c1 = Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876543215',
            'first_name' => 'Bulk1',
        ]);

        $c2 = Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876543216',
            'first_name' => 'Bulk2',
        ]);

        $response = $this->actingAs($this->user)->postJson(route('api.contacts.bulk_delete'), [
            'contact_uuids' => [$c1->uuid, $c2->uuid],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'deleted_count' => 2,
            ]);

        $this->assertSoftDeleted('contacts', ['id' => $c1->id]);
        $this->assertSoftDeleted('contacts', ['id' => $c2->id]);
    }

    /**
     * Test bulk assign groups.
     */
    public function test_can_bulk_assign_groups(): void
    {
        $group = ContactGroup::create([
            'vendor_id' => $this->vendor->id,
            'title' => 'Festival Promo Group',
        ]);

        $c1 = Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876543217',
            'first_name' => 'Promo1',
        ]);

        $c2 = Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876543218',
            'first_name' => 'Promo2',
        ]);

        $response = $this->actingAs($this->user)->postJson(route('api.contacts.bulk_assign_groups'), [
            'contact_uuids' => [$c1->uuid, $c2->uuid],
            'group_ids' => [$group->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 2,
            ]);

        $this->assertTrue($c1->fresh()->groups->contains($group->id));
        $this->assertTrue($c2->fresh()->groups->contains($group->id));
    }

    /**
     * Test contact group CRUD.
     */
    public function test_contact_group_crud(): void
    {
        // Store
        $response = $this->actingAs($this->user)->postJson(route('api.contact_groups.store'), [
            'title' => 'Beta Testers',
            'description' => 'Early access users',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Beta Testers',
                ],
            ]);

        $groupId = $response->json('data.id');

        // Update
        $this->actingAs($this->user)->putJson(route('api.contact_groups.update', ['contact_group' => $groupId]), [
            'title' => 'Alpha & Beta Testers',
        ])->assertStatus(200);

        $this->assertDatabaseHas('contact_groups', [
            'id' => $groupId,
            'title' => 'Alpha & Beta Testers',
        ]);

        // Destroy
        $this->actingAs($this->user)->deleteJson(route('api.contact_groups.destroy', ['contact_group' => $groupId]))
            ->assertStatus(200);

        $this->assertDatabaseMissing('contact_groups', ['id' => $groupId]);
    }

    /**
     * Test label CRUD.
     */
    public function test_label_crud(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('api.labels.store'), [
            'title' => 'Urgent',
            'color' => '#e11d48',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Urgent',
                    'color' => '#e11d48',
                ],
            ]);

        $labelId = $response->json('data.id');

        $this->actingAs($this->user)->deleteJson(route('api.labels.destroy', ['label' => $labelId]))
            ->assertStatus(200);

        $this->assertDatabaseMissing('labels', ['id' => $labelId]);
    }

    /**
     * Test custom field CRUD.
     */
    public function test_custom_field_crud(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('api.custom_fields.store'), [
            'field_name' => 'membership_tier',
            'field_type' => 'text',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'field_key' => 'membership_tier',
                    'field_name' => 'membership_tier',
                ],
            ]);

        $fieldId = $response->json('data.id');

        $this->actingAs($this->user)->deleteJson(route('api.custom_fields.destroy', ['custom_field' => $fieldId]))
            ->assertStatus(200);

        $this->assertDatabaseMissing('contact_custom_fields', ['id' => $fieldId]);
    }

    /**
     * Test process batch import contacts.
     */
    public function test_process_batch_import_contacts(): void
    {
        $group = ContactGroup::create([
            'vendor_id' => $this->vendor->id,
            'title' => 'Imported Batch',
        ]);

        $contacts = [
            [
                'phone_number' => '+91 99999 11111',
                'first_name' => 'Imported One',
                'email' => 'imp1@example.com',
            ],
            [
                'phone_number' => '+91 99999 22222',
                'first_name' => 'Imported Two',
                'email' => 'imp2@example.com',
            ],
        ];

        $response = $this->actingAs($this->user)->postJson(route('api.contacts.process_import'), [
            'contacts' => $contacts,
            'group_id' => $group->id,
            'overwrite_existing' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'imported' => 2,
                    'updated' => 0,
                    'failed' => 0,
                ],
            ]);

        $this->assertDatabaseHas('contacts', [
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919999911111',
            'first_name' => 'Imported One',
        ]);
        $this->assertDatabaseHas('contacts', [
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919999922222',
            'first_name' => 'Imported Two',
        ]);
    }

    /**
     * Test download dummy Excel template.
     */
    public function test_can_download_dummy_excel_template(): void
    {
        $response = $this->actingAs($this->user)->get(route('api.contacts.dummy_template'));

        $response->assertStatus(200);
        $this->assertEquals(
            'attachment; filename=dummy_contacts_template.xlsx',
            $response->headers->get('content-disposition')
        );
    }

    /**
     * Test exporting contacts to Excel.
     */
    public function test_can_export_contacts_to_excel(): void
    {
        Contact::create([
            'vendor_id' => $this->vendor->id,
            'phone_number' => '919876543299',
            'first_name' => 'Exportable',
            'last_name' => 'User',
            'email' => 'exportable@example.com',
        ]);

        $response = $this->actingAs($this->user)->get(route('api.contacts.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('contacts_export_', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }
}
