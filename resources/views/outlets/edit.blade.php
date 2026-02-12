@extends('layouts.app')

@section('title', 'Edit Outlet')
@section('page-title', 'Edit Outlet')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Outlet Information</h5>
                <span class="badge badge-status {{ $outlet->is_active ? 'badge-active' : 'badge-inactive' }}">
                    {{ $outlet->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="card-body">
                <form action="{{ route('outlets.update', $outlet) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Outlet Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name', $outlet->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control bg-light" value="{{ $outlet->code }}" disabled>
                            <small class="text-muted">Code cannot be changed</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Contact Person</label>
                            <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                   name="contact_person" value="{{ old('contact_person', $outlet->contact_person) }}">
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   name="phone" value="{{ old('phone', $outlet->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email', $outlet->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" rows="2">{{ old('address', $outlet->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   name="city" value="{{ old('city', $outlet->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State/Province</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                   name="state" value="{{ old('state', $outlet->state) }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Postal Code</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                   name="postal_code" value="{{ old('postal_code', $outlet->postal_code) }}">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <x-country-dropdown 
                            name="country" 
                            id="country" 
                            :value="old('country', $outlet->country)" 
                            :required="false" 
                            label="Country"
                            placeholder="Select Country"
                        />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3">{{ old('description', $outlet->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Opening Hours</label>
                        <textarea class="form-control @error('opening_hours') is-invalid @enderror" 
                                  name="opening_hours" rows="2">{{ old('opening_hours', $outlet->opening_hours) }}</textarea>
                        @error('opening_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="active" 
                                   id="active" value="1" {{ old('active', $outlet->active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                Active (Outlet will be visible and operational)
                            </label>
                        </div>
                    </div>

                    <!-- Logo Upload -->
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                               name="logo" accept="image/*">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($outlet->logo)
                        <div class="mt-2">
                            <img src="{{ Storage::url($outlet->logo) }}" alt="Current Logo" 
                                 class="img-thumbnail" style="max-height: 80px;">
                            <span class="text-muted ms-2">Current logo</span>
                        </div>
                        @endif
                    </div>

                    <!-- Hero Image Upload -->
                    <div class="mb-3">
                        <label class="form-label">Hero Image</label>
                        <input type="file" class="form-control @error('hero_image') is-invalid @enderror" 
                               name="hero_image" accept="image/*">
                        @error('hero_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($outlet->hero_image)
                        <div class="mt-2">
                            <img src="{{ Storage::url($outlet->hero_image) }}" alt="Current Hero Image" 
                                 class="img-thumbnail" style="max-height: 120px;">
                            <span class="text-muted ms-2">Current hero image</span>
                        </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('outlets.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Update Outlet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

