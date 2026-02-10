@extends('layouts.app')

@section('title', 'Edit Campaign')
@section('page-title', 'Edit: ' . $campaign->name)

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaigns</a></li>
                <li class="breadcrumb-item"><a href="{{ route('campaigns.show', $campaign) }}">{{ $campaign->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<form action="{{ route('campaigns.update', $campaign) }}" method="POST">
    @csrf
    @method('PUT')

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
                               id="name" name="name" value="{{ old('name', $campaign->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Internal name for this campaign (not visible to recipients)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Campaign Type *</label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required {{ $campaign->isSent() ? 'disabled' : '' }}>
                                <option value="">Select type...</option>
                                <option value="email" {{ old('type', $campaign->type) === 'email' ? 'selected' : '' }}>Email</option>
                                <option value="sms" {{ old('type', $campaign->type) === 'sms' ? 'selected' : '' }}>SMS</option>
                                <option value="push" {{ old('type', $campaign->type) === 'push' ? 'selected' : '' }}>Push Notification</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($campaign->isSent())
                            <small class="text-warning">Cannot change type after sending</small>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @endif" 
                                    id="status" name="status" {{ $campaign->isSent() ? 'disabled' : '' }}>
                                <option value="draft" {{ old('status', $campaign->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="ready" {{ old('status', $campaign->status) === 'ready' ? 'selected' : '' }}>Ready to Send</option>
                                <option value="cancelled" {{ old('status', $campaign->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($campaign->isSent())
                            <small class="text-warning">Cannot change status after sending</small>
                            @endif
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
                        <input type="text" class="form-control @error('subject') is-invalid @endif" 
                               id="subject" name="subject" value="{{ old('subject', $campaign->subject) }}" {{ $campaign->isSent() ? 'disabled' : '' }}>
                        @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="preheader" class="form-label">Preheader Text</label>
                        <input type="text" class="form-control @error('preheader') is-invalid @endif" 
                               id="preheader" name="preheader" value="{{ old('preheader', $campaign->preheader) }}" {{ $campaign->isSent() ? 'disabled' : '' }}>
                        @error('preheader')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Preview text shown in email clients (next to subject line)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="from_name" class="form-label">From Name</label>
                            <input type="text" class="form-control @error('from_name') is-invalid @endif" 
                                   id="from_name" name="from_name" value="{{ old('from_name', $campaign->from_name) }}" {{ $campaign->isSent() ? 'disabled' : '' }}>
                            @error('from_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reply_to" class="form-label">Reply-To Email</label>
                            <input type="email" class="form-control @error('reply_to') is-invalid @endif" 
                                   id="reply_to" name="reply_to" value="{{ old('reply_to', $campaign->reply_to) }}" {{ $campaign->isSent() ? 'disabled' : '' }}>
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
                        <textarea class="form-control @error('body') is-invalid @endif" 
                                  id="body" name="body" rows="12" {{ $campaign->isSent() ? 'disabled' : '' }}>{{ old('body', $campaign->body) }}</textarea>
                        @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="body-help">
                            For SMS: Maximum 160 characters per message.
                        </small>
                        @if($campaign->isSent())
                        <small class="text-warning d-block mt-1">Cannot modify content after sending</small>
                        @endif
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
                        <select class="form-select" name="segment_type" id="segment_type" {{ $campaign->isSent() ? 'disabled' : '' }}>
                            <option value="all" {{ old('segment_type', $campaign->segment_type ?? 'all') === 'all' ? 'selected' : '' }}>
                                All Customers
                            </option>
                            <option value="active" {{ old('segment_type', $campaign->segment_type) === 'active' ? 'selected' : '' }}>
                                Active Customers (visited in last 30 days)
                            </option>
                            <option value="inactive" {{ old('segment_type', $campaign->segment_type) === 'inactive' ? 'selected' : '' }}>
                                Inactive Customers (no visit in 60+ days)
                            </option>
                            <option value="new" {{ old('segment_type', $campaign->segment_type) === 'new' ? 'selected' : '' }}>
                                New Customers (joined in last 7 days)
                            </option>
                            <option value="loyal" {{ old('segment_type', $campaign->segment_type) === 'loyal' ? 'selected' : '' }}>
                                Loyal Customers (10+ visits)
                            </option>
                            <option value="vip" {{ old('segment_type', $campaign->segment_type) === 'vip' ? 'selected' : '' }}>
                                VIP Customers
                            </option>
                        </select>
                        <small class="text-muted">
                            Estimated recipients: <span id="estimated-count">{{ number_format($campaign->total_recipients ?? 0) }}</span>
                        </small>
                        @if($campaign->isSent())
                        <small class="text-warning d-block mt-1">Cannot change segment after sending</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Changes
                        </button>
                        @if(!$campaign->isSent())
                        <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        @else
                        <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline-secondary">
                            Back to Campaign
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Campaign Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Campaign Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Status:</small><br>
                        <span class="badge badge-status badge-{{ $campaign->status }}">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Created:</small><br>
                        {{ $campaign->created_at->format('M d, Y H:i') }}
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">By:</small><br>
                        {{ $campaign->creator->name ?? 'N/A' }}
                    </div>
                    @if($campaign->sent_at)
                    <div class="mb-2">
                        <small class="text-muted">Sent:</small><br>
                        {{ $campaign->sent_at->format('M d, Y H:i') }}
                    </div>
                    @endif
                    <div class="mb-2">
                        <small class="text-muted">Recipients:</small><br>
                        {{ number_format($campaign->total_recipients ?? 0) }}
                    </div>
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

