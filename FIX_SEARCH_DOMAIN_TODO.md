# Fix Customer Search Domain Issue - Implementation Plan

## Issue
- Search works on 192.168.1.54 but fails on https://devcrm.p7h.me with "No response from server"
- Root cause: Session cookie domain mismatch and CSRF token not included in AJAX requests

## Plan
1. Update config/session.php to set proper SESSION_DOMAIN for cookies
2. Add axios configuration to include CSRF token and credentials
3. Clear config cache
4. Test the fix

## Implementation Steps

### Step 1: Update config/session.php - COMPLETED
- [x] Set SESSION_DOMAIN to '.p7h.me' for cross-subdomain cookies (via .env SESSION_DOMAIN=.p7h.me)
- [x] Ensure SESSION_SECURE_COOKIE is true for HTTPS (default true)
- [x] Set SESSION_SAME_SITE to 'lax' for cross-site requests

### Step 2: Add axios CSRF configuration - COMPLETED
- [x] Add axios.defaults.withCredentials = true
- [x] Configure axios to include CSRF token in requests
- [x] Add response interceptor for CSRF 419 handling

### Step 3: Clear Laravel config cache - COMPLETED
- [x] Run php artisan config:clear

### Step 4: Add .env settings - COMPLETED
- [x] SESSION_DOMAIN=.p7h.me
- [x] SESSION_SECURE_COOKIE=true
- [x] SANCTUM_STATEFUL_DOMAINS=devcrm.p7h.me

### Step 5: Build frontend assets - IN PROGRESS
- [ ] Run npm install && npm run build

### Step 6: Test
- [ ] Test customer live search at https://devcrm.p7h.me/customers
- [ ] Verify console shows no CORS or session errors
- [ ] Verify search returns results correctly

## Required .env Settings
Already added to .env file:
```
SESSION_DOMAIN=.p7h.me
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=devcrm.p7h.me
```

## Files Modified
1. config/session.php - Updated secure cookie default and added comments
2. resources/js/app.js - Added withCredentials and CSRF interceptor

