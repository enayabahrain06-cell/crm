@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customer Management')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('customers.index') }}" method="GET" id="customer-search-form">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="customer-search-input" name="search" value="{{ request('search') }}" placeholder="Name, email, phone..." autocomplete="off">
                        <button class="btn btn-outline-primary" type="submit" id="customer-search-btn" title="Search">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select class="form-select" id="customer-type-select" name="type">
                    <option value="">All Types</option>
                    <option value="individual" {{ request('type') === 'individual' ? 'selected' : '' }}>Individual</option>
                    <option value="corporate" {{ request('type') === 'corporate' ? 'selected' : '' }}>Corporate</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Gender</label>
                <select class="form-select" id="customer-gender-select" name="gender">
                    <option value="">All</option>
                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Nationality</label>
                <select class="form-select" id="customer-nationality-select" name="nationality">
                    <option value="">All</option>
                    @foreach($nationalities as $nat)
                    <option value="{{ $nat }}" {{ request('nationality') === $nat ? 'selected' : '' }}>{{ $nat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" id="customer-status-select" name="status">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="blacklisted" {{ request('status') === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary w-100" title="Reset filters">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <div class="text-muted me-3" id="results-count">
            Showing <strong>{{ $customers->count() }}</strong> of <strong>{{ $customers->total() }}</strong> customers
        </div>
        <div id="live-search-loading" style="display:none;">
            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
            <span class="text-muted small">Searching...</span>
        </div>
    </div>
    <div class="btn-group">
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Customer
        </a>
        @can('customers.export')
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="bi bi-download"></i> Export
        </button>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Nationality</th>
                        <th>Points</th>
                        <th>Visits</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="customers-table-body">
                    @forelse($customers as $customer)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3" style="width: 36px; height: 36px; font-size: 0.875rem;">
                                    {{ substr($customer->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $customer->name }}</div>
                                    @if($customer->company_name)
                                    <small class="text-muted">{{ $customer->company_name }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $customer->type === 'corporate' ? 'info' : 'success' }}">
                                {{ ucfirst($customer->type) }}
                            </span>
                        </td>
                        <td>
                            <div>{{ $customer->email ?: 'No email' }}</div>
                            @if($customer->mobile_json)
                            <small class="text-muted">{{ formatMobileNumber($customer->mobile_json) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="flag-icon me-1">{{ getCountryFlag($customer->nationality) }}</span>
                            {{ $customer->nationality ?: 'N/A' }}
                        </td>
                        <td>
                            @if($customer->wallet)
                            <span class="fw-semibold">{{ number_format($customer->wallet->total_points) }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $customer->visits_count ?? 0 }}</td>
                        <td>
                            <span class="badge badge-status badge-{{ $customer->status === 'active' ? 'active' : ($customer->status === 'inactive' ? 'inactive' : 'pending') }}">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('customers.show', $customer) }}">
                                        <i class="bi bi-eye me-2"></i>View
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('customers.360', $customer) }}">
                                        <i class="bi bi-circle me-2"></i>Customer 360
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('customers.edit', $customer) }}">
                                        <i class="bi bi-pencil me-2"></i>Edit
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-people display-4"></i>
                                <p class="mt-2">No customers found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4" id="pagination-container">
    {{ $customers->appends(request()->query())->links() }}
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Customers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('import-export.export') }}" method="GET">
                <div class="modal-body">
                    <input type="hidden" name="type" value="customers">
                    <div class="mb-3">
                        <label class="form-label">Format</label>
                        <select class="form-select" name="format">
                            <option value="csv">CSV</option>
                            <option value="xlsx">Excel (XLSX)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Columns to Export</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="name" checked>
                            <label class="form-check-label">Name</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="email" checked>
                            <label class="form-check-label">Email</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="mobile" checked>
                            <label class="form-check-label">Mobile</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="nationality" checked>
                            <label class="form-check-label">Nationality</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="visits" checked>
                            <label class="form-check-label">Visit Count</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Customer Search Debug - Check dependencies first
console.log('Customer Search: Checking dependencies...');
console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
console.log('Axios available:', typeof axios !== 'undefined');

document.addEventListener('DOMContentLoaded', function() {
    console.log('Customer Live Search: Initializing...');
    console.log('Axios status at init:', typeof axios !== 'undefined' ? 'Available' : 'NOT AVAILABLE');
    
    // Configuration
    var DEBOUNCE_DELAY = 300;
    var LIVE_SEARCH_URL = '{{ route("customers.live-search") }}';
    
    // DOM Elements
    var searchInput = document.getElementById('customer-search-input');
    var searchBtn = document.getElementById('customer-search-btn');
    var typeSelect = document.getElementById('customer-type-select');
    var genderSelect = document.getElementById('customer-gender-select');
    var nationalitySelect = document.getElementById('customer-nationality-select');
    var statusSelect = document.getElementById('customer-status-select');
    var resultsTableBody = document.getElementById('customers-table-body');
    var resultsCountDiv = document.getElementById('results-count');
    var paginationDiv = document.getElementById('pagination-container');
    var loadingEl = document.getElementById('live-search-loading');
    
    console.log('Customer Live Search: Elements found:', {
        searchInput: !!searchInput,
        typeSelect: !!typeSelect,
        resultsTableBody: !!resultsTableBody
    });
    
    // State
    var currentPage = 1;
    var isLoading = false;
    var searchTimeout = null;
    
    // Show error notification
    function showError(message) {
        var container = document.getElementById('error-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'error-toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        
        var toastId = 'error-toast-' + Date.now();
        var toastHtml = '<div id="' + toastId + '" class="toast align-items-center text-white bg-danger border-0" role="alert">' +
            '<div class="d-flex">' +
            '<div class="toast-body">' + message + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
            '</div></div>';
        
        container.insertAdjacentHTML('beforeend', toastHtml);
        
        var toastEl = document.getElementById(toastId);
        var toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
        
        toastEl.addEventListener('hidden.bs.toast', function() {
            toastEl.remove();
        });
    }
    
    // Get current filters
    function getFilters() {
        return {
            search: searchInput ? searchInput.value.trim() : '',
            type: typeSelect ? typeSelect.value : '',
            gender: genderSelect ? genderSelect.value : '',
            nationality: nationalitySelect ? nationalitySelect.value : '',
            status: statusSelect ? statusSelect.value : '',
            page: currentPage,
            per_page: 20
        };
    }
    
    // Build query string
    function buildQueryString(filters) {
        var parts = [];
        Object.keys(filters).forEach(function(key) {
            var value = filters[key];
            if (value && value !== '') {
                parts.push(key + '=' + encodeURIComponent(value));
            }
        });
        return parts.join('&');
    }
    
    // Update URL without reload
    function updateURL(filters) {
        var params = new URLSearchParams();
        Object.keys(filters).forEach(function(key) {
            var value = filters[key];
            if (value && value !== '' && key !== 'page' && key !== 'per_page') {
                params.set(key, value);
            }
        });
        var newURL = window.location.pathname + '?' + params.toString();
        window.history.replaceState({}, '', newURL);
    }
    
    // Fetch results via AJAX
    function fetchResults() {
        var filters = getFilters();
        
        console.log('Customer Live Search: Fetching with filters:', filters);
        
        if (isLoading) {
            console.log('Customer Live Search: Already loading, skipping...');
            return;
        }
        
        isLoading = true;
        if (loadingEl) loadingEl.style.display = 'inline-flex';
        
        var queryString = buildQueryString(filters);
        var url = LIVE_SEARCH_URL + '?' + queryString;
        
        console.log('Customer Live Search: URL:', url);
        
        // Use axios if available, otherwise use fetch
        var requestPromise = typeof axios !== 'undefined' ? axios.get(url) : fetch(url);
        
        console.log('Customer Live Search: Using method:', typeof axios !== 'undefined' ? 'axios' : 'fetch');
        
        requestPromise
            .then(function(response) {
                console.log('Customer Live Search: Response received');
                
                // Handle both axios and fetch responses
                var data = response.data || response;
                
                console.log('Customer Live Search: Data:', data);
                
                if (!data || !data.customers) {
                    console.error('Customer Live Search: Invalid response format:', data);
                    showError('Invalid response from server');
                    return;
                }
                console.log('Customer Live Search: Rendering', data.customers.length, 'customers');
                renderResults(data);
                updateURL(filters);
            })
            .catch(function(error) {
                console.error('Customer Live Search Error:', error);
                if (error.response) {
                    console.error('Error response:', error.response);
                    showError('Search failed: ' + (error.response.data?.message || error.response.statusText));
                } else if (error.request) {
                    showError('Search failed: No response from server. Please check your connection.');
                } else {
                    showError('Search failed: ' + error.message);
                }
            })
            .finally(function() {
                isLoading = false;
                if (loadingEl) loadingEl.style.display = 'none';
            });
    }
    
    // Render results
    function renderResults(data) {
        console.log('Customer Live Search: Rendering results...');
        
        // Update count
        if (resultsCountDiv && data.filters) {
            resultsCountDiv.innerHTML = 'Showing <strong>' + data.filters.showing_count + '</strong> of <strong>' + data.filters.total_count + '</strong> customers';
        }
        
        // Render table rows
        if (resultsTableBody) {
            var html = '';
            if (data.customers && data.customers.length > 0) {
                data.customers.forEach(function(customer) {
                    var statusClass = customer.status === 'active' ? 'active' : (customer.status === 'inactive' ? 'inactive' : 'pending');
                    var typeBadge = customer.type === 'corporate' ? 'info' : 'success';
                    var typeLabel = customer.type.charAt(0).toUpperCase() + customer.type.slice(1);
                    var statusLabel = customer.status.charAt(0).toUpperCase() + customer.status.slice(1);
                    var companyHtml = customer.company_name ? '<small class="text-muted">' + customer.company_name + '</small>' : '';
                    var mobileHtml = customer.mobile ? '<small class="text-muted">' + customer.mobile + '</small>' : '';
                    var countryFlag = customer.country_flag ? '<span class="flag-icon me-1">' + customer.country_flag + '</span>' : '';
                    var nationality = customer.nationality ? customer.nationality : 'N/A';
                    var points = customer.points !== undefined ? customer.points.toLocaleString() : '0';
                    var visitsCount = customer.visits_count !== undefined ? customer.visits_count : '0';
                    
                    html += '<tr>';
                    html += '<td><div class="d-flex align-items-center"><div class="user-avatar me-3" style="width: 36px; height: 36px; font-size: 0.875rem;">' + customer.name.charAt(0) + '</div><div><div class="fw-semibold">' + customer.name + '</div>' + companyHtml + '</div></div></td>';
                    html += '<td><span class="badge bg-' + typeBadge + '">' + typeLabel + '</span></td>';
                    html += '<td><div>' + (customer.email || 'No email') + '</div>' + mobileHtml + '</td>';
                    html += '<td>' + countryFlag + nationality + '</td>';
                    html += '<td><span class="fw-semibold">' + points + '</span></td>';
                    html += '<td>' + visitsCount + '</td>';
                    html += '<td><span class="badge badge-status badge-' + statusClass + '">' + statusLabel + '</span></td>';
                    html += '<td><div class="dropdown"><button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button><ul class="dropdown-menu dropdown-menu-end">';
                    html += '<li><a class="dropdown-item" href="' + customer.show_url + '"><i class="bi bi-eye me-2"></i>View</a></li>';
                    html += '<li><a class="dropdown-item" href="' + customer['360_url'] + '"><i class="bi bi-circle me-2"></i>Customer 360</a></li>';
                    html += '<li><a class="dropdown-item" href="' + customer.edit_url + '"><i class="bi bi-pencil me-2"></i>Edit</a></li>';
                    html += '</ul></div></td>';
                    html += '</tr>';
                });
            } else {
                html = '<tr><td colspan="8" class="text-center py-5"><div class="text-muted"><i class="bi bi-people display-4"></i><p class="mt-2">No customers found</p></div></td></tr>';
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
        
        var html = '<nav><ul class="pagination justify-content-center mb-0">';
        var prevDisabled = pagination.current_page === 1 ? 'disabled' : '';
        html += '<li class="page-item ' + prevDisabled + '"><a class="page-link" href="#" data-page="' + (pagination.current_page - 1) + '">Previous</a></li>';
        
        var maxPages = 5;
        var startPage = Math.max(1, pagination.current_page - Math.floor(maxPages / 2));
        var endPage = Math.min(pagination.last_page, startPage + maxPages - 1);
        if (endPage - startPage < maxPages - 1) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }
        
        for (var i = startPage; i <= endPage; i++) {
            var activeClass = i === pagination.current_page ? 'active' : '';
            html += '<li class="page-item ' + activeClass + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
        }
        
        var nextDisabled = pagination.current_page === pagination.last_page ? 'disabled' : '';
        html += '<li class="page-item ' + nextDisabled + '"><a class="page-link" href="#" data-page="' + (pagination.current_page + 1) + '">Next</a></li>';
        html += '</ul></nav>';
        
        paginationDiv.innerHTML = html;
        
        // Add click handlers
        var links = paginationDiv.querySelectorAll('.page-link');
        for (var j = 0; j < links.length; j++) {
            links[j].addEventListener('click', function(e) {
                e.preventDefault();
                var page = parseInt(this.getAttribute('data-page'));
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
            console.log('Customer Live Search: Triggering search for:', searchInput ? searchInput.value : '');
            fetchResults();
        }, DEBOUNCE_DELAY);
    }
    
    // Attach event listeners
    function attachFilterEvents() {
        console.log('Customer Live Search: Attaching event listeners...');
        
        if (searchInput) {
            searchInput.addEventListener('input', handleInput);
            console.log('Customer Live Search: Search input listener attached');
        }
        
        // Search button click handler
        if (searchBtn) {
            searchBtn.addEventListener('click', function() {
                console.log('Customer Live Search: Button clicked, triggering search...');
                fetchResults();
            });
            console.log('Customer Live Search: Search button listener attached');
        }
        
        if (typeSelect) {
            typeSelect.addEventListener('change', fetchResults);
            console.log('Customer Live Search: Type select listener attached');
        }
        
        if (genderSelect) {
            genderSelect.addEventListener('change', fetchResults);
            console.log('Customer Live Search: Gender select listener attached');
        }
        
        if (nationalitySelect) {
            nationalitySelect.addEventListener('change', fetchResults);
            console.log('Customer Live Search: Nationality select listener attached');
        }
        
        if (statusSelect) {
            statusSelect.addEventListener('change', fetchResults);
            console.log('Customer Live Search: Status select listener attached');
        }
        
        console.log('Customer Live Search: All event listeners attached');
    }
    
    // Initialize
    attachFilterEvents();
    
    // Trigger initial fetch if there are URL parameters
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString()) {
        console.log('Customer Live Search: URL has params, triggering initial search...');
        fetchResults();
    }
});
</script>
@endpush

