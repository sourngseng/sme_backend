@extends('layouts.master_parent')

@section('title', 'Parent & Family Portal')
@section('header_title', 'Family Nutrition & Meal Ordering')

@section('content')
    <!-- Top Greeting & Date Filter Bar -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.65rem; font-weight: 900; color: #0f172a; margin: 0; line-height: 1.2; letter-spacing: -0.02em;">
                Welcome Parent, {{ auth()->user()->full_name }}!
            </h2>
            <p style="font-size: 0.875rem; color: #64748b; margin-top: 0.35rem; margin-bottom: 0;">
                Monitor your children's meal plans, review nutrition intake, and top up your family meal wallet.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <x-dashboard.date-filter
                :active-range="$activeRange ?? 'today'"
                :active-label="$activeLabel ?? 'Today (Oct 22, 2026)'"
                filter-id="parentDateFilter"
                on-select="window.applyParentDateFilter"
            />
        </div>
    </div>

    <!-- 4 Key Insights Metric Cards (Meta-Style Scrollable Rail on Mobile) -->
    <div class="insights-track-container" style="margin-bottom: 1.5rem;">
        <div class="insights-fade-edge fade-left"></div>
        <div class="insights-fade-edge fade-right"></div>

        <div class="portal-kpi-grid">
            <!-- 1. Enrolled Children -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Enrolled Children</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #fff1f2; display: flex; align-items: center; justify-content: center; color: #e11d48;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                    </div>
                </div>
                <div id="parent_kpi_children" style="font-size: 1.85rem; font-weight: 900; color: #e11d48; line-height: 1.1;">
                    {{ $metrics['enrolled_children'] ?? '2 Students' }}
                </div>
                <div style="font-size: 0.75rem; color: #16a34a; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Angkor Primary School</span>
                </div>
            </div>

            <!-- 2. Wallet Balance -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Family Wallet</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                </div>
                <div id="parent_kpi_wallet" style="font-size: 1.85rem; font-weight: 900; color: #0f172a; line-height: 1.1;">
                    {{ $metrics['wallet_balance'] ?? '$45.50' }}
                </div>
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Settled via Bakong KHQR</span>
                </div>
            </div>

            <!-- 3. Meals Received -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Meals Received</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                </div>
                <div id="parent_kpi_meals" style="font-size: 1.85rem; font-weight: 900; color: #16a34a; line-height: 1.1;">
                    {{ $metrics['meals_received'] ?? '2 Meals' }}
                </div>
                <div style="font-size: 0.75rem; color: #16a34a; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Claimed at campus cafeteria</span>
                </div>
            </div>

            <!-- 4. Nutrition Score -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Nutrition Balance</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #faf5ff; display: flex; align-items: center; justify-content: center; color: #9333ea;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                </div>
                <div id="parent_kpi_nutrition" style="font-size: 1.85rem; font-weight: 900; color: #9333ea; line-height: 1.1;">
                    {{ $metrics['nutrition_score'] ?? '96%' }}
                </div>
                <div style="font-size: 0.75rem; color: #9333ea; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Optimal green & protein index</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Family Meal Plans Section -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Family Meal Plans & History</h3>
                <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.2rem; margin-bottom: 0;">Manage allergen preferences, select upcoming lunch menus, and view consumption statements.</p>
            </div>
            <span style="font-size: 0.75rem; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 0.35rem 0.75rem; border-radius: 999px;">
                ● Active Plan: Weekly Campus Lunch
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Child 1: Sok Pisey (Grade 4)</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">Chicken Rice Bowl • Verified</div>
                <div style="font-size: 0.8rem; color: #16a34a; font-weight: 600; margin-top: 0.25rem;">✓ No Allergens Flagged • Dropped 11:30 AM</div>
            </div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Child 2: Sok Dara (Grade 2)</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">Fried Noodle with Greens</div>
                <div style="font-size: 0.8rem; color: #ea580c; font-weight: 600; margin-top: 0.25rem;">⚠️ Peanut-Free Verified • Dropped 11:30 AM</div>
            </div>
        </div>
    </div>

    <!-- Parent Date Filter AJAX Script -->
    <script>
        window.applyParentDateFilter = function(range, label, startDate, endDate) {
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
                    const mChild = document.getElementById('parent_kpi_children');
                    if (mChild && data.metrics.enrolled_children) mChild.textContent = data.metrics.enrolled_children;

                    const mWallet = document.getElementById('parent_kpi_wallet');
                    if (mWallet && data.metrics.wallet_balance) mWallet.textContent = data.metrics.wallet_balance;

                    const mMeals = document.getElementById('parent_kpi_meals');
                    if (mMeals && data.metrics.meals_received) mMeals.textContent = data.metrics.meals_received;

                    const mNutr = document.getElementById('parent_kpi_nutrition');
                    if (mNutr && data.metrics.nutrition_score) mNutr.textContent = data.metrics.nutrition_score;
                }

                let cleanUrl = window.location.pathname + '?range=' + encodeURIComponent(range);
                if (startDate && endDate) {
                    cleanUrl += '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                }
                window.history.pushState({ range, startDate, endDate }, '', cleanUrl);

                if (window.Toast) {
                    window.Toast.success('Family portal filtered: ' + (data.activeLabel || label));
                }
            })
            .catch(err => {
                console.error('Parent date filter error:', err);
                if (window.Toast) window.Toast.error('Could not apply parent date filter.');
            });
        };
    </script>
@endsection
