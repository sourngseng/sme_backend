@props([
    'activeRange' => 'this_month',
    'activeLabel' => 'Oct 1, 2026 – Oct 31, 2026',
    'startDate' => null,
    'endDate' => null,
    'filterId' => 'dashboardDateFilter',
    'onSelect' => 'window.applyDashboardDateFilter',
    'align' => 'right',
])

@php
    $presets = [
        'today' => [
            'label' => transLabel('dashboard.today', 'Today'),
            'sub' => 'Oct 22, 2026',
            'icon' => '⚡',
        ],
        'yesterday' => [
            'label' => transLabel('dashboard.yesterday', 'Yesterday'),
            'sub' => 'Oct 21, 2026',
            'icon' => '⏱️',
        ],
        'last_7_days' => [
            'label' => transLabel('dashboard.last_7_days', 'Last 7 Days'),
            'sub' => 'Oct 16 – Oct 22, 2026',
            'icon' => '📅',
        ],
        'this_month' => [
            'label' => transLabel('dashboard.this_month', 'This Month'),
            'sub' => 'Oct 1 – Oct 31, 2026',
            'icon' => '📊',
        ],
        'last_30_days' => [
            'label' => transLabel('dashboard.last_30_days', 'Last 30 Days'),
            'sub' => 'Sep 23 – Oct 22, 2026',
            'icon' => '📈',
        ],
        'last_month' => [
            'label' => transLabel('dashboard.last_month', 'Last Month'),
            'sub' => 'Sep 1 – Sep 30, 2026',
            'icon' => '🗓️',
        ],
        'this_year' => [
            'label' => transLabel('dashboard.this_year', 'This Year'),
            'sub' => 'Jan 1 – Dec 31, 2026',
            'icon' => '🎯',
        ],
    ];
@endphp

@once
@push('styles')
<style>
    /* Universal Date Filter Styling */
    .date-filter-wrapper {
        position: relative;
        display: inline-block;
        font-family: inherit;
    }

    .dashboard-date-filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.48rem 0.9rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        font-family: inherit;
        user-select: none;
    }
    .dashboard-date-filter-btn:hover {
        border-color: #2563eb;
        background: #fafbfd;
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.12);
    }
    .dashboard-date-filter-btn:active {
        transform: scale(0.98);
    }

    .filter-label-text {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: -0.01em;
    }

    .filter-chevron {
        transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        flex-shrink: 0;
    }

    /* Mobile Backdrop Overlay */
    .date-filter-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: 9998;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }
    .date-filter-backdrop.active {
        display: block !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    /* Common Components inside Panel */
    .mobile-drag-handle {
        display: none;
        padding-top: 0.2rem;
    }
    .drag-pill {
        display: block;
        width: 44px;
        height: 5px;
        background: #cbd5e1;
        border-radius: 999px;
        margin: 0 auto 0.85rem auto;
    }

    .filter-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.45rem 0.5rem 0.65rem 0.5rem;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 0.5rem;
    }
    .filter-panel-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.775rem;
        font-weight: 800;
        color: #475569;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .filter-panel-actions {
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }
    .active-preset-badge {
        font-size: 0.725rem;
        font-weight: 700;
        color: #2563EB;
        background: #eff6ff;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        border: 1px solid #dbeafe;
    }
    .date-filter-close-btn {
        display: none;
        background: #f1f5f9;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        color: #64748b;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }
    .date-filter-close-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .presets-list {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .preset-item-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0.48rem 0.7rem;
        background: transparent;
        border: 1px solid transparent;
        border-radius: 8px;
        cursor: pointer;
        text-align: left;
        transition: all 0.15s ease;
        font-family: inherit;
    }
    .preset-item-btn:hover {
        background: #f8fafc;
    }
    .preset-item-btn.active {
        background: #eff6ff !important;
        border-color: #bfdbfe !important;
    }
    .preset-item-info {
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }
    .preset-emoji {
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
        flex-shrink: 0;
    }
    .preset-title {
        font-size: 0.825rem;
        font-weight: 600;
        color: #1e293b;
    }
    .preset-item-btn.active .preset-title {
        color: #1d4ed8;
        font-weight: 800;
    }
    .preset-sub {
        font-size: 0.7rem;
        color: #64748b;
        margin-top: 1px;
    }
    .check-icon {
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #2563EB;
        color: #ffffff;
        font-size: 0.7rem;
        font-weight: 900;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
    }

    .filter-divider {
        height: 1px;
        background: #f1f5f9;
        margin: 0.65rem 0;
    }

    .custom-range-card {
        padding: 0.55rem 0.7rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .custom-range-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.55rem;
    }
    .custom-range-title {
        font-size: 0.775rem;
        font-weight: 800;
        color: #1e293b;
    }
    .custom-range-desc {
        font-size: 0.7rem;
        color: #94a3b8;
        font-weight: 600;
    }
    .custom-date-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.65rem;
        margin-bottom: 0.7rem;
    }
    .date-input-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 0.3rem;
    }
    .custom-date-input {
        width: 100%;
        box-sizing: border-box;
        padding: 0.42rem 0.55rem;
        font-size: 0.8rem;
        color: #1e293b;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        outline: none;
        font-family: inherit;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .custom-date-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .apply-filter-btn {
        width: 100%;
        padding: 0.55rem;
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 0.8125rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.28);
        font-family: inherit;
        transition: transform 0.1s ease, box-shadow 0.15s ease;
    }
    .apply-filter-btn:active {
        transform: scale(0.98);
    }

    /* Desktop Viewport Rules (>= 769px) */
    @media (min-width: 769px) {
        .date-filter-dropdown-panel {
            position: absolute !important;
            top: calc(100% + 8px);
            width: 320px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.14), 0 6px 14px -2px rgba(15, 23, 42, 0.05);
            padding: 0.75rem;
            z-index: 120;
            font-family: inherit;
        }
    }

    /* Mobile Viewport Rules (<= 768px): Native Bottom Sheet Experience */
    @media (max-width: 768px) {
        body.date-filter-sheet-open {
            overflow: hidden !important;
            touch-action: none;
        }

        .date-filter-wrapper {
            max-width: 100%;
        }

        .dashboard-date-filter-btn {
            padding: 0.52rem 0.85rem !important;
            max-width: 100%;
        }

        .filter-label-text {
            font-size: 0.8125rem !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: calc(100vw - 110px) !important;
        }

        .mobile-drag-handle {
            display: block !important;
        }

        .date-filter-close-btn {
            display: inline-flex !important;
        }

        .date-filter-dropdown-panel {
            position: fixed !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            top: auto !important;
            width: 100% !important;
            max-width: 100vw !important;
            box-sizing: border-box !important;
            border-radius: 24px 24px 0 0 !important;
            border: 1px solid #e2e8f0 !important;
            border-bottom: none !important;
            background: #ffffff !important;
            box-shadow: 0 -12px 40px rgba(15, 23, 42, 0.28) !important;
            z-index: 9999 !important;
            padding: 1.15rem 1.25rem calc(1.75rem + env(safe-area-inset-bottom, 24px)) 1.25rem !important;
            max-height: 85vh !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            transform: translateY(100%);
            opacity: 0;
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease !important;
            pointer-events: none;
        }

        .date-filter-dropdown-panel.open {
            display: block !important;
            transform: translateY(0) !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }

        .preset-item-btn {
            padding: 0.65rem 0.85rem !important;
            border-radius: 10px !important;
        }

        .preset-title {
            font-size: 0.875rem !important;
        }

        .preset-sub {
            font-size: 0.725rem !important;
        }

        .custom-range-card {
            padding: 0.75rem 0.85rem !important;
            border-radius: 12px !important;
        }

        .custom-date-input {
            min-height: 42px !important;
            padding: 0.45rem 0.65rem !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
        }

        .apply-filter-btn {
            min-height: 46px !important;
            font-size: 0.85rem !important;
            font-weight: 800 !important;
            border-radius: 10px !important;
        }
    }
</style>
@endpush
@endonce

<div class="date-filter-wrapper" id="{{ $filterId }}_wrapper">
    <!-- Date Range Filter Trigger Button -->
    <button
        type="button"
        class="dashboard-date-filter-btn"
        id="{{ $filterId }}_btn"
        onclick="toggleDateFilterMenu('{{ $filterId }}')"
        aria-haspopup="dialog"
        aria-expanded="false"
    >
        <svg class="calendar-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <span class="filter-label-text" id="{{ $filterId }}_text">
            {{ $activeLabel }}
        </span>
        <svg class="filter-chevron" id="{{ $filterId }}_chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </button>

    <!-- Mobile Screen Backdrop Overlay -->
    <div class="date-filter-backdrop" id="{{ $filterId }}_backdrop" onclick="closeDateFilterMenu('{{ $filterId }}')" aria-hidden="true"></div>

    <!-- Dropdown Panel / Mobile Bottom Sheet -->
    <div
        class="date-filter-dropdown-panel"
        id="{{ $filterId }}_dropdown"
        role="dialog"
        aria-label="{{ transLabel('dashboard.filter_by_date', 'Filter By Date') }}"
        style="display: none;"
    >
        <!-- Mobile Bottom Sheet Top Drag Pill Handle -->
        <div class="mobile-drag-handle">
            <span class="drag-pill"></span>
        </div>

        <!-- Panel Header -->
        <div class="filter-panel-header">
            <div class="filter-panel-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>{{ transLabel('dashboard.filter_by_date', 'Filter By Date') }}</span>
            </div>
            <div class="filter-panel-actions">
                <span class="active-preset-badge" id="{{ $filterId }}_badge">
                    {{ $presets[$activeRange]['label'] ?? transLabel('dashboard.custom', 'Custom') }}
                </span>
                <button type="button" class="date-filter-close-btn" onclick="closeDateFilterMenu('{{ $filterId }}')" aria-label="{{ transLabel('dashboard.close', 'Close') }}">
                    ✕
                </button>
            </div>
        </div>

        <!-- Presets List -->
        <div class="presets-list">
            @foreach($presets as $key => $p)
                <button
                    type="button"
                    class="preset-item-btn {{ $activeRange === $key ? 'active' : '' }}"
                    data-range="{{ $key }}"
                    onclick="handlePresetSelection('{{ $filterId }}', '{{ $key }}', '{{ $p['sub'] }}', '{{ $p['label'] }}', {{ $onSelect }})"
                >
                    <div class="preset-item-info">
                        <span class="preset-emoji">{{ $p['icon'] }}</span>
                        <div>
                            <div class="preset-title">
                                {{ $p['label'] }}
                            </div>
                            <div class="preset-sub">
                                {{ $p['sub'] }}
                            </div>
                        </div>
                    </div>
                    <div class="check-icon" style="display: {{ $activeRange === $key ? 'flex' : 'none' }};">
                        ✓
                    </div>
                </button>
            @endforeach
        </div>

        <!-- Divider -->
        <div class="filter-divider"></div>

        <!-- Custom Date Range Form -->
        <div class="custom-range-card">
            <div class="custom-range-header">
                <span class="custom-range-title">{{ transLabel('dashboard.custom_range', 'Custom Range') }}</span>
                <span class="custom-range-desc">{{ transLabel('dashboard.select_start_end', 'Select start & end') }}</span>
            </div>

            <div class="custom-date-grid">
                <div>
                    <label for="{{ $filterId }}_start" class="date-input-label">{{ transLabel('dashboard.start_date', 'Start Date') }}</label>
                    <input
                        type="date"
                        id="{{ $filterId }}_start"
                        value="{{ $startDate ?? '2026-10-01' }}"
                        class="custom-date-input"
                    >
                </div>
                <div>
                    <label for="{{ $filterId }}_end" class="date-input-label">{{ transLabel('dashboard.end_date', 'End Date') }}</label>
                    <input
                        type="date"
                        id="{{ $filterId }}_end"
                        value="{{ $endDate ?? '2026-10-31' }}"
                        class="custom-date-input"
                    >
                </div>
            </div>

            <button
                type="button"
                id="{{ $filterId }}_apply_btn"
                onclick="handleCustomRangeApply('{{ $filterId }}', {{ $onSelect }})"
                class="apply-filter-btn"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>{{ transLabel('dashboard.apply_date_filter', 'Apply Date Filter') }}</span>
            </button>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    // Universal Date Filter Toggle
    function toggleDateFilterMenu(filterId) {
        const dropdown = document.getElementById(filterId + '_dropdown');
        const btn = document.getElementById(filterId + '_btn');
        const chevron = document.getElementById(filterId + '_chevron');
        const backdrop = document.getElementById(filterId + '_backdrop');
        if (!dropdown) return;

        const isCurrentlyOpen = dropdown.classList.contains('open');

        // Close any other open date filter menus
        closeAllDateFilterMenus();

        if (!isCurrentlyOpen) {
            dropdown.style.display = 'block';

            if (window.innerWidth <= 768) {
                // Mobile Bottom Sheet Mode
                requestAnimationFrame(() => {
                    dropdown.classList.add('open');
                    if (backdrop) backdrop.classList.add('active');
                    document.body.classList.add('date-filter-sheet-open');
                });
            } else {
                // Desktop Dropdown Mode with Smart Boundary Positioning
                dropdown.classList.add('open');
                if (btn) {
                    const rect = btn.getBoundingClientRect();
                    if (rect.right - 320 < 10) {
                        dropdown.style.left = '0';
                        dropdown.style.right = 'auto';
                    } else {
                        dropdown.style.right = '0';
                        dropdown.style.left = 'auto';
                    }
                }
            }

            if (chevron) chevron.style.transform = 'rotate(180deg)';
            if (btn) btn.setAttribute('aria-expanded', 'true');
        }
    }

    // Close specific filter menu
    function closeDateFilterMenu(filterId) {
        const dropdown = document.getElementById(filterId + '_dropdown');
        const btn = document.getElementById(filterId + '_btn');
        const chevron = document.getElementById(filterId + '_chevron');
        const backdrop = document.getElementById(filterId + '_backdrop');

        if (dropdown) {
            dropdown.classList.remove('open');
            if (window.innerWidth <= 768) {
                setTimeout(() => {
                    if (!dropdown.classList.contains('open')) {
                        dropdown.style.display = 'none';
                    }
                }, 280);
            } else {
                dropdown.style.display = 'none';
            }
        }
        if (backdrop) backdrop.classList.remove('active');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
        if (btn) btn.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('date-filter-sheet-open');
    }

    // Close all open date filter menus
    function closeAllDateFilterMenus() {
        document.querySelectorAll('.date-filter-dropdown-panel').forEach(p => {
            p.classList.remove('open');
            p.style.display = 'none';
        });
        document.querySelectorAll('.date-filter-backdrop').forEach(b => {
            b.classList.remove('active');
        });
        document.querySelectorAll('.filter-chevron').forEach(c => {
            c.style.transform = 'rotate(0deg)';
        });
        document.querySelectorAll('.dashboard-date-filter-btn').forEach(b => {
            b.setAttribute('aria-expanded', 'false');
        });
        document.body.classList.remove('date-filter-sheet-open');
    }

    // Preset Selection Handler
    function handlePresetSelection(filterId, rangeKey, subText, labelText, callbackFn) {
        const textSpan = document.getElementById(filterId + '_text');
        const badge = document.getElementById(filterId + '_badge');

        // Update active classes
        const wrapper = document.getElementById(filterId + '_wrapper');
        if (wrapper) {
            wrapper.querySelectorAll('.preset-item-btn').forEach(btn => {
                const isMatch = btn.getAttribute('data-range') === rangeKey;
                btn.classList.toggle('active', isMatch);
                const check = btn.querySelector('.check-icon');
                if (check) check.style.display = isMatch ? 'flex' : 'none';
            });
        }

        const displayText = subText ? subText : labelText;
        if (textSpan) textSpan.textContent = displayText;
        if (badge) badge.textContent = labelText;

        closeDateFilterMenu(filterId);

        if (typeof callbackFn === 'function') {
            callbackFn(rangeKey, displayText, null, null);
        }
    }

    // Custom Date Range Handler
    function handleCustomRangeApply(filterId, callbackFn) {
        const startInput = document.getElementById(filterId + '_start');
        const endInput = document.getElementById(filterId + '_end');
        const textSpan = document.getElementById(filterId + '_text');
        const badge = document.getElementById(filterId + '_badge');

        const startDate = startInput ? startInput.value : '';
        const endDate = endInput ? endInput.value : '';

        if (!startDate || !endDate) {
            if (window.Toast) window.Toast.error('Please choose both start and end dates.');
            return;
        }

        if (new Date(startDate) > new Date(endDate)) {
            if (window.Toast) window.Toast.error('Start date cannot be after end date.');
            return;
        }

        const formattedText = startDate + ' – ' + endDate;
        if (textSpan) textSpan.textContent = formattedText;
        if (badge) badge.textContent = 'Custom';

        // Clear active preset highlights
        const wrapper = document.getElementById(filterId + '_wrapper');
        if (wrapper) {
            wrapper.querySelectorAll('.preset-item-btn').forEach(btn => {
                btn.classList.remove('active');
                const check = btn.querySelector('.check-icon');
                if (check) check.style.display = 'none';
            });
        }

        closeDateFilterMenu(filterId);

        if (typeof callbackFn === 'function') {
            callbackFn('custom', formattedText, startDate, endDate);
        }
    }

    // Desktop Click Outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.date-filter-wrapper') && !e.target.closest('.date-filter-backdrop')) {
            closeAllDateFilterMenus();
        }
    });

    // Escape Key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllDateFilterMenus();
        }
    });
</script>
@endpush
@endonce
