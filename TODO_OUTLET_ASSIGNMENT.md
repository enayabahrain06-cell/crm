# Outlet Assignment Feature Implementation

## Task
Add option to assign outlets while creating and editing users in the admin panel.

## Changes Required

### 1. Controller Updates (`app/Http/Controllers/Web/Admin/UserController.php`)
- [x] Update `create()` method to pass outlets to view
- [x] Update `store()` method to attach user to selected outlets
- [x] Update `edit()` method to pass assigned outlet IDs
- [x] Update `update()` method to sync outlet assignments

### 2. Create Form (`resources/views/admin/users/create.blade.php`)
- [x] Add multi-select dropdown for outlet assignment
- [x] Style the form section appropriately

### 3. Edit Form (`resources/views/admin/users/edit.blade.php`)
- [x] Add multi-select dropdown for outlet assignment
- [x] Pre-select currently assigned outlets
- [x] Style the form section appropriately

## Implementation Progress
- [x] Created TODO.md file
- [x] Updated UserController
- [x] Updated create.blade.php
- [x] Updated edit.blade.php
- [ ] Run migrations (php artisan migrate)

---

# Auto-Visit Recording on Registration

## Task
Automatically record a visit when customers register via the public QR form.

## Changes Required

### 1. Migration (`database/migrations/2025_01_01_000000_add_first_visit_at_to_customers.php`)
- [x] Add `first_visit_at` column to customers table

### 2. Customer Model (`app/Models/Customer.php`)
- [x] Add `first_visit_at` to fillable

### 3. PublicController (`app/Http/Controllers/Web/PublicController.php`)
- [x] Add LoyaltyService dependency
- [x] Add validation for `record_visit`, `bill_amount`, `visit_type`
- [x] Create `recordVisitForNewCustomer()` method
- [x] Create `recordVisitForExistingCustomer()` method
- [x] Update success view with `visit_recorded` flag

### 4. Registration Form (`resources/views/public/register.blade.php`)
- [x] Add "Record This Visit" section with checkbox
- [x] Add bill amount and visit type fields
- [x] Add JavaScript to handle toggle visibility

### 5. Success Page (`resources/views/public/register-success.blade.php`)
- [x] Show success message when visit is recorded

## Implementation Progress
- [x] Created migration file
- [x] Updated Customer model
- [x] Updated PublicController
- [x] Updated registration form
- [x] Updated success page
- [ ] Run migrations (php artisan migrate)

