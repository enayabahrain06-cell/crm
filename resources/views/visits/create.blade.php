@extends('layouts.app')

@section('title', 'Record Visit')
@section('page-title', 'Record New Visit')

@push('scripts')
<style>
/* Custom Searchable Dropdown Styles */
.customer-search-container {
    position: relative;
}

.customer-search-input {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2V9l-3 3h-7a2 2 0 0 1-2-2V5z'/%3e%3cpath fill='%23343a40' d='M8.5 8.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem;
    padding-right: 2.5rem;
}

.customer-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0 0 0.375rem 0.375rem;
    max-height: 300px;
    overflow-y: auto;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    display: none;
}

.customer-search-results.show {
    display: block;
}

.customer-search-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.15s ease;
}

.customer-search-item:last-child {
    border-bottom: none;
}

.customer-search-item:hover,
.customer-search-item.highlighted {
    background-color: #f8f9fa;
}

.customer-search-item.no-results {
    padding: 1.5rem 1rem;
    text-align: center;
    color: #6c757d;
    cursor: default;
}

.customer-search-item.selected {
    background-color: #e9ecef;
}

.customer-search-name {
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.25rem;
}

.customer-search-details {
    font-size: 0.813rem;
    color: #6c757d;
}

.customer-search-details i {
    margin-right: 0.25rem;
    width: 14px;
    text-align: center;
}

.customer-search-loading {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    display: none;
}

.customer-search-loading.show {
    display: block;
}

.customer-search-clear {
    position: absolute;
    right: 2.5rem;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
    display: none;
}

.customer-search-clear.show {
    display: block;
}

.customer-search-clear:hover {
    color: #212529;
}

.selected-customer-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background-color: #e3f2fd;
    border: 1px solid #90caf9;
    border-radius: 0.375rem;
    margin-top: 0.5rem;
}

.selected-customer-badge .customer-info {
    margin-left: 0.5rem;
    font-size: 0.875rem;
}

.selected-customer-badge .customer-name {
    font-weight: 600;
    color: #1976d2;
}

.selected-customer-badge .customer-meta {
    font-size: 0.75rem;
    color: #6c757d;
}

.selected-customer-badge .remove-btn {
    margin-left: 0.75rem;
    cursor: pointer;
    color: #dc3545;
    font-size: 1.25rem;
    line-height: 1;
}

.selected-customer-badge .remove-btn:hover {
    color: #bd2130;
}
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Record New Visit</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('visits.store') }}" method="POST" id="visit-form">
                    @csrf

                    {{-- Customer Search Field --}}
                    <div class="mb-4">
                        <label class="form-label">Customer <span class="text-danger">*</span></label>
                        <div class="customer-search-container">
                            <input type="text" 
                                   class="form-control customer-search-input @error('customer_id') is-invalid @enderror" 
                                   id="customer-search-input"
                                   placeholder="Search by name, phone, or email..."
                                   autocomplete="off"
                                   value="{{ $selectedCustomer ? $selectedCustomer->name : '' }}">
                            
                            <span class="customer-search-clear" id="customer-search-clear" title="Clear selection">
                                <i class="bi bi-x-circle-fill"></i>
                            </span>
                            
                            <div class="customer-search-loading" id="customer-search-loading">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                            </div>
                            
                            <div class="customer-search-results" id="customer-search-results"></div>
                            
                            {{-- Hidden input for form submission --}}
                            <input type="hidden" name="customer_id" id="customer-id" value="{{ old('customer_id', $customerId) }}">
                            
                            {{-- Selected customer badge --}}
                            <div id="selected-customer-badge">
                                @if($selectedCustomer)
                                <div class="selected-customer-badge">
                                    <i class="bi bi-person-fill text-primary" style="font-size: 1.25rem;"></i>
                                    <div class="customer-info">
                                        <div class="customer-name">{{ $selectedCustomer->name }}</div>
                                        <div class="customer-meta">
                                            @if($selectedCustomer->formatted_mobile)
                                            <i class="bi bi-phone"></i>{{ $selectedCustomer->formatted_mobile }}
                                            @endif
                                            @if($selectedCustomer->email)
                                            <span class="mx-2">|</span>
                                            <i class="bi bi-envelope"></i>{{ $selectedCustomer->email }}
                                            @endif
                                            @if($selectedCustomer->loyaltyWallet)
                                            <span class="mx-2">|</span>
                                            <i class="bi bi-star-fill text-warning"></i>{{ $selectedCustomer->loyaltyWallet->total_points }} pts
                                            @endif
                                        </div>
                                    </div>
                                    <span class="remove-btn" id="remove-customer" title="Remove selection">
                                        <i class="bi bi-x-circle"></i>
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @error('customer_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text small">Start typing to search customers by name, phone, or email</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Outlet <span class="text-danger">*</span></label>
                            <select class="form-select @error('outlet_id') is-invalid @enderror" name="outlet_id" required>
                                <option value="">Select Outlet</option>
                                @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ old('outlet_id', $outletId) == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('outlet_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bill Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('bill_amount') is-invalid @enderror" 
                                       name="bill_amount" value="{{ old('bill_amount') }}" 
                                       step="0.01" min="0" placeholder="0.00">
                            </div>
                            @error('bill_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Visit Date & Time</label>
                            <input type="datetime-local" class="form-control @error('visited_at') is-invalid @enderror" 
                                   name="visited_at" value="{{ old('visited_at', now()->format('Y-m-d\TH:i')) }}">
                            @error('visited_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Visit Type</label>
                            <select class="form-select @error('visit_type') is-invalid @enderror" name="visit_type">
                                <option value="dine_in" {{ old('visit_type') === 'dine_in' ? 'selected' : '' }}>Dine In</option>
                                <option value="takeaway" {{ old('visit_type') === 'takeaway' ? 'selected' : '' }}>Takeaway</option>
                                <option value="delivery" {{ old('visit_type') === 'delivery' ? 'selected' : '' }}>Delivery</option>
                                <option value="other" {{ old('visit_type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('visit_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  name="notes" rows="3" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('visits.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Record Visit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug: Log the autocomplete URL
    console.log('Customer Search URL:', '{{ route("customers.autocomplete") }}');
    
    // Customer Search Functionality
    const searchInput = document.getElementById('customer-search-input');
    const searchResults = document.getElementById('customer-search-results');
    const searchLoading = document.getElementById('customer-search-loading');
    const searchClear = document.getElementById('customer-search-clear');
    const customerIdInput = document.getElementById('customer-id');
    const selectedCustomerBadge = document.getElementById('selected-customer-badge');
    const visitForm = document.getElementById('visit-form');
    
    const AUTOCOMPLETE_URL = '{{ route("customers.autocomplete") }}';
    const DEBOUNCE_DELAY = 300;
    
    let searchTimeout = null;
    let highlightedIndex = -1;
    let searchResultsData = [];
    let isInitialized = {{ $selectedCustomer ? 'true' : 'false' }};

    // Initialize - show clear button if customer is pre-selected
    updateClearButton();
    
    if (isInitialized) {
        searchInput.value = '{{ $selectedCustomer ? addslashes($selectedCustomer->name) : "" }}';
    }

    // Search input event handler
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        highlightedIndex = -1;
        
        // Update clear button visibility
        updateClearButton();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        if (query.length < 1) {
            hideResults();
            if (query.length === 0 && !isInitialized) {
                customerIdInput.value = '';
                selectedCustomerBadge.innerHTML = '';
            }
            return;
        }
        
        // Debounce the search
        searchTimeout = setTimeout(function() {
            performSearch(query);
        }, DEBOUNCE_DELAY);
    });

    // Focus handler - show results if there's a query
    searchInput.addEventListener('focus', function() {
        const query = this.value.trim();
        if (query.length >= 1 && searchResultsData.length > 0) {
            showResults();
        }
    });

    // Keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        if (searchResultsData.length === 0) return;
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                highlightedIndex = Math.min(highlightedIndex + 1, searchResultsData.length - 1);
                updateHighlight();
                break;
            case 'ArrowUp':
                e.preventDefault();
                highlightedIndex = Math.max(highlightedIndex - 1, -1);
                updateHighlight();
                break;
            case 'Enter':
                e.preventDefault();
                if (highlightedIndex >= 0 && highlightedIndex < searchResultsData.length) {
                    selectCustomer(searchResultsData[highlightedIndex]);
                }
                break;
            case 'Escape':
                hideResults();
                break;
        }
    });

    // Clear button click
    if (searchClear) {
        searchClear.addEventListener('click', function() {
            clearSelection();
            searchInput.focus();
        });
    }

    // Remove customer button click
    const removeCustomerBtn = document.getElementById('remove-customer');
    if (removeCustomerBtn) {
        removeCustomerBtn.addEventListener('click', function() {
            clearSelection();
            searchInput.focus();
        });
    }

    // Click outside to close results
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            hideResults();
        }
    });

    // Form validation - ensure customer is selected
    visitForm.addEventListener('submit', function(e) {
        if (!customerIdInput.value) {
            e.preventDefault();
            searchInput.classList.add('is-invalid');
            searchInput.focus();
            return false;
        }
    });

    // Remove invalid class on input
    searchInput.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });

    // Perform the search
    function performSearch(query) {
        console.log('Searching for:', query);
        searchLoading.classList.add('show');
        
        const url = AUTOCOMPLETE_URL + '?q=' + encodeURIComponent(query);
        console.log('Fetching URL:', url);
        
        fetch(url)
            .then(function(response) {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    return response.text().then(function(text) {
                        throw new Error('Network error: ' + response.status + ' - ' + text);
                    });
                }
                return response.json();
            })
            .then(function(data) {
                console.log('Response data:', data);
                if (Array.isArray(data)) {
                    searchResultsData = data;
                    renderResults(data);
                    if (data.length > 0) {
                        showResults();
                    } else {
                        showNoResults();
                    }
                } else if (data.error) {
                    console.error('Server error:', data.error);
                    searchResultsData = [];
                    showNoResults();
                } else {
                    console.warn('Unexpected response format:', data);
                    searchResultsData = [];
                    showNoResults();
                }
            })
            .catch(function(error) {
                console.error('Customer search error:', error);
                searchResultsData = [];
                showNoResults();
            })
            .finally(function() {
                searchLoading.classList.remove('show');
            });
    }

    // Render search results
    function renderResults(customers) {
        if (customers.length === 0) {
            showNoResults();
            return;
        }

        let html = '';
        customers.forEach(function(customer, index) {
            const details = [];
            if (customer.mobile) details.push('<i class="bi bi-phone"></i>' + customer.mobile);
            if (customer.email) details.push('<i class="bi bi-envelope"></i>' + customer.email);
            
            const pointsHtml = customer.points > 0 
                ? '<span class="badge bg-warning text-dark ms-2"><i class="bi bi-star-fill me-1"></i>' + customer.points + ' pts</span>'
                : '';

            html += '<div class="customer-search-item" data-index="' + index + '" data-id="' + customer.id + '" data-name="' + escapeHtml(customer.name) + '" data-mobile="' + escapeHtml(customer.mobile || '') + '" data-email="' + escapeHtml(customer.email || '') + '" data-points="' + customer.points + '">';
            html += '<div class="customer-search-name">' + escapeHtml(customer.name) + pointsHtml + '</div>';
            if (details.length > 0) {
                html += '<div class="customer-search-details">' + details.join(' <span class="mx-1">|</span> ') + '</div>';
            }
            html += '</div>';
        });

        searchResults.innerHTML = html;

        // Add click handlers
        searchResults.querySelectorAll('.customer-search-item').forEach(function(item) {
            item.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                selectCustomer(searchResultsData[index]);
            });
            
            item.addEventListener('mouseenter', function() {
                highlightedIndex = parseInt(this.getAttribute('data-index'));
                updateHighlight();
            });
        });
    }

    // Show "no results" message
    function showNoResults() {
        searchResults.innerHTML = '<div class="customer-search-item no-results"><i class="bi bi-search me-2"></i>No customers found</div>';
        showResults();
    }

    // Show results dropdown
    function showResults() {
        searchResults.classList.add('show');
    }

    // Hide results dropdown
    function hideResults() {
        searchResults.classList.remove('show');
        highlightedIndex = -1;
        updateHighlight();
    }

    // Update highlighted item
    function updateHighlight() {
        searchResults.querySelectorAll('.customer-search-item').forEach(function(item, index) {
            item.classList.remove('highlighted');
            if (index === highlightedIndex) {
                item.classList.add('highlighted');
                item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        });
    }

    // Select a customer
    function selectCustomer(customer) {
        customerIdInput.value = customer.id;
        searchInput.value = customer.name;
        searchResultsData = [];
        hideResults();
        isInitialized = true;
        updateClearButton();
        
        // Render selected customer badge
        const details = [];
        if (customer.mobile) details.push('<i class="bi bi-phone"></i>' + customer.mobile);
        if (customer.email) details.push('<span class="mx-2">|</span><i class="bi bi-envelope"></i>' + customer.email);
        if (customer.points > 0) details.push('<span class="mx-2">|</span><i class="bi bi-star-fill text-warning"></i>' + customer.points + ' pts');

        selectedCustomerBadge.innerHTML = '<div class="selected-customer-badge">' +
            '<i class="bi bi-person-fill text-primary" style="font-size: 1.25rem;"></i>' +
            '<div class="customer-info">' +
                '<div class="customer-name">' + escapeHtml(customer.name) + '</div>' +
                '<div class="customer-meta">' + details.join('') + '</div>' +
            '</div>' +
            '<span class="remove-btn" id="remove-customer" title="Remove selection">' +
                '<i class="bi bi-x-circle"></i>' +
            '</span>' +
        '</div>';
        
        // Re-attach remove button handler
        const newRemoveBtn = document.getElementById('remove-customer');
        if (newRemoveBtn) {
            newRemoveBtn.addEventListener('click', function() {
                clearSelection();
                searchInput.focus();
            });
        }
        
        // Remove invalid class if present
        searchInput.classList.remove('is-invalid');
    }

    // Clear customer selection
    function clearSelection() {
        customerIdInput.value = '';
        searchInput.value = '';
        searchResultsData = [];
        isInitialized = false;
        hideResults();
        updateClearButton();
        selectedCustomerBadge.innerHTML = '';
    }

    // Update clear button visibility
    function updateClearButton() {
        if (searchInput.value.length > 0 || isInitialized) {
            searchClear.classList.add('show');
        } else {
            searchClear.classList.remove('show');
        }
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endpush

