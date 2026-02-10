@extends('layouts.app')

@section('title', 'Create Outlet')
@section('page-title', 'Create New Outlet')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Outlet Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('outlets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Outlet Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Person</label>
                            <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                   name="contact_person" value="{{ old('contact_person') }}">
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" rows="2">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-select @error('country') is-invalid @enderror" 
                                    id="country" name="country" required>
                                <option value="">Select Country</option>
                                <option value="Bahrain" {{ old('country') === 'Bahrain' ? 'selected' : '' }}>🇧🇭 Bahrain</option>
                                <option value="Saudi Arabia" {{ old('country') === 'Saudi Arabia' ? 'selected' : '' }}>🇸🇦 Saudi Arabia</option>
                                <option value="UAE" {{ old('country') === 'UAE' ? 'selected' : '' }}>🇦🇪 UAE</option>
                                <option value="Kuwait" {{ old('country') === 'Kuwait' ? 'selected' : '' }}>🇰🇼 Kuwait</option>
                                <option value="Qatar" {{ old('country') === 'Qatar' ? 'selected' : '' }}>🇶🇦 Qatar</option>
                                <option value="Oman" {{ old('country') === 'Oman' ? 'selected' : '' }}>🇴🇲 Oman</option>
                                <option value="Egypt" {{ old('country') === 'Egypt' ? 'selected' : '' }}>🇪🇬 Egypt</option>
                                <option value="Jordan" {{ old('country') === 'Jordan' ? 'selected' : '' }}>🇯🇴 Jordan</option>
                                <option value="India" {{ old('country') === 'India' ? 'selected' : '' }}>🇮🇳 India</option>
                                <option value="Pakistan" {{ old('country') === 'Pakistan' ? 'selected' : '' }}>🇵🇰 Pakistan</option>
                                <option value="Philippines" {{ old('country') === 'Philippines' ? 'selected' : '' }}>🇵🇭 Philippines</option>
                                <option value="USA" {{ old('country') === 'USA' ? 'selected' : '' }}>🇺🇸 USA</option>
                                <option value="UK" {{ old('country') === 'UK' ? 'selected' : '' }}>🇬🇧 UK</option>
                            </select>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State/Province <span class="text-danger">*</span></label>
                            <select class="form-select @error('state') is-invalid @enderror" 
                                    id="state" name="state" required>
                                <option value="">Select State/Province</option>
                            </select>
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   name="city" value="{{ old('city') }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Opening Hours</label>
                        <textarea class="form-control @error('opening_hours') is-invalid @enderror" 
                                  name="opening_hours" rows="2" placeholder="e.g., Mon-Sat: 9AM - 9PM">{{ old('opening_hours') }}</textarea>
                        @error('opening_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="active" 
                                   id="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
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
                    </div>

                    <!-- Hero Image Upload -->
                    <div class="mb-3">
                        <label class="form-label">Hero Image</label>
                        <input type="file" class="form-control @error('hero_image') is-invalid @enderror" 
                               name="hero_image" accept="image/*">
                        @error('hero_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('outlets.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Create Outlet
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
    const countrySelect = document.getElementById('country');
    const stateSelect = document.getElementById('state');
    const oldState = '{{ old('state', '') }}';

    // States/Provinces data with flags
    const statesByCountry = {
        'Bahrain': [
            { value: 'Capital Governorate', label: '🇧🇭 Capital Governorate (Manama)' },
            { value: 'Northern Governorate', label: '🇧🇭 Northern Governorate' },
            { value: 'Southern Governorate', label: '🇧🇭 Southern Governorate' },
            { value: 'Muharraq Governorate', label: '🇧🇭 Muharraq Governorate' }
        ],
        'Saudi Arabia': [
            { value: 'Riyadh', label: '🇸🇦 Riyadh' },
            { value: 'Makkah', label: '🇸🇦 Makkah (Jeddah)' },
            { value: 'Madinah', label: '🇸🇦 Madinah' },
            { value: 'Eastern Province', label: '🇸🇦 Eastern Province (Dammam)' },
            { value: 'Al-Qassim', label: '🇸🇦 Al-Qassim' },
            { value: 'Asir', label: '🇸🇦 Asir' },
            { value: 'Tabuk', label: '🇸🇦 Tabuk' },
            { value: 'Hail', label: '🇸🇦 Hail' },
            { value: 'Northern Borders', label: '🇸🇦 Northern Borders' },
            { value: 'Jizan', label: '🇸🇦 Jizan' },
            { value: 'Najran', label: '🇸🇦 Najran' },
            { value: 'Al-Baha', label: '🇸🇦 Al-Baha' },
            { value: 'Al-Jawf', label: '🇸🇦 Al-Jawf' }
        ],
        'UAE': [
            { value: 'Abu Dhabi', label: '🇦🇪 Abu Dhabi' },
            { value: 'Dubai', label: '🇦🇪 Dubai' },
            { value: 'Sharjah', label: '🇦🇪 Sharjah' },
            { value: 'Ajman', label: '🇦🇪 Ajman' },
            { value: 'Fujairah', label: '🇦🇪 Fujairah' },
            { value: 'Ras Al Khaimah', label: '🇦🇪 Ras Al Khaimah' },
            { value: 'Umm Al Quwain', label: '🇦🇪 Umm Al Quwain' }
        ],
        'Kuwait': [
            { value: 'Al Ahmadi', label: '🇰🇼 Al Ahmadi' },
            { value: 'Al Asimah', label: '🇰🇼 Al Asimah (Kuwait City)' },
            { value: 'Al Farwaniyah', label: '🇰🇼 Al Farwaniyah' },
            { value: 'Al Jahra', label: '🇰🇼 Al Jahra' },
            { value: 'Hawalli', label: '🇰🇼 Hawalli' },
            { value: 'Mubarak Al-Kabeer', label: '🇰🇼 Mubarak Al-Kabeer' }
        ],
        'Qatar': [
            { value: 'Doha', label: '🇶🇦 Doha' },
            { value: 'Al Rayyan', label: '🇶🇦 Al Rayyan' },
            { value: 'Al Wakrah', label: '🇶🇦 Al Wakrah' },
            { value: 'Al Khor', label: '🇶🇦 Al Khor' },
            { value: 'Al Shamal', label: '🇶🇦 Al Shamal' },
            { value: 'Um Slal', label: '🇶🇦 Um Slal' },
            { value: 'Al Daayen', label: '🇶🇦 Al Daayen' }
        ],
        'Oman': [
            { value: 'Muscat', label: '🇴🇲 Muscat' },
            { value: 'Dhofar', label: '🇴🇲 Dhofar (Salalah)' },
            { value: 'North Batinah', label: '🇴🇲 North Batinah' },
            { value: 'South Batinah', label: '🇴🇲 South Batinah' },
            { value: 'North Sharqiyah', label: '🇴🇲 North Sharqiyah' },
            { value: 'South Sharqiyah', label: '🇴🇲 South Sharqiyah' },
            { value: 'Al Batinah North', label: '🇴🇲 Al Batinah North' },
            { value: 'Al Batinah South', label: '🇴🇲 Al Batinah South' },
            { value: 'Al Dhahirah', label: '🇴🇲 Al Dhahirah' },
            { value: 'Al Masirah', label: '🇴🇲 Al Masirah' },
            { value: 'Wusta', label: '🇴🇲 Wusta' }
        ],
        'Egypt': [
            { value: 'Cairo', label: '🇪🇬 Cairo' },
            { value: 'Giza', label: '🇪🇬 Giza' },
            { value: 'Alexandria', label: '🇪🇬 Alexandria' },
            { value: 'Luxor', label: '🇪🇬 Luxor' },
            { value: 'Aswan', label: '🇪🇬 Aswan' },
            { value: 'Red Sea', label: '🇪🇬 Red Sea (Hurghada)' },
            { value: 'North Coast', label: '🇪🇬 North Coast' },
            { value: 'Suez', label: '🇪🇬 Suez' },
            { value: 'Ismailia', label: '🇪🇬 Ismailia' },
            { value: 'Port Said', label: '🇪🇬 Port Said' },
            { value: 'Dakahlia', label: '🇪🇬 Dakahlia' },
            { value: 'Sharqia', label: '🇪🇬 Sharqia' }
        ],
        'Jordan': [
            { value: 'Amman', label: '🇯🇴 Amman' },
            { value: 'Zarqa', label: '🇯🇴 Zarqa' },
            { value: 'Irbid', label: '🇯🇴 Irbid' },
            { value: 'Aqaba', label: '🇯🇴 Aqaba' },
            { value: 'Balqa', label: '🇯🇴 Balqa' },
            { value: 'Karak', label: '🇯🇴 Karak' },
            { value: 'Mafraq', label: '🇯🇴 Mafraq' },
            { value: 'Jerash', label: '🇯🇴 Jerash' },
            { value: 'Madaba', label: '🇯🇴 Madaba' },
            { value: 'Mafraq', label: '🇯🇴 Tafilah' },
            { value: 'Ma\'an', label: '🇯🇴 Ma\'an' }
        ],
        'India': [
            { value: 'Andhra Pradesh', label: '🇮🇳 Andhra Pradesh' },
            { value: 'Arunachal Pradesh', label: '🇮🇳 Arunachal Pradesh' },
            { value: 'Assam', label: '🇮🇳 Assam' },
            { value: 'Bihar', label: '🇮🇳 Bihar' },
            { value: 'Chhattisgarh', label: '🇮🇳 Chhattisgarh' },
            { value: 'Delhi', label: '🇮🇳 Delhi' },
            { value: 'Gujarat', label: '🇮🇳 Gujarat' },
            { value: 'Haryana', label: '🇮🇳 Haryana' },
            { value: 'Himachal Pradesh', label: '🇮🇳 Himachal Pradesh' },
            { value: 'Jharkhand', label: '🇮🇳 Jharkhand' },
            { value: 'Karnataka', label: '🇮🇳 Karnataka' },
            { value: 'Kerala', label: '🇮🇳 Kerala' },
            { value: 'Madhya Pradesh', label: '🇮🇳 Madhya Pradesh' },
            { value: 'Maharashtra', label: '🇮🇳 Maharashtra' },
            { value: 'Odisha', label: '🇮🇳 Odisha' },
            { value: 'Punjab', label: '🇮🇳 Punjab' },
            { value: 'Rajasthan', label: '🇮🇳 Rajasthan' },
            { value: 'Tamil Nadu', label: '🇮🇳 Tamil Nadu' },
            { value: 'Telangana', label: '🇮🇳 Telangana' },
            { value: 'Uttar Pradesh', label: '🇮🇳 Uttar Pradesh' },
            { value: 'West Bengal', label: '🇮🇳 West Bengal' },
            { value: 'Other', label: '🇮🇳 Other' }
        ],
        'Pakistan': [
            { value: 'Punjab', label: '🇵🇰 Punjab' },
            { value: 'Sindh', label: '🇵🇰 Sindh (Karachi)' },
            { value: 'Khyber Pakhtunkhwa', label: '🇵🇰 Khyber Pakhtunkhwa' },
            { value: 'Balochistan', label: '🇵🇰 Balochistan' },
            { value: 'Gilgit-Baltistan', label: '🇵🇰 Gilgit-Baltistan' },
            { value: 'Azad Kashmir', label: '🇵🇰 Azad Kashmir' },
            { value: 'Islamabad', label: '🇵🇰 Islamabad Capital Territory' }
        ],
        'Philippines': [
            { value: 'Metro Manila', label: '🇵🇭 Metro Manila' },
            { value: 'Cebu', label: '🇵🇭 Cebu' },
            { value: 'Luzon', label: '🇵🇭 Luzon' },
            { value: 'Visayas', label: '🇵🇭 Visayas' },
            { value: 'Mindanao', label: '🇵🇭 Mindanao' },
            { value: 'Davao', label: '🇵🇭 Davao' },
            { value: 'Bohol', label: '🇵🇭 Bohol' },
            { value: 'Palawan', label: '🇵🇭 Palawan' }
        ],
        'USA': [
            { value: 'Alabama', label: '🇺🇸 Alabama' },
            { value: 'Alaska', label: '🇺🇸 Alaska' },
            { value: 'Arizona', label: '🇺🇸 Arizona' },
            { value: 'California', label: '🇺🇸 California' },
            { value: 'Colorado', label: '🇺🇸 Colorado' },
            { value: 'Florida', label: '🇺🇸 Florida' },
            { value: 'Georgia', label: '🇺🇸 Georgia' },
            { value: 'Illinois', label: '🇺🇸 Illinois' },
            { value: 'New York', label: '🇺🇸 New York' },
            { value: 'Texas', label: '🇺🇸 Texas' },
            { value: 'Washington', label: '🇺🇸 Washington' },
            { value: 'Other', label: '🇺🇸 Other States' }
        ],
        'UK': [
            { value: 'England', label: '🇬🇧 England' },
            { value: 'Scotland', label: '🇬🇧 Scotland' },
            { value: 'Wales', label: '🇬🇧 Wales' },
            { value: 'Northern Ireland', label: '🇬🇧 Northern Ireland' },
            { value: 'London', label: '🇬🇧 London' },
            { value: 'Manchester', label: '🇬🇧 Manchester' },
            { value: 'Birmingham', label: '🇬🇧 Birmingham' },
            { value: 'Other', label: '🇬🇧 Other Regions' }
        ]
    };

    function updateStates(country, preselectState = null) {
        // Clear existing options
        stateSelect.innerHTML = '<option value="">Select State/Province</option>';
        
        if (country && statesByCountry[country]) {
            statesByCountry[country].forEach(state => {
                const option = document.createElement('option');
                option.value = state.value;
                option.textContent = state.label;
                if (preselectState && state.value === preselectState) {
                    option.selected = true;
                }
                stateSelect.appendChild(option);
            });
        }
    }

    // Handle country change
    countrySelect.addEventListener('change', function() {
        updateStates(this.value);
    });

    // Initialize on page load
    const selectedCountry = countrySelect.value;
    if (selectedCountry && oldState) {
        updateStates(selectedCountry, oldState);
    }
});
</script>
@endpush

@endsection

