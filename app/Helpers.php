<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;

/**
 * Global Helper Functions for Hospitality CRM
 */

// Simple settings storage (in-memory cache)
if (!isset($GLOBALS['app_settings'])) {
    $GLOBALS['app_settings'] = [];
}

/**
 * Get or set an application setting
 * 
 * @param string|null $key
 * @param mixed $default
 * @return mixed
 */
if (!function_exists('setting')) {
    function setting(?string $key = null, mixed $default = null): mixed
    {
        // Load settings from database on first call
        if (!isset($GLOBALS['app_settings_loaded'])) {
            try {
                $settings = \App\Models\Setting::all();
                foreach ($settings as $setting) {
                    $GLOBALS['app_settings'][$setting->key] = $setting->value;
                }
                $GLOBALS['app_settings_loaded'] = true;
            } catch (\Exception $e) {
                // Table might not exist yet
                $GLOBALS['app_settings_loaded'] = true;
            }
        }
        
        if ($key === null) {
            return new class($GLOBALS['app_settings']) {
                private $settings;
                
                public function __construct($settings) {
                    $this->settings = $settings;
                }
                
                public function get($key, $default = null) {
                    return $this->settings[$key] ?? $default;
                }
                
                public function set($key, $value) {
                    $this->settings[$key] = $value;
                    $GLOBALS['app_settings'] = $this->settings;
                    return $this;
                }
                
                public function save() {
                    // Save to database
                    foreach ($this->settings as $key => $value) {
                        try {
                            \App\Models\Setting::updateOrCreate(
                                ['key' => $key],
                                ['value' => $value]
                            );
                        } catch (\Exception $e) {
                            // Table might not exist, silently fail
                        }
                    }
                    return true;
                }
                
                public function all() {
                    return $this->settings;
                }
            };
        }
        
        return $GLOBALS['app_settings'][$key] ?? $default;
    }
}

// Get country name from ISO code
if (!function_exists('getCountryName')) {
    function getCountryName(?string $iso2): string
    {
        if (!$iso2) return 'Unknown';
        
        $countries = config('hospitality.nationalities', []);
        return $countries[$iso2] ?? $iso2;
    }
}

// Get country flag emoji
if (!function_exists('getCountryFlag')) {
    function getCountryFlag(?string $iso2): string
    {
        if (!$iso2) return '';
        
        $flags = [
            'BH' => '🇧🇭', 'SA' => '🇸🇦', 'AE' => '🇦🇪', 'KW' => '🇰🇼', 'QA' => '🇶🇦',
            'OM' => '🇴🇲', 'EG' => '🇪🇬', 'JO' => '🇯🇴', 'LB' => '🇱🇧', 'SY' => '🇸🇾',
            'IN' => '🇮🇳', 'PK' => '🇵🇰', 'BD' => '🇧🇩', 'PH' => '🇵🇭', 'US' => '🇺🇸',
            'GB' => '🇬🇧', 'DE' => '🇩🇪', 'FR' => '🇫🇷',
        ];
        
        return $flags[$iso2] ?? '';
    }
}

// Format mobile number from JSON
if (!function_exists('formatMobileNumber')) {
    function formatMobileNumber(?array $mobileJson): string
    {
        if (!$mobileJson) return 'N/A';
        
        $dialCode = $mobileJson['country_dial_code'] ?? '';
        $number = $mobileJson['national_number'] ?? '';
        
        return "{$dialCode} {$number}";
    }
}

// Get zodiac sign from date of birth
if (!function_exists('getZodiacSign')) {
    function getZodiacSign(?string $dob): ?string
    {
        if (!$dob) return null;
        
        $month = date('m', strtotime($dob));
        $day = date('d', strtotime($dob));
        
        if (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18)) return 'Aquarius';
        if (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20)) return 'Pisces';
        if (($month == 3 && $day >= 21) || ($month == 4 && $day <= 19)) return 'Aries';
        if (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20)) return 'Taurus';
        if (($month == 5 && $day >= 21) || ($month == 6 && $day <= 20)) return 'Gemini';
        if (($month == 6 && $day >= 21) || ($month == 7 && $day <= 22)) return 'Cancer';
        if (($month == 7 && $day >= 23) || ($month == 8 && $day <= 22)) return 'Leo';
        if (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22)) return 'Virgo';
        if (($month == 9 && $day >= 23) || ($month == 10 && $day <= 22)) return 'Libra';
        if (($month == 10 && $day >= 23) || ($month == 11 && $day <= 21)) return 'Scorpio';
        if (($month == 11 && $day >= 22) || ($month == 12 && $day <= 21)) return 'Sagittarius';
        if (($month == 12 && $day >= 22) || ($month == 1 && $day <= 19)) return 'Capricorn';
        
        return null;
    }
}

// Get age group from date of birth
if (!function_exists('getAgeGroup')) {
    function getAgeGroup(?string $dob): ?string
    {
        if (!$dob) return null;
        
        $age = date('Y') - date('Y', strtotime($dob));
        
        if ($age <= 3) return 'toddler';
        if ($age <= 12) return 'child';
        if ($age <= 25) return 'youth';
        if ($age <= 59) return 'adult';
        return 'senior';
    }
}

// Calculate age from date of birth
if (!function_exists('calculateAge')) {
    function calculateAge(?string $dob): ?int
    {
        if (!$dob) return null;
        
        return date('Y') - date('Y', strtotime($dob));
    }
}

// Normalize phone number to E.164 format
if (!function_exists('normalizePhoneNumber')) {
    function normalizePhoneNumber(string $countryCode, string $nationalNumber): string
    {
        // Remove any non-digit characters
        $nationalNumber = preg_replace('/\D/', '', $nationalNumber);
        
        // Remove leading zero if present
        if (str_starts_with($nationalNumber, '0')) {
            $nationalNumber = substr($nationalNumber, 1);
        }
        
        $dialCodes = [
            'BH' => '973', 'SA' => '966', 'AE' => '971', 'KW' => '965', 'QA' => '974',
            'OM' => '968', 'EG' => '20', 'JO' => '962', 'LB' => '961', 'SY' => '963',
            'IN' => '91', 'PK' => '92', 'BD' => '880', 'PH' => '63', 'US' => '1',
            'GB' => '44', 'DE' => '49', 'FR' => '33',
        ];
        
        $dialCode = $dialCodes[$countryCode] ?? $countryCode;
        
        return '+' . $dialCode . $nationalNumber;
    }
}

// Generate QR code URL for outlet registration
if (!function_exists('getOutletRegistrationUrl')) {
    function getOutletRegistrationUrl(string $outletCode): string
    {
        return url("/register?outlet={$outletCode}");
    }
}

// Generate outlet links page URL
if (!function_exists('getOutletLinksUrl')) {
    function getOutletLinksUrl(string $outletCode): string
    {
        return url("/o/{$outletCode}/links");
    }
}

// Get status badge class
if (!function_exists('getStatusBadgeClass')) {
    function getStatusBadgeClass(string $status): string
    {
        return match($status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'blacklisted' => 'danger',
            'pending' => 'warning',
            'completed' => 'success',
            'sending' => 'info',
            'draft' => 'secondary',
            'failed' => 'danger',
            default => 'primary',
        };
    }
}

// Get visit type badge class
if (!function_exists('getVisitTypeBadgeClass')) {
    function getVisitTypeBadgeClass(string $type): string
    {
        return match($type) {
            'stay' => 'primary',
            'dine' => 'success',
            'bar' => 'warning',
            'event' => 'info',
            default => 'secondary',
        };
    }
}

// Format currency
if (!function_exists('formatCurrency')) {
    function formatCurrency(float $amount, string $currency = 'BHD'): string
    {
        return number_format($amount, 3) . ' ' . $currency;
    }
}

// Get user outlets for scope
if (!function_exists('getUserOutletIds')) {
    function getUserOutletIds(): array
    {
        if (auth()->check() && auth()->user()->hasRole('super_admin')) {
            return \App\Models\Outlet::where('active', true)->pluck('id')->toArray();
        }
        
        return auth()->user()?->outlets->where('active', true)->pluck('id')->toArray() ?? [];
    }
}

// Can access outlet
if (!function_exists('canAccessOutlet')) {
    function canAccessOutlet(int $outletId): bool
    {
        if (!auth()->check()) return false;
        
        if (auth()->user()->hasRole('super_admin')) return true;
        
        return in_array($outletId, getUserOutletIds());
    }
}
