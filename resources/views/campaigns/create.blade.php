@extends('layouts.app')

@section('title', 'Create Campaign')
@section('page-title', 'Create New Campaign')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaigns</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create</li>
            </ol>
        </nav>
    </div>
</div>

<form action="{{ route('campaigns.store') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-md-8">
            <!-- Basic Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Campaign Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Internal name for this campaign (not visible to recipients)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Campaign Type *</label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="">Select type...</option>
                                <option value="email" {{ old('type') === 'email' ? 'selected' : '' }}>Email</option>
                                <option value="sms" {{ old('type') === 'sms' ? 'selected' : '' }}>SMS</option>
                                <option value="push" {{ old('type') === 'push' ? 'selected' : '' }}>Push Notification</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Initial Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="ready" {{ old('status') === 'ready' ? 'selected' : '' }}>Ready to Send</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Settings (for email campaigns) -->
            <div class="card mb-4" id="email-settings" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">Email Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Email Subject *</label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                               id="subject" name="subject" value="{{ old('subject') }}">
                        @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="preheader" class="form-label">Preheader Text</label>
                        <input type="text" class="form-control @error('preheader') is-invalid @enderror" 
                               id="preheader" name="preheader" value="{{ old('preheader') }}">
                        @error('preheader')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Preview text shown in email clients (next to subject line)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="from_name" class="form-label">From Name</label>
                            <input type="text" class="form-control @error('from_name') is-invalid @enderror" 
                                   id="from_name" name="from_name" value="{{ old('from_name') }}">
                            @error('from_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reply_to" class="form-label">Reply-To Email</label>
                            <input type="email" class="form-control @error('reply_to') is-invalid @enderror" 
                                   id="reply_to" name="reply_to" value="{{ old('reply_to') }}">
                            @error('reply_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Content</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="body" class="form-label">
                            <span id="body-label">Message Body *</span>
                        </label>
                        <textarea class="form-control @error('body') is-invalid @enderror" 
                                  id="body" name="body" rows="12">{{ old('body') }}</textarea>
                        @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="body-help">
                            For SMS: Maximum 160 characters per message.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Segment -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Target Segment</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Customer Segment</label>
                        <select class="form-select" name="segment_type" id="segment_type">
                            <option value="all" {{ old('segment_type', 'all') === 'all' ? 'selected' : '' }}>
                                All Customers
                            </option>
                            <option value="active" {{ old('segment_type') === 'active' ? 'selected' : '' }}>
                                Active Customers (visited in last 30 days)
                            </option>
                            <option value="inactive" {{ old('segment_type') === 'inactive' ? 'selected' : '' }}>
                                Inactive Customers (no visit in 60+ days)
                            </option>
                            <option value="new" {{ old('segment_type') === 'new' ? 'selected' : '' }}>
                                New Customers (joined in last 7 days)
                            </option>
                            <option value="loyal" {{ old('segment_type') === 'loyal' ? 'selected' : '' }}>
                                Loyal Customers (10+ visits)
                            </option>
                            <option value="vip" {{ old('segment_type') === 'vip' ? 'selected' : '' }}>
                                VIP Customers
                            </option>
                        </select>
                        <small class="text-muted">
                            Estimated recipients: <span id="estimated-count">-</span>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" name="status" value="draft">
                            <i class="bi bi-save me-2"></i>Save as Draft
                        </button>
                        <button type="submit" class="btn btn-success" name="status" value="ready">
                            <i class="bi bi-check-circle me-2"></i>Save and Ready
                        </button>
                        <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Use a compelling subject line to improve open rates.</li>
                        <li class="mb-2">Keep SMS messages under 160 characters for best delivery.</li>
                        <li class="mb-2">Segment your audience for more targeted messaging.</li>
                        <li class="mb-2">Always test your campaign before sending.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const emailSettings = document.getElementById('email-settings');
    const bodyLabel = document.getElementById('body-label');
    const bodyHelp = document.getElementById('body-help');
    const body = document.getElementById('body');

    function updateUI() {
        const type = typeSelect.value;
        
        if (type === 'email') {
            emailSettings.style.display = 'block';
            bodyLabel.textContent = 'HTML Email Body *';
            bodyHelp.textContent = 'Use HTML tags for formatting. Keep it mobile-friendly.';
            body.setAttribute('rows', '12');
        } else if (type === 'sms') {
            emailSettings.style.display = 'none';
            bodyLabel.textContent = 'SMS Message Body *';
            bodyHelp.textContent = 'Maximum 160 characters per message. Longer messages will be split.';
            body.setAttribute('rows', '4');
        } else if (type === 'push') {
            emailSettings.style.display = 'none';
            bodyLabel.textContent = 'Push Notification Body *';
            bodyHelp.textContent = 'Keep it short and compelling. Max 200 characters.';
            body.setAttribute('rows', '4');
        } else {
            emailSettings.style.display = 'none';
        }
    }

    typeSelect.addEventListener('change', updateUI);
    updateUI();

    // Character count for SMS
    if (typeSelect.value === 'sms') {
        body.addEventListener('input', function() {
            const len = this.value.length;
            const parts = Math.ceil(len / 160);
            bodyHelp.textContent = `${len}/160 characters (${parts} part${parts > 1 ? 's' : ''})`;
        });
    }
});
</script>
@endpush
@endsection

