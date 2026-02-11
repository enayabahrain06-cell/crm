# Fix Customer Search Issue on HTTPS Domain

## Issue
- Search works on 192.168.1.54 but fails on https://devcrm.p7h.me with "No response from server"
- Root cause: Session cookie domain mismatch, credentials not sent with AJAX requests

## Root Causes Identified
1. **Session Cookie Domain** - Cookies were not being sent cross-domain
2. **Axios Credentials** - `withCredentials` was not set to `true`
3. **CSRF Token** - Not being properly included in all AJAX requests

## Changes Made

### 1. config/session.php - Updated
- `'secure' => env('SESSION_SECURE_COOKIE', true)` - Set default to true for HTTPS
- `'domain' => env('SESSION_DOMAIN', null)` - Allows .env override
- Added comments about cross-domain scenarios

### 2. resources/js/app.js - Updated
- Added `window.axios.defaults.withCredentials = true` for cross-domain cookies
- Added response interceptor to handle CSRF 419 errors
- CSRF token retrieval from meta tag and cookies

### 3. config/cors.php - Already configured
- `supports_credentials` set to `true`
- Paths include `customers/*`, `visits/*`, `auth/*`

## .env Settings Added
```env
SESSION_DOMAIN=.p7h.me
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=devcrm.p7h.me
```

## Execution Status
- [x] 1. Update .env APP_URL to https://devcrm.p7h.me
- [x] 2. Update CORS config to include web routes (customers/*, visits/*)
- [x] 3. Update config/session.php for secure cookies
- [x] 4. Update resources/js/app.js for credentials and CSRF
- [x] 5. Clear config cache
- [x] 6. Add .env settings
- [ ] 7. Build frontend assets (npm install && npm run build)

## Testing
Test the customer search at https://devcrm.p7h.me/customers

### Expected Console Output:
```
Customer Live Search: Using method: axios
Customer Live Search: URL: https://devcrm.p7h.me/customers/live-search?...
Customer Live Search: Response received
Customer Live Search: Rendering X customers
```

### If Still Failing:
Check browser console for:
1. CORS errors - Ensure SESSION_DOMAIN is set in .env
2. 419 CSRF errors - Check meta csrf-token tag exists
3. Network errors - Verify SSL certificate is valid

