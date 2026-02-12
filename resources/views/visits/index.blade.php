@extends('layouts.app')

@section('title', 'Visits')
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

@section('content')
{{-- Stats Row --}}
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

{{-- Search & Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('visits.index') }}" method="GET" id="visit-search-form">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Outlet</label>
                    <select class="form-select" name="outlet_id" id="outlet-filter">
                        <option value="">All Outlets</option>
                        @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>
                            {{ $outlet->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" id="start-date-filter" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" id="end-date-filter" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search visits..." autocomplete="off">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary w-100" title="Reset filters">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Results Header --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <div class="text-muted" id="results-count">
            Showing <strong>{{ $visits->count() }}</strong> of <strong>{{ $visits->total() }}</strong> visits
        </div>
    </div>
    <div class="btn-group">
        <a href="{{ route('visits.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Record Visit
        </a>
        <button class="btn btn-outline-primary" data-bs-toggle="dropdown">
            <i class="bi bi-download"></i> Export
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('visits.export', array_merge(request()->query(), ['format' => 'csv'])) }}">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i>CSV
            </a></li>
            <li><a class="dropdown-item" href="{{ route('visits.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}">
                <i class="bi bi-file-earmark-excel me-2"></i>Excel
            </a></li>
            <li><a class="dropdown-item" href="{{ route('visits.export-pdf', request()->query()) }}">
                <i class="bi bi-file-earmark-pdf me-2"></i>PDF
            </a></li>
        </ul>
    </div>
</div>

{{-- Visits Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Outlet</th>
                        <th>Staff</th>
                        <th class="text-end">Bill Amount</th>
                        <th class="text-center">Points</th>
                        <th>Visited At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="visits-table-body">
                    @forelse($visits as $visit)
                    <tr>
                        <td>
                            @if($visit->customer)
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3" style="width: 36px; height: 36px; font-size: 0.875rem;">
                                    {{ substr($visit->customer->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $visit->customer->name }}</div>
                                    <small class="text-muted">{{ $visit->customer->mobile_json ? formatMobileNumber($visit->customer->mobile_json) : '-' }}</small>
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
                                <span>{{ $visit->staff->name }}</span>
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
                            <div>{{ $visit->visited_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $visit->visited_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('visits.show', $visit) }}">
                                        <i class="bi bi-eye me-2"></i>View
                                    </a></li>
                                    @can('visits.delete')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('visits.destroy', $visit) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
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
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-calendar-check display-4"></i>
                                <p class="mt-2">No visits found</p>
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
<div class="mt-4">
    {{ $visits->appends(request()->query())->links() }}
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Visit Page: Initialized');
    
    const searchForm = document.getElementById('visit-search-form');
    const searchInputs = searchForm ? searchForm.querySelectorAll('input[type="text"], input[type="date"], select') : [];
    
    // Handle form submission
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            // Form submission works normally for page reload with filters
            console.log('Visit Search: Form submitted');
        });
    }
    
    // Handle select changes
    searchInputs.forEach(function(input) {
        if (input.tagName === 'SELECT' || input.type === 'date') {
            input.addEventListener('change', function() {
                searchForm.submit();
            });
        }
    });
    
    console.log('Visit Page: Ready');
});
</script>
@endpush

