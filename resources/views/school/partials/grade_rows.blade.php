@forelse($grades as $g)
    <tr class="academic-row grade-row"
        data-id="{{ $g['id'] }}"
        data-code="{{ $g['code'] }}"
        data-name="{{ strtolower($g['name'] . ' ' . $g['name_km']) }}"
        data-stage="{{ $g['academic_stage'] }}"
        data-level="{{ $g['level_number'] }}"
        data-status="{{ $g['status'] }}"
        data-desc="{{ $g['description'] }}"
        data-desc-km="{{ $g['description_km'] }}">

        {{-- 1. Pinned: Checkbox --}}
        <td class="sticky-col-select" data-col="select">
            <input type="checkbox" class="row-checkbox grade-row-checkbox" value="{{ $g['id'] }}" style="cursor: pointer;">
        </td>

        {{-- 2. Pinned: Grade Code --}}
        <td class="sticky-col-code" data-col="code">
            <span class="id-code-badge" style="font-size: 0.85rem; font-weight: 800; cursor: pointer;" onclick="viewGradeDetails('{{ $g['id'] }}')">
                {{ $g['code'] }}
            </span>
        </td>

        {{-- 3. Pinned: Grade Name + Inline Context Menu Action (Meta Content List Concept) --}}
        <td class="sticky-col-student" data-col="grade_name">
            <div class="entity-cell-layout">
                <div class="entity-cell-main">
                    <div class="grade-badge-avatar">
                        L{{ $g['level_number'] }}
                    </div>
                    <div class="entity-cell-meta">
                        <div class="entity-name-en">{{ $g['name'] }}</div>
                        <div class="entity-name-km">{{ $g['name_km'] }}</div>
                    </div>
                </div>

                <!-- Action button as Context Menu (Meta Content List Concept) -->
                <button type="button" class="btn-context-menu" title="{{ transLabel('common.options', 'Options') }}"
                        onclick="openGradeContextMenu(event, '{{ $g['id'] }}', '{{ $g['code'] }}', '{{ addslashes($g['name']) }}', '{{ addslashes($g['name_km']) }}', '{{ $g['level_number'] }}', '{{ addslashes($g['academic_stage']) }}', '{{ addslashes($g['academic_stage_km'] ?? '') }}', '{{ $g['status'] }}', '{{ addslashes($g['description']) }}', '{{ addslashes($g['description_km']) }}')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="1.5"></circle>
                        <circle cx="6" cy="12" r="1.5"></circle>
                        <circle cx="18" cy="12" r="1.5"></circle>
                    </svg>
                </button>
            </div>
        </td>

        {{-- 4. Scrollable: Academic Stage --}}
        <td data-col="stage">
            <span class="stage-pill">
                {{ $g['academic_stage'] }}
            </span>
            @if(!empty($g['academic_stage_km']))
                <div style="font-size: 0.7rem; color: #64748B; font-family: var(--font-khmer), 'Kantumruy Pro', sans-serif;">{{ $g['academic_stage_km'] }}</div>
            @endif
        </td>

        {{-- 5. Scrollable: Active Class Sections --}}
        <td data-col="classes">
            <div style="font-weight: 700; color: #0066FF; font-size: 0.825rem;">
                {{ $g['class_count'] }} Sections
            </div>
            <div style="font-size: 0.725rem; color: #64748B;">({{ $g['class_codes'] }})</div>
        </td>

        {{-- 6. Scrollable: Total Enrolled Pupils --}}
        <td data-col="enrolled">
            <div style="font-weight: 800; font-size: 0.85rem; color: #0F172A;">{{ $g['enrolled'] }} Pupils</div>
        </td>

        {{-- 7. Scrollable: Meal Participation --}}
        <td data-col="meal_rate">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="meal-pill meal-halal" style="font-size: 0.75rem;">{{ $g['meal_pct'] }}% ({{ $g['meal_subscribed'] }})</span>
            </div>
        </td>

        {{-- 8. Scrollable: Allergy Alerts --}}
        <td data-col="allergies">
            @if($g['allergies_count'] > 0)
                <span class="allergy-badge">{{ $g['allergies_count'] }} Alerts</span>
            @else
                <span style="color: #10B981; font-weight: 700; font-size: 0.75rem;">✓ None</span>
            @endif
        </td>

        {{-- 9. Scrollable: Status --}}
        <td data-col="status">
            <span class="status-pill {{ strtolower($g['status']) === 'active' ? 'status-active' : 'status-inactive' }}">
                <span class="status-dot"></span>
                <span>{{ ucfirst($g['status']) }}</span>
            </span>
        </td>

        {{-- 10. Scrollable: Description / Focus --}}
        <td data-col="desc">
            <span style="font-size: 0.775rem; color: #64748B; max-width: 240px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                {{ $g['description'] ?: '-' }}
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" style="text-align: center; padding: 3.5rem 1rem; color: #94A3B8;">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <div style="font-weight: 700; font-size: 0.95rem; color: #475569;">No grade levels configured</div>
                <div style="font-size: 0.825rem; color: #94A3B8;">Click "+ Add Grade" above to register academic grade levels.</div>
            </div>
        </td>
    </tr>
@endforelse
