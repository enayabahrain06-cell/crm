@extends('layouts.app')

@section('title', 'Loyalty Rules')
@section('page-title', 'Loyalty Rules')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="text-muted">
        {{ $rules->count() }} rules configured
    </div>
    <a href="{{ route('loyalty.rules.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Rule
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Priority</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $rule)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $rule->priority }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $rule->name }}</div>
                        </td>
                        <td>
                            @if($rule->type === 'earn')
                            <span class="badge bg-success">
                                <i class="bi bi-arrow-up-circle me-1"></i>Earn
                            </span>
                            @elseif($rule->type === 'burn')
                            <span class="badge bg-warning">
                                <i class="bi bi-arrow-down-circle me-1"></i>Burn
                            </span>
                            @else
                            <span class="badge bg-info">
                                <i class="bi bi-arrow-up-circle me-1"></i>Upgrade
                            </span>
                            @endif
                        </td>
                        <td>
                            @if($rule->active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            {{ $rule->creator->name ?? 'System' }}
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('loyalty.rules.edit', $rule) }}">
                                        <i class="bi bi-pencil me-2"></i>Edit
                                    </a></li>
                                    <li>
                                        <form action="{{ route('loyalty.rules.destroy', $rule) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this rule?')">
                                                <i class="bi bi-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-gear display-4"></i>
                                <p class="mt-2">No rules configured yet</p>
                                @can('loyalty_rules.create')
                                <a href="{{ route('loyalty.rules.create') }}" class="btn btn-primary mt-2">
                                    <i class="bi bi-plus-lg"></i> Create First Rule
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
    {{ $rules->appends(request()->query())->links() }}
</div>
@endsection

