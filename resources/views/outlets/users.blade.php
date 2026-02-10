@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users - ' . $outlet->name)

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Outlet Users</h5>
            <small class="text-muted">{{ $outlet->name }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('outlets.show', $outlet) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Outlet
            </a>
        </div>
    </div>
    
    {{-- Add User Form --}}
    @if($availableUsers && $availableUsers->count() > 0)
    <div class="card-body border-bottom">
        <h6 class="mb-3">Add User to Outlet</h6>
        <form action="{{ route('outlets.users.store', $outlet) }}" method="POST" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-5">
                <label class="form-label">Select User</label>
                <select class="form-select @error('user_id') is-invalid @enderror" name="user_id" required>
                    <option value="">Choose a user...</option>
                    @foreach($availableUsers as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Role at Outlet</label>
                <select class="form-select" name="role_at_outlet">
                    <option value="staff">Staff</option>
                    <option value="manager">Manager</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus me-1"></i>Add User
                </button>
            </div>
        </form>
    </div>
    @endif
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role at Outlet</th>
                        <th>System Roles</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    @if($user->phone)
                                    <small class="text-muted">{{ $user->phone }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $user->pivot->role_at_outlet ?? 'staff' }}</span>
                        </td>
                        <td>
                            @forelse($user->roles as $role)
                            <span class="badge bg-secondary">{{ $role->name }}</span>
                            @empty
                            <span class="text-muted">No role</span>
                            @endforelse
                        </td>
                        <td>{{ $user->pivot->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <form action="{{ route('outlets.users.destroy', [$outlet, $user]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove User" onclick="return confirm('Are you sure you want to remove this user from the outlet?')">
                                    <i class="bi bi-person-dash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-people display-4"></i>
                                <p class="mt-2 mb-0">No users assigned to this outlet</p>
                                @if($availableUsers && $availableUsers->count() > 0)
                                <small>Use the form above to add users</small>
                                @endif
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

