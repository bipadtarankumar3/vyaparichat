<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Models\ContactCustomField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomFieldController extends BaseVendorController
{
    /**
     * Listing: Custom Fields.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $fields = ContactCustomField::where('vendor_id', $vendor->id)->get();

        return view('vendor.custom-fields.index', [
            'vendor' => $vendor,
            'fields' => $fields,
            'activeNav' => 'contacts_fields',
        ]);
    }

    /**
     * Add: Store new Custom Field.
     */
    public function store(Request $request): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
            'field_name' => 'nullable|string|max:100',
            'field_key' => 'nullable|string|max:100',
            'field_type' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'is_required' => 'nullable|boolean',
        ]);

        $fieldName = $validated['field_name'] ?? $validated['name'] ?? 'Custom Field';
        $key = !empty($validated['field_key']) ? Str::snake($validated['field_key']) : Str::snake($fieldName);

        ContactCustomField::create([
            'vendor_id' => $vendor->id,
            'field_name' => $fieldName,
            'field_key' => $key,
            'field_type' => $validated['field_type'] ?? 'text',
            'is_required' => (bool) ($validated['is_required'] ?? false),
        ]);

        return back()->with('success', "Custom field \"{$fieldName}\" added successfully.");
    }

    /**
     * Update: Update Custom Field.
     */
    public function update(Request $request, ContactCustomField $field): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($field->vendor_id !== $vendor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
            'field_name' => 'nullable|string|max:100',
            'is_required' => 'nullable|boolean',
        ]);

        $fieldName = $validated['field_name'] ?? $validated['name'] ?? $field->field_name;

        $field->update([
            'field_name' => $fieldName,
            'is_required' => isset($validated['is_required']) ? (bool) $validated['is_required'] : $field->is_required,
        ]);

        return back()->with('success', "Custom field updated successfully.");
    }

    /**
     * Delete: Remove Custom Field.
     */
    public function destroy(Request $request, ContactCustomField $field): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($field->vendor_id !== $vendor->id) {
            abort(403);
        }

        $field->delete();
        return back()->with('success', 'Custom field deleted successfully.');
    }
}
