<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyWallet extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'total_points',
        'points_earned',
        'points_redeemed',
        'points_expired',
        'tier',
        'last_earned_at',
        'last_redeemed_at',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'points_earned' => 'integer',
        'points_redeemed' => 'integer',
        'points_expired' => 'integer',
        'last_earned_at' => 'datetime',
        'last_redeemed_at' => 'datetime',
    ];

    /**
     * Get the customer for this wallet
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get loyalty ledger entries
     */
    public function ledger()
    {
        return $this->hasMany(LoyaltyPointLedger::class);
    }

    /**
     * Add points to wallet
     */
    public function addPoints(int $points, string $description = '', array $meta = []): self
    {
        $this->total_points += $points;
        $this->points_earned += $points;
        $this->last_earned_at = now();
        $this->save();

        // Create ledger entry
        LoyaltyPointLedger::create([
            'customer_id' => $this->customer_id,
            'points' => $points,
            'source_type' => 'visit',
            'description' => $description,
            'meta' => $meta,
        ]);

        return $this;
    }

    /**
     * Redeem points from wallet
     */
    public function redeemPoints(int $points, string $description = '', array $meta = []): self
    {
        if ($points > $this->total_points) {
            throw new \Exception('Insufficient points for redemption');
        }

        $this->total_points -= $points;
        $this->points_redeemed += $points;
        $this->last_redeemed_at = now();
        $this->save();

        // Create ledger entry
        LoyaltyPointLedger::create([
            'customer_id' => $this->customer_id,
            'points' => -$points,
            'source_type' => 'reward_redemption',
            'description' => $description,
            'meta' => $meta,
        ]);

        return $this;
    }

    /**
     * Expire points
     */
    public function expirePoints(int $points, string $description = '', array $meta = []): self
    {
        $this->total_points = max(0, $this->total_points - $points);
        $this->points_expired += $points;
        $this->save();

        // Create ledger entry
        LoyaltyPointLedger::create([
            'customer_id' => $this->customer_id,
            'points' => -$points,
            'source_type' => 'expiry',
            'description' => $description,
            'meta' => $meta,
        ]);

        return $this;
    }

    /**
     * Adjust points manually
     */
    public function adjustPoints(int $points, string $description = '', ?int $userId = null, array $meta = []): self
    {
        $this->total_points += $points;
        
        if ($points > 0) {
            $this->points_earned += $points;
            $this->last_earned_at = now();
        } else {
            $this->points_redeemed += abs($points);
            $this->last_redeemed_at = now();
        }
        
        $this->save();

        // Create ledger entry
        LoyaltyPointLedger::create([
            'customer_id' => $this->customer_id,
            'points' => $points,
            'source_type' => 'manual_adjustment',
            'description' => $description,
            'created_by_user_id' => $userId,
            'meta' => $meta,
        ]);

        return $this;
    }

    /**
     * Get tier based on points
     */
    public static function getTierForPoints(int $points): string
    {
        if ($points >= 10000) {
            return 'platinum';
        } elseif ($points >= 5000) {
            return 'gold';
        } elseif ($points >= 1000) {
            return 'silver';
        }
        
        return 'basic';
    }

    /**
     * Update tier based on current points
     */
    public function updateTier(): self
    {
        $this->tier = self::getTierForPoints($this->total_points);
        $this->save();
        
        return $this;
    }

    /**
     * Check if customer can redeem reward
     */
    public function canRedeem(int $requiredPoints): bool
    {
        return $this->total_points >= $requiredPoints;
    }
}

