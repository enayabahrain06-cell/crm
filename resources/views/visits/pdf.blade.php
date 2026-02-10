<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Visits Report - {{ now()->format('d M Y') }}</title>
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4a6cf7;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4a6cf7;
            font-size: 22px;
            margin-bottom: 5px;
        }
        .header .subtitle {
            color: #666;
            font-size: 12px;
        }
        .filters-info {
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #dee2e6;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .filters-info strong {
            color: #4a6cf7;
        }
        .stats-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1;
            min-width: 100px;
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #4a6cf7;
        }
        .stat-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-end { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #333; }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        .page-break {
            page-break-before: always;
        }
        .summary-section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Visits Report</h1>
        <div class="subtitle">Generated on {{ now()->format('d M Y h:i A') }}</div>
    </div>

    {{-- Filters Applied --}}
    @if(!empty($filters) && (isset($filters['outlet_id']) || isset($filters['start_date']) || isset($filters['end_date']) || isset($filters['search'])))
    <div class="filters-info">
        <strong>Filters Applied:</strong>
        @if(isset($filters['outlet_id']) && $filters['outlet_id'])
            Outlet: {{ $filters['outlet_name'] ?? 'Selected' }} |
        @endif
        @if(isset($filters['start_date']) && $filters['start_date'])
            From: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d M Y') }}
        @endif
        @if(isset($filters['end_date']) && $filters['end_date'])
            To: {{ \Carbon\Carbon::parse($filters['end_date'])->format('d M Y') }}
        @endif
        @if(isset($filters['search']) && $filters['search'])
            | Search: "{{ $filters['search'] }}"
        @endif
    </div>
    @endif

    {{-- Summary Statistics --}}
    <div class="summary-section">
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-value">{{ number_format($stats['total_visits'] ?? $visits->count()) }}</div>
                <div class="stat-label">Total Visits</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $currencySymbol }}{{ number_format($stats['total_revenue'] ?? 0, 3) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ number_format($stats['total_points'] ?? 0) }}</div>
                <div class="stat-label">Points Awarded</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $visits->count() }}</div>
                <div class="stat-label">Records Shown</div>
            </div>
        </div>
    </div>

    {{-- Visits Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th style="width: 140px;">Customer</th>
                <th style="width: 100px;">Outlet</th>
                <th style="width: 100px;">Staff</th>
                <th style="80px;" class="text-right">Bill</th>
                <th style="60px;" class="text-center">Points</th>
                <th style="100px;">Date & Time</th>
                <th style="80px;">Type</th>
            </tr>
        </thead>
        <tbody>
            @forelse($visits as $visit)
            <tr>
                <td>#{{ $visit->id }}</td>
                <td>
                    {{ $visit->customer->name ?? 'N/A' }}
                    @if($visit->customer && $visit->customer->mobile_json)
                        <br><small style="color: #666;">{{ formatMobileNumber($visit->customer->mobile_json) }}</small>
                    @endif
                </td>
                <td>{{ $visit->outlet->name ?? 'N/A' }}</td>
                <td>{{ $visit->staff->name ?? 'N/A' }}</td>
                <td class="text-end">
                    @if($visit->bill_amount > 0)
                        {{ $currencySymbol }}{{ number_format($visit->bill_amount, 3) }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if($visit->points_awarded > 0)
                        <span class="badge badge-success">{{ $visit->points_awarded }}</span>
                    @else
                        -
                    @endif
                </td>
                <td>
                    {{ $visit->visited_at->format('d M Y') }}
                    <br><small style="color: #666;">{{ $visit->visited_at->format('h:i A') }}</small>
                </td>
                <td>
                    @if($visit->visit_type)
                        <span class="badge badge-info">{{ ucfirst($visit->visit_type) }}</span>
                    @else
                        Regular
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No visits found matching your criteria</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('d M Y h:i A') }} | Hospitality CRM
    </div>
</body>
</html>

