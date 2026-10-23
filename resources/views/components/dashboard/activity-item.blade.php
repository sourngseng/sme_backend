@props([
    'title' => '',
    'description' => '',
    'time' => '',
    'color' => 'blue',
    'type' => null,
])

@php
    $colors = [
        'blue' => ['bg' => '#eff6ff', 'text' => '#2563eb'],
        'green' => ['bg' => '#ecfdf5', 'text' => '#059669'],
        'orange' => ['bg' => '#fff7ed', 'text' => '#ea580c'],
        'purple' => ['bg' => '#faf5ff', 'text' => '#9333ea'],
        'teal' => ['bg' => '#f0fdfa', 'text' => '#0d9488'],
        'red' => ['bg' => '#fef2f2', 'text' => '#dc2626'],
    ];

    $cfg = $colors[$color] ?? $colors['blue'];
@endphp

<div class="activity-item-row" @if($type) data-activity-type="{{ $type }}" @endif style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.55rem 0; border-bottom: 1px solid #f8fafc; transition: all 0.2s ease;">
    <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ $cfg['bg'] }}; color: {{ $cfg['text'] }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
        {{ $slot }}
    </div>
    <div style="flex: 1; min-width: 0;">
        <div style="display: flex; align-items: baseline; justify-content: space-between; gap: 0.5rem;">
            <div style="font-size: 0.785rem; font-weight: 700; color: #0f172a; line-height: 1.2;">{{ $title }}</div>
            <div style="font-size: 0.675rem; color: #94a3b8; white-space: nowrap;">{{ $time }}</div>
        </div>
        <div style="font-size: 0.725rem; color: #64748b; margin-top: 1px; line-height: 1.25;">{{ $description }}</div>
    </div>
</div>
