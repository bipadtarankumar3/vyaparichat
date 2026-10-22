<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Models\BotFlow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BotFlowController extends BaseVendorController
{
    /**
     * Listing: Visual Bot Flows.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $flows = BotFlow::where('vendor_id', $vendor->id)->get();

        return view('vendor.bot-flows.index', [
            'vendor' => $vendor,
            'flows' => $flows,
            'activeNav' => 'bot_flows',
        ]);
    }

    /**
     * Add: Store new Visual Bot Flow.
     */
    public function store(Request $request): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'title' => 'nullable|string|max:150',
            'name' => 'nullable|string|max:150',
            'trigger_keyword' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'nullable|in:active,draft,inactive',
        ]);

        $title = $validated['title'] ?? $validated['name'] ?? 'Flow #' . Str::random(5);
        $trigger = $validated['trigger_keyword'] ?? 'start';
        $isActive = ($validated['status'] ?? 'active') === 'active';

        BotFlow::create([
            'vendor_id' => $vendor->id,
            'title' => $title,
            'is_active' => $isActive,
            'flow_data' => [
                'trigger' => $trigger,
                'description' => $validated['description'] ?? null,
                'nodes' => [
                    ['id' => 'start', 'type' => 'trigger', 'title' => 'Start On: ' . $trigger],
                    ['id' => 'msg1', 'type' => 'message', 'title' => 'Send WhatsApp Catalog Menu'],
                ],
                'edges' => [],
            ],
        ]);

        return back()->with('success', "Bot Flow \"{$title}\" created successfully.");
    }

    /**
     * Update: Update Visual Bot Flow.
     */
    public function update(Request $request, BotFlow $flow): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($flow->vendor_id !== $vendor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:150',
            'name' => 'nullable|string|max:150',
            'trigger_keyword' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'nullable|in:active,draft,inactive',
        ]);

        $updateData = [];
        if (!empty($validated['title']) || !empty($validated['name'])) {
            $updateData['title'] = $validated['title'] ?? $validated['name'];
        }
        if (isset($validated['status'])) {
            $updateData['is_active'] = $validated['status'] === 'active';
        }

        $flow->update($updateData);
        return back()->with('success', "Bot flow updated successfully.");
    }

    /**
     * Delete: Remove Visual Bot Flow.
     */
    public function destroy(Request $request, BotFlow $flow): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        if ($flow->vendor_id !== $vendor->id) {
            abort(403);
        }

        $flow->delete();
        return back()->with('success', 'Bot flow deleted successfully.');
    }
}
