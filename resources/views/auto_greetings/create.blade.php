@extends('layouts.app')

@section('title', 'Create Auto Greeting')
@section('page-title', 'Create Auto Greeting Rule')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Rule Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('auto-greetings.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Rule Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="2">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Channel <span class="text-danger">*</span></label>
                            <select class="form-select @error('channel') is-invalid @enderror" name="channel" required>
                                <option value="">Select channel...</option>
                                <option value="email" {{ old('channel') === 'email' ? 'selected' : '' }}>Email</option>
                            </select>
                            @error('channel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trigger Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('trigger_type') is-invalid @enderror" name="trigger_type" id="trigger" required>
                                <option value="">Select trigger...</option>
                                <option value="birthday" {{ old('trigger_type') === 'birthday' ? 'selected' : '' }}>Birthday</option>
                                <option value="fixed_date" {{ old('trigger_type') === 'fixed_date' ? 'selected' : '' }}>Fixed Date</option>
                            </select>
                            @error('trigger_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3" id="trigger_date_container" style="display: none;">
                        <label class="form-label">Trigger Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('trigger_date') is-invalid @enderror" 
                               name="trigger_date" value="{{ old('trigger_date') }}">
                        @error('trigger_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Template Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('template_subject') is-invalid @enderror" 
                               name="template_subject" value="{{ old('template_subject') }}" required>
                        <small class="text-muted">Use {name} for customer name, etc.</small>
                        @error('template_subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Template Body <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('template_body') is-invalid @enderror" 
                                  name="template_body" rows="4" required>{{ old('template_body') }}</textarea>
                        <small class="text-muted">Use {name} for customer name, {outlet_name} for outlet name, etc.</small>
                        @error('template_body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="active" 
                                   id="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                Active (Rule will be processed automatically)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('auto-greetings.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Create Rule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const triggerSelect = document.getElementById('trigger');
    const triggerDateContainer = document.getElementById('trigger_date_container');

    function toggleTriggerDate() {
        if (triggerSelect.value === 'fixed_date') {
            triggerDateContainer.style.display = 'block';
        } else {
            triggerDateContainer.style.display = 'none';
        }
    }

    triggerSelect.addEventListener('change', toggleTriggerDate);
    toggleTriggerDate(); // Initial check
</script>
@endpush
@endsection

