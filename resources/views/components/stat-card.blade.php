{{-- Reusable Stat Card Component --}}
@php
    $iconClass = $iconClass ?? 'bi bi-graph-up';
    $bgClass = $bgClass ?? 'bg-primary-subtle';
    $changeType = $changeType ?? 'positive';
    $changeIcon = $changeIcon ?? 'bi-arrow-up';
    $iconColor = $iconColor ?? 'var(--primary-color)';
    $iconBg = $iconBg ?? 'var(--primary-subtle)';
@endphp

<div class="stat-card">
    <div class="d-flex justify-content-between align-items-start">
        <div class="stat-content">
            <div class="stat-value">{{ $value }}</div>
            <div class="stat-label">{{ $label }}</div>
            @if(isset($change))
            <div class="stat-change {{ $changeType }}">
                <i class="{{ $changeIcon }}"></i> {{ $change }}
            </div>
            @endif
        </div>
        <div class="stat-icon" style="background: {{ $iconBg }}; color: {{ $iconColor }};">
            <i class="{{ $iconClass }}"></i>
        </div>
    </div>
</div>

<style>
.stat-card {
    background: var(--card-bg);
    border-radius: var(--card-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    transition: all 0.2s ease;
    height: 100%;
    border: 1px solid rgba(0, 0, 0, 0.04);
}

.stat-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.stat-content {
    flex: 1;
    padding-right: 1rem;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
    margin-bottom: 0.25rem;
    letter-spacing: -0.02em;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.stat-change {
    font-size: 0.8125rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-weight: 500;
}

.stat-change.positive {
    color: var(--success-color);
}

.stat-change.negative {
    color: var(--danger-color);
}

.stat-change.neutral {
    color: var(--text-secondary);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
</style>

