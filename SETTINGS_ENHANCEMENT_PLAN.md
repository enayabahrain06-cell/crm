# Admin Settings Enhancement Plan

## Objective
Add Feature Toggles and Email/SMS Provider Settings to http://127.0.0.1:8001/admin/settings

## Information Gathered
- Current settings page has: Basic, Company, Localization, Security tabs
- Controller already supports validation for all these fields
- Settings are stored using the `setting()` helper function

## Plan
### Files to Modify:
1. `/root/hospitality-crm/resources/views/admin/settings/index.blade.php`
   - Add "Feature Toggles" tab with checkboxes for:
     - QR Code Generation
     - Auto Greetings
     - Customer Ratings
     - Import/Export
     - Multi-Outlet Mode
     - Guest Check-in
   - Add "Notifications" tab with:
     - SMS Provider settings (API Key, Sender ID, API URL)
     - Email Provider settings (SMTP Host, Port, Username, Password, Encryption)

## Implementation Steps:
1. Add Feature Toggles tab with toggle switches
2. Add Notifications tab with SMS/Email provider configuration
3. Test the new sections in the browser

## Follow-up:
- Clear view cache after implementation

