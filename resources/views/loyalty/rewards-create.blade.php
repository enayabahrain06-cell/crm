@extends('layouts.app')

@section('title', 'Create Reward')
@section('page-title', 'Create Reward')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">New Reward</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('loyalty.rewards.store') }}" method="POST">
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
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Required Points</label>
                        <input type="number" class="form-control @error('required_points') is-invalid @enderror" 
                               name="required_points" value="{{ old('required_points', 100) }}" min="1" required>
                        @error('required_points')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Valid From</label>
                        <input type="date" class="form-control @error('valid_from') is-invalid @enderror" 
                               name="valid_from" value="{{ old('valid_from') }}">
                        @error('valid_from')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Valid Until</label>
                        <input type="date" class="form-control @error('valid_to') is-invalid @enderror" 
                               name="valid_to" value="{{ old('valid_to') }}">
                        @error('valid_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="active" id="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Active</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('loyalty.rewards') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Create Reward
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

