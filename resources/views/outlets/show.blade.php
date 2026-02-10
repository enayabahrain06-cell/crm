@extends('layouts.app')

@section('title', $outlet->name)
@section('page-title', $outlet->name)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Outlet Image -->
        @if($outlet->hero_image || $outlet->logo)
        <div class="card mb-4 overflow-hidden">
            @if($outlet->hero_image)
            <img src="{{ Storage::url($outlet->hero_image) }}" alt="{{ $outlet->name }}" 
                 class="img-fluid w-100" style="height: 250px; object-fit: cover;">
            @elseif($outlet->logo)
            <div class="p-4 text-center bg-light">
                <img src="{{ Storage::url($outlet->logo) }}" alt="{{ $outlet->name }}" 
                     class="img-fluid" style="max-height: 150px;">
            </div>
            @endif
        </div>
        @endif

        <!-- Outlet Details Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Outlet Details</h5>
                <div class="btn-group">
                    @can('outlets.edit')
                    <a href="{{ route('outlets.edit', $outlet) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary-subtle rounded p-3 me-3">
                                <i class="bi bi-shop fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $outlet->name }}</h4>
                                <code class="text-muted">{{ $outlet->code }}</code>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge badge-status {{ $outlet->is_active ? 'badge-active' : 'badge-inactive' }} fs-6">
                            {{ $outlet->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                @if($outlet->description)
                <div class="mb-4">
                    <label class="text-muted small fw-bold">Description</label>
                    <p class="mb-0">{{ $outlet->description }}</p>
                </div>
                @endif

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small fw-bold">Contact Person</label>
                        <p class="mb-0">{{ $outlet->contact_person ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small fw-bold">Email</label>
                        <p class="mb-0">{{ $outlet->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small fw-bold">Phone</label>
                        <p class="mb-0">{{ $outlet->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small fw-bold">Opening Hours</label>
                        <p class="mb-0">{{ $outlet->opening_hours ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($outlet->address)
                <hr class="my-4">
                <div class="mb-0">
                    <label class="text-muted small fw-bold">Address</label>
                    <p class="mb-0">
                        {{ $outlet->address }}<br>
                        {{ $outlet->city ? $outlet->city . ',' : '' }} {{ $outlet->state }} {{ $outlet->postal_code }}<br>
                        {{ $outlet->country }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Stats Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Quick Stats</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Total Users</span>
                    <span class="badge bg-primary fs-6">{{ $outlet->users_count ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Created</span>
                    <span>{{ $outlet->created_at->format('M d, Y') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Last Updated</span>
                    <span>{{ $outlet->updated_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('outlets.view')
                    <a href="{{ route('outlets.users', $outlet) }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-people me-2"></i>Manage Users
                    </a>
                    <a href="{{ route('outlets.social-links', $outlet) }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-link-45deg me-2"></i>Social Links
                    </a>
                    <a href="{{ route('outlets.qr', $outlet) }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-qr-code me-2"></i>QR Code
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Social Links Preview -->
        @if($outlet->socialLinks && $outlet->socialLinks->count() > 0)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Social Links</h5>
                <a href="{{ route('outlets.social-links', $outlet) }}" class="btn btn-sm btn-outline-secondary">
                    Manage
                </a>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($outlet->socialLinks->where('is_active', true)->take(6) as $link)
                    <a href="{{ $link->url }}" target="_blank" class="btn btn-sm" 
                       style="background-color: {{ $link->color ?? '#6c757d' }}; color: white;">
                        <i class="bi bi-{{ $link->platform }}"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

