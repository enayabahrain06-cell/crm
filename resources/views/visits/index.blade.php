@extends('layouts.app')

@section('title', 'Visit Management')
@section('page-title', 'Visit Management')

@php
$systemCurrency = setting('currency', 'BHD');
$currencySymbol = match($systemCurrency) {
    'USD' => '$',
    'EUR' => '€',
    'GBP' => '£',
    'BHD' => 'BHD ',
    'SAR' => 'SR ',
    'AED' => 'AED ',
    default => $systemCurrency . ' '
};
@endphp

@push('scripts')
<script>
// Live Search functionality for Visit Management
document.addEventListener('DOMContentLoaded', function() {
    console.log('Visit Live Search: Initializing...');
    
    // Configuration
    const DEBOUNCE_DELAY = 300;
    const LIVE_SEARCH_URL = '{{ route("visits.live-search") }}';
    const CURRENCY_SYMBOL = '{{ $currencySymbol }}';
    
    // DOM Elements
    const outletSelect = document.getElementById('outlet-filter');
    const startDateInput = document.getElementById('start-date-filter');
    const endDateInput = document.getElementById('end-date-filter');
    const customerIdInput = document.getElementById('customer-id-filter');
    const searchInput = document.getElementById('visit-search-input');
    const resetBtn = document.getElementById('reset-btn');
    const resultsTableBody = document.querySelector('#visits-table tbody');
    const resultsCountDiv = document.getElementById('results-count');
    const statsContainer = document.getElementById('stats-container');
    const paginationDiv = document.querySelector('.mt-4 nav');
    const loadingEl = document.getElementById('live-search-loading');
    
    console.log('Visit Live Search: Elements found:', {
        outletSelect: !!outletSelect,
        startDateInput: !!startDateInput,
        endDateInput: !!endDateInput,
        customerIdInput: !!customerIdInput,
        searchInput: !!searchInput,
        resultsTableBody: !!resultsTableBody,
        statsContainer: !!statsContainer
    });
    
    // State
    let currentPage = 1;
    let isLoading = false;
    let searchTimeout = null;
    
    // Get current filters
    function getFilters() {
        return {
            outlet_id: outletSelect ? outletSelect.value : '',
            start_date: startDateInput ? startDateInput.value : '',
            end_date: endDateInput ? endDateInput.value : '',
            customer_id: customerIdInput ? customerIdInput.value : '',
            search: searchInput ? searchInput.value.trim() : '',
            page: currentPage,
            per_page: 20
        };
    }
    
    // Build query string
    function buildQueryString(filters) {
        const parts = [];
        Object.keys(filters).forEach(function(key) {
            const value = filters[key];
            if (value && value !== '') {
                parts.push(key + '=' + encodeURIComponent(value));
            }
        });
        return parts.join('&');
    }
    
    // Update URL without reload
    function updateURL(filters) {
        const params = new URLSearchParams();
        Object.keys(filters).forEach(function(key) {
            const value = filters[key];
            if (value && value !== '' && key !== 'page' && key !== 'per_page') {
                params.set(key, value);
            }
        });
        const newURL = window.location.pathname + '?' + params.toString();
        window.history.replaceState({}, '', newURL);
    }
    
    // Fetch results via AJAX
    function fetchResults() {
        const filters = getFilters();
        
        console.log('Visit Live Search: Fetching with filters:', filters);
        
        if (isLoading) {
            console.log('Visit Live Search: Already loading, skipping...');
            return;
        }
        
        isLoading = true;
        if (loadingEl) loadingEl.style.display = 'inline-flex';
        
        const queryString = buildQueryString(filters);
        const url = LIVE_SEARCH_URL + '?' + queryString;
        
        console.log('Visit Live Search: URL:', url);
        
        fetch(url)
            .then(function(response) {
                console.log('Visit Live Search: Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network error: ' + response.status);
                }
                return response.json();
            })
            .then(function(data) {
                console.log('Visit Live Search: Data received:', data);
                renderResults(data);
                updateURL(filters);
            })
            .catch(function(error) {
                console.error('Visit Live Search Error:', error);
            })
            .finally(function() {
                isLoading = false;
                if (loadingEl) loadingEl.style.display = 'none';
            });
    }
    
    // Render stats
    function renderStats(stats) {
        if (!statsContainer) return;
        
        const statsHtml = `
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="card bg-primary-subtle border-0 h-100">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-calendar-check-fill fs-3 text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fs-4 fw-bold">${parseInt(stats.total_visits || 0).toLocaleString()}</div>
                                    <div class="text-muted small">Total Visits</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-success-subtle border-0 h-100">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-cash-stack fs-3 text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fs-4 fw-bold">${CURRENCY_SYMBOL}${parseFloat(stats.total_revenue || 0).toLocaleString(undefined, {minimumFractionDigits: 3, maximumFractionDigits: 3})}</div>
                                    <div class="text-muted small">Total Revenue</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-warning-subtle border-0 h-100">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-star-fill fs-3 text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fs-4 fw-bold">${parseInt(stats.total_points || 0).toLocaleString()}</div>
                                    <div class="text-muted small">Points Awarded</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-info-subtle border-0 h-100">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-shop fs-3 text-info"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fs-4 fw-bold">{{ $outlets->count() }}</div>
                                    <div class="text-muted small">Active Outlets</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        statsContainer.innerHTML = statsHtml;
    }
    
    // Render results
    function renderResults(data) {
        console.log('Visit Live Search: Rendering results...');
        
        // Update count
        if (resultsCountDiv && data.stats) {
            resultsCountDiv.innerHTML = 'Showing <strong>' + data.stats.showing_count + '</strong> of <strong>' + data.stats.total_count + '</strong> visits';
        }
        
        // Render stats
        renderStats(data.stats || {});
        
        // Render table rows
        if (resultsTableBody) {
            let html = '';
            if (data.visits && data.visits.length > 0) {
                data.visits.forEach(function(visit) {
                    const customerHtml = visit.customer_name 
                        ? `<div class="d-flex align-items-center">
                                <div class="user-avatar me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                    ${visit.customer_initial || '?'}
                                </div>
                                <div class="overflow-hidden">
                                    <div class="fw-semibold small text-truncate">${visit.customer_name}</div>
                                    <small class="text-muted text-truncate d-block">${visit.customer_mobile || '-'}</small>
                                </div>
                           </div>`
                        : '<span class="text-muted">-</span>';
                    
                    const outletHtml = visit.outlet_name
                        ? `<span class="badge bg-info">${visit.outlet_name}</span>`
                        : '<span class="text-muted">-</span>';
                    
                    const staffHtml = visit.staff_name
                        ? `<div class="d-flex align-items-center">
                                <i class="bi bi-person-fill text-muted me-2"></i>
                                <span class="small text-truncate">${visit.staff_name}</span>
                           </div>`
                        : '<span class="text-muted">-</span>';
                    
                    const billAmountHtml = visit.bill_amount > 0
                        ? `${parseFloat(visit.bill_amount).toLocaleString(undefined, {minimumFractionDigits: 3, maximumFractionDigits: 3})}`
                        : '<span class="text-muted">-</span>';
                    
                    const pointsHtml = visit.points_awarded > 0
                        ? `<span class="badge bg-success">${visit.points_awarded}</span>`
                        : '<span class="text-muted">-</span>';
                    
                    html += '<tr data-id="' + visit.id + '">';
                    html += '<td class="text-center"><input class="form-check-input visit-checkbox" type="checkbox" value="' + visit.id + '"></td>';
                    html += '<td><span class="text-muted small">#' + visit.id + '</span></td>';
                    html += '<td>' + customerHtml + '</td>';
                    html += '<td>' + outletHtml + '</td>';
                    html += '<td>' + staffHtml + '</td>';
                    html += '<td class="text-end fw-semibold">' + billAmountHtml + '</td>';
                    html += '<td class="text-center">' + pointsHtml + '</td>';
                    html += '<td><div class="small">' + visit.visited_date + '</div><small class="text-muted">' + visit.visited_time + '</small></td>';
                    html += '<td class="text-center"><div class="dropdown d-inline-block"><button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button><ul class="dropdown-menu dropdown-menu-end">';
                    html += '<li><a class="dropdown-item" href="' + visit.show_url + '"><i class="bi bi-eye me-2"></i>View Details</a></li>';
                    html += '</ul></div></td>';
                    html += '</tr>';
                });
            } else {
                html = '<tr><td colspan="9" class="text-center py-5"><div class="text-muted"><i class="bi bi-calendar-check display-4"></i><p class="mt-2 mb-0">No visits found matching your criteria</p></div></td></tr>';
            }
            resultsTableBody.innerHTML = html;
        }
        
        // Render pagination
        renderPagination(data.pagination);
    }
    
    // Render pagination
    function renderPagination(pagination) {
        if (!paginationDiv) return;
        
        if (!pagination || pagination.last_page <= 1) {
            paginationDiv.style.display = 'none';
            return;
        }
        
        paginationDiv.style.display = 'block';
        
        let html = '<nav><ul class="pagination justify-content-center mb-0">';
        let prevDisabled = pagination.current_page === 1 ? 'disabled' : '';
        html += '<li class="page-item ' + prevDisabled + '"><a class="page-link" href="#" data-page="' + (pagination.current_page - 1) + '">Previous</a></li>';
        
        let maxPages = 5;
        let startPage = Math.max(1, pagination.current_page - Math.floor(maxPages / 2));
        let endPage = Math.min(pagination.last_page, startPage + maxPages - 1);
        if (endPage - startPage < maxPages - 1) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            let activeClass = i === pagination.current_page ? 'active' : '';
            html += '<li class="page-item ' + activeClass + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
        }
        
        let nextDisabled = pagination.current_page === pagination.last_page ? 'disabled' : '';
        html += '<li class="page-item ' + nextDisabled + '"><a class="page-link" href="#" data-page="' + (pagination.current_page + 1) + '">Next</a></li>';
        html += '</ul></nav>';
        
        paginationDiv.innerHTML = html;
        
        // Add click handlers
        let links = paginationDiv.querySelectorAll('.page-link');
        for (let j = 0; j < links.length; j++) {
            links[j].addEventListener('click', function(e) {
                e.preventDefault();
                let page = parseInt(this.getAttribute('data-page'));
                if (page && page !== pagination.current_page && page > 0 && page <= pagination.last_page) {
                    currentPage = page;
                    fetchResults();
                }
            });
        }
    }
    
    // Handle input with debounce
    function handleInput() {
        currentPage = 1;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            console.log('Visit Live Search: Triggering search for:', searchInput ? searchInput.value : '');
            fetchResults();
        }, DEBOUNCE_DELAY);
    }
    
    // Attach events to filter inputs
    function attachFilterEvents() {
        console.log('Visit Live Search: Attaching event listeners...');
        
        if (outletSelect) {
            outletSelect.addEventListener('change', fetchResults);
            console.log('Visit Live Search: Outlet select listener attached');
        }
        if (startDateInput) {
            startDateInput.addEventListener('change', fetchResults);
            console.log('Visit Live Search: Start date listener attached');
        }
        if (endDateInput) {
            endDateInput.addEventListener('change', fetchResults);
            console.log('Visit Live Search: End date listener attached');
        }
        if (customerIdInput) {
            customerIdInput.addEventListener('change', fetchResults);
            console.log('Visit Live Search: Customer ID listener attached');
        }
        if (searchInput) {
            searchInput.addEventListener('input', handleInput);
            searchInput.addEventListener('keyup', handleInput);
            console.log('Visit Live Search: Search input listener attached');
        }
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (outletSelect) outletSelect.value = '';
                if (startDateInput) startDateInput.value = '';
                if (endDateInput) endDateInput.value = '';
                if (customerIdInput) customerIdInput.value = '';
                if (searchInput) searchInput.value = '';
                currentPage = 1;
                console.log('Visit Live Search: Filters reset, triggering search...');
                fetchResults();
            });
            console.log('Visit Live Search: Reset button listener attached');
        }
        
        console.log('Visit Live Search: All event listeners attached');
    }
    
    // Initialize
    attachFilterEvents();
    
    // Trigger initial fetch if there are URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString()) {
        console.log('Visit Live Search: URL has params, triggering initial search...');
        fetchResults();
    }
});
</script>
@endpush

@section('content')
{{-- Page Header --}}
<div class="row mb-4">
    <div class="col-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Visits</li>
            </ol>
        </nav>
        <h4 class="mb-0">Visit Management</h4>
        <p class="text-muted mb-0 small">Track and manage customer visits</p>
    </div>
    <div class="col-4 text-end">
        <div class="btn-group">
            <a href="{{ route('visits.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Record Visit
            </a>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><h6 class="dropdown-header">Export Visits</h6></li>
                <li><a class="dropdown-item" href="{{ route('visits.export', array_merge(request()->query(), ['format' => 'csv'])) }}">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export as CSV
                </a></li>
                <li><a class="dropdown-item" href="{{ route('visits.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}">
                    <i class="bi bi-file-earmark-excel me-2"></i>Export as Excel
                </a></li>
                <li><a class="dropdown-item" href="{{ route('visits.export-pdf', request()->query()) }}">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Export as PDF
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><span class="px-3 text-muted small">Current filters will be applied</span></li>
            </ul>
        </div>
    </div>
</div>

{{-- Dynamic Stats Container --}}
<div id="stats-container">
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card bg-primary-subtle border-0 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-calendar-check-fill fs-3 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-4 fw-bold">{{ number_format($stats['total_visits'] ?? 0) }}</div>
                            <div class="text-muted small">Total Visits</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-success-subtle border-0 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-cash-stack fs-3 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-4 fw-bold">{{ $currencySymbol }}{{ number_format($stats['total_revenue'] ?? 0, 3) }}</div>
                            <div class="text-muted small">Total Revenue</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-warning-subtle border-0 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-star-fill fs-3 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-4 fw-bold">{{ number_format($stats['total_points'] ?? 0) }}</div>
                            <div class="text-muted small">Points Awarded</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-info-subtle border-0 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-shop fs-3 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-4 fw-bold">{{ $outlets->count() }}</div>
                            <div class="text-muted small">Active Outlets</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters & Search --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-medium">Outlet</label>
                <select class="form-select" id="outlet-filter">
                    <option value="">All Outlets</option>
                    @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>
                        {{ $outlet->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-medium">Start Date</label>
                <input type="date" class="form-control" id="start-date-filter" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-medium">End Date</label>
                <input type="date" class="form-control" id="end-date-filter" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-medium">Customer ID</label>
                <input type="number" class="form-control" id="customer-id-filter" value="{{ request('customer_id') }}" placeholder="Customer ID">
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-medium">Search</label>
                <input type="text" class="form-control" id="visit-search-input" value="{{ request('search') }}" placeholder="Name, email, phone..." autocomplete="off">
            </div>
            <div class="col-md-2 col-sm-12">
                <div class="d-flex gap-2">
                    <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary" id="reset-btn" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Results Count & Bulk Actions --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <div class="text-muted me-3" id="results-count">
            Showing <strong>{{ $visits->count() }}</strong> of <strong>{{ $visits->total() }}</strong> visits
        </div>
        <div id="live-search-loading" style="display:none;">
            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
            <span class="text-muted small">Searching...</span>
        </div>
    </div>
    <div class="form-check d-none" id="bulk-actions-toolbar">
        <input class="form-check-input" type="checkbox" id="select-all">
        <label class="form-check-label small" for="select-all">Select All</label>
        <span class="ms-2 small text-muted"><span id="selected-count">0</span> selected</span>
        <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="bulk-delete-btn" disabled>
            <i class="bi bi-trash me-1"></i>Delete Selected
        </button>
    </div>
</div>

{{-- Visits Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="visits-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">
                            <input class="form-check-input" type="checkbox" id="select-all-header">
                        </th>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 200px;">Customer</th>
                        <th style="width: 140px;">Outlet</th>
                        <th style="width: 140px;">Staff</th>
                        <th style="width: 120px;" class="text-end">Bill Amount</th>
                        <th style="width: 80px;" class="text-center">Points</th>
                        <th style="width: 140px;">Visited At</th>
                        <th style="width: 100px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visits as $visit)
                    <tr data-id="{{ $visit->id }}">
                        <td class="text-center">
                            <input class="form-check-input visit-checkbox" type="checkbox" value="{{ $visit->id }}">
                        </td>
                        <td>
                            <span class="text-muted small">#{{ $visit->id }}</span>
                        </td>
                        <td>
                            @if($visit->customer)
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                    {{ substr($visit->customer->name, 0, 1) }}
                                </div>
                                <div class="overflow-hidden">
                                    <div class="fw-semibold small text-truncate">{{ $visit->customer->name }}</div>
                                    <small class="text-muted text-truncate d-block">{{ $visit->customer->mobile_json ? formatMobileNumber($visit->customer->mobile_json) : '-' }}</small>
                                </div>
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($visit->outlet)
                            <span class="badge bg-info">{{ $visit->outlet->name }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($visit->staff)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-fill text-muted me-2"></i>
                                <span class="small text-truncate">{{ $visit->staff->name }}</span>
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end fw-semibold">
                            @if($visit->bill_amount > 0)
                            {{ number_format($visit->bill_amount, 3) }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($visit->points_awarded > 0)
                            <span class="badge bg-success">{{ $visit->points_awarded }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="small">{{ $visit->visited_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $visit->visited_at->format('h:i A') }}</small>
                        </td>
                        <td class="text-center">
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('visits.show', $visit) }}">
                                        <i class="bi bi-eye me-2"></i>View Details
                                    </a></li>
                                    @can('visits.delete')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('visits.destroy', $visit) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this visit?')">
                                                <i class="bi bi-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-calendar-check display-4"></i>
                                <p class="mt-2 mb-0">No visits found matching your criteria</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-4 d-flex justify-content-center">
    {{ $visits->appends(request()->query())->links() }}
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('visits-table');
    const selectAllHeader = document.getElementById('select-all-header');
    const selectAll = document.getElementById('select-all');
    const bulkToolbar = document.getElementById('bulk-actions-toolbar');
    const selectedCount = document.getElementById('selected-count');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const checkboxes = table ? table.querySelectorAll('.visit-checkbox') : [];

    // Show toolbar when any checkbox is selected
    function updateToolbar() {
        if (!table) return;
        
        const checked = table.querySelectorAll('.visit-checkbox:checked');
        const count = checked.length;
        if (selectedCount) selectedCount.textContent = count;
        if (bulkDeleteBtn) bulkDeleteBtn.disabled = count === 0;
        
        if (count > 0) {
            bulkToolbar.classList.remove('d-none');
        } else {
            bulkToolbar.classList.add('d-none');
        }

        // Update select all checkbox state
        if (selectAllHeader) {
            selectAllHeader.checked = count === checkboxes.length;
            selectAllHeader.indeterminate = count > 0 && count < checkboxes.length;
        }
        if (selectAll) {
            selectAll.checked = count === checkboxes.length;
            selectAll.indeterminate = count > 0 && count < checkboxes.length;
        }
    }

    // Select all in header
    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAllHeader.checked;
            });
            updateToolbar();
        });
    }

    // Select all in toolbar
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateToolbar();
        });
    }

    // Individual checkbox change
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateToolbar);
    });

    // Bulk delete
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            if (!table) return;
            
            const checked = table.querySelectorAll('.visit-checkbox:checked');
            if (checked.length === 0) return;

            if (!confirm('Are you sure you want to delete ' + checked.length + ' visit(s)?')) {
                return;
            }

            const ids = Array.from(checked).map(cb => cb.value);
            
            // Send delete request
            fetch('{{ route("visits.index") }}/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting visits: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error deleting visits: ' + error.message);
            });
        });
    }
});
</script>
@endpush
@endsection
