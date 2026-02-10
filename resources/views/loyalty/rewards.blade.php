@extends('layouts.app')

@section('title', 'Rewards')
@section('page-title', 'Loyalty Rewards')

@section('content')
{{-- Header with Stats --}}
<div class="row mb-4">
    <div class="col-md-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Rewards</li>
            </ol>
        </nav>
        <h4 class="mb-0">Loyalty Rewards</h4>
        <p class="text-muted mb-0">Manage your loyalty program rewards and redemption options</p>
    </div>
    <div class="col-md-4 text-md-end">
        @can('rewards.create')
        <a href="{{ route('loyalty.rewards.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Add Reward
        </a>
        @endcan
    </div>
</div>

{{-- Quick Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary-subtle border-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-gift fs-3 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ $rewards->count() }}</div>
                        <div class="text-muted small">Total Rewards</div>
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
                        <i class="bi bi-check-circle fs-3 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ $rewards->where('active', true)->count() }}</div>
                        <div class="text-muted small">Active</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-secondary-subtle border-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-pause-circle fs-3 text-secondary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ $rewards->where('active', false)->count() }}</div>
                        <div class="text-muted small">Inactive</div>
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
                        <i class="bi bi-star fs-3 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ number_format($rewards->avg('required_points') ?? 0) }}</div>
                        <div class="text-muted small">Avg Points</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Search rewards..." id="searchRewards">
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterStatus">
                    <option value="">All Status</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterSort">
                    <option value="name">Sort by Name</option>
                    <option value="points_asc">Points (Low to High)</option>
                    <option value="points_desc">Points (High to Low)</option>
                    <option value="newest">Newest First</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <span class="text-muted" id="rewardCount">{{ $rewards->count() }} rewards</span>
            </div>
        </div>
    </div>
</div>

{{-- Rewards Grid --}}
@forelse($rewards as $reward)
<div class="card reward-card mb-3" data-name="{{ strtolower($reward->name) }}" data-status="{{ $reward->active ? 'active' : 'inactive' }}">
    <div class="card-body">
        <div class="row align-items-center">
            {{-- Reward Icon & Name --}}
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <div class="reward-icon rounded-circle d-flex align-items-center justify-content-center me-3
                        {{ $reward->active ? 'bg-success-subtle' : 'bg-secondary-subtle' }}
                        {{ $reward->active ? 'text-success' : 'text-secondary' }}"
                        style="width: 50px; height: 50px;">
                        <i class="bi {{ $reward->active ? 'bi-gift-fill' : 'bi-gift' }} fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $reward->name }}</h6>
                        <small class="text-muted">{{ $reward->description ? Str::limit($reward->description, 40) : 'No description' }}</small>
                    </div>
                </div>
            </div>

            {{-- Points Required --}}
            <div class="col-md-2 text-center">
                <span class="h5 mb-0 text-primary">{{ number_format($reward->required_points) }}</span>
                <small class="text-muted d-block">points</small>
            </div>

            {{-- Validity --}}
            <div class="col-md-2 text-center">
                @if($reward->valid_from || $reward->valid_to)
                    @if($reward->valid_from && $reward->valid_to)
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($reward->valid_from)->format('M d') }} - {{ \Carbon\Carbon::parse($reward->valid_to)->format('M d, Y') }}
                        </small>
                    @elseif($reward->valid_from)
                        <small class="text-muted">From {{ \Carbon\Carbon::parse($reward->valid_from)->format('M d, Y') }}</small>
                    @else
                        <small class="text-muted">Until {{ \Carbon\Carbon::parse($reward->valid_to)->format('M d, Y') }}</small>
                    @endif
                @else
                    <small class="text-muted">No expiry</small>
                @endif
            </div>

            {{-- Status Badge --}}
            <div class="col-md-1 text-center">
                @if($reward->active)
                    <span class="badge bg-success-subtle text-success">Active</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                @endif
            </div>

            {{-- Actions --}}
            <div class="col-md-3 text-end">
                <div class="btn-group">
                    <a href="{{ route('loyalty.rewards.show', $reward) }}" class="btn btn-outline-primary btn-sm" title="View Details">
                        <i class="bi bi-eye"></i>
                    </a>
                    @can('rewards.redeem')
                    <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#redeemModal{{ $reward->id }}" title="Redeem">
                        <i class="bi bi-gift"></i>
                    </button>
                    @endcan
                    @can('rewards.delete')
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $reward->id }}" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Redeem Modal -->
<div class="modal fade" id="redeemModal{{ $reward->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Redeem Reward: {{ $reward->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('loyalty.rewards.redeem', $reward) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>{{ number_format($reward->required_points) }} points</strong> required for this reward
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Customer</label>
                        <select class="form-select" name="customer_id" required>
                            <option value="">Choose a customer...</option>
                            @foreach(\App\Models\Customer::whereHas('loyaltyWallet')->with('loyaltyWallet')->get() as $customer)
                            <option value="{{ $customer->id }}" {{ $customer->loyaltyWallet->available_points < $reward->required_points ? 'disabled' : '' }}>
                                {{ $customer->name }} ({{ number_format($customer->loyaltyWallet->available_points) }} pts)
                                {{ $customer->loyaltyWallet->available_points < $reward->required_points ? '- Insufficient points' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-gift me-2"></i>Redeem Reward
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal{{ $reward->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Reward</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-exclamation-triangle text-warning display-4"></i>
                </div>
                <p class="text-center">Are you sure you want to delete <strong>{{ $reward->name }}</strong>?</p>
                <p class="text-danger text-center mb-0"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('loyalty.rewards.destroy', $reward) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Reward
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@empty
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-gift display-3 text-muted"></i>
        <h5 class="mt-3 mb-2">No rewards yet</h5>
        <p class="text-muted mb-4">Create your first loyalty reward to get started</p>
        @can('rewards.create')
        <a href="{{ route('loyalty.rewards.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Create First Reward
        </a>
        @endcan
    </div>
</div>
@endforelse

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchRewards');
    const statusFilter = document.getElementById('filterStatus');
    const sortFilter = document.getElementById('filterSort');
    const cards = document.querySelectorAll('.reward-card');

    function filterCards() {
        const searchTerm = searchInput.value.toLowerCase();
        const status = statusFilter.value;
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.dataset.name;
            const cardStatus = card.dataset.status;
            const matchesSearch = name.includes(searchTerm);
            const matchesStatus = !status || cardStatus === status;

            if (matchesSearch && matchesStatus) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        document.getElementById('rewardCount').textContent = visibleCount + ' rewards';
    }

    searchInput.addEventListener('input', filterCards);
    statusFilter.addEventListener('change', filterCards);
    sortFilter.addEventListener('change', function() {
        const cardsArray = Array.from(cards);
        const sortBy = sortFilter.value;

        cardsArray.sort((a, b) => {
            if (sortBy === 'points_asc') {
                return parseInt(a.querySelector('.h5').textContent.replace(/,/g, '')) - 
                       parseInt(b.querySelector('.h5').textContent.replace(/,/g, ''));
            } else if (sortBy === 'points_desc') {
                return parseInt(b.querySelector('.h5').textContent.replace(/,/g, '')) - 
                       parseInt(a.querySelector('.h5').textContent.replace(/,/g, ''));
            }
            return 0;
        });

        cardsArray.forEach(card => card.parentNode.appendChild(card));
    });
});
</script>
@endpush
@endsection

