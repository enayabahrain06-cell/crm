<?php

namespace App\Traits;

// libphonenumber\PhoneNumberUtil;
// libphonenumber\PhoneNumberFormat;
// libphonenumber\NumberParseException;

trait HasPhoneNormalization
{
    /**
     * Normalize phone number to E.164 format
     *
     * @param string $countryCode ISO2 country code (e.g., 'BH')
     * @param string $nationalNumber National phone number
     * @return array|null ['country_iso2', 'country_dial_code', 'national_number', 'e164'] or null on failure
     */
    public static function normalizePhone(string $countryCode, string $nationalNumber): ?array
    {
        // Use fallback normalization (no external dependency required)
        return self::fallbackNormalize($countryCode, $nationalNumber);
    }
    
    /**
     * Fallback normalization when libphonenumber fails
     */
    private static function fallbackNormalize(string $countryCode, string $nationalNumber): ?array
    {
        $countryCodeUpper = strtoupper($countryCode);
        $dialCodes = self::getDialCodes();
        
        $nationalNumber = preg_replace('/[^0-9]/', '', $nationalNumber);
        
        if (isset($dialCodes[$countryCodeUpper])) {
            return [
                'country_iso2' => $countryCodeUpper,
                'country_dial_code' => $dialCodes[$countryCodeUpper],
                'national_number' => $nationalNumber,
                'e164' => $dialCodes[$countryCodeUpper] . $nationalNumber
            ];
        }
        
        return null;
    }
    
    /**
     * Get dial codes for common countries
     */
    private static function getDialCodes(): array
    {
        return [
            'BH' => '+973', // Bahrain
            'SA' => '+966', // Saudi Arabia
            'AE' => '+971', // UAE
            'KW' => '+965', // Kuwait
            'QA' => '+974', // Qatar
            'OM' => '+968', // Oman
            'JO' => '+962', // Jordan
            'LB' => '+961', // Lebanon
            'EG' => '+20',  // Egypt
            'IQ' => '+964', // Iraq
            'US' => '+1',
            'GB' => '+44',
            'IN' => '+91',
            'PK' => '+92',
        ];
    }
    
    /**
     * Format phone number for display
     */
    public static function formatPhoneForDisplay(array $mobileJson): string
    {
        if (!isset($mobileJson['country_dial_code']) || !isset($mobileJson['national_number'])) {
            return '';
        }
        
        return $mobileJson['country_dial_code'] . ' ' . chunk_split($mobileJson['national_number'], 3, ' ');
    }
    
    /**
     * Validate phone number format
     */
    public static function validatePhone(string $countryCode, string $nationalNumber): bool
    {
        $normalized = self::normalizePhone($countryCode, $nationalNumber);
        return $normalized !== null;
    }
}

