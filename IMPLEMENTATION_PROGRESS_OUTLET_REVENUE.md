# Outlet Revenue Line Chart Implementation - Progress

## Step 1: Add method to DashboardService ✅
- [x] Add `getOutletRevenueByMonth(int $year): array` method
- [x] Get all active outlets with their monthly revenue for the year
- [x] Return data structure suitable for Chart.js

## Step 2: Update DashboardController ✅
- [x] Add `$selectedYear` parameter from request (default: current year)
- [x] Call `getOutletRevenueByMonth()` method
- [x] Pass `$outletRevenue` and `$selectedYear` to the view

## Step 3: Add Line Chart to Dashboard View ✅
- [x] Add year selector dropdown in analytics section
- [x] Add multi-line chart container
- [x] Implement Chart.js configuration with:
  - X-axis: January to December
  - Y-axis: Revenue with currency
  - One line per outlet with distinct colors
- [x] Add JavaScript for year selector change event

## Step 4: Test and Verify
- [ ] Verify chart displays with sample data
- [ ] Test year selector functionality
- [ ] Ensure currency formatting matches system settings

## Implementation Summary

### Changes Made:

1. **app/Services/DashboardService.php**
   - Added `getOutletRevenueByMonth(int $year, ?int $outletId = null): array` - Returns monthly revenue data for each outlet
   - Added `getAvailableYears(): array` - Returns list of years with visit data

2. **app/Http/Controllers/Web/DashboardController.php**
   - Added `$selectedYear` from request (defaults to current year)
   - Added `$outletRevenue` and `$availableYears` variables

3. **resources/views/dashboard/index.blade.php**
   - Added "Outlet Revenue by Month" chart section with:
     - Year selector dropdown in the card header
     - Multi-line Chart.js chart spanning Jan-Dec
     - One line per outlet with distinct colors
     - Currency-formatted tooltips
     - Total revenue footer in tooltips

