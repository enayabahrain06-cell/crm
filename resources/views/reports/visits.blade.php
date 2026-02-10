@extends('layouts.app')

@section('title', 'Visit Report')
@section('page-title', 'Visit Report')

@section('content')
{{-- Report Header --}}
<div class="reports-header mb-4">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-2">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="reports-welcome mb-0">Visit Report 📊</h2>
            </div>
            <p class="reports-subtitle mb-0">Track customer visits, frequency patterns, and engagement metrics.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="reports-actions">
                <button class="btn btn-outline-primary me-2">
                    <i class="bi bi-download me-2"></i>Export
                </button>
                <button class="btn btn-success">
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
            'value' => number_format(\App\Models\Visit::where('visited_at', '>=', now()->startOfMonth())->count()),
            'label' => 'This Month',
            'change' => 'Current period',
            'changeType' => 'positive',
            'iconClass' => 'bi bi-calendar-month',
            'iconBg' => '#E0E7FF',
            'iconColor' => '#6366F1'
        ])
    </div>

    <div class="col-md-6 col-xl-3">
        @include('components.stat-card', [
            'value' => number_format(\App\Models\Visit::where('visited_at', '>=', now()->subDays(30))->count()),
            'label' => 'Last 30 Days',
            'change' => 'Recent activity',
            'changeType' => 'neutral',
            'iconClass' => 'bi bi-clock-history',
            'iconBg' => '#FEF3C7',
            'iconColor' => '#F59E0B'
        ])
    </div>

    <div class="col-md-6 col-xl-3">
        @include('components.stat-card', [
            'value' => number_format(\App\Models\Visit::count() / max(\App\Models\Customer::count(), 1), 2),
            'label' => 'Avg per Customer',
            'change' => 'Engagement rate',
            'changeType' => 'neutral',
            'iconClass' => 'bi bi-graph-up-arrow',
            'iconBg' => '#DBEAFE',
            'iconColor' => '#3B82F6'
        ])
    </div>
</div>

{{-- Data Table --}}
<x-data-card title="Visit Metrics">
    <x-slot name="actions">
        <input type="text" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;">
    </x-slot>
    <div class="table-responsive">
        <table class="table table-modern" id="visitsTable">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th class="text-end">Value</th>
                    <th class="text-end">Change</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Total Visits</div>
                        <small class="text-muted">All time recorded visits</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\Visit::count()) }}</td>
                    <td class="text-end">
                        <span class="badge badge-active">All time</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Visits This Month</div>
                        <small class="text-muted">{{ now()->format('F Y') }}</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\Visit::where('visited_at', '>=', now()->startOfMonth())->count()) }}</td>
                    <td class="text-end">
                        <span class="stat-change positive">
                            <i class="bi bi-arrow-up"></i> Current
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Visits Last 30 Days</div>
                        <small class="text-muted">Recent activity</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\Visit::where('visited_at', '>=', now()->subDays(30))->count()) }}</td>
                    <td class="text-end">
                        <span class="stat-change positive">
                            <i class="bi bi-activity"></i> Active
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Average Visits per Customer</div>
                        <small class="text-muted">Engagement metric</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ round(\App\Models\Visit::count() / max(\App\Models\Customer::count(), 1), 2) }}</td>
                    <td class="text-end">
                        <span class="stat-change neutral">
                            <i class="bi bi-dash"></i> Avg
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Unique Customers Visited</div>
                        <small class="text-muted">Customers with visits</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\Customer::whereHas('visits')->count()) }}</td>
                    <td class="text-end">
                        <span class="badge badge-success">Active</span>
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
</style>
@endsection

