<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\WhatsAppTemplate;
use App\Services\WhatsApp\WhatsAppTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppTemplateController extends Controller
{
    public function __construct(
        protected WhatsAppTemplateService $templateService
    ) {}

    /**
     * Get active vendor for current request.
     */
    protected function getActiveVendor(Request $request): Vendor
    {
        $user = $request->user();
        if ($user && $user->vendors()->exists()) {
            return $user->vendors()->first();
        }

        return Vendor::firstOrFail();
    }

    /**
     * List WhatsApp templates for vendor.
     */
    public function index(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);
        $perPage = (int) $request->input('per_page', 20);
        $filters = $request->only(['search', 'status', 'category', 'language']);

        $templates = $this->templateService->listTemplates($vendor, $filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    /**
     * Create and submit a new WhatsApp template to Meta.
     */
    public function store(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'name' => 'required|string|regex:/^[a-z0-9_]+$/|max:150',
            'language' => 'required|string|max:10',
            'category' => 'required|string|in:MARKETING,UTILITY,AUTHENTICATION',
            'components' => 'required|array|min:1',
            'components.*.type' => 'required|string|in:HEADER,BODY,FOOTER,BUTTONS',
        ]);

        $result = $this->templateService->createTemplate($vendor, $validated);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Template creation failed.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Template created and submitted to Meta for review.',
            'data' => $result['template'],
        ], 201);
    }

    /**
     * Show template details with preview.
     */
    public function show(WhatsAppTemplate $template): JsonResponse
    {
        $preview = $this->templateService->renderPreview($template);

        return response()->json([
            'success' => true,
            'data' => $template,
            'preview' => $preview,
        ]);
    }

    /**
     * Delete template from Meta and local DB.
     */
    public function destroy(WhatsAppTemplate $template): JsonResponse
    {
        $this->templateService->deleteTemplate($template);

        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully.',
        ]);
    }

    /**
     * Sync templates from Meta Graph API.
     */
    public function sync(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);
        $result = $this->templateService->syncTemplates($vendor);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to sync templates from Meta.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully synced {$result['synced_count']} templates from Meta.",
            'data' => $result,
        ]);
    }

    /**
     * Render live preview with customized sample variable values.
     */
    public function preview(Request $request, WhatsAppTemplate $template): JsonResponse
    {
        $sampleParams = $request->input('sample_parameters', []);
        $preview = $this->templateService->renderPreview($template, $sampleParams);

        return response()->json([
            'success' => true,
            'preview' => $preview,
        ]);
    }
}
