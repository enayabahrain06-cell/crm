@extends('layouts.app')

@section('title', 'Create Customer')
@section('page-title', 'Create New Customer')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf

                    {{-- Customer Type --}}
                    <div class="mb-3">
                        <label class="form-label">Customer Type <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group" aria-label="Customer type">
                            <input type="radio" class="btn-check" name="type" id="type_individual" 
                                   value="individual" {{ old('type', 'individual') === 'individual' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="type_individual">Individual</label>

                            <input type="radio" class="btn-check" name="type" id="type_corporate" 
                                   value="corporate" {{ old('type') === 'corporate' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="type_corporate">Corporate</label>
                        </div>
                        @error('type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Basic Info --}}
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Contact Info --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" placeholder="optional">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Country Code <span class="text-danger">*</span></label>
                            <x-phone-code-dropdown 
                                name="country_code" 
                                id="country_code" 
                                :value="old('country_code', 'BH')" 
                                :required="true" 
                                label="Country Code"
                                placeholder="Select Country"
                            />
                            @error('country_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mobile <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('mobile_number') is-invalid @enderror" 
                                   name="mobile_number" value="{{ old('mobile_number') }}" placeholder="12345678" required>
                            @error('mobile_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Personal Details --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <x-country-dropdown 
                                name="nationality" 
                                id="nationality" 
                                :value="old('nationality')" 
                                :required="false" 
                                label="Nationality"
                                placeholder="Select Nationality"
                                mode="nationality"
                            />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select class="form-select @error('gender') is-invalid @enderror" name="gender">
                                <option value="male" {{ old('gender', 'male') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                   name="date_of_birth" value="{{ old('date_of_birth') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" rows="2">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Corporate Fields (shown conditionally) --}}
                    <div class="corporate-fields" style="display: {{ old('type') === 'corporate' ? 'block' : 'none' }}">
                        <hr>
                        <h6 class="text-muted mb-3">Corporate Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Company Name <span class="text-danger corporate-required">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       name="company_name" value="{{ old('company_name') }}">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Position <span class="text-danger corporate-required">*</span></label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       name="position" value="{{ old('position') }}">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Outlet Selection --}}
                    @if($outletId)
                        <input type="hidden" name="outlet_id" value="{{ $outletId }}">
                    @else
                    <div class="mb-3">
                        <label class="form-label">Registration Outlet</label>
                        <select class="form-select @error('outlet_id') is-invalid @enderror" name="outlet_id">
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                        @error('outlet_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    {{-- Status --}}
                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="blacklisted" {{ old('status') === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Create Customer
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
    const typeIndividual = document.getElementById('type_individual');
    const typeCorporate = document.getElementById('type_corporate');
    const corporateFields = document.querySelector('.corporate-fields');

    function toggleCorporateFields() {
        corporateFields.style.display = typeCorporate.checked ? 'block' : 'none';
    }

    typeIndividual.addEventListener('change', toggleCorporateFields);
    typeCorporate.addEventListener('change', toggleCorporateFields);

    // Initial state
    toggleCorporateFields();
});
</script>
@endpush
@endsection

