<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customer Profile - {{ $profile->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4a6cf7;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4a6cf7;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header .subtitle {
            color: #666;
            font-size: 14px;
        }
        .card {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .card-header {
            background-color: #f8f9fa;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            font-weight: bold;
            font-size: 14px;
        }
        .card-body {
            padding: 12px;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -5px;
        }
        .col {
            padding: 0 5px;
            width: 50%;
        }
        .col-full {
            padding: 0 5px;
            width: 100%;
        }
        .label {
            color: #666;
            font-size: 11px;
            margin-bottom: 2px;
        }
        .value {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            color: white;
        }
        .badge-active { background-color: #28a745; }
        .badge-inactive { background-color: #6c757d; }
        .badge-blacklisted { background-color: #dc3545; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .stats-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .stat-item {
            flex: 1;
            min-width: 120px;
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #4a6cf7;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .two-column {
            display: flex;
            gap: 20px;
        }
        .column {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Customer Profile</h1>
        <div class="subtitle">{{ $profile->name }}</div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">Personal Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="label">Customer ID</div>
                            <div class="value">{{ $profile->customer_id }}</div>
                        </div>
                        <div class="col">
                            <div class="label">Status</div>
                            <div class="badge badge-{{ $profile->status }}">{{ ucfirst($profile->status) }}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="label">Type</div>
                            <div class="value">{{ ucfirst($profile->type) }}</div>
                        </div>
                        <div class="col">
                            <div class="label">Email</div>
                            <div class="value">{{ $profile->email ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="label">Mobile</div>
                            <div class="value">{{ $profile->formatted_mobile }}</div>
                        </div>
                        <div class="col">
                            <div class="label">Nationality</div>
                            <div class="value">{{ $profile->nationality ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="label">Gender</div>
                            <div class="value">{{ ucfirst($profile->gender ?? 'Not specified') }}</div>
                        </div>
                        <div class="col">
                            <div class="label">Date of Birth</div>
                            <div class="value">{{ $profile->date_of_birth?->format('d M Y') ?? 'N/A' }}</div>
                        </div>
                    </div>
                    @if($profile->address)
                    <div class="row">
                        <div class="col-full">
                            <div class="label">Address</div>
                            <div class="value">{{ $profile->address }}</div>
                        </div>
                    </div>
                    @endif
                    @if($profile->type === 'corporate' && $profile->company_name)
                    <div class="row">
                        <div class="col">
                            <div class="label">Company Name</div>
                            <div class="value">{{ $profile->company_name }}</div>
                        </div>
                        <div class="col">
                            <div class="label">Position</div>
                            <div class="value">{{ $profile->position ?? 'N/A' }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Statistics</div>
        <div class="card-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value">{{ number_format($kpis['current_points'] ?? 0) }}</div>
                    <div class="stat-label">Loyalty Points</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $kpis['total_visits'] ?? $visits->count() }}</div>
                    <div class="stat-label">Total Visits</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $kpis['unique_outlets'] ?? 'N/A' }}</div>
                    <div class="stat-label">Unique Outlets</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ number_format($kpis['total_spend'] ?? 0, 3) }}</div>
                    <div class="stat-label">Total Spend (BHD)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="two-column">
        <div class="column">
            <div class="card">
                <div class="card-header">Registration Info</div>
                <div class="card-body">
                    <div class="label">Registered At</div>
                    <div class="value">{{ $profile->created_at->format('d M Y') }}</div>
                    
                    <div class="label">First Outlet</div>
                    <div class="value">{{ $profile->firstRegistrationOutlet?->name ?? 'N/A' }}</div>
                    
                    @if($profile->creator)
                    <div class="label">Created By</div>
                    <div class="value">{{ $profile->creator->name }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="column">
            <div class="card">
                <div class="card-header">Tags</div>
                <div class="card-body">
                    @if($tags->count() > 0)
                        @foreach($tags as $tag)
                            <span class="badge" style="background-color: {{ $tag->color }}; margin-right: 5px; margin-bottom: 5px;">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    @else
                        No tags assigned
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($visits->count() > 0)
    <div class="card">
        <div class="card-header">Recent Visits</div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Outlet</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($visits->take(10) as $visit)
                    <tr>
                        <td>{{ $visit->visited_at->format('d M Y') }}</td>
                        <td>{{ $visit->outlet->name ?? 'N/A' }}</td>
                        <td>{{ $visit->visited_at->format('h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="footer">
        Generated on {{ now()->format('d M Y h:i A') }} | Hospitality CRM
    </div>
</body>
</html>

