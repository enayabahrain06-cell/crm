@extends('layouts.app')

@section('title', 'Wallets')
@section('page-title', 'Loyalty Wallets')

@section('content')
{{-- Header --}}
<div class="row mb-4">
    <div class="col-md-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Wallets</li>
            </ol>
        </nav>
        <h4 class="mb-0">Loyalty Wallets</h4>
        <p class="text-muted mb-0">Manage customer loyalty points and tiers</p>
    </div>
    <div class="col-md-4 text-md-end">
        <div class="btn-group">
            <a href="{{ route('loyalty.wallets') }}" class="btn {{ !request('tier') ? 'btn-primary' : 'btn-outline-primary' }}">
                All Wallets
            </a>
            <a href="{{ route('loyalty.wallets', ['tier' => 'bronze']) }}" class="btn {{ request('tier') == 'bronze' ? 'btn-primary' : 'btn-outline-primary' }}">
                Bronze
            </a>
            <a href="{{ route('loyalty.wallets', ['tier' => 'silver']) }}" class="btn {{ request('tier') == 'silver' ? 'btn-primary' : 'btn-outline-primary' }}">
                Silver
            </a>
            <a href="{{ route('loyalty.wallets', ['tier' => 'gold']) }}" class="btn {{ request('tier') == 'gold' ? 'btn-primary' : 'btn-outline-primary' }}">
                Gold
            </a>
            <a href="{{ route('loyalty.wallets', ['tier' => 'platinum']) }}" class="btn {{ request('tier') == 'platinum' ? 'btn-primary' : 'btn-outline-primary' }}">
                Platinum
            </a>
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary-subtle border-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-star-fill fs-3 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ number_format($stats['total_points_issued']) }}</div>
                        <div class="text-muted small">Points Issued</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger-subtle border-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-star-half fs-3 text-danger"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ number_format($stats['total_points_redeemed']) }}</div>
                        <div class="text-muted small">Points Redeemed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success-subtle border-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-wallet2 fs-3 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ number_format($stats['total_wallets']) }}</div>
                        <div class="text-muted small">Total Wallets</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info-subtle border-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-layers fs-3 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">
                            @php
                                $tierCounts = $stats['wallets_by_tier'];
                                echo count($tierCounts) > 0 ? max($tierCounts) : 0;
                            @endphp
                        </div>
                        <div class="text-muted small">Top Tier Count</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tier Distribution --}}
<div class="row g-3 mb-4">
    @php
        $tiers = ['bronze' => ['color' => 'warning', 'icon' => 'bi-circle-fill', 'label' => 'Bronze'],
                  'silver' => ['color' => 'secondary', 'icon' => 'bi-circle-fill', 'label' => 'Silver'],
                  'gold' => ['color' => 'warning', 'icon' => 'bi-star-fill', 'label' => 'Gold'],
                  'platinum' => ['color' => 'primary', 'icon' => 'bi-gem', 'label' => 'Platinum']];
        $tierCounts = $stats['wallets_by_tier'];
        $totalWithTier = array_sum($tierCounts);
    @endphp
    @foreach($tiers as $tier => $config)
    @if(isset($tierCounts[$tier]))
    <div class="col-md-3">
        <div class="card border-0" style="background: var(--bs-{{ $config['color'] }}-subtle);">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi {{ $config['icon'] }} text-{{ $config['color'] }} fs-4 me-2"></i>
                        <div>
                            <div class="fw-bold">{{ $config['label'] }}</div>
                            <small class="text-muted">{{ number_format($tierCounts[$tier]) }} members</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">{{ $totalWithTier > 0 ? round(($tierCounts[$tier] / $totalWithTier) * 100) : 0 }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>

{{-- Filters & Search --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <form action="{{ route('loyalty.wallets') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Search customer name..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="tier">
                    <option value="">All Tiers</option>
                    <option value="bronze" {{ request('tier') === 'bronze' ? 'selected' : '' }}>Bronze</option>
                    <option value="silver" {{ request('tier') === 'silver' ? 'selected' : '' }}>Silver</option>
                    <option value="gold" {{ request('tier') === 'gold' ? 'selected' : '' }}>Gold</option>
                    <option value="platinum" {{ request('tier') === 'platinum' ? 'selected' : '' }}>Platinum</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="sort">
                    <option value="points_desc" {{ request('sort', 'points_desc') === 'points_desc' ? 'selected' : '' }}>Most Points</option>
                    <option value="points_asc" {{ request('sort') === 'points_asc' ? 'selected' : '' }}>Least Points</option>
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest First</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-primary me-2">Apply</button>
                <a href="{{ route('loyalty.wallets') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Results Count --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted">
        Showing <strong>{{ $wallets->count() }}</strong> of <strong>{{ $wallets->total() }}</strong> wallets
    </div>
</div>

{{-- Wallets Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Tier</th>
                        <th class="text-end">Total Points</th>
                        <th class="text-end">Available</th>
                        <th class="text-end">Redeemed</th>
                        <th class="text-center">Visits</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wallets as $wallet)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                    {{ substr($wallet->customer?->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $wallet->customer?->name ?? 'Unknown Customer' }}</div>
                                    <small class="text-muted">{{ $wallet->customer?->email ?: 'No email' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $tierColors = [
                                    'bronze' => ['bg' => 'bg-warning-subtle', 'text' => 'text-warning', 'icon' => 'bi-circle-fill'],
                                    'silver' => ['bg' => 'bg-secondary-subtle', 'text' => 'text-secondary', 'icon' => 'bi-circle-fill'],
                                    'gold' => ['bg' => 'bg-warning-subtle', 'text' => 'text-warning', 'icon' => 'bi-star-fill'],
                                    'platinum' => ['bg' => 'bg-primary-subtle', 'text' => 'text-primary', 'icon' => 'bi-gem-fill'],
                                ];
                                $tierStyle = $tierColors[$wallet->tier] ?? ['bg' => 'bg-secondary-subtle', 'text' => 'text-secondary', 'icon' => 'bi-circle'];
                            @endphp
                            <span class="badge {{ $tierStyle['bg'] }} {{ $tierStyle['text'] }}">
                                <i class="bi {{ $tierStyle['icon'] }} me-1"></i>
                                {{ ucfirst($wallet->tier) }}
                            </span>
                        </td>
                        <td class="text-end fw-semibold">{{ number_format($wallet->total_points) }}</td>
                        <td class="text-end">
                            <span class="text-success">{{ number_format($wallet->available_points) }}</span>
                        </td>
                        <td class="text-end text-muted">{{ number_format($wallet->redeemed_points) }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark">{{ $wallet->visits_count ?? 0 }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('loyalty.wallets.show', $wallet) }}" class="btn btn-outline-primary btn-sm" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($wallet->customer)
                                <a href="{{ route('customers.360', $wallet->customer) }}" class="btn btn-outline-secondary btn-sm" title="Customer 360">
                                    <i class="bi bi-circle"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-wallet2 display-4"></i>
                                <p class="mt-2 mb-0">No wallets found matching your criteria</p>
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
    {{ $wallets->appends(request()->query())->links() }}
</div>
@endsection

