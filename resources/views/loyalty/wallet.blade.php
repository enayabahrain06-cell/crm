@extends('layouts.app')

@section('title', 'Wallet Details')
@section('page-title', 'Wallet: ' . $customer->name)

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ number_format($wallet->total_points) }}</div>
                    <div class="stat-label">Total Points</div>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-star-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value text-success">{{ number_format($wallet->available_points) }}</div>
                    <div class="stat-label">Available Points</div>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value text-danger">{{ number_format($wallet->redeemed_points) }}</div>
                    <div class="stat-label">Redeemed Points</div>
                </div>
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="bi bi-gift"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="user-avatar me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        {{ substr($customer->name, 0, 1) }}
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $customer->name }}</h5>
                        <p class="text-muted mb-0">{{ $customer->email ?: 'No email' }}</p>
                        @if($customer->mobile_json)
                        <small class="text-muted">{{ formatMobileNumber($customer->mobile_json) }}</small>
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Tier</small>
                        <div class="fw-semibold">
                            @php
                                $tierColors = [
                                    'bronze' => 'warning',
                                    'silver' => 'secondary',
                                    'gold' => 'warning',
                                    'platinum' => 'primary'
                                ];
                                $tierColor = $tierColors[$wallet->tier] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $tierColor }}">{{ ucfirst($wallet->tier) }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Visits</small>
                        <div class="fw-semibold">{{ $customer->visits_count ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Available Rewards</h5>
                <span class="badge bg-primary">{{ count($availableRewards) }}</span>
            </div>
            <div class="card-body">
                @forelse($availableRewards as $reward)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <div class="fw-semibold">{{ $reward->name }}</div>
                        <small class="text-muted">{{ number_format($reward->required_points) }} pts</small>
                    </div>
                    @can('rewards.redeem')
                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#redeemModal{{ $reward->id }}">
                        Redeem
                    </button>
                    @endcan
                </div>

                <!-- Redeem Modal -->
                <div class="modal fade" id="redeemModal{{ $reward->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Redeem: {{ $reward->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('loyalty.rewards.redeem', $reward) }}" method="POST">
                                @csrf
                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                <div class="modal-body">
                                    <p>Are you sure you want to redeem this reward for <strong>{{ $customer->name }}</strong>?</p>
                                    <p class="text-muted">Cost: {{ number_format($reward->required_points) }} points</p>
                                    <p>Available: {{ number_format($wallet->available_points) }} points</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-gift me-2"></i>Redeem
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3 mb-0">No rewards available for this customer</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Points Ledger</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Outlet</th>
                        <th class="text-end">Points</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledger as $entry)
                    <tr>
                        <td>{{ $entry->created_at->format('M d, Y H:i') }}</td>
                        <td>{{ $entry->description }}</td>
                        <td>{{ $entry->outlet->name ?? 'N/A' }}</td>
                        <td class="text-end">
                            @if($entry->points > 0)
                            <span class="text-success">+{{ number_format($entry->points) }}</span>
                            @else
                            <span class="text-danger">{{ number_format($entry->points) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <p class="text-muted mb-0">No ledger entries yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@can('loyalty_rules.edit')
<div class="mt-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adjustPointsModal">
        <i class="bi bi-plus-circle"></i> Adjust Points
    </button>
</div>

<!-- Adjust Points Modal -->
<div class="modal fade" id="adjustPointsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Points</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('loyalty.adjustPoints', $customer) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Points</label>
                        <input type="number" class="form-control @error('points') is-invalid @enderror" 
                               name="points" placeholder="Enter positive or negative number" required>
                        @error('points')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Use positive numbers to add points, negative to deduct</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror" 
                               name="description" placeholder="Reason for adjustment" required>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Adjust Points
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

