@extends('layouts.app')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('content')
{{-- Reports Header Section --}}
<div class="reports-header mb-5">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h2 class="reports-welcome mb-2">Reports & Analytics 📊</h2>
            <p class="reports-subtitle mb-0">Comprehensive insights into your hospitality business performance.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="reports-actions">
                <button class="btn btn-outline-primary me-2">
                    <i class="bi bi-download me-2"></i>Export All
                </button>
                <button class="btn btn-outline-primary">
                    <i class="bi bi-calendar3 me-2"></i>This Month
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Quick Stats Overview --}}
<div class="row g-4 mb-5">
    <div class="col-md-6 col-xl-3">
        @include('components.stat-card', [
            'value' => number_format(\App\Models\Customer::count()),
            'label' => 'Total Customers',
            'change' => \App\Models\Customer::where('created_at', '>=', now()->startOfMonth())->count() . ' new this month',
            'changeType' => 'positive',
            'iconClass' => 'bi bi-people',
            'iconBg' => '#E0E7FF',
            'iconColor' => '#6366F1'
        ])
    </div>

    <div class="col-md-6 col-xl-3">
        @include('components.stat-card', [
            'value' => number_format(\App\Models\Visit::count()),
            'label' => 'Total Visits',
            'change' => \App\Models\Visit::where('visited_at', '>=', now()->startOfMonth())->count() . ' this month',
            'changeType' => 'positive',
            'iconClass' => 'bi bi-calendar-check',
            'iconBg' => '#DCFCE7',
            'iconColor' => '#10B981'
        ])
    </div>

    <div class="col-md-6 col-xl-3">
        @include('components.stat-card', [
            'value' => number_format(\App\Models\LoyaltyWallet::count()),
            'label' => 'Active Wallets',
            'change' => number_format(\App\Models\LoyaltyPointLedger::where('type', 'earned')->sum('points')) . ' pts issued',
            'changeType' => 'neutral',
            'iconClass' => 'bi bi-wallet2',
            'iconBg' => '#FEF3C7',
            'iconColor' => '#F59E0B'
        ])
    </div>

    <div class="col-md-6 col-xl-3">
        @include('components.stat-card', [
            'value' => number_format(\App\Models\Reward::where('active', true)->count()),
            'label' => 'Active Rewards',
            'change' => number_format(\App\Models\LoyaltyPointLedger::where('type', 'redeemed')->sum('points')) . ' pts redeemed',
            'changeType' => 'neutral',
            'iconClass' => 'bi bi-gift',
            'iconBg' => '#DBEAFE',
            'iconColor' => '#3B82F6'
        ])
    </div>
</div>

{{-- Reports Section --}}
<div class="row g-4 mb-5">
    {{-- Customer Report --}}
    <div class="col-lg-4">
        <div class="report-card stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="report-icon" style="background: var(--primary-subtle); color: var(--primary-color);">
                    <i class="bi bi-people-fill"></i>
                </div>
                <span class="badge bg-primary-subtle text-primary">Analytics</span>
            </div>
            <h4 class="report-title mb-2">Customer Report</h4>
            <p class="report-description text-muted mb-4">Detailed customer analytics including demographics, acquisition trends, and engagement metrics.</p>
            <div class="report-stats mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Total Customers</span>
                    <span class="fw-semibold">{{ number_format(\App\Models\Customer::count()) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Active (30 days)</span>
                    <span class="fw-semibold">{{ number_format(\App\Models\Customer::whereHas('visits', function($q) {
                        $q->where('visited_at', '>=', now()->subDays(30));
                    })->count()) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">New This Month</span>
                    <span class="fw-semibold">{{ number_format(\App\Models\Customer::where('created_at', '>=', now()->startOfMonth())->count()) }}</span>
                </div>
            </div>
            <a href="{{ route('reports.customers') }}" class="btn btn-primary w-100">
                <i class="bi bi-arrow-right me-2"></i>View Customer Report
            </a>
        </div>
    </div>

    {{-- Visit Report --}}
    <div class="col-lg-4">
        <div class="report-card stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="report-icon" style="background: #DCFCE7; color: #10B981;">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <span class="badge bg-success-subtle text-success">Tracking</span>
            </div>
            <h4 class="report-title mb-2">Visit Report</h4>
            <p class="report-description text-muted mb-4">Track customer visits, frequency patterns, peak hours, and engagement metrics across all outlets.</p>
            <div class="report-stats mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Total Visits</span>
                    <span class="fw-semibold">{{ number_format(\App\Models\Visit::count()) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">This Month</span>
                    <span class="fw-semibold">{{ number_format(\App\Models\Visit::where('visited_at', '>=', now()->startOfMonth())->count()) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Avg per Customer</span>
                    <span class="fw-semibold">{{ round(\App\Models\Visit::count() / max(\App\Models\Customer::count(), 1), 2) }}</span>
                </div>
            </div>
            <a href="{{ route('reports.visits') }}" class="btn btn-success w-100">
                <i class="bi bi-arrow-right me-2"></i>View Visit Report
            </a>
        </div>
    </div>

    {{-- Loyalty Report --}}
    <div class="col-lg-4">
        <div class="report-card stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="report-icon" style="background: #DBEAFE; color: #3B82F6;">
                    <i class="bi bi-star-fill"></i>
                </div>
                <span class="badge bg-info-subtle text-info">Program</span>
            </div>
            <h4 class="report-title mb-2">Loyalty Report</h4>
            <p class="report-description text-muted mb-4">Comprehensive loyalty program analytics including point issuance, redemption rates, and reward performance.</p>
            <div class="report-stats mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Points Issued</span>
                    <span class="fw-semibold">{{ number_format(\App\Models\LoyaltyPointLedger::where('type', 'earned')->sum('points')) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Points Redeemed</span>
                    <span class="fw-semibold">{{ number_format(\App\Models\LoyaltyPointLedger::where('type', 'redeemed')->sum('points')) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Available Balance</span>
                    <span class="fw-semibold">{{ number_format(\App\Models\LoyaltyWallet::sum('points_balance')) }}</span>
                </div>
            </div>
            <a href="{{ route('reports.loyalty') }}" class="btn btn-info w-100" style="background: var(--info-color); border-color: var(--info-color); color: white;">
                <i class="bi bi-arrow-right me-2"></i>View Loyalty Report
            </a>
        </div>
    </div>
</div>

{{-- Additional Reports Section --}}
<div class="row g-4 mb-5">
    <div class="col-12">
        <x-data-card title="Quick Actions" :showActions="false">
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('import-export.index') }}" class="quick-action-card d-flex align-items-center p-3 text-decoration-none">
                        <div class="quick-action-icon me-3" style="background: #FEF3C7; color: #F59E0B;">
                            <i class="bi bi-file-earmark-arrow-up-down"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Import/Export</div>
                            <small class="text-muted">Manage data imports</small>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('dashboard') }}" class="quick-action-card d-flex align-items-center p-3 text-decoration-none">
                        <div class="quick-action-icon me-3" style="background: #E0E7FF; color: #6366F1;">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Dashboard</div>
                            <small class="text-muted">Back to overview</small>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('customers.index') }}" class="quick-action-card d-flex align-items-center p-3 text-decoration-none">
                        <div class="quick-action-icon me-3" style="background: #DCFCE7; color: #10B981;">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Customers</div>
                            <small class="text-muted">Manage customers</small>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('loyalty.rewards') }}" class="quick-action-card d-flex align-items-center p-3 text-decoration-none">
                        <div class="quick-action-icon me-3" style="background: #DBEAFE; color: #3B82F6;">
                            <i class="bi bi-gift"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Loyalty Rewards</div>
                            <small class="text-muted">Configure rewards</small>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
            </div>
        </x-data-card>
    </div>
</div>

{{-- Data Quality Section --}}
<div class="row g-4">
    <div class="col-lg-6">
        <x-data-card title="Report Categories">
            <div class="list-modern">
                <div class="list-item-modern d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people-chart me-3" style="color: var(--primary-color); font-size: 1.25rem;"></i>
                        <span class="fw-medium text-dark">Customer Analytics</span>
                    </div>
                    <span class="badge badge-status badge-active">3 Reports</span>
                </div>
                <div class="list-item-modern d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-check me-3" style="color: var(--success-color); font-size: 1.25rem;"></i>
                        <span class="fw-medium text-dark">Visit Tracking</span>
                    </div>
                    <span class="badge badge-status badge-active">2 Reports</span>
                </div>
                <div class="list-item-modern d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-star-fill me-3" style="color: var(--info-color); font-size: 1.25rem;"></i>
                        <span class="fw-medium text-dark">Loyalty Program</span>
                    </div>
                    <span class="badge badge-status badge-active">4 Reports</span>
                </div>
                <div class="list-item-modern d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-bar-chart me-3" style="color: var(--warning-color); font-size: 1.25rem;"></i>
                        <span class="fw-medium text-dark">Performance Metrics</span>
                    </div>
                    <span class="badge badge-pending">Coming Soon</span>
                </div>
            </div>
        </x-data-card>
    </div>

    <div class="col-lg-6">
        <x-data-card title="Last Updated">
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Report</th>
                            <th class="text-end">Records</th>
                            <th class="text-end">Last Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">Customer Report</div>
                            </td>
                            <td class="text-end fw-semibold">{{ number_format(\App\Models\Customer::count()) }}</td>
                            <td class="text-end text-muted small">Just now</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">Visit Report</div>
                            </td>
                            <td class="text-end fw-semibold">{{ number_format(\App\Models\Visit::count()) }}</td>
                            <td class="text-end text-muted small">Just now</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">Loyalty Report</div>
                            </td>
                            <td class="text-end fw-semibold">{{ number_format(\App\Models\LoyaltyWallet::count()) }}</td>
                            <td class="text-end text-muted small">Just now</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-data-card>
    </div>
</div>

<style>
    /* Reports Page Specific Styles */
    .reports-header {
        margin-top: 0;
    }

    .reports-welcome {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .reports-subtitle {
        color: var(--text-secondary);
        font-size: 1rem;
    }

    .report-card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .report-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .report-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .report-description {
        font-size: 0.875rem;
        line-height: 1.6;
        flex-grow: 1;
    }

    .report-stats {
        background: #F8FAFC;
        border-radius: 10px;
        padding: 1rem;
    }

    .quick-action-card {
        background: #F8FAFC;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .quick-action-card:hover {
        background: white;
        border-color: #E2E8F0;
        box-shadow: var(--shadow-md);
    }

    .quick-action-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .bg-primary-subtle {
        background-color: var(--primary-subtle) !important;
    }

    .bg-success-subtle {
        background-color: #DCFCE7 !important;
    }

    .bg-info-subtle {
        background-color: #DBEAFE !important;
    }

    @media (max-width: 991.98px) {
        .reports-header {
            margin-bottom: 1.5rem;
        }

        .reports-actions {
            margin-top: 1rem;
            text-align: left !important;
        }
    }
</style>
@endsection
