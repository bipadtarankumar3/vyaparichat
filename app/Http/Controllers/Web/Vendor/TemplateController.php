<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Models\WhatsAppTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TemplateController extends BaseVendorController
{
    /**
     * Listing: Templates.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $templates = WhatsAppTemplate::where('vendor_id', $vendor->id)->get();

        return view('vendor.templates.index', [
            'vendor' => $vendor,
            'templates' => $templates,
            'activeNav' => 'templates',
        ]);
    }

    /**
     * Add: Store new WhatsApp Template.
     */
    public function store(Request $request): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:MARKETING,UTILITY,AUTHENTICATION',
            'language' => 'required|string|max:10',
            'body_text' => 'required|string|max:1024',
            'header_type' => 'nullable|string|in:NONE,TEXT,IMAGE,VIDEO,DOCUMENT',
            'header_text' => 'nullable|string|max:60',
            'footer_text' => 'nullable|string|max:60',
        ]);

        $templateName = Str::snake(strtolower($validated['name']));

        WhatsAppTemplate::create([
            'vendor_id' => $vendor->id,
            'name' => $templateName,
            'category' => $validated['category'],
            'language' => $validated['language'],
            'status' => 'APPROVED',
            'header_type' => $validated['header_type'] ?? 'NONE',
            'components' => [
                ['type' => 'BODY', 'text' => $validated['body_text']],
            ],
        ]);

        return back()->with('success', "Template \"{$templateName}\" created and ready to use.");
    }

    /**
     * Delete: Remove WhatsApp Template.
     */
    public function destroy(Request $request, WhatsAppTemplate $template): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($template->vendor_id !== $vendor->id) {
            abort(403);
        }

        $template->delete();
        return back()->with('success', 'Template removed successfully.');
    }
}
