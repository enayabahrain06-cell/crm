<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'city',
        'address',
        'country',
        'phone',
        'email',
        'logo',
        'hero_image',
        'timezone',
        'currency',
        'active',
        'meta',
    ];

    protected $casts = [
        'active' => 'boolean',
        'meta' => 'array',
        'country' => 'string',
    ];

    /**
     * Get the users assigned to this outlet
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'outlet_user')
            ->withPivot(['role_at_outlet'])
            ->withTimestamps();
    }

    /**
     * Get customers first registered at this outlet
     */
    public function firstRegisteredCustomers()
    {
        return $this->hasMany(Customer::class, 'first_registration_outlet_id');
    }

    /**
     * Get all visits at this outlet
     */
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Get customers who visited this outlet
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'visits')
            ->withPivot(['visited_at', 'visit_type', 'bill_amount', 'items_json'])
            ->distinct();
    }

    /**
     * Get social links for this outlet
     */
    public function socialLinks()
    {
        return $this->hasMany(OutletSocialLink::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Get loyalty rules for this outlet
     */
    public function loyaltyRules()
    {
        return $this->hasMany(LoyaltyRule::class);
    }

    /**
     * Get reward redemptions at this outlet
     */
    public function rewardRedemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    /**
     * Get loyalty ledger entries for this outlet
     */
    public function loyaltyLedger()
    {
        return $this->hasMany(LoyaltyPointLedger::class);
    }

    /**
     * Scope for active outlets
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope by city
     */
    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Find outlet by code
     */
    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }

    /**
     * Get the outlet's active status
     */
    public function getIsActiveAttribute(): bool
    {
        return (bool) $this->active;
    }

    /**
     * Get the active status (alias for is_active accessor)
     */
    public function getActiveAttribute($value): bool
    {
        return (bool) $value;
    }

    /**
     * Get QR code registration URL
     */
    public function getRegistrationUrlAttribute(): string
    {
        return route('public.register', ['outlet' => $this->code]);
    }

    /**
     * Get linktree URL
     */
    public function getLinktreeUrlAttribute(): string
    {
        return route('public.outlet.links', ['outletCode' => $this->code]);
    }
}

