@extends('layouts.app')

@section('title', 'Visit Details')
@section('page-title', 'Visit Details')

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
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Visit Details</h5>
                <a href="{{ route('visits.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-2"></i>Back to Visits
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Customer</h6>
                        @if($visit->customer)
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-3" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                {{ substr($visit->customer->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $visit->customer->name }}</div>
                                @if($visit->customer->email)
                                <small class="text-muted">{{ $visit->customer->email }}</small>
                                @endif
                            </div>
                        </div>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Outlet</h6>
                        @if($visit->outlet)
                        <span class="badge bg-info fs-6">{{ $visit->outlet->name }}</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Staff Member</h6>
                        @if($visit->staff)
                        <div class="fw-semibold">{{ $visit->staff->name }}</div>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Visit Type</h6>
                        @if($visit->visit_type)
                        <span class="badge bg-{{ $visit->visit_type === 'dine_in' ? 'primary' : ($visit->visit_type === 'takeaway' ? 'warning' : ($visit->visit_type === 'delivery' ? 'success' : 'secondary')) }}">
                            {{ ucfirst(str_replace('_', ' ', $visit->visit_type)) }}
                        </span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Bill Amount</h6>
                        <div class="fs-5 fw-semibold">
                            @if($visit->bill_amount)
                            {{ $currencySymbol }}{{ number_format($visit->bill_amount, 3) }}
                            @else
                            -
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Points Awarded</h6>
                        <div class="fs-5 fw-semibold">
                            @if($visit->points_awarded)
                            <span class="badge bg-success fs-6">{{ $visit->points_awarded }} pts</span>
                            @else
                            -
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Visit Date & Time</h6>
                        <div class="fw-semibold">
                            {{ $visit->visited_at->format('M d, Y') }}
                        </div>
                        <small class="text-muted">{{ $visit->visited_at->format('h:i A') }}</small>
                    </div>
                </div>

                @if($visit->notes)
                <hr>
                <div class="mb-0">
                    <h6 class="text-muted mb-2">Notes</h6>
                    <p class="mb-0">{{ $visit->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

