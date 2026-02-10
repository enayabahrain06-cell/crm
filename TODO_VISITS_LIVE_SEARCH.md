# TODO: Live Search Implementation for Visit Management

## Task: Implement search-as-you-type functionality on /visits page

### Steps:

- [x] 1. Add liveSearch() method to VisitController.php
       - Create API endpoint that accepts filter parameters
       - Return JSON with visits data, stats, and pagination info

- [x] 2. Add route to routes/web.php
       - Add GET route: `visits/live-search` → `VisitController@liveSearch`

- [x] 3. Update visits/index.blade.php
       - Add JavaScript with debounced event listeners
       - Implement AJAX fetching from live-search endpoint
       - Dynamic table rendering
       - Loading indicator
       - Client-side pagination
       - URL state synchronization

### Technical Details:
- Debounce time: 300ms (same as customers)
- Pagination: 20 results per page
- API endpoint: `/visits/live-search`
- Method: GET (for browser history support)
- Filters to handle live: outlet_id, start_date, end_date, customer_id, search

### Features:
- ✅ Search happens automatically as you type
- ✅ All filters work live (outlet, dates, customer)
- ✅ Pagination works with live search
- ✅ URL updates reflect current filters
- ✅ Loading indicator shows when fetching results
- ✅ Traditional filter button still works as fallback
