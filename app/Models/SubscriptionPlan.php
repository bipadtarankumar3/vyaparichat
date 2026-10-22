<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'max_contacts',
        'max_campaigns_per_month',
        'max_staff_members',
        'has_ai_bot',
        'has_api_access',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'price_yearly' => 'decimal:2',
            'max_contacts' => 'integer',
            'max_campaigns_per_month' => 'integer',
            'max_staff_members' => 'integer',
            'has_ai_bot' => 'boolean',
            'has_api_access' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
