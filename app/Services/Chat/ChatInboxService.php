<?php

namespace App\Services\Chat;

use App\Models\Contact;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppApiService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ChatInboxService
{
    public function __construct(
        protected WhatsAppApiService $apiService
    ) {}

    /**
     * List conversations with search, tabs, unread counts, and last message snippet.
     */
    public function listConversations(
        Vendor $vendor,
        array $filters = [],
        ?User $currentUser = null,
        int $perPage = 25
    ): LengthAwarePaginator {
        $query = Contact::with(['labels', 'assignedUser', 'country'])
            ->where('vendor_id', $vendor->id)
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('direction', 'inbound')
                  ->whereNull('read_at');
            }]);

        // Filter by tab
        $tab = $filters['tab'] ?? 'all';
        if ($tab === 'mine' && $currentUser) {
            $query->where('assigned_user_id', $currentUser->id);
        } elseif ($tab === 'unassigned') {
            $query->whereNull('assigned_user_id');
        } elseif ($tab === 'unread') {
            $query->whereHas('messages', function ($q) {
                $q->where('direction', 'inbound')->whereNull('read_at');
            });
        }

        // Filter by label / tag
        if (!empty($filters['label_id'])) {
            $labelId = (int) $filters['label_id'];
            $query->whereHas('labels', fn($q) => $q->where('labels.id', $labelId));
        }

        // Search in contact name, phone, email
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $cleanPhone = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $cleanPhone) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");

                if (!empty($cleanPhone)) {
                    $q->orWhere('phone_number', 'like', "%{$cleanPhone}%");
                }
            });
        }

        // Order by latest message activity
        $query->orderByDesc(
            WhatsAppMessage::select('created_at')
                ->whereColumn('contact_id', 'contacts.id')
                ->latest()
                ->limit(1)
        )->orderByDesc('updated_at');

        $paginated = $query->paginate($perPage);

        // Attach latest message snippet to each contact
        $paginated->getCollection()->transform(function ($contact) {
            $latest = $contact->messages()->latest()->first();
            $contact->latest_message = $latest ? [
                'text' => $latest->message_text,
                'direction' => $latest->direction,
                'message_type' => $latest->message_type,
                'status' => $latest->status,
                'created_at' => $latest->created_at->toIso8601String(),
            ] : null;

            return $contact;
        });

        return $paginated;
    }

    /**
     * Get paginated message history for a contact conversation.
     */
    public function getMessageThread(Contact $contact, int $perPage = 50): LengthAwarePaginator
    {
        return WhatsAppMessage::where('contact_id', $contact->id)
            ->with('assignedUser:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Send real-time message to contact via WhatsApp Cloud API.
     */
    public function sendMessage(
        Vendor $vendor,
        Contact $contact,
        array $data,
        ?User $sender = null
    ): array {
        $type = $data['type'] ?? 'text';
        $apiResult = ['success' => false];

        if ($type === 'text') {
            $messageText = trim($data['message']);
            $apiResult = $this->apiService->sendTextMessage(
                $vendor,
                $contact->phone_number,
                $messageText
            );

            $message = WhatsAppMessage::create([
                'vendor_id' => $vendor->id,
                'contact_id' => $contact->id,
                'assigned_user_id' => $sender?->id,
                'direction' => 'outbound',
                'message_type' => 'text',
                'message_text' => $messageText,
                'status' => $apiResult['success'] ? 'sent' : 'failed',
                'wamid' => $apiResult['wamid'] ?? null,
                'error_message' => $apiResult['error'] ?? null,
                'sent_at' => $apiResult['success'] ? now() : null,
                'raw_payload' => $apiResult['data'] ?? null,
            ]);
        } elseif ($type === 'media') {
            $mediaType = $data['media_type'] ?? 'image';
            $mediaUrl = $data['media_url'] ?? '';

            // Handle file upload if present
            if (!empty($data['file']) && $data['file'] instanceof UploadedFile) {
                $path = $data['file']->store('chat_media', 'public');
                $mediaUrl = asset('storage/' . $path);
            }

            $caption = $data['caption'] ?? null;

            $apiResult = $this->apiService->sendMediaMessage(
                $vendor,
                $contact->phone_number,
                $mediaType,
                $mediaUrl,
                $caption
            );

            $message = WhatsAppMessage::create([
                'vendor_id' => $vendor->id,
                'contact_id' => $contact->id,
                'assigned_user_id' => $sender?->id,
                'direction' => 'outbound',
                'message_type' => $mediaType,
                'message_text' => $caption,
                'media_url' => $mediaUrl,
                'status' => $apiResult['success'] ? 'sent' : 'failed',
                'wamid' => $apiResult['wamid'] ?? null,
                'error_message' => $apiResult['error'] ?? null,
                'sent_at' => $apiResult['success'] ? now() : null,
                'raw_payload' => $apiResult['data'] ?? null,
            ]);
        } elseif ($type === 'interactive') {
            $bodyText = $data['body_text'] ?? '';
            $buttons = $data['buttons'] ?? [];
            $headerText = $data['header_text'] ?? null;
            $footerText = $data['footer_text'] ?? null;

            $apiResult = $this->apiService->sendInteractiveButtonsMessage(
                $vendor,
                $contact->phone_number,
                $bodyText,
                $buttons,
                $headerText,
                $footerText
            );

            $message = WhatsAppMessage::create([
                'vendor_id' => $vendor->id,
                'contact_id' => $contact->id,
                'assigned_user_id' => $sender?->id,
                'direction' => 'outbound',
                'message_type' => 'interactive',
                'message_text' => $bodyText,
                'status' => $apiResult['success'] ? 'sent' : 'failed',
                'wamid' => $apiResult['wamid'] ?? null,
                'error_message' => $apiResult['error'] ?? null,
                'sent_at' => $apiResult['success'] ? now() : null,
                'raw_payload' => $apiResult['data'] ?? null,
            ]);
        } else {
            return [
                'success' => false,
                'error' => "Unsupported message type: {$type}",
            ];
        }

        $contact->touch();

        return [
            'success' => $apiResult['success'],
            'message' => $message,
            'error' => $apiResult['error'] ?? null,
        ];
    }

    /**
     * Assign / reassign conversation to an agent.
     */
    public function assignAgent(Contact $contact, ?int $userId): Contact
    {
        $contact->update(['assigned_user_id' => $userId]);
        return $contact->load('assignedUser');
    }

    /**
     * Mark all unread inbound messages for contact as read.
     */
    public function markAsRead(Contact $contact): int
    {
        return $contact->messages()
            ->where('direction', 'inbound')
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'status' => 'read',
            ]);
    }

    /**
     * Toggle AI Bot auto-responder status for contact.
     */
    public function toggleAiBot(Contact $contact, bool $disabled): Contact
    {
        $contact->update(['is_ai_bot_disabled' => $disabled]);
        return $contact;
    }

    /**
     * Update internal agent notes for contact.
     */
    public function updateNotes(Contact $contact, ?string $notes): Contact
    {
        $contact->update(['notes' => $notes]);
        return $contact;
    }
}
