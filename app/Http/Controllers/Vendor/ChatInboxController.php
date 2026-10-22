<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Vendor;
use App\Services\Chat\ChatInboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatInboxController extends Controller
{
    public function __construct(
        protected ChatInboxService $inboxService
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
     * List all conversations with filters (search, tab, label).
     */
    public function conversations(Request $request): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);
        $perPage = (int) $request->input('per_page', 25);
        $filters = $request->only(['search', 'tab', 'label_id']);

        $conversations = $this->inboxService->listConversations(
            $vendor,
            $filters,
            $request->user(),
            $perPage
        );

        return response()->json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    /**
     * Get message thread for a contact.
     */
    public function messages(Request $request, Contact $contact): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 50);
        $messages = $this->inboxService->getMessageThread($contact, $perPage);

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    /**
     * Send real-time message (text, media, interactive).
     */
    public function sendMessage(Request $request, Contact $contact): JsonResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'type' => 'required|string|in:text,media,interactive',
            'message' => 'required_if:type,text|nullable|string',
            'media_type' => 'required_if:type,media|nullable|string|in:image,video,audio,document',
            'media_url' => 'nullable|string|url',
            'file' => 'nullable|file|max:20480', // 20MB max
            'caption' => 'nullable|string',
            'body_text' => 'required_if:type,interactive|nullable|string',
            'buttons' => 'required_if:type,interactive|nullable|array|min:1|max:3',
            'buttons.*.id' => 'required|string|max:20',
            'buttons.*.title' => 'required|string|max:20',
            'header_text' => 'nullable|string|max:60',
            'footer_text' => 'nullable|string|max:60',
        ]);

        $result = $this->inboxService->sendMessage(
            $vendor,
            $contact,
            $validated,
            $request->user()
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to send message via WhatsApp Cloud API.',
                'data' => $result['message'] ?? null,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => $result['message'],
        ], 201);
    }

    /**
     * Assign conversation to a team agent.
     */
    public function assignAgent(Request $request, Contact $contact): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        $updated = $this->inboxService->assignAgent($contact, $validated['user_id'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Conversation assigned successfully.',
            'data' => $updated,
        ]);
    }

    /**
     * Mark all unread messages for conversation as read.
     */
    public function markAsRead(Contact $contact): JsonResponse
    {
        $count = $this->inboxService->markAsRead($contact);

        return response()->json([
            'success' => true,
            'message' => "Marked {$count} messages as read.",
            'marked_count' => $count,
        ]);
    }

    /**
     * Toggle AI Bot responder on/off.
     */
    public function toggleBot(Request $request, Contact $contact): JsonResponse
    {
        $validated = $request->validate([
            'disabled' => 'required|boolean',
        ]);

        $updated = $this->inboxService->toggleAiBot($contact, $validated['disabled']);

        return response()->json([
            'success' => true,
            'message' => $validated['disabled'] ? 'AI Bot disabled for contact.' : 'AI Bot enabled for contact.',
            'data' => $updated,
        ]);
    }

    /**
     * Update internal agent notes.
     */
    public function updateNotes(Request $request, Contact $contact): JsonResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $updated = $this->inboxService->updateNotes($contact, $validated['notes'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Notes saved successfully.',
            'data' => $updated,
        ]);
    }
}
