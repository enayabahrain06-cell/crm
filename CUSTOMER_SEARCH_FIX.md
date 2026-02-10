# Customer Search Fix - TODO List

## Issues Identified:
1. CSRF token missing in AJAX requests (JavaScript uses fetch() without CSRF)
2. Mobile JSON search not working properly (searching array as string)
3. No error feedback to users when search fails
4. Inconsistent AJAX library (project has axios but search uses fetch)
5. Script loading order issue - inline script was in @section('content') but axios loaded later

## Fix Plan:

### Step 1: Fix Backend - Customer Model Search Scope ✅ COMPLETED
- [x] Fix scopeSearch method to properly query JSON mobile fields
- [x] Use whereJsonContains or whereRaw for mobile_json searches

### Step 2: Fix Frontend - JavaScript Search ✅ COMPLETED
- [x] Add CSRF token header to all AJAX requests (in app.js)
- [x] Replace fetch() with axios for consistency (in index.blade.php)
- [x] Add error notifications for users (toast notifications)
- [x] Improve debug logging
- [x] Fix script loading order - moved script to @push('scripts')

### Step 3: Test the fixes
- [ ] Test search by name
- [ ] Test search by email
- [ ] Test search by phone number
- [ ] Test filters (type, gender, nationality, status)
- [ ] Test pagination

## Summary of Changes Made:

### 1. Backend Changes (`app/Models/Customer.php`)
- Fixed `scopeSearch()` method to properly handle mobile JSON searches
- Now extracts national_number from mobile_json using JSON query
- Cleans search term to remove formatting characters before searching phone numbers
- Uses `json_extract()` for SQLite-compatible JSON queries

### 2. Frontend Changes (`resources/views/customers/index.blade.php`)
- Replaced `fetch()` with `axios` for consistent AJAX handling
- Added CSRF token support (axios automatically includes it from meta tag)
- Added `showError()` function to display toast notifications on errors
- Improved error handling with detailed error messages
- Enhanced debug logging for troubleshooting
- **Fixed script loading order** - Moved script from `@section('content')` to `@push('scripts')` so it loads AFTER axios is available

### 3. Axios Configuration (`resources/js/app.js`)
- Added automatic CSRF token extraction from meta tag
- Set X-XSRF-TOKEN header for all axios requests
- Ensures CSRF protection works with all AJAX requests

### 4. Layout Changes (`resources/views/layouts/app.blade.php`)
- Added axios CDN script for immediate availability

## Status: ✅ FIXES IMPLEMENTED - PENDING TESTING

## Test Instructions:
1. Open browser developer console (F12)
2. Go to /customers page
3. Check console for "Axios available: true" message
4. Type in the search box
5. Check console for debug messages showing search results

