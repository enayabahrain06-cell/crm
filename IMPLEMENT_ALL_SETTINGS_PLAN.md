# Comprehensive Settings Implementation Plan

## Objective
Add all pending settings to http://127.0.0.1:8001/admin/settings including:
- Loyalty Program Settings
- Customer Settings
- Audit & Logging Settings  
- Notification Templates

## Information Gathered

### Current Settings Implementation:
1. **SettingsController.php** - Already has validation for most fields
2. **settings/index.blade.php** - Has 6 tabs: Basic, Company, Localization, Features, Notifications, Security
3. **Routes** - Already configured for settings

### Missing UI Sections:
1. Loyalty Program Settings tab
2. Customer Settings tab  
3. Audit & Logging Settings tab
4. Notification Templates section

## Implementation Plan

### Phase 1: Update SettingsController.php
**File:** `/root/hospitality-crm/app/Http/Controllers/Web/Admin/SettingsController.php`

**Add validation for:**
```php
// Loyalty Program Settings
'loyalty_program_enabled' => 'nullable|boolean',
'points_per_currency' => 'nullable|numeric|min:0',
'points_expiration_months' => 'nullable|integer|min:0',
'min_points_redemption' => 'nullable|integer|min:0',
'welcome_points' => 'nullable|integer|min:0',
'enable_tiered_membership' => 'nullable|boolean',

// Customer Settings
'auto_assign_customer_to_outlet' => 'nullable|boolean',
'birthday_reminder_days' => 'nullable|integer|min:0|max:365',
'allow_duplicate_phones' => 'nullable|boolean',
'require_phone_verification' => 'nullable|boolean',
'default_customer_status' => 'nullable|string|in:active,inactive,pending',

// Audit & Logging
'audit_logging_enabled' => 'nullable|boolean',
'log_retention_days' => 'nullable|integer|min:1|max:365',
'log_sensitive_data' => 'nullable|boolean',
'log_api_calls' => 'nullable|boolean',

// Notification Templates
'welcome_sms_template' => 'nullable|string|max:500',
'welcome_email_template' => 'nullable|string|max:1000',
'birthday_sms_template' => 'nullable|string|max:500',
'birthday_email_template' => 'nullable|string|max:1000',
'visit_confirmation_sms_template' => 'nullable|string|max:500',
'visit_confirmation_email_template' => 'nullable|string|max:1000',
```

### Phase 2: Update settings/index.blade.php
**File:** `/root/hospitality-crm/resources/views/admin/settings/index.blade.php`

**Add new tabs:**
1. **Loyalty Program Settings Tab** - Toggle and points configuration
2. **Customer Settings Tab** - Customer management options
3. **Audit & Logging Tab** - Logging and compliance settings
4. **Notification Templates Tab** - Message templates for SMS/Email

**Update Notifications tab:**
- Add template editor sections

### Phase 3: Testing
**Steps:**
1. Clear view cache
2. Test settings save/load functionality
3. Verify all new fields work correctly
4. Test file uploads if needed

## Files to Modify
1. `/root/hospitality-crm/app/Http/Controllers/Web/Admin/SettingsController.php`
2. `/root/hospitality-crm/resources/views/admin/settings/index.blade.php`

## Implementation Order
1. Update SettingsController validation rules
2. Add Loyalty Program Settings tab
3. Add Customer Settings tab
4. Add Audit & Logging Settings tab
5. Add Notification Templates tab
6. Test all functionality

## Success Criteria
- All new settings appear in the admin UI
- Settings save/load correctly
- No validation errors
- Consistent styling with existing settings

