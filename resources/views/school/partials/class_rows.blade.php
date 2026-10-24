@forelse($classes as $c)
    <tr class="academic-row class-row"
        data-id="{{ $c['id'] }}"
        data-code="{{ $c['code'] }}"
        data-name="{{ strtolower($c['name'] . ' ' . $c['name_km']) }}"
        data-grade="{{ $c['grade_name'] }}"
        data-grade-id="{{ $c['grade_id'] }}"
        data-room="{{ $c['room'] }}"
        data-room-km="{{ $c['room_km'] }}"
        data-teacher="{{ $c['teacher'] }}"
        data-teacher-km="{{ $c['teacher_km'] }}"
        data-phone="{{ $c['phone'] }}"
        data-shift="{{ $c['shift_name'] }}"
        data-shift-id="{{ $c['shift_id'] }}"
        data-capacity="{{ $c['capacity'] }}"
        data-status="{{ $c['status'] }}"
        data-desc="{{ $c['description'] }}"
        data-desc-km="{{ $c['description_km'] }}">
        
        {{-- 1. Pinned: Checkbox --}}
        <td class="sticky-col-select" data-col="select">
            <input type="checkbox" class="row-checkbox class-row-checkbox" value="{{ $c['id'] }}" style="cursor: pointer;">
        </td>

        {{-- 2. Pinned: Class Code --}}
        <td class="sticky-col-code" data-col="code">
            <span class="id-code-badge" style="font-size: 0.85rem; font-weight: 800; cursor: pointer;" onclick="viewClassDetails('{{ $c['id'] }}')">
                {{ $c['code'] }}
            </span>
        </td>

        {{-- 3. Pinned: Class Name + Inline Context Menu Action (Meta Content List Concept) --}}
        <td class="sticky-col-student" data-col="class_name">
            <div class="entity-cell-layout">
                <div class="entity-cell-main">
                    <div class="class-icon-avatar">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0066FF" stroke-width="2.2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
                    </div>
                    <div class="entity-cell-meta">
                        <div class="entity-name-en">{{ $c['name'] }} <span class="grade-inline-pill">{{ $c['grade_name'] }}</span></div>
                        <div class="entity-name-km">{{ $c['name_km'] }}</div>
                    </div>
                </div>

                <!-- Action button as Context Menu (Meta Content List Concept) -->
                <button type="button" class="btn-context-menu" title="{{ transLabel('common.options', 'Options') }}"
                        onclick="openClassContextMenu(event, '{{ $c['id'] }}', '{{ $c['code'] }}', '{{ addslashes($c['name']) }}', '{{ addslashes($c['name_km']) }}', '{{ $c['grade_id'] }}', '{{ addslashes($c['grade_name']) }}', '{{ addslashes($c['room']) }}', '{{ addslashes($c['room_km']) }}', '{{ addslashes($c['teacher']) }}', '{{ addslashes($c['teacher_km']) }}', '{{ $c['phone'] }}', '{{ $c['capacity'] }}', '{{ $c['shift_id'] }}', '{{ $c['status'] }}', '{{ addslashes($c['description']) }}', '{{ addslashes($c['description_km']) }}')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="1.5"></circle>
                        <circle cx="6" cy="12" r="1.5"></circle>
                        <circle cx="18" cy="12" r="1.5"></circle>
                    </svg>
                </button>
            </div>
        </td>

        {{-- 4. Scrollable: Homeroom Teacher --}}
        <td data-col="teacher">
            <div class="teacher-meta-cell">
                <div class="teacher-name-title">{{ $c['teacher'] }}</div>
                @if($c['teacher_km'])
                    <div style="font-size: 0.725rem; color: #64748B; font-family: var(--font-khmer), 'Kantumruy Pro', sans-serif;">{{ $c['teacher_km'] }}</div>
                @endif
                <div class="teacher-phone-sub">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>{{ $c['phone'] }}</span>
                </div>
            </div>
        </td>

        {{-- 5. Scrollable: Grade Level --}}
        <td data-col="grade">
            <span style="font-weight: 700; color: #334155;">{{ $c['grade_name'] }}</span>
        </td>

        {{-- 6. Scrollable: Room / Building Location --}}
        <td data-col="room">
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                <div>
                    <div style="font-weight: 600; font-size: 0.8rem; color: #1E293B;">{{ $c['room'] }}</div>
                    @if($c['room_km'])
                        <div style="font-size: 0.7rem; color: #64748B; font-family: var(--font-khmer), 'Kantumruy Pro', sans-serif;">{{ $c['room_km'] }}</div>
                    @endif
                </div>
            </div>
        </td>

        {{-- 7. Scrollable: Operating Shift --}}
        <td data-col="shift">
            <span class="shift-pill">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>{{ $c['shift_name'] }}</span>
            </span>
        </td>

        {{-- 8. Scrollable: Enrolled Pupils (with capacity bar) --}}
        <td data-col="enrolled">
            <div style="display: flex; flex-direction: column; gap: 0.25rem; min-width: 100px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.775rem;">
                    <strong style="color: #0F172A;">{{ $c['enrolled'] }}</strong>
                    <span style="color: #64748B;">/ {{ $c['capacity'] }} Max</span>
                </div>
                @php
                    $occupancy = min(100, round(($c['enrolled'] / max(1, $c['capacity'])) * 100));
                    $barColor = $occupancy > 95 ? '#EF4444' : ($occupancy > 80 ? '#10B981' : '#0066FF');
                @endphp
                <div style="width: 100%; height: 5px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                    <div style="width: {{ $occupancy }}%; height: 100%; background: {{ $barColor }}; border-radius: 999px;"></div>
                </div>
            </div>
        </td>

        {{-- 9. Scrollable: Meal Subscribed Pupils --}}
        <td data-col="meal">
            <span class="meal-pill meal-regular">
                {{ $c['meal_subscribed'] }} Subscribed
            </span>
        </td>

        {{-- 10. Scrollable: Dietary Allergies --}}
        <td data-col="allergy">
            @if($c['allergies_count'] > 0)
                <span class="allergy-badge">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span>{{ $c['allergies_count'] }} Alerts</span>
                </span>
            @else
                <span style="color: #10B981; font-weight: 700; font-size: 0.75rem;">✓ None</span>
            @endif
        </td>

        {{-- 11. Scrollable: Status --}}
        <td data-col="status">
            <span class="status-pill {{ strtolower($c['status']) === 'active' ? 'status-active' : 'status-inactive' }}">
                <span class="status-dot"></span>
                <span>{{ ucfirst($c['status']) }}</span>
            </span>
        </td>

        {{-- 12. Scrollable: Notes & Instructions (Extra Column) --}}
        <td data-col="notes">
            <span style="font-size: 0.775rem; color: #64748B; max-width: 200px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                {{ $c['description'] ?: '-' }}
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="12" style="text-align: center; padding: 3.5rem 1rem; color: #94A3B8;">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.8"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/></svg>
                <div style="font-weight: 700; font-size: 0.95rem; color: #475569;">No classrooms found</div>
                <div style="font-size: 0.825rem; color: #94A3B8;">Click "+ Add Class" above to create your first classroom.</div>
            </div>
        </td>
    </tr>
@endforelse
