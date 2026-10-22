<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ContactCustomField;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactCustomFieldController extends Controller
{
    protected function getActiveVendor(Request $request): Vendor
    {
        $user = $request->user();
        if ($user && $user->vendors()->exists()) {
            return $user->vendors()->first();
        }
        return Vendor::firstOrFail();
    }

    public function index(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);
        $fields = ContactCustomField::where('vendor_id', $vendor->id)
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $fields]);
    }

    public function store(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'field_name' => 'required|string|max:150',
            'field_type' => 'required|string|in:text,number,date,select',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
        ]);

        $fieldKey = Str::slug($validated['field_name'], '_');

        $field = ContactCustomField::create([
            'vendor_id' => $vendor->id,
            'field_key' => $fieldKey,
            'field_name' => $validated['field_name'],
            'field_type' => $validated['field_type'],
            'options' => $validated['options'] ?? null,
            'is_required' => !empty($validated['is_required']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Custom field created successfully.',
            'data' => $field,
        ], 201);
    }

    public function update(Request $request, ContactCustomField $customField): JsonResponse
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:150',
            'field_type' => 'required|string|in:text,number,date,select',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
        ]);

        $customField->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Custom field updated successfully.',
            'data' => $customField,
        ]);
    }

    public function destroy(ContactCustomField $customField): JsonResponse
    {
        $customField->delete();

        return response()->json([
            'success' => true,
            'message' => 'Custom field deleted successfully.',
        ]);
    }
}
