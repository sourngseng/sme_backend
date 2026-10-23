@extends('layouts.master_support')

@section('title', 'Customer Care & Support Portal')
@section('header_title', 'Helpdesk Tickets & Customer Disputes')

@section('content')
    <!-- Top Greeting & Date Filter Bar -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.65rem; font-weight: 900; color: #0f172a; margin: 0; line-height: 1.2; letter-spacing: -0.02em;">
                Welcome Customer Support, {{ auth()->user()->full_name }}!
            </h2>
            <p style="font-size: 0.875rem; color: #64748b; margin-top: 0.35rem; margin-bottom: 0;">
                Handle inquiries from parents and schools, resolve delivery disputes, and maintain high customer satisfaction.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <x-dashboard.date-filter
                :active-range="$activeRange ?? 'today'"
                :active-label="$activeLabel ?? 'Today (Oct 22, 2026)'"
                filter-id="supportDateFilter"
                on-select="window.applySupportDateFilter"
            />
        </div>
    </div>

    <!-- 4 Key Insights Metric Cards (Meta-Style Scrollable Rail on Mobile) -->
    <div class="insights-track-container" style="margin-bottom: 1.5rem;">
        <div class="insights-fade-edge fade-left"></div>
        <div class="insights-fade-edge fade-right"></div>

        <div class="portal-kpi-grid">
            <!-- 1. Open Tickets -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Open Tickets</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #f0fdfa; display: flex; align-items: center; justify-content: center; color: #0d9488;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                </div>
                <div id="support_kpi_open" style="font-size: 1.85rem; font-weight: 900; color: #0d9488; line-height: 1.1;">
                    {{ $metrics['open_tickets'] ?? '11 Open' }}
                </div>
                <div style="font-size: 0.75rem; color: #d97706; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>2 urgent (dietary dispute)</span>
                </div>
            </div>

            <!-- 2. Resolved Tickets -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Resolved Cases</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                </div>
                <div id="support_kpi_resolved" style="font-size: 1.85rem; font-weight: 900; color: #16a34a; line-height: 1.1;">
                    {{ $metrics['resolved_tickets'] ?? '34 Resolved' }}
                </div>
                <div style="font-size: 0.75rem; color: #16a34a; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Closed with parent signoff</span>
                </div>
            </div>

            <!-- 3. Avg Resolution Time -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Avg Resolution</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
                <div id="support_kpi_time" style="font-size: 1.85rem; font-weight: 900; color: #0f172a; line-height: 1.1;">
                    {{ $metrics['avg_resolution_time'] ?? '18 Mins' }}
                </div>
                <div style="font-size: 0.75rem; color: #16a34a; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>-4 mins vs last week</span>
                </div>
            </div>

            <!-- 4. Customer CSAT -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Customer CSAT</span>
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #fffbeb; display: flex; align-items: center; justify-content: center; color: #d97706;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                </div>
                <div id="support_kpi_csat" style="font-size: 1.85rem; font-weight: 900; color: #d97706; line-height: 1.1;">
                    {{ $metrics['csat_score'] ?? '98.2%' }}
                </div>
                <div style="font-size: 0.75rem; color: #16a34a; font-weight: 700; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.25rem;">
                    <span>Positive feedback rating</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Helpdesk Live Queue Section -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Helpdesk Queue & Ticket Inquiries</h3>
                <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.2rem; margin-bottom: 0;">Inquiries submitted by parents, schools, and drivers for immediate assistance.</p>
            </div>
            <span style="font-size: 0.75rem; font-weight: 700; color: #0d9488; background: #f0fdfa; padding: 0.35rem 0.75rem; border-radius: 999px;">
                ● Live Agent Response Active
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #ea580c; text-transform: uppercase;">Ticket #TK-842 • Urgent</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">Nut allergy confirmation for Keo Sopheak</div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">Parent requested kitchen verification • 12 mins ago</div>
            </div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #2563eb; text-transform: uppercase;">Ticket #TK-841 • In Progress</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">Bakong topup transaction status query</div>
                <div style="font-size: 0.8rem; color: #16a34a; font-weight: 600; margin-top: 0.25rem;">✓ Payment confirmed on ledger • Response drafted</div>
            </div>
        </div>
    </div>

    <!-- Support Date Filter AJAX Script -->
    <script>
        window.applySupportDateFilter = function(range, label, startDate, endDate) {
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
                    const mOpen = document.getElementById('support_kpi_open');
                    if (mOpen && data.metrics.open_tickets) mOpen.textContent = data.metrics.open_tickets;

                    const mResolved = document.getElementById('support_kpi_resolved');
                    if (mResolved && data.metrics.resolved_tickets) mResolved.textContent = data.metrics.resolved_tickets;

                    const mTime = document.getElementById('support_kpi_time');
                    if (mTime && data.metrics.avg_resolution_time) mTime.textContent = data.metrics.avg_resolution_time;

                    const mCsat = document.getElementById('support_kpi_csat');
                    if (mCsat && data.metrics.csat_score) mCsat.textContent = data.metrics.csat_score;
                }

                let cleanUrl = window.location.pathname + '?range=' + encodeURIComponent(range);
                if (startDate && endDate) {
                    cleanUrl += '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                }
                window.history.pushState({ range, startDate, endDate }, '', cleanUrl);

                if (window.Toast) {
                    window.Toast.success('Support helpdesk filtered: ' + (data.activeLabel || label));
                }
            })
            .catch(err => {
                console.error('Support date filter error:', err);
                if (window.Toast) window.Toast.error('Could not apply support date filter.');
            });
        };
    </script>
@endsection
