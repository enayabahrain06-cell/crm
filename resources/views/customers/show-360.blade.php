@extends('layouts.app')

@section('title', 'Customer 360° - ' . $profile->name)
@section('page-title', 'Customer 360° View')

@section('content')
<!-- Customer Header -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="user-avatar" style="width: 80px; height: 80px; font-size: 2rem;">
                    {{ substr($profile->name, 0, 1) }}
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <h2 class="mb-0">{{ $profile->name }}</h2>
                    <span class="badge bg-{{ $profile->type === 'corporate' ? 'info' : 'success' }} fs-6">
                        {{ ucfirst($profile->type) }}
                    </span>
                    <span class="badge badge-status badge-{{ $profile->status === 'active' ? 'active' : ($profile->status === 'inactive' ? 'inactive' : 'pending') }} fs-6">
                        {{ ucfirst($profile->status) }}
                    </span>
                </div>
                @if($profile->company_name)
                <div class="text-muted mb-2">
                    <i class="bi bi-building me-2"></i>{{ $profile->company_name }}
                    @if($profile->position)
                    | {{ $profile->position }}
                    @endif
                </div>
                @endif
                <div class="d-flex gap-4 text-muted">
                    @if($profile->email)
                    <span><i class="bi bi-envelope me-2"></i>{{ $profile->email }}</span>
                    @endif
                    @if($profile->mobile_json)
                    <span><i class="bi bi-phone me-2"></i>{{ formatMobileNumber($profile->mobile_json) }}</span>
                    @endif
                    @if($profile->nationality)
                    <span><i class="bi bi-globe me-2"></i>{{ getCountryName($profile->nationality) }}</span>
                    @endif
                </div>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    @can('customers.edit')
                    <a href="{{ route('customers.edit', $profile) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    @endcan
                    @can('visits.create')
                    <a href="{{ route('visits.create.with.customer', $profile->id) }}" class="btn btn-success">
                        <i class="bi bi-plus-lg me-2"></i>Log Visit
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-star-fill"></i>
            </div>
            <div class="stat-value">{{ number_format($profile->wallet?->total_points ?? 0) }}</div>
            <div class="stat-label">Current Points</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-subtle text-success">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="stat-value">{{ $profile->visits->count() }}</div>
            <div class="stat-label">Total Visits</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="stat-value">{{ number_format($profile->visits->sum('bill_amount'), 3) }} BHD</div>
            <div class="stat-label">Total Spend</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-subtle text-info">
                <i class="bi bi-shop"></i>
            </div>
            <div class="stat-value">{{ $profile->visits->unique('outlet_id')->count() }}</div>
            <div class="stat-label">Outlets Visited</div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#timeline" type="button">
            <i class="bi bi-clock-history me-2"></i>Timeline
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#visits" type="button">
            <i class="bi bi-calendar-check me-2"></i>Visits & Spend
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#loyalty" type="button">
            <i class="bi bi-star me-2"></i>Loyalty & Points
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#notes" type="button">
            <i class="bi bi-sticky me-2"></i>Notes
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- Timeline Tab -->
    <div class="tab-pane fade show active" id="timeline">
        <div class="card">
            <div class="card-body">
                <div class="timeline">
                    @forelse($timeline as $item)
                    <div class="timeline-item mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-start">
                            <div class="timeline-icon me-3">
                                @if($item['type'] === 'visit')
                                <i class="bi bi-calendar-check text-success"></i>
                                @elseif($item['type'] === 'points')
                                <i class="bi bi-star text-warning"></i>
                                @elseif($item['type'] === 'redemption')
                                <i class="bi bi-gift text-info"></i>
                                @elseif($item['type'] === 'campaign')
                                <i class="bi bi-megaphone text-primary"></i>
                                @else
                                <i class="bi bi-circle text-secondary"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <span class="fw-semibold">{{ $item['title'] }}</span>
                                        @if(isset($item['outlet']))
                                        <span class="text-muted"> at {{ $item['outlet'] }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $item['date']->diffForHumans() }}</small>
                                </div>
                                @if(isset($item['description']))
                                <p class="text-muted mb-0 small">{{ $item['description'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-clock-history display-4"></i>
                        <p class="mt-2">No activity yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Visits Tab -->
    <div class="tab-pane fade" id="visits">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Visit History</span>
                @can('visits.create')
                <a href="{{ route('visits.create.with.customer', $profile->id) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> Log Visit
                </a>
                @endcan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Outlet</th>
                                <th>Type</th>
                                <th class="text-end">Bill Amount</th>
                                <th>Items</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visits as $visit)
                            <tr>
                                <td>{{ $visit->visited_at->format('M d, Y') }}</td>
                                <td>{{ $visit->outlet->name }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($visit->visit_type) }}</span>
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($visit->bill_amount, 3) }} BHD</td>
                                <td>
                                    @if($visit->items_json)
                                    <small class="text-muted">
                                        {{ count($visit->items_json) }} items
                                    </small>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No visits recorded yet
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Loyalty Tab -->
    <div class="tab-pane fade" id="loyalty">
        <div class="row g-4">
            <!-- Wallet Summary -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header">Wallet Summary</div>
                    <div class="card-body">
                        @if($profile->wallet)
                        <div class="mb-3">
                            <div class="text-muted small">Total Points</div>
                            <div class="h3 mb-0">{{ number_format($profile->wallet->total_points) }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Points Earned</div>
                            <div class="text-success">+{{ number_format($profile->wallet->points_earned) }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Points Redeemed</div>
                            <div class="text-danger">-{{ number_format($profile->wallet->points_redeemed) }}</div>
                        </div>
                        <div>
                            <div class="text-muted small">Points Expired</div>
                            <div class="text-secondary">-{{ number_format($profile->wallet->points_expired) }}</div>
                        </div>
                        @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-wallet2 display-4"></i>
                            <p class="mt-2">No wallet yet</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Point Ledger -->
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header">Point Ledger</div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th class="text-end">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loyalty['ledger'] as $entry)
                                    <tr>
                                        <td>{{ $entry->created_at->format('M d, Y') }}</td>
                                        <td>
                                            {{ $entry->description }}
                                            @if($entry->outlet)
                                            <small class="text-muted"> ({{ $entry->outlet->name }})</small>
                                            @endif
                                        </td>
                                        <td class="text-end fw-semibold {{ $entry->points >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $entry->points >= 0 ? '+' : '' }}{{ number_format($entry->points) }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            No point transactions yet
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Tab -->
    <div class="tab-pane fade" id="notes">
        <div class="card">
            <div class="card-header">Internal Notes</div>
            <div class="card-body">
                <form action="#" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <textarea class="form-control" name="note" rows="3" placeholder="Add a note..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Add Note
                    </button>
                </form>

                <div class="list-group list-group-flush">
                    @forelse($profile->notes ?? [] as $note)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div class="fw-semibold">{{ $note['author'] }}</div>
                            <small class="text-muted">{{ $note['date'] }}</small>
                        </div>
                        <p class="mb-0 mt-2">{{ $note['content'] }}</p>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-sticky display-4"></i>
                        <p class="mt-2">No notes yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
