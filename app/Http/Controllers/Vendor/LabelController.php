<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabelController extends Controller
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
        $labels = Label::where('vendor_id', $vendor->id)
            ->withCount('contacts')
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $labels]);
    }

    public function store(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'color' => 'nullable|string|max:30',
            'text_color' => 'nullable|string|max:30',
        ]);

        $label = Label::create([
            'vendor_id' => $vendor->id,
            'title' => $validated['title'],
            'color' => $validated['color'] ?? '#17a2b8',
            'text_color' => $validated['text_color'] ?? '#ffffff',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Label created successfully.',
            'data' => $label,
        ], 201);
    }

    public function update(Request $request, Label $label): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'color' => 'nullable|string|max:30',
            'text_color' => 'nullable|string|max:30',
        ]);

        $label->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Label updated successfully.',
            'data' => $label,
        ]);
    }

    public function destroy(Label $label): JsonResponse
    {
        $label->delete();

        return response()->json([
            'success' => true,
            'message' => 'Label deleted successfully.',
        ]);
    }
}
