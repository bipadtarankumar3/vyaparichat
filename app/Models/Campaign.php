<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'vendor_id',
        'whatsapp_template_id',
        'title',
        'scheduled_at',
        'status',
        'variables_mapping',
        'total_contacts',
        'sent_count',
        'delivered_count',
        'read_count',
        'failed_count',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'variables_mapping' => 'array',
            'total_contacts' => 'integer',
            'sent_count' => 'integer',
            'delivered_count' => 'integer',
            'read_count' => 'integer',
            'failed_count' => 'integer',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsAppTemplate::class, 'whatsapp_template_id');
    }

    public function targetGroups(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'campaign_group_pivot');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    public function queueItems(): HasMany
    {
        return $this->hasMany(WhatsAppMessageQueue::class);
    }
}
