@forelse($students as $st)
    <tr class="student-row"
        data-id="{{ $st['id'] }}"
        data-code="{{ $st['code'] }}"
        data-name="{{ strtolower($st['name_en'] . ' ' . $st['name_km']) }}"
        data-grade="{{ $st['grade'] }}"
        data-class="{{ $st['class'] }}"
        data-meal="{{ $st['meal_plan'] }}"
        data-allergy="{{ $st['allergy'] ? strtolower($st['allergy']) : 'none' }}"
        data-status="{{ $st['status'] }}"
        data-parent="{{ $st['parent_name'] }}"
        data-phone="{{ $st['parent_phone'] }}"
        data-email="{{ $st['parent_email'] ?? '-' }}"
        data-gender="{{ $st['gender'] }}"
        data-shift="{{ $st['shift_name'] ?? 'Morning Shift' }}"
        data-dob="{{ $st['dob_formatted'] ?? '-' }}"
        data-avatar="{{ $st['avatar'] }}">
        
        {{-- 1. Pinned: Checkbox --}}
        <td class="sticky-col-select" data-col="select">
            <input type="checkbox" class="student-row-checkbox" value="{{ $st['id'] }}" style="cursor: pointer;">
        </td>

        {{-- 2. Pinned: Student ID --}}
        <td class="sticky-col-code" data-col="code">
            <a href="javascript:void(0)" class="id-code-badge" onclick="viewStudentDetails('{{ $st['code'] }}', '{{ addslashes($st['name_en']) }}', '{{ addslashes($st['name_km']) }}', '{{ $st['grade'] }}', '{{ $st['class'] }}', '{{ addslashes($st['parent_name']) }}', '{{ $st['parent_phone'] }}', '{{ $st['meal_plan'] }}', '{{ $st['allergy'] ?? 'None' }}', '{{ $st['status'] }}', '{{ $st['avatar'] }}')">
                {{ $st['code'] }}
            </a>
        </td>

        {{-- 3. Pinned: Student Name + Context Menu Action Button (Meta Content List Concept matching user screenshot) --}}
        <td class="sticky-col-student" data-col="student">
            <div class="student-cell-layout">
                <div class="student-cell-main">
                    <img src="{{ $st['avatar'] }}" alt="{{ $st['name_en'] }}" class="student-avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($st['name_en']) }}&background=0066FF&color=fff';">
                    <div class="student-cell-meta">
                        <div class="student-name-en">{{ $st['name_en'] }}</div>
                        <div class="student-name-km">{{ $st['name_km'] }}</div>
                    </div>
                </div>
                <!-- Action button as Context Menu (Meta Content List Concept) -->
                <button type="button" class="btn-context-menu" title="{{ transLabel('common.options', 'Options') }}"
                        onclick="openStudentContextMenu(event, '{{ $st['id'] }}', '{{ $st['code'] }}', '{{ addslashes($st['name_en']) }}', '{{ addslashes($st['name_km']) }}', '{{ $st['grade'] }}', '{{ $st['class'] }}', '{{ strtolower($st['gender']) }}', '{{ addslashes($st['parent_name']) }}', '{{ $st['parent_phone'] }}', '{{ $st['meal_plan'] }}', '{{ $st['allergy'] ?? 'None' }}', '{{ $st['status'] }}', '{{ $st['avatar'] }}')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="1.5"></circle>
                        <circle cx="6" cy="12" r="1.5"></circle>
                        <circle cx="18" cy="12" r="1.5"></circle>
                    </svg>
                </button>
            </div>
        </td>

        {{-- 4. Scrollable: Gender --}}
        <td data-col="gender">
            <span class="gender-pill {{ strtolower($st['gender']) === 'male' ? 'gender-male' : 'gender-female' }}">
                {{ $st['gender'] }}
            </span>
        </td>

        {{-- 5. Scrollable: Grade --}}
        <td data-col="grade">{{ $st['grade'] }}</td>

        {{-- 6. Scrollable: Class --}}
        <td data-col="class"><strong>{{ $st['class'] }}</strong></td>

        {{-- 7. Scrollable: Parent / Guardian --}}
        <td data-col="parent">
            <div class="parent-meta">
                <div class="parent-name">{{ $st['parent_name'] }}</div>
                <div class="parent-phone">{{ $st['parent_phone'] }}</div>
            </div>
        </td>

        {{-- 8. Scrollable: Parent Phone (Extra Column) --}}
        <td data-col="phone">
            <span style="font-family: monospace; font-size: 0.8rem; color: #334155;">{{ $st['parent_phone'] }}</span>
        </td>

        {{-- 9. Scrollable: Parent Email (Extra Column) --}}
        <td data-col="email">
            <span style="font-size: 0.775rem; color: #64748B;">{{ $st['parent_email'] ?? '-' }}</span>
        </td>

        {{-- 10. Scrollable: Meal Plan --}}
        <td data-col="meal">
            @php
                $mpLower = strtolower($st['meal_plan']);
                $pillClass = str_contains($mpLower, 'halal') ? 'meal-halal' : (str_contains($mpLower, 'veg') ? 'meal-vegetarian' : 'meal-regular');
            @endphp
            <span class="meal-pill {{ $pillClass }}">{{ $st['meal_plan'] }}</span>
        </td>

        {{-- 11. Scrollable: Allergy --}}
        <td data-col="allergy">
            @if($st['allergy'])
                <span class="allergy-badge">{{ $st['allergy'] }}</span>
            @else
                <span style="color: #94A3B8; font-weight: bold;">-</span>
            @endif
        </td>

        {{-- 12. Scrollable: Operating Shift (Extra Column) --}}
        <td data-col="shift">
            <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; color: #475569;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ $st['shift_name'] ?? 'Morning Shift' }}
            </span>
        </td>

        {{-- 13. Scrollable: Date of Birth & Age (Extra Column) --}}
        <td data-col="dob">
            <div style="line-height: 1.3;">
                <div style="font-size: 0.8rem; font-weight: 600; color: #1E293B;">{{ $st['dob_formatted'] ?? '-' }}</div>
                <div style="font-size: 0.725rem; color: #64748B;">{{ $st['age'] ?? '-' }}</div>
            </div>
        </td>

        {{-- 14. Scrollable: Status --}}
        <td data-col="status">
            @php
                $stLower = strtolower($st['status']);
                $statusClass = str_contains($stLower, 'alert') ? 'status-alert' : (str_contains($stLower, 'inactive') ? 'status-inactive' : 'status-active');
            @endphp
            <span class="status-pill {{ $statusClass }}">
                <span class="status-dot"></span>
                <span>{{ $st['status'] }}</span>
            </span>
        </td>

        {{-- 15. Scrollable: Enrolled Date (Extra Column) --}}
        <td data-col="enrolled">
            <span style="font-size: 0.775rem; color: #64748B;">{{ $st['enrolled_date'] ?? '-' }}</span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="15" style="text-align: center; padding: 3.5rem 1rem; color: #94A3B8;">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <div style="font-weight: 700; font-size: 0.95rem; color: #475569;">{{ transLabel('school.no_students_found', 'No student records found') }}</div>
                <div style="font-size: 0.825rem; color: #94A3B8;">{{ transLabel('school.try_adjusting_filters', 'Try adjusting your search or filter settings, or click "+ Add Student" to enroll.') }}</div>
            </div>
        </td>
    </tr>
@endforelse
