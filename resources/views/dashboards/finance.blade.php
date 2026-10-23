@extends('layouts.master_finance')

@section('title', 'Finance & Settlements')
@section('header_title', 'Finance & Merchant Settlements')

@section('content')
    <!-- Top Greeting & Date Filter Bar -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.65rem; font-weight: 900; color: #0f172a; margin: 0; line-height: 1.2; letter-spacing: -0.02em;">
                Welcome Finance Lead, {{ auth()->user()->full_name }}!
            </h2>
            <p style="font-size: 0.875rem; color: #64748b; margin-top: 0.35rem; margin-bottom: 0;">
                Monitor platform revenue, merchant escrow payouts, and driver disbursement ledgers.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <x-dashboard.date-filter
                :active-range="$activeRange ?? 'this_month'"
                :active-label="$activeLabel ?? 'Oct 1 – Oct 31, 2026'"
                filter-id="financeDateFilter"
                on-select="window.applyFinanceDateFilter"
            />
        </div>
    </div>

    <!-- 4 Key Insights Metric Cards (Meta-Style Scrollable Rail on Mobile) -->
    <div class="insights-track-container" style="margin-bottom: 1.5rem;">
        <div class="insights-fade-edge fade-left"></div>
        <div class="insights-fade-edge fade-right"></div>

        <div class="portal-kpi-grid">
            <!-- 1. Gross Volume -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Gross Platform Volume</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                </div>
                <div id="finance_kpi_gross" style="font-size: 1.85rem; font-weight: 900; color: #0f172a; line-height: 1.1;">
                    {{ $metrics['gross_volume'] ?? '$18,420' }}
                </div>
                <div style="font-size: 0.75rem; color: #10b981; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Total parent payments processed</span>
                </div>
            </div>

            <!-- 2. Settled Volume -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Settled Merchant Volume</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                </div>
                <div id="finance_kpi_settled" style="font-size: 1.85rem; font-weight: 900; color: #16a34a; line-height: 1.1;">
                    {{ $metrics['settled_volume'] ?? '$14,280' }}
                </div>
                <div style="font-size: 0.75rem; color: #16a34a; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Disbursed to partner kitchens</span>
                </div>
            </div>

            <!-- 3. Driver Payouts -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Driver Payouts</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #fff7ed; display: flex; align-items: center; justify-content: center; color: #ea580c;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                </div>
                <div id="finance_kpi_payouts" style="font-size: 1.85rem; font-weight: 900; color: #0f172a; line-height: 1.1;">
                    {{ $metrics['driver_payouts'] ?? '$2,140' }}
                </div>
                <div style="font-size: 0.75rem; color: #ea580c; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Delivery fees & route bonuses</span>
                </div>
            </div>

            <!-- 4. Escrow Buffer / Pending -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Escrow Buffer</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #faf5ff; display: flex; align-items: center; justify-content: center; color: #9333ea;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                </div>
                <div id="finance_kpi_escrow" style="font-size: 1.85rem; font-weight: 900; color: #9333ea; line-height: 1.1;">
                    {{ $metrics['pending_escrow'] ?? '$2,000' }}
                </div>
                <div style="font-size: 0.75rem; color: #9333ea; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Guaranteed holding buffer</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Settlements & Ledgers Section -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Settlements & Merchant Ledgers</h3>
                <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.2rem; margin-bottom: 0;">Automated reconciliations with Bakong KHQR, credit card gateways, and subsidy balances.</p>
            </div>
            <span style="font-size: 0.75rem; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 0.35rem 0.75rem; border-radius: 999px;">
                ● Auto-Reconciliation Active
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Next Payout Run</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">Today at 18:00 (Bakong ACH)</div>
                <div style="font-size: 0.8rem; color: #16a34a; font-weight: 600; margin-top: 0.25rem;">✓ 4 Partner Kitchens Verified</div>
            </div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Dispute Retention</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">$0.00 In Dispute</div>
                <div style="font-size: 0.8rem; color: #2563eb; font-weight: 600; margin-top: 0.25rem;">0 open chargeback tickets</div>
            </div>
        </div>
    </div>

    <!-- Finance Date Filter AJAX Script -->
    <script>
        window.applyFinanceDateFilter = function(range, label, startDate, endDate) {
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
                    const mGross = document.getElementById('finance_kpi_gross');
                    if (mGross && data.metrics.gross_volume) mGross.textContent = data.metrics.gross_volume;

                    const mSettled = document.getElementById('finance_kpi_settled');
                    if (mSettled && data.metrics.settled_volume) mSettled.textContent = data.metrics.settled_volume;

                    const mPayouts = document.getElementById('finance_kpi_payouts');
                    if (mPayouts && data.metrics.driver_payouts) mPayouts.textContent = data.metrics.driver_payouts;

                    const mEscrow = document.getElementById('finance_kpi_escrow');
                    if (mEscrow && data.metrics.pending_escrow) mEscrow.textContent = data.metrics.pending_escrow;
                }

                let cleanUrl = window.location.pathname + '?range=' + encodeURIComponent(range);
                if (startDate && endDate) {
                    cleanUrl += '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                }
                window.history.pushState({ range, startDate, endDate }, '', cleanUrl);

                if (window.Toast) {
                    window.Toast.success('Finance ledger filtered: ' + (data.activeLabel || label));
                }
            })
            .catch(err => {
                console.error('Finance date filter error:', err);
                if (window.Toast) window.Toast.error('Could not apply finance date filter.');
            });
        };
    </script>
@endsection
