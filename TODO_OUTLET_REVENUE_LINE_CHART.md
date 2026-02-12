# Outlet Revenue Line Chart Implementation Plan

## Information Gathered

1. **Dashboard Structure**: The dashboard (`resources/views/dashboard/index.blade.php`) already uses Chart.js for visualizations (gender doughnut chart, age groups line chart)
2. **Data Models**: 
   - `Visit` model has `bill_amount` for revenue and `visited_at` for date tracking
   - `Outlet` model has relationship with visits
3. **DashboardService**: Already has methods for analytics but lacks revenue-by-outlet-per-month data
4. **Chart.js**: Already integrated and used in the dashboard
5. **Current Year**: Need to default to `now()->year` for the year selector

## Plan

### Step 1: Add method to DashboardService
Add `getOutletRevenueByMonth(int $year): array` method that returns:
- All active outlets
- For each outlet: monthly revenue totals (Jan-Dec) for the selected year

### Step 2: Update DashboardController
Pass the outlet revenue data to the dashboard view:
- Current year (default)
- All outlets with their revenue data

### Step 3: Add Year Selector to Dashboard
Add a year dropdown in the analytics section header with:
- Current year as default
- Range of years (based on available data or last 5 years)

### Step 4: Create Line Chart in Dashboard View
Add a new chart section that:
- Displays a multi-line chart (one line per outlet)
- X-axis: Jan, Feb, Mar, ..., Dec
- Y-axis: Revenue amount with currency formatting
- Legend: Outlet names with distinct colors
- Tooltip: Shows revenue for each outlet per month

### Step 5: Add JavaScript for Chart Update
Implement:
- Year selector change event handler
- AJAX call to fetch new data when year changes
- Chart.js update/re-render logic

## Dependent Files to be Edited

1. `app/Services/DashboardService.php` - Add `getOutletRevenueByMonth()` method
2. `app/Http/Controllers/DashboardController.php` - Pass data to view
3. `resources/views/dashboard/index.blade.php` - Add chart UI and JavaScript

## Followup Steps

1. Test the chart displays correctly with sample data
2. Verify year selector works and updates the chart
3. Ensure currency formatting matches system settings

