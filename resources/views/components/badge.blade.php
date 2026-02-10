{{-- Reusable Badge Component --}}
@php
    $variant = $variant ?? 'primary';
    $size = $size ?? 'md';
    $rounded = $rounded ?? true;
@endphp

<span class="badge-modern badge-{{ $variant }} badge-{{ $size }} {{ $rounded ? 'badge-rounded' : '' }}">
    @if(isset($icon))
    <i class="{{ $icon }} me-1"></i>
    @endif
    {{ $slot }}
</span>

<style>
.badge-modern {
    display: inline-flex;
    align-items: center;
    font-weight: 600;
    font-size: 0.75rem;
    line-height: 1;
    border-radius: 9999px;
    padding: 0.375rem 0.75rem;
    transition: all 0.2s ease;
    white-space: nowrap;
}

/* Size variants */
.badge-sm {
    font-size: 0.6875rem;
    padding: 0.25rem 0.5rem;
}

.badge-md {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.badge-lg {
    font-size: 0.8125rem;
    padding: 0.5rem 1rem;
}

/* Color variants - Soft pastel backgrounds with darker text */
.badge-primary {
    background: var(--primary-subtle);
    color: var(--primary-color);
}

.badge-secondary {
    background: #F1F5F9;
    color: var(--text-secondary);
}

.badge-success {
    background: #DCFCE7;
    color: #166534;
}

.badge-warning {
    background: #FEF3C7;
    color: #92400E;
}

.badge-danger {
    background: #FEE2E2;
    color: #991B1B;
}

.badge-info {
    background: #DBEAFE;
    color: #1E40AF;
}

.badge-light {
    background: #F8FAFC;
    color: var(--text-secondary);
}

.badge-dark {
    background: #1E293B;
    color: white;
}

/* Status variants */
.badge-active {
    background: #DCFCE7;
    color: #166534;
}

.badge-inactive {
    background: #FEE2E2;
    color: #991B1B;
}

.badge-pending {
    background: #FEF3C7;
    color: #92400E;
}

.badge-approved {
    background: #DBEAFE;
    color: #1E40AF;
}

/* Rounded variant (always true now) */
.badge-rounded {
    border-radius: 9999px;
}
</style>

