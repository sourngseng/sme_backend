@props([
    'title' => '',
    'viewAllUrl' => '#',
])

<div class="dash-table-card" style="background: #ffffff; border-radius: 14px; border: 1px solid #eef2f6; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03); display: flex; flex-direction: column;">
    <div class="dash-card-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; gap: 0.5rem;">
        <h3 class="dash-card-title" style="font-size: 1.05rem; font-weight: 800; color: #0f172a; letter-spacing: -0.01em;">{{ $title }}</h3>
        <a href="{{ $viewAllUrl }}" class="dash-card-link" style="font-size: 0.775rem; font-weight: 700; color: #2563EB; text-decoration: none; white-space: nowrap; flex-shrink: 0;">{{ transLabel('common.view_all', 'View All') }}</a>
    </div>

    <div style="overflow-x: auto; -webkit-overflow-scrolling: touch; flex: 1;">
        {{ $slot }}
    </div>
</div>
