<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppTemplate extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'whatsapp_templates';

    protected $fillable = [
        'uuid',
        'vendor_id',
        'meta_template_id',
        'name',
        'language',
        'category',
        'status',
        'header_type',
        'components',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'components' => 'array',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
