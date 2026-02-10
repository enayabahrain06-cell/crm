@extends('layouts.app')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Role: {{ $role->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.roles.update', [$role, 'tab' => 'roles']) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $role->name) }}" 
                               placeholder="e.g., editor, moderator" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">The role name must be unique.</div>
                    </div>

                    <div class="mb-4">
                        <label for="guard_name" class="form-label">Guard Name</label>
                        <select class="form-select @error('guard_name') is-invalid @enderror" id="guard_name" name="guard_name">
                            <option value="web" {{ old('guard_name', $role->guard_name) == 'web' ? 'selected' : '' }}>web</option>
                            <option value="api" {{ old('guard_name', $role->guard_name) == 'api' ? 'selected' : '' }}>api</option>
                        </select>
                        @error('guard_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">The authentication guard to use for this role.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Permissions</label>
                        <div class="row">
                            @forelse($permissions->groupBy(function($item) {
                                return explode('.', $item->name)[0];
                            }) as $group => $groupPermissions)
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-header py-2">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="select-all-{{ $group }}"
                                                   {{ $groupPermissions->every(function($p) use ($rolePermissions) {
                                                       return in_array($p->id, $rolePermissions);
                                                   }) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="select-all-{{ $group }}">
                                                {{ ucfirst($group) }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body py-2">
                                        @foreach($groupPermissions as $permission)
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" 
                                                   type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}"
                                                   id="permission-{{ $permission->id }}"
                                                   {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <p class="text-muted">No permissions available.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index', ['tab' => 'roles']) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Update Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle select all checkboxes for each permission group
    document.querySelectorAll('[id^="select-all-"]').forEach(function(selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const groupCard = this.closest('.card');
            const checkboxes = groupCard.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    });

    // Update select all checkbox state when individual checkboxes change
    document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const groupCard = this.closest('.card');
            const selectAllCheckbox = groupCard.querySelector('[id^="select-all-"]');
            const allCheckboxes = groupCard.querySelectorAll('.permission-checkbox');
            const checkedCheckboxes = groupCard.querySelectorAll('.permission-checkbox:checked');
            
            if (allCheckboxes.length === checkedCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCheckboxes.length > 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
        });
    });
});
</script>
@endpush
