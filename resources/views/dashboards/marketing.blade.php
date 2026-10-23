@extends('layouts.master_marketing')

@section('title', 'Marketing & Growth Portal')
@section('header_title', 'Campaigns & Promotion Performance')

@section('content')
    <!-- Top Greeting & Date Filter Bar -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.65rem; font-weight: 900; color: #0f172a; margin: 0; line-height: 1.2; letter-spacing: -0.02em;">
                Welcome Marketing Specialist, {{ auth()->user()->full_name }}!
            </h2>
            <p style="font-size: 0.875rem; color: #64748b; margin-top: 0.35rem; margin-bottom: 0;">
                Create seasonal meal discount vouchers, analyze promo campaigns, and track parent conversion rates.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <x-dashboard.date-filter
                :active-range="$activeRange ?? 'this_month'"
                :active-label="$activeLabel ?? 'Oct 1 – Oct 31, 2026'"
                filter-id="marketingDateFilter"
                on-select="window.applyMarketingDateFilter"
            />
        </div>
    </div>

    <!-- 4 Key Insights Metric Cards (Meta-Style Scrollable Rail on Mobile) -->
    <div class="insights-track-container" style="margin-bottom: 1.5rem;">
        <div class="insights-fade-edge fade-left"></div>
        <div class="insights-fade-edge fade-right"></div>

        <div class="portal-kpi-grid">
            <!-- 1. Active Campaigns -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Active Promotions</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #faf5ff; display: flex; align-items: center; justify-content: center; color: #7c3aed;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    </div>
                </div>
                <div id="marketing_kpi_campaigns" style="font-size: 1.85rem; font-weight: 900; color: #7c3aed; line-height: 1.1;">
                    {{ $metrics['campaigns'] ?? '5 Active' }}
                </div>
                <div style="font-size: 0.75rem; color: #16a34a; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Back to School 15% OFF active</span>
                </div>
            </div>

            <!-- 2. Vouchers Redeemed -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Vouchers Redeemed</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/></svg>
                    </div>
                </div>
                <div id="marketing_kpi_vouchers" style="font-size: 1.85rem; font-weight: 900; color: #0f172a; line-height: 1.1;">
                    {{ $metrics['vouchers_redeemed'] ?? '1,842' }}
                </div>
                <div style="font-size: 0.75rem; color: #2563eb; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Redeemed at checkout</span>
                </div>
            </div>

            <!-- 3. Promo Sales Attributed -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Promotional Sales</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                </div>
                <div id="marketing_kpi_sales" style="font-size: 1.85rem; font-weight: 900; color: #16a34a; line-height: 1.1;">
                    {{ $metrics['promo_sales'] ?? '$6,450' }}
                </div>
                <div style="font-size: 0.75rem; color: #16a34a; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>ROI: 4.8x on ad spend</span>
                </div>
            </div>

            <!-- 4. Conversion Rate -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Conversion Rate</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #fff7ed; display: flex; align-items: center; justify-content: center; color: #ea580c;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    </div>
                </div>
                <div id="marketing_kpi_conversion" style="font-size: 1.85rem; font-weight: 900; color: #ea580c; line-height: 1.1;">
                    {{ $metrics['conversion_rate'] ?? '24.8%' }}
                </div>
                <div style="font-size: 0.75rem; color: #ea580c; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Visitors converted to meal plans</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaigns & Promotion Performance Section -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Campaigns & Promotion Performance</h3>
                <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.2rem; margin-bottom: 0;">Manage referral bonuses, targeted school discount codes, and customer retention campaigns.</p>
            </div>
            <span style="font-size: 0.75rem; font-weight: 700; color: #7c3aed; background: #faf5ff; padding: 0.35rem 0.75rem; border-radius: 999px;">
                ● 3 Discount Codes Live
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">BACK2SCHOOL15</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">15% Off All Monthly Meal Passes</div>
                <div style="font-size: 0.8rem; color: #16a34a; font-weight: 600; margin-top: 0.25rem;">✓ 1,240 Redemptions • Ends Oct 31</div>
            </div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">SIEMREAPHEALTHY</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">Free Organic Salad Add-on</div>
                <div style="font-size: 0.8rem; color: #2563eb; font-weight: 600; margin-top: 0.25rem;">🥗 602 Redemptions • Active</div>
            </div>
        </div>
    </div>

    <!-- Marketing Date Filter AJAX Script -->
    <script>
        window.applyMarketingDateFilter = function(range, label, startDate, endDate) {
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
                    const mCamp = document.getElementById('marketing_kpi_campaigns');
                    if (mCamp && data.metrics.campaigns) mCamp.textContent = data.metrics.campaigns;

                    const mVouchers = document.getElementById('marketing_kpi_vouchers');
                    if (mVouchers && data.metrics.vouchers_redeemed) mVouchers.textContent = data.metrics.vouchers_redeemed;

                    const mSales = document.getElementById('marketing_kpi_sales');
                    if (mSales && data.metrics.promo_sales) mSales.textContent = data.metrics.promo_sales;

                    const mConv = document.getElementById('marketing_kpi_conversion');
                    if (mConv && data.metrics.conversion_rate) mConv.textContent = data.metrics.conversion_rate;
                }

                let cleanUrl = window.location.pathname + '?range=' + encodeURIComponent(range);
                if (startDate && endDate) {
                    cleanUrl += '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                }
                window.history.pushState({ range, startDate, endDate }, '', cleanUrl);

                if (window.Toast) {
                    window.Toast.success('Marketing performance filtered: ' + (data.activeLabel || label));
                }
            })
            .catch(err => {
                console.error('Marketing date filter error:', err);
                if (window.Toast) window.Toast.error('Could not apply marketing date filter.');
            });
        };
    </script>
@endsection
