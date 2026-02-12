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
                            <select class="form-select @error('country_code') is-invalid @enderror" name="country_code">
                                <option value="BH" {{ old('country_code', 'BH') === 'BH' ? 'selected' : '' }}>BH (+973)</option>
                                <option value="SA" {{ old('country_code') === 'SA' ? 'selected' : '' }}>SA (+966)</option>
                                <option value="AE" {{ old('country_code') === 'AE' ? 'selected' : '' }}>AE (+971)</option>
                                <option value="KW" {{ old('country_code') === 'KW' ? 'selected' : '' }}>KW (+965)</option>
                                <option value="QA" {{ old('country_code') === 'QA' ? 'selected' : '' }}>QA (+974)</option>
                                <option value="OM" {{ old('country_code') === 'OM' ? 'selected' : '' }}>OM (+968)</option>
                                <option value="IN" {{ old('country_code') === 'IN' ? 'selected' : '' }}>IN (+91)</option>
                                <option value="PK" {{ old('country_code') === 'PK' ? 'selected' : '' }}>PK (+92)</option>
                                <option value="BD" {{ old('country_code') === 'BD' ? 'selected' : '' }}>BD (+880)</option>
                                <option value="PH" {{ old('country_code') === 'PH' ? 'selected' : '' }}>PH (+63)</option>
                                <option value="US" {{ old('country_code') === 'US' ? 'selected' : '' }}>US (+1)</option>
                                <option value="GB" {{ old('country_code') === 'GB' ? 'selected' : '' }}>GB (+44)</option>
                            </select>
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
                            <label class="form-label">Nationality</label>
                            <select class="form-select @error('nationality') is-invalid @enderror" name="nationality">
                                <option value="">Select</option>
                                <option value="BH" {{ old('nationality') === 'BH' ? 'selected' : '' }}>Bahraini</option>
                                <option value="SA" {{ old('nationality') === 'SA' ? 'selected' : '' }}>Saudi</option>
                                <option value="AE" {{ old('nationality') === 'AE' ? 'selected' : '' }}>Emirati</option>
                                <option value="KW" {{ old('nationality') === 'KW' ? 'selected' : '' }}>Kuwaiti</option>
                                <option value="QA" {{ old('nationality') === 'QA' ? 'selected' : '' }}>Qatari</option>
                                <option value="OM" {{ old('nationality') === 'OM' ? 'selected' : '' }}>Omani</option>
                                <option value="EG" {{ old('nationality') === 'EG' ? 'selected' : '' }}>Egyptian</option>
                                <option value="JO" {{ old('nationality') === 'JO' ? 'selected' : '' }}>Jordanian</option>
                                <option value="LB" {{ old('nationality') === 'LB' ? 'selected' : '' }}>Lebanese</option>
                                <option value="SY" {{ old('nationality') === 'SY' ? 'selected' : '' }}>Syrian</option>
                                <option value="IN" {{ old('nationality') === 'IN' ? 'selected' : '' }}>Indian</option>
                                <option value="PK" {{ old('nationality') === 'PK' ? 'selected' : '' }}>Pakistani</option>
                                <option value="BD" {{ old('nationality') === 'BD' ? 'selected' : '' }}>Bangladeshi</option>
                                <option value="PH" {{ old('nationality') === 'PH' ? 'selected' : '' }}>Filipino</option>
                                <option value="US" {{ old('nationality') === 'US' ? 'selected' : '' }}>American</option>
                                <option value="GB" {{ old('nationality') === 'GB' ? 'selected' : '' }}>British</option>
                            </select>
                            @error('nationality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

