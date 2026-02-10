<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LoyaltyWallet;
use App\Models\CustomerEvent;
use App\Models\AuditLog;
use App\Traits\HasPhoneNormalization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    use HasPhoneNormalization;

    /**
     * Create a new customer
     */
    public function createCustomer(array $data, ?int $userId = null, ?int $outletId = null): Customer
    {
        return DB::transaction(function () use ($data, $userId, $outletId) {
            // Normalize phone if provided
            if (isset($data['country_code']) && isset($data['mobile_number'])) {
                $normalized = self::normalizePhone($data['country_code'], $data['mobile_number']);
                if ($normalized) {
                    $data['mobile_json'] = $normalized;
                }
            }

            $customer = Customer::create([
                'type' => $data['type'] ?? 'individual',
                'name' => $data['name'],
                'nationality' => $data['nationality'] ?? null,
                'gender' => $data['gender'] ?? 'unknown',
                'email' => $data['email'] ?? null,
                'mobile_json' => $data['mobile_json'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address' => $data['address'] ?? null,
                'company_name' => $data['company_name'] ?? null,
                'position' => $data['position'] ?? null,
                'first_registration_outlet_id' => $outletId,
                'created_by_user_id' => $userId,
                'status' => $data['status'] ?? 'active',
            ]);

            // Create loyalty wallet
            LoyaltyWallet::create([
                'customer_id' => $customer->id,
                'total_points' => 0,
                'points_earned' => 0,
                'points_redeemed' => 0,
                'points_expired' => 0,
            ]);

            // Log registration event
            CustomerEvent::create([
                'customer_id' => $customer->id,
                'outlet_id' => $outletId,
                'event_type' => 'registration',
                'meta' => [
                    'registration_method' => $data['registration_method'] ?? 'manual',
                ],
            ]);

            AuditLog::log(
                $userId,
                'created',
                'Customer',
                $customer->id,
                null,
                $customer->toArray()
            );

            return $customer;
        });
    }

    /**
     * Update customer
     */
    public function updateCustomer(Customer $customer, array $data, ?int $userId = null): Customer
    {
        return DB::transaction(function () use ($customer, $data, $userId) {
            $oldValues = $customer->toArray();

            // Normalize phone if both country_code and mobile_number are provided
            if (isset($data['country_code']) && isset($data['mobile_number']) && !empty($data['country_code']) && !empty($data['mobile_number'])) {
                $normalized = self::normalizePhone($data['country_code'], $data['mobile_number']);
                if ($normalized) {
                    $data['mobile_json'] = $normalized;
                }
                // Remove the individual fields, we only store mobile_json
                unset($data['country_code'], $data['mobile_number']);
            } elseif (!isset($data['mobile_json'])) {
                // Don't overwrite mobile_json if phone fields aren't being updated
                unset($data['country_code'], $data['mobile_number']);
            }

            $customer->update($data);

            AuditLog::log(
                $userId,
                'updated',
                'Customer',
                $customer->id,
                $oldValues,
                $customer->fresh()->toArray()
            );

            return $customer;
        });
    }

    /**
     * Find or create customer based on identity (email or mobile)
     */
    public function findOrCreate(array $data, ?int $userId = null, ?int $outletId = null, bool $upsert = false): array
    {
        $email = $data['email'] ?? null;
        $mobileE164 = null;

        if (isset($data['country_code']) && isset($data['mobile_number'])) {
            $normalized = self::normalizePhone($data['country_code'], $data['mobile_number']);
            if ($normalized) {
                $mobileE164 = $normalized['e164'];
            }
        }

        // Check for existing customer
        $existingCustomer = Customer::findByIdentity($email, $mobileE164);

        if ($existingCustomer) {
            return [
                'customer' => $existingCustomer,
                'is_new' => false,
                'action' => 'found',
            ];
        }

        if (!$upsert) {
            return [
                'customer' => null,
                'is_new' => false,
                'action' => 'not_found',
            ];
        }

        // Create new customer
        $customer = $this->createCustomer($data, $userId, $outletId);

        return [
            'customer' => $customer,
            'is_new' => true,
            'action' => 'created',
        ];
    }

    /**
     * Upsert customer based on identity
     */
    public function upsertCustomer(array $data, ?int $userId = null, ?int $outletId = null): Customer
    {
        $email = $data['email'] ?? null;
        $mobileE164 = null;

        if (isset($data['country_code']) && isset($data['mobile_number'])) {
            $normalized = self::normalizePhone($data['country_code'], $data['mobile_number']);
            if ($normalized) {
                $mobileE164 = $normalized['e164'];
            }
        }

        $existingCustomer = Customer::findByIdentity($email, $mobileE164);

        if ($existingCustomer) {
            return $this->updateCustomer($existingCustomer, $data, $userId);
        }

        return $this->createCustomer($data, $userId, $outletId);
    }

    /**
     * Get customer 360 view data
     */
    public function getCustomer360(Customer $customer): array
    {
        $customer->load([
            'firstRegistrationOutlet',
            'creator',
            // 'tags', // Temporarily disabled due to database schema issue
            'loyaltyWallet',
            'visits' => function ($query) {
                $query->orderBy('visited_at', 'desc')->limit(50);
            },
            'visits.outlet',
            'loyaltyLedger' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(20);
            },
            'loyaltyLedger.outlet',
            'events' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'events.outlet',
            'rewardRedemptions' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'rewardRedemptions.reward',
        ]);

        // Calculate additional stats
        $totalSpend = $customer->visits->sum('bill_amount');
        $uniqueOutlets = $customer->visits->pluck('outlet_id')->unique()->count();
        $avgSpend = $customer->visits->count() > 0 
            ? $totalSpend / $customer->visits->count() 
            : 0;

        return [
            'profile' => $customer,
            'kpis' => [
                'total_visits' => $customer->visits->count(),
                'total_spend' => round($totalSpend, 3),
                'unique_outlets' => $uniqueOutlets,
                'current_points' => $customer->loyaltyWallet?->total_points ?? 0,
                'tier' => $customer->loyaltyWallet?->tier ?? 'basic',
                'avg_spend_per_visit' => round($avgSpend, 3),
            ],
            'visits' => $customer->visits,
            'loyalty' => [
                'wallet' => $customer->loyaltyWallet,
                'ledger' => $customer->loyaltyLedger,
            ],
            'events' => $customer->events,
            'tags' => $customer->tags ?? collect([]),
            'redemptions' => $customer->rewardRedemptions,
        ];
    }

    /**
     * Search customers with filters
     */
    public function search(array $filters, int $perPage = 20)
    {
        $query = Customer::with(['firstRegistrationOutlet', 'loyaltyWallet']);

        // Text search
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Type filter
        if (isset($filters['type']) && !empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        // Status filter
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Nationality filter
        if (isset($filters['nationality']) && !empty($filters['nationality'])) {
            $query->byNationality($filters['nationality']);
        }

        // Gender filter
        if (isset($filters['gender']) && !empty($filters['gender'])) {
            $query->byGender($filters['gender']);
        }

        // Age group filter
        if (isset($filters['age_group']) && !empty($filters['age_group'])) {
            $query->byAgeGroup($filters['age_group']);
        }

        // Outlet filter (visits)
        if (isset($filters['outlet_id']) && !empty($filters['outlet_id'])) {
            $query->byOutlet($filters['outlet_id']);
        }

        // Registration outlet filter
        if (isset($filters['registered_outlet_id']) && !empty($filters['registered_outlet_id'])) {
            $query->registeredAtOutlet($filters['registered_outlet_id']);
        }

        // Points range filter
        if (isset($filters['min_points'])) {
            $query->withPointsAbove($filters['min_points']);
        }

        if (isset($filters['max_points'])) {
            $query->withPointsBelow($filters['max_points']);
        }

        // Visits count filter
        if (isset($filters['min_visits'])) {
            $query->withMinimumVisits($filters['min_visits']);
        }

        // Zodiac filter
        if (isset($filters['zodiac']) && !empty($filters['zodiac'])) {
            $query->whereRaw('DATE_FORMAT(date_of_birth, "%m-%d") BETWEEN ? AND ?', 
                $this->getZodiacDateRange($filters['zodiac']));
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage);
    }

    /**
     * Get zodiac date range
     */
    protected function getZodiacDateRange(string $zodiac): array
    {
        $ranges = [
            'capricorn' => ['12-22', '01-19'],
            'aquarius' => ['01-20', '02-18'],
            'pisces' => ['02-19', '03-20'],
            'aries' => ['03-21', '04-19'],
            'taurus' => ['04-20', '05-20'],
            'gemini' => ['05-21', '06-20'],
            'cancer' => ['06-21', '07-22'],
            'leo' => ['07-23', '08-22'],
            'virgo' => ['08-23', '09-22'],
            'libra' => ['09-23', '10-22'],
            'scorpio' => ['10-23', '11-21'],
            'sagittarius' => ['11-22', '12-21'],
        ];

        return $ranges[strtolower($zodiac)] ?? ['01-01', '12-31'];
    }

    /**
     * Get customer statistics for dashboard
     */
    public function getStats(array $filters = []): array
    {
        $query = Customer::query();

        if (isset($filters['outlet_id'])) {
            $query->whereHas('visits', function ($q) use ($filters) {
                $q->where('outlet_id', $filters['outlet_id']);
            });
        }

        if (isset($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return [
            'total' => $query->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'new_this_month' => (clone $query)->whereMonth('created_at', now()->month)->count(),
            'new_today' => (clone $query)->whereDate('created_at', today())->count(),
        ];
    }
}

