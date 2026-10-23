@extends('layouts.master_school_admin')

@section('title', 'School Portal')
@section('header_title', 'School Meal Coordination Portal')

@section('content')
    <!-- Top Greeting & Date Filter Bar -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.65rem; font-weight: 900; color: #0f172a; margin: 0; line-height: 1.2; letter-spacing: -0.02em;">
                Welcome to School Administration, {{ auth()->user()->full_name }}!
            </h2>
            <p style="font-size: 0.875rem; color: #64748b; margin-top: 0.35rem; margin-bottom: 0;">
                Manage student meal enrollment, monitor daily deliveries, and oversee dietary safety alerts.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <x-dashboard.date-filter
                :active-range="$activeRange ?? 'today'"
                :active-label="$activeLabel ?? 'Today (Oct 22, 2026)'"
                filter-id="schoolDateFilter"
                on-select="window.applySchoolDateFilter"
            />
        </div>
    </div>

    <!-- 4 Key Insights Metric Cards (Meta-Style Scrollable Rail on Mobile) -->
    <div class="insights-track-container" style="margin-bottom: 1.5rem;">
        <div class="insights-fade-edge fade-left"></div>
        <div class="insights-fade-edge fade-right"></div>

        <div class="portal-kpi-grid">
            <!-- 1. Enrolled Students -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Enrolled Students</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                </div>
                <div id="school_kpi_enrolled" style="font-size: 1.85rem; font-weight: 900; color: #0f172a; line-height: 1.1;">
                    {{ $metrics['enrolled_students'] ?? '1,240' }}
                </div>
                <div style="font-size: 0.75rem; color: #10b981; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Active campus roster</span>
                </div>
            </div>

            <!-- 2. Meals Ordered -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Meals Ordered</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    </div>
                </div>
                <div id="school_kpi_ordered" style="font-size: 1.85rem; font-weight: 900; color: #0f172a; line-height: 1.1;">
                    {{ $metrics['meals_ordered'] ?? '1,080' }}
                </div>
                <div style="font-size: 0.75rem; color: #16a34a; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Ordered via parent portal</span>
                </div>
            </div>

            <!-- 3. Dietary Alerts -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Dietary Alerts</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #fff7ed; display: flex; align-items: center; justify-content: center; color: #ea580c;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                </div>
                <div id="school_kpi_dietary" style="font-size: 1.85rem; font-weight: 900; color: #ea580c; line-height: 1.1;">
                    {{ $metrics['dietary_alerts'] ?? '16' }}
                </div>
                <div style="font-size: 0.75rem; color: #ea580c; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Allergen flags monitored</span>
                </div>
            </div>

            <!-- 4. Attendance Rate -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Attendance Rate</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #faf5ff; display: flex; align-items: center; justify-content: center; color: #9333ea;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                </div>
                <div id="school_kpi_attendance" style="font-size: 1.85rem; font-weight: 900; color: #0f172a; line-height: 1.1;">
                    {{ $metrics['attendance_rate'] ?? '94.2%' }}
                </div>
                <div style="font-size: 0.75rem; color: #9333ea; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Verified meal recipients</span>
                </div>
            </div>
        </div>
    </div>

    <!-- School Roster & Meal Drops Section -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">School Roster & Meal Verification</h3>
                <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.2rem; margin-bottom: 0;">Coordinating daily breakfast and lunch deliveries with certified partner kitchens.</p>
            </div>
            <span style="font-size: 0.75rem; font-weight: 700; color: #16a34a; background: #f0fdf4; padding: 0.35rem 0.75rem; border-radius: 999px;">
                ● 2 Kitchen Dispatches On Schedule
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Breakfast Drop</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">07:30 AM – 08:15 AM</div>
                <div style="font-size: 0.8rem; color: #16a34a; font-weight: 600; margin-top: 0.25rem;">✓ 420 Meals Delivered & Verified</div>
            </div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Lunch Drop</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">11:15 AM – 12:00 PM</div>
                <div style="font-size: 0.8rem; color: #2563eb; font-weight: 600; margin-top: 0.25rem;">🚚 In Transit • 660 Meals</div>
            </div>
        </div>
    </div>

    <!-- School Date Filter AJAX Script -->
    <script>
        window.applySchoolDateFilter = function(range, label, startDate, endDate) {
            let url = window.location.pathname + '?range=' + encodeURIComponent(range) + '&format=json';
            if (startDate && endDate) {
                url += '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
            }

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                if (data.metrics) {
                    const mEnrolled = document.getElementById('school_kpi_enrolled');
                    if (mEnrolled && data.metrics.enrolled_students) mEnrolled.textContent = data.metrics.enrolled_students;

                    const mOrdered = document.getElementById('school_kpi_ordered');
                    if (mOrdered && data.metrics.meals_ordered) mOrdered.textContent = data.metrics.meals_ordered;

                    const mDietary = document.getElementById('school_kpi_dietary');
                    if (mDietary && data.metrics.dietary_alerts) mDietary.textContent = data.metrics.dietary_alerts;

                    const mAttendance = document.getElementById('school_kpi_attendance');
                    if (mAttendance && data.metrics.attendance_rate) mAttendance.textContent = data.metrics.attendance_rate;
                }

                let cleanUrl = window.location.pathname + '?range=' + encodeURIComponent(range);
                if (startDate && endDate) {
                    cleanUrl += '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                }
                window.history.pushState({ range, startDate, endDate }, '', cleanUrl);

                if (window.Toast) {
                    window.Toast.success('School portal filtered: ' + (data.activeLabel || label));
                }
            })
            .catch(err => {
                console.error('School date filter error:', err);
                if (window.Toast) window.Toast.error('Could not apply school date filter.');
            });
        };
    </script>
@endsection
