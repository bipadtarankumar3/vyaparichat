<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Models\Contact;
use App\Models\WhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends BaseVendorController
{
    /**
     * Dedicated Live Chat / Inbox Page.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);

        $contacts = Contact::where('vendor_id', $vendor->id)
            ->with(['messages' => fn ($q) => $q->latest()->limit(1)])
            ->orderByDesc('created_at')
            ->take(30)
            ->get();

        $activeContact = null;
        if ($contactId = $request->input('contact_id')) {
            $activeContact = Contact::where('vendor_id', $vendor->id)->find($contactId);
        }

        if (!$activeContact && $contacts->isNotEmpty()) {
            $activeContact = $contacts->first();
        }

        $messages = collect();
        if ($activeContact) {
            $messages = WhatsAppMessage::where('vendor_id', $vendor->id)
                ->where('contact_id', $activeContact->id)
                ->orderBy('created_at', 'asc')
                ->take(100)
                ->get();
        }

        return view('vendor.chat.index', [
            'vendor' => $vendor,
            'contacts' => $contacts,
            'activeContact' => $activeContact,
            'messages' => $messages,
            'activeNav' => 'chat',
        ]);
    }
}
