<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\BotReply;
use App\Models\Vendor;
use App\Services\Bot\BotAutomationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BotReplyController extends Controller
{
    public function __construct(
        protected BotAutomationService $botService
    ) {}

    protected function getActiveVendor(Request $request): Vendor
    {
        $user = $request->user();
        if ($user && $user->vendors()->exists()) {
            return $user->vendors()->first();
        }

        return Vendor::firstOrFail();
    }

    /**
     * List all bot replies for vendor.
     */
    public function index(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $rules = BotReply::where('vendor_id', $vendor->id)
            ->orderBy('priority', 'desc')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rules,
        ]);
    }

    /**
     * Store new bot reply rule.
     */
    public function store(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'trigger_type' => 'required|string|in:exact,contains,starts_with,regex,fallback',
            'trigger_keywords' => 'nullable|array',
            'reply_type' => 'required|string|in:text,interactive,media,template',
            'reply_payload' => 'required|array',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0|max:1000',
        ]);

        $rule = BotReply::create([
            'vendor_id' => $vendor->id,
            'name' => $validated['name'],
            'trigger_type' => $validated['trigger_type'],
            'trigger_keywords' => $validated['trigger_keywords'] ?? [],
            'reply_type' => $validated['reply_type'],
            'reply_payload' => $validated['reply_payload'],
            'is_active' => $validated['is_active'] ?? true,
            'priority' => $validated['priority'] ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bot reply rule created successfully.',
            'data' => $rule,
        ], 201);
    }

    /**
     * Show single bot reply rule.
     */
    public function show(BotReply $botReply): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $botReply,
        ]);
    }

    /**
     * Update bot reply rule.
     */
    public function update(Request $request, BotReply $botReply): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:150',
            'trigger_type' => 'sometimes|string|in:exact,contains,starts_with,regex,fallback',
            'trigger_keywords' => 'nullable|array',
            'reply_type' => 'sometimes|string|in:text,interactive,media,template',
            'reply_payload' => 'sometimes|array',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0|max:1000',
        ]);

        $botReply->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bot reply rule updated successfully.',
            'data' => $botReply,
        ]);
    }

    /**
     * Delete bot reply rule.
     */
    public function destroy(BotReply $botReply): JsonResponse
    {
        $botReply->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bot reply rule deleted successfully.',
        ]);
    }

    /**
     * Simulate bot match against test message.
     */
    public function simulate(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $request->validate([
            'message' => 'required|string',
        ]);

        $result = $this->botService->simulate($vendor, $request->input('message'));

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
