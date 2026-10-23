@props([
    'title' => '',
    'value' => '0',
    'change' => '0%',
    'trend' => 'up',
    'period' => 'vs last month',
    'color' => 'blue',
    'sparkline' => 'smooth',
    'key' => null,
])

@php
    $colorConfig = [
        'blue' => [
            'bg' => '#eff6ff',
            'icon_bg' => '#2563EB',
            'stroke' => '#3b82f6',
            'text' => '#1d4ed8',
        ],
        'green' => [
            'bg' => '#ecfdf5',
            'icon_bg' => '#10B981',
            'stroke' => '#10b981',
            'text' => '#047857',
        ],
        'orange' => [
            'bg' => '#fff7ed',
            'icon_bg' => '#F59E0B',
            'stroke' => '#f59e0b',
            'text' => '#b45309',
        ],
        'purple' => [
            'bg' => '#faf5ff',
            'icon_bg' => '#8B5CF6',
            'stroke' => '#8b5cf6',
            'text' => '#6d28d9',
        ],
        'red' => [
            'bg' => '#fef2f2',
            'icon_bg' => '#EF4444',
            'stroke' => '#ef4444',
            'text' => '#b91c1c',
        ],
        'teal' => [
            'bg' => '#f0fdfa',
            'icon_bg' => '#06B6D4',
            'stroke' => '#06b6d4',
            'text' => '#0f766e',
        ],
        'indigo' => [
            'bg' => '#eef2ff',
            'icon_bg' => '#4F46E5',
            'stroke' => '#6366f1',
            'text' => '#3730a3',
        ],
        'amber' => [
            'bg' => '#fffbeb',
            'icon_bg' => '#D97706',
            'stroke' => '#f59e0b',
            'text' => '#b45309',
        ],
    ];

    $cfg = $colorConfig[$color] ?? $colorConfig['blue'];
    $isUp = ($trend === 'up');
    $strokeColor = $isUp ? $cfg['stroke'] : '#EF4444';

    // Upward trajectory curve and area fill
    $linePathUp = 'M 0 22 C 20 22, 35 26, 50 16 C 65 7, 80 13, 98 5';
    $areaPathUp = 'M 0 22 C 20 22, 35 26, 50 16 C 65 7, 80 13, 98 5 L 98 29 L 0 29 Z';

    // Downward trajectory curve and area fill
    $linePathDown = 'M 0 7 C 20 7, 35 5, 50 15 C 65 24, 80 19, 98 25';
    $areaPathDown = 'M 0 7 C 20 7, 35 5, 50 15 C 65 24, 80 19, 98 25 L 98 29 L 0 29 Z';

    $linePath = $isUp ? $linePathUp : $linePathDown;
    $areaPath = $isUp ? $areaPathUp : $areaPathDown;
    $endY = $isUp ? 5 : 25;
    $gradientId = 'grad-' . ($key ?: $color) . '-' . bin2hex(random_bytes(4));
@endphp

<div class="kpi-card" @if($key) data-kpi="{{ $key }}" id="kpi-{{ $key }}" @endif data-stroke="{{ $cfg['stroke'] }}" style="background: #ffffff; border-radius: 16px; border: 1px solid #eef2f6; padding: 1.15rem 1.25rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.03), 0 4px 12px rgba(0,0,0,0.02); transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); scroll-snap-align: start; cursor: pointer; position: relative;">
    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: {{ $cfg['icon_bg'] }}; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
            {{ $icon ?? '' }}
        </div>
        <div style="flex: 1; min-width: 0;">
            <div style="font-size: 0.775rem; font-weight: 600; color: #64748b; margin-bottom: 0.25rem;">{{ $title }}</div>
            <div class="kpi-value" style="font-size: 1.65rem; font-weight: 800; color: #0f172a; line-height: 1.1; letter-spacing: -0.02em;">{{ $value }}</div>
        </div>
    </div>

    <div style="display: flex; align-items: flex-end; justify-content: space-between; margin-top: 1rem; padding-top: 0.5rem;">
        <div class="kpi-trend-box" style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 700; color: {{ $trend === 'up' ? '#10B981' : '#EF4444' }};">
            <span class="kpi-change">{{ $trend === 'up' ? '↑' : '↓' }} {{ $change }}</span>
            <span class="kpi-period" style="font-size: 0.7rem; font-weight: 500; color: #94a3b8;">{{ $period }}</span>
        </div>

        <!-- Micro Sparkline SVG with Modern Area Gradient & Pulse Tip -->
        <div class="kpi-sparkline-box" style="width: 70px; height: 28px; position: relative; overflow: visible;">
            <svg class="kpi-sparkline-svg" width="70" height="28" viewBox="0 0 100 30" fill="none" xmlns="http://www.w3.org/2000/svg" style="overflow: visible;">
                <defs>
                    <linearGradient id="{{ $gradientId }}" class="kpi-sparkline-gradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="{{ $strokeColor }}" stop-opacity="0.35"/>
                        <stop offset="100%" stop-color="{{ $strokeColor }}" stop-opacity="0.0"/>
                    </linearGradient>
                </defs>
                <path class="kpi-sparkline-area" d="{{ $areaPath }}" fill="url(#{{ $gradientId }})" />
                <path class="kpi-sparkline-line" d="{{ $linePath }}" stroke="{{ $strokeColor }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <circle class="kpi-sparkline-pulse" cx="98" cy="{{ $endY }}" r="5" fill="{{ $strokeColor }}" opacity="0.22" />
                <circle class="kpi-sparkline-dot" cx="98" cy="{{ $endY }}" r="2.8" fill="{{ $strokeColor }}" />
            </svg>
        </div>
    </div>
</div>
