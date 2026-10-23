@props([
    'title' => '',
    'filters' => [],
    'actionText' => null,
    'actionUrl' => '#',
    'headerExtra' => null,
    'filterId' => null,
    'chartId' => null,
    'onFilterChange' => null,
    'activeFilter' => null,
])

<div class="dash-chart-card" style="background: #ffffff; border-radius: 14px; border: 1px solid #eef2f6; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03); display: flex; flex-direction: column;">
    <div class="dash-card-header">
        <div class="dash-card-header-main">
            <h3 class="dash-card-title" title="{{ $title }}">{{ $title }}</h3>

            @if($actionText)
                <a href="{{ $actionUrl }}" class="dash-card-action-link" style="font-size: 0.775rem; font-weight: 700; color: #2563EB; text-decoration: none; display: flex; align-items: center; gap: 0.25rem; white-space: nowrap;">
                    <span>{{ $actionText }}</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @endif
        </div>

        @if(!empty($filters) || $headerExtra)
            <div class="dash-card-actions">
                @if(!empty($filters))
                    <select
                        @if($filterId) id="{{ $filterId }}" @endif
                        @if($chartId) data-chart-target="{{ $chartId }}" @endif
                        class="dash-filter-select"
                        @if($onFilterChange) onchange="{{ $onFilterChange }}(this)" @endif
                        style="font-size: 0.775rem; font-weight: 600; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.3rem 0.65rem; outline: none; cursor: pointer; transition: border-color 0.15s ease, background-color 0.15s ease;">
                        @foreach($filters as $key => $label)
                            @php
                                $val = is_string($key) && !is_numeric($key) ? $key : $label;
                                $txt = $label;
                            @endphp
                            <option value="{{ $val }}" {{ (($activeFilter ?? '') === $val || ($activeFilter ?? '') === $label) ? 'selected' : '' }}>{{ $txt }}</option>
                        @endforeach
                    </select>
                @endif

                {{ $headerExtra ?? '' }}
            </div>
        @endif
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; position: relative;">
        {{ $slot }}
    </div>
</div>

