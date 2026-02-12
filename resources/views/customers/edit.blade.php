@extends('layouts.app')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Customer Information</h5>
                <span class="badge badge-status {{ $customer->status === 'active' ? 'badge-active' : ($customer->status === 'inactive' ? 'badge-inactive' : 'badge-blacklisted') }}">
                    {{ ucfirst($customer->status) }}
                </span>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Customer Type --}}
                    <div class="mb-3">
                        <label class="form-label">Customer Type <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group" aria-label="Customer type">
                            <input type="radio" class="btn-check" name="type" id="type_individual" 
                                   value="individual" {{ old('type', $customer->type) === 'individual' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="type_individual">Individual</label>

                            <input type="radio" class="btn-check" name="type" id="type_corporate" 
                                   value="corporate" {{ old('type', $customer->type) === 'corporate' ? 'checked' : '' }}>
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
                                   name="name" value="{{ old('name', $customer->name) }}" required>
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
                                   name="email" value="{{ old('email', $customer->email) }}" placeholder="optional">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Country Code <span class="text-danger">*</span></label>
                            <select class="form-select @error('country_code') is-invalid @enderror" name="country_code">
                                <option value="BH" {{ old('country_code', $customer->country_code) === 'BH' ? 'selected' : '' }}>BH (+973)</option>
                                <option value="SA" {{ old('country_code', $customer->country_code) === 'SA' ? 'selected' : '' }}>SA (+966)</option>
                                <option value="AE" {{ old('country_code', $customer->country_code) === 'AE' ? 'selected' : '' }}>AE (+971)</option>
                                <option value="KW" {{ old('country_code', $customer->country_code) === 'KW' ? 'selected' : '' }}>KW (+965)</option>
                                <option value="QA" {{ old('country_code', $customer->country_code) === 'QA' ? 'selected' : '' }}>QA (+974)</option>
                                <option value="OM" {{ old('country_code', $customer->country_code) === 'OM' ? 'selected' : '' }}>OM (+968)</option>
                                <option value="IN" {{ old('country_code', $customer->country_code) === 'IN' ? 'selected' : '' }}>IN (+91)</option>
                                <option value="PK" {{ old('country_code', $customer->country_code) === 'PK' ? 'selected' : '' }}>PK (+92)</option>
                                <option value="BD" {{ old('country_code', $customer->country_code) === 'BD' ? 'selected' : '' }}>BD (+880)</option>
                                <option value="PH" {{ old('country_code', $customer->country_code) === 'PH' ? 'selected' : '' }}>PH (+63)</option>
                                <option value="US" {{ old('country_code', $customer->country_code) === 'US' ? 'selected' : '' }}>US (+1)</option>
                                <option value="GB" {{ old('country_code', $customer->country_code) === 'GB' ? 'selected' : '' }}>GB (+44)</option>
                            </select>
                            @error('country_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mobile</label>
                            <div class="input-group">
                                <select class="form-select" name="country_code" style="max-width: 120px;">
                                    <option value="BH" {{ old('country_code', $customer->country_code) === 'BH' ? 'selected' : '' }}>+973</option>
                                    <option value="SA" {{ old('country_code', $customer->country_code) === 'SA' ? 'selected' : '' }}>+966</option>
                                    <option value="AE" {{ old('country_code', $customer->country_code) === 'AE' ? 'selected' : '' }}>+971</option>
                                    <option value="KW" {{ old('country_code', $customer->country_code) === 'KW' ? 'selected' : '' }}>+965</option>
                                    <option value="QA" {{ old('country_code', $customer->country_code) === 'QA' ? 'selected' : '' }}>+974</option>
                                    <option value="OM" {{ old('country_code', $customer->country_code) === 'OM' ? 'selected' : '' }}>+968</option>
                                    <option value="IN" {{ old('country_code', $customer->country_code) === 'IN' ? 'selected' : '' }}>+91</option>
                                    <option value="PK" {{ old('country_code', $customer->country_code) === 'PK' ? 'selected' : '' }}>+92</option>
                                    <option value="BD" {{ old('country_code', $customer->country_code) === 'BD' ? 'selected' : '' }}>+880</option>
                                    <option value="PH" {{ old('country_code', $customer->country_code) === 'PH' ? 'selected' : '' }}>+63</option>
                                    <option value="US" {{ old('country_code', $customer->country_code) === 'US' ? 'selected' : '' }}>+1</option>
                                    <option value="GB" {{ old('country_code', $customer->country_code) === 'GB' ? 'selected' : '' }}>+44</option>
                                </select>
                                <input type="text" class="form-control @error('mobile_number') is-invalid @enderror" 
                                       name="mobile_number" value="{{ old('mobile_number', $customer->mobile_number) }}">
                            </div>
                            @error('mobile_number')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Personal Details --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Nationality</label>
                            <select class="form-select @error('nationality') is-invalid @enderror" name="nationality">
                                <option value="">Select</option>
                                <option value="BH" {{ old('nationality', $customer->nationality) === 'BH' ? 'selected' : '' }}>Bahraini</option>
                                <option value="SA" {{ old('nationality', $customer->nationality) === 'SA' ? 'selected' : '' }}>Saudi</option>
                                <option value="AE" {{ old('nationality', $customer->nationality) === 'AE' ? 'selected' : '' }}>Emirati</option>
                                <option value="KW" {{ old('nationality', $customer->nationality) === 'KW' ? 'selected' : '' }}>Kuwaiti</option>
                                <option value="QA" {{ old('nationality', $customer->nationality) === 'QA' ? 'selected' : '' }}>Qatari</option>
                                <option value="OM" {{ old('nationality', $customer->nationality) === 'OM' ? 'selected' : '' }}>Omani</option>
                                <option value="EG" {{ old('nationality', $customer->nationality) === 'EG' ? 'selected' : '' }}>Egyptian</option>
                                <option value="JO" {{ old('nationality', $customer->nationality) === 'JO' ? 'selected' : '' }}>Jordanian</option>
                                <option value="LB" {{ old('nationality', $customer->nationality) === 'LB' ? 'selected' : '' }}>Lebanese</option>
                                <option value="SY" {{ old('nationality', $customer->nationality) === 'SY' ? 'selected' : '' }}>Syrian</option>
                                <option value="IN" {{ old('nationality', $customer->nationality) === 'IN' ? 'selected' : '' }}>Indian</option>
                                <option value="PK" {{ old('nationality', $customer->nationality) === 'PK' ? 'selected' : '' }}>Pakistani</option>
                                <option value="BD" {{ old('nationality', $customer->nationality) === 'BD' ? 'selected' : '' }}>Bangladeshi</option>
                                <option value="PH" {{ old('nationality', $customer->nationality) === 'PH' ? 'selected' : '' }}>Filipino</option>
                                <option value="US" {{ old('nationality', $customer->nationality) === 'US' ? 'selected' : '' }}>American</option>
                                <option value="GB" {{ old('nationality', $customer->nationality) === 'GB' ? 'selected' : '' }}>British</option>
                            </select>
                            @error('nationality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select class="form-select @error('gender') is-invalid @enderror" name="gender">
                                <option value="male" {{ old('gender', $customer->gender ?? 'male') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $customer->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                   name="date_of_birth" value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" rows="2">{{ old('address', $customer->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Corporate Fields --}}
                    <div class="corporate-fields" style="display: {{ $customer->type === 'corporate' ? 'block' : 'none' }}">
                        <hr>
                        <h6 class="text-muted mb-3">Corporate Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Company Name</label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       name="company_name" value="{{ old('company_name', $customer->company_name) }}">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Position</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       name="position" value="{{ old('position', $customer->position) }}">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status">
                            <option value="active" {{ old('status', $customer->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $customer->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="blacklisted" {{ old('status', $customer->status) === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Customer Info Card --}}
                    <div class="card bg-light mb-4">
                        <div class="card-body py-2">
                            <div class="row text-muted small">
                                <div class="col-md-3">
                                    <strong>Customer ID:</strong> {{ $customer->customer_id }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Registered:</strong> {{ $customer->created_at->format('d M Y') }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Outlet:</strong> {{ $customer->firstRegistrationOutlet?->name ?? 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Loyalty Points:</strong> {{ number_format($customer->loyaltyWallet?->total_points ?? 0) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Update Customer
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

