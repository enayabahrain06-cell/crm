# Sidebar and Page Layout Redesign TODO

## Phase 1: Reorder Sidebar Sections and Items ✅
- [x] Keep Dashboard at the top.
- [x] Move Customer Management (Customers, Visits) to first section after Dashboard.
- [x] Move Loyalty Program next, with Rewards first, then Wallets, then Rules.
- [x] Marketing (Campaigns, Auto Greetings).
- [x] Outlets.
- [x] Reports (Reports, Import/Export).
- [x] Administration (Users, Roles, Settings, Audit Logs) for super_admin.

## Phase 2: Make Sections Collapsible ✅
- [x] Add chevron icons to section titles.
- [x] Use Bootstrap's collapse component for toggling sections.
- [x] Default all sections open except Administration (collapsed by default for non-super_admin).

## Phase 3: Highlight Super_Admin Sections ✅
- [x] Add subtle background color (light gold) to Administration section title and items.

## Phase 4: Add UI Improvements ✅
- [x] Add badges for counts (e.g., total customers, pending campaigns) - requires backend data.
- [x] Ensure color coding: Active links in primary color, inactive in gray.
- [x] Maintain consistent spacing and icons.

## Phase 5: Ensure Responsive Layout ✅
- [x] Add toggle button for mobile sidebar (hamburger menu).
- [x] Sidebar slides in/out on mobile.

## Phase 6: Update Layout File ✅
- [x] Modify `resources/views/layouts/app.blade.php` to implement all changes.

## Phase 7: Testing and Validation ✅
- [x] Test the updated layout on desktop and mobile screen sizes.
- [x] Verify collapsible functionality and role-based visibility.
- [x] Implement backend logic for dynamic badges if needed.
- [x] Run Laravel's asset compilation if any custom CSS/JS is added.

## Summary
The sidebar and page layout redesign has been completed successfully. Key improvements include:
- Reorganized menu structure with logical grouping and prioritization of frequently used modules.
- Collapsible sections for better navigation and space utilization.
- Visual highlighting for super_admin sections.
- Added dynamic badges for quick insights (customer counts, etc.).
- Enhanced responsive design with mobile-friendly sidebar toggle.
- Consistent styling and improved usability across all screen sizes.
