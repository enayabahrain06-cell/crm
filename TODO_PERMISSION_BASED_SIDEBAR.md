# TODO: Implement Permission-Based Sidebar & Authorization

## Overview
Change sidebar and controllers to use permission-based access instead of role-based access.

## Changes Required

### 1. Sidebar (resources/views/components/sidebar.blade.php)
- [x] Update Customers menu item to check `customers.view` permission
- [x] Update Visits menu item to check `visits.view` permission
- [x] Update Outlets menu item to check `outlets.view` permission
- [x] Update Loyalty Rewards menu item to check `rewards.view` permission
- [x] Update Loyalty Rules menu item to check `loyalty_rules.view` permission
- [x] Update Loyalty Wallets menu item to check `loyalty_wallets.view` permission
- [x] Update Campaigns menu item to check `campaigns.view` permission
- [x] Update Auto Greetings menu item to check `auto_greetings.view` permission
- [x] Update Reports menu items to check `reports.view` permission
- [x] Update Import/Export menu item to check `import_export.view` permission
- [x] Update Administration section checks to use super_admin role

### 2. Controllers Authorization
- [x] CustomerController - Already has permission checks
- [x] VisitController - Already has permission checks
- [x] LoyaltyController - Updated to use `loyalty_wallets.view` for wallet method
- [x] OutletController - Already has permission checks
- [x] CampaignController - Already has permission checks
- [x] AutoGreetingController - Already has permission checks
- [x] ReportController - Added permission checks for `reports.view`
- [x] ImportExportController - Added permission checks for `import_export.view`, `import_export.import`, `import_export.export`

### 3. Database Seeders
- [x] Added new permissions: `loyalty_wallets.view`, `audit_logs.view`, `backups.view`
- [x] Updated role permissions to include new permissions

## Permission Mapping

| Menu Item | Permission Required |
|-----------|---------------------|
| Customers | `customers.view` |
| Visits | `visits.view` |
| Outlets | `outlets.view` |
| Loyalty Rewards | `rewards.view` |
| Loyalty Wallets | `loyalty_wallets.view` |
| Loyalty Rules | `loyalty_rules.view` |
| Campaigns | `campaigns.view` |
| Auto Greetings | `auto_greetings.view` |
| Reports Overview | `reports.view` |
| Customer Report | `reports.view` |
| Visit Report | `reports.view` |
| Loyalty Report | `reports.view` |
| Import/Export | `import_export.view` |
| Users (Admin) | `users.view` |
| Roles (Admin) | `roles.view` |
| Settings (Admin) | `settings.view` |
| Audit Logs | `audit_logs.view` |
| Backups | `backups.view` |

## How to Assign Permissions to User `test@hospitality.com`

The user should be assigned permissions:
- `customers.view`
- `visits.view`
- `visits.create`
- `rewards.view`
- `loyalty_rules.view`

Then they will see in sidebar:
- **Core Operations**: Customers, Visits
- **Business Assets**: Loyalty Rewards, Loyalty Rules

## Commands Run
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan permission:cache-reset
```

## Testing Checklist
- [x] Run seeder and clear cache
- [ ] Login as user with limited permissions
- [ ] Verify sidebar only shows permitted items
- [ ] Try accessing restricted routes via URL
- [ ] Verify 403 error is returned for unauthorized access

