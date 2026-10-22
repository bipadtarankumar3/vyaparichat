<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotReply extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'vendor_id',
        'name',
        'trigger_type',
        'trigger_keywords',
        'reply_type',
        'reply_payload',
        'is_active',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'trigger_keywords' => 'array',
            'reply_payload' => 'array',
            'is_active' => 'boolean',
            'priority' => 'integer',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
