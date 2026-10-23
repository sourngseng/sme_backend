@extends('layouts.master_driver')

@section('title', 'Driver Dispatch')
@section('header_title', 'Delivery Dispatch Dashboard')

@section('content')
    <!-- Top Greeting & Date Filter Bar -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.65rem; font-weight: 900; color: #0f172a; margin: 0; line-height: 1.2; letter-spacing: -0.02em;">
                Welcome Driver, {{ auth()->user()->full_name }}!
            </h2>
            <p style="font-size: 0.875rem; color: #64748b; margin-top: 0.35rem; margin-bottom: 0;">
                View assigned delivery routes, kitchen pickup manifests, and school dropoff schedules.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <x-dashboard.date-filter
                :active-range="$activeRange ?? 'today'"
                :active-label="$activeLabel ?? 'Today (Oct 22, 2026)'"
                filter-id="driverDateFilter"
                on-select="window.applyDriverDateFilter"
            />
        </div>
    </div>

    <!-- 4 Key Insights Metric Cards (Meta-Style Scrollable Rail on Mobile) -->
    <div class="insights-track-container" style="margin-bottom: 1.5rem;">
        <div class="insights-fade-edge fade-left"></div>
        <div class="insights-fade-edge fade-right"></div>

        <div class="portal-kpi-grid">
            <!-- 1. Assigned Routes -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Assigned Routes</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                    </div>
                </div>
                <div id="driver_kpi_routes" style="font-size: 1.85rem; font-weight: 900; color: #0f172a; line-height: 1.1;">
                    {{ $metrics['assigned_routes'] ?? '4 Routes' }}
                </div>
                <div style="font-size: 0.75rem; color: #2563eb; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Optimized multi-drop route</span>
                </div>
            </div>

            <!-- 2. School Stops -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Delivery Stops</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                </div>
                <div id="driver_kpi_stops" style="font-size: 1.85rem; font-weight: 900; color: #16a34a; line-height: 1.1;">
                    {{ $metrics['stops_count'] ?? '12 Schools' }}
                </div>
                <div style="font-size: 0.75rem; color: #16a34a; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Verified campus drop points</span>
                </div>
            </div>

            <!-- 3. Meals Loaded -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Meals Loaded</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #fff7ed; display: flex; align-items: center; justify-content: center; color: #ea580c;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 8 3 3 3-3"/><path d="m9 12 3 3 3-3"/></svg>
                    </div>
                </div>
                <div id="driver_kpi_meals" style="font-size: 1.85rem; font-weight: 900; color: #0f172a; line-height: 1.1;">
                    {{ $metrics['meals_loaded'] ?? '320 Meals' }}
                </div>
                <div style="font-size: 0.75rem; color: #ea580c; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Insulated thermal boxes</span>
                </div>
            </div>

            <!-- 4. On-Time Delivery Rate -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">On-Time Rate</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #faf5ff; display: flex; align-items: center; justify-content: center; color: #9333ea;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
                <div id="driver_kpi_ontime" style="font-size: 1.85rem; font-weight: 900; color: #9333ea; line-height: 1.1;">
                    {{ $metrics['on_time_pct'] ?? '98.5%' }}
                </div>
                <div style="font-size: 0.75rem; color: #9333ea; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>GPS confirmed delivery</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Route Manifests Section -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Route Manifests & Drops</h3>
                <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.2rem; margin-bottom: 0;">Morning delivery schedule and school cafeteria drop-off verifications.</p>
            </div>
            <span style="font-size: 0.75rem; font-weight: 700; color: #16a34a; background: #f0fdf4; padding: 0.35rem 0.75rem; border-radius: 999px;">
                ● Live GPS Tracking Active
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Route #SR-04 (Siem Reap North)</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">Angkor Primary ➔ Wat Bo School</div>
                <div style="font-size: 0.8rem; color: #16a34a; font-weight: 600; margin-top: 0.25rem;">✓ 180 Meals Delivered • Drop 1 Complete</div>
            </div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Next Stop: Siem Reap High</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">ETA: 11:25 AM (140 Meals)</div>
                <div style="font-size: 0.8rem; color: #2563eb; font-weight: 600; margin-top: 0.25rem;">🚚 In Route • 1.4 km remaining</div>
            </div>
        </div>
    </div>

    <!-- Driver Date Filter AJAX Script -->
    <script>
        window.applyDriverDateFilter = function(range, label, startDate, endDate) {
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
                    const mRoutes = document.getElementById('driver_kpi_routes');
                    if (mRoutes && data.metrics.assigned_routes) mRoutes.textContent = data.metrics.assigned_routes;

                    const mStops = document.getElementById('driver_kpi_stops');
                    if (mStops && data.metrics.stops_count) mStops.textContent = data.metrics.stops_count;

                    const mMeals = document.getElementById('driver_kpi_meals');
                    if (mMeals && data.metrics.meals_loaded) mMeals.textContent = data.metrics.meals_loaded;

                    const mOnTime = document.getElementById('driver_kpi_ontime');
                    if (mOnTime && data.metrics.on_time_pct) mOnTime.textContent = data.metrics.on_time_pct;
                }

                let cleanUrl = window.location.pathname + '?range=' + encodeURIComponent(range);
                if (startDate && endDate) {
                    cleanUrl += '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                }
                window.history.pushState({ range, startDate, endDate }, '', cleanUrl);

                if (window.Toast) {
                    window.Toast.success('Dispatch routes filtered: ' + (data.activeLabel || label));
                }
            })
            .catch(err => {
                console.error('Driver date filter error:', err);
                if (window.Toast) window.Toast.error('Could not apply driver date filter.');
            });
        };
    </script>
@endsection
