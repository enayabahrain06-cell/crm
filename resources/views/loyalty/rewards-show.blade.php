@extends('layouts.app')

@section('title', $reward->name)
@section('page-title', $reward->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Reward Details</h5>
                <div class="d-flex gap-2">
                    @can('rewards.redeem')
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#redeemModal">
                        <i class="bi bi-gift me-2"></i>Redeem
                    </button>
                    @endcan
                    <a href="{{ route('loyalty.rewards') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h3 class="mb-1">{{ $reward->name }}</h3>
                        @if($reward->active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                    <div class="text-end">
                        <span class="h2 text-primary">{{ number_format($reward->required_points) }}</span>
                        <span class="text-muted">points required</span>
                    </div>
                </div>

                @if($reward->description)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Description</h6>
                    <p>{{ $reward->description }}</p>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-muted mb-1">Validity Period</h6>
                            @if($reward->valid_from && $reward->valid_to)
                            <p class="mb-0">
                                <i class="bi bi-calendar-event me-2"></i>
                                {{ \Carbon\Carbon::parse($reward->valid_from)->format('M d, Y') }} - 
                                {{ \Carbon\Carbon::parse($reward->valid_to)->format('M d, Y') }}
                            </p>
                            @elseif($reward->valid_from)
                            <p class="mb-0">
                                <i class="bi bi-calendar-event me-2"></i>
                                From {{ \Carbon\Carbon::parse($reward->valid_from)->format('M d, Y') }}
                            </p>
                            @elseif($reward->valid_to)
                            <p class="mb-0">
                                <i class="bi bi-calendar-event me-2"></i>
                                Until {{ \Carbon\Carbon::parse($reward->valid_to)->format('M d, Y') }}
                            </p>
                            @else
                            <p class="mb-0 text-muted">No validity restriction</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-muted mb-1">Redemptions</h6>
                            @if($reward->max_redemptions)
                            <p class="mb-0">
                                {{ number_format($reward->current_redemptions) }} / {{ number_format($reward->max_redemptions) }}
                                <span class="text-muted">({{ round(($reward->current_redemptions / $reward->max_redemptions) * 100, 1) }}% used)</span>
                            </p>
                            @else
                            <p class="mb-0">Unlimited redemptions</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Redeem Modal -->
<div class="modal fade" id="redeemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Redeem Reward: {{ $reward->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('loyalty.rewards.redeem', $reward) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Select a customer to redeem this reward for:</p>
                    <div class="mb-3">
                        <label class="form-label">Customer</label>
                        <select class="form-select" name="customer_id" required>
                            <option value="">Select a customer</option>
                            @foreach(\App\Models\Customer::whereHas('loyaltyWallet')->get() as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ number_format($customer->loyaltyWallet->available_points) }} pts)</option>
                            @endforeach
                        </select>
                    </div>
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
@endsection

