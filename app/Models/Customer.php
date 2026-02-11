<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasPhoneNormalization;
use App\Traits\HasCustomerDemographics;

class Customer extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes, HasPhoneNormalization, HasCustomerDemographics;

    protected $fillable = [
        'type',
        'name',
        'nationality',
        'gender',
        'email',
        'mobile_json',
        'mobile_e164',
        'date_of_birth',
        'address',
        'company_name',
        'position',
        'first_registration_outlet_id',
        'first_visit_at',
        'created_by_user_id',
        'status',
        'preferences',
        'meta',
    ];

    protected $hidden = [];

    protected $casts = [
        'type' => 'string',
        'gender' => 'string',
        'mobile_json' => 'array',
        'date_of_birth' => 'date',
        'status' => 'string',
        'preferences' => 'array',
        'meta' => 'array',
    ];

    /**
     * The attributes that should be appended.
     */
    protected $appends = [
        'age',
        'age_group',
        'zodiac',
        'formatted_mobile',
        'is_birthday_today',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate mobile_e164 when mobile_json is set
        static::saving(function ($customer) {
            if (isset($customer->mobile_json) && is_array($customer->mobile_json)) {
                if (isset($customer->mobile_json['e164'])) {
                    $customer->mobile_e164 = $customer->mobile_json['e164'];
                }
            }
        });
    }

    /**
     * Get the outlet where customer first registered
     */
    public function firstRegistrationOutlet()
    {
        return $this->belongsTo(Outlet::class, 'first_registration_outlet_id');
    }

    /**
     * Get the user who created this customer
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get all visits for this customer
     */
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Get loyalty wallet
     */
    public function loyaltyWallet()
    {
        return $this->hasOne(LoyaltyWallet::class);
    }

    /**
     * Get loyalty ledger entries
     */
    public function loyaltyLedger()
    {
        return $this->hasMany(LoyaltyPointLedger::class);
    }

    /**
     * Get customer tags
     */
    public function tags()
    {
        return $this->belongsToMany(CustomerTag::class, 'customer_tag_pivot', 'customer_id', 'tag_id')
            ->withPivot(['tagged_by'])
            ->withTimestamps();
    }

    /**
     * Get customer events
     */
    public function events()
    {
        return $this->hasMany(CustomerEvent::class);
    }

    /**
     * Get campaign messages
     */
    public function campaignMessages()
    {
        return $this->hasMany(CampaignMessage::class);
    }

    /**
     * Get auto greeting logs
     */
    public function autoGreetingLogs()
    {
        return $this->hasMany(AutoGreetingLog::class);
    }

    /**
     * Get reward redemptions
     */
    public function rewardRedemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    /**
     * Get outlets visited
     */
    public function visitedOutlets()
    {
        return $this->belongsToMany(Outlet::class, 'visits')
            ->distinct();
    }

    /**
     * Accessors
     */
    public function getAgeAttribute(): ?int
    {
        return self::calculateAge($this->date_of_birth?->format('Y-m-d'));
    }

    public function getAgeGroupAttribute(): string
    {
        return self::getAgeGroup($this->age);
    }

    public function getZodiacAttribute(): ?string
    {
        return self::getZodiac($this->date_of_birth?->format('Y-m-d'));
    }

    public function getFormattedMobileAttribute(): string
    {
        return self::formatPhoneForDisplay($this->mobile_json ?? []);
    }

    public function getIsBirthdayTodayAttribute(): bool
    {
        return self::isBirthdayToday($this->date_of_birth?->format('Y-m-d'));
    }

    /**
     * Get mobile_number from mobile_json
     */
    public function getMobileNumberAttribute(): ?string
    {
        return $this->mobile_json['national_number'] ?? null;
    }

    /**
     * Get country_code from mobile_json
     */
    public function getCountryCodeAttribute(): ?string
    {
        return $this->mobile_json['country_iso2'] ?? null;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByNationality($query, string $nationality)
    {
        return $query->where('nationality', $nationality);
    }

    public function scopeByGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeByAgeGroup($query, string $ageGroup)
    {
        $range = self::getAgeGroupRange($ageGroup);
        // SQLite-compatible age calculation using strftime
        $ageCalculation = "CAST(strftime('%Y', 'now') AS INTEGER) - CAST(strftime('%Y', date_of_birth) AS INTEGER) - (CASE WHEN strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth) THEN 1 ELSE 0 END)";
        return $query->whereRaw("{$ageCalculation} BETWEEN ? AND ?", $range);
    }

    public function scopeByOutlet($query, int $outletId)
    {
        return $query->whereHas('visits', function ($q) use ($outletId) {
            $q->where('outlet_id', $outletId);
        });
    }

    public function scopeRegisteredAtOutlet($query, int $outletId)
    {
        return $query->where('first_registration_outlet_id', $outletId);
    }

    public function scopeWithPointsAbove($query, int $points)
    {
        return $query->whereHas('loyaltyWallet', function ($q) use ($points) {
            $q->where('total_points', '>=', $points);
        });
    }

    public function scopeWithPointsBelow($query, int $points)
    {
        return $query->whereHas('loyaltyWallet', function ($q) use ($points) {
            $q->where('total_points', '<', $points);
        });
    }

    public function scopeWithMinimumVisits($query, int $count)
    {
        return $query->whereHas('visits', function ($q) use ($count) {
            $q->select('customer_id')
                ->groupBy('customer_id')
                ->havingRaw('COUNT(*) >= ?', [$count]);
        });
    }

    public function scopeWithMinimumOutletsVisited($query, int $count)
    {
        return $query->whereHas('visitedOutlets', function ($q) use ($count) {
            $q->select('outlet_id')
                ->distinct()
                ->from('visits')
                ->whereColumn('visits.customer_id', 'customers.id')
                ->groupBy('outlet_id')
                ->havingRaw('COUNT(DISTINCT outlet_id) >= ?', [$count]);
        });
    }

    /**
     * Helper to get age range for age group
     */
    private static function getAgeGroupRange(string $ageGroup): array
    {
        return match ($ageGroup) {
            'toddler' => [0, 3],
            'child' => [4, 12],
            'youth' => [13, 25],
            'adult' => [26, 59],
            'senior' => [60, 150],
            default => [0, 150],
        };
    }

    /**
     * Search scope - Fixed to properly handle mobile JSON searches
     * Compatible with both SQLite and MySQL databases
     */
    public function scopeSearch($query, string $search)
    {
        // Check if search looks like an email (contains @)
        $looksLikeEmail = str_contains($search, '@');
        
        // Check if search is primarily a phone number (starts with + or all digits)
        // Don't apply phone search logic for email-like searches
        $isPhoneSearch = !$looksLikeEmail && (
            str_starts_with($search, '+') || 
            preg_match('/^[0-9\s\-\(\)]+$/', $search) === 1
        );
        
        // Clean search term - remove common formatting characters (spaces, dashes, parentheses)
        $cleanSearch = $isPhoneSearch ? preg_replace('/[^0-9]/', '', $search) : '';
        
        return $query->where(function ($q) use ($search, $cleanSearch, $looksLikeEmail, $isPhoneSearch) {
            // Search by name (partial match, case-insensitive)
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%" . strtolower($search) . "%")
              ->orWhere('name', 'like', "%" . ucfirst(strtolower($search)) . "%");
            
            // Search by email - use exact match if it looks like an email, otherwise partial
            if ($looksLikeEmail) {
                // For email searches, try exact match first, then partial
                $q->orWhere('email', $search)  // Exact match
                  ->orWhere('email', 'like', "%{$search}%");  // Partial match as fallback
            } else {
                $q->orWhere('email', 'like', "%{$search}%");
            }
            
            // Search by company name
            $q->orWhere('company_name', 'like', "%{$search}%");
            
            // Only search phone fields if it looks like a phone number
            if ($isPhoneSearch && !empty($cleanSearch) && strlen($cleanSearch) >= 3) {
                // Search in mobile_e164 (clean format like +97312345678)
                $q->orWhere('mobile_e164', 'like', "%{$cleanSearch}%");
                
                // Also try with leading + if not present
                if (strpos($cleanSearch, '+') !== 0) {
                    $q->orWhere('mobile_e164', 'like', "%+{$cleanSearch}%");
                }
                
                // Search in mobile_json national_number field using JSON query
                // This handles the mobile_json array structure: {"national_number": "12345678", "e164": "+97312345678", ...}
                $q->orWhere(function($innerQ) use ($cleanSearch) {
                    $innerQ->whereNotNull('mobile_json')
                           ->where('mobile_json', '!=', '')
                           ->where(function($subQ) use ($cleanSearch) {
                               // Search in national_number field of mobile_json
                               $subQ->whereRaw("json_extract(mobile_json, '$.national_number') LIKE ?", ["%{$cleanSearch}%"])
                                    ->orWhereRaw("json_extract(mobile_json, '$.national_number') LIKE ?", ["{$cleanSearch}%"])
                                    ->orWhereRaw("json_extract(mobile_json, '$.national_number') LIKE ?", ["%{$cleanSearch}"]);
                           });
                });
                
                // Also try searching the entire mobile_json as a string (for SQLite compatibility)
                $q->orWhere('mobile_json', 'like', "%{$cleanSearch}%");
            }
        });
    }

    /**
     * Find customer by email or mobile E.164
     */
    public static function findByIdentity(?string $email = null, ?string $mobileE164 = null)
    {
        if (!$email && !$mobileE164) {
            return null;
        }

        return static::where(function ($q) use ($email, $mobileE164) {
            if ($email) {
                $q->orWhere('email', $email);
            }
            if ($mobileE164) {
                $q->orWhere('mobile_e164', $mobileE164);
            }
        })->first();
    }
}

