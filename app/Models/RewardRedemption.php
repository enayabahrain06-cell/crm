<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RewardRedemption extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reward_id',
        'customer_id',
        'outlet_id',
        'points_redeemed',
        'redemption_code',
        'status',
        'redeemed_by_user_id',
        'redeemed_at',
        'expires_at',
        'notes',
        'meta',
    ];

    protected $casts = [
        'points_redeemed' => 'integer',
        'redeemed_at' => 'datetime',
        'expires_at' => 'datetime',
        'meta' => 'array',
    ];

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function redeemedBy()
    {
        return $this->belongsTo(User::class, 'redeemed_by_user_id');
    }

    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeValid($query)
    {
        return $query->whereIn('status', ['pending', 'completed'])
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return in_array($this->status, ['pending', 'completed']) 
            && (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function generateRedemptionCode(): string
    {
        return 'RDM-' . strtoupper(bin2hex(random_bytes(6)));
    }

    public function markAsCompleted(?int $userId = null): self
    {
        $this->status = 'completed';
        $this->redeemed_at = now();
        $this->redeemed_by_user_id = $userId;
        $this->save();
        return $this;
    }

    public function markAsCancelled(string $reason = ''): self
    {
        $this->status = 'cancelled';
        $this->notes = $reason;
        $this->save();
        return $this;
    }

    public function markAsExpired(): self
    {
        $this->status = 'expired';
        $this->save();
        return $this;
    }
}

