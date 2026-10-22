<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Vendor;
use App\Services\Campaign\CampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function __construct(
        protected CampaignService $campaignService
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
     * List campaigns.
     */
    public function index(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);
        $perPage = (int) $request->input('per_page', 15);
        $filters = $request->only(['search', 'status']);

        $campaigns = $this->campaignService->listCampaigns($vendor, $filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $campaigns,
        ]);
    }

    /**
     * Store new broadcast campaign.
     */
    public function store(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'whatsapp_template_id' => 'required|exists:whatsapp_templates,id',
            'scheduled_at' => 'nullable|date',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:contact_groups,id',
            'variables_mapping' => 'nullable|array',
        ]);

        $campaign = $this->campaignService->createCampaign($vendor, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Campaign created successfully.',
            'data' => $campaign,
        ], 201);
    }

    /**
     * Show campaign details and live progress.
     */
    public function show(Campaign $campaign): JsonResponse
    {
        $campaign->load(['template', 'targetGroups']);
        $stats = $this->campaignService->getCampaignStats($campaign);

        return response()->json([
            'success' => true,
            'data' => $campaign,
            'stats' => $stats,
        ]);
    }

    /**
     * Update campaign.
     */
    public function update(Request $request, Campaign $campaign): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:200',
            'whatsapp_template_id' => 'sometimes|exists:whatsapp_templates,id',
            'scheduled_at' => 'nullable|date',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:contact_groups,id',
            'variables_mapping' => 'nullable|array',
        ]);

        $updated = $this->campaignService->updateCampaign($campaign, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Campaign updated successfully.',
            'data' => $updated,
        ]);
    }

    /**
     * Launch campaign broadcast.
     */
    public function launch(Campaign $campaign): JsonResponse
    {
        $result = $this->campaignService->launchCampaign($campaign);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to launch campaign.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result['campaign'],
        ]);
    }

    /**
     * Pause an in-flight campaign.
     */
    public function pause(Campaign $campaign): JsonResponse
    {
        $paused = $this->campaignService->pauseCampaign($campaign);

        if (!$paused) {
            return response()->json([
                'success' => false,
                'message' => 'Only in-flight (processing) campaigns can be paused.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Campaign paused successfully.',
        ]);
    }

    /**
     * Resume a paused campaign.
     */
    public function resume(Campaign $campaign): JsonResponse
    {
        $resumed = $this->campaignService->resumeCampaign($campaign);

        if (!$resumed) {
            return response()->json([
                'success' => false,
                'message' => 'Only paused campaigns can be resumed.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Campaign resumed successfully.',
        ]);
    }

    /**
     * Cancel campaign.
     */
    public function cancel(Campaign $campaign): JsonResponse
    {
        $cancelled = $this->campaignService->cancelCampaign($campaign);

        if (!$cancelled) {
            return response()->json([
                'success' => false,
                'message' => 'Completed campaigns cannot be cancelled.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Campaign cancelled successfully.',
        ]);
    }

    /**
     * Delete campaign.
     */
    public function destroy(Campaign $campaign): JsonResponse
    {
        $campaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Campaign deleted successfully.',
        ]);
    }

    /**
     * Real-time campaign stats for live progress bar polling.
     */
    public function stats(Campaign $campaign): JsonResponse
    {
        $stats = $this->campaignService->getCampaignStats($campaign);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
