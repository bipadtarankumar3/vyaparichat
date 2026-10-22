<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotFlow extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'vendor_id',
        'title',
        'flow_data',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'flow_data' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
