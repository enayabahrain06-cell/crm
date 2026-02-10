@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

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
{{-- Dashboard Header Section --}}
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h2 class="dashboard-welcome mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h2>
            <p class="dashboard-subtitle mb-0">Here's what's happening with your hospitality business today.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="dashboard-actions">
                <a href="{{ route('customers.create') }}" class="btn btn-primary me-2">
                    <i class="bi bi-plus-lg me-2"></i>Add Customer
                </a>
                <a href="{{ route('visits.create') }}" class="btn btn-outline-primary">
                    <i class="bi bi-calendar-plus me-2"></i>Log Visit
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Key Metrics Section --}}
<div class="row g-4 mb-5">
    <div class="col-md-6 col-xl-3">
        <a href="{{ route('customers.index') }}" class="text-decoration-none">
            @include('components.stat-card', [
                'value' => number_format($summary['customers']['total'] ?? 0),
                'label' => 'Total Customers',
                'change' => ($summary['customers']['new_this_period'] ?? 0) . ' this month',
                'changeType' => 'positive',
                'iconClass' => 'bi bi-people',
                'iconBg' => '#E0E7FF',
                'iconColor' => '#6366F1'
            ])
        </a>
    </div>

    <div class="col-md-6 col-xl-3">
        <a href="{{ route('visits.index') }}" class="text-decoration-none">
            @include('components.stat-card', [
                'value' => number_format($summary['visits']['total_this_period'] ?? 0),
                'label' => 'Total Visits',
                'change' => number_format($summary['visits']['total_this_period'] ?? 0) . ' this period',
                'changeType' => 'positive',
                'iconClass' => 'bi bi-calendar-check',
                'iconBg' => '#DCFCE7',
                'iconColor' => '#10B981'
            ])
        </a>
    </div>

    <div class="col-md-6 col-xl-3">
        <a href="{{ route('reports.loyalty') }}" class="text-decoration-none">
            @include('components.stat-card', [
                'value' => $currencySymbol . number_format($summary['visits']['total_spend'] ?? 0, 3),
                'label' => 'Total Revenue',
                'change' => $currencySymbol . number_format($summary['visits']['total_spend'] ?? 0, 3) . ' this period',
                'changeType' => 'positive',
                'iconClass' => 'bi bi-cash-stack',
                'iconBg' => '#FEF3C7',
                'iconColor' => '#F59E0B'
            ])
        </a>
    </div>

    <div class="col-md-6 col-xl-3">
        <a href="{{ route('loyalty.wallets') }}" class="text-decoration-none">
            @include('components.stat-card', [
                'value' => number_format($summary['loyalty']['points_issued'] ?? 0),
                'label' => 'Points Issued',
                'change' => number_format($summary['loyalty']['points_redeemed'] ?? 0) . ' redeemed',
                'changeType' => 'neutral',
                'iconClass' => 'bi bi-star-fill',
                'iconBg' => '#DBEAFE',
                'iconColor' => '#3B82F6'
            ])
        </a>
    </div>
</div>

{{-- Analytics Section --}}
<div class="row g-4 mb-5">
    {{-- Gender Distribution --}}
    <div class="col-lg-4">
        <x-data-card title="Gender Distribution">
            @php
            $genderGroups = $demographics['genders'] ?? [];
            $totalGender = array_sum($genderGroups);
            $genderLabels = ['male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'unknown' => 'Prefer not to say'];
            $genderColors = ['male' => '#6366F1', 'female' => '#EC4899', 'other' => '#FBBF24', 'unknown' => '#94A3B8'];
            @endphp
            @if(!empty($genderGroups) && $totalGender > 0)
            <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                <canvas id="genderChart"></canvas>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('genderChart').getContext('2d');
                    
                    const genderKeys = @json(array_keys($genderGroups));
                    const genderData = @json(array_values($genderGroups));
                    const genderColors = @json(array_values($genderColors));
                    const genderLabelMap = @json($genderLabels);
                    const totalGender = @json($totalGender);
                    const genderPercentages = genderData.map(count => Math.round((count / totalGender) * 100));
                    
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: genderKeys.map(key => genderLabelMap[key] || key),
                            datasets: [{
                                data: genderData,
                                backgroundColor: genderColors,
                                borderColor: '#fff',
                                borderWidth: 3,
                                hoverOffset: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        font: {
                                            size: 12,
                                            weight: '500'
                                        },
                                        color: '#475569'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(30, 41, 59, 0.9)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    padding: 12,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: function(context) {
                                            const index = context.dataIndex;
                                            const count = context.raw;
                                            const percentage = genderPercentages[index];
                                            const label = context.label;
                                            return `${label}: ${count} customers (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            animation: {
                                animateRotate: true,
                                animateScale: true,
                                duration: 1000,
                                easing: 'easeOutQuart'
                            }
                        }
                    });
                });
            </script>
            @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-gender-ambiguous display-4 mb-3 d-block" style="color: #CBD5E1;"></i>
                <p class="mb-0">No data available</p>
            </div>
            @endif
        </x-data-card>
    </div>

    {{-- Age Groups --}}
    <div class="col-lg-8">
        <x-data-card title="Age Groups Distribution">
            @php
            $ageGroups = $demographics['age_groups'] ?? [];
            $totalAge = array_sum($ageGroups);
            $ageLabels = [
                'toddler' => '0-3',
                'child' => '4-12', 
                'youth' => '13-25',
                'adult' => '26-59',
                'senior' => '60+'
            ];
            $ageColors = ['toddler' => '#FBBF24', 'child' => '#34D399', 'youth' => '#60A5FA', 'adult' => '#A78BFA', 'senior' => '#F472B6'];
            @endphp
            @if(!empty($ageGroups) && $totalAge > 0)
            <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                <canvas id="ageGroupsChart"></canvas>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('ageGroupsChart').getContext('2d');
                    
                    const ageLabels = @json(array_values($ageLabels));
                    const ageData = @json(array_values($ageGroups));
                    const ageColors = @json(array_values($ageColors));
                    const ageKeys = @json(array_keys($ageGroups));
                    const totalAge = @json($totalAge);
                    const agePercentages = ageData.map(count => Math.round((count / totalAge) * 100));
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ageLabels,
                            datasets: [{
                                label: 'Customers',
                                data: ageData,
                                borderColor: '#6366F1',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                borderWidth: 3,
                                pointBackgroundColor: ageColors,
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 6,
                                pointHoverRadius: 8,
                                fill: true,
                                tension: 0.4,
                                segment: {
                                    borderColor: function(context) {
                                        const index = context.p0DataIndex;
                                        return ageColors[index] || '#6366F1';
                                    },
                                    backgroundColor: function(context) {
                                        const index = context.p0DataIndex;
                                        return ageColors[index] + '20' || 'rgba(99, 102, 241, 0.1)';
                                    }
                                }
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(30, 41, 59, 0.9)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            const index = context.dataIndex;
                                            const count = context.raw;
                                            const percentage = agePercentages[index];
                                            const groupName = ageKeys[index];
                                            return [
                                                `${groupName.charAt(0).toUpperCase() + groupName.slice(1)}: ${count} customers (${percentage}%)`
                                            ];
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Age Groups',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        color: '#64748B'
                                    },
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#64748B',
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Number of Customers',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        color: '#64748B'
                                    },
                                    grid: {
                                        color: 'rgba(226, 232, 240, 0.6)'
                                    },
                                    ticks: {
                                        color: '#64748B',
                                        font: {
                                            size: 11
                                        },
                                        stepSize: Math.ceil(totalAge / 5) || 1
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                });
            </script>
            @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-bar-chart-steps display-4 mb-3 d-block" style="color: #CBD5E1;"></i>
                <p class="mb-0">No age data available</p>
            </div>
            @endif
        </x-data-card>
    </div>
</div>

{{-- Demographics Section --}}
<div class="row g-4 mb-5">
    {{-- Top Nationalities --}}
    <div class="col-lg-6">
        <x-data-card title="Top Nationalities">
            @php
            $nationalities = $demographics['nationalities'] ?? [];
            arsort($nationalities);
            $topNationalities = array_slice($nationalities, 0, 6, true);
            @endphp
            @if(!empty($topNationalities))
            <div class="list-group list-group-flush">
                @foreach($topNationalities as $code => $count)
                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-2">
                    <div class="d-flex align-items-center">
                        <span class="fs-5 me-2">{{ getCountryFlag($code) }}</span>
                        <span class="fw-medium">{{ getCountryName($code) }}</span>
                    </div>
                    <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center text-muted py-4">
                <i class="bi bi-globe display-6 mb-2 d-block" style="color: #CBD5E1;"></i>
                <p class="mb-0 small">No nationality data</p>
            </div>
            @endif
        </x-data-card>
    </div>

    {{-- Zodiac Signs --}}
    <div class="col-lg-6">
        <x-data-card title="Zodiac Signs">
            @php
            $zodiacs = $demographics['zodiac_signs'] ?? [];
            arsort($zodiacs);
            $topZodiacs = array_slice($zodiacs, 0, 6, true);
            $zodiacEmojis = [
                'Capricorn' => '♑', 'Aquarius' => '♒', 'Pisces' => '♓',
                'Aries' => '♈', 'Taurus' => '♉', 'Gemini' => '♊',
                'Cancer' => '♋', 'Leo' => '♌', 'Virgo' => '♍',
                'Libra' => '♎', 'Scorpio' => '♏', 'Sagittarius' => '♐'
            ];
            @endphp
            @if(!empty($topZodiacs))
            <div class="list-group list-group-flush">
                @foreach($topZodiacs as $sign => $count)
                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-2">
                    <div class="d-flex align-items-center">
                        <span class="fs-5 me-2">{{ $zodiacEmojis[$sign] ?? '⭐' }}</span>
                        <span class="fw-medium">{{ $sign }}</span>
                    </div>
                    <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center text-muted py-4">
                <i class="bi bi-stars display-6 mb-2 d-block" style="color: #CBD5E1;"></i>
                <p class="mb-0 small">No zodiac data</p>
            </div>
            @endif
        </x-data-card>
    </div>
</div>

{{-- Birthdays This Month Section --}}
@if(!empty($birthdaysThisMonth['customers']))
<div class="row g-4 mb-5">
    <div class="col-12">
        <x-data-card title="🎂 Birthdays This Month ({{ $birthdaysThisMonth['month'] }})">
            @php
            $birthdays = $birthdaysThisMonth['customers'];
            $birthdayCount = $birthdaysThisMonth['count'];
            $todayBirthdays = array_filter($birthdays, fn($c) => $c['birth_day'] == now()->format('d'));
            $upcomingBirthdays = array_filter($birthdays, fn($c) => $c['birth_day'] >= now()->format('d'));
            @endphp
            <div class="row g-4">
                {{-- Today's Birthdays --}}
                @if(!empty($todayBirthdays))
                <div class="col-lg-4">
                    <div class="birthday-today-section">
                        <h6 class="text-success fw-bold mb-3">
                            <i class="bi bi-cake me-2"></i>Today ({{ $birthdaysThisMonth['month'] }} {{ now()->format('d') }})
                        </h6>
                        @foreach($todayBirthdays as $customer)
                        <a href="{{ route('customers.show', $customer['id']) }}" class="text-decoration-none">
                            <div class="birthday-today-card bg-success-light rounded p-3 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="birthday-avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-size: 1.2rem;">
                                        🎂
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $customer['name'] }}</div>
                                        <small class="text-muted">Turning {{ $customer['age'] + 1 }} today!</small>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Upcoming Birthdays --}}
                <div class="{{ !empty($todayBirthdays) ? 'col-lg-8' : 'col-12' }}">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-calendar-heart me-2"></i>Upcoming Birthdays ({{ $birthdayCount }} total)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Age</th>
                                    <th>Zodiac</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingBirthdays as $customer)
                                <tr class="{{ $customer['birth_day'] == now()->format('d') ? 'bg-success-transparent' : '' }}">
                                    <td class="fw-medium">{{ $customer['formatted_dob'] }}</td>
                                    <td>
                                        <a href="{{ route('customers.show', $customer['id']) }}" class="text-decoration-none fw-medium text-dark">
                                            {{ $customer['name'] }}
                                        </a>
                                        @include('components.badge', [
                                            'variant' => $customer['type'] === 'corporate' ? 'info' : 'success',
                                            'slot' => $customer['type']
                                        ])
                                    </td>
                                    <td>Turning {{ $customer['age'] + 1 }}</td>
                                    <td>
                                        @php
                                        $zodiacEmojis = [
                                            'Capricorn' => '♑', 'Aquarius' => '♒', 'Pisces' => '♓',
                                            'Aries' => '♈', 'Taurus' => '♉', 'Gemini' => '♊',
                                            'Cancer' => '♋', 'Leo' => '♌', 'Virgo' => '♍',
                                            'Libra' => '♎', 'Scorpio' => '♏', 'Sagittarius' => '♐'
                                        ];
                                        @endphp
                                        {{ $zodiacEmojis[$customer['zodiac']] ?? '' }} {{ $customer['zodiac'] ?? '-' }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('customers.show', $customer['id']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </x-data-card>
    </div>
</div>
@endif

{{-- Data Tables Section --}}
<div class="row g-4 mb-5">
    {{-- Top Outlets by Visits --}}
    <div class="col-lg-6">
        <a href="{{ route('outlets.index') }}" class="text-decoration-none">
            <x-data-card title="Top Outlets by Visits" :showActions="true">
                <x-slot name="actions">
                    <span class="btn btn-sm btn-outline-primary">View All</span>
                </x-slot>
                @php
                $topOutlets = \App\Models\Outlet::withCount('visits')
                    ->orderByDesc('visits_count')
                    ->take(5)
                    ->get();
                @endphp
                @if(!empty($topOutlets) && $topOutlets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Outlet</th>
                                <th class="text-end">Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topOutlets as $outlet)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $outlet->name }}</div>
                                    <small class="text-muted text-capitalize">{{ $outlet->type }}</small>
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($outlet->visits_count) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-shop display-4 mb-3 d-block" style="color: #CBD5E1;"></i>
                    <p class="mb-0">No data available</p>
                </div>
                @endif
            </x-data-card>
        </a>
    </div>

    {{-- Recent Customers --}}
    <div class="col-lg-6">
        <a href="{{ route('customers.index') }}" class="text-decoration-none">
            <x-data-card title="Recent Customers" :showActions="true">
                <x-slot name="actions">
                    <span class="btn btn-sm btn-outline-primary">View All</span>
                </x-slot>
                @php
                $recentCustomers = \App\Models\Customer::with('visits')->latest()->take(5)->get();
                @endphp
                @if(!empty($recentCustomers) && $recentCustomers->count() > 0)
                <div class="list-modern">
                    @foreach($recentCustomers as $customer)
                    <a href="{{ route('customers.show', $customer) }}" class="list-item-modern text-decoration-none">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-dark">{{ $customer->name }}</div>
                                <small class="text-muted d-block">
                                    {{ $customer->email ?: 'No email' }}
                                </small>
                            </div>
                            <div class="text-end ms-3">
                                @include('components.badge', [
                                    'variant' => $customer->type === 'corporate' ? 'info' : 'success',
                                    'slot' => $customer->type
                                ])
                                <div class="small text-muted mt-1">{{ $customer->visits->count() }} visits</div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-people display-4 mb-3 d-block" style="color: #CBD5E1;"></i>
                    <p class="mb-0">No customers yet</p>
                </div>
                @endif
            </x-data-card>
        </a>
    </div>
</div>

{{-- The rest of your code (Analytics widgets, Age Groups, Chart.js scripts, styles) remains exactly the same --}}
@endsection
