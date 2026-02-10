@extends('layouts.app')

@section('title', $campaign->name)
@section('page-title', $campaign->name)

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaigns</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $campaign->name }}</li>
            </ol>
        </nav>
    </div>
    <div class="col-md-4 text-end">
        @if($campaign->isDraft())
        @can('campaigns.edit')
        <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit Campaign
        </a>
        @endcan
        @endif
    </div>
</div>

<!-- Campaign Info Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title text-muted">Status</h5>
                <span class="badge badge-status badge-{{ $campaign->status }} fs-6">
                    {{ ucfirst($campaign->status) }}
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title text-muted">Recipients</h5>
                <h4>{{ number_format($campaign->total_recipients ?? 0) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title text-muted">Sent</h5>
                <h4>{{ number_format($campaign->sent_count ?? 0) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title text-muted">Opened</h5>
                <h4>
                    @if($campaign->sent_count > 0)
                    {{ number_format($campaign->opened_count ?? 0) }}
                    <small class="text-muted">({{ round(($campaign->opened_count / $campaign->sent_count) * 100, 1) }}%)</small>
                    @else
                    -
                    @endif
                </h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Campaign Details -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Campaign Details</h5>
                @if($campaign->isDraft())
                @can('campaigns.send')
                @if($campaign->isReadyToSend())
                <form action="{{ route('campaigns.send', $campaign) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to send this campaign?')">
                        <i class="bi bi-send me-2"></i>Send Campaign
                    </button>
                </form>
                @endif
                @endcan
                @endif
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 text-muted">Name:</div>
                    <div class="col-md-9 fw-semibold">{{ $campaign->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 text-muted">Type:</div>
                    <div class="col-md-9">
                        <span class="badge bg-{{ $campaign->type === 'email' ? 'info' : ($campaign->type === 'sms' ? 'warning' : 'success') }}">
                            {{ ucfirst($campaign->type) }}
                        </span>
                    </div>
                </div>
                @if($campaign->subject)
                <div class="row mb-3">
                    <div class="col-md-3 text-muted">Subject:</div>
                    <div class="col-md-9">{{ $campaign->subject }}</div>
                </div>
                @endif
                @if($campaign->preheader)
                <div class="row mb-3">
                    <div class="col-md-3 text-muted">Preheader:</div>
                    <div class="col-md-9">{{ $campaign->preheader }}</div>
                </div>
                @endif
                <div class="row mb-3">
                    <div class="col-md-3 text-muted">From Name:</div>
                    <div class="col-md-9">{{ $campaign->from_name ?: 'Not set' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 text-muted">Reply To:</div>
                    <div class="col-md-9">{{ $campaign->reply_to ?: 'Not set' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 text-muted">Segment:</div>
                    <div class="col-md-9">
                        @if($campaign->segment_definition_json)
                        <code>{{ json_encode($campaign->segment_definition_json) }}</code>
                        @else
                        All customers
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 text-muted">Created:</div>
                    <div class="col-md-9">{{ $campaign->created_at->format('M d, Y H:i') }} by {{ $campaign->creator->name ?? 'N/A' }}</div>
                </div>
                @if($campaign->sent_at)
                <div class="row mb-3">
                    <div class="col-md-3 text-muted">Sent:</div>
                    <div class="col-md-9">{{ $campaign->sent_at->format('M d, Y H:i') }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Campaign Content -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Content</h5>
            </div>
            <div class="card-body">
                @if($campaign->type === 'email')
                <div class="mb-3">
                    <label class="text-muted">HTML Content:</label>
                    <div class="border p-3 bg-light rounded">
                        {!! $campaign->body !!}
                    </div>
                </div>
                @else
                <div class="mb-3">
                    <label class="text-muted">Message Body:</label>
                    <div class="border p-3 bg-light rounded">
                        {{ $campaign->body }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Messages -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Messages</h5>
                <a href="{{ route('campaigns.messages', $campaign) }}" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th>Opened At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaign->messages->take(10) as $message)
                            <tr>
                                <td>
                                    {{ $message->customer->name ?? 'Unknown' }}
                                    <small class="text-muted d-block">{{ $message->customer->email ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-status badge-{{ $message->status }}">
                                        {{ ucfirst($message->status) }}
                                    </span>
                                </td>
                                <td>{{ $message->sent_at ? $message->sent_at->format('M d, H:i') : '-' }}</td>
                                <td>{{ $message->opened_at ? $message->opened_at->format('M d, H:i') : '-' }}</td>
                                <td>
                                    @if($message->isFailed())
                                    @can('campaigns.edit')
                                    <button class="btn btn-sm btn-outline-primary" onclick="retryMessage({{ $message->id }})">
                                        Retry
                                    </button>
                                    @endcan
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No messages sent yet
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($campaign->isDraft())
                    @can('campaigns.edit')
                    <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    @endcan
                    @can('campaigns.send')
                    @if($campaign->isReadyToSend())
                    <form action="{{ route('campaigns.send', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Are you sure you want to send this campaign?')">
                            <i class="bi bi-send me-2"></i>Send Now
                        </button>
                    </form>
                    @endif
                    @endcan
                    @endif
                    @can('campaigns.view')
                    <a href="{{ route('campaigns.preview', $campaign) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-eye me-2"></i>Preview
                    </a>
                    @endcan
                    @can('campaigns.delete')
                    @if($campaign->canBeCancelled())
                    <form action="{{ route('campaigns.cancel', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning w-100" onclick="return confirm('Are you sure you want to cancel this campaign?')">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                    </form>
                    @endif
                    @endcan
                    <hr>
                    <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        @can('campaigns.delete')
                        <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure? This action cannot be undone.')">
                            <i class="bi bi-trash me-2"></i>Delete Campaign
                        </button>
                        @endcan
                    </form>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Performance</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Delivery Rate</span>
                        <span>
                            @if($campaign->total_recipients > 0)
                            {{ round(($campaign->sent_count / $campaign->total_recipients) * 100, 1) }}%
                            @else
                            0%
                            @endif
                        </span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $campaign->total_recipients > 0 ? ($campaign->sent_count / $campaign->total_recipients) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Open Rate</span>
                        <span>
                            @if($campaign->sent_count > 0)
                            {{ round(($campaign->opened_count / $campaign->sent_count) * 100, 1) }}%
                            @else
                            0%
                            @endif
                        </span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $campaign->sent_count > 0 ? ($campaign->opened_count / $campaign->sent_count) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Click Rate</span>
                        <span>
                            @if($campaign->sent_count > 0)
                            {{ round(($campaign->clicked_count / $campaign->sent_count) * 100, 1) }}%
                            @else
                            0%
                            @endif
                        </span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $campaign->sent_count > 0 ? ($campaign->clicked_count / $campaign->sent_count) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

