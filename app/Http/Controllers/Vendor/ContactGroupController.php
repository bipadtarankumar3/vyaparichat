<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ContactGroup;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactGroupController extends Controller
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
        $groups = ContactGroup::where('vendor_id', $vendor->id)
            ->withCount('contacts')
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $groups]);
    }

    public function store(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
        ]);

        $group = ContactGroup::create([
            'vendor_id' => $vendor->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Group created successfully.',
            'data' => $group,
        ], 201);
    }

    public function update(Request $request, ContactGroup $contactGroup): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
        ]);

        $contactGroup->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Group updated successfully.',
            'data' => $contactGroup,
        ]);
    }

    public function destroy(ContactGroup $contactGroup): JsonResponse
    {
        $contactGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Group deleted successfully.',
        ]);
    }
}
