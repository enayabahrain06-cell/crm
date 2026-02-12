<?php

namespace App\Data;

use Illuminate\Support\Facades\File;

/**
 * Countries Data Service
 * 
 * Provides access to country information including names, flags, currencies, etc.
 */
class Countries
{
    private static ?array $countries = null;
    
    /**
     * Get all countries data
     */
    public static function getCountries(): array
    {
        if (self::$countries === null) {
            self::loadCountries();
        }
        
        return self::$countries;
    }
    
    /**
     * Load countries from JSON file
     */
    private static function loadCountries(): void
    {
        $path = base_path('app/Data/countries.json');
        
        if (File::exists($path)) {
            $data = json_decode(File::get($path), true);
            
            // Index by ISO2 code for easy lookup
            self::$countries = [];
            foreach ($data as $country) {
                $iso2 = $country['iso2'];
                // Convert flag code to emoji
                $flagEmoji = self::codeToEmoji($country['flag']);
                self::$countries[$iso2] = [
                    'name' => $country['name'],
                    'iso2' => $iso2,
                    'iso3' => $country['iso3'],
                    'flag' => $flagEmoji,
                    'call_code' => $country['call_code'],
                    'currency' => $country['currency'],
                    'currency_symbol' => $country['currency_symbol'],
                    'timezone' => $country['timezone'],
                ];
            }
        } else {
            self::$countries = [];
        }
    }
    
    /**
     * Convert 2-letter country code to emoji flag
     */
    private static function codeToEmoji(string $code): string
    {
        if (strlen($code) !== 2) {
            return '';
        }
        
        $firstChar = ord(strtolower($code[0])) - ord('a') + 0x1F1E6;
        $secondChar = ord(strtolower($code[1])) - ord('a') + 0x1F1E6;
        
        return json_decode('"' . $firstChar . $secondChar . '"') ?? '';
    }
    
    /**
     * Get a specific country by ISO2 code
     */
    public static function get(string $iso2): ?array
    {
        return self::getCountries()[$iso2] ?? null;
    }
    
    /**
     * Get country name
     */
    public static function name(string $iso2): string
    {
        return self::get($iso2)['name'] ?? $iso2;
    }
    
    /**
     * Get country flag emoji
     */
    public static function flag(string $iso2): string
    {
        return self::get($iso2)['flag'] ?? '';
    }
    
    /**
     * Get call code
     */
    public static function callCode(string $iso2): string
    {
        return self::get($iso2)['call_code'] ?? '';
    }
    
    /**
     * Get currency
     */
    public static function currency(string $iso2): string
    {
        return self::get($iso2)['currency'] ?? '';
    }
    
    /**
     * Get timezone
     */
    public static function timezone(string $iso2): string
    {
        return self::get($iso2)['timezone'] ?? '';
    }
    
    /**
     * Get all countries as a list for dropdowns (flag + name)
     */
    public static function list(): array
    {
        $countries = self::getCountries();
        $list = [];
        
        foreach ($countries as $iso2 => $country) {
            $list[$iso2] = $country['flag'] . ' ' . $country['name'];
        }
        
        return $list;
    }
    
    /**
     * Get countries indexed by name
     */
    public static function byName(): array
    {
        $countries = self::getCountries();
        $byName = [];
        
        foreach ($countries as $iso2 => $country) {
            $byName[$country['name']] = $country;
        }
        
        return $byName;
    }
}
