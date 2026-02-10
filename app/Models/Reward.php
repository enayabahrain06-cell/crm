<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'required_points',
        'type',
        'outlet_scope_json',
        'discount_value',
        'currency',
        'valid_from',
        'valid_to',
        'active',
        'max_redemptions',
        'current_redemptions',
        'image',
    ];

    protected $casts = [
        'required_points' => 'integer',
        'outlet_scope_json' => 'array',
        'discount_value' => 'decimal:3',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'active' => 'boolean',
        'max_redemptions' => 'integer',
        'current_redemptions' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_to')
                  ->orWhere('valid_to', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('max_redemptions')
                  ->orWhereColumn('current_redemptions', '<', 'max_redemptions');
            });
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopePointsRequiredAtLeast($query, int $points)
    {
        return $query->where('required_points', '>=', $points);
    }

    public function scopePointsRequiredAtMost($query, int $points)
    {
        return $query->where('required_points', '<=', $points);
    }

    public function redemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    public function isValidForCustomer(Customer $customer): bool
    {
        if (!$this->active) {
            return false;
        }

        if ($this->valid_from && $this->valid_from->isFuture()) {
            return false;
        }

        if ($this->valid_to && $this->valid_to->isPast()) {
            return false;
        }

        if ($this->max_redemptions && $this->current_redemptions >= $this->max_redemptions) {
            return false;
        }

        if ($this->outlet_scope_json && $this->outlet_scope_json !== ['all']) {
            // Check if customer has visited any of the allowed outlets
            $allowedOutlets = $this->outlet_scope_json;
            $customerOutlets = $customer->visitedOutlets()->pluck('outlets.id')->toArray();
            
            return count(array_intersect($allowedOutlets, $customerOutlets)) > 0;
        }

        return true;
    }

    public function canBeRedeemedBy(Customer $customer): bool
    {
        return $this->isValidForCustomer($customer) 
            && $customer->loyaltyWallet 
            && $customer->loyaltyWallet->canRedeem($this->required_points);
    }

    public function incrementRedemptions(): self
    {
        $this->current_redemptions++;
        $this->save();
        return $this;
    }
}

