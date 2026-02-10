<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerEvent extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'outlet_id',
        'event_type',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByOutlet($query, int $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public const EVENT_REGISTRATION = 'registration';
    public const EVENT_FIRST_VISIT = 'first_visit';
    public const EVENT_BIRTHDAY_VISIT = 'birthday_visit';
    public const EVENT_MILESTONE_VISIT = 'milestone_visit';
    public const EVENT_REWARD_REDEEMED = 'reward_redeemed';
    public const EVENT_CAMPAIGN_SENT = 'campaign_sent';
    public const EVENT_GREETING_SENT = 'greeting_sent';
}

