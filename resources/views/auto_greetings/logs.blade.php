@extends('layouts.app')

@section('title', 'Greeting Logs')
@section('page-title', 'Greeting Logs - ' . $rule->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Greeting Logs</h5>
                    <small class="text-muted">{{ $rule->name }}</small>
                </div>
                <a href="{{ route('auto-greetings.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Rules
                </a>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('auto-greetings.logs', ['auto_greeting' => $rule]) }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Statuses</option>
                            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter me-1"></i>Filter
                        </button>
                    </div>
                </form>

                <!-- Logs Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>
                                    @if($log->customer)
                                    <div class="fw-semibold">{{ $log->customer->name }}</div>
                                    <small class="text-muted">{{ $log->customer->email }}</small>
                                    @else
                                    <span class="text-muted">Unknown Customer</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $log->channel === 'email' ? 'info' : 'warning' }}">
                                        {{ ucfirst($log->channel) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-status badge-{{ $log->status }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->sent_at)
                                    {{ $log->sent_at->format('M d, Y H:i') }}
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->status === 'failed' && $log->error_message)
                                    <small class="text-danger" title="{{ $log->error_message }}">
                                        <i class="bi bi-exclamation-triangle"></i> Error
                                    </small>
                                    @else
                                    <small class="text-muted">-</small>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-clock-history display-4"></i>
                                        <p class="mt-2 mb-0">No logs found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($logs->hasPages())
                <div class="mt-4">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

