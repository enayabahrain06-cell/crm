@extends('layouts.app')

@section('title', 'User & Role Management')
@section('page-title', 'User & Role Management')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="User name or email...">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="userRoleTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ !request('tab') || request('tab') === 'users' ? 'active' : '' }}" 
                id="users-tab" data-bs-toggle="tab" data-bs-target="#users-pane" 
                type="button" role="tab" aria-controls="users-pane" 
                aria-selected="{{ !request('tab') || request('tab') === 'users' ? 'true' : 'false' }}">
            <i class="bi bi-people me-2"></i>Users
            <span class="badge bg-primary ms-2">{{ $users->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request('tab') === 'roles' ? 'active' : '' }}" 
                id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles-pane" 
                type="button" role="tab" aria-controls="roles-pane" 
                aria-selected="{{ request('tab') === 'roles' ? 'true' : 'false' }}">
            <i class="bi bi-shield-check me-2"></i>Roles
        </button>
    </li>
</ul>

<div class="tab-content" id="userRoleTabsContent">
    <!-- Users Tab -->
    <div class="tab-pane fade {{ !request('tab') || request('tab') === 'users' ? 'show active' : '' }}" 
         id="users-pane" role="tabpanel" aria-labelledby="users-tab">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted">
                Showing {{ $users->count() }} {{ $users->count() === 1 ? 'user' : 'users' }}
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> New User
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Roles</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                </td>
                                <td>
                                    {{ $user->email }}
                                </td>
                                <td>
                                    @if($user->phone)
                                    {{ $user->phone }}
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @forelse($user->roles as $role)
                                    <span class="badge bg-info">{{ $role->name }}</span>
                                    @empty
                                    <span class="badge bg-secondary">No Role</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span class="badge badge-status {{ $user->active ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $user->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}">
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </a></li>
                                            @if($user->active)
                                            <li>
                                                <form action="{{ route('admin.users.deactivate', $user) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to deactivate this user?')">
                                                        <i class="bi bi-slash-circle me-2"></i>Deactivate
                                                    </button>
                                                </form>
                                            </li>
                                            @else
                                            <li>
                                                <form action="{{ route('admin.users.activate', $user) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to activate this user?')">
                                                        <i class="bi bi-check-circle me-2"></i>Activate
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                            @if($user->id !== auth()->id())
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure? This action cannot be undone.')">
                                                        <i class="bi bi-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people display-4"></i>
                                        <p class="mt-2">No users found</p>
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-lg"></i> Create Your First User
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($users instanceof \Illuminate\Contracts\Pagination\Paginator && $users->hasPages())
        <div class="mt-4">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Roles Tab -->
    <div class="tab-pane fade {{ request('tab') === 'roles' ? 'show active' : '' }}" 
         id="roles-pane" role="tabpanel" aria-labelledby="roles-tab">
        
        @php
        use Spatie\Permission\Models\Role;
        $roles = Role::with('permissions')->paginate(10);
        @endphp

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted">
                Showing {{ $roles->count() }} {{ $roles->count() === 1 ? 'role' : 'roles' }}
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.users.roles.create', ['tab' => 'roles']) }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> New Role
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Guard</th>
                                <th>Permissions</th>
                                <th>Users</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $role->name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $role->guard_name }}</span>
                                </td>
                                <td>
                                    @if($role->permissions->count() > 0)
                                        <span class="badge bg-info">{{ $role->permissions->count() }} permissions</span>
                                    @else
                                        <span class="badge bg-warning">No permissions</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">{{ $role->users_count ?? 0 }} users</span>
                                </td>
                                <td>
                                    {{ $role->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('admin.users.roles.edit', [$role, 'tab' => 'roles']) }}">
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </a></li>
                                            @if($role->name !== 'super_admin')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.users.roles.destroy', [$role, 'tab' => 'roles']) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure? This action cannot be undone.')">
                                                        <i class="bi bi-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-shield-lock display-4"></i>
                                        <p class="mt-2">No roles found</p>
                                        <a href="{{ route('admin.users.roles.create', ['tab' => 'roles']) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-lg"></i> Create Your First Role
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($roles instanceof \Illuminate\Contracts\Pagination\Paginator && $roles->hasPages())
        <div class="mt-4">
            {{ $roles->appends(['tab' => 'roles'])->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.nav-tabs {
    border-bottom: 2px solid #E2E8F0;
}

.nav-tabs .nav-link {
    color: var(--text-secondary);
    border: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    padding: 0.75rem 1.25rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.nav-tabs .nav-link:hover {
    color: var(--primary-color);
    border-color: transparent;
}

.nav-tabs .nav-link.active {
    color: var(--primary-color);
    background: transparent;
    border-bottom-color: var(--primary-color);
}

.nav-tabs .nav-link .badge {
    font-size: 0.6875rem;
}
</style>
@endsection
