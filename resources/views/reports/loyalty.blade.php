@extends('layouts.app')

@section('title', 'Loyalty Report')
@section('page-title', 'Loyalty Report')

@section('content')
{{-- Report Header --}}
<div class="reports-header mb-4">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-2">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="reports-welcome mb-0">Loyalty Report ⭐</h2>
            </div>
            <p class="reports-subtitle mb-0">Comprehensive loyalty program analytics and point statistics.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="reports-actions">
                <button class="btn btn-outline-primary me-2">
                    <i class="bi bi-download me-2"></i>Export
                </button>
                <button class="btn btn-info" style="background: var(--info-color); border-color: var(--info-color);">
                    <i class="bi bi-calendar3 me-2"></i>This Month
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div class="row g-4 mb-5">
    <div class="col-md-6 col-xl-3">
        @include('components.stat-card', [
            'value' => number_format(\App\Models\LoyaltyWallet::count()),
            'label' => 'Active Wallets',
            'change' => 'Total participants',
            'changeType' => 'positive',
            'iconClass' => 'bi bi-wallet2',
            'iconBg' => '#DBEAFE',
            'iconColor' => '#3B82F6'
        ])
    </div>

    <div class="col-md-6 col-xl-3">
        @include('components.stat-card', [
            'value' => number_format(\App\Models\LoyaltyPointLedger::where('type', 'earned')->sum('points')),
            'label' => 'Points Issued',
            'change' => 'All time earnings',
            'changeType' => 'positive',
            'iconClass' => 'bi bi-plus-circle',
            'iconBg' => '#DCFCE7',
            'iconColor' => '#10B981'
        ])
    </div>

    <div class="col-md-6 col-xl-3">
        @include('components.stat-card', [
            'value' => number_format(\App\Models\LoyaltyPointLedger::where('type', 'redeemed')->sum('points')),
            'label' => 'Points Redeemed',
            'change' => 'Total redemptions',
            'changeType' => 'neutral',
            'iconClass' => 'bi bi-dash-circle',
            'iconBg' => '#FEE2E2',
            'iconColor' => '#EF4444'
        ])
    </div>

    <div class="col-md-6 col-xl-3">
        @include('components.stat-card', [
            'value' => number_format(\App\Models\LoyaltyWallet::sum('points_balance')),
            'label' => 'Available Balance',
            'change' => 'Current points',
            'changeType' => 'neutral',
            'iconClass' => 'bi bi-star-fill',
            'iconBg' => '#FEF3C7',
            'iconColor' => '#F59E0B'
        ])
    </div>
</div>

{{-- Data Table --}}
<x-data-card title="Loyalty Program Metrics">
    <x-slot name="actions">
        <input type="text" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;">
    </x-slot>
    <div class="table-responsive">
        <table class="table table-modern" id="loyaltyTable">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th class="text-end">Value</th>
                    <th class="text-end">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Active Wallets</div>
                        <small class="text-muted">Customers with loyalty accounts</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\LoyaltyWallet::count()) }}</td>
                    <td class="text-end">
                        <span class="badge badge-active">Active</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Total Points Issued</div>
                        <small class="text-muted">All earned points</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\LoyaltyPointLedger::where('type', 'earned')->sum('points')) }}</td>
                    <td class="text-end">
                        <span class="badge bg-success-subtle text-success">Earned</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Total Points Redeemed</div>
                        <small class="text-muted">Points used for rewards</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\LoyaltyPointLedger::where('type', 'redeemed')->sum('points')) }}</td>
                    <td class="text-end">
                        <span class="badge bg-danger-subtle text-danger">Redeemed</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Available Points Balance</div>
                        <small class="text-muted">Current point balance</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\LoyaltyWallet::sum('points_balance')) }}</td>
                    <td class="text-end">
                        <span class="badge bg-warning-subtle text-warning">Balance</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Active Rewards</div>
                        <small class="text-muted">Available reward options</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\Reward::where('active', true)->count()) }}</td>
                    <td class="text-end">
                        <span class="badge badge-info">Rewards</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Redemption Rate</div>
                        <small class="text-muted">Points redeemed vs issued</small>
                    </td>
                    <td class="text-end fw-bold fs-5">
                        @php
                        $issued = \App\Models\LoyaltyPointLedger::where('type', 'earned')->sum('points');
                        $redeemed = \App\Models\LoyaltyPointLedger::where('type', 'redeemed')->sum('points');
                        $rate = $issued > 0 ? round(($redeemed / $issued) * 100, 1) : 0;
                        echo $rate . '%';
                        @endphp
                    </td>
                    <td class="text-end">
                        <span class="stat-change {{ $rate > 50 ? 'positive' : 'neutral' }}">
                            <i class="bi bi-{{ $rate > 50 ? 'graph-up' : 'dash' }}"></i> {{ $rate }}%
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</x-data-card>

<style>
    .reports-header {
        margin-top: 0;
    }
    .reports-welcome {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }
    .reports-subtitle {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }
    .reports-actions {
        margin-top: 0;
    }
    .bg-danger-subtle {
        background-color: #FEE2E2 !important;
    }
    .bg-warning-subtle {
        background-color: #FEF3C7 !important;
    }
    .badge-info {
        background-color: #DBEAFE;
        color: #1D4ED8;
    }
</style>
@endsection

