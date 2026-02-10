<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyPointLedger extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'loyalty_point_ledger';

    protected $fillable = [
        'customer_id',
        'outlet_id',
        'visit_id',
        'source_type',
        'source_id',
        'points',
        'description',
        'created_by_user_id',
        'meta',
    ];

    protected $casts = [
        'points' => 'integer',
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

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('source_type', $type);
    }

    public function scopeEarned($query)
    {
        return $query->where('points', '>', 0);
    }

    public function scopeRedeemedOrExpired($query)
    {
        return $query->where('points', '<', 0);
    }
}

