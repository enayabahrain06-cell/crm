@extends('layouts.app')

@section('title', 'Auto Greetings')
@section('page-title', 'Auto Greeting Rules')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('auto-greetings.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Rule name...">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted">
        Showing {{ $rules->count() }} {{ $rules->count() === 1 ? 'rule' : 'rules' }}
    </div>
    <div class="btn-group">
        @can('auto_greetings.create')
        <a href="{{ route('auto-greetings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Rule
        </a>
        @endcan
        @can('auto_greetings.manage')
        <form action="{{ route('auto-greetings.process') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-outline-primary" onclick="return confirm('Process all active rules now?')">
                <i class="bi bi-play-circle"></i> Process Now
            </button>
        </form>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Rule</th>
                        <th>Type</th>
                        <th>Trigger</th>
                        <th>Status</th>
                        <th>Logs</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $rule)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $rule->name }}</div>
                            @if($rule->description)
                            <small class="text-muted">{{ Str::limit($rule->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $rule->channel === 'email' ? 'info' : 'warning' }}">
                                {{ ucfirst($rule->channel) }}
                            </span>
                        </td>
                        <td>
                            @switch($rule->trigger_type)
                                @case('birthday')
                                    <i class="bi bi-calendar-event me-1"></i>Birthday
                                    @if($rule->days_before)
                                        ({{ $rule->days_before }} days before)
                                    @endif
                                    @break
                                @case('fixed_date')
                                    <i class="bi bi-calendar-check me-1"></i>Fixed Date: {{ $rule->trigger_date }}
                                    @break
                                @default
                                    {{ ucfirst($rule->trigger_type) }}
                            @endswitch
                        </td>
                        <td>
                            <span class="badge badge-status {{ $rule->active ? 'badge-active' : 'badge-inactive' }}">
                                {{ $rule->active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('auto-greetings.logs', ['auto_greeting' => $rule]) }}" class="badge bg-light text-dark" style="text-decoration: none;">
                                <i class="bi bi-clock-history me-1"></i>
                                {{ $rule->logs_count ?? 0 }} logs
                            </a>
                        </td>
                        <td>{{ $rule->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('auto-greetings.edit', ['auto_greeting' => $rule]) }}">
                                        <i class="bi bi-pencil me-2"></i>Edit
                                    </a></li>
                                    @can('auto_greetings.edit')
                                    <li>
                                        <form action="{{ route('auto-greetings.toggle', ['auto_greeting' => $rule]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-power me-2"></i>{{ $rule->active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </li>
                                    @endcan
                                    <li><a class="dropdown-item" href="{{ route('auto-greetings.logs', ['auto_greeting' => $rule]) }}">
                                        <i class="bi bi-clock-history me-2"></i>View Logs
                                    </a></li>
                                    @can('auto_greetings.delete')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('auto-greetings.destroy', ['auto_greeting' => $rule]) }}" method="POST">
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
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-calendar-heart display-4"></i>
                                <p class="mt-2">No auto-greeting rules found</p>
                                @can('auto_greetings.create')
                                <a href="{{ route('auto-greetings.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg"></i> Create Your First Rule
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
@endsection
