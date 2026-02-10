# SQLite Compatibility Fixes

This document tracks the progress of fixing MySQL-specific functions for SQLite compatibility.

## Issues Identified
- DashboardService.php: `getAgeGroupDistribution()` uses `TIMESTAMPDIFF()` and `CURDATE()`
- DashboardService.php: `getZodiacDistribution()` uses `DATE_FORMAT()` and non-existent constant
- Customer.php: `scopeByAgeGroup()` uses `TIMESTAMPDIFF()` and `CURDATE()`
- HasCustomerDemographics.php: `scopeBirthdayToday()` uses `DAYOFYEAR()` and `CURDATE()`
- HasCustomerDemographics.php: `scopeBirthdayOnDate()` uses `MONTH()` and `DAYOFMONTH()`

## Tasks - COMPLETED ✅

### 1. Fix DashboardService.php - getAgeGroupDistribution()
- [x] Replace `TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())` with SQLite-compatible calculation
- [x] Use `strftime('%Y', 'now') - strftime('%Y', date_of_birth) - (strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth))`

### 2. Fix DashboardService.php - getZodiacDistribution()
- [x] Remove reference to non-existent `Customer::ZODIAC_RANGES`
- [x] Add zodiac constant `ZODIAC_RANGES` to DashboardService.php
- [x] Use SQLite-compatible date comparison with strftime

### 3. Fix Customer.php - scopeByAgeGroup()
- [x] Replace `TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())` with SQLite-compatible calculation

### 4. Fix HasCustomerDemographics.php - scopeBirthdayToday()
- [x] Replace `DAYOFYEAR(date_of_birth) = DAYOFYEAR(CURDATE())` with SQLite `strftime('%j')` equivalent

### 5. Fix HasCustomerDemographics.php - scopeBirthdayOnDate()
- [x] Replace `MONTH(date_of_birth)` with SQLite `strftime('%m', date_of_birth)`
- [x] Replace `DAYOFMONTH(date_of_birth)` with SQLite `strftime('%d', date_of_birth)`

## SQLite Date Functions Reference
- `strftime('%Y', date)` - Year
- `strftime('%m', date)` - Month (01-12)
- `strftime('%d', date)` - Day (01-31)
- `strftime('%Y-%m-%d', date)` - Full date
- `strftime('%j', date)` - Day of year (001-366)
- `strftime('%w', date)` - Weekday (0-6, Sunday=0)

## Summary of Changes

### DashboardService.php
1. Added `ZODIAC_RANGES` constant for zodiac sign ranges
2. Fixed `getAgeGroupDistribution()` to use SQLite-compatible age calculation:
   ```php
   $ageCalculation = "CAST(strftime('%Y', 'now') AS INTEGER) - CAST(strftime('%Y', date_of_birth) AS INTEGER) - (CASE WHEN strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth) THEN 1 ELSE 0 END)";
   ```
3. Fixed `getZodiacDistribution()` to use `self::ZODIAC_RANGES` and SQLite strftime
4. Fixed `getCampaignAnalytics()` - moved `orderByDesc()` from Collection to Query Builder (before `->get()`)

### Customer.php
1. Fixed `scopeByAgeGroup()` to use SQLite-compatible age calculation

### HasCustomerDemographics.php
1. Fixed `scopeBirthdayToday()` to use `strftime('%j', ...)`
2. Fixed `scopeBirthdayOnDate()` to use `strftime('%m', ...)` and `strftime('%d', ...)`

## Additional Fix - BadMethodCallException
- Fixed `orderByDesc()` being called on Collection instead of Query Builder in `getCampaignAnalytics()`

## Additional Fix - PermissionDoesNotExist
- Fixed `Controller.php` - pluralized resource names to match database permissions (`customer` → `customers`)

