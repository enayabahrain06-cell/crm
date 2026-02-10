@extends('layouts.app')

@section('title', 'Preview: ' . $campaign->name)
@section('page-title', 'Preview Campaign')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaigns</a></li>
                <li class="breadcrumb-item"><a href="{{ route('campaigns.show', $campaign) }}">{{ $campaign->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Preview</li>
            </ol>
        </nav>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Campaign
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Campaign Preview</h5>
                <span class="badge bg-{{ $campaign->type === 'email' ? 'info' : ($campaign->type === 'sms' ? 'warning' : 'success') }}">
                    {{ ucfirst($campaign->type) }}
                </span>
            </div>
            <div class="card-body">
                @if($campaign->type === 'email')
                <!-- Email Preview -->
                <div class="email-preview">
                    <div class="email-header border-bottom pb-3 mb-3">
                        <div class="row mb-2">
                            <div class="col-md-3 text-muted">From:</div>
                            <div class="col-md-9">{{ $campaign->from_name ?: 'Not set' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 text-muted">To:</div>
                            <div class="col-md-9">recipient@example.com</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 text-muted">Subject:</div>
                            <div class="col-md-9 fw-semibold">{{ $campaign->subject ?: 'No subject' }}</div>
                        </div>
                        @if($campaign->preheader)
                        <div class="row">
                            <div class="col-md-3 text-muted">Preview:</div>
                            <div class="col-md-9 text-muted">{{ $campaign->preheader }}</div>
                        </div>
                        @endif
                    </div>
                    <div class="email-body">
                        <div class="p-4 bg-light rounded">
                            {!! $campaign->body ?: '<em>No content</em>' !!}
                        </div>
                    </div>
                </div>
                @elseif($campaign->type === 'sms')
                <!-- SMS Preview -->
                <div class="sms-preview">
                    <div class="sms-device border rounded p-3 mx-auto" style="max-width: 350px;">
                        <div class="sms-header border-bottom pb-2 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle p-2 me-2" style="width: 32px; height: 32px;">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $campaign->from_name ?: 'Sender' }}</div>
                                    <small class="text-muted">Now</small>
                                </div>
                            </div>
                        </div>
                        <div class="sms-body">
                            {{ $campaign->body ?: '<em>No content</em>' }}
                        </div>
                        <div class="sms-footer mt-2 pt-2 border-top">
                            <small class="text-muted">
                                {{ strlen($campaign->body ?? '') }}/160 characters
                            </small>
                        </div>
                    </div>
                </div>
                @elseif($campaign->type === 'push')
                <!-- Push Notification Preview -->
                <div class="push-preview">
                    <div class="push-device border rounded p-3 mx-auto" style="max-width: 350px;">
                        <div class="d-flex align-items-start">
                            <div class="bg-primary text-white rounded-circle p-2 me-3" style="min-width: 40px; text-align: center;">
                                <i class="bi bi-bell"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $campaign->from_name ?: 'App Name' }}</div>
                                <div class="text-muted small">Just now</div>
                                <div class="mt-1">{{ $campaign->body ?: '<em>No content</em>' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Test Send Form -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Send Test</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('campaigns.preview', $campaign) }}" method="GET" class="row g-3">
                    <div class="col-md-8">
                        <label for="test_email" class="form-label">Test Email Address</label>
                        <input type="email" class="form-control" id="test_email" name="email" 
                               value="{{ request('email') }}" placeholder="Enter email address">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-send me-2"></i>Send Test
                        </button>
                    </div>
                </form>
                @if(session('test_sent'))
                <div class="alert alert-success mt-3 mb-0">
                    <i class="bi bi-check-circle me-2"></i>
                    Test email sent successfully to {{ session('test_sent') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

