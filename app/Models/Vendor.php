<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'email',
        'phone',
        'status',
        'timezone',
        'whatsapp_status',
        'whatsapp_phone_number_id',
        'whatsapp_business_account_id',
        'custom_permissions',
    ];

    protected function casts(): array
    {
        return [
            'custom_permissions' => 'array',
        ];
    }

    /**
     * Check if vendor has access to a specific section/module.
     * Allowed keys: 'chat', 'contacts', 'campaigns', 'templates', 'bot', 'integrations', 'billing', 'settings'
     */
    public function hasSectionAccess(string $section): bool
    {
        if (is_array($this->custom_permissions) && array_key_exists($section, $this->custom_permissions)) {
            return (bool) $this->custom_permissions[$section];
        }

        return true;
    }

    /**
     * Settings key-value pairs for this vendor.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(VendorSetting::class);
    }

    /**
     * Helper to get a setting value.
     */
    public function getSetting(string $key, $default = null)
    {
        $setting = $this->settings()->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Helper to set a setting value.
     */
    public function setSetting(string $key, $value): void
    {
        $this->settings()->updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value]
        );
    }

    /**
     * Users associated with this vendor.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'vendor_users')
            ->withPivot('role', 'permissions', 'status')
            ->withTimestamps();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function contactGroups(): HasMany
    {
        return $this->hasMany(ContactGroup::class);
    }

    public function labels(): HasMany
    {
        return $this->hasMany(Label::class);
    }

    public function whatsappTemplates(): HasMany
    {
        return $this->hasMany(WhatsAppTemplate::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    public function botReplies(): HasMany
    {
        return $this->hasMany(BotReply::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latestOfMany();
    }

    public function currentSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latestOfMany();
    }

    /**
     * Get vendor's public API key.
     */
    public function getApiKey(): ?string
    {
        return $this->getSetting('api_key');
    }

    /**
     * Generate or rotate vendor's public API key.
     */
    public function generateApiKey(): string
    {
        $key = 'vyapari_live_' . \Illuminate\Support\Str::random(32);
        $this->setSetting('api_key', $key);
        return $key;
    }
}
