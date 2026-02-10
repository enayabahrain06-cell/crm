@extends('layouts.app')

@section('title', $profile->name)
@section('page-title', $profile->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        {{-- Header Card --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center rounded" style="width: 64px; height: 64px; font-size: 1.5rem;">
                            {{ strtoupper(substr($profile->name, 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="mb-1">{{ $profile->name }}</h4>
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <span><i class="bi bi-envelope me-1"></i>{{ $profile->email ?? 'N/A' }}</span>
                                <span><i class="bi bi-phone me-1"></i>{{ $profile->formatted_mobile }}</span>
                                <span class="badge badge-status {{ $profile->status === 'active' ? 'badge-active' : ($profile->status === 'inactive' ? 'badge-inactive' : 'badge-blacklisted') }}">
                                    {{ ucfirst($profile->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('customers.export-pdf', $profile) }}" class="btn btn-outline-primary" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i>Export PDF
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-primary">
                            <i class="bi bi-printer me-1"></i>Print
                        </button>
                        <a href="{{ route('customers.edit', $profile) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Left Column --}}
            <div class="col-lg-8">
                {{-- Basic Information --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Customer Type</label>
                                <div class="fw-medium">{{ ucfirst($profile->type) }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Gender</label>
                                <div class="fw-medium">{{ ucfirst($profile->gender ?? 'Not specified') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Nationality</label>
                                <div class="fw-medium">{{ $profile->nationality ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Date of Birth</label>
                                <div class="fw-medium">{{ $profile->date_of_birth?->format('d M Y') ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Age</label>
                                <div class="fw-medium">{{ $profile->age ?? 'N/A' }} ({{ $profile->age_group }})</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Zodiac</label>
                                <div class="fw-medium">{{ $profile->zodiac ?? 'N/A' }}</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="text-muted small">Address</label>
                                <div class="fw-medium">{{ $profile->address ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Corporate Information --}}
                @if($profile->type === 'corporate' || $profile->company_name)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-building me-2"></i>Corporate Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Company Name</label>
                                <div class="fw-medium">{{ $profile->company_name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Position</label>
                                <div class="fw-medium">{{ $profile->position ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Tags --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-tags me-2"></i>Customer Tags</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTagModal">
                            <i class="bi bi-plus me-1"></i>Add Tag
                        </button>
                    </div>
                    <div class="card-body">
                        @if($tags->count() > 0)
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($tags as $tag)
                                    <span class="badge d-flex align-items-center gap-1" style="background-color: {{ $tag->color }}; color: white;">
                                        {{ $tag->name }}
                                        <form action="{{ route('customers.tags.remove', [$profile, $tag->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-close btn-close-white ms-1" style="font-size: 0.5rem;" aria-label="Remove" onclick="return confirm('Are you sure you want to remove this tag?')"></button>
                                        </form>
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No tags assigned to this customer.</p>
                        @endif
                    </div>
                </div>

                {{-- Add Tag Modal --}}
                <div class="modal fade" id="addTagModal" tabindex="-1" aria-labelledby="addTagModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addTagModalLabel">Add Tag to Customer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('customers.tags', $profile) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="tag_id" class="form-label">Select Tag</label>
                                        <select class="form-select" id="tag_id" name="tag_id" required>
                                            <option value="">Choose a tag...</option>
                                            @php
                                                $availableTags = \App\Models\CustomerTag::whereNotIn('id', $tags->pluck('id'))->get();
                                            @endphp
                                            @foreach($availableTags as $tag)
                                                <option value="{{ $tag->id }}" style="background-color: {{ $tag->color }}; color: white;">
                                                    {{ $tag->name }}
                                                </option>
                                            @endforeach
                                            @if($availableTags->count() === 0)
                                                <option value="" disabled>No tags available</option>
                                            @endif
                                        </select>
                                        @if($availableTags->count() === 0)
                                            <small class="text-muted">All tags have already been assigned to this customer.</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" {{ $availableTags->count() === 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-plus-lg me-1"></i>Add Tag
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Visits --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Recent Visits</h5>
                    </div>
                    <div class="card-body">
                        @if($visits->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
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
                        @else
                            <p class="text-muted mb-0">No visits recorded yet.</p>
                        @endif
                    </div>
                </div>

                {{-- Events --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Customer Events</h5>
                    </div>
                    <div class="card-body">
                        @if($events->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($events as $event)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-info mb-1">{{ $event->event_type }}</span>
                                            <div class="text-muted small">{{ $event->outlet->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="text-muted small">{{ $event->created_at->format('d M Y h:i A') }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No events recorded yet.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="col-lg-4">
                {{-- Loyalty Points --}}
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-primary">{{ number_format($kpis['current_points'] ?? 0) }}</div>
                        <div class="text-muted">Loyalty Points</div>
                        @if(isset($loyalty['wallet']) && $loyalty['wallet'])
                            <div class="small text-muted mt-2">
                                Tier: {{ $loyalty['wallet']->tier ?? 'Standard' }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Stats --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Statistics</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Visits</span>
                            <strong>{{ $kpis['total_visits'] ?? $visits->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Unique Outlets</span>
                            <strong>{{ $kpis['unique_outlets'] ?? $visits->pluck('outlet_id')->unique()->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Member Since</span>
                            <strong>{{ $profile->created_at->format('M Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Spend</span>
                            <strong>{{ number_format($kpis['total_spend'] ?? 0) }}</strong>
                        </div>
                    </div>
                </div>

                {{-- Registration Info --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Registration Info</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Registered At</span>
                            <strong>{{ $profile->created_at->format('d M Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">First Outlet</span>
                            <strong>{{ $profile->firstRegistrationOutlet?->name ?? 'N/A' }}</strong>
                        </div>
                        @if($profile->creator)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Created By</span>
                            <strong>{{ $profile->creator->name }}</strong>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Reward Redemptions --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-gift me-2"></i>Reward Redemptions</h6>
                    </div>
                    <div class="card-body">
                        @if($redemptions->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($redemptions->take(5) as $redemption)
                                    <div class="list-group-item px-0 py-2">
                                        <div class="fw-medium">{{ $redemption->reward->name ?? 'Unknown Reward' }}</div>
                                        <div class="small text-muted">
                                            {{ $redemption->points_used }} pts - {{ $redemption->created_at->format('d M Y') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0 small">No redemptions yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
@media print {
    .btn, .card-header button, .modal {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .card-header {
        background: none !important;
        border-bottom: 1px solid #ddd !important;
    }
    body {
        font-size: 12px;
    }
    .container, .row, .col-lg-8, .col-lg-4 {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>
@endpush

