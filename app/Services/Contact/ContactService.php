<?php

namespace App\Services\Contact;

use App\Models\Contact;
use App\Models\ContactCustomField;
use App\Models\ContactCustomFieldValue;
use App\Models\ContactGroup;
use App\Models\Country;
use App\Models\Label;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use OpenSpout\Reader\XLSX\Reader as SpoutXLSXReader;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use XLSXWriter;

class ContactService
{
    /**
     * Get paginated & filtered contacts for a vendor.
     */
    public function listContacts(Vendor $vendor, array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = Contact::with(['assignedUser:id,name,email', 'labels:id,title,color,text_color', 'groups:id,title', 'country:id,name,phone_code'])
            ->where('vendor_id', $vendor->id);

        // Search term (name, phone, email)
        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', $search)
                    ->orWhere('last_name', 'like', $search)
                    ->orWhere('phone_number', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        // Filter by Group
        if (!empty($filters['group_id'])) {
            $query->whereHas('groups', function ($q) use ($filters) {
                $q->where('contact_groups.id', $filters['group_id']);
            });
        }

        // Filter by Label/Tag
        if (!empty($filters['label_id'])) {
            $query->whereHas('labels', function ($q) use ($filters) {
                $q->where('labels.id', $filters['label_id']);
            });
        }

        // Filter by Assigned Staff Member
        if (!empty($filters['assigned_user_id'])) {
            $query->where('assigned_user_id', $filters['assigned_user_id']);
        }

        // Filter by Marketing Opt-out
        if (isset($filters['is_opted_out']) && $filters['is_opted_out'] !== '') {
            $query->where('is_opted_out', (bool) $filters['is_opted_out']);
        }

        // Date Range (From / To)
        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($filters['from_date']));
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($filters['to_date']));
        }

        // Tabs: 'live_chat' vs 'imported' vs 'all'
        if (!empty($filters['tab'])) {
            if ($filters['tab'] === 'live_chat') {
                $query->whereHas('messages');
            } elseif ($filters['tab'] === 'imported') {
                $query->whereHas('groups', function ($q) {
                    $q->where('title', 'like', '%Imported%');
                });
            }
        }

        // Sorting
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDir = strtolower($filters['order_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($orderBy, $orderDir)->paginate($perPage);
    }

    /**
     * Create a new contact.
     */
    public function createContact(Vendor $vendor, array $data): Contact
    {
        $phoneNumber = $this->cleanPhoneNumber($data['phone_number']);

        $contact = Contact::create([
            'vendor_id' => $vendor->id,
            'assigned_user_id' => $data['assigned_user_id'] ?? null,
            'country_id' => $data['country_id'] ?? null,
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'phone_number' => $phoneNumber,
            'email' => $data['email'] ?? null,
            'language_code' => $data['language_code'] ?? 'en',
            'is_opted_out' => !empty($data['is_opted_out']),
            'is_ai_bot_disabled' => !empty($data['is_ai_bot_disabled']),
            'notes' => $data['notes'] ?? null,
        ]);

        if (!empty($data['group_ids'])) {
            $contact->groups()->sync($data['group_ids']);
        }

        if (!empty($data['label_ids'])) {
            $contact->labels()->sync($data['label_ids']);
        }

        if (!empty($data['custom_fields'])) {
            $this->saveCustomFields($contact, $data['custom_fields']);
        }

        return $contact;
    }

    /**
     * Update an existing contact.
     */
    public function updateContact(Contact $contact, array $data): Contact
    {
        $updateData = [];

        if (isset($data['phone_number'])) {
            $updateData['phone_number'] = $this->cleanPhoneNumber($data['phone_number']);
        }
        foreach (['first_name', 'last_name', 'email', 'assigned_user_id', 'country_id', 'language_code', 'notes', 'is_opted_out', 'is_ai_bot_disabled'] as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        $contact->update($updateData);

        if (isset($data['group_ids'])) {
            $contact->groups()->sync($data['group_ids']);
        }

        if (isset($data['label_ids'])) {
            $contact->labels()->sync($data['label_ids']);
        }

        if (!empty($data['custom_fields'])) {
            $this->saveCustomFields($contact, $data['custom_fields']);
        }

        return $contact->fresh(['assignedUser', 'labels', 'groups', 'country']);
    }

    /**
     * Bulk delete contacts by UUIDs.
     */
    public function bulkDelete(Vendor $vendor, array $contactUuids): int
    {
        return Contact::where('vendor_id', $vendor->id)
            ->whereIn('uuid', $contactUuids)
            ->delete();
    }

    /**
     * Bulk assign groups to contacts.
     */
    public function bulkAssignGroups(Vendor $vendor, array $contactUuids, array $groupIds): int
    {
        $contacts = Contact::where('vendor_id', $vendor->id)
            ->whereIn('uuid', $contactUuids)
            ->get();

        foreach ($contacts as $contact) {
            $contact->groups()->syncWithoutDetaching($groupIds);
        }

        return $contacts->count();
    }

    /**
     * Generate & download sample dummy Excel template.
     */
    public function downloadSampleTemplate(): BinaryFileResponse
    {
        $headers = [
            'First Name' => 'string',
            'Second Name' => 'string',
            'Phone Number' => 'string',
            'Email' => 'string',
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'sample_contacts_') . '.xlsx';
        $writer = new XLSXWriter();
        $writer->writeSheetHeader('Contacts', $headers);
        $writer->writeSheetRow('Contacts', ['Rahul', 'Sharma', '919876543210', 'rahul@example.com']);
        $writer->writeSheetRow('Contacts', ['Priya', 'Patel', '919876543211', 'priya@example.com']);
        $writer->writeToFile($tempFile);

        return response()->download($tempFile, 'dummy_contacts_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
    }

    /**
     * Parse uploaded Excel spreadsheet and return preview rows.
     */
    public function parseExcelPreview(UploadedFile $file): array
    {
        $reader = new SpoutXLSXReader();
        $reader->open($file->getRealPath());

        $headers = [];
        $previewRows = [];
        $rowIndex = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = array_map(fn($cell) => (string) $cell->getValue(), $row->getCells());

                if ($rowIndex === 0) {
                    $headers = $cells;
                } elseif ($rowIndex <= 50) { // Preview up to 50 rows
                    $previewRows[] = $cells;
                }
                $rowIndex++;
            }
            break; // only first sheet
        }

        $reader->close();

        return [
            'total_rows' => max(0, $rowIndex - 1),
            'headers' => $headers,
            'preview_rows' => $previewRows,
        ];
    }

    /**
     * Import batch of contacts from mapped spreadsheet rows.
     */
    public function importContacts(
        Vendor $vendor,
        array $rows,
        ?int $groupId = null,
        bool $overwriteExisting = true
    ): array {
        $importedCount = 0;
        $updatedCount = 0;
        $failedCount = 0;

        // Auto find or create "Imported Leads" group if not specified
        if (!$groupId) {
            $group = ContactGroup::firstOrCreate(
                ['vendor_id' => $vendor->id, 'title' => 'Imported Leads'],
                ['description' => 'Contacts imported via Excel']
            );
            $groupId = $group->id;
        }

        foreach ($rows as $row) {
            $phone = $this->cleanPhoneNumber($row['phone_number'] ?? ($row[2] ?? ''));

            if (strlen($phone) < 8) {
                $failedCount++;
                continue;
            }

            $firstName = trim($row['first_name'] ?? ($row[0] ?? ''));
            $lastName = trim($row['last_name'] ?? ($row[1] ?? ''));
            $email = trim($row['email'] ?? ($row[3] ?? ''));

            $existing = Contact::where('vendor_id', $vendor->id)
                ->where('phone_number', $phone)
                ->first();

            if ($existing) {
                if ($overwriteExisting) {
                    $existing->update([
                        'first_name' => $firstName ?: $existing->first_name,
                        'last_name' => $lastName ?: $existing->last_name,
                        'email' => $email ?: $existing->email,
                    ]);
                    $existing->groups()->syncWithoutDetaching([$groupId]);
                    $updatedCount++;
                }
            } else {
                $contact = Contact::create([
                    'vendor_id' => $vendor->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone_number' => $phone,
                    'email' => $email,
                    'language_code' => 'en',
                ]);
                $contact->groups()->syncWithoutDetaching([$groupId]);
                $importedCount++;
            }
        }

        return [
            'imported' => $importedCount,
            'updated' => $updatedCount,
            'failed' => $failedCount,
        ];
    }

    /**
     * Export contacts to Excel file.
     */
    public function exportContacts(Vendor $vendor, array $filters = []): BinaryFileResponse
    {
        $contacts = Contact::with(['groups', 'labels', 'assignedUser', 'country'])
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->get();

        $headers = [
            'First Name' => 'string',
            'Last Name' => 'string',
            'Phone Number' => 'string',
            'Email' => 'string',
            'Country' => 'string',
            'Assigned Agent' => 'string',
            'Tags' => 'string',
            'Groups' => 'string',
            'Marketing Opt-out' => 'string',
            'Created At' => 'string',
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'export_contacts_') . '.xlsx';
        $writer = new XLSXWriter();
        $writer->writeSheetHeader('Contacts', $headers);

        foreach ($contacts as $c) {
            $tags = $c->labels->pluck('title')->implode(', ');
            $groups = $c->groups->pluck('title')->implode(', ');

            $writer->writeSheetRow('Contacts', [
                $c->first_name,
                $c->last_name,
                $c->phone_number,
                $c->email,
                $c->country?->name ?? '',
                $c->assignedUser?->name ?? '',
                $tags,
                $groups,
                $c->is_opted_out ? 'Yes' : 'No',
                $c->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        $writer->writeToFile($tempFile);
        $fileName = 'contacts_export_' . now()->format('Y_m_d_His') . '.xlsx';

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
    }

    /**
     * Save dynamic custom fields for contact.
     */
    protected function saveCustomFields(Contact $contact, array $fields): void
    {
        foreach ($fields as $fieldKey => $value) {
            $customField = ContactCustomField::where('vendor_id', $contact->vendor_id)
                ->where('field_key', $fieldKey)
                ->first();

            if ($customField) {
                ContactCustomFieldValue::updateOrCreate(
                    [
                        'contact_id' => $contact->id,
                        'custom_field_id' => $customField->id,
                    ],
                    ['value' => is_array($value) ? json_encode($value) : $value]
                );
            }
        }
    }

    /**
     * Clean phone number format.
     */
    public function cleanPhoneNumber(string $number): string
    {
        return preg_replace('/[^0-9]/', '', $number);
    }
}
