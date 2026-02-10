# Sidebar Reorganization - TODO List

## Plan Approved: Yes

### Changes to Implement:

1. **Reorder Sections** (Done: ✓)
   - Dashboard - keep as first item
   - Core Operations - keep as first section
   - Business Assets - reorder after Core Operations
   - Growth & Marketing - reorder after Business Assets
   - Insights & Analytics - reorder after Growth & Marketing
   - Administration - keep at bottom

2. **Simplify Structure** (Done: ✓)
   - Make all collapsible sections expanded by default
   - Streamline navigation items
   - Remove redundant links (QR Codes and Social Links will remain as they're anchor links)

3. **Visual Improvements** (Done: ✓)
   - Add section dividers
   - Improve spacing between sections
   - Consistent badge styling
   - Better hover effects

### Implementation Status:
- [x] Create TODO.md
- [x] Plan approved by user
- [x] Implement sidebar reorganization
- [x] Add CSS styling for nav-divider

## Changes Applied:

1. **Section Dividers Added** - Added `<li class="nav-divider"></li>` between all sections for better visual separation, with CSS styling in `app.blade.php`

2. **Business Assets Section Simplified** - Removed QR Codes and Social Links as separate items (they're accessible via anchor links on the Outlets page)

3. **Badge Styling Improved** - Changed Campaigns badge from `bg-warning` to `bg-warning text-dark` for better readability

4. **Logical Section Ordering**:
   - Dashboard (first item)
   - Core Operations
   - Business Assets
   - Growth & Marketing
   - Insights & Analytics
   - Administration (Super Admin only - collapsed by default)

5. **All Sections Expanded by Default** - All collapsible sections use `collapse show` for immediate visibility

6. **CSS Styling Added** - Added `.nav-divider` styling in `layouts/app.blade.php` for visual section separators

**Files Modified:**
- `hospitality-crm/resources/views/components/sidebar.blade.php`
- `hospitality-crm/resources/views/layouts/app.blade.php`

---
**File to Edit:** hospitality-crm/resources/views/components/sidebar.blade.php

