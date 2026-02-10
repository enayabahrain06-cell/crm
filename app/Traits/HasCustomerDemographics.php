<?php

namespace App\Traits;

use DateTime;

trait HasCustomerDemographics
{
    /**
     * Calculate age from date of birth
     */
    public static function calculateAge(?string $dateOfBirth): ?int
    {
        if (empty($dateOfBirth)) {
            return null;
        }
        
        try {
            $dob = new DateTime($dateOfBirth);
            $now = new DateTime();
            $diff = $now->diff($dob);
            return $diff->y;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Get age group from age
     */
    public static function getAgeGroup(?int $age): string
    {
        if ($age === null) {
            return 'unknown';
        }
        
        if ($age <= 3) {
            return 'toddler';
        } elseif ($age <= 12) {
            return 'child';
        } elseif ($age <= 25) {
            return 'youth';
        } elseif ($age <= 59) {
            return 'adult';
        } else {
            return 'senior';
        }
    }
    
    /**
     * Get zodiac sign from date of birth
     */
    public static function getZodiac(?string $dateOfBirth): ?string
    {
        if (empty($dateOfBirth)) {
            return null;
        }
        
        try {
            $month = (int) date('m', strtotime($dateOfBirth));
            $day = (int) date('d', strtotime($dateOfBirth));
            
            $zodiacs = [
                ['name' => 'Capricorn', 'start' => [12, 22], 'end' => [1, 19]],
                ['name' => 'Aquarius', 'start' => [1, 20], 'end' => [2, 18]],
                ['name' => 'Pisces', 'start' => [2, 19], 'end' => [3, 20]],
                ['name' => 'Aries', 'start' => [3, 21], 'end' => [4, 19]],
                ['name' => 'Taurus', 'start' => [4, 20], 'end' => [5, 20]],
                ['name' => 'Gemini', 'start' => [5, 21], 'end' => [6, 20]],
                ['name' => 'Cancer', 'start' => [6, 21], 'end' => [7, 22]],
                ['name' => 'Leo', 'start' => [7, 23], 'end' => [8, 22]],
                ['name' => 'Virgo', 'start' => [8, 23], 'end' => [9, 22]],
                ['name' => 'Libra', 'start' => [9, 23], 'end' => [10, 22]],
                ['name' => 'Scorpio', 'start' => [10, 23], 'end' => [11, 21]],
                ['name' => 'Sagittarius', 'start' => [11, 22], 'end' => [12, 21]],
            ];
            
            foreach ($zodiacs as $zodiac) {
                $start = $zodiac['start'];
                $end = $zodiac['end'];
                
                if ($start[0] == $end[0]) {
                    // Same month zodiac
                    if ($month == $start[0] && $day >= $start[1] && $day <= $end[1]) {
                        return $zodiac['name'];
                    }
                } else {
                    // Cross-month zodiac
                    if (($month == $start[0] && $day >= $start[1]) || ($month == $end[0] && $day <= $end[1])) {
                        return $zodiac['name'];
                    }
                }
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Check if today is customer's birthday
     */
    public static function isBirthdayToday(?string $dateOfBirth): bool
    {
        if (empty($dateOfBirth)) {
            return false;
        }
        
        $dobMonth = date('m', strtotime($dateOfBirth));
        $dobDay = date('d', strtotime($dateOfBirth));
        $todayMonth = date('m');
        $todayDay = date('d');
        
        return $dobMonth == $todayMonth && $dobDay == $todayDay;
    }
    
    /**
     * Get customers with birthday today
     * SQLite-compatible: use strftime('%j') for day of year
     */
    public static function scopeBirthdayToday($query)
    {
        return $query->whereNotNull('date_of_birth')
            ->whereRaw("strftime('%j', date_of_birth) = strftime('%j', 'now')");
    }

    /**
     * Get customers with birthday on specific date
     * SQLite-compatible: use strftime('%m') and strftime('%d')
     */
    public static function scopeBirthdayOnDate($query, int $month, int $day)
    {
        // Pad month and day with leading zeros for SQLite comparison
        $monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
        $dayPadded = str_pad($day, 2, '0', STR_PAD_LEFT);

        return $query->whereNotNull('date_of_birth')
            ->whereRaw("strftime('%m', date_of_birth) = ?", [$monthPadded])
            ->whereRaw("strftime('%d', date_of_birth) = ?", [$dayPadded]);
    }
}

