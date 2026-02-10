<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Visit;
use App\Models\LoyaltyWallet;
use App\Models\LoyaltyPointLedger;
use App\Models\LoyaltyRule;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\CustomerEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoyaltyService
{
    /**
     * Calculate and award points for a visit
     */
    public function processVisitPoints(Visit $visit): int
    {
        $customer = $visit->customer;
        $pointsAwarded = 0;

        // Get active earn rules ordered by priority
        $rules = LoyaltyRule::active()
            ->byType('earn')
            ->ordered()
            ->get();

        foreach ($rules as $rule) {
            try {
                if ($rule->matchesConditions($visit, $customer)) {
                    $points = $rule->calculatePoints($visit);
                    
                    if ($points > 0) {
                        $this->awardPoints($customer, $points, $visit->outlet, $visit, 'visit', [
                            'rule_id' => $rule->id,
                            'rule_name' => $rule->name,
                            'visit_id' => $visit->id,
                        ]);
                        
                        $pointsAwarded += $points;
                        
                        // Log the event
                        $this->logCustomerEvent($customer, $visit->outlet, 'points_earned', [
                            'points' => $points,
                            'rule_id' => $rule->id,
                            'total_points' => $customer->loyaltyWallet->total_points,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error processing loyalty rule {$rule->id} for visit {$visit->id}: " . $e->getMessage());
                continue;
            }
        }

        // Update wallet tier if applicable
        if ($customer->loyaltyWallet) {
            $customer->loyaltyWallet->updateTier();
        }

        return $pointsAwarded;
    }

    /**
     * Award points to a customer
     */
    public function awardPoints(Customer $customer, int $points, $outlet = null, $source = null, string $sourceType = 'manual_adjustment', array $meta = []): LoyaltyPointLedger
    {
        return DB::transaction(function () use ($customer, $points, $outlet, $source, $sourceType, $meta) {
            // Get or create wallet
            $wallet = $this->getOrCreateWallet($customer);
            
            // Update wallet
            $wallet->total_points += $points;
            $wallet->points_earned += $points;
            $wallet->last_earned_at = now();
            $wallet->save();
            
            // Create ledger entry
            $ledger = LoyaltyPointLedger::create([
                'customer_id' => $customer->id,
                'outlet_id' => $outlet?->id,
                'visit_id' => $source?->id,
                'source_type' => $sourceType,
                'source_id' => $source?->id,
                'points' => $points,
                'description' => $meta['rule_name'] ?? 'Points awarded',
                'meta' => $meta,
            ]);
            
            return $ledger;
        });
    }

    /**
     * Redeem points for a reward
     */
    public function redeemReward(Customer $customer, Reward $reward, $outlet = null, ?int $userId = null): RewardRedemption
    {
        return DB::transaction(function () use ($customer, $reward, $outlet, $userId) {
            $wallet = $customer->loyaltyWallet;
            
            if (!$wallet || !$wallet->canRedeem($reward->required_points)) {
                throw new \Exception('Insufficient points for redemption');
            }
            
            // Create redemption record
            $redemption = RewardRedemption::create([
                'reward_id' => $reward->id,
                'customer_id' => $customer->id,
                'outlet_id' => $outlet?->id,
                'points_redeemed' => $reward->required_points,
                'redemption_code' => RewardRedemption::generateRedemptionCode(),
                'status' => 'pending',
                'expires_at' => now()->addMonths(3),
                'redeemed_by_user_id' => $userId,
            ]);
            
            // Deduct points from wallet
            $wallet->total_points -= $reward->required_points;
            $wallet->points_redeemed += $reward->required_points;
            $wallet->last_redeemed_at = now();
            $wallet->save();
            
            // Create ledger entry
            LoyaltyPointLedger::create([
                'customer_id' => $customer->id,
                'outlet_id' => $outlet?->id,
                'source_type' => 'reward_redemption',
                'source_id' => $redemption->id,
                'points' => -$reward->required_points,
                'description' => "Redeemed: {$reward->name}",
                'created_by_user_id' => $userId,
                'meta' => [
                    'redemption_id' => $redemption->id,
                    'reward_id' => $reward->id,
                ],
            ]);
            
            // Update reward redemption count
            $reward->incrementRedemptions();
            
            // Log event
            $this->logCustomerEvent($customer, $outlet, 'reward_redeemed', [
                'reward_id' => $reward->id,
                'reward_name' => $reward->name,
                'points_redeemed' => $reward->required_points,
                'redemption_code' => $redemption->redemption_code,
            ]);
            
            return $redemption;
        });
    }

    /**
     * Adjust points manually
     */
    public function adjustPoints(Customer $customer, int $points, string $description, ?int $userId = null, $outlet = null, array $meta = []): LoyaltyPointLedger
    {
        return DB::transaction(function () use ($customer, $points, $description, $userId, $outlet, $meta) {
            $wallet = $this->getOrCreateWallet($customer);
            
            $wallet->total_points += $points;
            
            if ($points > 0) {
                $wallet->points_earned += $points;
                $wallet->last_earned_at = now();
            } else {
                $wallet->points_redeemed += abs($points);
                $wallet->last_redeemed_at = now();
            }
            
            $wallet->save();
            
            return LoyaltyPointLedger::create([
                'customer_id' => $customer->id,
                'outlet_id' => $outlet?->id,
                'source_type' => 'manual_adjustment',
                'points' => $points,
                'description' => $description,
                'created_by_user_id' => $userId,
                'meta' => $meta,
            ]);
        });
    }

    /**
     * Calculate points for a visit (without awarding)
     */
    public function calculatePointsForVisit(Visit $visit): int
    {
        $customer = $visit->customer;
        $totalPoints = 0;
        
        $rules = LoyaltyRule::active()
            ->byType('earn')
            ->ordered()
            ->get();
        
        foreach ($rules as $rule) {
            if ($rule->matchesConditions($visit, $customer)) {
                $totalPoints += $rule->calculatePoints($visit);
            }
        }
        
        return $totalPoints;
    }

    /**
     * Get or create loyalty wallet
     */
    public function getOrCreateWallet(Customer $customer): LoyaltyWallet
    {
        return $customer->loyaltyWallet ?? LoyaltyWallet::create([
            'customer_id' => $customer->id,
            'total_points' => 0,
            'points_earned' => 0,
            'points_redeemed' => 0,
            'points_expired' => 0,
        ]);
    }

    /**
     * Log customer event
     */
    protected function logCustomerEvent(Customer $customer, $outlet, string $eventType, array $meta = []): void
    {
        CustomerEvent::create([
            'customer_id' => $customer->id,
            'outlet_id' => $outlet?->id,
            'event_type' => $eventType,
            'meta' => $meta,
        ]);
    }

    /**
     * Get available rewards for customer
     */
    public function getAvailableRewardsForCustomer(Customer $customer)
    {
        return Reward::available()
            ->where('required_points', '<=', $customer->loyaltyWallet?->total_points ?? 0)
            ->orderBy('required_points', 'asc')
            ->get();
    }

    /**
     * Get customer's points summary
     */
    public function getPointsSummary(Customer $customer): array
    {
        $wallet = $customer->loyaltyWallet;
        
        if (!$wallet) {
            return [
                'total_points' => 0,
                'points_earned' => 0,
                'points_redeemed' => 0,
                'tier' => 'basic',
                'recent_transactions' => [],
            ];
        }
        
        return [
            'total_points' => $wallet->total_points,
            'points_earned' => $wallet->points_earned,
            'points_redeemed' => $wallet->points_redeemed,
            'tier' => $wallet->tier,
            'next_tier' => $this->getNextTier($wallet->total_points),
            'points_to_next_tier' => $this->getPointsToNextTier($wallet->total_points),
            'recent_transactions' => $customer->loyaltyLedger()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Get next tier name
     */
    protected function getNextTier(int $currentPoints): ?string
    {
        if ($currentPoints < 1000) {
            return 'silver';
        } elseif ($currentPoints < 5000) {
            return 'gold';
        } elseif ($currentPoints < 10000) {
            return 'platinum';
        }
        return null;
    }

    /**
     * Get points needed for next tier
     */
    protected function getPointsToNextTier(int $currentPoints): int
    {
        if ($currentPoints < 1000) {
            return 1000 - $currentPoints;
        } elseif ($currentPoints < 5000) {
            return 5000 - $currentPoints;
        } elseif ($currentPoints < 10000) {
            return 10000 - $currentPoints;
        }
        return 0;
    }
}

