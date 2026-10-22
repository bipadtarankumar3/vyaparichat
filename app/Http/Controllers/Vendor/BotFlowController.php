<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\BotFlow;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BotFlowController extends Controller
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

        $flows = BotFlow::where('vendor_id', $vendor->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $flows,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'flow_data' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $flow = BotFlow::create([
            'vendor_id' => $vendor->id,
            'title' => $validated['title'],
            'flow_data' => $validated['flow_data'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visual bot flow created successfully.',
            'data' => $flow,
        ], 201);
    }

    public function show(BotFlow $botFlow): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $botFlow,
        ]);
    }

    public function update(Request $request, BotFlow $botFlow): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:200',
            'flow_data' => 'sometimes|array',
            'is_active' => 'boolean',
        ]);

        $botFlow->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Visual bot flow updated successfully.',
            'data' => $botFlow,
        ]);
    }

    public function destroy(BotFlow $botFlow): JsonResponse
    {
        $botFlow->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bot flow deleted successfully.',
        ]);
    }
}
