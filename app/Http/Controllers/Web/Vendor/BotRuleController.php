<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Models\BotReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BotRuleController extends BaseVendorController
{
    /**
     * Listing: Bot Rules.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $rules = BotReply::where('vendor_id', $vendor->id)->get();

        return view('vendor.bot.index', [
            'vendor' => $vendor,
            'rules' => $rules,
            'activeNav' => 'bot_rules',
        ]);
    }

    /**
     * Add: Store new Bot Rule.
     */
    public function store(Request $request): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'trigger_type' => 'required|in:exact,contains,starts_with,regex',
            'keywords' => 'required|string',
            'reply_type' => 'required|in:text,template,interactive',
            'reply_content' => 'required|string|max:2000',
            'is_active' => 'nullable',
        ]);

        $keywordsArray = array_values(array_filter(array_map('trim', explode(',', $validated['keywords']))));

        BotReply::create([
            'vendor_id' => $vendor->id,
            'name' => $validated['name'],
            'trigger_type' => $validated['trigger_type'],
            'trigger_keywords' => $keywordsArray,
            'reply_type' => $validated['reply_type'],
            'reply_payload' => ['text' => $validated['reply_content']],
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
            'priority' => 10,
        ]);

        return back()->with('success', "Bot rule \"{$validated['name']}\" saved successfully.");
    }

    /**
     * Update: Update Bot Rule.
     */
    public function update(Request $request, BotReply $rule): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($rule->vendor_id !== $vendor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'trigger_type' => 'nullable|in:exact,contains,starts_with,regex',
            'keywords' => 'nullable|string',
            'reply_type' => 'nullable|in:text,template,interactive',
            'reply_content' => 'required|string|max:2000',
            'is_active' => 'nullable',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'reply_payload' => ['text' => $validated['reply_content']],
        ];

        if (!empty($validated['trigger_type'])) {
            $updateData['trigger_type'] = $validated['trigger_type'];
        }

        if (!empty($validated['keywords'])) {
            $updateData['trigger_keywords'] = array_values(array_filter(array_map('trim', explode(',', $validated['keywords']))));
        }

        $updateData['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : true;

        $rule->update($updateData);
        return back()->with('success', "Bot rule \"{$rule->name}\" updated successfully.");
    }

    /**
     * Delete: Remove Bot Rule.
     */
    public function destroy(Request $request, BotReply $rule): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($rule->vendor_id !== $vendor->id) {
            abort(403);
        }

        $rule->delete();
        return back()->with('success', 'Bot rule deleted successfully.');
    }
}
