# Moving Outlet Revenue Card to Outlets Page

## Task: Move "Outlet Revenue by Month" card from dashboard to outlets page

## Status: ✅ COMPLETED

### Changes Made:

#### 1. OutletController (`app/Http/Controllers/Web/OutletController.php`)
- ✅ Injected `DashboardService` into the controller
- ✅ Added `$request` parameter to `index()` method for year filtering
- ✅ Added code to fetch `$outletRevenue`, `$selectedYear`, and `$availableYears`
- ✅ Passed data to the view

#### 2. Outlets Index View (`resources/views/outlets/index.blade.php`)
- ✅ Added currency symbol variable (same as dashboard)
- ✅ Added Outlet Revenue by Month card with Chart.js
- ✅ Added year selector dropdown
- ✅ Card displays data with proper styling and responsiveness

### Access:
The Outlet Revenue by Month card is now available at:
- **https://devcrm.p7h.me/outlets**

### Features:
- Line chart showing monthly revenue for each outlet
- Year filter dropdown to change the displayed year
- Tooltips showing revenue per outlet and total
- Legend with color-coded outlets
- Responsive design
- Empty state message when no data available

