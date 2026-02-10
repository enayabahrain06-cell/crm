@extends('layouts.app')

@section('title', 'Outlets')
@section('page-title', 'Outlet Management')

@section('content')
{{-- Header --}}
<div class="row mb-4">
    <div class="col-md-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Outlets</li>
            </ol>
        </nav>
        <h4 class="mb-0">Outlet Management</h4>
        <p class="text-muted mb-0">Manage your business locations and branches</p>
    </div>
    <div class="col-md-4 text-md-end">
        @can('outlets.create')
        <a href="{{ route('outlets.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>New Outlet
        </a>
        @endcan
    </div>
</div>

{{-- Quick Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary-subtle border-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-shop fs-3 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ $outlets->count() }}</div>
                        <div class="text-muted small">Total Outlets</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success-subtle border-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle fs-3 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ $outlets->count() > 0 ? $outlets->filter(fn($o) => $o->is_active)->count() : 0 }}</div>
                        <div class="text-muted small">Active</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-secondary-subtle border-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-pause-circle fs-3 text-secondary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ $outlets->count() > 0 ? $outlets->filter(fn($o) => !$o->is_active)->count() : 0 }}</div>
                        <div class="text-muted small">Inactive</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info-subtle border-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-people fs-3 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ $outlets->sum(fn($o) => $o->users_count ?? 0) }}</div>
                        <div class="text-muted small">Total Users</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <form action="{{ route('outlets.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Search outlets..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="sort">
                    <option value="name" {{ request('sort', 'name') === 'name' ? 'selected' : '' }}>Sort by Name</option>
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('outlets.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Results Count --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted">
        Showing <strong>{{ $outlets->count() }}</strong> of <strong>{{ $outlets->count() }}</strong> outlets
    </div>
</div>

{{-- Outlets Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Outlet</th>
                        <th>Code</th>
                        <th>Contact</th>
                        <th class="text-center">Users</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($outlets as $outlet)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="outlet-icon rounded d-flex align-items-center justify-content-center me-3
                                    {{ $outlet->is_active ? 'bg-success-subtle' : 'bg-secondary-subtle' }}
                                    {{ $outlet->is_active ? 'text-success' : 'text-secondary' }}"
                                    style="width: 45px; height: 45px;">
                                    <i class="bi bi-shop {{ $outlet->is_active ? '' : 'text-muted' }} fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $outlet->name }}</div>
                                    @if($outlet->description)
                                    <small class="text-muted">{{ Str::limit($outlet->description, 40) }}</small>
                                    @else
                                    <small class="text-muted">No description</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <code class="badge bg-light text-dark fs-6">{{ $outlet->code }}</code>
                        </td>
                        <td>
                            @if($outlet->address || $outlet->phone)
                            <div>
                                @if($outlet->address)
                                <small class="text-muted d-block">{{ Str::limit($outlet->address, 35) }}</small>
                                @endif
                                @if($outlet->phone)
                                <small><i class="bi bi-phone me-1"></i>{{ $outlet->phone }}</small>
                                @endif
                            </div>
                            @else
                            <small class="text-muted">No contact info</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $outlet->users_count > 0 ? 'bg-primary-subtle text-primary' : 'bg-light text-muted' }}">
                                <i class="bi bi-people me-1"></i>
                                {{ $outlet->users_count ?? 0 }}
                            </span>
                        </td>
                        <td>
                            @if($outlet->is_active)
                            <span class="badge bg-success-subtle text-success">
                                <i class="bi bi-check-circle me-1"></i>Active
                            </span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary">
                                <i class="bi bi-pause-circle me-1"></i>Inactive
                            </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('outlets.show', $outlet) }}" class="btn btn-outline-primary btn-sm" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('outlets.edit')
                                <a href="{{ route('outlets.edit', $outlet) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('outlets.view')
                                <a href="{{ route('outlets.users', $outlet) }}" class="btn btn-outline-info btn-sm" title="Users">
                                    <i class="bi bi-people"></i>
                                </a>
                                <a href="{{ route('outlets.qr', $outlet) }}" class="btn btn-outline-dark btn-sm" title="QR Code">
                                    <i class="bi bi-qr-code"></i>
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-shop display-4"></i>
                                <p class="mt-2 mb-0">No outlets found matching your criteria</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination --}}
@if($outlets instanceof \Illuminate\Contracts\Pagination\Paginator && $outlets->hasPages())
<div class="mt-4">
    {{ $outlets->appends(request()->query())->links() }}
</div>
@endif
@endsection


