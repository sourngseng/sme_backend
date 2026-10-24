@forelse($rooms as $r)
    <tr class="academic-row room-row"
        data-room="{{ $r['room'] }}"
        data-class="{{ $r['class_code'] }}"
        data-building="{{ $r['building'] }}"
        data-status="{{ $r['status'] }}">

        {{-- 1. Pinned: Checkbox --}}
        <td class="sticky-col-select" data-col="select">
            <input type="checkbox" class="row-checkbox room-row-checkbox" value="{{ $r['room'] }}" style="cursor: pointer;">
        </td>

        {{-- 2. Pinned: Room Number / Code --}}
        <td class="sticky-col-code" data-col="code">
            <span class="id-code-badge" style="font-size: 0.85rem; font-weight: 800;">
                {{ explode('-', $r['room'])[0] ?? $r['room'] }}
            </span>
        </td>

        {{-- 3. Pinned: Room Title & Inline Action (Meta Content List Concept) --}}
        <td class="sticky-col-student" data-col="room_name">
            <div class="entity-cell-layout">
                <div class="entity-cell-main">
                    <div class="room-icon-avatar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <div class="entity-cell-meta">
                        <div class="entity-name-en">{{ $r['room'] }}</div>
                        <div class="entity-name-km">{{ $r['room_km'] ?: $r['building'] }}</div>
                    </div>
                </div>

                <!-- Action button as Context Menu (Meta Content List Concept) -->
                <button type="button" class="btn-context-menu" title="{{ transLabel('common.options', 'Options') }}"
                        onclick="window.Toast.info('Campus Room: {{ addslashes($r['room']) }} assigned to Section {{ $r['class_code'] }}')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="1.5"></circle>
                        <circle cx="6" cy="12" r="1.5"></circle>
                        <circle cx="18" cy="12" r="1.5"></circle>
                    </svg>
                </button>
            </div>
        </td>

        {{-- 4. Scrollable: Building Block --}}
        <td data-col="building">
            <span style="font-weight: 700; color: #1E293B;">{{ $r['building'] }}</span>
        </td>

        {{-- 5. Scrollable: Assigned Section --}}
        <td data-col="assigned_class">
            <span class="gender-pill gender-male" style="font-weight: 800;">{{ $r['class_code'] }} ({{ $r['grade'] }})</span>
        </td>

        {{-- 6. Scrollable: Homeroom Lead --}}
        <td data-col="teacher">
            <div style="font-weight: 600; color: #0F172A; font-size: 0.8rem;">{{ $r['teacher'] }}</div>
            <div style="font-size: 0.725rem; color: #64748B;">{{ $r['phone'] }}</div>
        </td>

        {{-- 7. Scrollable: Utilization --}}
        <td data-col="capacity">
            <div style="font-size: 0.8rem; font-weight: 700; color: #0F172A;">
                {{ $r['enrolled'] }} / {{ $r['capacity'] }} Seats
            </div>
            <div style="font-size: 0.7rem; color: #10B981; font-weight: 600;">
                {{ round(($r['enrolled'] / max(1, $r['capacity'])) * 100) }}% Filled
            </div>
        </td>

        {{-- 8. Scrollable: Crate Drop Point --}}
        <td data-col="drop">
            <span class="meal-pill meal-regular">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v2"/><path d="m7.5 4.27 9 5.15"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22" x2="12" y2="12"/></svg>
                <span>{{ $r['drop_point'] }}</span>
            </span>
        </td>

        {{-- 9. Scrollable: Shift --}}
        <td data-col="shift">
            <span class="shift-pill">{{ $r['shift'] }}</span>
        </td>

        {{-- 10. Scrollable: Status --}}
        <td data-col="status">
            <span class="status-pill status-active">
                <span class="status-dot"></span>
                <span>Active Room</span>
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" style="text-align: center; padding: 3.5rem 1rem; color: #94A3B8;">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                <div style="font-weight: 700; font-size: 0.95rem; color: #475569;">No room allocations found</div>
            </div>
        </td>
    </tr>
@endforelse
