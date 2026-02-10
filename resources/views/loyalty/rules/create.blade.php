@extends('layouts.app')

@section('title', 'Create Rule')
@section('page-title', 'Create Loyalty Rule')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">New Loyalty Rule</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('loyalty.rules.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select @error('type') is-invalid @enderror" name="type" id="ruleType" required>
                            <option value="">Select type</option>
                            <option value="earn" {{ old('type') === 'earn' ? 'selected' : '' }}>
                                <i class="bi bi-arrow-up-circle"></i> Earn (How customers earn points)
                            </option>
                            <option value="burn" {{ old('type') === 'burn' ? 'selected' : '' }}>
                                <i class="bi bi-arrow-down-circle"></i> Burn (How customers redeem points)
                            </option>
                            <option value="upgrade" {{ old('type') === 'upgrade' ? 'selected' : '' }}>
                                <i class="bi bi-arrow-up-circle"></i> Tier Upgrade (How customers upgrade tiers)
                            </option>
                        </select>
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Earn-specific fields -->
                    <div id="earnFields" class="d-none">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Points Earned</label>
                                <input type="number" class="form-control @error('points_earned') is-invalid @enderror" 
                                       name="points_earned" value="{{ old('points_earned', 1) }}" min="0">
                                @error('points_earned')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Multiplier</label>
                                <input type="number" class="form-control @error('multiplier') is-invalid @enderror" 
                                       name="multiplier" value="{{ old('multiplier', 1) }}" min="1" step="0.1">
                                @error('multiplier')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Upgrade-specific fields -->
                    <div id="upgradeFields" class="d-none">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Points Required</label>
                                <input type="number" class="form-control @error('points_required') is-invalid @enderror" 
                                       name="points_required" value="{{ old('points_required') }}" min="0">
                                @error('points_required')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tier Name</label>
                                <input type="text" class="form-control @error('tier_name') is-invalid @enderror" 
                                       name="tier_name" value="{{ old('tier_name') }}" placeholder="e.g., Gold">
                                @error('tier_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Common fields -->
                    <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Min Visit Count</label>
                        <input type="number" class="form-control @error('min_visit_count') is-invalid @enderror" 
                               name="min_visit_count" value="{{ old('min_visit_count', 0) }}" min="0">
                        @error('min_visit_count')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Min Spent Amount</label>
                        <input type="number" class="form-control @error('min_spent_amount') is-invalid @enderror" 
                               name="min_spent_amount" value="{{ old('min_spent_amount', 0) }}" min="0" step="0.01">
                        @error('min_spent_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Priority</label>
                    <input type="number" class="form-control @error('priority') is-invalid @enderror" 
                           name="priority" value="{{ old('priority', 0) }}" min="0">
                        @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Higher priority rules are evaluated first</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="active" id="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Active</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('loyalty.rules.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Create Rule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('ruleType').addEventListener('change', function() {
    const earnFields = document.getElementById('earnFields');
    const upgradeFields = document.getElementById('upgradeFields');
    
    earnFields.classList.add('d-none');
    upgradeFields.classList.add('d-none');
    
    if (this.value === 'earn') {
        earnFields.classList.remove('d-none');
    } else if (this.value === 'upgrade') {
        upgradeFields.classList.remove('d-none');
    }
});

// Show relevant fields on page load if there are old values
document.addEventListener('DOMContentLoaded', function() {
    const ruleType = document.getElementById('ruleType').value;
    if (ruleType === 'earn') {
        document.getElementById('earnFields').classList.remove('d-none');
    } else if (ruleType === 'upgrade') {
        document.getElementById('upgradeFields').classList.remove('d-none');
    }
});
</script>
@endpush
@endsection

