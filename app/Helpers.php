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
        if (!$iso2 || strlen($iso2) !== 2) return '';
        
        // Predefined flags for common countries (faster lookup)
        $flags = [
            'BH' => '🇧🇭', 'SA' => '🇸🇦', 'AE' => '🇦🇪', 'KW' => '🇰🇼', 'QA' => '🇶🇦',
            'OM' => '🇴🇲', 'EG' => '🇪🇬', 'JO' => '🇯🇴', 'LB' => '🇱🇧', 'SY' => '🇸🇾',
            'IN' => '🇮🇳', 'PK' => '🇵🇰', 'BD' => '🇧🇩', 'PH' => '🇵🇭', 'US' => '🇺🇸',
            'GB' => '🇬🇧', 'DE' => '🇩🇪', 'FR' => '🇫🇷',
        ];
        
        // Return predefined flag if available
        if (isset($flags[$iso2])) {
            return $flags[$iso2];
        }
        
        // Dynamically generate flag from ISO2 code using Unicode regional indicators
        // This works for any 2-letter ISO2 code
        $firstChar = ord(strtoupper($iso2[0])) - ord('A') + 0x1F1E6;
        $secondChar = ord(strtoupper($iso2[1])) - ord('A') + 0x1F1E6;
        
        return json_decode('"' . $firstChar . $secondChar . '"');
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

// ============================================
// Countries Helper Functions (wrapping Countries class)
// ============================================

/**
 * Get a specific country by ISO2 code
 */
if (!function_exists('country')) {
    function country(string $iso2): ?array
    {
        return \App\Data\Countries::get($iso2);
    }
}

/**
 * Get all countries as a dropdown-friendly list (flag + name)
 */
if (!function_exists('countries')) {
    function countries(): array
    {
        return \App\Data\Countries::list();
    }
}

/**
 * Get all countries indexed by ISO2 code
 */
if (!function_exists('countriesAll')) {
    function countriesAll(): array
    {
        return \App\Data\Countries::getCountries();
    }
}

/**
 * Get country call code
 */
if (!function_exists('countryCallCode')) {
    function countryCallCode(string $iso2): string
    {
        return \App\Data\Countries::callCode($iso2);
    }
}

/**
 * Get country currency code
 */
if (!function_exists('countryCurrency')) {
    function countryCurrency(string $iso2): string
    {
        return \App\Data\Countries::currency($iso2);
    }
}

/**
 * Get country timezone
 */
if (!function_exists('countryTimezone')) {
    function countryTimezone(string $iso2): string
    {
        return \App\Data\Countries::timezone($iso2);
    }
}

/**
 * Get country name (alias for getCountryName for consistency)
 */
if (!function_exists('countryName')) {
    function countryName(string $iso2): string
    {
        return \App\Data\Countries::name($iso2);
    }
}

/**
 * Get country flag emoji
 */
if (!function_exists('countryFlag')) {
    function countryFlag(string $iso2): string
    {
        return \App\Data\Countries::flag($iso2);
    }
}

/**
 * Get countries indexed by name (for name-based lookups)
 */
if (!function_exists('countriesByName')) {
    function countriesByName(): array
    {
        return \App\Data\Countries::byName();
    }
}

/**
 * Find country ISO2 code by name
 */
if (!function_exists('countryCodeByName')) {
    function countryCodeByName(string $name): ?string
    {
        $countries = \App\Data\Countries::byName();
        return $countries[$name]['iso2'] ?? null;
    }
}

// ============================================
// Nationalities Helper Functions
// ============================================

/**
 * Get nationality demonym from ISO2 country code
 * e.g., "BH" -> "Bahraini", "SA" -> "Saudi", "AE" -> "Emirati"
 */
if (!function_exists('nationality')) {
    function nationality(string $iso2): string
    {
        $demonyms = [
            'AF' => 'Afghan',
            'AL' => 'Albanian',
            'DZ' => 'Algerian',
            'AD' => 'Andorran',
            'AO' => 'Angolan',
            'AG' => 'Antiguan',
            'AR' => 'Argentine',
            'AM' => 'Armenian',
            'AU' => 'Australian',
            'AT' => 'Austrian',
            'AZ' => 'Azerbaijani',
            'BS' => 'Bahamian',
            'BH' => 'Bahraini',
            'BD' => 'Bangladeshi',
            'BB' => 'Barbadian',
            'BY' => 'Belarusian',
            'BE' => 'Belgian',
            'BZ' => 'Belizean',
            'BJ' => 'Beninese',
            'BT' => 'Bhutanese',
            'BO' => 'Bolivian',
            'BA' => 'Bosnian',
            'BW' => 'Botswanan',
            'BR' => 'Brazilian',
            'BN' => 'Bruneian',
            'BG' => 'Bulgarian',
            'BF' => 'Burkinabe',
            'BI' => 'Burundian',
            'CV' => 'Cape Verdean',
            'KH' => 'Cambodian',
            'CM' => 'Cameroonian',
            'CA' => 'Canadian',
            'CF' => 'Central African',
            'TD' => 'Chadian',
            'CL' => 'Chilean',
            'CN' => 'Chinese',
            'CO' => 'Colombian',
            'KM' => 'Comorian',
            'CG' => 'Congolese',
            'CR' => 'Costa Rican',
            'HR' => 'Croatian',
            'CU' => 'Cuban',
            'CY' => 'Cypriot',
            'CZ' => 'Czech',
            'CD' => 'Congolese',
            'DK' => 'Danish',
            'DJ' => 'Djiboutian',
            'DM' => 'Dominican',
            'DO' => 'Dominican',
            'EC' => 'Ecuadorian',
            'EG' => 'Egyptian',
            'SV' => 'Salvadoran',
            'GQ' => 'Equatorial Guinean',
            'ER' => 'Eritrean',
            'EE' => 'Estonian',
            'SZ' => 'Eswatini',
            'ET' => 'Ethiopian',
            'FJ' => 'Fijian',
            'FI' => 'Finnish',
            'FR' => 'French',
            'GA' => 'Gabonese',
            'GM' => 'Gambian',
            'GE' => 'Georgian',
            'DE' => 'German',
            'GH' => 'Ghanaian',
            'GR' => 'Greek',
            'GD' => 'Grenadian',
            'GT' => 'Guatemalan',
            'GN' => 'Guinean',
            'GW' => 'Guinea-Bissauan',
            'GY' => 'Guyanese',
            'HT' => 'Haitian',
            'HN' => 'Honduran',
            'HU' => 'Hungarian',
            'IS' => 'Icelandic',
            'IN' => 'Indian',
            'ID' => 'Indonesian',
            'IR' => 'Iranian',
            'IQ' => 'Iraqi',
            'IE' => 'Irish',
            'IL' => 'Israeli',
            'IT' => 'Italian',
            'JM' => 'Jamaican',
            'JP' => 'Japanese',
            'JO' => 'Jordanian',
            'KZ' => 'Kazakhstani',
            'KE' => 'Kenyan',
            'KI' => 'I-Kiribati',
            'KW' => 'Kuwaiti',
            'KG' => 'Kyrgyz',
            'LA' => 'Lao',
            'LV' => 'Latvian',
            'LB' => 'Lebanese',
            'LS' => 'Lesotho',
            'LR' => 'Liberian',
            'LY' => 'Libyan',
            'LI' => 'Liechtensteiner',
            'LT' => 'Lithuanian',
            'LU' => 'Luxembourgish',
            'MG' => 'Malagasy',
            'MW' => 'Malawian',
            'MY' => 'Malaysian',
            'MV' => 'Maldivian',
            'ML' => 'Malian',
            'MT' => 'Maltese',
            'MH' => 'Marshallese',
            'MR' => 'Mauritanian',
            'MU' => 'Mauritian',
            'MX' => 'Mexican',
            'FM' => 'Micronesian',
            'MD' => 'Moldovan',
            'MC' => 'Monacan',
            'MN' => 'Mongolian',
            'ME' => 'Montenegrin',
            'MA' => 'Moroccan',
            'MZ' => 'Mozambican',
            'MM' => 'Burmese',
            'NA' => 'Namibian',
            'NR' => 'Nauruan',
            'NP' => 'Nepalese',
            'NL' => 'Dutch',
            'NZ' => 'New Zealander',
            'NI' => 'Nicaraguan',
            'NE' => 'Nigerien',
            'NG' => 'Nigerian',
            'KP' => 'North Korean',
            'MK' => 'Macedonian',
            'NO' => 'Norwegian',
            'OM' => 'Omani',
            'PK' => 'Pakistani',
            'PW' => 'Palauan',
            'PS' => 'Palestinian',
            'PA' => 'Panamanian',
            'PG' => 'Papua New Guinean',
            'PY' => 'Paraguayan',
            'PE' => 'Peruvian',
            'PH' => 'Filipino',
            'PL' => 'Polish',
            'PT' => 'Portuguese',
            'QA' => 'Qatari',
            'RO' => 'Romanian',
            'RU' => 'Russian',
            'RW' => 'Rwandan',
            'KN' => 'Kittsian',
            'LC' => 'Lucian',
            'VC' => 'Vincentian',
            'WS' => 'Samoan',
            'SM' => 'San Marinese',
            'ST' => 'Sao Tomean',
            'SA' => 'Saudi',
            'SN' => 'Senegalese',
            'RS' => 'Serbian',
            'SC' => 'Seychellois',
            'SL' => 'Sierra Leonean',
            'SG' => 'Singaporean',
            'SK' => 'Slovak',
            'SI' => 'Slovenian',
            'SB' => 'Solomon Islander',
            'SO' => 'Somali',
            'ZA' => 'South African',
            'KR' => 'South Korean',
            'SS' => 'South Sudanese',
            'ES' => 'Spanish',
            'LK' => 'Sri Lankan',
            'SD' => 'Sudanese',
            'SR' => 'Surinamese',
            'SE' => 'Swedish',
            'CH' => 'Swiss',
            'SY' => 'Syrian',
            'TW' => 'Taiwanese',
            'TJ' => 'Tajikistani',
            'TZ' => 'Tanzanian',
            'TH' => 'Thai',
            'TL' => 'Timorese',
            'TG' => 'Togolese',
            'TO' => 'Tongan',
            'TT' => 'Trinidadian',
            'TN' => 'Tunisian',
            'TR' => 'Turkish',
            'TM' => 'Turkmen',
            'TV' => 'Tuvaluan',
            'UG' => 'Ugandan',
            'UA' => 'Ukrainian',
            'AE' => 'Emirati',
            'GB' => 'British',
            'US' => 'American',
            'UY' => 'Uruguayan',
            'UZ' => 'Uzbekistani',
            'VU' => 'Ni-Vanuatu',
            'VA' => 'Vatican',
            'VE' => 'Venezuelan',
            'VN' => 'Vietnamese',
            'YE' => 'Yemeni',
            'ZM' => 'Zambian',
            'ZW' => 'Zimbabwean',
        ];
        
        $iso2 = strtoupper($iso2);
        return $demonyms[$iso2] ?? \App\Data\Countries::name($iso2);
    }
}

/**
 * Get all nationalities as an array (for dropdowns)
 */
if (!function_exists('nationalities')) {
    function nationalities(): array
    {
        $result = [];
        $countries = \App\Data\Countries::getCountries();
        
        foreach ($countries as $iso2 => $country) {
            $result[$iso2] = nationality($iso2);
        }
        
        asort($result);
        return $result;
    }
}
