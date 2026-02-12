# Country & Nationality Implementation - TODO

## Summary
This document tracks the implementation of standardized country/nationality selection across the Hospitality CRM project using ISO2 codes.

---

## ✅ Completed Tasks

### 1. Updated Components

#### `resources/views/components/country-dropdown.blade.php`
- Changed from ISO3 to ISO2 codes (BHR → BH, SAU → SA, etc.)
- Added `mode` prop to support both "country" and "nationality" display formats
- In nationality mode: displays demonyms (🇧🇭 Bahraini instead of 🇧🇭 Bahrain)
- Integrated Select2 for searchable dropdown
- Added GPS-based auto-detection for user's country
- Loads countries from `/data/countries.json`
- Sorted alphabetically by country name
- Added comprehensive demonym mapping for all 195+ countries

#### `resources/views/components/phone-country-code.blade.php` (NEW)
- Created separate component for phone country codes
- Shows flag + country name + dial code (e.g., 🇧🇭 Bahrain (+973))
- Loads from same JSON file for consistency

### 2. Updated Forms

#### Outlets
- ✅ `resources/views/outlets/create.blade.php` - Country dropdown
- ✅ `resources/views/outlets/edit.blade.php` - Country dropdown

#### Customers
- ✅ `resources/views/customers/create.blade.php` - Nationality & country code
- ✅ `resources/views/customers/edit.blade.php` - Nationality & country code

#### Public Registration
- ✅ `resources/views/public/register.blade.php` - Nationality & country code

#### Admin Settings
- ✅ `resources/views/admin/settings/index.blade.php` - Default country

### 3. Database Changes

#### Migration: `database/migrations/2025_02_05_000000_normalize_countries_to_iso2.php`
- Converts full country names to ISO2 codes in outlets table
- Supports 250+ country mappings
- Reversible migration (rollback to names)
- Migration ran successfully ✓

#### Console Command: `app/Console/Commands/NormalizeCountries.php`
- Command: `php artisan data:normalize-countries`
- Option: `--dry-run` to preview changes
- Validates existing data

### 4. Helper Functions

#### Updated: `app/Helpers.php`
- Added `nationality($iso2)` - Get demonym from ISO2 code
- Added `nationalities()` - Get all nationalities as sorted array
- Updated demonyms for all countries (e.g., BH → Bahraini, SA → Saudi)

---

## 📋 Usage Examples

### Country Selection (Default Mode)
```blade
<x-country-dropdown 
    name="country" 
    id="country" 
    :value="old('country', $outlet->country)" 
    :required="true" 
    label="Country"
/>
```
**Output:** 🇧🇭 Bahrain, 🇸🇦 Saudi Arabia, 🇦🇪 United Arab Emirates...

### Nationality Selection (Nationality Mode)
```blade
<x-country-dropdown 
    name="nationality" 
    id="nationality" 
    :value="old('nationality', $customer->nationality)" 
    :required="false" 
    label="Nationality"
    mode="nationality"
/>
```
**Output:** 🇧🇭 Bahraini, 🇸🇦 Saudi, 🇦🇪 Emirati...

### Phone Country Code Selection
```blade
<x-phone-country-code 
    name="country_code" 
    id="country_code" 
    :value="old('country_code', 'BH')" 
    :required="true" 
    label="Country Code"
/>
```
**Output:** 🇧🇭 Bahrain (+973), 🇸🇦 Saudi Arabia (+966)...

---

## 🔧 Helper Functions

### Get Nationality Demonym
```php
nationality('BH');    // "Bahraini"
nationality('SA');     // "Saudi"
nationality('AE');     // "Emirati"
nationality('US');     // "American"
nationality('GB');     // "British"
```

### Get All Nationalities
```php
nationalities();
// [
//     'AF' => 'Afghan',
//     'AL' => 'Albanian',
//     'DZ' => 'Algerian',
//     ...
// ]
```

### Get Country Information
```php
countryName('BH');     // "Bahrain"
countryFlag('BH');     // "🇧🇭"
countryCallCode('BH'); // "+973"
```

---

## 📊 Data Format Standard

| Field | Format | Example |
|-------|--------|---------|
| `customers.nationality` | ISO2 | `BH`, `SA`, `AE` |
| `outlets.country` | ISO2 | `BH`, `SA`, `AE` |
| Display | Flag + Name | 🇧🇭 Bahrain |
| Nationality | Flag + Demonym | 🇧🇭 Bahraini |
| Phone Code | Flag + Name + Code | 🇧🇭 Bahrain (+973) |

---

## 🚀 Next Steps

1. **Test the forms** - Verify dropdowns work correctly
2. **Clear cache** - Run `php artisan optimize:clear`
3. **Check customer data** - Ensure nationalities display correctly
4. **Update any remaining forms** - If more country dropdowns exist
5. **Add tests** - For helper functions

---

## 📁 Files Modified/Created

### Modified
1. `resources/views/components/country-dropdown.blade.php`
2. `resources/views/outlets/create.blade.php`
3. `resources/views/outlets/edit.blade.php`
4. `resources/views/customers/create.blade.php`
5. `resources/views/customers/edit.blade.php`
6. `resources/views/public/register.blade.php`
7. `resources/views/admin/settings/index.blade.php`
8. `app/Helpers.php`

### Created
1. `resources/views/components/phone-country-code.blade.php`
2. `database/migrations/2025_02_05_000000_normalize_countries_to_iso2.php`
3. `app/Console/Commands/NormalizeCountries.php`

---

## ✅ Verification Commands

```bash
# Run migration
php artisan migrate --path=database/migrations/2025_02_05_000000_normalize_countries_to_iso2.php

# Check data normalization
php artisan data:normalize-countries

# Clear caches
php artisan optimize:clear
```

