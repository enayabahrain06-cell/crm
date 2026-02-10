<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ $outlet->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .registration-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            text-align: center;
            padding: 2rem;
        }
        .outlet-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            font-weight: 600;
            padding: 12px 24px;
            position: relative;
            z-index: 1;
        }
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
        }
        .btn-primary-custom:focus {
            box-shadow: 0 0 0 0.25rem rgba(124, 58, 237, 0.5);
        }
        .section-title {
            color: #4f46e5;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e0e7ff;
        }
        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.4rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 12px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.25);
        }
        .input-group-text {
            border-radius: 8px 0 0 8px;
            background-color: #f3f4f6;
            border-color: #d1d5db;
        }
        .invalid-feedback {
            font-size: 0.8rem;
        }
        .registration-type-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
        }
        .registration-type-card:hover {
            border-color: #7c3aed;
            background-color: #faf5ff;
        }
        .registration-type-card.active {
            border-color: #7c3aed;
            background-color: #f5f3ff;
        }
        .registration-type-card i {
            font-size: 2.5rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        .registration-type-card.active i {
            color: #7c3aed;
        }
        .terms-checkbox {
            display: flex;
            align-items: start;
            gap: 0.5rem;
        }
        .terms-checkbox input {
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="registration-card">
                    <!-- Header Section -->
                    <div class="card-header-custom">
                        <div class="outlet-logo">
                            <i class="bi bi-shop"></i>
                        </div>
                        <h3 class="mb-2">{{ $outlet->name }}</h3>
                        <p class="mb-0 opacity-75">{{ $outlet->description ?? 'Join our loyalty program and enjoy exclusive benefits!' }}</p>
                    </div>

                    <div class="card-body p-4">
                        <!-- Form Start -->
                        <form id="registrationForm" action="{{ route('public.register.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="outlet_code" value="{{ $outlet->code }}">
                            <input type="hidden" name="type" value="individual">

                            <!-- Session Error Messages -->
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Registration Type Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Select Registration Type:</label>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="registration-type-card active" data-type="individual">
                                            <i class="bi bi-person-fill"></i>
                                            <h6 class="mb-1">Individual</h6>
                                            <small class="text-muted">Personal membership</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="registration-type-card" data-type="corporate">
                                            <i class="bi bi-building-fill"></i>
                                            <h6 class="mb-1">Corporate</h6>
                                            <small class="text-muted">Business account</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Individual Registration Fields -->
                            <div id="individualFields">
                                <!-- Personal Information Section -->
                                <div class="mb-4">
                                    <h5 class="section-title">
                                        <i class="bi bi-person-vcard-fill me-2"></i>Personal Information
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="first_name">First Name *</label>
                                            <input type="text" 
                                                   class="form-control individual-required @error('first_name') is-invalid @enderror" 
                                                   id="first_name" 
                                                   name="first_name" 
                                                   value="{{ old('first_name') }}"
                                                   placeholder="First name"
                                                   required>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="last_name">Last Name *</label>
                                            <input type="text" 
                                                   class="form-control individual-required @error('last_name') is-invalid @enderror" 
                                                   id="last_name" 
                                                   name="last_name" 
                                                   value="{{ old('last_name') }}"
                                                   placeholder="Last name"
                                                   required>
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="email">Email Address *</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email') }}"
                                                   placeholder="you@example.com"
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="date_of_birth">Date of Birth</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-calendar-fill"></i></span>
                                                <input type="date" 
                                                       class="form-control @error('date_of_birth') is-invalid @enderror" 
                                                       id="date_of_birth" 
                                                       name="date_of_birth" 
                                                       value="{{ old('date_of_birth') }}">
                                                @error('date_of_birth')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="gender">Gender</label>
                                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                                <option value="">Select Gender</option>
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                                <option value="unknown" {{ old('gender') == 'unknown' ? 'selected' : '' }}>Prefer not to say</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information Section -->
                                <div class="mb-4">
                                    <h5 class="section-title">
                                        <i class="bi bi-telephone-fill me-2"></i>Contact Information
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="country_code">Country</label>
                                            <select class="form-select @error('country_code') is-invalid @enderror" id="country_code" name="country_code">
                                                <option value="BH" {{ old('country_code', 'BH') == 'BH' ? 'selected' : '' }}>🇧🇭 Bahrain (+973)</option>
                                                <option value="SA" {{ old('country_code') == 'SA' ? 'selected' : '' }}>🇸🇦 Saudi Arabia (+966)</option>
                                                <option value="AE" {{ old('country_code') == 'AE' ? 'selected' : '' }}>🇦🇪 UAE (+971)</option>
                                                <option value="KW" {{ old('country_code') == 'KW' ? 'selected' : '' }}>🇰🇼 Kuwait (+965)</option>
                                                <option value="QA" {{ old('country_code') == 'QA' ? 'selected' : '' }}>🇶🇦 Qatar (+974)</option>
                                                <option value="OM" {{ old('country_code') == 'OM' ? 'selected' : '' }}>🇴🇲 Oman (+968)</option>
                                            </select>
                                            @error('country_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label" for="mobile_number">Mobile Number *</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-phone-fill"></i></span>
                                                <input type="tel" 
                                                       class="form-control @error('mobile_number') is-invalid @enderror" 
                                                       id="mobile_number" 
                                                       name="mobile_number" 
                                                       value="{{ old('mobile_number') }}"
                                                       placeholder="39123456"
                                                       required>
                                                @error('mobile_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="nationality">Nationality</label>
                                        <select class="form-select @error('nationality') is-invalid @enderror" id="nationality" name="nationality">
                                            <option value="">Select Nationality</option>
                                            <option value="BH" {{ old('nationality') == 'BH' ? 'selected' : '' }}>Bahraini</option>
                                            <option value="SA" {{ old('nationality') == 'SA' ? 'selected' : '' }}>Saudi</option>
                                            <option value="AE" {{ old('nationality') == 'AE' ? 'selected' : '' }}>Emirati</option>
                                            <option value="KW" {{ old('nationality') == 'KW' ? 'selected' : '' }}>Kuwaiti</option>
                                            <option value="QA" {{ old('nationality') == 'QA' ? 'selected' : '' }}>Qatari</option>
                                            <option value="OM" {{ old('nationality') == 'OM' ? 'selected' : '' }}>Omani</option>
                                            <option value="EG" {{ old('nationality') == 'EG' ? 'selected' : '' }}>Egyptian</option>
                                            <option value="JO" {{ old('nationality') == 'JO' ? 'selected' : '' }}>Jordanian</option>
                                            <option value="IN" {{ old('nationality') == 'IN' ? 'selected' : '' }}>Indian</option>
                                            <option value="PK" {{ old('nationality') == 'PK' ? 'selected' : '' }}>Pakistani</option>
                                            <option value="PH" {{ old('nationality') == 'PH' ? 'selected' : '' }}>Filipino</option>
                                            <option value="US" {{ old('nationality') == 'US' ? 'selected' : '' }}>American</option>
                                            <option value="GB" {{ old('nationality') == 'GB' ? 'selected' : '' }}>British</option>
                                        </select>
                                        @error('nationality')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="address">Address (Optional)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-house-fill"></i></span>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      id="address" 
                                                      name="address" 
                                                      rows="2"
                                                      placeholder="Enter your address">{{ old('address') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Corporate Registration Fields -->
                            <div id="corporateFields" style="display: none;">
                                <!-- Company Information Section -->
                                <div class="mb-4">
                                    <h5 class="section-title">
                                        <i class="bi bi-building-fill me-2"></i>Company Information
                                    </h5>
                                    <div class="mb-3">
                                        <label class="form-label" for="company_name">Company Name *</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-briefcase-fill"></i></span>
                                            <input type="text" 
                                                   class="form-control corporate-required @error('company_name') is-invalid @enderror" 
                                                   id="company_name" 
                                                   name="company_name" 
                                                   value="{{ old('company_name') }}"
                                                   placeholder="Your company name">
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label" for="corporate_name">Contact Person *</label>
                                            <input type="text" 
                                                   class="form-control corporate-required @error('name') is-invalid @enderror" 
                                                   id="corporate_name" 
                                                   name="name" 
                                                   value="{{ old('name') }}"
                                                   placeholder="Full name">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="position">Position</label>
                                            <input type="text" 
                                                   class="form-control @error('position') is-invalid @enderror" 
                                                   id="position" 
                                                   name="position" 
                                                   value="{{ old('position') }}"
                                                   placeholder="Your role">
                                            @error('position')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Corporate Contact Section -->
                                <div class="mb-4">
                                    <h5 class="section-title">
                                        <i class="bi bi-telephone-fill me-2"></i>Corporate Contact Details
                                    </h5>
                                    <div class="mb-3">
                                        <label class="form-label" for="corporate_email">Business Email *</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                            <input type="email" 
                                                   class="form-control corporate-required @error('corporate_email') is-invalid @enderror" 
                                                   id="corporate_email" 
                                                   name="corporate_email" 
                                                   value="{{ old('corporate_email') }}"
                                                   placeholder="you@company.com">
                                            @error('corporate_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="corporate_country_code">Country</label>
                                            <select class="form-select corporate-required @error('corporate_country_code') is-invalid @enderror" id="corporate_country_code" name="corporate_country_code">
                                                <option value="BH" {{ old('corporate_country_code', 'BH') == 'BH' ? 'selected' : '' }}>🇧🇭 Bahrain (+973)</option>
                                                <option value="SA" {{ old('corporate_country_code') == 'SA' ? 'selected' : '' }}>🇸🇦 Saudi Arabia (+966)</option>
                                                <option value="AE" {{ old('corporate_country_code') == 'AE' ? 'selected' : '' }}>🇦🇪 UAE (+971)</option>
                                                <option value="KW" {{ old('corporate_country_code') == 'KW' ? 'selected' : '' }}>🇰🇼 Kuwait (+965)</option>
                                                <option value="QA" {{ old('corporate_country_code') == 'QA' ? 'selected' : '' }}>🇶🇦 Qatar (+974)</option>
                                            </select>
                                            @error('corporate_country_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label" for="corporate_mobile_number">Mobile Number *</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-phone-fill"></i></span>
                                                <input type="tel" 
                                                       class="form-control corporate-required @error('corporate_mobile_number') is-invalid @enderror" 
                                                       id="corporate_mobile_number" 
                                                       name="corporate_mobile_number" 
                                                       value="{{ old('corporate_mobile_number') }}"
                                                       placeholder="39123456">
                                                @error('corporate_mobile_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Visit Recording Section -->
                            <div class="mb-4">
                                <h5 class="section-title">
                                    <i class="bi bi-receipt-fill me-2"></i>Record This Visit
                                </h5>
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="record_visit" name="record_visit" value="1" checked>
                                            <label class="form-check-label fw-semibold" for="record_visit">
                                                Record this as a visit
                                            </label>
                                        </div>
                                        <p class="text-muted small mb-3">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Record this registration as a visit to earn loyalty points. You can add bill amount later if needed.
                                        </p>
                                        
                                        <div id="visitDetails" class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="bill_amount">Bill Amount</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" 
                                                           class="form-control @error('bill_amount') is-invalid @enderror" 
                                                           id="bill_amount" 
                                                           name="bill_amount" 
                                                           value="{{ old('bill_amount', 0) }}"
                                                           step="0.01" 
                                                           min="0"
                                                           placeholder="0.00">
                                                    @error('bill_amount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="visit_type">Visit Type</label>
                                                <select class="form-select" id="visit_type" name="visit_type">
                                                    <option value="dine_in" {{ old('visit_type') == 'dine_in' ? 'selected' : '' }}>Dine In</option>
                                                    <option value="takeaway" {{ old('visit_type') == 'takeaway' ? 'selected' : '' }}>Takeaway</option>
                                                    <option value="delivery" {{ old('visit_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                                                    <option value="other" {{ old('visit_type') == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions Section -->
                            <div class="mb-4">
                                <h5 class="section-title">
                                    <i class="bi bi-file-text-fill me-2"></i>Terms & Conditions
                                </h5>
                                <div class="terms-checkbox">
                                    <input type="checkbox" 
                                           class="form-check-input @error('terms_accepted') is-invalid @enderror" 
                                           id="terms_accepted" 
                                           name="terms_accepted" 
                                           value="1"
                                           {{ old('terms_accepted') ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label" for="terms_accepted">
                                        I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a> 
                                        and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a> 
                                        of {{ $outlet->name }}. I understand that my personal information will be processed 
                                        in accordance with the applicable data protection regulations.
                                    </label>
                                </div>
                                @error('terms_accepted')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary-custom text-white btn-lg">
                                    <i class="bi bi-person-plus-fill me-2"></i>Complete Registration
                                </button>
                            </div>

                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check me-1"></i>Your information is secure and will never be shared.
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms & Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>By registering with {{ $outlet->name }}, you agree to the following terms:</p>
                    <ol>
                        <li>You provide accurate and complete information during registration.</li>
                        <li>You are responsible for maintaining the confidentiality of your account.</li>
                        <li>You agree to receive promotional communications from {{ $outlet->name }}.</li>
                        <li>Your loyalty points are subject to the outlet's loyalty program rules.</li>
                        <li>Points have no cash value and cannot be transferred.</li>
                        <li>{{ $outlet->name }} reserves the right to modify or terminate the loyalty program at any time.</li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Privacy Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Your privacy is important to us. This policy explains how we collect, use, and protect your information:</p>
                    <ol>
                        <li>We collect personal information you provide during registration.</li>
                        <li>Your information is used to provide and improve our services.</li>
                        <li>We do not sell your personal information to third parties.</li>
                        <li>We implement appropriate security measures to protect your data.</li>
                        <li>You may request to access, correct, or delete your personal information at any time.</li>
                        <li>We retain your data as long as your account is active or as needed to provide services.</li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Registration type card selection
            const typeCards = document.querySelectorAll('.registration-type-card');
            const typeInput = document.querySelector('input[name="type"]');
            const individualFields = document.getElementById('individualFields');
            const corporateFields = document.getElementById('corporateFields');

            typeCards.forEach(card => {
                card.addEventListener('click', function() {
                    const type = this.dataset.type;
                    
                    // Update active state
                    typeCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Update hidden input
                    typeInput.value = type;
                    
                    // Show/hide fields
                    individualFields.style.display = type === 'individual' ? 'block' : 'none';
                    corporateFields.style.display = type === 'corporate' ? 'block' : 'none';
                    
                    // Update required fields
                    toggleRequiredFields('individual', type === 'individual');
                    toggleRequiredFields('corporate', type === 'corporate');
                });
            });

            function toggleRequiredFields(prefix, isRequired) {
                if (prefix === 'corporate') {
                    const fields = document.querySelectorAll('.corporate-required');
                    fields.forEach(field => {
                        if (isRequired) {
                            field.setAttribute('required', 'required');
                        } else {
                            field.removeAttribute('required');
                        }
                    });
                }
            }

            // Initialize field states on page load (corporate fields should not be required initially)
            const currentType = typeInput.value;
            if (currentType === 'corporate') {
                toggleRequiredFields('corporate', true);
            }
        });
    </script>
</body>
</html>

