<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Models\ContactGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactGroupController extends BaseVendorController
{
    /**
     * Listing: Contact Groups.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $groups = ContactGroup::where('vendor_id', $vendor->id)
            ->withCount('contacts')
            ->get();

        return view('vendor.contact-groups.index', [
            'vendor' => $vendor,
            'groups' => $groups,
            'activeNav' => 'contacts_groups',
        ]);
    }

    /**
     * Add: Store new Contact Group.
     */
    public function store(Request $request): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
            'title' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        $title = $validated['title'] ?? $validated['name'] ?? 'New Group';

        ContactGroup::create([
            'vendor_id' => $vendor->id,
            'title' => $title,
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', "Group \"{$title}\" created successfully.");
    }

    /**
     * Update: Update Contact Group.
     */
    public function update(Request $request, ContactGroup $group): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($group->vendor_id !== $vendor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
            'title' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        $title = $validated['title'] ?? $validated['name'] ?? $group->title;

        $group->update([
            'title' => $title,
            'description' => $validated['description'] ?? $group->description,
        ]);

        return back()->with('success', "Group \"{$title}\" updated successfully.");
    }

    /**
     * Delete: Remove Contact Group.
     */
    public function destroy(Request $request, ContactGroup $group): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($group->vendor_id !== $vendor->id) {
            abort(403);
        }

        $group->delete();
        return back()->with('success', 'Group deleted successfully.');
    }
}
