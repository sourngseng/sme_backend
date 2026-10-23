@extends('layouts.master_super_admin')

@section('title', 'Admin Overview')

@push('styles')
<style>
    /* Meta (Facebook) Insights Scrollable Rail Styling */
    .dashboard-rail-section,
    .insights-wrapper {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .insights-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        padding: 0 0.25rem;
    }

    .insights-title-box {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .insights-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.85rem;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.28rem 0.65rem;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .insights-pill-info {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        padding: 0.2rem 0.65rem;
        border-radius: 9999px;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .insights-pill-info .pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10B981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }

    /* Meta Carousel Controls */
    .insights-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .insights-nav-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        padding: 0;
    }

    .insights-nav-btn:hover:not(:disabled) {
        background: #f8fafc;
        color: #1d4ed8;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
        transform: scale(1.06);
    }

    .insights-nav-btn:disabled,
    .insights-nav-btn.disabled {
        opacity: 0.35;
        cursor: not-allowed;
        pointer-events: none;
        box-shadow: none;
    }

    /* Track Container & Overflow Rail */
    .insights-track-container {
        position: relative;
    }

    .dashboard-scroll-track,
    .insights-scroll-track {
        display: flex;
        gap: 1.15rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        padding: 6px 4px 14px 4px;
        cursor: grab;
        user-select: none;
    }

    .dashboard-scroll-track::-webkit-scrollbar,
    .insights-scroll-track::-webkit-scrollbar {
        display: none;
    }

    .dashboard-scroll-track.is-dragging,
    .insights-scroll-track.is-dragging {
        cursor: grabbing;
        scroll-snap-type: none;
        scroll-behavior: auto;
    }

    /* Row 1: KPI Cards */
    .insights-scroll-track .kpi-card {
        flex: 0 0 236px;
        min-width: 236px;
        max-width: 236px;
        scroll-snap-align: start;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .insights-scroll-track .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.08), 0 4px 8px -2px rgba(15, 23, 42, 0.04);
        border-color: #cbd5e1;
    }

    /* Row 2: Metric / Graph Cards */
    .graphs-scroll-track .graph-rail-card {
        flex-shrink: 0;
        scroll-snap-align: start;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
    }

    .graphs-scroll-track .graph-rail-card:hover {
        transform: translateY(-2px);
    }

    .graphs-scroll-track .graph-rail-card > .dash-chart-card {
        height: 100%;
        box-sizing: border-box;
    }

    .graph-card-wide {
        flex: 0 0 540px;
        width: 540px;
        min-width: 480px;
    }

    .graph-card-medium {
        flex: 0 0 360px;
        width: 360px;
        min-width: 340px;
    }

    /* Row 3: List / Table Cards */
    .lists-scroll-track .list-rail-card {
        flex-shrink: 0;
        scroll-snap-align: start;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
    }

    .lists-scroll-track .list-rail-card:hover {
        transform: translateY(-2px);
    }

    .lists-scroll-track .list-rail-card > .dash-table-card {
        height: 100%;
        box-sizing: border-box;
    }

    .list-card-table {
        flex: 0 0 410px;
        width: 410px;
        min-width: 380px;
    }

    .list-card-wide {
        flex: 0 0 560px;
        width: 560px;
        min-width: 480px;
    }

    /* Dashboard Standard Card Headers */
    .dash-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.85rem;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .dash-card-header-main {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .dash-card-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.01em;
        margin: 0;
    }

    .dash-card-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    /* Edge Gradient Fade Masks */
    .insights-fade-edge {
        position: absolute;
        top: 0;
        bottom: 14px;
        width: 44px;
        pointer-events: none;
        z-index: 5;
        transition: opacity 0.25s ease;
    }

    .insights-fade-edge.fade-left {
        left: 0;
        background: linear-gradient(to right, rgba(248, 250, 252, 0.95), transparent);
        opacity: 0;
    }

    .insights-fade-edge.fade-right {
        right: 0;
        background: linear-gradient(to left, rgba(248, 250, 252, 0.95), transparent);
        opacity: 1;
    }

    /* Row 4: Operational Gauges & Live Stream Scrollable Rail */
    .gauges-scroll-track .gauge-rail-card {
        flex-shrink: 0;
        scroll-snap-align: start;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
    }

    .gauges-scroll-track .gauge-rail-card:hover {
        transform: translateY(-2px);
    }

    .gauges-scroll-track .gauge-rail-card > .dash-chart-card {
        height: 100%;
        box-sizing: border-box;
    }

    .gauge-card-compact {
        flex: 0 0 330px;
        width: 330px;
        min-width: 300px;
    }

    .gauge-card-wide {
        flex: 0 0 480px;
        width: 480px;
        min-width: 390px;
    }

    @media (max-width: 768px) {
        .dashboard-header-container {
            margin-bottom: 1rem !important;
            gap: 0.5rem !important;
        }

        .dashboard-main-title {
            font-size: 1.35rem !important;
        }

        .dashboard-main-subtitle {
            font-size: 0.75rem !important;
            line-height: 1.35 !important;
        }

        .dashboard-date-filter {
            padding: 0.35rem 0.65rem !important;
            font-size: 0.75rem !important;
            border-radius: 8px !important;
        }

        /* 2-tier Grid for Section Headers */
        .insights-header {
            display: grid !important;
            grid-template-columns: 1fr auto !important;
            grid-template-rows: auto auto !important;
            row-gap: 0.35rem !important;
            column-gap: 0.5rem !important;
            align-items: center !important;
            margin-bottom: 0.5rem !important;
            padding: 0 !important;
        }

        .insights-title-box {
            grid-column: 1 / 2 !important;
            grid-row: 1 / 3 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.25rem !important;
            min-width: 0 !important;
        }

        .insights-badge {
            font-size: 0.75rem !important;
            padding: 0.2rem 0.5rem !important;
            max-width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .insights-pill-info {
            font-size: 0.68rem !important;
            padding: 0.15rem 0.45rem !important;
            white-space: nowrap !important;
        }

        .insights-controls {
            grid-column: 2 / 3 !important;
            grid-row: 1 / 2 !important;
            align-self: center !important;
            gap: 0.35rem !important;
        }

        .insights-nav-btn {
            width: 30px !important;
            height: 30px !important;
        }

        .insights-nav-btn svg {
            width: 14px !important;
            height: 14px !important;
        }

        /* Track Container & Overflow Rail */
        .dashboard-scroll-track,
        .insights-scroll-track {
            gap: 0.75rem !important;
            padding: 4px 2px 10px 2px !important;
        }

        /* Row 1: KPI Cards on Mobile */
        .insights-scroll-track .kpi-card {
            flex: 0 0 72vw !important;
            width: 72vw !important;
            min-width: 200px !important;
            max-width: 250px !important;
            padding: 0.85rem 0.95rem !important;
            border-radius: 14px !important;
        }

        /* Row 2: Graphs / Metric Cards on Mobile */
        .graph-card-wide,
        .graph-card-medium {
            flex: 0 0 calc(100vw - 2.1rem) !important;
            width: calc(100vw - 2.1rem) !important;
            min-width: 280px !important;
            max-width: 420px !important;
            scroll-snap-align: center !important;
        }

        .graphs-scroll-track .graph-rail-card > .dash-chart-card {
            padding: 0.85rem 0.75rem !important;
            border-radius: 12px !important;
        }

        /* Row 3: Lists / Table Cards on Mobile */
        .list-card-table,
        .list-card-wide {
            flex: 0 0 calc(100vw - 2.1rem) !important;
            width: calc(100vw - 2.1rem) !important;
            min-width: 280px !important;
            max-width: 420px !important;
            scroll-snap-align: center !important;
        }

        .lists-scroll-track .list-rail-card > .dash-table-card {
            padding: 0.85rem 0.75rem !important;
            border-radius: 12px !important;
        }

        /* Mobile Chart Card Header & Action Wrap */
        .dash-card-header {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.45rem !important;
            margin-bottom: 0.65rem !important;
        }

        .dash-card-header-main {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            width: 100% !important;
            gap: 0.5rem !important;
        }

        .dash-card-title {
            font-size: 0.95rem !important;
            line-height: 1.25 !important;
        }

        .dash-card-actions {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            width: 100% !important;
            gap: 0.5rem !important;
            flex-wrap: wrap !important;
        }

        .dash-filter-select {
            font-size: 0.72rem !important;
            padding: 0.25rem 0.5rem !important;
        }

        .orders-combo-legend {
            font-size: 0.68rem !important;
            gap: 0.5rem !important;
        }

        .orders-status-container {
            gap: 0.65rem !important;
        }

        .orders-status-donut-box {
            width: 115px !important;
            height: 115px !important;
        }

        .orders-status-legend {
            font-size: 0.68rem !important;
            gap: 0.25rem !important;
        }

        /* Keep Table Card Header on a single row (Title + View All) */
        .dash-table-card .dash-card-header {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 0.5rem !important;
            margin-bottom: 0.75rem !important;
        }

        /* Table styling inside cards on mobile */
        .lists-scroll-track .dash-table {
            min-width: 400px !important;
        }

        .lists-scroll-track .recent-orders-table {
            min-width: 520px !important;
        }

        .lists-scroll-track table {
            min-width: 380px !important;
        }

        .lists-scroll-track th {
            white-space: nowrap !important;
            font-size: 0.72rem !important;
            padding: 0.35rem 0.4rem !important;
        }

        .lists-scroll-track td {
            font-size: 0.73rem !important;
            padding: 0.45rem 0.4rem !important;
            white-space: nowrap !important;
        }

        /* Row 4 Responsive Cards on Mobile */
        .gauge-card-compact,
        .gauge-card-wide {
            flex: 0 0 calc(100vw - 2.1rem) !important;
            width: calc(100vw - 2.1rem) !important;
            min-width: 280px !important;
            max-width: 420px !important;
            scroll-snap-align: center !important;
        }

        .gauges-scroll-track .gauge-rail-card > .dash-chart-card {
            padding: 0.85rem 0.75rem !important;
            border-radius: 12px !important;
        }

        .insights-fade-edge {
            width: 20px !important;
        }
    }
</style>
@endpush

@section('content')

    <!-- Top Dashboard Header with Title & Date Range Picker -->
    <div class="dashboard-header-container" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.85rem; margin-bottom: 1.25rem;">
        <div>
            <h1 class="dashboard-main-title" style="font-size: 1.65rem; font-weight: 800; color: #0f172a; letter-spacing: -0.025em; line-height: 1.15;">{{ transLabel('dashboard.title', 'Dashboard') }}</h1>
            <p class="dashboard-main-subtitle" style="font-size: 0.825rem; color: #64748b; margin-top: 0.25rem;">
                {{ transLabel('dashboard.subtitle', "Welcome back, :name! Here's an overview of your School Meal platform.", ['name' => auth()->user()->full_name ?? 'SENG Sourng']) }}
            </p>
        </div>

        <!-- Interactive Date Range Filter Component -->
        <x-dashboard.date-filter
            :activeRange="$activeRange ?? 'this_month'"
            :activeLabel="$activeLabel ?? 'Oct 1, 2026 – Oct 31, 2026'"
            :startDate="$startDate ?? null"
            :endDate="$endDate ?? null"
            filterId="adminDateFilter"
            onSelect="window.applyDashboardDateFilter"
        />
    </div>

    <!-- ROW 1: META (FACEBOOK) INSIGHTS SCROLLABLE RAIL -->
    <div class="insights-wrapper">
        <!-- Header with Meta Insights Title & Chevrons -->
        <div class="insights-header">
            <div class="insights-title-box">
                <div class="insights-badge">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                    <span>{{ transLabel('dashboard.performance_insights', 'Key Insights') }}</span>
                </div>
                <div class="insights-pill-info">
                    <span class="pulse-dot"></span>
                    <span>{{ transLabel('dashboard.metrics_count', ':count Metrics', ['count' => 8]) }} • {{ transLabel('dashboard.scroll_to_explore', 'Scroll to explore') }}</span>
                </div>
            </div>

            <div class="insights-controls">
                <button
                    type="button"
                    class="insights-nav-btn disabled"
                    id="insightsPrevBtn"
                    onclick="scrollInsights('left')"
                    title="{{ transLabel('dashboard.scroll_left', 'Scroll left') }}"
                    aria-label="{{ transLabel('dashboard.scroll_left', 'Scroll left') }}"
                    disabled
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button
                    type="button"
                    class="insights-nav-btn"
                    id="insightsNextBtn"
                    onclick="scrollInsights('right')"
                    title="{{ transLabel('dashboard.scroll_right', 'Scroll right') }}"
                    aria-label="{{ transLabel('dashboard.scroll_right', 'Scroll right') }}"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>

        <!-- Scrollable Track with Edge Fade Masks -->
        <div class="insights-track-container">
            <div class="insights-fade-edge fade-left" id="insightsFadeLeft"></div>
            <div class="insights-fade-edge fade-right" id="insightsFadeRight"></div>

            <div class="insights-scroll-track" id="insightsTrack">
                <!-- 1. Total Orders -->
                <x-dashboard.kpi-card
                    key="total_orders"
                    :title="transLabel('dashboard.total_orders', 'Total Orders')"
                    :value="$stats['total_orders']"
                    :change="$stats['total_orders_change'] ?? '12%'"
                    :trend="$stats['total_orders_trend'] ?? 'up'"
                    :period="$stats['total_orders_period'] ?? 'vs last month'"
                    color="blue"
                >
                    <x-slot:icon>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </x-slot:icon>
                </x-dashboard.kpi-card>

                <!-- 2. Revenue (USD) -->
                <x-dashboard.kpi-card
                    key="revenue_usd"
                    :title="transLabel('dashboard.revenue_usd', 'Revenue (USD)')"
                    :value="$stats['revenue_usd']"
                    :change="$stats['revenue_usd_change'] ?? '8%'"
                    :trend="$stats['revenue_usd_trend'] ?? 'up'"
                    :period="$stats['revenue_usd_period'] ?? 'vs last month'"
                    color="green"
                >
                    <x-slot:icon>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </x-slot:icon>
                </x-dashboard.kpi-card>

                <!-- 3. Active Schools -->
                <x-dashboard.kpi-card
                    key="active_schools"
                    :title="transLabel('dashboard.active_schools', 'Active Schools')"
                    :value="$stats['active_schools']"
                    :change="$stats['active_schools_change'] ?? '4%'"
                    :trend="$stats['active_schools_trend'] ?? 'up'"
                    :period="$stats['active_schools_period'] ?? 'vs last month'"
                    color="orange"
                >
                    <x-slot:icon>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m4 6 8-4 8 4"/><path d="M18 10 22 12v10H2V12l4-2"/><path d="M6 5v17"/><path d="M18 5v17"/><circle cx="12" cy="9" r="2"/></svg>
                    </x-slot:icon>
                </x-dashboard.kpi-card>

                <!-- 4. Total Students -->
                <x-dashboard.kpi-card
                    key="total_students"
                    :title="transLabel('dashboard.total_students', 'Total Students')"
                    :value="$stats['total_students']"
                    :change="$stats['total_students_change'] ?? '10%'"
                    :trend="$stats['total_students_trend'] ?? 'up'"
                    :period="$stats['total_students_period'] ?? 'vs last month'"
                    color="purple"
                >
                    <x-slot:icon>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </x-slot:icon>
                </x-dashboard.kpi-card>

                <!-- 5. Partner Restaurants -->
                <x-dashboard.kpi-card
                    key="partner_restaurants"
                    :title="transLabel('dashboard.partner_restaurants', 'Partner Restaurants')"
                    :value="$stats['partner_restaurants']"
                    :change="$stats['partner_restaurants_change'] ?? '6%'"
                    :trend="$stats['partner_restaurants_trend'] ?? 'up'"
                    :period="$stats['partner_restaurants_period'] ?? 'vs last month'"
                    color="red"
                >
                    <x-slot:icon>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </x-slot:icon>
                </x-dashboard.kpi-card>

                <!-- 6. On-time Delivery -->
                <x-dashboard.kpi-card
                    key="ontime_delivery_pct"
                    :title="transLabel('dashboard.ontime_delivery', 'On-time Delivery')"
                    :value="$stats['ontime_delivery_pct']"
                    :change="$stats['ontime_delivery_pct_change'] ?? '2%'"
                    :trend="$stats['ontime_delivery_pct_trend'] ?? 'up'"
                    :period="$stats['ontime_delivery_pct_period'] ?? 'vs target'"
                    color="teal"
                >
                    <x-slot:icon>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/></svg>
                    </x-slot:icon>
                </x-dashboard.kpi-card>

                <!-- 7. Total Meals Served -->
                <x-dashboard.kpi-card
                    key="meals_served"
                    :title="transLabel('dashboard.meals_served', 'Meals Served')"
                    :value="$stats['meals_served'] ?? '24,580'"
                    :change="$stats['meals_served_change'] ?? '15%'"
                    :trend="$stats['meals_served_trend'] ?? 'up'"
                    :period="$stats['meals_served_period'] ?? 'vs last month'"
                    color="indigo"
                >
                    <x-slot:icon>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2v6a3 3 0 0 1-3 3 3 3 0 0 1-3-3V2"/><path d="M15 2v19"/><path d="M6 2v19"/><path d="M9 2a3 3 0 0 1-3 3 3 3 0 0 1-3-3"/></svg>
                    </x-slot:icon>
                </x-dashboard.kpi-card>

                <!-- 8. Meal Quality / Satisfaction -->
                <x-dashboard.kpi-card
                    key="satisfaction_rate"
                    :title="transLabel('dashboard.satisfaction_rate', 'Satisfaction Rate')"
                    :value="$stats['satisfaction_rate'] ?? '98.5%'"
                    :change="$stats['satisfaction_rate_change'] ?? '0.5%'"
                    :trend="$stats['satisfaction_rate_trend'] ?? 'up'"
                    :period="$stats['satisfaction_rate_period'] ?? 'overall'"
                    color="amber"
                >
                    <x-slot:icon>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </x-slot:icon>
                </x-dashboard.kpi-card>
            </div>
        </div>
    </div>

    <!-- ROW 2: MAJOR VISUAL ANALYTICS & CHARTS SCROLLABLE RAIL -->
    <div class="dashboard-rail-section graphs-wrapper">
        <div class="insights-header">
            <div class="insights-title-box">
                <div class="insights-badge" style="background: rgba(37, 99, 235, 0.08); color: #1d4ed8; border-color: rgba(37, 99, 235, 0.2);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3v18h18"/>
                        <path d="m19 9-5 5-4-4-3 3"/>
                    </svg>
                    <span>{{ transLabel('dashboard.visual_analytics', 'Visual Analytics & Charts') }}</span>
                </div>
                <div class="insights-pill-info">
                    <span class="pulse-dot" style="background: #2563EB; box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);"></span>
                    <span>{{ transLabel('dashboard.views_count', ':count Views', ['count' => 3]) }} • {{ transLabel('dashboard.scroll_to_explore', 'Scroll to explore') }}</span>
                </div>
            </div>

            <div class="insights-controls">
                <button
                    type="button"
                    class="insights-nav-btn disabled"
                    id="graphsPrevBtn"
                    onclick="scrollGraphs('left')"
                    title="{{ transLabel('dashboard.scroll_left', 'Scroll left') }}"
                    aria-label="{{ transLabel('dashboard.scroll_left', 'Scroll left') }}"
                    disabled
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button
                    type="button"
                    class="insights-nav-btn"
                    id="graphsNextBtn"
                    onclick="scrollGraphs('right')"
                    title="{{ transLabel('dashboard.scroll_right', 'Scroll right') }}"
                    aria-label="{{ transLabel('dashboard.scroll_right', 'Scroll right') }}"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>

        <div class="insights-track-container">
            <div class="insights-fade-edge fade-left" id="graphsFadeLeft"></div>
            <div class="insights-fade-edge fade-right" id="graphsFadeRight"></div>

            <div class="dashboard-scroll-track graphs-scroll-track" id="graphsTrack">
                <!-- 1. Orders Overview Combo Chart -->
                <div class="graph-rail-card graph-card-wide">
                    <x-dashboard.chart-card
                        title="{{ transLabel('dashboard.orders_overview', 'Orders Overview') }}"
                        :filters="[
                            'last_30_days' => transLabel('dashboard.last_30_days', 'Last 30 Days'),
                            'last_7_days' => transLabel('dashboard.last_7_days', 'Last 7 Days'),
                            'this_month' => transLabel('dashboard.this_month', 'This Month'),
                            'this_year' => transLabel('dashboard.this_year', 'This Year'),
                        ]"
                        activeFilter="last_30_days"
                        chartId="ordersOverview"
                        filterId="filterOrdersOverview"
                        onFilterChange="window.filterChartCard"
                    >
                        <x-slot:headerExtra>
                            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                <!-- Dynamic Graph Trend Badge -->
                                <div id="overviewTrendBadge" style="display: inline-flex; align-items: center; gap: 0.3rem; background: #ecfdf5; color: #059669; font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 9999px; border: 1px solid rgba(16, 185, 129, 0.2); transition: all 0.2s ease;">
                                    <svg id="overviewTrendIcon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                                    <span id="overviewTrendText">+18.4% Trend</span>
                                </div>

                                <!-- Clickable Dataset Toggle Legend -->
                                <div class="orders-combo-legend" style="display: flex; align-items: center; gap: 0.65rem; font-size: 0.75rem; font-weight: 600;">
                                    <button type="button" id="toggleLegendOrders" onclick="toggleDataset(0, this)" title="Click to toggle Orders series" style="display: flex; align-items: center; gap: 0.35rem; background: transparent; border: none; cursor: pointer; padding: 0; color: #2563EB; font-family: inherit; font-size: inherit; font-weight: inherit; transition: opacity 0.2s ease;">
                                        <span style="width: 10px; height: 10px; background: #2563EB; border-radius: 2px; display: inline-block;"></span>
                                        <span>{{ transLabel('dashboard.orders', 'Orders') }}</span>
                                    </button>
                                    <button type="button" id="toggleLegendRevenue" onclick="toggleDataset(1, this)" title="Click to toggle Revenue series" style="display: flex; align-items: center; gap: 0.35rem; background: transparent; border: none; cursor: pointer; padding: 0; color: #10B981; font-family: inherit; font-size: inherit; font-weight: inherit; transition: opacity 0.2s ease;">
                                        <span style="width: 10px; height: 10px; background: #10B981; border-radius: 50%; display: inline-block;"></span>
                                        <span>{{ transLabel('dashboard.revenue_usd', 'Revenue (USD)') }}</span>
                                    </button>
                                </div>
                            </div>
                        </x-slot:headerExtra>

                        <div style="height: 250px; width: 100%; position: relative;">
                            <canvas id="chartOrdersOverview"></canvas>
                        </div>
                    </x-dashboard.chart-card>
                </div>

                <!-- 2. Orders by Status Donut Chart -->
                <div class="graph-rail-card graph-card-medium">
                    <x-dashboard.chart-card
                        title="{{ transLabel('dashboard.orders_by_status', 'Orders by Status') }}"
                        :filters="[
                            'this_month' => transLabel('dashboard.this_month', 'This Month'),
                            'last_month' => transLabel('dashboard.last_month', 'Last Month'),
                            'last_30_days' => transLabel('dashboard.last_30_days', 'Last 30 Days'),
                            'this_year' => transLabel('dashboard.this_year', 'This Year'),
                        ]"
                        activeFilter="this_month"
                        chartId="ordersByStatus"
                        filterId="filterOrdersByStatus"
                        onFilterChange="window.filterChartCard"
                    >
                        <div class="orders-status-container" style="display: flex; align-items: center; gap: 1rem; height: 250px;">
                            <!-- Donut Canvas with Center Text -->
                            <div class="orders-status-donut-box" style="position: relative; width: 145px; height: 145px; flex-shrink: 0;">
                                <canvas id="chartOrdersByStatus"></canvas>
                                <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none;">
                                    <span style="font-size: 1.15rem; font-weight: 800; color: #0f172a; line-height: 1;">1,248</span>
                                    <span style="font-size: 0.65rem; color: #64748b; font-weight: 600;">{{ transLabel('dashboard.total_orders', 'Total Orders') }}</span>
                                </div>
                            </div>

                            <!-- Custom Legend List matching UI -->
                            <div class="orders-status-legend" style="flex: 1; font-size: 0.725rem; font-weight: 600; display: flex; flex-direction: column; gap: 0.35rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; color: #334155;">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #F59E0B;"></span>
                                        <span>{{ transLabel('dashboard.pending', 'Pending') }}</span>
                                    </div>
                                    <span style="color: #64748b;">124 (9.9%)</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; color: #334155;">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #3B82F6;"></span>
                                        <span>{{ transLabel('dashboard.confirmed', 'Confirmed') }}</span>
                                    </div>
                                    <span style="color: #64748b;">386 (30.9%)</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; color: #334155;">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #8B5CF6;"></span>
                                        <span>{{ transLabel('dashboard.preparing', 'Preparing') }}</span>
                                    </div>
                                    <span style="color: #64748b;">248 (19.9%)</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; color: #334155;">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981;"></span>
                                        <span>{{ transLabel('dashboard.out_for_delivery', 'Out for Delivery') }}</span>
                                    </div>
                                    <span style="color: #64748b;">302 (24.2%)</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; color: #334155;">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #22C55E;"></span>
                                        <span>{{ transLabel('dashboard.delivered', 'Delivered') }}</span>
                                    </div>
                                    <span style="color: #64748b;">168 (13.5%)</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; color: #334155;">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #EF4444;"></span>
                                        <span>{{ transLabel('dashboard.cancelled', 'Cancelled') }}</span>
                                    </div>
                                    <span style="color: #64748b;">20 (1.6%)</span>
                                </div>
                            </div>
                        </div>
                    </x-dashboard.chart-card>
                </div>

                <!-- 3. Live Delivery Tracking Map -->
                <div class="graph-rail-card graph-card-medium">
                    <x-dashboard.chart-card
                        title="{{ transLabel('dashboard.live_delivery_tracking', 'Live Delivery Tracking') }}"
                        :actionText="transLabel('dashboard.full_map_view', 'Full Map View')"
                        actionUrl="javascript:void(0)"
                    >
                        <div style="height: 195px; width: 100%; border-radius: 10px; overflow: hidden; position: relative; border: 1px solid #e2e8f0;" id="liveMapContainer">
                            <div id="siemReapMap" style="width: 100%; height: 100%;"></div>
                        </div>

                        <!-- Map Bottom Legend matching UI -->
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 0.65rem; font-size: 0.725rem; font-weight: 700;">
                            <div style="display: flex; align-items: center; gap: 0.35rem; color: #2563EB;">
                                <span style="width: 9px; height: 9px; border-radius: 50%; background: #2563EB;"></span>
                                <span>{{ transLabel('dashboard.school', 'School') }} (12)</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.35rem; color: #EA580C;">
                                <span style="width: 9px; height: 9px; border-radius: 50%; background: #EA580C;"></span>
                                <span>{{ transLabel('dashboard.restaurant', 'Restaurant') }} (8)</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.35rem; color: #10B981;">
                                <span style="width: 9px; height: 9px; border-radius: 50%; background: #10B981;"></span>
                                <span>{{ transLabel('dashboard.driver', 'Driver') }} (5)</span>
                            </div>
                        </div>
                    </x-dashboard.chart-card>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 3: OPERATIONS & LEADERBOARD LISTS SCROLLABLE RAIL -->
    <div class="dashboard-rail-section lists-wrapper">
        <div class="insights-header">
            <div class="insights-title-box">
                <div class="insights-badge" style="background: rgba(16, 185, 129, 0.08); color: #059669; border-color: rgba(16, 185, 129, 0.2);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="8" y1="6" x2="21" y2="6"/>
                        <line x1="8" y1="12" x2="21" y2="12"/>
                        <line x1="8" y1="18" x2="21" y2="18"/>
                        <line x1="3" y1="6" x2="3.01" y2="6"/>
                        <line x1="3" y1="12" x2="3.01" y2="12"/>
                        <line x1="3" y1="18" x2="3.01" y2="18"/>
                    </svg>
                    <span>{{ transLabel('dashboard.operations_leaderboard', 'Operations & Leaderboard Lists') }}</span>
                </div>
                <div class="insights-pill-info">
                    <span class="pulse-dot" style="background: #059669; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);"></span>
                    <span>{{ transLabel('dashboard.views_count', ':count Views', ['count' => 3]) }} • {{ transLabel('dashboard.scroll_to_explore', 'Scroll to explore') }}</span>
                </div>
            </div>

            <div class="insights-controls">
                <button
                    type="button"
                    class="insights-nav-btn disabled"
                    id="listsPrevBtn"
                    onclick="scrollLists('left')"
                    title="{{ transLabel('dashboard.scroll_left', 'Scroll left') }}"
                    aria-label="{{ transLabel('dashboard.scroll_left', 'Scroll left') }}"
                    disabled
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button
                    type="button"
                    class="insights-nav-btn"
                    id="listsNextBtn"
                    onclick="scrollLists('right')"
                    title="{{ transLabel('dashboard.scroll_right', 'Scroll right') }}"
                    aria-label="{{ transLabel('dashboard.scroll_right', 'Scroll right') }}"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>

        <div class="insights-track-container">
            <div class="insights-fade-edge fade-left" id="listsFadeLeft"></div>
            <div class="insights-fade-edge fade-right" id="listsFadeRight"></div>

            <div class="dashboard-scroll-track lists-scroll-track" id="listsTrack">
                <!-- 1. Top Restaurants by Orders -->
                <div class="list-rail-card list-card-table">
                    <x-dashboard.table-card title="{{ transLabel('dashboard.top_restaurants', 'Top Restaurants by Orders') }}" viewAllUrl="javascript:void(0)">
                        <table class="dash-table" style="min-width: 400px; width: 100%; border-collapse: collapse; font-size: 0.775rem;">
                            <thead>
                                <tr style="color: #64748b; font-weight: 700; border-bottom: 1px solid #f1f5f9; text-align: left; white-space: nowrap;">
                                    <th style="padding: 0.4rem 0.25rem; white-space: nowrap;">#</th>
                                    <th style="padding: 0.4rem 0.5rem; white-space: nowrap;">{{ transLabel('dashboard.restaurant', 'Restaurant') }}</th>
                                    <th style="padding: 0.4rem 0.5rem; text-align: right; white-space: nowrap;">{{ transLabel('dashboard.orders', 'Orders') }}</th>
                                    <th style="padding: 0.4rem 0.5rem; text-align: right; white-space: nowrap;">{{ transLabel('dashboard.revenue', 'Revenue') }}</th>
                                    <th style="padding: 0.4rem 0.25rem; text-align: right; white-space: nowrap;">{{ transLabel('dashboard.on_time', 'On-time') }}</th>
                                </tr>
                            </thead>
                            <tbody id="topRestaurantsBody">
                                @foreach($topRestaurants as $idx => $r)
                                    <tr style="border-bottom: 1px solid #f8fafc; font-weight: 600;">
                                        <td style="padding: 0.55rem 0.25rem; color: #94a3b8;">{{ $idx + 1 }}</td>
                                        <td style="padding: 0.55rem 0.5rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <img src="{{ $r['img'] }}" alt="{{ $r['name'] }}" style="width: 26px; height: 26px; border-radius: 6px; object-fit: cover;" onerror="this.src='/images/food_dish_1.jpg'">
                                                <span style="color: #0f172a; white-space: nowrap;">{{ $r['name'] }}</span>
                                            </div>
                                        </td>
                                        <td style="padding: 0.55rem 0.5rem; text-align: right; color: #0f172a; font-weight: 700;">{{ $r['orders'] }}</td>
                                        <td style="padding: 0.55rem 0.5rem; text-align: right; color: #64748b;">{{ $r['revenue'] }}</td>
                                        <td style="padding: 0.55rem 0.25rem; text-align: right;">
                                            <span style="background: #ecfdf5; color: #059669; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.7rem; font-weight: 700;">{{ $r['ontime'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-dashboard.table-card>
                </div>

                <!-- 2. Top Schools by Orders -->
                <div class="list-rail-card list-card-table">
                    <x-dashboard.table-card title="{{ transLabel('dashboard.top_schools', 'Top Schools by Orders') }}" viewAllUrl="javascript:void(0)">
                        <table class="dash-table" style="min-width: 400px; width: 100%; border-collapse: collapse; font-size: 0.775rem;">
                            <thead>
                                <tr style="color: #64748b; font-weight: 700; border-bottom: 1px solid #f1f5f9; text-align: left; white-space: nowrap;">
                                    <th style="padding: 0.4rem 0.25rem; white-space: nowrap;">#</th>
                                    <th style="padding: 0.4rem 0.5rem; white-space: nowrap;">{{ transLabel('dashboard.school', 'School') }}</th>
                                    <th style="padding: 0.4rem 0.5rem; text-align: right; white-space: nowrap;">{{ transLabel('dashboard.orders', 'Orders') }}</th>
                                    <th style="padding: 0.4rem 0.5rem; text-align: right; white-space: nowrap;">{{ transLabel('dashboard.students', 'Students') }}</th>
                                    <th style="padding: 0.4rem 0.25rem; text-align: right; white-space: nowrap;">{{ transLabel('dashboard.participation', 'Participation') }}</th>
                                </tr>
                            </thead>
                            <tbody id="topSchoolsBody">
                                @foreach($topSchools as $idx => $s)
                                    <tr style="border-bottom: 1px solid #f8fafc; font-weight: 600;">
                                        <td style="padding: 0.55rem 0.25rem; color: #94a3b8;">{{ $idx + 1 }}</td>
                                        <td style="padding: 0.55rem 0.5rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <div style="width: 26px; height: 26px; border-radius: 6px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 800; flex-shrink: 0;">
                                                    🏫
                                                </div>
                                                <span style="color: #0f172a; white-space: nowrap;">{{ $s['name'] }}</span>
                                            </div>
                                        </td>
                                        <td style="padding: 0.55rem 0.5rem; text-align: right; color: #0f172a; font-weight: 700;">{{ $s['orders'] }}</td>
                                        <td style="padding: 0.55rem 0.5rem; text-align: right; color: #64748b;">{{ $s['students'] }}</td>
                                        <td style="padding: 0.55rem 0.25rem; text-align: right;">
                                            <span style="background: {{ (int)$s['participation'] > 75 ? '#ecfdf5' : '#fffbeb' }}; color: {{ (int)$s['participation'] > 75 ? '#059669' : '#d97706' }}; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.7rem; font-weight: 700;">{{ $s['participation'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-dashboard.table-card>
                </div>

                <!-- 3. Recent Orders -->
                <div class="list-rail-card list-card-wide">
                    <x-dashboard.table-card title="{{ transLabel('dashboard.recent_orders', 'Recent Orders') }}" viewAllUrl="javascript:void(0)">
                        <table class="dash-table recent-orders-table" style="min-width: 520px; width: 100%; border-collapse: collapse; font-size: 0.75rem;">
                            <thead>
                                <tr style="color: #64748b; font-weight: 700; border-bottom: 1px solid #f1f5f9; text-align: left; white-space: nowrap;">
                                    <th style="padding: 0.4rem 0.25rem; white-space: nowrap;">#</th>
                                    <th style="padding: 0.4rem 0.4rem; white-space: nowrap;">{{ transLabel('dashboard.order_id', 'Order ID') }}</th>
                                    <th style="padding: 0.4rem 0.4rem; white-space: nowrap;">{{ transLabel('dashboard.student', 'Student') }}</th>
                                    <th style="padding: 0.4rem 0.4rem; white-space: nowrap;">{{ transLabel('dashboard.school', 'School') }}</th>
                                    <th style="padding: 0.4rem 0.4rem; white-space: nowrap;">{{ transLabel('common.status', 'Status') }}</th>
                                    <th style="padding: 0.4rem 0.4rem; text-align: right; white-space: nowrap;">{{ transLabel('dashboard.amount', 'Amount') }}</th>
                                    <th style="padding: 0.4rem 0.25rem; text-align: right; white-space: nowrap;">{{ transLabel('dashboard.time', 'Time') }}</th>
                                </tr>
                            </thead>
                            <tbody id="recentOrdersBody">
                                @foreach($recentOrders as $idx => $o)
                                    <tr style="border-bottom: 1px solid #f8fafc; font-weight: 600;">
                                        <td style="padding: 0.55rem 0.25rem; color: #94a3b8;">{{ $idx + 1 }}</td>
                                        <td style="padding: 0.55rem 0.4rem; color: #2563EB; font-weight: 700; white-space: nowrap;">{{ $o['id'] }}</td>
                                        <td style="padding: 0.55rem 0.4rem; color: #0f172a; white-space: nowrap;">{{ $o['student'] }}</td>
                                        <td style="padding: 0.55rem 0.4rem; color: #64748b; white-space: nowrap;">{{ $o['school'] }}</td>
                                        <td style="padding: 0.55rem 0.4rem;">
                                            <x-dashboard.status-badge :status="$o['status']" />
                                        </td>
                                        <td style="padding: 0.55rem 0.4rem; text-align: right; color: #0f172a; font-weight: 700; white-space: nowrap;">{{ $o['amount'] }}</td>
                                        <td style="padding: 0.55rem 0.25rem; text-align: right; color: #94a3b8; white-space: nowrap;">{{ $o['time'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-dashboard.table-card>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 4: OPERATIONAL HEALTH & LIVE STREAM SCROLLABLE RAIL -->
    <div class="dashboard-rail-section gauges-wrapper">
        <div class="insights-header">
            <div class="insights-title-box">
                <div class="insights-badge" style="background: rgba(139, 92, 246, 0.08); color: #7c3aed; border-color: rgba(139, 92, 246, 0.2);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="m4.93 4.93 4.24 4.24"/>
                        <path d="m14.83 9.17 4.24-4.24"/>
                        <path d="m14.83 14.83 4.24 4.24"/>
                        <path d="m9.17 14.83-4.24 4.24"/>
                        <circle cx="12" cy="12" r="2"/>
                    </svg>
                    <span>{{ transLabel('dashboard.operational_health_activities', 'Operational Health & Live Stream') }}</span>
                </div>
                <div class="insights-pill-info">
                    <span class="pulse-dot" style="background: #7c3aed; box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2);"></span>
                    <span>{{ transLabel('dashboard.views_count', ':count Views', ['count' => 4]) }} • {{ transLabel('dashboard.scroll_to_explore', 'Scroll to explore') }}</span>
                </div>
            </div>

            <div class="insights-controls">
                <button
                    type="button"
                    class="insights-nav-btn disabled"
                    id="gaugesPrevBtn"
                    onclick="scrollGauges('left')"
                    title="{{ transLabel('dashboard.scroll_left', 'Scroll left') }}"
                    aria-label="{{ transLabel('dashboard.scroll_left', 'Scroll left') }}"
                    disabled
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button
                    type="button"
                    class="insights-nav-btn"
                    id="gaugesNextBtn"
                    onclick="scrollGauges('right')"
                    title="{{ transLabel('dashboard.scroll_right', 'Scroll right') }}"
                    aria-label="{{ transLabel('dashboard.scroll_right', 'Scroll right') }}"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>

        <div class="insights-track-container">
            <div class="insights-fade-edge fade-left" id="gaugesFadeLeft"></div>
            <div class="insights-fade-edge fade-right" id="gaugesFadeRight"></div>

            <div class="dashboard-scroll-track gauges-scroll-track" id="gaugesTrack">

                <!-- 1. Payment Status -->
                <div class="gauge-rail-card gauge-card-compact">
                    <x-dashboard.chart-card
                        :title="transLabel('dashboard.payment_status', 'Payment Status')"
                        :filters="[
                            'this_month' => transLabel('dashboard.this_month', 'This Month'),
                            'last_month' => transLabel('dashboard.last_month', 'Last Month'),
                            'last_30_days' => transLabel('dashboard.last_30_days', 'Last 30 Days'),
                            'this_year' => transLabel('dashboard.this_year', 'This Year'),
                        ]"
                        activeFilter="this_month"
                        chartId="paymentStatus"
                        filterId="filterPaymentStatus"
                        onFilterChange="window.filterChartCard"
                    >
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 215px;">
                            <div style="position: relative; width: 130px; height: 130px; margin-bottom: 0.85rem;">
                                <canvas id="chartPaymentStatus"></canvas>
                                <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none;">
                                    <span style="font-size: 0.95rem; font-weight: 800; color: #0f172a; line-height: 1;">$18,420</span>
                                    <span style="font-size: 0.6rem; color: #64748b; font-weight: 600;">{{ transLabel('dashboard.total_revenue', 'Total Revenue') }}</span>
                                </div>
                            </div>

                            <!-- 4 Status Legend Row -->
                            <div style="width: 100%; display: grid; grid-template-columns: 1fr 1fr; gap: 0.4rem; font-size: 0.72rem; font-weight: 600;">
                                <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 0.25rem 0.45rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.3rem;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981;"></span>
                                        <span style="color: #475569;">{{ transLabel('dashboard.paid', 'Paid') }}</span>
                                    </div>
                                    <span style="color: #0f172a; font-weight: 700;">$16,320</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 0.25rem 0.45rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.3rem;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #F59E0B;"></span>
                                        <span style="color: #475569;">{{ transLabel('dashboard.pending', 'Pending') }}</span>
                                    </div>
                                    <span style="color: #0f172a; font-weight: 700;">$1,420</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 0.25rem 0.45rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.3rem;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #EF4444;"></span>
                                        <span style="color: #475569;">{{ transLabel('dashboard.failed', 'Failed') }}</span>
                                    </div>
                                    <span style="color: #0f172a; font-weight: 700;">$430</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 0.25rem 0.45rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.3rem;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #94A3B8;"></span>
                                        <span style="color: #475569;">{{ transLabel('dashboard.refunded', 'Refunded') }}</span>
                                    </div>
                                    <span style="color: #0f172a; font-weight: 700;">$250</span>
                                </div>
                            </div>
                        </div>
                    </x-dashboard.chart-card>
                </div>

                <!-- 2. Delivery Performance -->
                <div class="gauge-rail-card gauge-card-compact">
                    <x-dashboard.chart-card
                        :title="transLabel('dashboard.delivery_performance', 'Delivery Performance')"
                        :filters="[
                            'this_month' => transLabel('dashboard.this_month', 'This Month'),
                            'last_month' => transLabel('dashboard.last_month', 'Last Month'),
                            'last_30_days' => transLabel('dashboard.last_30_days', 'Last 30 Days'),
                            'this_year' => transLabel('dashboard.this_year', 'This Year'),
                        ]"
                        activeFilter="this_month"
                        chartId="deliveryPerformance"
                        filterId="filterDeliveryPerformance"
                        onFilterChange="window.filterChartCard"
                    >
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 215px;">
                            <div style="position: relative; width: 130px; height: 130px; margin-bottom: 0.85rem;">
                                <canvas id="chartDeliveryPerformance"></canvas>
                                <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none;">
                                    <span style="font-size: 1.15rem; font-weight: 800; color: #0f172a; line-height: 1;">94.2%</span>
                                    <span style="font-size: 0.6rem; color: #64748b; font-weight: 600;">{{ transLabel('dashboard.ontime_delivery', 'On-time Delivery') }}</span>
                                </div>
                            </div>

                            <!-- Breakdown Legend -->
                            <div style="width: 100%; display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.72rem; font-weight: 600;">
                                <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 0.25rem 0.5rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981;"></span>
                                        <span style="color: #475569;">{{ transLabel('dashboard.on_time', 'On-time') }}</span>
                                    </div>
                                    <span style="color: #0f172a; font-weight: 700;">1,020 (94.2%)</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 0.25rem 0.5rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #F59E0B;"></span>
                                        <span style="color: #475569;">{{ transLabel('dashboard.delayed', 'Delayed') }}</span>
                                    </div>
                                    <span style="color: #0f172a; font-weight: 700;">48 (4.4%)</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 0.25rem 0.5rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #EF4444;"></span>
                                        <span style="color: #475569;">{{ transLabel('dashboard.failed', 'Failed') }}</span>
                                    </div>
                                    <span style="color: #0f172a; font-weight: 700;">15 (1.4%)</span>
                                </div>
                            </div>
                        </div>
                    </x-dashboard.chart-card>
                </div>

                <!-- 3. Student Meal Participation -->
                <div class="gauge-rail-card gauge-card-compact">
                    <x-dashboard.chart-card
                        :title="transLabel('dashboard.student_participation', 'Student Meal Participation')"
                        :filters="[
                            'this_month' => transLabel('dashboard.this_month', 'This Month'),
                            'last_month' => transLabel('dashboard.last_month', 'Last Month'),
                            'last_30_days' => transLabel('dashboard.last_30_days', 'Last 30 Days'),
                            'this_year' => transLabel('dashboard.this_year', 'This Year'),
                        ]"
                        activeFilter="this_month"
                        chartId="studentParticipation"
                        filterId="filterStudentParticipation"
                        onFilterChange="window.filterChartCard"
                    >
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 215px;">
                            <div style="position: relative; width: 130px; height: 130px; margin-bottom: 0.85rem;">
                                <canvas id="chartStudentParticipation"></canvas>
                                <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none;">
                                    <span style="font-size: 1.15rem; font-weight: 800; color: #0f172a; line-height: 1;">76%</span>
                                    <span style="font-size: 0.6rem; color: #64748b; font-weight: 600;">{{ transLabel('dashboard.participation_rate', 'Participation Rate') }}</span>
                                </div>
                            </div>

                            <!-- Breakdown Legend -->
                            <div style="width: 100%; display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.72rem; font-weight: 600;">
                                <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 0.25rem 0.5rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #2563EB;"></span>
                                        <span style="color: #475569;">{{ transLabel('dashboard.ordered', 'Ordered') }}</span>
                                    </div>
                                    <span style="color: #0f172a; font-weight: 700;">6,420 (76%)</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 0.25rem 0.5rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #E2E8F0;"></span>
                                        <span style="color: #475569;">{{ transLabel('dashboard.not_ordered', 'Not Ordered') }}</span>
                                    </div>
                                    <span style="color: #0f172a; font-weight: 700;">2,006 (24%)</span>
                                </div>
                            </div>
                        </div>
                    </x-dashboard.chart-card>
                </div>

                <!-- 4. Recent Activities Feed -->
                <div class="gauge-rail-card gauge-card-wide">
                    <x-dashboard.chart-card :title="transLabel('dashboard.recent_activities', 'Recent Activities')" :actionText="transLabel('common.view_all', 'View All')" actionUrl="javascript:void(0)">
                        <x-slot:headerExtra>
                            <div class="activity-filter-tabs" style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.68rem; font-weight: 700;">
                                <button type="button" class="activity-pill-btn active" onclick="filterActivities('all', this)" style="padding: 0.18rem 0.5rem; border-radius: 9999px; border: 1px solid #2563EB; cursor: pointer; background: #2563EB; color: #ffffff; transition: all 0.15s ease;">{{ transLabel('common.all', 'All') }}</button>
                                <button type="button" class="activity-pill-btn" onclick="filterActivities('payment', this)" style="padding: 0.18rem 0.5rem; border-radius: 9999px; border: 1px solid #e2e8f0; cursor: pointer; background: #f8fafc; color: #64748b; transition: all 0.15s ease;">{{ transLabel('dashboard.tab_payments', 'Payments') }}</button>
                                <button type="button" class="activity-pill-btn" onclick="filterActivities('operations', this)" style="padding: 0.18rem 0.5rem; border-radius: 9999px; border: 1px solid #e2e8f0; cursor: pointer; background: #f8fafc; color: #64748b; transition: all 0.15s ease;">{{ transLabel('dashboard.tab_operations', 'Operations') }}</button>
                                <button type="button" class="activity-pill-btn" onclick="filterActivities('user', this)" style="padding: 0.18rem 0.5rem; border-radius: 9999px; border: 1px solid #e2e8f0; cursor: pointer; background: #f8fafc; color: #64748b; transition: all 0.15s ease;">{{ transLabel('dashboard.tab_users', 'Users') }}</button>
                            </div>
                        </x-slot:headerExtra>

                        <div id="activitiesListContainer" style="display: flex; flex-direction: column; gap: 0.15rem; max-height: 220px; overflow-y: auto; padding-right: 0.25rem;">
                            @foreach($activities as $act)
                                <x-dashboard.activity-item
                                    :title="$act['title']"
                                    :description="$act['desc']"
                                    :time="$act['time']"
                                    :color="$act['color']"
                                    :type="$act['icon']"
                                >
                                    @if($act['icon'] === 'store')
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                    @elseif($act['icon'] === 'school')
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m4 6 8-4 8 4"/><path d="M18 10 22 12v10H2V12l4-2"/><path d="M6 5v17"/></svg>
                                    @elseif($act['icon'] === 'payment')
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                    @elseif($act['icon'] === 'user')
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    @elseif($act['icon'] === 'truck')
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="17" cy="18" r="2"/></svg>
                                    @endif
                                </x-dashboard.activity-item>
                            @endforeach
                        </div>
                    </x-dashboard.chart-card>
                </div>

            </div>
        </div>

        <!-- Mobile Bottom Safe Spacer: Cleanly handled by admin-content -->
        <div class="mobile-only-spacer" style="height: 0; margin: 0; padding: 0; width: 100%; clear: both;" aria-hidden="true"></div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Global Chart.js Registry & Setup
        window.dashboardCharts = {};

        if (window.Chart) {
            const isKhmer = document.documentElement.lang === 'km';
            Chart.defaults.font.family = isKhmer
                ? "'Hanuman', 'Plus Jakarta Sans', sans-serif"
                : "'Plus Jakarta Sans', sans-serif";
            if (isKhmer) {
                Chart.defaults.font.size = 11;
            }
        }

        // ==========================================
        // Helper: Dynamic Trend Calculation for Orders Overview
        // ==========================================
        function updateOverviewTrendBadge(revenueArr) {
            const badge = document.getElementById('overviewTrendBadge');
            const text = document.getElementById('overviewTrendText');
            const icon = document.getElementById('overviewTrendIcon');
            if (!badge || !revenueArr || revenueArr.length < 2) return;

            const first = Number(revenueArr[0]) || 1;
            const last = Number(revenueArr[revenueArr.length - 1]) || 0;
            const diff = last - first;
            const pct = Math.round((diff / (first || 1)) * 100);
            const isUp = diff >= 0;

            if (isUp) {
                badge.style.background = '#ecfdf5';
                badge.style.color = '#059669';
                badge.style.borderColor = 'rgba(16, 185, 129, 0.2)';
                if (text) text.textContent = (pct >= 0 ? '+' : '') + pct + '% Trend';
                if (icon) {
                    icon.innerHTML = '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>';
                }
            } else {
                badge.style.background = '#fef2f2';
                badge.style.color = '#ef4444';
                badge.style.borderColor = 'rgba(239, 68, 68, 0.2)';
                if (text) text.textContent = pct + '% Trend';
                if (icon) {
                    icon.innerHTML = '<polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/>';
                }
            }
        }

        // ==========================================
        // Helper: Interactive Dataset Legend Toggle
        // ==========================================
        window.toggleDataset = function(index, btnEl) {
            const chart = window.dashboardCharts.ordersOverview;
            if (!chart) return;
            const meta = chart.getDatasetMeta(index);
            meta.hidden = meta.hidden === null ? !chart.data.datasets[index].hidden : null;
            chart.update();

            if (btnEl) {
                btnEl.style.opacity = meta.hidden ? '0.35' : '1';
                btnEl.style.textDecoration = meta.hidden ? 'line-through' : 'none';
            }
        };

        // ==========================================
        // Helper: Synchronize Per-Card Dropdown Filters
        // ==========================================
        function syncCardSelectFilters(rangeKey) {
            const mapRangeToHuman = {
                'today': 'Today',
                'yesterday': 'Yesterday',
                'last_7_days': 'Last 7 Days',
                'this_month': 'This Month',
                'last_30_days': 'Last 30 Days',
                'last_month': 'Last Month',
                'this_year': 'This Year',
            };
            const targetLabel = mapRangeToHuman[rangeKey];

            document.querySelectorAll('.dash-filter-select[data-chart-target]').forEach(sel => {
                const optMatch = Array.from(sel.options).find(opt => opt.value === rangeKey || (targetLabel && opt.value === targetLabel));
                if (optMatch) {
                    sel.value = optMatch.value;
                }
            });
        }

        // ==========================================
        // 1. Chart: Orders Overview (Dual Axis with Gradient Spline)
        // ==========================================
        const ctxOrdersOverview = document.getElementById('chartOrdersOverview');
        if (ctxOrdersOverview) {
            const canvasCtx = ctxOrdersOverview.getContext('2d');

            // Rich vertical gradient fill for Revenue line
            const revenueGradient = canvasCtx.createLinearGradient(0, 0, 0, 240);
            revenueGradient.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
            revenueGradient.addColorStop(0.5, 'rgba(16, 185, 129, 0.08)');
            revenueGradient.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

            // Modern gradient for Orders bars
            const ordersBarGradient = canvasCtx.createLinearGradient(0, 0, 0, 240);
            ordersBarGradient.addColorStop(0, '#3B82F6');
            ordersBarGradient.addColorStop(1, '#60A5FA');

            const initLabels = {!! json_encode($chartData['ordersOverview']['labels'] ?? ['Oct 1', 'Oct 3', 'Oct 5', 'Oct 7', 'Oct 10', 'Oct 12', 'Oct 15', 'Oct 17', 'Oct 20', 'Oct 22', 'Oct 25', 'Oct 27', 'Oct 31']) !!};
            const initOrders = {!! json_encode($chartData['ordersOverview']['orders'] ?? [85, 110, 140, 95, 75, 105, 130, 160, 205, 185, 150, 165, 210]) !!};
            const initRevenue = {!! json_encode($chartData['ordersOverview']['revenue'] ?? [1500, 1750, 2200, 1800, 1600, 2100, 2800, 3100, 4200, 3400, 3100, 3600, 4600]) !!};

            window.dashboardCharts.ordersOverview = new Chart(ctxOrdersOverview, {
                data: {
                    labels: initLabels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Orders',
                            data: initOrders,
                            backgroundColor: ordersBarGradient,
                            hoverBackgroundColor: '#2563EB',
                            borderRadius: { topLeft: 6, topRight: 6, bottomLeft: 0, bottomRight: 0 },
                            barPercentage: 0.52,
                            categoryPercentage: 0.7,
                            yAxisID: 'yOrders',
                            order: 2,
                        },
                        {
                            type: 'line',
                            label: 'Revenue (USD)',
                            data: initRevenue,
                            borderColor: '#10B981',
                            backgroundColor: revenueGradient,
                            fill: true,
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 3.5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#10B981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointHoverBorderWidth: 3,
                            yAxisID: 'yRevenue',
                            order: 1,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.94)',
                            titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: '700', size: 12 },
                            bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
                            padding: 12,
                            cornerRadius: 8,
                            boxPadding: 4,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.dataset.yAxisID === 'yRevenue') {
                                        label += '$' + Number(context.parsed.y).toLocaleString();
                                    } else {
                                        label += Number(context.parsed.y).toLocaleString() + ' orders';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                autoSkip: true,
                                maxTicksLimit: window.innerWidth < 640 ? 6 : 14,
                                maxRotation: 0,
                                minRotation: 0,
                                font: { size: window.innerWidth < 640 ? 9 : 10, family: "'Plus Jakarta Sans', sans-serif" },
                                color: '#94a3b8'
                            }
                        },
                        yOrders: {
                            type: 'linear',
                            position: 'left',
                            min: 0,
                            ticks: {
                                stepSize: 50,
                                font: { size: window.innerWidth < 640 ? 9 : 10, family: "'Plus Jakarta Sans', sans-serif" },
                                color: '#94a3b8'
                            },
                            grid: { color: 'rgba(241, 245, 249, 0.8)' },
                        },
                        yRevenue: {
                            type: 'linear',
                            position: 'right',
                            min: 0,
                            ticks: {
                                stepSize: 1000,
                                callback: val => '$' + (val >= 1000 ? (val / 1000) + 'K' : val),
                                font: { size: window.innerWidth < 640 ? 9 : 10, family: "'Plus Jakarta Sans', sans-serif" },
                                color: '#94a3b8'
                            },
                            grid: { display: false }
                        }
                    }
                }
            });

            // Initialize dynamic trend badge
            updateOverviewTrendBadge(initRevenue);
        }

        // ==========================================
        // 2. Chart: Orders by Status Donut
        // ==========================================
        const ctxStatus = document.getElementById('chartOrdersByStatus');
        if (ctxStatus) {
            window.dashboardCharts.ordersByStatus = new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartData['ordersByStatus']['labels'] ?? ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled']) !!},
                    datasets: [{
                        data: {!! json_encode($chartData['ordersByStatus']['data'] ?? [124, 386, 248, 302, 168, 20]) !!},
                        backgroundColor: ['#F59E0B', '#3B82F6', '#8B5CF6', '#10B981', '#22C55E', '#EF4444'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        spacing: 2,
                        borderRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.94)',
                            cornerRadius: 8,
                            padding: 10,
                            callbacks: {
                                label: ctx => `${ctx.label}: ${ctx.raw} orders`
                            }
                        }
                    }
                }
            });
        }

        // ==========================================
        // 3. Chart: Payment Status Donut
        // ==========================================
        const ctxPayment = document.getElementById('chartPaymentStatus');
        if (ctxPayment) {
            window.dashboardCharts.paymentStatus = new Chart(ctxPayment, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartData['paymentStatus']['labels'] ?? ['Paid', 'Pending', 'Failed', 'Refunded']) !!},
                    datasets: [{
                        data: {!! json_encode($chartData['paymentStatus']['data'] ?? [16320, 1420, 430, 250]) !!},
                        backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#94A3B8'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        spacing: 2,
                        borderRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.94)',
                            cornerRadius: 8,
                            padding: 10,
                            callbacks: {
                                label: ctx => `${ctx.label}: $${Number(ctx.raw).toLocaleString()}`
                            }
                        }
                    }
                }
            });
        }

        // ==========================================
        // 4. Chart: Delivery Performance Ring
        // ==========================================
        const ctxDelivery = document.getElementById('chartDeliveryPerformance');
        if (ctxDelivery) {
            window.dashboardCharts.deliveryPerformance = new Chart(ctxDelivery, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartData['deliveryPerformance']['labels'] ?? ['On-time', 'Delayed', 'Failed']) !!},
                    datasets: [{
                        data: {!! json_encode($chartData['deliveryPerformance']['data'] ?? [1020, 48, 15]) !!},
                        backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        spacing: 2,
                        borderRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.94)',
                            cornerRadius: 8,
                            padding: 10
                        }
                    }
                }
            });
        }

        // ==========================================
        // 5. Chart: Student Participation Ring
        // ==========================================
        const ctxParticipation = document.getElementById('chartStudentParticipation');
        if (ctxParticipation) {
            window.dashboardCharts.studentParticipation = new Chart(ctxParticipation, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartData['studentParticipation']['labels'] ?? ['Ordered', 'Not Ordered']) !!},
                    datasets: [{
                        data: {!! json_encode($chartData['studentParticipation']['data'] ?? [6420, 2006]) !!},
                        backgroundColor: ['#2563EB', '#E2E8F0'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        spacing: 2,
                        borderRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.94)',
                            cornerRadius: 8,
                            padding: 10
                        }
                    }
                }
            });
        }

        // ==========================================
        // PER-CHART DYNAMIC FILTER HANDLER
        // ==========================================
        window.filterChartCard = function(selectEl) {
            const chartTarget = selectEl.getAttribute('data-chart-target');
            const selectedValue = selectEl.value;

            const rangeMap = {
                'Today': 'today',
                'Yesterday': 'yesterday',
                'Last 7 Days': 'last_7_days',
                'This Month': 'this_month',
                'Last 30 Days': 'last_30_days',
                'Last Month': 'last_month',
                'This Year': 'this_year',
            };

            const rangeKey = rangeMap[selectedValue] || selectedValue || 'this_month';
            const selectedText = selectEl.options[selectEl.selectedIndex]?.text || selectedValue;
            const cardEl = selectEl.closest('.dash-chart-card');
            const canvas = cardEl ? cardEl.querySelector('canvas') : null;

            if (canvas) {
                canvas.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
                canvas.style.opacity = '0.35';
                canvas.style.transform = 'scale(0.98)';
            }

            fetch(`/admin/dashboard?range=${encodeURIComponent(rangeKey)}&format=json`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.chartData) return;

                if (canvas) {
                    canvas.style.opacity = '1';
                    canvas.style.transform = 'scale(1)';
                }

                if (chartTarget === 'ordersOverview' && window.dashboardCharts.ordersOverview && data.chartData.ordersOverview) {
                    const co = data.chartData.ordersOverview;
                    window.dashboardCharts.ordersOverview.data.labels = co.labels;
                    window.dashboardCharts.ordersOverview.data.datasets[0].data = co.orders;
                    window.dashboardCharts.ordersOverview.data.datasets[1].data = co.revenue;

                    updateOverviewTrendBadge(co.revenue);

                    const maxOrders = Math.max(...co.orders, 10);
                    const maxRevenue = Math.max(...co.revenue, 100);
                    if (window.dashboardCharts.ordersOverview.options.scales.yOrders) {
                        window.dashboardCharts.ordersOverview.options.scales.yOrders.max = Math.ceil(maxOrders * 1.25);
                    }
                    if (window.dashboardCharts.ordersOverview.options.scales.yRevenue) {
                        window.dashboardCharts.ordersOverview.options.scales.yRevenue.max = Math.ceil(maxRevenue * 1.25);
                    }
                    window.dashboardCharts.ordersOverview.update();
                } else if (chartTarget === 'ordersByStatus' && window.dashboardCharts.ordersByStatus && data.chartData.ordersByStatus) {
                    window.dashboardCharts.ordersByStatus.data.datasets[0].data = data.chartData.ordersByStatus.data;
                    window.dashboardCharts.ordersByStatus.update();
                    const centerTotal = document.querySelector('#chartOrdersByStatus')?.parentElement?.querySelector('span[style*="font-weight: 800"]');
                    if (centerTotal) {
                        centerTotal.textContent = data.chartData.ordersByStatus.total || data.stats.total_orders;
                    }
                } else if (chartTarget === 'paymentStatus' && window.dashboardCharts.paymentStatus && data.chartData.paymentStatus) {
                    window.dashboardCharts.paymentStatus.data.datasets[0].data = data.chartData.paymentStatus.data;
                    window.dashboardCharts.paymentStatus.update();
                    const centerRev = document.querySelector('#chartPaymentStatus')?.parentElement?.querySelector('span[style*="font-weight: 800"]');
                    if (centerRev) {
                        centerRev.textContent = data.chartData.paymentStatus.total || data.stats.revenue_usd;
                    }
                } else if (chartTarget === 'deliveryPerformance' && window.dashboardCharts.deliveryPerformance && data.chartData.deliveryPerformance) {
                    window.dashboardCharts.deliveryPerformance.data.datasets[0].data = data.chartData.deliveryPerformance.data;
                    window.dashboardCharts.deliveryPerformance.update();
                } else if (chartTarget === 'studentParticipation' && window.dashboardCharts.studentParticipation && data.chartData.studentParticipation) {
                    window.dashboardCharts.studentParticipation.data.datasets[0].data = data.chartData.studentParticipation.data;
                    window.dashboardCharts.studentParticipation.update();
                }

                const cardTitle = selectEl.closest('.dash-chart-card')?.querySelector('.dash-card-title')?.textContent?.trim() || 'Chart';
                if (window.Toast) {
                    window.Toast.success(`${cardTitle}: ${selectedText}`);
                }
            })
            .catch(err => {
                console.error('Card filter error:', err);
                if (canvas) {
                    canvas.style.opacity = '1';
                    canvas.style.transform = 'scale(1)';
                }
            });
        };

        // ==========================================
        // DYNAMIC DASHBOARD FILTER LOGIC (Full Page Synchronizer)
        // ==========================================
        window.applyDashboardDateFilter = function(rangeKey, labelText, startDate, endDate, skipHistory = false) {
            // Update browser URL query params without reloading page
            if (!skipHistory) {
                const url = new URL(window.location.href);
                url.searchParams.set('range', rangeKey);
                if (startDate && endDate) {
                    url.searchParams.set('start_date', startDate);
                    url.searchParams.set('end_date', endDate);
                } else {
                    url.searchParams.delete('start_date');
                    url.searchParams.delete('end_date');
                }
                window.history.pushState({ range: rangeKey }, '', url.toString());
            }

            // Visual feedback: Subtle transition on KPI values & charts
            document.querySelectorAll('.kpi-value').forEach(el => {
                el.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
                el.style.opacity = '0.35';
                el.style.transform = 'translateY(-2px)';
            });

            // Build request URL for JSON API endpoint
            let fetchUrl = `/admin/dashboard?range=${encodeURIComponent(rangeKey)}&format=json`;
            if (startDate && endDate) {
                fetchUrl += `&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;
            }

            // Fetch dynamic filtered data from backend service
            fetch(fetchUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data) return;

                // 1. Update all 8 Key Insights KPI Cards
                const kpiKeys = [
                    'total_orders', 'revenue_usd', 'active_schools', 'total_students',
                    'partner_restaurants', 'ontime_delivery_pct', 'meals_served', 'satisfaction_rate'
                ];

                kpiKeys.forEach(key => {
                    const card = document.querySelector(`[data-kpi="${key}"]`);
                    if (card && data.stats && data.stats[key] !== undefined) {
                        const valEl = card.querySelector('.kpi-value');
                        if (valEl) {
                            setTimeout(() => {
                                valEl.textContent = data.stats[key];
                                valEl.style.opacity = '1';
                                valEl.style.transform = 'translateY(0)';
                            }, 100);
                        }

                        const changeEl = card.querySelector('.kpi-change');
                        const periodEl = card.querySelector('.kpi-period');
                        const trendBox = card.querySelector('.kpi-trend-box');
                        const trend = data.stats[key + '_trend'] || 'up';
                        const change = data.stats[key + '_change'];
                        const period = data.stats[key + '_period'];

                        if (changeEl && change) {
                            changeEl.textContent = (trend === 'up' ? '↑ ' : '↓ ') + change;
                        }
                        if (trendBox) {
                            trendBox.style.color = trend === 'up' ? '#10B981' : '#EF4444';
                        }
                        if (periodEl && period) {
                            periodEl.textContent = period;
                        }

                        // Reactive Sparkline Wave & Glow Tip Update
                        const sparkLine = card.querySelector('.kpi-sparkline-line');
                        const sparkArea = card.querySelector('.kpi-sparkline-area');
                        const sparkDot = card.querySelector('.kpi-sparkline-dot');
                        const sparkPulse = card.querySelector('.kpi-sparkline-pulse');
                        const defaultStroke = card.getAttribute('data-stroke') || '#3B82F6';
                        const strokeColor = trend === 'up' ? defaultStroke : '#EF4444';

                        if (sparkLine && sparkArea) {
                            const isUp = (trend === 'up');
                            const linePath = isUp
                                ? 'M 0 22 C 20 22, 35 26, 50 16 C 65 7, 80 13, 98 5'
                                : 'M 0 7 C 20 7, 35 5, 50 15 C 65 24, 80 19, 98 25';
                            const areaPath = isUp
                                ? 'M 0 22 C 20 22, 35 26, 50 16 C 65 7, 80 13, 98 5 L 98 29 L 0 29 Z'
                                : 'M 0 7 C 20 7, 35 5, 50 15 C 65 24, 80 19, 98 25 L 98 29 L 0 29 Z';
                            const endY = isUp ? 5 : 25;

                            sparkLine.setAttribute('d', linePath);
                            sparkLine.setAttribute('stroke', strokeColor);
                            sparkArea.setAttribute('d', areaPath);

                            // Update SVG Gradient Stops
                            const gradStops = card.querySelectorAll('.kpi-sparkline-gradient stop');
                            gradStops.forEach(stop => {
                                stop.setAttribute('stop-color', strokeColor);
                            });

                            if (sparkDot) {
                                sparkDot.setAttribute('cy', endY);
                                sparkDot.setAttribute('fill', strokeColor);
                            }
                            if (sparkPulse) {
                                sparkPulse.setAttribute('cy', endY);
                                sparkPulse.setAttribute('fill', strokeColor);
                            }
                        }
                    }
                });

                // 2. Update Major Orders Overview Chart
                if (window.dashboardCharts.ordersOverview && data.chartData && data.chartData.ordersOverview) {
                    const co = data.chartData.ordersOverview;
                    window.dashboardCharts.ordersOverview.data.labels = co.labels;
                    window.dashboardCharts.ordersOverview.data.datasets[0].data = co.orders;
                    window.dashboardCharts.ordersOverview.data.datasets[1].data = co.revenue;

                    updateOverviewTrendBadge(co.revenue);

                    const maxOrders = Math.max(...co.orders, 10);
                    const maxRevenue = Math.max(...co.revenue, 100);
                    if (window.dashboardCharts.ordersOverview.options.scales.yOrders) {
                        window.dashboardCharts.ordersOverview.options.scales.yOrders.max = Math.ceil(maxOrders * 1.25);
                    }
                    if (window.dashboardCharts.ordersOverview.options.scales.yRevenue) {
                        window.dashboardCharts.ordersOverview.options.scales.yRevenue.max = Math.ceil(maxRevenue * 1.25);
                    }
                    window.dashboardCharts.ordersOverview.update();
                }

                // 3. Update Orders by Status Donut
                if (window.dashboardCharts.ordersByStatus && data.chartData && data.chartData.ordersByStatus) {
                    window.dashboardCharts.ordersByStatus.data.datasets[0].data = data.chartData.ordersByStatus.data;
                    window.dashboardCharts.ordersByStatus.update();

                    const centerTotal = document.querySelector('#chartOrdersByStatus')?.parentElement?.querySelector('span[style*="font-weight: 800"]');
                    if (centerTotal) {
                        centerTotal.textContent = data.chartData.ordersByStatus.total || data.stats.total_orders;
                    }
                }

                // 4. Update Payment Status Donut
                if (window.dashboardCharts.paymentStatus && data.chartData && data.chartData.paymentStatus) {
                    window.dashboardCharts.paymentStatus.data.datasets[0].data = data.chartData.paymentStatus.data;
                    window.dashboardCharts.paymentStatus.update();

                    const centerRev = document.querySelector('#chartPaymentStatus')?.parentElement?.querySelector('span[style*="font-weight: 800"]');
                    if (centerRev) {
                        centerRev.textContent = data.chartData.paymentStatus.total || data.stats.revenue_usd;
                    }
                }

                // 5. Update Delivery Performance Ring
                if (window.dashboardCharts.deliveryPerformance && data.chartData && data.chartData.deliveryPerformance) {
                    window.dashboardCharts.deliveryPerformance.data.datasets[0].data = data.chartData.deliveryPerformance.data;
                    window.dashboardCharts.deliveryPerformance.update();
                }

                // 6. Update Student Participation Ring
                if (window.dashboardCharts.studentParticipation && data.chartData && data.chartData.studentParticipation) {
                    window.dashboardCharts.studentParticipation.data.datasets[0].data = data.chartData.studentParticipation.data;
                    window.dashboardCharts.studentParticipation.update();
                }

                // 7. Synchronize Card Dropdown Selects with active filter
                syncCardSelectFilters(rangeKey);

                // 8. Update Top Restaurants Table
                const restBody = document.getElementById('topRestaurantsBody');
                if (restBody && data.topRestaurants) {
                    restBody.innerHTML = data.topRestaurants.map((r, i) => `
                        <tr style="border-bottom: 1px solid #f8fafc; font-weight: 600;">
                            <td style="padding: 0.55rem 0.25rem; color: #94a3b8;">${i + 1}</td>
                            <td style="padding: 0.55rem 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <img src="${r.img || '/images/food_dish_1.jpg'}" alt="${r.name}" style="width: 26px; height: 26px; border-radius: 6px; object-fit: cover;" onerror="this.src='/images/food_dish_1.jpg'">
                                    <span style="color: #0f172a; white-space: nowrap;">${r.name}</span>
                                </div>
                            </td>
                            <td style="padding: 0.55rem 0.5rem; text-align: right; color: #0f172a; font-weight: 700;">${r.orders}</td>
                            <td style="padding: 0.55rem 0.5rem; text-align: right; color: #64748b;">${r.revenue}</td>
                            <td style="padding: 0.55rem 0.25rem; text-align: right;">
                                <span style="background: #ecfdf5; color: #059669; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.7rem; font-weight: 700;">${r.ontime}</span>
                            </td>
                        </tr>
                    `).join('');
                }

                // 9. Update Top Schools Table
                const schoolsBody = document.getElementById('topSchoolsBody');
                if (schoolsBody && data.topSchools) {
                    schoolsBody.innerHTML = data.topSchools.map((s, i) => `
                        <tr style="border-bottom: 1px solid #f8fafc; font-weight: 600;">
                            <td style="padding: 0.55rem 0.25rem; color: #94a3b8;">${i + 1}</td>
                            <td style="padding: 0.55rem 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="width: 26px; height: 26px; border-radius: 6px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 800; flex-shrink: 0;">
                                        🏫
                                    </div>
                                    <span style="color: #0f172a; white-space: nowrap;">${s.name}</span>
                                </div>
                            </td>
                            <td style="padding: 0.55rem 0.5rem; text-align: right; color: #0f172a; font-weight: 700;">${s.orders}</td>
                            <td style="padding: 0.55rem 0.5rem; text-align: right; color: #64748b;">${s.students}</td>
                            <td style="padding: 0.55rem 0.25rem; text-align: right;">
                                <span style="background: ${parseInt(s.participation) > 75 ? '#ecfdf5' : '#fffbeb'}; color: ${parseInt(s.participation) > 75 ? '#059669' : '#d97706'}; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.7rem; font-weight: 700;">${s.participation}</span>
                            </td>
                        </tr>
                    `).join('');
                }

                // 10. Update Recent Orders Table
                const ordersBody = document.getElementById('recentOrdersBody');
                if (ordersBody && data.recentOrders) {
                    ordersBody.innerHTML = data.recentOrders.map((o, i) => `
                        <tr style="border-bottom: 1px solid #f8fafc; font-weight: 600;">
                            <td style="padding: 0.55rem 0.25rem; color: #94a3b8;">${i + 1}</td>
                            <td style="padding: 0.55rem 0.4rem; color: #2563EB; font-weight: 700; white-space: nowrap;">${o.id}</td>
                            <td style="padding: 0.55rem 0.4rem; color: #0f172a; white-space: nowrap;">${o.student}</td>
                            <td style="padding: 0.55rem 0.4rem; color: #64748b; white-space: nowrap;">${o.school}</td>
                            <td style="padding: 0.55rem 0.4rem;">
                                <span style="display: inline-block; padding: 0.2rem 0.55rem; border-radius: 6px; font-size: 0.7rem; font-weight: 700; background: ${o.status === 'Delivered' ? '#ecfdf5; color: #059669;' : (o.status === 'Out for Delivery' ? '#eff6ff; color: #1d4ed8;' : '#fffbeb; color: #d97706;')}">${o.status}</span>
                            </td>
                            <td style="padding: 0.55rem 0.4rem; text-align: right; color: #0f172a; font-weight: 700; white-space: nowrap;">${o.amount}</td>
                            <td style="padding: 0.55rem 0.25rem; text-align: right; color: #94a3b8; white-space: nowrap;">${o.time}</td>
                        </tr>
                    `).join('');
                }

                // 11. Display Floating Toast notification
                if (window.Toast) {
                    window.Toast.success('Dashboard data updated for: ' + (data.activeLabel || labelText));
                }
            })
            .catch(err => {
                console.error('Filter update error:', err);
                document.querySelectorAll('.kpi-value').forEach(el => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                });
                if (window.Toast) {
                    window.Toast.info('Displaying updated view for ' + labelText);
                }
            });
        };

        // Browser Back/Forward navigation support
        window.addEventListener('popstate', function() {
            const url = new URL(window.location.href);
            const range = url.searchParams.get('range') || 'this_month';
            const start = url.searchParams.get('start_date');
            const end = url.searchParams.get('end_date');
            if (typeof window.applyDashboardDateFilter === 'function') {
                window.applyDashboardDateFilter(range, range, start, end, true);
            }
        });

        // ==========================================
        // 6. Interactive Live Delivery Map (Siem Reap)
        // ==========================================
        if (typeof L !== 'undefined' && document.getElementById('siemReapMap')) {
            const map = L.map('siemReapMap', {
                center: [13.3633, 103.8564],
                zoom: 13,
                zoomControl: false,
                attributionControl: false
            });
            window.leafletMapInstance = map;

            // Modern Light Map Tiles
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
            }).addTo(map);

            // Custom SVG Icons
            const createCustomIcon = (color, emoji) => L.divIcon({
                className: 'custom-map-pin',
                html: `<div style="background:${color}; width:28px; height:28px; border-radius:50%; border:2px solid #ffffff; box-shadow:0 3px 8px rgba(0,0,0,0.25); display:flex; align-items:center; justify-content:center; color:#ffffff; font-size:12px;">${emoji}</div>`,
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });

            // Add sample Schools
            L.marker([13.368, 103.860], { icon: createCustomIcon('#2563EB', '🏫') }).addTo(map).bindPopup('Angkor Primary School');
            L.marker([13.355, 103.864], { icon: createCustomIcon('#2563EB', '🏫') }).addTo(map).bindPopup('Wat Bo Primary School');
            L.marker([13.372, 103.848], { icon: createCustomIcon('#2563EB', '🏫') }).addTo(map).bindPopup('Siem Reap High School');

            // Add sample Restaurants
            L.marker([13.358, 103.854], { icon: createCustomIcon('#EA580C', '🍽️') }).addTo(map).bindPopup('Khmer Kitchen');
            L.marker([13.365, 103.845], { icon: createCustomIcon('#EA580C', '🥗') }).addTo(map).bindPopup('Healthy Food Co.');

            // Add sample Drivers
            L.marker([13.362, 103.858], { icon: createCustomIcon('#10B981', '🚚') }).addTo(map).bindPopup('Driver Sokha (In Transit)');
            L.marker([13.352, 103.851], { icon: createCustomIcon('#10B981', '🛵') }).addTo(map).bindPopup('Driver Vanna (Delivering)');

            // Observe map resizing/visibility changes to keep tiles sharp
            if (window.ResizeObserver) {
                const mapObserver = new ResizeObserver(() => {
                    map.invalidateSize();
                });
                mapObserver.observe(document.getElementById('siemReapMap'));
            }
        }

        // ==========================================
        // 7. Generic Meta-Style Scrollable Rail Controller
        // ==========================================
        function createScrollableRail({ trackId, prevBtnId, nextBtnId, fadeLeftId, fadeRightId, stepOffset = 360, onScroll }) {
            const track = document.getElementById(trackId);
            const prevBtn = document.getElementById(prevBtnId);
            const nextBtn = document.getElementById(nextBtnId);
            const fadeLeft = document.getElementById(fadeLeftId);
            const fadeRight = document.getElementById(fadeRightId);

            if (!track) return null;

            function updateRailState() {
                const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
                const currentScroll = track.scrollLeft;
                const hasOverflow = maxScroll > 8;

                // Update Chevron buttons disabled state
                if (prevBtn) {
                    if (currentScroll <= 8 || !hasOverflow) {
                        prevBtn.classList.add('disabled');
                        prevBtn.disabled = true;
                    } else {
                        prevBtn.classList.remove('disabled');
                        prevBtn.disabled = false;
                    }
                }

                if (nextBtn) {
                    if (currentScroll >= maxScroll - 8 || !hasOverflow) {
                        nextBtn.classList.add('disabled');
                        nextBtn.disabled = true;
                    } else {
                        nextBtn.classList.remove('disabled');
                        nextBtn.disabled = false;
                    }
                }

                // Update edge gradient masks
                if (fadeLeft) fadeLeft.style.opacity = (hasOverflow && currentScroll > 15) ? '1' : '0';
                if (fadeRight) fadeRight.style.opacity = (hasOverflow && currentScroll < maxScroll - 15) ? '1' : '0';

                if (typeof onScroll === 'function') {
                    onScroll({ currentScroll, maxScroll, hasOverflow });
                }
            }

            // Smooth scroll by direction
            function scrollByDirection(direction) {
                const delta = direction === 'left' ? -stepOffset : stepOffset;
                track.scrollBy({ left: delta, behavior: 'smooth' });
                setTimeout(updateRailState, 320);
            }

            // Desktop Mouse Drag-To-Scroll (Meta-style)
            let isDragging = false;
            let startPageX = 0;
            let initialScrollLeft = 0;
            let dragDistance = 0;

            track.addEventListener('mousedown', (e) => {
                if (e.target.closest('input, select, button, a')) return;
                isDragging = true;
                dragDistance = 0;
                track.classList.add('is-dragging');
                startPageX = e.pageX - track.offsetLeft;
                initialScrollLeft = track.scrollLeft;
            });

            track.addEventListener('mouseleave', () => {
                if (!isDragging) return;
                isDragging = false;
                track.classList.remove('is-dragging');
                updateRailState();
            });

            track.addEventListener('mouseup', () => {
                if (!isDragging) return;
                isDragging = false;
                track.classList.remove('is-dragging');
                updateRailState();
            });

            track.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const currentX = e.pageX - track.offsetLeft;
                const walk = (currentX - startPageX) * 1.35;
                dragDistance = Math.abs(walk);
                track.scrollLeft = initialScrollLeft - walk;
            });

            // Prevent accidental click triggers during a drag gesture
            track.addEventListener('click', (e) => {
                if (dragDistance > 6) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);

            // Native listeners
            track.addEventListener('scroll', updateRailState, { passive: true });
            window.addEventListener('resize', updateRailState, { passive: true });

            setTimeout(updateRailState, 150);

            return {
                scroll: scrollByDirection,
                update: updateRailState,
                track: track
            };
        }

        // Initialize Row 1: Key Insights Rail
        const insightsRail = createScrollableRail({
            trackId: 'insightsTrack',
            prevBtnId: 'insightsPrevBtn',
            nextBtnId: 'insightsNextBtn',
            fadeLeftId: 'insightsFadeLeft',
            fadeRightId: 'insightsFadeRight',
            stepOffset: 480
        });
        window.scrollInsights = function(direction) {
            insightsRail?.scroll(direction);
        };

        // Initialize Row 2: Visual Analytics & Charts Rail
        const graphsRail = createScrollableRail({
            trackId: 'graphsTrack',
            prevBtnId: 'graphsPrevBtn',
            nextBtnId: 'graphsNextBtn',
            fadeLeftId: 'graphsFadeLeft',
            fadeRightId: 'graphsFadeRight',
            stepOffset: 460,
            onScroll: function() {
                if (window.leafletMapInstance) {
                    window.leafletMapInstance.invalidateSize();
                }
            }
        });
        window.scrollGraphs = function(direction) {
            graphsRail?.scroll(direction);
            setTimeout(() => {
                if (window.leafletMapInstance) window.leafletMapInstance.invalidateSize();
            }, 320);
        };

        // Initialize Row 3: Operations & Leaderboard Lists Rail
        const listsRail = createScrollableRail({
            trackId: 'listsTrack',
            prevBtnId: 'listsPrevBtn',
            nextBtnId: 'listsNextBtn',
            fadeLeftId: 'listsFadeLeft',
            fadeRightId: 'listsFadeRight',
            stepOffset: 420
        });
        window.scrollLists = function(direction) {
            listsRail?.scroll(direction);
        };

        // Activity category tab filter handler
        window.filterActivities = function(type, btnEl) {
            document.querySelectorAll('.activity-pill-btn').forEach(b => {
                b.style.background = '#f8fafc';
                b.style.color = '#64748b';
                b.style.borderColor = '#e2e8f0';
            });
            if (btnEl) {
                btnEl.style.background = '#2563EB';
                btnEl.style.color = '#ffffff';
                btnEl.style.borderColor = '#2563EB';
            }
            document.querySelectorAll('.activity-item-row').forEach(row => {
                const rowType = row.getAttribute('data-activity-type');
                if (type === 'all' || rowType === type || (type === 'operations' && (rowType === 'truck' || rowType === 'store' || rowType === 'school'))) {
                    row.style.display = 'flex';
                } else {
                    row.style.display = 'none';
                }
            });
        };

        // Initialize Row 4: Operational Health & Live Stream Rail
        const gaugesRail = createScrollableRail({
            trackId: 'gaugesTrack',
            prevBtnId: 'gaugesPrevBtn',
            nextBtnId: 'gaugesNextBtn',
            fadeLeftId: 'gaugesFadeLeft',
            fadeRightId: 'gaugesFadeRight',
            stepOffset: 340
        });
        window.scrollGauges = function(direction) {
            gaugesRail?.scroll(direction);
        };

    });
</script>
@endpush
