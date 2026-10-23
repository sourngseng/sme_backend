@extends('layouts.master_super_admin')

@section('title', transLabel('settings.title', 'Platform System Settings & Operational Governance | SchoolMeal'))

@section('content')
@include('profile.partials.styles')

<style>
    /* Executive System Settings Hero */
    .settings-hero {
        position: relative;
        background: linear-gradient(135deg, #090d16 0%, #0f172a 35%, #1e1b4b 75%, #064e3b 100%);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 16px 40px -10px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.08) inset;
        border-radius: 20px;
        padding: 2.25rem 2rem 1.75rem 2rem;
        color: #ffffff;
        overflow: hidden;
        margin-bottom: 1.75rem;
    }
    .settings-hero-glow {
        position: absolute;
        top: -60px;
        right: -60px;
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, rgba(16, 185, 129, 0.15) 50%, rgba(99, 102, 241, 0) 70%);
        pointer-events: none;
        border-radius: 50%;
    }
    .settings-hero-pattern {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1.5px, transparent 1.5px);
        background-size: 24px 24px;
        pointer-events: none;
        opacity: 0.6;
    }
    .settings-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.75rem;
        flex-wrap: wrap;
    }
    .settings-hero-left {
        flex: 1;
        min-width: 300px;
    }
    .settings-hero-title-row {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }
    .settings-hero-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.02em;
        margin: 0;
    }
    .settings-hero-sub {
        color: #cbd5e1;
        font-size: 0.875rem;
        line-height: 1.5;
        max-width: 720px;
    }
    .settings-hero-badges {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: wrap;
        margin-top: 0.85rem;
    }
    .settings-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    .badge-clearance {
        background: rgba(99, 102, 241, 0.2);
        color: #c7d2fe;
        border: 1px solid rgba(165, 180, 252, 0.35);
    }
    .badge-cluster {
        background: rgba(16, 185, 129, 0.18);
        color: #6ee7b7;
        border: 1px solid rgba(110, 231, 183, 0.35);
    }
    .badge-maintenance {
        background: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
        border: 1px solid rgba(252, 165, 165, 0.35);
    }
    .settings-hero-stats {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        background: rgba(255, 255, 255, 0.07);
        backdrop-filter: blur(12px);
        padding: 0.85rem 1.25rem;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }
    .hero-stat-cell {
        text-align: center;
        padding: 0 0.5rem;
    }
    .hero-stat-cell:not(:last-child) {
        border-right: 1px solid rgba(255, 255, 255, 0.12);
        padding-right: 1.25rem;
    }
    .hero-stat-number {
        font-size: 1.3rem;
        font-weight: 800;
        color: #ffffff;
    }
    .hero-stat-caption {
        font-size: 0.7rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 600;
        margin-top: 2px;
    }

    /* Maintenance Actions Grid */
    .maintenance-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .maint-action-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .maint-action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    .maint-card-top {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
    }
    .maint-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .maint-card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }
    .maint-card-desc {
        font-size: 0.775rem;
        color: #64748b;
        line-height: 1.4;
    }

    /* Diagnostics Table */
    .diag-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.825rem;
    }
    .diag-table tr {
        border-bottom: 1px solid #f1f5f9;
    }
    .diag-table tr:last-child {
        border-bottom: none;
    }
    .diag-table td {
        padding: 0.75rem 0.5rem;
        color: #334155;
    }
    .diag-table td.label-col {
        font-weight: 600;
        color: #64748b;
        width: 40%;
    }
    .diag-table td.val-col {
        font-family: monospace;
        font-weight: 700;
        color: #0f172a;
    }

    /* Zero Scrollbar Enforcement */
    .profile-tabs-nav,
    .settings-tabs-list,
    .settings-nav-rail,
    .settings-workspace {
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }
    .profile-tabs-nav::-webkit-scrollbar,
    .settings-tabs-list::-webkit-scrollbar,
    .settings-nav-rail::-webkit-scrollbar,
    .settings-workspace::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }

    /* Settings Toolbar & View Switcher */
    .settings-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }
    .settings-toolbar-left {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }
    .settings-toolbar-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .settings-toolbar-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.01em;
    }
    .settings-toolbar-pill {
        font-size: 0.75rem;
        font-weight: 700;
        color: #4f46e5;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
    }
    .settings-layout-toggle {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        padding: 3px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        gap: 3px;
    }
    .layout-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.85rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .layout-toggle-btn:hover {
        color: #0f172a;
        background: #f8fafc;
    }
    .layout-toggle-btn.active {
        color: #4338ca;
        background: #eef2ff;
        font-weight: 700;
        box-shadow: 0 1px 2px rgba(79, 70, 229, 0.08);
    }

    /* Settings Workspace Layouts */
    .settings-workspace {
        width: 100%;
        transition: all 0.25s ease;
    }

    /* 1. Vertical Mode (Default) */
    .settings-workspace.layout-vertical {
        display: grid;
        grid-template-columns: 310px 1fr;
        gap: 1.5rem;
        align-items: start;
    }
    .settings-workspace.layout-vertical .settings-nav-rail {
        position: sticky;
        top: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .settings-workspace.layout-vertical .settings-tabs-list {
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
    }
    .settings-workspace.layout-vertical .settings-tab-item {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.85rem 1rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        text-align: left;
        cursor: pointer;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        width: 100%;
        position: relative;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .settings-workspace.layout-vertical .settings-tab-item:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateX(3px);
    }
    .settings-workspace.layout-vertical .settings-tab-item.active {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 4px 18px rgba(99, 102, 241, 0.12);
        border-left: 4.5px solid #4f46e5;
    }
    .settings-workspace.layout-vertical .settings-tab-item.active .tab-primary-label {
        color: #4338ca;
        font-weight: 500;
    }
    .settings-workspace.layout-vertical .settings-tab-item.active .tab-indicator-arrow {
        color: #4f46e5;
        transform: translateX(2px);
    }
    .settings-workspace.layout-vertical .settings-tab-item.active .tab-indicator-arrow svg {
        stroke: #4f46e5 !important;
    }
    .settings-workspace.layout-vertical .settings-content-area {
        min-width: 0;
    }

    /* 2. Horizontal Mode */
    .settings-workspace.layout-horizontal {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .settings-workspace.layout-horizontal .settings-nav-rail {
        width: 100%;
    }
    .settings-workspace.layout-horizontal .settings-tabs-list {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 0.55rem;
        background: #ffffff;
        padding: 0.65rem;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .settings-workspace.layout-horizontal .settings-tab-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 0.4rem;
        padding: 0.75rem 0.5rem;
        border-radius: 12px;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: all 0.15s ease;
        width: 100%;
    }
    .settings-workspace.layout-horizontal .settings-tab-item:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    .settings-workspace.layout-horizontal .settings-tab-item.active {
        background: #eef2ff;
        border-color: #c7d2fe;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.08);
    }
    .settings-workspace.layout-horizontal .settings-tab-item.active .tab-primary-label {
        color: #4338ca;
        font-weight: 500;
    }
    .settings-workspace.layout-horizontal .tab-secondary-caption,
    .settings-workspace.layout-horizontal .tab-indicator-arrow,
    .settings-workspace.layout-horizontal .settings-rail-health {
        display: none;
    }

    /* Tab Inner Element Styles */
    .tab-icon-wrap {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    .tab-icon-wrap svg,
    .settings-tab-item .tab-icon-wrap svg,
    .settings-tab-item.active .tab-icon-wrap svg,
    .profile-tab-btn.active .tab-icon-wrap svg {
        stroke: currentColor !important;
        stroke-width: 2.2 !important;
    }
    .icon-indigo { background: #eef2ff; color: #4338ca; }
    .icon-amber { background: #fef3c7; color: #b45309; }
    .icon-emerald { background: #d1fae5; color: #047857; }
    .icon-blue { background: #dbeafe; color: #1d4ed8; }
    .icon-purple { background: #f3e8ff; color: #7e22ce; }
    .icon-rose { background: #ffe4e6; color: #be123c; }

    .settings-tab-item.active .tab-icon-wrap.icon-indigo { background: #e0e7ff; color: #3730a3; box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.25); }
    .settings-tab-item.active .tab-icon-wrap.icon-amber { background: #fef08a; color: #92400e; box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25); }
    .settings-tab-item.active .tab-icon-wrap.icon-emerald { background: #a7f3d0; color: #065f46; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25); }
    .settings-tab-item.active .tab-icon-wrap.icon-blue { background: #bfdbfe; color: #1e40af; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25); }
    .settings-tab-item.active .tab-icon-wrap.icon-purple { background: #e9d5ff; color: #6b21a8; box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.25); }
    .settings-tab-item.active .tab-icon-wrap.icon-rose { background: #fecdd3; color: #9f1239; box-shadow: 0 0 0 2px rgba(244, 63, 94, 0.25); }

    .tab-meta-wrap {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }
    .tab-primary-label {
        font-size: 1.1rem;
        font-weight: 500;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .tab-secondary-caption {
        font-size: 0.72rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 2px;
    }
    .tab-indicator-arrow {
        color: #cbd5e1;
        display: flex;
        align-items: center;
        transition: all 0.15s ease;
    }

    /* Vertical Rail Health Card */
    .settings-rail-health {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.15rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .health-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.4rem;
    }
    .health-dot-pulse {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
        animation: pulseDot 2s infinite ease-in-out;
    }
    .health-title {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
    }
    .health-status-text {
        font-size: 0.88rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.75rem;
    }
    .health-metrics-row {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.85rem;
    }
    .health-metric {
        flex: 1;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem;
        text-align: center;
    }
    .metric-val {
        display: block;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
    }
    .metric-lbl {
        display: block;
        font-size: 0.68rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
    }
    .btn-rail-reset {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-rail-reset:hover {
        background: #fee2e2;
        color: #b91c1c;
        border-color: #fca5a5;
    }

    @keyframes pulseDot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.9); }
    }

    @media (max-width: 991px) {
        .settings-workspace.layout-vertical {
            grid-template-columns: 1fr;
        }
        .settings-workspace.layout-vertical .settings-nav-rail {
            position: static;
        }
        .settings-workspace.layout-horizontal .settings-tabs-list {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 640px) {
        .settings-workspace.layout-horizontal .settings-tabs-list {
            grid-template-columns: repeat(2, 1fr);
        }
        .tab-secondary-caption {
            display: none;
        }
    }
</style>

<div class="profile-page-wrapper">

    <!-- 1. EXECUTIVE HERO BANNER -->
    <div class="settings-hero">
        <div class="settings-hero-pattern"></div>
        <div class="settings-hero-glow"></div>
        <div class="settings-hero-content">
            <div class="settings-hero-left">
                <div class="settings-hero-title-row">
                    <h1 class="settings-hero-title">{{ transLabel('settings.title', 'Platform System Settings & Operational Governance') }}</h1>
                </div>
                <div class="settings-hero-sub">
                    {{ transLabel('settings.subtitle', 'Configure nationwide school meal parameters, order cutoff timings, escrow commissions, security policies & cache controls') }}
                </div>
                <div class="settings-hero-badges">
                    <span class="settings-badge badge-clearance">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        {{ transLabel('profile.clearance_level_0', 'Level 0 - Master Root Clearance') }}
                    </span>
                    <span class="settings-badge badge-cluster">
                        <span class="status-pulse-dot admin-live-pulse"></span>
                        {{ transLabel('settings.cluster_online', 'Production Cluster Online') }}
                    </span>
                    @if($stats['maintenance_mode'])
                        <span class="settings-badge badge-maintenance">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            {{ transLabel('settings.maintenance_active', 'Maintenance Mode Active') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Platform Runtime Quick Stats -->
            <div class="settings-hero-stats">
                <div class="hero-stat-cell">
                    <div class="hero-stat-number">{{ $stats['total_settings'] }}</div>
                    <div class="hero-stat-caption">{{ transLabel('settings.total_parameters', 'Parameters') }}</div>
                </div>
                <div class="hero-stat-cell">
                    <div class="hero-stat-number" style="color: #60a5fa;">{{ $stats['currency'] }}</div>
                    <div class="hero-stat-caption">{{ transLabel('settings.currency', 'Currency') }}</div>
                </div>
                <div class="hero-stat-cell">
                    <div class="hero-stat-number" style="color: #34d399;">1:{{ number_format($stats['exchange_rate']) }}</div>
                    <div class="hero-stat-caption">USD &bull; KHR</div>
                </div>
                <div class="hero-stat-cell">
                    <div class="hero-stat-number" style="color: #a78bfa;">Asia/PP</div>
                    <div class="hero-stat-caption">Timezone</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TOOLBAR WITH DOMAINS TITLE & VIEW SWITCHER (VERTICAL / HORIZONTAL) -->
    <div class="settings-toolbar">
        <div class="settings-toolbar-left">
            <span class="settings-toolbar-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="18" x="3" y="3" rx="1"/><rect width="7" height="18" x="14" y="3" rx="1"/></svg>
            </span>
            <span class="settings-toolbar-title">{{ transLabel('settings.domains_title', 'Operational Domains') }}</span>
            <span class="settings-toolbar-pill">{{ $stats['total_settings'] }} {{ transLabel('settings.total_parameters', 'Parameters') }}</span>
        </div>
        <div class="settings-layout-toggle" role="group" aria-label="Layout View">
            <button type="button" class="layout-toggle-btn active" id="btnLayoutVertical" onclick="setSettingsLayout('vertical')" title="Vertical Sidebar Layout">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="18" x="3" y="3" rx="1"/><rect width="11" height="18" x="14" y="3" rx="1"/></svg>
                <span>{{ transLabel('settings.layout_vertical', 'Vertical Sidebar') }}</span>
            </button>
            <button type="button" class="layout-toggle-btn" id="btnLayoutHorizontal" onclick="setSettingsLayout('horizontal')" title="Horizontal Grid Layout">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="7" x="3" y="3" rx="1"/><rect width="18" height="11" x="3" y="14" rx="1"/></svg>
                <span>{{ transLabel('settings.layout_horizontal', 'Horizontal Grid') }}</span>
            </button>
        </div>
    </div>

    <!-- 3. MAIN WORKSPACE CONTAINER -->
    <div class="settings-workspace layout-vertical" id="settingsLayoutWrapper">

        <!-- NAVIGATION RAIL -->
        <aside class="settings-nav-rail">
            <div class="settings-tabs-list profile-tabs-nav" role="tablist">
                <!-- Tab 1: General & Platform -->
                <button type="button" class="settings-tab-item profile-tab-btn {{ $activeTab === 'general' ? 'active' : '' }}" id="generalTabBtn" onclick="switchSystemSettingsTab('general', event)">
                    <div class="tab-icon-wrap icon-indigo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    </div>
                    <div class="tab-meta-wrap">
                        <span class="tab-primary-label">{{ transLabel('settings.tab_general', 'General & Platform') }}</span>
                        <span class="tab-secondary-caption">Brand identity, currency & timezone</span>
                    </div>
                    <span class="tab-indicator-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                </button>

                <!-- Tab 2: Meal Ordering & Cycles -->
                <button type="button" class="settings-tab-item profile-tab-btn {{ $activeTab === 'ordering' ? 'active' : '' }}" id="orderingTabBtn" onclick="switchSystemSettingsTab('ordering', event)">
                    <div class="tab-icon-wrap icon-amber">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div class="tab-meta-wrap">
                        <span class="tab-primary-label">{{ transLabel('settings.tab_ordering', 'Meal Ordering & Cycles') }}</span>
                        <span class="tab-secondary-caption">Cutoffs, allergy rules & quotas</span>
                    </div>
                    <span class="tab-indicator-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                </button>

                <!-- Tab 3: Merchant & Kitchen -->
                <button type="button" class="settings-tab-item profile-tab-btn {{ $activeTab === 'merchant' ? 'active' : '' }}" id="merchantTabBtn" onclick="switchSystemSettingsTab('merchant', event)">
                    <div class="tab-icon-wrap icon-emerald">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <div class="tab-meta-wrap">
                        <span class="tab-primary-label">{{ transLabel('settings.tab_merchant', 'Merchant & Kitchen') }}</span>
                        <span class="tab-secondary-caption">Commissions, payouts & safety</span>
                    </div>
                    <span class="tab-indicator-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                </button>

                <!-- Tab 4: Security & Auth -->
                <button type="button" class="settings-tab-item profile-tab-btn {{ $activeTab === 'security' ? 'active' : '' }}" id="securityTabBtn" onclick="switchSystemSettingsTab('security', event)">
                    <div class="tab-icon-wrap icon-blue">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="tab-meta-wrap">
                        <span class="tab-primary-label">{{ transLabel('settings.tab_security', 'Security & Auth') }}</span>
                        <span class="tab-secondary-caption">2FA, session timeouts & lockouts</span>
                    </div>
                    <span class="tab-indicator-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                </button>

                <!-- Tab 5: SMS & Telemetry -->
                <button type="button" class="settings-tab-item profile-tab-btn {{ $activeTab === 'notifications' ? 'active' : '' }}" id="notificationsTabBtn" onclick="switchSystemSettingsTab('notifications', event)">
                    <div class="tab-icon-wrap icon-purple">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    <div class="tab-meta-wrap">
                        <span class="tab-primary-label">{{ transLabel('settings.tab_notifications', 'SMS & Telemetry') }}</span>
                        <span class="tab-secondary-caption">Parent alerts, order SMS & gateway</span>
                    </div>
                    <span class="tab-indicator-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                </button>

                <!-- Tab 6: Cache & Maintenance -->
                <button type="button" class="settings-tab-item profile-tab-btn {{ $activeTab === 'maintenance' ? 'active' : '' }}" id="maintenanceTabBtn" onclick="switchSystemSettingsTab('maintenance', event)">
                    <div class="tab-icon-wrap icon-rose">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </div>
                    <div class="tab-meta-wrap">
                        <span class="tab-primary-label">{{ transLabel('settings.tab_maintenance', 'Cache & Maintenance') }}</span>
                        <span class="tab-secondary-caption">Diagnostics, cache purge & audits</span>
                    </div>
                    <span class="tab-indicator-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                </button>
            </div>

            <!-- QUICK SYSTEM HEALTH CARD IN VERTICAL RAIL -->
            <div class="settings-rail-health">
                <div class="health-header">
                    <div class="health-dot-pulse"></div>
                    <span class="health-title">{{ transLabel('settings.quick_health', 'Platform Status') }}</span>
                </div>
                <div class="health-status-text">{{ transLabel('settings.all_systems_ok', 'All Systems Operational') }}</div>
                <div class="health-metrics-row">
                    <div class="health-metric">
                        <span class="metric-val">{{ $stats['total_settings'] }}</span>
                        <span class="metric-lbl">{{ transLabel('settings.total_parameters', 'Parameters') }}</span>
                    </div>
                    <div class="health-metric">
                        <span class="metric-val">{{ strtoupper($stats['db_driver']) }}</span>
                        <span class="metric-lbl">Database</span>
                    </div>
                </div>
                <button type="button" class="btn-rail-reset" onclick="confirmResetDefaults()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    <span>{{ transLabel('settings.btn_reset_defaults', 'Reset to Defaults') }}</span>
                </button>
            </div>
        </aside>

        <!-- RIGHT CONTENT AREA -->
        <main class="settings-content-area">

    <!-- 3. TAB PANE 1: GENERAL & PLATFORM -->
    <div class="tab-pane {{ $activeTab === 'general' ? 'active' : '' }}" id="generalTabPane">
        <form method="POST" action="{{ route('admin.system_settings.update') }}" id="generalSettingsForm">
            @csrf
            <input type="hidden" name="active_tab" value="general">

            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="card-header-left">
                        <div class="card-header-icon" style="background: #eef2ff; color: #4338ca;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        </div>
                        <div>
                            <div class="card-header-title">{{ transLabel('settings.general_title', 'General Platform Identity & Localization') }}</div>
                            <div class="card-header-desc">{{ transLabel('settings.general_desc', 'Configure core brand identity, primary district timezone, and currency exchange rates') }}</div>
                        </div>
                    </div>
                </div>

                <div class="settings-card-body">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="setting_platform_name">Platform Brand Title <span class="required">*</span></label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                </span>
                                <input type="text" name="settings[platform_name]" id="setting_platform_name" class="form-control" value="{{ old('settings.platform_name', \App\Models\SystemSetting::get('platform_name', 'SchoolMeal National E-Commerce')) }}" required>
                            </div>
                            <div class="form-help">Displayed across portal headers, login branding, and official notifications.</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="setting_platform_tagline">Platform Mission & Slogan</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                </span>
                                <input type="text" name="settings[platform_tagline]" id="setting_platform_tagline" class="form-control" value="{{ old('settings.platform_tagline', \App\Models\SystemSetting::get('platform_tagline')) }}">
                            </div>
                            <div class="form-help">Summary subtitle rendered on student and parent landing portals.</div>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="setting_support_email">Official Support Email <span class="required">*</span></label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                </span>
                                <input type="email" name="settings[support_email]" id="setting_support_email" class="form-control" value="{{ old('settings.support_email', \App\Models\SystemSetting::get('support_email')) }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="setting_support_phone">National Support Hotline</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </span>
                                <input type="text" name="settings[support_phone]" id="setting_support_phone" class="form-control" value="{{ old('settings.support_phone', \App\Models\SystemSetting::get('support_phone')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="setting_primary_timezone">Primary School District Timezone</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </span>
                                <select name="settings[primary_timezone]" id="setting_primary_timezone" class="form-control">
                                    <option value="Asia/Phnom_Penh" {{ \App\Models\SystemSetting::get('primary_timezone') === 'Asia/Phnom_Penh' ? 'selected' : '' }}>Asia/Phnom_Penh (Indochina Time UTC+07:00)</option>
                                    <option value="Asia/Bangkok" {{ \App\Models\SystemSetting::get('primary_timezone') === 'Asia/Bangkok' ? 'selected' : '' }}>Asia/Bangkok (UTC+07:00)</option>
                                    <option value="UTC" {{ \App\Models\SystemSetting::get('primary_timezone') === 'UTC' ? 'selected' : '' }}>Coordinated Universal Time (UTC)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="setting_exchange_rate_usd_khr">USD to KHR Exchange Peg Rate</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                </span>
                                <input type="number" name="settings[exchange_rate_usd_khr]" id="setting_exchange_rate_usd_khr" class="form-control" value="{{ old('settings.exchange_rate_usd_khr', \App\Models\SystemSetting::get('exchange_rate_usd_khr', 4100)) }}" step="50" min="3000">
                            </div>
                            <div class="form-help">Standard rate applied when calculating food item prices and Bakong payouts.</div>
                        </div>
                    </div>

                    <!-- Platform Lock & Maintenance Mode -->
                    <div style="margin-top: 1.5rem; border-top: 1px dashed #cbd5e1; padding-top: 1.25rem;">
                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title" style="color: #dc2626;">Emergency Maintenance Mode Lockdown</div>
                                <div class="pref-label-desc">Lock all public parent and school portals with maintenance page during database migration.</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="settings[maintenance_mode]" value="1" {{ \App\Models\SystemSetting::get('maintenance_mode') ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="form-group" style="margin-top: 1rem;">
                            <label class="form-label" for="setting_maintenance_message">Public Maintenance Notice Message</label>
                            <textarea name="settings[maintenance_message]" id="setting_maintenance_message" rows="2" class="form-control no-icon">{{ old('settings.maintenance_message', \App\Models\SystemSetting::get('maintenance_message')) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="settings-card-footer">
                    <button type="button" class="btn-brand-secondary" onclick="confirmResetDefaults()">
                        {{ transLabel('settings.btn_reset_defaults', 'Reset to Defaults') }}
                    </button>
                    <button type="submit" class="btn-brand-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ transLabel('settings.btn_save_settings', 'Save System Settings') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 4. TAB PANE 2: MEAL ORDERING & CYCLES -->
    <div class="tab-pane {{ $activeTab === 'ordering' ? 'active' : '' }}" id="orderingTabPane">
        <form method="POST" action="{{ route('admin.system_settings.update') }}" id="orderingSettingsForm">
            @csrf
            <input type="hidden" name="active_tab" value="ordering">

            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="card-header-left">
                        <div class="card-header-icon" style="background: #fff7ed; color: #ea580c;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div>
                            <div class="card-header-title">{{ transLabel('settings.ordering_title', 'Student Meal Ordering & Nutritional Cycle Rules') }}</div>
                            <div class="card-header-desc">{{ transLabel('settings.ordering_desc', 'Control order cutoff deadlines, allergy restrictions, and daily pupil quotas') }}</div>
                        </div>
                    </div>
                </div>

                <div class="settings-card-body">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="setting_daily_order_cutoff_time">Daily Morning Order Cutoff Time <span class="required">*</span></label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </span>
                                <input type="time" name="settings[daily_order_cutoff_time]" id="setting_daily_order_cutoff_time" class="form-control" value="{{ old('settings.daily_order_cutoff_time', \App\Models\SystemSetting::get('daily_order_cutoff_time', '08:00')) }}" required>
                            </div>
                            <div class="form-help">Daily threshold after which today's orders lock and route to restaurant kitchen prep queues.</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="setting_order_cancellation_window_minutes">Order Cancellation Window (Minutes)</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                </span>
                                <input type="number" name="settings[order_cancellation_window_minutes]" id="setting_order_cancellation_window_minutes" class="form-control" value="{{ old('settings.order_cancellation_window_minutes', \App\Models\SystemSetting::get('order_cancellation_window_minutes', 30)) }}" min="5" max="120">
                            </div>
                            <div class="form-help">Grace period before cutoff during which parents can cancel meal bookings.</div>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="setting_max_daily_meals_per_student">Maximum Meals Per Student Per Day</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M5.5 21v-2a6.5 6.5 0 0 1 13 0v2"/></svg>
                                </span>
                                <input type="number" name="settings[max_daily_meals_per_student]" id="setting_max_daily_meals_per_student" class="form-control" value="{{ old('settings.max_daily_meals_per_student', \App\Models\SystemSetting::get('max_daily_meals_per_student', 3)) }}" min="1" max="10">
                            </div>
                            <div class="form-help">Prevents accidental duplicate orders by parents or children.</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="setting_nutrition_calorie_target_daily">Daily Target Nutrition Calorie (kCal)</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                </span>
                                <input type="number" name="settings[nutrition_calorie_target_daily]" id="setting_nutrition_calorie_target_daily" class="form-control" value="{{ old('settings.nutrition_calorie_target_daily', \App\Models\SystemSetting::get('nutrition_calorie_target_daily', 650)) }}" step="25">
                            </div>
                            <div class="form-help">National nutrition target for primary school lunch portion benchmarks.</div>
                        </div>
                    </div>

                    <!-- Ordering Policy Toggles -->
                    <div style="margin-top: 1.5rem; border-top: 1px dashed #cbd5e1; padding-top: 1.25rem;">
                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">Auto-Confirm Paid Orders to Kitchen Queue</div>
                                <div class="pref-label-desc">Automatically route paid student orders straight into restaurant kitchen prep without manual school clerk approval.</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="settings[auto_confirm_paid_orders]" value="1" {{ \App\Models\SystemSetting::get('auto_confirm_paid_orders') ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title" style="color: #059669;">Strict Medical Allergy Enforcement</div>
                                <div class="pref-label-desc">Hard-block cart checkout if dish contains allergens registered in student health profile (e.g., Peanuts, Seafood).</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="settings[strict_allergy_check]" value="1" {{ \App\Models\SystemSetting::get('strict_allergy_check') ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">Weekend Meal Ordering</div>
                                <div class="pref-label-desc">Permit meal ordering for Saturday sports clubs, remedial tutoring sessions, and extracurricular activities.</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="settings[allow_weekend_ordering]" value="1" {{ \App\Models\SystemSetting::get('allow_weekend_ordering') ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">Require Parent Digital Consent for Advance Subscriptions</div>
                                <div class="pref-label-desc">Enforce SMS OTP confirmation before recurring weekly lunch subscriptions can be finalized.</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="settings[require_parent_consent_for_preorders]" value="1" {{ \App\Models\SystemSetting::get('require_parent_consent_for_preorders') ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="settings-card-footer">
                    <button type="button" class="btn-brand-secondary" onclick="confirmResetDefaults()">
                        {{ transLabel('settings.btn_reset_defaults', 'Reset to Defaults') }}
                    </button>
                    <button type="submit" class="btn-brand-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ transLabel('settings.btn_save_settings', 'Save System Settings') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 5. TAB PANE 3: MERCHANT & KITCHEN GOVERNANCE -->
    <div class="tab-pane {{ $activeTab === 'merchant' ? 'active' : '' }}" id="merchantTabPane">
        <form method="POST" action="{{ route('admin.system_settings.update') }}" id="merchantSettingsForm">
            @csrf
            <input type="hidden" name="active_tab" value="merchant">

            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="card-header-left">
                        <div class="card-header-icon" style="background: #eff6ff; color: #1d4ed8;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div>
                            <div class="card-header-title">{{ transLabel('settings.merchant_title', 'Merchant, Kitchen & Settlement Escrow Rules') }}</div>
                            <div class="card-header-desc">{{ transLabel('settings.merchant_desc', 'Manage commission schedules, payout thresholds, and kitchen batch capacities') }}</div>
                        </div>
                    </div>
                </div>

                <div class="settings-card-body">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="setting_platform_commission_percent">Platform Escrow Commission Rate (%) <span class="required">*</span></label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                                </span>
                                <input type="number" name="settings[platform_commission_percent]" id="setting_platform_commission_percent" class="form-control" value="{{ old('settings.platform_commission_percent', \App\Models\SystemSetting::get('platform_commission_percent', 5.0)) }}" step="0.1" min="0" max="30" required>
                            </div>
                            <div class="form-help">Percentage automatically deducted from vendor sales for system infrastructure maintenance.</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="setting_settlement_cycle">Automated Merchant Settlement Cycle</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg>
                                </span>
                                <select name="settings[settlement_cycle]" id="setting_settlement_cycle" class="form-control">
                                    <option value="daily" {{ \App\Models\SystemSetting::get('settlement_cycle') === 'daily' ? 'selected' : '' }}>Daily End-of-Shift Payout</option>
                                    <option value="weekly" {{ \App\Models\SystemSetting::get('settlement_cycle') === 'weekly' ? 'selected' : '' }}>Weekly (Every Monday Morning)</option>
                                    <option value="biweekly" {{ \App\Models\SystemSetting::get('settlement_cycle') === 'biweekly' ? 'selected' : '' }}>Bi-Weekly (1st & 15th of month)</option>
                                    <option value="monthly" {{ \App\Models\SystemSetting::get('settlement_cycle') === 'monthly' ? 'selected' : '' }}>Monthly Reconciliation</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="setting_min_merchant_payout_amount">Minimum Payout Threshold (KHR)</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                </span>
                                <input type="number" name="settings[min_merchant_payout_amount]" id="setting_min_merchant_payout_amount" class="form-control" value="{{ old('settings.min_merchant_payout_amount', \App\Models\SystemSetting::get('min_merchant_payout_amount', 50000)) }}" step="10000" min="10000">
                            </div>
                            <div class="form-help">Minimum net funds required in merchant escrow account before automated Bakong release.</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="setting_max_active_prep_orders_per_kitchen">Max Concurrent Prep Queue Size</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/></svg>
                                </span>
                                <input type="number" name="settings[max_active_prep_orders_per_kitchen]" id="setting_max_active_prep_orders_per_kitchen" class="form-control" value="{{ old('settings.max_active_prep_orders_per_kitchen', \App\Models\SystemSetting::get('max_active_prep_orders_per_kitchen', 50)) }}" min="10" max="250">
                            </div>
                            <div class="form-help">Protects kitchen staff from order flooding during peak lunch delivery windows.</div>
                        </div>
                    </div>

                    <!-- Merchant Safety Policies -->
                    <div style="margin-top: 1.5rem; border-top: 1px dashed #cbd5e1; padding-top: 1.25rem;">
                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">Enforce Daily Food Safety & Hygiene Audit</div>
                                <div class="pref-label-desc">Require merchant kitchen managers to certify food safety and sanitized prep counters prior to cooking morning meal shifts.</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="settings[kitchen_safety_audit_required]" value="1" {{ \App\Models\SystemSetting::get('kitchen_safety_audit_required') ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">Digital Food Temperature Logging</div>
                                <div class="pref-label-desc">Mandate hot meal holding temperatures (min 60°C) recorded into kitchen dispatch log before driver hand-off.</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="settings[food_temperature_logging_required]" value="1" {{ \App\Models\SystemSetting::get('food_temperature_logging_required') ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="settings-card-footer">
                    <button type="button" class="btn-brand-secondary" onclick="confirmResetDefaults()">
                        {{ transLabel('settings.btn_reset_defaults', 'Reset to Defaults') }}
                    </button>
                    <button type="submit" class="btn-brand-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ transLabel('settings.btn_save_settings', 'Save System Settings') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 6. TAB PANE 4: SECURITY & AUTH POLICY -->
    <div class="tab-pane {{ $activeTab === 'security' ? 'active' : '' }}" id="securityTabPane">
        <form method="POST" action="{{ route('admin.system_settings.update') }}" id="securitySettingsForm">
            @csrf
            <input type="hidden" name="active_tab" value="security">

            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="card-header-left">
                        <div class="card-header-icon" style="background: #fef2f2; color: #dc2626;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <div class="card-header-title">{{ transLabel('settings.security_title', 'Access Security, Brute-Force & Session Policy') }}</div>
                            <div class="card-header-desc">{{ transLabel('settings.security_desc', 'Enforce multi-factor authentication, inactivity timeouts, and credential standards') }}</div>
                        </div>
                    </div>
                </div>

                <div class="settings-card-body">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="setting_session_lifetime_minutes">Session Inactivity Lifetime (Minutes) <span class="required">*</span></label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </span>
                                <input type="number" name="settings[session_lifetime_minutes]" id="setting_session_lifetime_minutes" class="form-control" value="{{ old('settings.session_lifetime_minutes', \App\Models\SystemSetting::get('session_lifetime_minutes', 120)) }}" min="15" max="1440" required>
                            </div>
                            <div class="form-help">Automatically signs out idle sessions on shared campus and kitchen terminals.</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="setting_max_login_attempts">Max Failed Login Attempts Before Lockout</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <input type="number" name="settings[max_login_attempts]" id="setting_max_login_attempts" class="form-control" value="{{ old('settings.max_login_attempts', \App\Models\SystemSetting::get('max_login_attempts', 5)) }}" min="3" max="20">
                            </div>
                            <div class="form-help">Threshold of bad credentials before temporary rate-limiting quarantine.</div>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="setting_lockout_duration_minutes">Security Quarantine Duration (Minutes)</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                </span>
                                <input type="number" name="settings[lockout_duration_minutes]" id="setting_lockout_duration_minutes" class="form-control" value="{{ old('settings.lockout_duration_minutes', \App\Models\SystemSetting::get('lockout_duration_minutes', 15)) }}" min="1" max="120">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="setting_audit_log_retention_days">Immutable Audit Trail Retention (Days)</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </span>
                                <input type="number" name="settings[audit_log_retention_days]" id="setting_audit_log_retention_days" class="form-control" value="{{ old('settings.audit_log_retention_days', \App\Models\SystemSetting::get('audit_log_retention_days', 365)) }}" min="90" max="3650">
                            </div>
                        </div>
                    </div>

                    <!-- Security Toggles -->
                    <div style="margin-top: 1.5rem; border-top: 1px dashed #cbd5e1; padding-top: 1.25rem;">
                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title" style="color: #047857;">Enforce 2FA for Staff & Merchant Principals</div>
                                <div class="pref-label-desc">Require SMS OTP or hardware authenticator token upon sign-in across all administrator and merchant management dashboards.</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="settings[enforce_2fa_for_staff]" value="1" {{ \App\Models\SystemSetting::get('enforce_2fa_for_staff') ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">Enforce Mixed Case & Alphanumeric Credentials</div>
                                <div class="pref-label-desc">Require passwords to contain at least 8 characters including uppercase letters, numbers, and symbols.</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="settings[password_require_mixed_case]" value="1" {{ \App\Models\SystemSetting::get('password_require_mixed_case', true) ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="settings-card-footer">
                    <button type="button" class="btn-brand-secondary" onclick="confirmResetDefaults()">
                        {{ transLabel('settings.btn_reset_defaults', 'Reset to Defaults') }}
                    </button>
                    <button type="submit" class="btn-brand-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ transLabel('settings.btn_save_settings', 'Save System Settings') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 7. TAB PANE 5: SMS & TELEMETRY -->
    <div class="tab-pane {{ $activeTab === 'notifications' ? 'active' : '' }}" id="notificationsTabPane">
        <form method="POST" action="{{ route('admin.system_settings.update') }}" id="notificationsSettingsForm">
            @csrf
            <input type="hidden" name="active_tab" value="notifications">

            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="card-header-left">
                        <div class="card-header-icon" style="background: #f0fdf4; color: #15803d;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div>
                            <div class="card-header-title">{{ transLabel('settings.notifs_title', 'Telecommunications, SMS Gateway & Parent Alerts') }}</div>
                            <div class="card-header-desc">{{ transLabel('settings.notifs_desc', 'Configure national SMS gateway dispatchers and automated parent delivery flashes') }}</div>
                        </div>
                    </div>
                </div>

                <div class="settings-card-body">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="setting_sms_gateway_provider">National SMS Telecom Gateway Provider</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </span>
                                <select name="settings[sms_gateway_provider]" id="setting_sms_gateway_provider" class="form-control">
                                    <option value="Smart Axiata Gateway" {{ \App\Models\SystemSetting::get('sms_gateway_provider') === 'Smart Axiata Gateway' ? 'selected' : '' }}>Smart Axiata Enterprise SMS (Cambodia)</option>
                                    <option value="Cellcard CamGSM" {{ \App\Models\SystemSetting::get('sms_gateway_provider') === 'Cellcard CamGSM' ? 'selected' : '' }}>Cellcard CamGSM SMS Gateway</option>
                                    <option value="Metfone Viettel" {{ \App\Models\SystemSetting::get('sms_gateway_provider') === 'Metfone Viettel' ? 'selected' : '' }}>Metfone Telecom Gateway</option>
                                    <option value="Twilio Global SMS" {{ \App\Models\SystemSetting::get('sms_gateway_provider') === 'Twilio Global SMS' ? 'selected' : '' }}>Twilio International Fallback</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="setting_sms_sender_id">Approved Telecom Sender ID Header</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </span>
                                <input type="text" name="settings[sms_sender_id]" id="setting_sms_sender_id" class="form-control" value="{{ old('settings.sms_sender_id', \App\Models\SystemSetting::get('sms_sender_id', 'SchoolMeal')) }}" maxlength="11">
                            </div>
                            <div class="form-help">Alphanumeric header shown on mobile devices (max 11 chars).</div>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="setting_low_wallet_balance_alert_khr">Low Wallet Balance Warning Threshold (KHR)</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                </span>
                                <input type="number" name="settings[low_wallet_balance_alert_khr]" id="setting_low_wallet_balance_alert_khr" class="form-control" value="{{ old('settings.low_wallet_balance_alert_khr', \App\Models\SystemSetting::get('low_wallet_balance_alert_khr', 20000)) }}" step="5000">
                            </div>
                            <div class="form-help">Triggers SMS to parents when child's prepaid meal balance drops below this amount.</div>
                        </div>
                    </div>

                    <!-- Telemetry Toggles -->
                    <div style="margin-top: 1.5rem; border-top: 1px dashed #cbd5e1; padding-top: 1.25rem;">
                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">Real-time Lunch Delivery SMS Flashes</div>
                                <div class="pref-label-desc">Sends instantaneous SMS notification to guardians the moment child's badge is scanned at the cafeteria counter.</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="settings[enable_sms_order_receipts]" value="1" {{ \App\Models\SystemSetting::get('enable_sms_order_receipts') ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">Consolidated Evening Nutrition Digest</div>
                                <div class="pref-label-desc">Dispatches daily 6:00 PM summary to parents detailing student daily calorie intake and menu choices for tomorrow.</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="settings[enable_daily_parent_digest]" value="1" {{ \App\Models\SystemSetting::get('enable_daily_parent_digest') ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title" style="color: #b45309;">Emergency Food Recall Broadcast Channel</div>
                                <div class="pref-label-desc">Enables broadcast dispatches to kitchen managers and school principals in the event of an urgent supplier food recall.</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="settings[urgent_emergency_broadcast_enabled]" value="1" {{ \App\Models\SystemSetting::get('urgent_emergency_broadcast_enabled') ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="settings-card-footer">
                    <button type="button" class="btn-brand-secondary" onclick="confirmResetDefaults()">
                        {{ transLabel('settings.btn_reset_defaults', 'Reset to Defaults') }}
                    </button>
                    <button type="submit" class="btn-brand-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ transLabel('settings.btn_save_settings', 'Save System Settings') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 8. TAB PANE 6: CACHE & MAINTENANCE -->
    <div class="tab-pane {{ $activeTab === 'maintenance' ? 'active' : '' }}" id="maintenanceTabPane">
        
        <!-- Cache Purge Action Cards -->
        <div class="maintenance-cards-grid">
            <div class="maint-action-card">
                <div class="maint-card-top">
                    <div class="maint-card-icon" style="background: #eff6ff; color: #1d4ed8;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                    </div>
                    <div>
                        <div class="maint-card-title">{{ transLabel('settings.cache_app_title', 'Application & Query Cache') }}</div>
                        <div class="maint-card-desc">{{ transLabel('settings.cache_app_desc', 'Purge cached query datasets, session stores, and key-value memory records') }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.system_settings.cache_clear') }}">
                    @csrf
                    <input type="hidden" name="cache_type" value="cache">
                    <button type="submit" class="btn-brand-secondary" style="width: 100%; justify-content: center;">
                        {{ transLabel('settings.btn_purge_cache', 'Purge Cache') }}
                    </button>
                </form>
            </div>

            <div class="maint-action-card">
                <div class="maint-card-top">
                    <div class="maint-card-icon" style="background: #fdf2f8; color: #db2777;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    </div>
                    <div>
                        <div class="maint-card-title">{{ transLabel('settings.cache_views_title', 'Compiled Blade Views') }}</div>
                        <div class="maint-card-desc">{{ transLabel('settings.cache_views_desc', 'Recompile all Blade template assets and cached view partials') }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.system_settings.cache_clear') }}">
                    @csrf
                    <input type="hidden" name="cache_type" value="views">
                    <button type="submit" class="btn-brand-secondary" style="width: 100%; justify-content: center;">
                        {{ transLabel('settings.btn_recompile_views', 'Recompile Views') }}
                    </button>
                </form>
            </div>

            <div class="maint-action-card">
                <div class="maint-card-top">
                    <div class="maint-card-icon" style="background: #f0fdf4; color: #059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    </div>
                    <div>
                        <div class="maint-card-title">{{ transLabel('settings.cache_config_title', 'Configuration & Routes') }}</div>
                        <div class="maint-card-desc">{{ transLabel('settings.cache_config_desc', 'Refresh cached environment configuration files and service routing maps') }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.system_settings.cache_clear') }}">
                    @csrf
                    <input type="hidden" name="cache_type" value="config">
                    <button type="submit" class="btn-brand-secondary" style="width: 100%; justify-content: center;">
                        {{ transLabel('settings.btn_refresh_config', 'Refresh Config') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- 2 Column Diagnostics & Audit Record -->
        <div class="profile-grid-2">
            <!-- Left: Runtime Specification -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="card-header-left">
                        <div class="card-header-icon" style="background: #f8fafc; color: #334155;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        </div>
                        <div>
                            <div class="card-header-title">{{ transLabel('settings.runtime_diagnostics', 'System Runtime Telemetry & Diagnostics') }}</div>
                            <div class="card-header-desc">{{ transLabel('settings.runtime_desc', 'Live infrastructure environment specification and cluster metrics') }}</div>
                        </div>
                    </div>
                </div>

                <div class="settings-card-body" style="padding: 0.75rem 1.25rem;">
                    <table class="diag-table">
                        <tbody>
                            <tr>
                                <td class="label-col">PHP Engine Version</td>
                                <td class="val-col">{{ $stats['php_version'] }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">Laravel Framework</td>
                                <td class="val-col">v{{ $stats['laravel_version'] }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">Primary Database Engine</td>
                                <td class="val-col">{{ strtoupper($stats['db_driver']) }} 16.2 (PostgreSQL)</td>
                            </tr>
                            <tr>
                                <td class="label-col">Cache Store Driver</td>
                                <td class="val-col">{{ strtoupper($stats['cache_driver']) }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">Server Time (Cambodia)</td>
                                <td class="val-col">{{ now()->timezone('Asia/Phnom_Penh')->format('d M Y, h:i:s A') }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">Memory Limit</td>
                                <td class="val-col">{{ ini_get('memory_limit') }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">Max Execution Time</td>
                                <td class="val-col">{{ ini_get('max_execution_time') }}s</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="settings-card-footer" style="justify-content: space-between;">
                    <span style="font-size: 0.75rem; color: #64748b;">Host: {{ gethostname() }}</span>
                    <form method="POST" action="{{ route('admin.system_settings.cache_clear') }}" onsubmit="return confirm('{{ addslashes(transLabel('settings.confirm_purge_all', 'Are you sure you want to purge all framework and template caches?')) }}')">
                        @csrf
                        <input type="hidden" name="cache_type" value="all">
                        <button type="submit" class="btn-brand-danger">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            {{ transLabel('settings.btn_purge_all', 'Purge All Caches') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Recent System Audit Trail -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="card-header-left">
                        <div class="card-header-icon" style="background: #f1f5f9; color: #334155;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div>
                            <div class="card-header-title">Recent System Settings Audit Trail</div>
                            <div class="card-header-desc">Cryptographic audit record of recent administrative changes</div>
                        </div>
                    </div>
                </div>

                <div class="settings-card-body" style="padding: 0.5rem 0;">
                    @forelse($recentAuditLogs as $log)
                        <div class="audit-log-item">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span class="audit-action-tag">{{ $log->action }}</span>
                                <span style="color: #475569; font-weight: 500;">
                                    {{ $log->actor?->full_name ?? 'System Root' }}
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.85rem; color: #64748b; font-size: 0.75rem;">
                                <span>IP: {{ $log->ip_address ?? '127.0.0.1' }}</span>
                                <span>&bull;</span>
                                <span>{{ $log->created_at ? $log->created_at->diffForHumans() : 'Just now' }}</span>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 2.5rem 1rem; color: #94a3b8; font-size: 0.85rem;">
                            No system configuration audit logs recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

        </main>
    </div>

</div>

<!-- Hidden Reset to Defaults Form -->
<form id="resetDefaultsForm" method="POST" action="{{ route('admin.system_settings.reset_defaults') }}" style="display: none;">
    @csrf
</form>

<script>
    /**
     * Seamless client-side tab switching for System Settings
     */
    function switchSystemSettingsTab(tabName, event) {
        if (event && !event.ctrlKey && !event.metaKey) {
            event.preventDefault();
        }

        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.profile-tab-btn').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.settings-tab-item').forEach(el => el.classList.remove('active'));

        const targetTab = document.getElementById(tabName + 'TabPane');
        const targetBtn = document.getElementById(tabName + 'TabBtn');

        if (targetTab) targetTab.classList.add('active');
        if (targetBtn) targetBtn.classList.add('active');

        if (window.history && window.history.pushState) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            window.history.pushState({ tab: tabName }, '', url.toString());
        }
    }

    /**
     * Set layout mode between 'vertical' and 'horizontal'
     */
    function setSettingsLayout(mode) {
        const wrapper = document.getElementById('settingsLayoutWrapper');
        const btnV = document.getElementById('btnLayoutVertical');
        const btnH = document.getElementById('btnLayoutHorizontal');

        if (!wrapper) return;

        if (mode === 'horizontal') {
            wrapper.classList.remove('layout-vertical');
            wrapper.classList.add('layout-horizontal');
            if (btnV) btnV.classList.remove('active');
            if (btnH) btnH.classList.add('active');
            try { localStorage.setItem('admin_settings_tab_layout', 'horizontal'); } catch(e) {}
        } else {
            wrapper.classList.remove('layout-horizontal');
            wrapper.classList.add('layout-vertical');
            if (btnV) btnV.classList.add('active');
            if (btnH) btnH.classList.remove('active');
            try { localStorage.setItem('admin_settings_tab_layout', 'vertical'); } catch(e) {}
        }
    }

    /**
     * Confirmation dialog for restoring system defaults
     */
    function confirmResetDefaults() {
        const msg = {!! json_encode(transLabel('settings.confirm_reset_defaults', 'Are you sure you want to restore system parameters to baseline defaults? Any custom parameters will be overridden.')) !!};
        if (confirm(msg)) {
            document.getElementById('resetDefaultsForm').submit();
        }
    }

    // Auto-detect tab and saved layout on initial DOM load
    document.addEventListener('DOMContentLoaded', function() {
        // Restore layout preference (default to 'vertical')
        let savedLayout = 'vertical';
        try {
            savedLayout = localStorage.getItem('admin_settings_tab_layout') || 'vertical';
        } catch (e) {}
        setSettingsLayout(savedLayout);

        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam && ['general', 'ordering', 'merchant', 'security', 'notifications', 'maintenance'].includes(tabParam)) {
            switchSystemSettingsTab(tabParam);
        }
    });
</script>
@endsection
