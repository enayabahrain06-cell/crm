@extends('layouts.app')

@section('title', 'Social Links')
@section('page-title', 'Social Links - ' . $outlet->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Manage Social Links</h5>
                    <small class="text-muted">{{ $outlet->name }}</small>
                </div>
                <a href="{{ route('outlets.show', $outlet) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Outlet
                </a>
            </div>
            <div class="card-body">
                <!-- Add New Social Link Form -->
                @can('outlets.edit')
                <form action="{{ route('outlets.social-links.store', $outlet) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Platform</label>
                            <select class="form-select @error('platform') is-invalid @enderror" name="platform" required>
                                <option value="">Select...</option>
                                <option value="facebook">Facebook</option>
                                <option value="instagram">Instagram</option>
                                <option value="twitter">Twitter</option>
                                <option value="linkedin">LinkedIn</option>
                                <option value="youtube">YouTube</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="telegram">Telegram</option>
                                <option value="tiktok">TikTok</option>
                                <option value="pinterest">Pinterest</option>
                                <option value="snapchat">Snapchat</option>
                                <option value="website">Website</option>
                                <option value="email">Email</option>
                                <option value="phone">Phone</option>
                                <option value="map-marker">Map</option>
                            </select>
                            @error('platform')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Label</label>
                            <input type="text" class="form-control @error('label') is-invalid @enderror" 
                                   name="label" placeholder="e.g., Follow us" maxlength="50" required>
                            @error('label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">URL</label>
                            <input type="url" class="form-control @error('url') is-invalid @enderror" 
                                   name="url" placeholder="https://..." required>
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus-lg"></i> Add
                            </button>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label class="form-label">Color (optional)</label>
                            <input type="color" class="form-control form-control-color" 
                                   name="color" value="#6c757d">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" value="0">
                        </div>
                    </div>
                </form>
                @endcan

                <!-- Social Links List -->
                @if($outlet->socialLinks && $outlet->socialLinks->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($outlet->socialLinks->sortBy('sort_order') as $link)
                    <div class="list-group-item d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-2 me-3" 
                                 style="background-color: {{ $link->color ?? '#f8f9fa' }} !important;">
                                <i class="bi bi-{{ $link->platform }}" 
                                   style="color: {{ $link->color ? 'white' : 'inherit' }};"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $link->label }}</div>
                                <a href="{{ $link->url }}" target="_blank" class="text-muted small text-decoration-none">
                                    {{ Str::limit($link->url, 50) }}
                                    <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.75rem;"></i>
                                </a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @can('outlets.edit')
                            <form action="{{ route('outlets.social-links.destroy', [$outlet, $link]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Delete this link?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-link-45deg display-4"></i>
                    <p class="mt-2 mb-0">No social links added yet</p>
                    <small>Add your first social link using the form above</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

