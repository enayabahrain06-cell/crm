# Settings Page Reorganization Plan

## Objective
Reorganize the admin settings page at http://127.0.0.1:8000/admin/settings into logical categories

## Current Organization (10 tabs)
1. Basic
2. Company
3. Loyalty
4. Customers
5. Localization
6. Features
7. Notifications
8. Templates
9. Security
10. Audit

## Proposed New Organization (6 Logical Categories)

### **1. General Settings** (Core app configuration)
- Basic Settings
- Company Settings
- Localization Settings

### **2. Customer Management** (Customer-facing features)
- Customer Settings
- Feature Toggles

### **3. Loyalty Program** (Rewards & points)
- Loyalty Program Settings

### **4. Communications** (Notifications & messaging)
- Notification Settings
- Message Templates

### **5. System & Security** (Admin & compliance)
- Security Settings
- Audit & Logging

## Implementation Steps

### Phase 1: Reorder Tab Navigation
Change tab order in navigation to match logical grouping

### Phase 2: Reorganize Tab Content Sections
Move content sections to match new tab order

### Phase 3: Update Tab Icons
Assign appropriate icons to each category

### Phase 4: Add Section Headers
Add visual section headers within each tab for better organization

## New Tab Structure

### Tab 1: ⚙️ General
**Content:**
- Basic Settings (app name, email, logo, favicon)
- Company Settings (company info, logo)
- Localization (language, timezone, currency, date format)

### Tab 2: 👥 Customers
**Content:**
- Customer Settings (phone, verification, birthday, status)
- Feature Toggles (QR codes, greetings, ratings, etc.)

### Tab 3: 🎁 Loyalty
**Content:**
- Loyalty Program Settings (points, tiers, welcome bonus)

### Tab 4: 📢 Communications
**Content:**
- SMS Provider Settings
- Email Provider Settings (SMTP)
- Notification Preferences
- Message Templates (welcome, birthday, visit, tier upgrade)

### Tab 5: 🔐 Security
**Content:**
- Password Policy
- Session Settings
- Two-Factor Authentication
- Lockout Duration

### Tab 6: 📊 Audit
**Content:**
- Audit Logging Toggle
- Log Sensitive Data
- Log API Calls
- Log Retention

## Files to Modify
1. `/root/hospitality-crm/resources/views/admin/settings/index.blade.php`
   - Reorder tab navigation items
   - Reorder tab content sections
   - Add section headers within tabs
   - Update icons

## Success Criteria
- ✅ Logical grouping of settings
- ✅ Easy navigation for admins
- ✅ Consistent styling
- ✅ No functionality changes
- ✅ All existing settings preserved

## Estimated Time
15-20 minutes for complete reorganization

