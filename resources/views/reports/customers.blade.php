@extends('layouts.app')

@section('title', 'Customer Report')
@section('page-title', 'Customer Report')

@section('content')
{{-- Report Header --}}
<div class="reports-header mb-4">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-2">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="reports-welcome mb-0">Customer Report 📊</h2>
            </div>
            <p class="reports-subtitle mb-0">Detailed customer analytics and demographics overview.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="reports-actions">
                <button class="btn btn-outline-primary me-2">
                    <i class="bi bi-download me-2"></i>Export
                </button>
                <button class="btn btn-primary">
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
            'value' => number_format(\App\Models\Customer::whereHas('visits', function($q) {
                $q->where('visited_at', '>=', now()->subDays(30));
            })->count()),
            'label' => 'Active (30 days)',
            'change' => 'Engaged customers',
            'changeType' => 'positive',
            'iconClass' => 'bi bi-person-check',
            'iconBg' => '#DCFCE7',
            'iconColor' => '#10B981'
        ])
    </div>

    <div class="col-md-6 col-xl-3">
        @include('components.stat-card', [
            'value' => number_format(\App\Models\Customer::where('created_at', '>=', now()->startOfMonth())->count()),
            'label' => 'New This Month',
            'change' => 'Recent acquisitions',
            'changeType' => 'neutral',
            'iconClass' => 'bi bi-person-plus',
            'iconBg' => '#FEF3C7',
            'iconColor' => '#F59E0B'
        ])
    </div>

    <div class="col-md-6 col-xl-3">
        @include('components.stat-card', [
            'value' => number_format(\App\Models\Visit::count() / max(\App\Models\Customer::count(), 1), 2),
            'label' => 'Avg Visits/Customer',
            'change' => 'Engagement rate',
            'changeType' => 'neutral',
            'iconClass' => 'bi bi-graph-up',
            'iconBg' => '#DBEAFE',
            'iconColor' => '#3B82F6'
        ])
    </div>
</div>

{{-- Data Table --}}
<x-data-card title="Customer Metrics">
    <x-slot name="actions">
        <input type="text" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;">
    </x-slot>
    <div class="table-responsive">
        <table class="table table-modern" id="customersTable">
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
                        <div class="fw-semibold text-dark">Total Customers</div>
                        <small class="text-muted">All time registrations</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\Customer::count()) }}</td>
                    <td class="text-end">
                        <span class="badge badge-active">All time</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Active Customers</div>
                        <small class="text-muted">With visits in last 30 days</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\Customer::whereHas('visits', function($q) {
                        $q->where('visited_at', '>=', now()->subDays(30));
                    })->count()) }}</td>
                    <td class="text-end">
                        <span class="stat-change positive">
                            <i class="bi bi-arrow-up"></i> Active
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">New This Month</div>
                        <small class="text-muted">Joined {{ now()->format('F Y') }}</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\Customer::where('created_at', '>=', now()->startOfMonth())->count()) }}</td>
                    <td class="text-end">
                        <span class="stat-change positive">
                            <i class="bi bi-plus-circle"></i> Growth
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Corporate Customers</div>
                        <small class="text-muted">Business accounts</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\Customer::where('type', 'corporate')->count()) }}</td>
                    <td class="text-end">
                        <span class="badge badge-info">Business</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">Individual Customers</div>
                        <small class="text-muted">Personal accounts</small>
                    </td>
                    <td class="text-end fw-bold fs-5">{{ number_format(\App\Models\Customer::where('type', 'individual')->count()) }}</td>
                    <td class="text-end">
                        <span class="badge badge-success">Personal</span>
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
    .badge-info {
        background-color: #DBEAFE;
        color: #1D4ED8;
    }
</style>
@endsection

