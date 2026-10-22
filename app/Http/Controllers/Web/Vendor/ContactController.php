<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends BaseVendorController
{
    /**
     * Listing: Contacts.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $groups = ContactGroup::where('vendor_id', $vendor->id)->get();
        $query = Contact::where('vendor_id', $vendor->id)->with('group');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($groupId = $request->input('group_id')) {
            $query->where('contact_group_id', $groupId);
        }

        $contacts = $query->latest()->paginate(20)->withQueryString();

        return view('vendor.contacts.index', [
            'vendor' => $vendor,
            'contacts' => $contacts,
            'groups' => $groups,
            'activeNav' => 'contacts',
        ]);
    }

    /**
     * Add: Store new Contact.
     */
    public function store(Request $request): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:150',
            'contact_group_id' => 'nullable|exists:contact_groups,id',
            'lead_status' => 'nullable|in:new,warm,hot,won,lost',
        ]);

        $phone = preg_replace('/[^0-9+]/', '', $validated['phone']);

        Contact::create([
            'vendor_id' => $vendor->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'phone_number' => $phone,
            'email' => $validated['email'] ?? null,
            'notes' => $validated['lead_status'] ?? 'new',
        ]);

        return back()->with('success', "Contact \"{$validated['first_name']}\" created successfully.");
    }

    /**
     * Update: Update Contact details.
     */
    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($contact->vendor_id !== $vendor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:150',
            'contact_group_id' => 'nullable|exists:contact_groups,id',
            'lead_status' => 'nullable|in:new,warm,hot,won,lost',
        ]);

        $contact->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'phone_number' => preg_replace('/[^0-9+]/', '', $validated['phone']),
            'email' => $validated['email'] ?? null,
            'notes' => $validated['lead_status'] ?? $contact->notes,
        ]);

        return back()->with('success', "Contact \"{$contact->first_name}\" updated successfully.");
    }

    /**
     * Delete: Remove Contact.
     */
    public function destroy(Request $request, Contact $contact): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($contact->vendor_id !== $vendor->id) {
            abort(403);
        }

        $contact->delete();
        return back()->with('success', 'Contact deleted successfully.');
    }
}
