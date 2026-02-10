@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="settings-page">
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" style="color: #000000 !important;">
                <i class="bi bi-gear me-2"></i>General
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="customers-tab" data-bs-toggle="tab" data-bs-target="#customers" type="button" style="color: #000000 !important;">
                <i class="bi bi-people me-2"></i>Customers
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="loyalty-tab" data-bs-toggle="tab" data-bs-target="#loyalty" type="button" style="color: #000000 !important;">
                <i class="bi bi-gift me-2"></i>Loyalty
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="communications-tab" data-bs-toggle="tab" data-bs-target="#communications" type="button" style="color: #000000 !important;">
                <i class="bi bi-chat-dots me-2"></i>Communications
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" style="color: #000000 !important;">
                <i class="bi bi-shield-lock me-2"></i>Security
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit" type="button" style="color: #000000 !important;">
                <i class="bi bi-clipboard-data me-2"></i>Audit
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="backup-tab" data-bs-toggle="tab" data-bs-target="#backup" type="button" style="color: #000000 !important;">
                <i class="bi bi-cloud-arrow-down me-2"></i>Backup
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="admin-access-tab" data-bs-toggle="tab" data-bs-target="#admin-access" type="button" style="color: #000000 !important;">
                <i class="bi bi-shield-check me-2"></i>Admin Access
            </button>
        </li>
    </ul>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Tab Content -->
        <div class="tab-content" id="settingsTabsContent">

            <!-- General Settings -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <!-- Application Settings -->
                <h6 class="mb-3 text-primary"><i class="bi bi-app me-2"></i>Application Settings</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="app_name" class="form-label">Application Name</label>
                                <input type="text" class="form-control" id="app_name" name="app_name" value="{{ setting('app_name', 'Hospitality CRM') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="app_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="app_email" name="app_email" value="{{ setting('app_email') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="app_logo" class="form-label">Application Logo</label>
                                <input type="file" class="form-control" id="app_logo" name="app_logo" accept="image/*">
                                @php
                                    $appLogoFile = setting('app_logo');
                                    $appLogoExists = $appLogoFile && file_exists(storage_path('app/public/settings/' . $appLogoFile));
                                @endphp
                                @if($appLogoExists)
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset('storage/settings/' . $appLogoFile) }}" alt="App Logo" style="max-height: 50px;">
                                        <form action="{{ route('admin.settings.delete-file') }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="key" value="app_logo">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete the app logo?')" title="Delete Logo">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="app_favicon" class="form-label">Application Favicon</label>
                                <input type="file" class="form-control" id="app_favicon" name="app_favicon" accept="image/png,image/ico">
                                @php
                                    $appFaviconFile = setting('app_favicon');
                                    $appFaviconExists = $appFaviconFile && file_exists(storage_path('app/public/settings/' . $appFaviconFile));
                                @endphp
                                @if($appFaviconExists)
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset('storage/settings/' . $appFaviconFile) }}" alt="Favicon" style="max-height: 32px;">
                                        <form action="{{ route('admin.settings.delete-file') }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="key" value="app_favicon">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete the favicon?')" title="Delete Favicon">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Information -->
                <h6 class="mb-3 text-primary"><i class="bi bi-building me-2"></i>Company Information</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ setting('company_name') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="company_website" class="form-label">Company Website</label>
                                <input type="url" class="form-control" id="company_website" name="company_website" value="{{ setting('company_website') }}" placeholder="https://example.com">
                            </div>
                            <div class="col-md-6">
                                <label for="company_phone" class="form-label">Company Phone</label>
                                <input type="text" class="form-control" id="company_phone" name="company_phone" value="{{ setting('company_phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="company_vat_number" class="form-label">VAT / Tax Number</label>
                                <input type="text" class="form-control" id="company_vat_number" name="company_vat_number" value="{{ setting('company_vat_number') }}">
                            </div>
                            <div class="col-12">
                                <label for="company_address" class="form-label">Company Address</label>
                                <textarea class="form-control" id="company_address" name="company_address" rows="3">{{ setting('company_address') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="company_logo" class="form-label">Company Logo</label>
                                <input type="file" class="form-control" id="company_logo" name="company_logo" accept="image/*">
                                @php
                                    $companyLogoFile = setting('company_logo');
                                    $companyLogoExists = $companyLogoFile && file_exists(storage_path('app/public/settings/' . $companyLogoFile));
                                @endphp
                                @if($companyLogoExists)
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset('storage/settings/' . $companyLogoFile) }}" alt="Company Logo" style="max-height: 80px;">
                                        <form action="{{ route('admin.settings.delete-file') }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="key" value="company_logo">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete the company logo?')" title="Delete Company Logo">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Localization Settings -->
                <h6 class="mb-3 text-primary"><i class="bi bi-globe me-2"></i>Localization Settings</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="timezone" class="form-label">Timezone</label>
                                <select class="form-select" id="timezone" name="timezone">
                                    <option value="UTC" {{ setting('timezone') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ setting('timezone') === 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                    <option value="Europe/London" {{ setting('timezone') === 'Europe/London' ? 'selected' : '' }}>London</option>
                                    <option value="Asia/Dubai" {{ setting('timezone') === 'Asia/Dubai' ? 'selected' : '' }}>Dubai</option>
                                    <option value="Asia/Bahrain" {{ setting('timezone') === 'Asia/Bahrain' ? 'selected' : '' }}>Bahrain</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="date_format" class="form-label">Date Format</label>
                                <select class="form-select" id="date_format" name="date_format">
                                    <option value="Y-m-d" {{ setting('date_format') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    <option value="m/d/Y" {{ setting('date_format') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                    <option value="d/m/Y" {{ setting('date_format') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="currency" class="form-label">Currency</label>
                                <select class="form-select" id="currency" name="currency">
                                    <option value="USD" {{ setting('currency') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ setting('currency') === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="GBP" {{ setting('currency') === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                    <option value="AED" {{ setting('currency') === 'AED' ? 'selected' : '' }}>AED (د.إ)</option>
                                    <option value="BHD" {{ setting('currency') === 'BHD' ? 'selected' : '' }}>BHD (.د.ب)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="default_language" class="form-label">Default Language</label>
                                <select class="form-select" id="default_language" name="default_language">
                                    <option value="en" {{ setting('default_language') === 'en' ? 'selected' : '' }}>English</option>
                                    <option value="ar" {{ setting('default_language') === 'ar' ? 'selected' : '' }}>Arabic</option>
                                    <option value="zh" {{ setting('default_language') === 'zh' ? 'selected' : '' }}>Chinese</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="time_format" class="form-label">Time Format</label>
                                <select class="form-select" id="time_format" name="time_format">
                                    <option value="12h" {{ setting('time_format', '12h') === '12h' ? 'selected' : '' }}>12-hour (AM/PM)</option>
                                    <option value="24h" {{ setting('time_format') === '24h' ? 'selected' : '' }}>24-hour</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="number_format" class="form-label">Number Format</label>
                                <select class="form-select" id="number_format" name="number_format">
                                    <option value="dot" {{ setting('number_format', 'dot') === 'dot' ? 'selected' : '' }}>Dot (1,000.00)</option>
                                    <option value="comma" {{ setting('number_format') === 'comma' ? 'selected' : '' }}>Comma (1.000,00)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="default_country" class="form-label">Default Country</label>
                                <select class="form-select" id="default_country" name="default_country">
                                    <option value="US" {{ setting('default_country') === 'US' ? 'selected' : '' }}>United States</option>
                                    <option value="GB" {{ setting('default_country') === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                    <option value="BH" {{ setting('default_country') === 'BH' ? 'selected' : '' }}>Bahrain</option>
                                    <option value="AE" {{ setting('default_country') === 'AE' ? 'selected' : '' }}>UAE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Loyalty Program Settings -->
            <div class="tab-pane fade" id="loyalty" role="tabpanel">
                <!-- Loyalty Program Configuration -->
                <h6 class="mb-3 text-primary"><i class="bi bi-gift me-2"></i>Loyalty Program Configuration</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="loyalty_program_enabled" name="loyalty_program_enabled" value="true" {{ setting('loyalty_program_enabled') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="loyalty_program_enabled">Enable Loyalty Program</label>
                                </div>
                                <small class="text-muted">Enable or disable the loyalty rewards program for customers</small>
                            </div>
                            <div class="col-md-4">
                                <label for="points_per_currency" class="form-label">Points Per Currency Unit</label>
                                <input type="number" class="form-control" id="points_per_currency" name="points_per_currency" value="{{ setting('points_per_currency', 10) }}" min="0" step="1">
                                <small class="text-muted">Number of points earned per currency unit spent</small>
                            </div>
                            <div class="col-md-4">
                                <label for="welcome_points" class="form-label">Welcome Points</label>
                                <input type="number" class="form-control" id="welcome_points" name="welcome_points" value="{{ setting('welcome_points', 100) }}" min="0">
                                <small class="text-muted">Points awarded to new customers on registration</small>
                            </div>
                            <div class="col-md-4">
                                <label for="min_points_redemption" class="form-label">Minimum Points for Redemption</label>
                                <input type="number" class="form-control" id="min_points_redemption" name="min_points_redemption" value="{{ setting('min_points_redemption', 500) }}" min="0">
                                <small class="text-muted">Minimum points required to redeem rewards</small>
                            </div>
                            <div class="col-md-4">
                                <label for="points_expiration_months" class="form-label">Points Expiration (Months)</label>
                                <input type="number" class="form-control" id="points_expiration_months" name="points_expiration_months" value="{{ setting('points_expiration_months', 12) }}" min="0" max="120">
                                <small class="text-muted">Months until unused points expire (0 = never)</small>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="enable_tiered_membership" name="enable_tiered_membership" value="true" {{ setting('enable_tiered_membership') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_tiered_membership">Enable Tiered Membership</label>
                                </div>
                                <small class="text-muted">Allow customers to progress through membership tiers</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Settings -->
            <div class="tab-pane fade" id="customers" role="tabpanel">
                <!-- Customer Management Settings -->
                <h6 class="mb-3 text-primary"><i class="bi bi-person-gear me-2"></i>Customer Management Settings</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_assign_customer_to_outlet" name="auto_assign_customer_to_outlet" value="true" {{ setting('auto_assign_customer_to_outlet') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_assign_customer_to_outlet">Auto-assign to Outlet</label>
                                </div>
                                <small class="text-muted">Automatically assign customers to the current outlet</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="allow_duplicate_phones" name="allow_duplicate_phones" value="true" {{ setting('allow_duplicate_phones') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_duplicate_phones">Allow Duplicate Phone Numbers</label>
                                </div>
                                <small class="text-muted">Allow multiple customers with the same phone number</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="require_phone_verification" name="require_phone_verification" value="true" {{ setting('require_phone_verification') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="require_phone_verification">Require Phone Verification</label>
                                </div>
                                <small class="text-muted">Require customers to verify their phone number</small>
                            </div>
                            <div class="col-md-4">
                                <label for="birthday_reminder_days" class="form-label">Birthday Reminder (Days Before)</label>
                                <input type="number" class="form-control" id="birthday_reminder_days" name="birthday_reminder_days" value="{{ setting('birthday_reminder_days', 7) }}" min="0" max="30">
                                <small class="text-muted">Days before birthday to send reminder</small>
                            </div>
                            <div class="col-md-4">
                                <label for="default_customer_status" class="form-label">Default Customer Status</label>
                                <select class="form-select" id="default_customer_status" name="default_customer_status">
                                    <option value="active" {{ setting('default_customer_status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ setting('default_customer_status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="pending" {{ setting('default_customer_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                                <small class="text-muted">Default status for new customers</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature Toggles -->
                <h6 class="mb-3 text-primary"><i class="bi bi-toggle-on me-2"></i>Feature Toggles</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="qr_code_enabled" name="qr_code_enabled" value="true" {{ setting('qr_code_enabled') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="qr_code_enabled">QR Code Generation</label>
                                </div>
                                <small class="text-muted">Enable customer QR code generation for quick check-in</small>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_greetings_enabled" name="auto_greetings_enabled" value="true" {{ setting('auto_greetings_enabled') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_greetings_enabled">Auto Greetings</label>
                                </div>
                                <small class="text-muted">Send automatic greeting messages to customers</small>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="customer_ratings_enabled" name="customer_ratings_enabled" value="true" {{ setting('customer_ratings_enabled') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="customer_ratings_enabled">Customer Ratings</label>
                                </div>
                                <small class="text-muted">Allow customers to rate their experience</small>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="import_export_enabled" name="import_export_enabled" value="true" {{ setting('import_export_enabled') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="import_export_enabled">Import/Export</label>
                                </div>
                                <small class="text-muted">Enable bulk import and export of data</small>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="multi_outlet_mode" name="multi_outlet_mode" value="true" {{ setting('multi_outlet_mode') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="multi_outlet_mode">Multi-Outlet Mode</label>
                                </div>
                                <small class="text-muted">Enable management of multiple outlets</small>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="guest_checkin_enabled" name="guest_checkin_enabled" value="true" {{ setting('guest_checkin_enabled') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="guest_checkin_enabled">Guest Check-in</label>
                                </div>
                                <small class="text-muted">Allow check-in without customer registration</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





            <!-- Communications Settings -->
            <div class="tab-pane fade" id="communications" role="tabpanel">
                <!-- SMS Provider Settings -->
                <h6 class="mb-3 text-primary"><i class="bi bi-chat-dots me-2"></i>SMS Provider Settings</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="sms_provider" class="form-label">SMS Provider</label>
                                <select class="form-select" id="sms_provider" name="sms_provider">
                                    <option value="default" {{ setting('sms_provider') === 'default' ? 'selected' : '' }}>Default (Log Only)</option>
                                    <option value="twilio" {{ setting('sms_provider') === 'twilio' ? 'selected' : '' }}>Twilio</option>
                                    <option value="nexmo" {{ setting('sms_provider') === 'nexmo' ? 'selected' : '' }}>Nexmo (Vonage)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="sms_api_key" class="form-label">API Key</label>
                                <input type="text" class="form-control" id="sms_api_key" name="sms_api_key" value="{{ setting('sms_api_key') }}" placeholder="Enter API Key">
                            </div>
                            <div class="col-md-4">
                                <label for="sms_api_url" class="form-label">API URL</label>
                                <input type="url" class="form-control" id="sms_api_url" name="sms_api_url" value="{{ setting('sms_api_url') }}" placeholder="https://api.example.com/sms">
                            </div>
                            <div class="col-md-4">
                                <label for="sms_sender_id" class="form-label">Sender ID</label>
                                <input type="text" class="form-control" id="sms_sender_id" name="sms_sender_id" value="{{ setting('sms_sender_id') }}" placeholder="HOSPITALITY">
                            </div>
                            <div class="col-md-4">
                                <label for="sms_username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="sms_username" name="sms_username" value="{{ setting('sms_username') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="sms_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="sms_password" name="sms_password" value="{{ setting('sms_password') }}" placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Provider Settings -->
                <h6 class="mb-3 text-primary"><i class="bi bi-envelope me-2"></i>Email Provider Settings (SMTP)</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="email_provider" class="form-label">Email Provider</label>
                                <select class="form-select" id="email_provider" name="email_provider">
                                    <option value="smtp" {{ setting('email_provider') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="sendgrid" {{ setting('email_provider') === 'sendgrid' ? 'selected' : '' }}>SendGrid</option>
                                    <option value="mailgun" {{ setting('email_provider') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="smtp_host" class="form-label">SMTP Host</label>
                                <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="{{ setting('smtp_host') }}" placeholder="smtp.example.com">
                            </div>
                            <div class="col-md-4">
                                <label for="smtp_port" class="form-label">SMTP Port</label>
                                <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="{{ setting('smtp_port', 587) }}" placeholder="587">
                            </div>
                            <div class="col-md-4">
                                <label for="smtp_username" class="form-label">SMTP Username</label>
                                <input type="text" class="form-control" id="smtp_username" name="smtp_username" value="{{ setting('smtp_username') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="smtp_password" class="form-label">SMTP Password</label>
                                <input type="password" class="form-control" id="smtp_password" name="smtp_password" value="{{ setting('smtp_password') }}" placeholder="••••••••">
                            </div>
                            <div class="col-md-4">
                                <label for="smtp_encryption" class="form-label">Encryption</label>
                                <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                    <option value="tls" {{ setting('smtp_encryption') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ setting('smtp_encryption') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="none" {{ setting('smtp_encryption') === 'none' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="email_from_address" class="form-label">From Email Address</label>
                                <input type="email" class="form-control" id="email_from_address" name="email_from_address" value="{{ setting('email_from_address') }}" placeholder="noreply@yourdomain.com">
                            </div>
                            <div class="col-md-6">
                                <label for="email_from_name" class="form-label">From Email Name</label>
                                <input type="text" class="form-control" id="email_from_name" name="email_from_name" value="{{ setting('email_from_name') }}" placeholder="Hospitality CRM">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notification Preferences -->
                <h6 class="mb-3 text-primary"><i class="bi bi-bell-settings me-2"></i>Notification Preferences</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="send_sms_on_visit" name="send_sms_on_visit" value="true" {{ setting('send_sms_on_visit') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_sms_on_visit">Send SMS on Visit Confirmation</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="send_email_on_visit" name="send_email_on_visit" value="true" {{ setting('send_email_on_visit') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_email_on_visit">Send Email on Visit Confirmation</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Welcome Message Templates -->
                <h6 class="mb-3 text-primary"><i class="bi bi-person-plus me-2"></i>Welcome Message Templates</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="welcome_sms_template" class="form-label">Welcome SMS Template</label>
                                <textarea class="form-control" id="welcome_sms_template" name="welcome_sms_template" rows="4" placeholder="Welcome {customer_name}! Thank you for joining us.">{{ setting('welcome_sms_template') }}</textarea>
                                <small class="text-muted">Variables: {customer_name}, {points}, {outlet_name}</small>
                            </div>
                            <div class="col-md-6">
                                <label for="welcome_email_template" class="form-label">Welcome Email Template</label>
                                <textarea class="form-control" id="welcome_email_template" name="welcome_email_template" rows="4" placeholder="Welcome {customer_name}! Thank you for registering...">{{ setting('welcome_email_template') }}</textarea>
                                <small class="text-muted">Variables: {customer_name}, {points}, {outlet_name}, {loyalty_tier}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Birthday Message Templates -->
                <h6 class="mb-3 text-primary"><i class="bi bi-cake me-2"></i>Birthday Message Templates</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="birthday_sms_template" class="form-label">Birthday SMS Template</label>
                                <textarea class="form-control" id="birthday_sms_template" name="birthday_sms_template" rows="4" placeholder="Happy Birthday {customer_name}! Enjoy {bonus_points} bonus points on us! 🎂">{{ setting('birthday_sms_template') }}</textarea>
                                <small class="text-muted">Variables: {customer_name}, {bonus_points}, {outlet_name}, {birthday_reward}</small>
                            </div>
                            <div class="col-md-6">
                                <label for="birthday_email_template" class="form-label">Birthday Email Template</label>
                                <textarea class="form-control" id="birthday_email_template" name="birthday_email_template" rows="4" placeholder="Happy Birthday {customer_name}! Here's your special birthday reward...">{{ setting('birthday_email_template') }}</textarea>
                                <small class="text-muted">Variables: {customer_name}, {bonus_points}, {outlet_name}, {birthday_reward}, {coupon_code}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visit Confirmation Templates -->
                <h6 class="mb-3 text-primary"><i class="bi bi-calendar-check me-2"></i>Visit Confirmation Templates</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="visit_confirmation_sms_template" class="form-label">Visit Confirmation SMS Template</label>
                                <textarea class="form-control" id="visit_confirmation_sms_template" name="visit_confirmation_sms_template" rows="4" placeholder="Thank you for visiting {outlet_name}, {customer_name}! You earned {points_earned} points.">{{ setting('visit_confirmation_sms_template') }}</textarea>
                                <small class="text-muted">Variables: {customer_name}, {points_earned}, {total_points}, {outlet_name}, {visit_date}</small>
                            </div>
                            <div class="col-md-6">
                                <label for="visit_confirmation_email_template" class="form-label">Visit Confirmation Email Template</label>
                                <textarea class="form-control" id="visit_confirmation_email_template" name="visit_confirmation_email_template" rows="4" placeholder="Thank you for visiting {outlet_name}...">{{ setting('visit_confirmation_email_template') }}</textarea>
                                <small class="text-muted">Variables: {customer_name}, {points_earned}, {total_points}, {outlet_name}, {visit_date}, {receipt_amount}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loyalty Tier Upgrade Templates -->
                <h6 class="mb-3 text-primary"><i class="bi bi-star me-2"></i>Loyalty Tier Upgrade Templates</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="loyalty_tier_upgrade_sms_template" class="form-label">Tier Upgrade SMS Template</label>
                                <textarea class="form-control" id="loyalty_tier_upgrade_sms_template" name="loyalty_tier_upgrade_sms_template" rows="4" placeholder="Congratulations {customer_name}! You've been upgraded to {new_tier} tier! 🎉">{{ setting('loyalty_tier_upgrade_sms_template') }}</textarea>
                                <small class="text-muted">Variables: {customer_name}, {new_tier}, {previous_tier}, {benefits}, {outlet_name}</small>
                            </div>
                            <div class="col-md-6">
                                <label for="loyalty_tier_upgrade_email_template" class="form-label">Tier Upgrade Email Template</label>
                                <textarea class="form-control" id="loyalty_tier_upgrade_email_template" name="loyalty_tier_upgrade_email_template" rows="4" placeholder="Congratulations {customer_name}! You've reached {new_tier} tier...">{{ setting('loyalty_tier_upgrade_email_template') }}</textarea>
                                <small class="text-muted">Variables: {customer_name}, {new_tier}, {previous_tier}, {benefits}, {outlet_name}, {exclusive_rewards}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Security Settings -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <!-- Security Configuration -->
                <h6 class="mb-3 text-primary"><i class="bi bi-shield-lock me-2"></i>Security Configuration</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="password_min_length" class="form-label">Minimum Password Length</label>
                                <input type="number" class="form-control" id="password_min_length" name="password_min_length" value="{{ setting('password_min_length', 8) }}" min="6" max="32">
                            </div>
                            <div class="col-md-4">
                                <label for="session_timeout_minutes" class="form-label">Session Timeout (minutes)</label>
                                <input type="number" class="form-control" id="session_timeout_minutes" name="session_timeout_minutes" value="{{ setting('session_timeout_minutes', 60) }}" min="5" max="1440">
                            </div>
                            <div class="col-md-4">
                                <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                                <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" value="{{ setting('max_login_attempts', 5) }}" min="1" max="10">
                            </div>
                            <div class="col-md-4">
                                <label for="lockout_duration_minutes" class="form-label">Lockout Duration (minutes)</label>
                                <input type="number" class="form-control" id="lockout_duration_minutes" name="lockout_duration_minutes" value="{{ setting('lockout_duration_minutes', 15) }}" min="1" max="60">
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="enforce_strong_password" name="enforce_strong_password" value="true" {{ setting('enforce_strong_password') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enforce_strong_password">Enforce Strong Password</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="enable_2fa" name="enable_2fa" value="true" {{ setting('enable_2fa') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_2fa">Enable Two-Factor Authentication</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<!-- Audit & Logging Settings -->
            <div class="tab-pane fade" id="audit" role="tabpanel">
                <!-- Audit & Logging Configuration -->
                <h6 class="mb-3 text-primary"><i class="bi bi-clipboard-data me-2"></i>Audit & Logging Configuration</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="audit_logging_enabled" name="audit_logging_enabled" value="true" {{ setting('audit_logging_enabled') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="audit_logging_enabled">Enable Audit Logging</label>
                                </div>
                                <small class="text-muted">Track all user actions and system events</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="log_sensitive_data" name="log_sensitive_data" value="true" {{ setting('log_sensitive_data') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="log_sensitive_data">Log Sensitive Data</label>
                                </div>
                                <small class="text-muted">Include sensitive data in audit logs (passwords, tokens)</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="log_api_calls" name="log_api_calls" value="true" {{ setting('log_api_calls') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="log_api_calls">Log API Calls</label>
                                </div>
                                <small class="text-muted">Record all API requests and responses</small>
                            </div>
                            <div class="col-md-4">
                                <label for="log_retention_days" class="form-label">Log Retention (Days)</label>
                                <input type="number" class="form-control" id="log_retention_days" name="log_retention_days" value="{{ setting('log_retention_days', 90) }}" min="1" max="365">
                                <small class="text-muted">Number of days to keep audit logs</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Access Settings -->
            <div class="tab-pane fade" id="admin-access" role="tabpanel">
                <!-- Admin Section Access Configuration -->
                <h6 class="mb-3 text-primary"><i class="bi bi-shield-check me-2"></i>Admin Section Access Configuration</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 mb-3">
                                <p class="text-muted">
                                    Configure which roles can access the Administration section in the sidebar.
                                    Users with these roles will see the Admin menu and be able to manage users, roles, settings, and audit logs.
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Roles Allowed to Access Admin Section</label>
                                <select class="form-select" name="admin_section_roles[]" multiple size="6">
                                    @php
                                        $allRoles = \Spatie\Permission\Models\Role::orderBy('name')->get();
                                        $selectedRoles = json_decode(setting('admin_section_roles', '["super_admin","group_manager"]'), true) ?? ['super_admin', 'group_manager'];
                                    @endphp
                                    @foreach($allRoles as $role)
                                    <option value="{{ $role->name }}" {{ in_array($role->name, $selectedRoles) ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple roles. Super Admin role always has access.</small>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Current Setting:</strong>
                                    <ul class="mb-0 mt-2">
                                        @forelse($selectedRoles as $role)
                                        <li>{{ ucfirst(str_replace('_', ' ', $role)) }}</li>
                                        @empty
                                        <li>No roles configured (only super_admin will have access)</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Descriptions -->
                <h6 class="mb-3 text-primary"><i class="bi bi-people me-2"></i>Role Descriptions</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Role</th>
                                        <th>Permissions</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $roleDescriptions = [
                                            'super_admin' => 'Full system access - can access everything including backups',
                                            'group_manager' => 'Can manage users, roles, settings, and view audit logs',
                                            'admin' => 'Can manage users and view settings',
                                            'manager' => 'Can view users and audit logs',
                                        ];
                                    @endphp
                                    @foreach($allRoles->take(8) as $role)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                        </td>
                                        <td>
                                            <small>{{ $role->permissions()->count() }} permissions</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $roleDescriptions[$role->name] ?? 'Custom role with assigned permissions' }}</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <h6 class="mb-3 text-primary"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-people me-2"></i>Manage Users
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('admin.users.roles.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-shield-check me-2"></i>Manage Roles
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-journal-text me-2"></i>View Audit Logs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Backup Settings -->
            <div class="tab-pane fade" id="backup" role="tabpanel">
                <!-- Backup Configuration -->
                <h6 class="mb-3 text-primary"><i class="bi bi-cloud-arrow-down me-2"></i>Backup Configuration</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_backup_enabled" name="auto_backup_enabled" value="true" {{ setting('auto_backup_enabled') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_backup_enabled">Enable Automatic Backups</label>
                                </div>
                                <small class="text-muted">Automatically create scheduled backups</small>
                            </div>
                            <div class="col-md-6">
                                <label for="backup_frequency" class="form-label">Backup Frequency</label>
                                <select class="form-select" id="backup_frequency" name="backup_frequency">
                                    <option value="daily" {{ setting('backup_frequency', 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ setting('backup_frequency') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ setting('backup_frequency') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="backup_time" class="form-label">Backup Time</label>
                                <input type="time" class="form-control" id="backup_time" name="backup_time" value="{{ setting('backup_time', '02:00') }}">
                                <small class="text-muted">Time to run automatic backups (server time)</small>
                            </div>
                            <div class="col-md-6">
                                <label for="backup_retention" class="form-label">Backup Retention (Days)</label>
                                <input type="number" class="form-control" id="backup_retention" name="backup_retention" value="{{ setting('backup_retention', 30) }}" min="1" max="365">
                                <small class="text-muted">Number of days to keep backup files</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manual Backup -->
                <h6 class="mb-3 text-primary"><i class="bi bi-database me-2"></i>Manual Backup</h6>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Database Backup</label>
                                <p class="text-muted small mb-2">Create a full database backup including all tables and data.</p>
                                <a href="{{ route('admin.backup.create', ['type' => 'database']) }}" class="btn btn-primary">
                                    <i class="bi bi-database me-2"></i>Create Database Backup
                                </a>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Files Backup</label>
                                <p class="text-muted small mb-2">Create a backup of uploaded files, storage, and configuration.</p>
                                <a href="{{ route('admin.backup.create', ['type' => 'files']) }}" class="btn btn-primary">
                                    <i class="bi bi-folder me-2"></i>Create Files Backup
                                </a>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Full Backup</label>
                                <p class="text-muted small mb-2">Create a complete backup including database, files, and settings.</p>
                                <a href="{{ route('admin.backup.create', ['type' => 'full']) }}" class="btn btn-success">
                                    <i class="bi bi-cloud-download me-2"></i>Create Full Backup
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Save Button -->
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-check-lg me-2"></i>Save All Settings
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.settings-page .nav-tabs { border-bottom: 2px solid #e9ecef; }
.settings-page .nav-tabs .nav-link { 
    border: none; 
    color: #000000 !important; 
    font-weight: 500; 
    padding: 0.75rem 1.25rem; 
    border-radius: 0; 
    margin-bottom: -2px; 
}
.settings-page .nav-tabs .nav-link:hover { color: #6366F1 !important; background-color: #f8f9fa; }
.settings-page .nav-tabs .nav-link.active { color: #6366F1 !important; background-color: transparent; border-bottom: 2px solid #6366F1; }
.settings-page .card { border: 1px solid #e9ecef; box-shadow: none; }
.settings-page .card-header { background-color: #f8f9fa; border-bottom: 1px solid #e9ecef; }
.settings-page .form-label { font-weight: 500; color: #495057; }
.settings-page h6 { font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem; }
.form-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
.form-switch .form-check-input:checked { background-color: #6366F1; border-color: #6366F1; }
</style>
@endpush

<!-- IMMEDIATE TAB VISIBILITY FIX - Applies before any scripts load -->
<style>
/* Force immediate visibility of all tab content */
#settingsTabsContent .tab-pane {
    opacity: 1 !important;
    visibility: visible !important;
    display: block !important;
    transform: none !important;
    transition: none !important;
}

/* Only hide inactive panes after JavaScript initializes */
#settingsTabsContent .tab-pane:not(.active):not(.show) {
    display: none !important;
}

/* Hardware acceleration */
#settingsTabsContent .tab-pane.active,
#settingsTabsContent .tab-pane.show {
    transform: translateZ(0);
    will-change: transform;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==========================================================================
    // ULTRA-FAST TAB FIX - Runs immediately, before Bootstrap
    // ==========================================================================
    function fixTabsNow() {
        var panes = document.querySelectorAll('#settingsTabsContent .tab-pane');
        panes.forEach(function(pane) {
            // Force inline styles to override Bootstrap's fade
            pane.style.opacity = '1';
            pane.style.visibility = 'visible';
            pane.style.display = pane.classList.contains('active') || pane.classList.contains('show') ? 'block' : 'none';
            pane.style.transform = 'translateZ(0)';
            pane.style.willChange = 'transform';
        });
    }
    
    // Run immediately
    fixTabsNow();
    
    // Run again after any slight delay to catch Bootstrap initialization
    setTimeout(fixTabsNow, 0);
    setTimeout(fixTabsNow, 50);
    setTimeout(fixTabsNow, 100);
    
    // ==========================================================================
    // BOOTSTRAP TAB INITIALIZATION
    // ==========================================================================
    function initTabs() {
        if (typeof bootstrap === 'undefined') {
            // Bootstrap not loaded yet, retry
            setTimeout(initTabs, 100);
            return;
        }
        
        // Get all trigger elements
        var triggerTabList = [].slice.call(document.querySelectorAll('#settingsTabs .nav-link'));
        triggerTabList.forEach(function(triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl);
            
            triggerEl.addEventListener('click', function(e) {
                e.preventDefault();
                tabTrigger.show();
            });
        });
        
        // Handle URL hash for direct tab access
        if (window.location.hash) {
            var hash = window.location.hash;
            var tabEl = document.querySelector('#settingsTabs button[data-bs-target="' + hash + '"]');
            if (tabEl) {
                var tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        }
        
        // Update URL hash when tabs change
        var tabElms = document.querySelectorAll('#settingsTabs .nav-link');
        tabElms.forEach(function(triggerEl) {
            triggerEl.addEventListener('shown.bs.tab', function(e) {
                var targetId = e.target.getAttribute('data-bs-target');
                if (targetId) {
                    history.replaceState(null, null, targetId);
                }
            });
        });
        
        // Hardware acceleration for tab transitions
        function enableHardwareAcceleration() {
            var activePane = document.querySelector('#settingsTabsContent .tab-pane.active');
            if (activePane) {
                activePane.style.transform = 'translateZ(0)';
                activePane.style.backfaceVisibility = 'hidden';
            }
        }
        
        enableHardwareAcceleration();
        tabElms.forEach(function(triggerEl) {
            triggerEl.addEventListener('shown.bs.tab', enableHardwareAcceleration);
        });
    }
    
    initTabs();
});
</script>
