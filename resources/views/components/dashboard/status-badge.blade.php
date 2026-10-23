@props([
    'status' => 'Pending',
])

@php
    $normalized = strtolower(str_replace([' ', '-'], '_', $status));

    $statusConfig = [
        'delivered' => ['bg' => '#ecfdf5', 'color' => '#059669', 'label' => 'Delivered'],
        'out_for_delivery' => ['bg' => '#eff6ff', 'color' => '#2563eb', 'label' => 'Out for Delivery'],
        'preparing' => ['bg' => '#faf5ff', 'color' => '#9333ea', 'label' => 'Preparing'],
        'confirmed' => ['bg' => '#e0f2fe', 'color' => '#0284c7', 'label' => 'Confirmed'],
        'pending' => ['bg' => '#fffbeb', 'color' => '#d97706', 'label' => 'Pending'],
        'cancelled' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'label' => 'Cancelled'],
        'paid' => ['bg' => '#ecfdf5', 'color' => '#059669', 'label' => 'Paid'],
        'failed' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'label' => 'Failed'],
        'refunded' => ['bg' => '#f1f5f9', 'color' => '#64748b', 'label' => 'Refunded'],
        'active' => ['bg' => '#ecfdf5', 'color' => '#059669', 'label' => 'Active'],
    ];

    $cfg = $statusConfig[$normalized] ?? ['bg' => '#f1f5f9', 'color' => '#475569', 'label' => $status];
@endphp

<span class="status-badge" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.725rem; font-weight: 700; background: {{ $cfg['bg'] }}; color: {{ $cfg['color'] }}; white-space: nowrap;">
    {{ $cfg['label'] }}
</span>
