{{-- Reusable Data Card Component --}}
@php
    $headerClass = $headerClass ?? '';
    $bodyClass = $bodyClass ?? '';
    $showActions = $showActions ?? false;
@endphp

<div class="data-card-modern">
    @if(isset($title))
    <div class="data-card-header {{ $headerClass }}">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="data-card-title mb-0">{{ $title }}</h5>
            @if($showActions && isset($actions))
            <div class="data-card-actions">
                {{ $actions }}
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="data-card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
</div>

<style>
.data-card-modern {
    background: var(--card-bg);
    border-radius: var(--card-radius);
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(0, 0, 0, 0.04);
    overflow: hidden;
    transition: all 0.2s ease;
    height: 100%;
}

.data-card-modern:hover {
    box-shadow: var(--shadow-lg);
}

.data-card-header {
    background: var(--card-bg);
    border-bottom: 1px solid #F1F5F9;
    padding: 1.25rem 1.5rem;
}

.data-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.data-card-actions {
    display: flex;
    gap: 0.5rem;
}

.data-card-body {
    padding: 1.5rem;
}

.data-card-body.p-0 {
    padding: 0;
}

/* Enhanced table styling within data cards */
.data-card-body .table-modern {
    margin: 0;
    border-radius: var(--item-radius);
    overflow: hidden;
}

.data-card-body .table-modern thead th {
    background: #F8FAFC;
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #E2E8F0;
    padding: 0.875rem 1rem;
    white-space: nowrap;
}

.data-card-body .table-modern tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #F1F5F9;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.data-card-body .table-modern tbody tr {
    transition: background 0.15s ease;
}

.data-card-body .table-modern tbody tr:hover {
    background: #F8FAFC;
}

.data-card-body .table-modern tbody tr:last-child td {
    border-bottom: none;
}

/* List styling within data cards */
.data-card-body .list-modern {
    list-style: none;
    padding: 0;
    margin: 0;
}

.data-card-body .list-modern .list-item-modern {
    padding: 1rem;
    border-bottom: 1px solid #F1F5F9;
    transition: all 0.15s ease;
    text-decoration: none;
    display: block;
    color: inherit;
}

.data-card-body .list-modern .list-item-modern:hover {
    background: #F8FAFC;
}

.data-card-body .list-modern .list-item-modern:last-child {
    border-bottom: none;
}

/* Chart container styling */
.data-card-body .chart-container {
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Remove border-radius from last item in body */
.data-card-body > *:last-child {
    border-bottom-right-radius: var(--card-radius);
}

.data-card-body > *:last-child {
    border-bottom-left-radius: var(--card-radius);
}
</style>

