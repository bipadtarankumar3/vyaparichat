<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Vendor;
use App\Services\Contact\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContactController extends Controller
{
    public function __construct(
        protected ContactService $contactService
    ) {}

    /**
     * Get active vendor for current request (from authenticated user or header/session).
     */
    protected function getActiveVendor(Request $request): Vendor
    {
        // If user is authenticated, use user's active vendor, or fallback to first vendor
        $user = $request->user();
        if ($user && $user->vendors()->exists()) {
            return $user->vendors()->first();
        }

        return Vendor::firstOrFail();
    }

    /**
     * List contacts with filtering, pagination, and search.
     */
    public function index(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);
        $perPage = (int) $request->input('per_page', 25);
        $filters = $request->only([
            'search',
            'group_id',
            'label_id',
            'assigned_user_id',
            'is_opted_out',
            'from_date',
            'to_date',
            'tab',
            'order_by',
            'order_dir',
        ]);

        $contacts = $this->contactService->listContacts($vendor, $filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $contacts,
        ]);
    }

    /**
     * Store a newly created contact.
     */
    public function store(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'phone_number' => 'required|string|min:8|max:20',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
            'country_id' => 'nullable|exists:countries,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:contact_groups,id',
            'label_ids' => 'nullable|array',
            'label_ids.*' => 'exists:labels,id',
            'is_opted_out' => 'boolean',
            'is_ai_bot_disabled' => 'boolean',
            'notes' => 'nullable|string',
            'custom_fields' => 'nullable|array',
        ]);

        $contact = $this->contactService->createContact($vendor, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Contact created successfully.',
            'data' => $contact->load(['groups', 'labels', 'assignedUser', 'country']),
        ], 201);
    }

    /**
     * Display the specified contact.
     */
    public function show(Request $request, Contact $contact): JsonResponse
    {
        $contact->load(['groups', 'labels', 'assignedUser', 'country', 'customFieldValues.customField']);

        return response()->json([
            'success' => true,
            'data' => $contact,
        ]);
    }

    /**
     * Update the specified contact.
     */
    public function update(Request $request, Contact $contact): JsonResponse
    {
        $validated = $request->validate([
            'phone_number' => 'nullable|string|min:8|max:20',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
            'country_id' => 'nullable|exists:countries,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:contact_groups,id',
            'label_ids' => 'nullable|array',
            'label_ids.*' => 'exists:labels,id',
            'is_opted_out' => 'boolean',
            'is_ai_bot_disabled' => 'boolean',
            'notes' => 'nullable|string',
            'custom_fields' => 'nullable|array',
        ]);

        $updated = $this->contactService->updateContact($contact, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully.',
            'data' => $updated,
        ]);
    }

    /**
     * Delete contact.
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully.',
        ]);
    }

    /**
     * Bulk delete contacts.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $request->validate([
            'contact_uuids' => 'required|array|min:1',
            'contact_uuids.*' => 'string',
        ]);

        $deletedCount = $this->contactService->bulkDelete($vendor, $request->input('contact_uuids'));

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} contacts.",
            'deleted_count' => $deletedCount,
        ]);
    }

    /**
     * Bulk assign groups.
     */
    public function bulkAssignGroups(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $request->validate([
            'contact_uuids' => 'required|array|min:1',
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:contact_groups,id',
        ]);

        $assignedCount = $this->contactService->bulkAssignGroups(
            $vendor,
            $request->input('contact_uuids'),
            $request->input('group_ids')
        );

        return response()->json([
            'success' => true,
            'message' => "Assigned groups to {$assignedCount} contacts.",
            'count' => $assignedCount,
        ]);
    }

    /**
     * Download sample dummy Excel template.
     */
    public function downloadDummyTemplate(): BinaryFileResponse
    {
        return $this->contactService->downloadSampleTemplate();
    }

    /**
     * Preview uploaded Excel spreadsheet before importing.
     */
    public function previewImport(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        $preview = $this->contactService->parseExcelPreview($request->file('file'));

        return response()->json([
            'success' => true,
            'data' => $preview,
        ]);
    }

    /**
     * Import batch of contacts from previewed spreadsheet.
     */
    public function processImport(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $request->validate([
            'contacts' => 'required|array|min:1',
            'group_id' => 'nullable|exists:contact_groups,id',
            'overwrite_existing' => 'boolean',
        ]);

        $result = $this->contactService->importContacts(
            $vendor,
            $request->input('contacts'),
            $request->input('group_id'),
            $request->boolean('overwrite_existing', true)
        );

        return response()->json([
            'success' => true,
            'message' => "Import complete. {$result['imported']} imported, {$result['updated']} updated, {$result['failed']} failed.",
            'data' => $result,
        ]);
    }

    /**
     * Export contacts to Excel file.
     */
    public function export(Request $request): BinaryFileResponse
    {
        $vendor = $this->getActiveVendor($request);
        return $this->contactService->exportContacts($vendor, $request->all());
    }
}
