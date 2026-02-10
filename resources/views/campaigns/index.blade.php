@extends('layouts.app')

@section('title', 'Campaigns')
@section('page-title', 'Campaign Management')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('campaigns.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Campaign name...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="sending" {{ request('status') === 'sending' ? 'selected' : '' }}>Sending</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select class="form-select" name="type">
                    <option value="">All Types</option>
                    <option value="email" {{ request('type') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="sms" {{ request('type') === 'sms' ? 'selected' : '' }}>SMS</option>
                    <option value="push" {{ request('type') === 'push' ? 'selected' : '' }}>Push</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted">
        Showing {{ $campaigns->count() }} of {{ $campaigns->total() }} campaigns
    </div>
    <div class="btn-group">
        @can('campaigns.create')
        <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Campaign
        </a>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Recipients</th>
                        <th>Sent</th>
                        <th>Opens</th>
                        <th>Clicks</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $campaign->name }}</div>
                            @if($campaign->subject)
                            <small class="text-muted">{{ Str::limit($campaign->subject, 40) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $campaign->type === 'email' ? 'info' : ($campaign->type === 'sms' ? 'warning' : 'success') }}">
                                {{ ucfirst($campaign->type) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-status badge-{{ $campaign->status }}">
                                {{ ucfirst($campaign->status) }}
                            </span>
                        </td>
                        <td>{{ number_format($campaign->total_recipients ?? 0) }}</td>
                        <td>{{ number_format($campaign->sent_count ?? 0) }}</td>
                        <td>
                            @if($campaign->sent_count > 0)
                            {{ number_format($campaign->opened_count ?? 0) }}
                            <small class="text-muted">({{ round(($campaign->opened_count / $campaign->sent_count) * 100, 1) }}%)</small>
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if($campaign->sent_count > 0)
                            {{ number_format($campaign->clicked_count ?? 0) }}
                            <small class="text-muted">({{ round(($campaign->clicked_count / $campaign->sent_count) * 100, 1) }}%)</small>
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $campaign->creator->name ?? 'N/A' }}</td>
                        <td>{{ $campaign->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('campaigns.show', $campaign) }}">
                                        <i class="bi bi-eye me-2"></i>View
                                    </a></li>
                                    @can('campaigns.edit')
                                    @if($campaign->isDraft())
                                    <li><a class="dropdown-item" href="{{ route('campaigns.edit', $campaign) }}">
                                        <i class="bi bi-pencil me-2"></i>Edit
                                    </a></li>
                                    @endif
                                    @endcan
                                    @can('campaigns.send')
                                    @if($campaign->isReadyToSend())
                                    <li>
                                        <form action="{{ route('campaigns.send', $campaign) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to send this campaign?')">
                                                <i class="bi bi-send me-2"></i>Send
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                    @endcan
                                    @can('campaigns.delete')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-megaphone display-4"></i>
                                <p class="mt-2">No campaigns found</p>
                                @can('campaigns.create')
                                <a href="{{ route('campaigns.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg"></i> Create Your First Campaign
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $campaigns->appends(request()->query())->links() }}
</div>
@endsection

