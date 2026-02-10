@extends('layouts.app')

@section('title', 'Messages: ' . $campaign->name)
@section('page-title', 'Campaign Messages')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaigns</a></li>
                <li class="breadcrumb-item"><a href="{{ route('campaigns.show', $campaign) }}">{{ $campaign->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Messages</li>
            </ol>
        </nav>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group">
            <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Campaign
            </a>
            @if($campaign->messages()->failed()->exists())
            @can('campaigns.edit')
            <form action="{{ route('campaigns.retry-failed', $campaign) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-warning" onclick="return confirm('Retry all failed messages?')">
                    <i class="bi bi-arrow-clockwise me-2"></i>Retry Failed
                </button>
            </form>
            @endcan
            @endif
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Total</h5>
                <h3>{{ number_format($messages->total()) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Pending</h5>
                <h3>{{ number_format($messages->where('status', 'pending')->count()) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Sent</h5>
                <h3>{{ number_format($messages->where('status', 'sent')->count()) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Delivered</h5>
                <h3>{{ number_format($messages->where('status', 'delivered')->count()) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Opened</h5>
                <h3>{{ number_format($messages->where('status', 'opened')->count()) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Failed</h5>
                <h3 class="text-danger">{{ number_format($messages->where('status', 'failed')->count()) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('campaigns.messages', $campaign) }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="opened" {{ request('status') === 'opened' ? 'selected' : '' }}>Opened</option>
                    <option value="clicked" {{ request('status') === 'clicked' ? 'selected' : '' }}>Clicked</option>
                    <option value="bounced" {{ request('status') === 'bounced' ? 'selected' : '' }}>Bounced</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Search Customer</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Name or email...">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('campaigns.messages', $campaign) }}" class="btn btn-outline-secondary w-100">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Messages Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email/Phone</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th>Delivered At</th>
                        <th>Opened At</th>
                        <th>Error</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $message->customer->name ?? 'Unknown' }}</div>
                            <small class="text-muted">ID: {{ $message->customer_id }}</small>
                        </td>
                        <td>
                            @if($message->customer->email)
                            <div><i class="bi bi-envelope me-1"></i>{{ $message->customer->email }}</div>
                            @endif
                            @if($message->customer->mobile_json)
                            <small class="text-muted"><i class="bi bi-phone me-1"></i>{{ formatMobileNumber($message->customer->mobile_json) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-status badge-{{ $message->status }}">
                                {{ ucfirst($message->status) }}
                            </span>
                        </td>
                        <td>{{ $message->sent_at ? $message->sent_at->format('M d, H:i') : '-' }}</td>
                        <td>{{ $message->delivered_at ? $message->delivered_at->format('M d, H:i') : '-' }}</td>
                        <td>{{ $message->opened_at ? $message->opened_at->format('M d, H:i') : '-' }}</td>
                        <td>
                            @if($message->error_message)
                            <span class="text-danger" title="{{ $message->error_message }}">
                                <i class="bi bi-exclamation-triangle"></i> {{ Str::limit($message->error_message, 30) }}
                            </span>
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if($message->isFailed())
                            @can('campaigns.edit')
                            <button class="btn btn-sm btn-outline-primary" onclick="retryMessage({{ $message->id }})">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            @endcan
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-envelope display-4"></i>
                                <p class="mt-2">No messages found</p>
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
    {{ $messages->appends(request()->query())->links() }}
</div>

@push('scripts')
<script>
function retryMessage(messageId) {
    if (confirm('Retry sending this message?')) {
        // In a real implementation, this would call an API endpoint
        fetch(`/campaigns/messages/${messageId}/retry`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}
</script>
@endpush
@endsection

