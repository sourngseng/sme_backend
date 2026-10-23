@extends('layouts.master_student')

@section('title', 'Student Portal')
@section('header_title', 'Student Meal Pass & Nutrition')

@section('content')
    <!-- Top Greeting & Date Filter Bar -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.65rem; font-weight: 900; color: #0f172a; margin: 0; line-height: 1.2; letter-spacing: -0.02em;">
                Welcome Student, {{ auth()->user()->full_name }}!
            </h2>
            <p style="font-size: 0.875rem; color: #64748b; margin-top: 0.35rem; margin-bottom: 0;">
                Show your digital meal token, track nutrition scores, and collect healthy clean-plate badges.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <x-dashboard.date-filter
                :active-range="$activeRange ?? 'today'"
                :active-label="$activeLabel ?? 'Today (Oct 22, 2026)'"
                filter-id="studentDateFilter"
                on-select="window.applyStudentDateFilter"
            />
        </div>
    </div>

    <!-- 4 Key Insights Metric Cards (Meta-Style Scrollable Rail on Mobile) -->
    <div class="insights-track-container" style="margin-bottom: 1.5rem;">
        <div class="insights-fade-edge fade-left"></div>
        <div class="insights-fade-edge fade-right"></div>

        <div class="portal-kpi-grid">
            <!-- 1. Meal Status -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Meal Status</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                </div>
                <div id="student_kpi_status" style="font-size: 1.55rem; font-weight: 900; color: #16a34a; line-height: 1.2;">
                    {{ $metrics['meal_status'] ?? 'Ready to Claim' }}
                </div>
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Counter B • Dropoff: 11:30 AM</span>
                </div>
            </div>

            <!-- 2. Nutrition Score -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Nutrition Score</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #eef2ff; display: flex; align-items: center; justify-content: center; color: #4f46e5;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                </div>
                <div id="student_kpi_nutrition" style="font-size: 1.85rem; font-weight: 900; color: #4f46e5; line-height: 1.1;">
                    {{ $metrics['weekly_nutrition'] ?? '96 / 100' }}
                </div>
                <div style="font-size: 0.75rem; color: #16a34a; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Balanced vitamins & greens</span>
                </div>
            </div>

            <!-- 3. Reward Points -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Reward Points</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #fffbeb; display: flex; align-items: center; justify-content: center; color: #d97706;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                </div>
                <div id="student_kpi_points" style="font-size: 1.85rem; font-weight: 900; color: #d97706; line-height: 1.1;">
                    {{ $metrics['reward_points'] ?? '180 Pts' }}
                </div>
                <div style="font-size: 0.75rem; color: #d97706; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Clean plate badge bonus</span>
                </div>
            </div>

            <!-- 4. Healthy Meal Streak -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Meal Streak</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #fff7ed; display: flex; align-items: center; justify-content: center; color: #ea580c;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
                    </div>
                </div>
                <div id="student_kpi_streak" style="font-size: 1.85rem; font-weight: 900; color: #ea580c; line-height: 1.1;">
                    {{ $metrics['streak_days'] ?? '12 Days' }}
                </div>
                <div style="font-size: 0.75rem; color: #ea580c; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Daily hot lunch completed</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Meal Pass QR Section -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Digital Meal Token & Campus Menu</h3>
                <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.2rem; margin-bottom: 0;">Present your digital token QR to cafeteria kitchen staff for immediate tray handover.</p>
            </div>
            <button type="button" onclick="window.Toast.success('Digital meal QR pass refreshed.')" style="background: #2563eb; color: #ffffff; border: none; border-radius: 10px; padding: 0.5rem 0.95rem; font-size: 0.8125rem; font-weight: 700; cursor: pointer; font-family: inherit;">
                Show Token QR
            </button>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <div style="width: 60px; height: 60px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                🍱
            </div>
            <div>
                <div style="font-size: 1.05rem; font-weight: 800; color: #0f172a;">Today's Chef Selection: Chicken Rice Bowl + Clear Soup</div>
                <div style="font-size: 0.825rem; color: #64748b; margin-top: 0.25rem;">Prepared by Khmer Kitchen • Freshly Steamed • 100% Nut Free</div>
            </div>
        </div>
    </div>

    <!-- Student Date Filter AJAX Script -->
    <script>
        window.applyStudentDateFilter = function(range, label, startDate, endDate) {
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
                    const mStatus = document.getElementById('student_kpi_status');
                    if (mStatus && data.metrics.meal_status) mStatus.textContent = data.metrics.meal_status;

                    const mNutr = document.getElementById('student_kpi_nutrition');
                    if (mNutr && data.metrics.weekly_nutrition) mNutr.textContent = data.metrics.weekly_nutrition;

                    const mPoints = document.getElementById('student_kpi_points');
                    if (mPoints && data.metrics.reward_points) mPoints.textContent = data.metrics.reward_points;

                    const mStreak = document.getElementById('student_kpi_streak');
                    if (mStreak && data.metrics.streak_days) mStreak.textContent = data.metrics.streak_days;
                }

                let cleanUrl = window.location.pathname + '?range=' + encodeURIComponent(range);
                if (startDate && endDate) {
                    cleanUrl += '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                }
                window.history.pushState({ range, startDate, endDate }, '', cleanUrl);

                if (window.Toast) {
                    window.Toast.success('Student pass filtered: ' + (data.activeLabel || label));
                }
            })
            .catch(err => {
                console.error('Student date filter error:', err);
                if (window.Toast) window.Toast.error('Could not apply student date filter.');
            });
        };
    </script>
@endsection
