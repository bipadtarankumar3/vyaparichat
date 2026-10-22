<?php

namespace App\Services\WhatsApp;

use App\Models\Vendor;
use App\Models\WhatsAppTemplate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class WhatsAppTemplateService
{
    public function __construct(
        protected WhatsAppApiService $apiService
    ) {}

    /**
     * List templates for vendor with filtering and search.
     */
    public function listTemplates(Vendor $vendor, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = WhatsAppTemplate::where('vendor_id', $vendor->id);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', strtoupper($filters['status']));
        }

        if (!empty($filters['category'])) {
            $query->where('category', strtoupper($filters['category']));
        }

        if (!empty($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Sync WhatsApp Templates from Meta Graph API.
     */
    public function syncTemplates(Vendor $vendor): array
    {
        $response = $this->apiService->fetchTemplates($vendor);

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => $response['error'] ?? 'Failed to fetch templates from Meta.',
                'synced_count' => 0,
            ];
        }

        $templates = $response['data'] ?? [];
        $syncedCount = 0;

        foreach ($templates as $metaTemplate) {
            $name = $metaTemplate['name'] ?? null;
            $language = $metaTemplate['language'] ?? 'en';
            if (!$name) {
                continue;
            }

            $components = $metaTemplate['components'] ?? [];
            $headerType = 'none';

            foreach ($components as $component) {
                if (($component['type'] ?? '') === 'HEADER') {
                    $headerType = strtolower($component['format'] ?? 'text');
                    break;
                }
            }

            WhatsAppTemplate::updateOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'name' => $name,
                    'language' => $language,
                ],
                [
                    'meta_template_id' => $metaTemplate['id'] ?? null,
                    'category' => strtoupper($metaTemplate['category'] ?? 'MARKETING'),
                    'status' => strtoupper($metaTemplate['status'] ?? 'PENDING'),
                    'header_type' => $headerType,
                    'components' => $components,
                    'rejection_reason' => $metaTemplate['rejected_reason'] ?? null,
                ]
            );

            $syncedCount++;
        }

        return [
            'success' => true,
            'synced_count' => $syncedCount,
            'total_from_meta' => count($templates),
        ];
    }

    /**
     * Create / submit new template to Meta and store locally.
     */
    public function createTemplate(Vendor $vendor, array $data): array
    {
        // 1. Submit to Meta
        $metaResult = $this->apiService->createTemplate($vendor, $data);

        if (!$metaResult['success']) {
            return [
                'success' => false,
                'error' => $metaResult['error'] ?? 'Meta template creation failed.',
            ];
        }

        $components = $data['components'] ?? [];
        $headerType = 'none';
        foreach ($components as $comp) {
            if (($comp['type'] ?? '') === 'HEADER') {
                $headerType = strtolower($comp['format'] ?? 'text');
                break;
            }
        }

        // 2. Store in local DB
        $template = WhatsAppTemplate::updateOrCreate(
            [
                'vendor_id' => $vendor->id,
                'name' => strtolower($data['name']),
                'language' => $data['language'] ?? 'en_US',
            ],
            [
                'meta_template_id' => $metaResult['meta_template_id'] ?? null,
                'category' => strtoupper($data['category']),
                'status' => strtoupper($metaResult['status'] ?? 'PENDING'),
                'header_type' => $headerType,
                'components' => $components,
            ]
        );

        return [
            'success' => true,
            'template' => $template,
            'meta' => $metaResult,
        ];
    }

    /**
     * Delete template from Meta and local DB.
     */
    public function deleteTemplate(WhatsAppTemplate $template): array
    {
        $vendor = $template->vendor;

        // Attempt deletion from Meta Graph API
        $metaResult = $this->apiService->deleteTemplate(
            $vendor,
            $template->name,
            $template->meta_template_id
        );

        // Delete from local DB regardless of Meta response (or if already removed in Meta)
        $template->delete();

        return [
            'success' => true,
            'meta_response' => $metaResult,
        ];
    }

    /**
     * Render a preview of the template with sample parameters.
     */
    public function renderPreview(WhatsAppTemplate $template, array $sampleParams = []): array
    {
        $preview = [
            'header' => null,
            'body' => null,
            'footer' => null,
            'buttons' => [],
        ];

        foreach ($template->components as $component) {
            $type = strtoupper($component['type'] ?? '');

            if ($type === 'HEADER') {
                $format = strtoupper($component['format'] ?? 'TEXT');
                $text = $component['text'] ?? '';
                if ($format === 'TEXT') {
                    $headerParams = $sampleParams['header'] ?? [];
                    foreach ($headerParams as $index => $val) {
                        $text = str_replace('{{' . ($index + 1) . '}}', $val, $text);
                    }
                }
                $preview['header'] = [
                    'format' => $format,
                    'text' => $text,
                ];
            } elseif ($type === 'BODY') {
                $bodyText = $component['text'] ?? '';
                $bodyParams = $sampleParams['body'] ?? [];
                foreach ($bodyParams as $index => $val) {
                    $bodyText = str_replace('{{' . ($index + 1) . '}}', $val, $bodyText);
                }
                $preview['body'] = $bodyText;
            } elseif ($type === 'FOOTER') {
                $preview['footer'] = $component['text'] ?? '';
            } elseif ($type === 'BUTTONS') {
                $preview['buttons'] = $component['buttons'] ?? [];
            }
        }

        return $preview;
    }
}
