<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'outlet_id',
        'visited_at',
        'visit_type',
        'bill_amount',
        'currency',
        'items_json',
        'staff_user_id',
        'notes',
        'meta',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'bill_amount' => 'decimal:3',
        'items_json' => 'array',
        'meta' => 'array',
    ];

    /**
     * Get the customer for this visit
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the outlet for this visit
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Get the staff who recorded this visit
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    /**
     * Get loyalty ledger entries for this visit
     */
    public function loyaltyLedger()
    {
        return $this->hasMany(LoyaltyPointLedger::class);
    }

    /**
     * Scope for outlet
     */
    public function scopeByOutlet($query, int $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    /**
     * Scope for customer
     */
    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope by visit type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('visit_type', $type);
    }

    /**
     * Scope by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('visited_at', [$startDate, $endDate]);
    }

    /**
     * Scope for today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', today());
    }

    /**
     * Check if this is a first visit for the customer at this outlet
     */
    public function isFirstVisitAtOutlet(): bool
    {
        return !static::where('customer_id', $this->customer_id)
            ->where('outlet_id', $this->outlet_id)
            ->where('id', '<', $this->id)
            ->exists();
    }

    /**
     * Get visit number for this customer at this outlet
     */
    public function getVisitNumberAttribute(): int
    {
        return static::where('customer_id', $this->customer_id)
            ->where('outlet_id', $this->outlet_id)
            ->where('id', '<=', $this->id)
            ->count();
    }

    /**
     * Check if it's a birthday visit
     */
    public function isBirthdayVisit(): bool
    {
        if (!$this->customer->date_of_birth) {
            return false;
        }
        
        return $this->customer->date_of_birth->format('m-d') === $this->visited_at->format('m-d');
    }

    /**
     * Calculate points earned for this visit
     */
    public function getCalculatedPointsAttribute(): int
    {
        return app(\App\Services\LoyaltyService::class)->calculatePointsForVisit($this);
    }
}

