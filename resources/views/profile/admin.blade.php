@extends('layouts.master_super_admin')

@section('title', transLabel('profile.admin_title', 'Super Admin Profile & Platform Governance | SchoolMeal'))

@section('content')
@include('profile.partials.styles')

<style>
    /* Super Admin Executive Theme Customizations */
    .profile-hero.admin-executive-hero {
        background: linear-gradient(135deg, #090d16 0%, #0f172a 35%, #1e1b4b 75%, #0f2b2b 100%);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 16px 40px -10px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.08) inset;
    }
    .profile-hero.admin-executive-hero .profile-hero-glow {
        background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, rgba(16, 185, 129, 0.15) 50%, rgba(99, 102, 241, 0) 70%);
        width: 380px;
        height: 380px;
        top: -60px;
        right: -60px;
    }
    .admin-crown-badge {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        border-color: #0f172a !important;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.5) !important;
    }
    .profile-role-badge.super-admin-badge {
        background: rgba(99, 102, 241, 0.2);
        color: #c7d2fe;
        border: 1px solid rgba(165, 180, 252, 0.3);
    }
    .status-pulse-dot.admin-live-pulse {
        background: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.35);
    }
    .admin-stat-chip {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 0.4rem 0.85rem;
        color: #e2e8f0;
        font-size: 0.8125rem;
        font-weight: 600;
    }
    .admin-stat-chip svg {
        color: #818cf8;
    }
    .infra-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    .infra-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1.15rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .infra-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    .infra-card-val {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        margin-bottom: 0.25rem;
    }
    .infra-card-lbl {
        font-size: 0.775rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .privilege-tags-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }
    .privilege-tag-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.75rem 0.9rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.825rem;
        font-weight: 600;
        color: #1e293b;
        transition: all 0.2s ease;
    }
    .privilege-tag-item:hover {
        border-color: #3b82f6;
        background: #f0f7ff;
        color: #1d4ed8;
    }
    .privilege-tag-item svg {
        color: #10b981;
        flex-shrink: 0;
    }
    .audit-log-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.825rem;
    }
    .audit-log-item:last-child {
        border-bottom: none;
    }
    .audit-action-tag {
        font-family: monospace;
        font-weight: 700;
        color: #1d4ed8;
        background: #eff6ff;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        border: 1px solid #dbeafe;
    }
</style>

<div class="profile-page-wrapper">

    <!-- 1. HERO HEADER BANNER: SUPER ADMIN ROOT GOVERNANCE -->
    <div class="profile-hero admin-executive-hero">
        <div class="profile-hero-pattern"></div>
        <div class="profile-hero-glow"></div>
        <div class="profile-hero-content">
            <div class="profile-hero-left">
                <div class="profile-avatar-container">
                    <img src="{{ $user->profile_image_url ?? asset('images/avatar_seng_sourng.jpg') }}" alt="{{ $user->full_name }}" class="profile-hero-avatar" id="heroAvatarTarget" onerror="this.src='https://ui-avatars.com/api/?name=Super+Admin&background=1E1B4B&color=fff'">
                    <div class="profile-avatar-badge admin-crown-badge" title="{{ transLabel('profile.root_authority', 'Root Authority') }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                </div>
                <div class="profile-meta-header">
                    <div class="profile-meta-title-row">
                        <h1 class="profile-meta-name">{{ $user->full_name }}</h1>
                        <div class="profile-badges-row">
                            <span class="profile-role-badge super-admin-badge">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                {{ transLabel('form.role_super_admin', 'Super Admin') }}
                            </span>
                            <span class="profile-status-pill">
                                <span class="status-pulse-dot admin-live-pulse"></span>
                                {{ transLabel('profile.root_active', 'Root Active') }}
                            </span>
                            <span class="admin-stat-chip">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                {{ transLabel('profile.clearance_level_0', 'Level 0 - Master Root Clearance') }}
                            </span>
                        </div>
                    </div>
                    <div class="profile-meta-subtitle">
                        {!! transLabel('profile.super_admin_subtitle', 'System Architect & Root Authority &bull; Platform Governance') !!}
                    </div>
                    <div class="profile-meta-sub">
                        <div class="profile-meta-sub-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <span>{{ $user->email }}</span>
                        </div>
                        <div class="profile-meta-sub-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>{{ $user->phone ?? '+855 12 888 001' }}</span>
                        </div>
                        <div class="profile-meta-sub-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span>{{ transLabel('profile.joined', ['date' => $user->created_at ? $user->created_at->format('M Y') : 'Oct 2026'], 'Joined :date') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Platform Root Operational Telemetry -->
            <div class="profile-hero-quick-stats">
                <div class="hero-stat-box">
                    <div class="hero-stat-val">{{ number_format($stats['total_users']) }}</div>
                    <div class="hero-stat-lbl">{{ transLabel('profile.platform_users', 'Platform Users') }}</div>
                </div>
                <div class="hero-stat-box">
                    <div class="hero-stat-val" style="color: #60a5fa;">{{ number_format($stats['total_schools']) }}</div>
                    <div class="hero-stat-lbl">{{ transLabel('profile.partner_schools', 'Partner Schools') }}</div>
                </div>
                <div class="hero-stat-box">
                    <div class="hero-stat-val" style="color: #34d399;">{{ $stats['uptime'] }}</div>
                    <div class="hero-stat-lbl">{{ transLabel('profile.system_uptime', 'System Uptime') }}</div>
                </div>
                <div class="hero-stat-box">
                    <div class="hero-stat-val" style="color: #a78bfa;">{{ count($sessions) }}</div>
                    <div class="hero-stat-lbl">{{ transLabel('profile.active_sessions', 'Active Sessions') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. NAVIGATION TABS: OVERVIEW (/admin/profile), SETTINGS (/admin/profile/setting), SECURITY (/admin/profile/security) -->
    <div class="profile-tabs-nav" role="tablist">
        <a href="{{ route('admin.profile') }}" class="profile-tab-btn {{ $activeTab === 'overview' ? 'active' : '' }}" id="overviewTabBtn" onclick="switchAdminProfileTab('overview', event)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>{{ transLabel('profile.tab_overview', 'My Profile') }}</span>
        </a>

        <a href="{{ route('admin.profile.settings') }}" class="profile-tab-btn {{ $activeTab === 'settings' ? 'active' : '' }}" id="settingsTabBtn" onclick="switchAdminProfileTab('settings', event)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span>{{ transLabel('profile.tab_settings', 'Account Settings') }}</span>
        </a>

        <a href="{{ route('admin.profile.security') }}" class="profile-tab-btn {{ $activeTab === 'security' ? 'active' : '' }}" id="securityTabBtn" onclick="switchAdminProfileTab('security', event)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>{{ transLabel('profile.tab_security', 'Security & Sessions') }}</span>
            <span class="profile-tab-badge">{{ count($sessions) }}</span>
        </a>
    </div>

    <!-- 3. TAB PANE 1: MY PROFILE (OVERVIEW & ROOT GOVERNANCE MATRIX) -->
    <div class="tab-pane {{ $activeTab === 'overview' ? 'active' : '' }}" id="overviewTabPane">
        <div class="profile-grid-sidebar">
            
            <!-- Left Column: Master Identification Card & Master Administrative Privileges -->
            <div>
                <div class="settings-card" style="margin-bottom: 1.5rem;">
                    <div class="settings-card-header">
                        <div class="card-header-left">
                            <div class="card-header-icon" style="background: #eef2ff; color: #4338ca;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            </div>
                            <div>
                                <div class="card-header-title">{{ transLabel('profile.admin_identification', 'Master Administrator Identification') }}</div>
                                <div class="card-header-desc">{{ transLabel('profile.admin_identification_desc', 'Root cryptographic credentials & clearance level') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="settings-card-body">
                        <div class="info-list-group">
                            <div class="info-item">
                                <span class="info-label">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                    {{ transLabel('profile.master_admin_id', 'Master Admin ID') }}
                                </span>
                                <span class="info-value" style="font-family: monospace; color: #4338ca; cursor: pointer;" onclick="copyProfileText('{{ $user->id }}', '{{ addslashes(transLabel('profile.master_admin_id', 'Master Admin ID')) }}')">
                                    {{ substr($user->id, 0, 14) }}...
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                    {{ transLabel('profile.executive_rank', 'Executive Rank') }}
                                </span>
                                <span class="info-value" style="color: #0f172a; font-weight: 700;">
                                    {{ transLabel('profile.principal_architect', 'Tier 1 - Principal Architect') }}
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/></svg>
                                    {{ transLabel('profile.clearance_level', 'Clearance Level') }}
                                </span>
                                <span class="info-value" style="color: #10b981; font-weight: 700;">
                                    {{ transLabel('profile.clearance_level_0', 'Level 0 - Master Root Clearance') }}
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    {{ transLabel('profile.key_rotation', 'Key Rotation') }}
                                </span>
                                <span class="info-value">
                                    <span style="display: inline-block; padding: 0.15rem 0.5rem; background: #ecfdf5; color: #047857; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                                        {{ transLabel('profile.days_left', ['days' => 42], ':days Days Left') }}
                                    </span>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    {{ transLabel('profile.env_cluster', 'Environment Cluster') }}
                                </span>
                                <span class="info-value" style="font-weight: 600; color: #1e293b;">
                                    {{ transLabel('profile.prod_cloud_ha', 'Production Cloud (High Availability)') }}
                                </span>
                            </div>
                        </div>

                        <!-- System Uptime Badge -->
                        <div style="margin-top: 1.5rem; padding: 0.85rem 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span class="status-pulse-dot admin-live-pulse"></span>
                                <span style="font-size: 0.8125rem; font-weight: 700; color: #166534;">
                                    PostgreSQL Primary Cluster Online
                                </span>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 800; color: #059669; font-family: monospace;">{{ $stats['uptime'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Master Administrative Privileges -->
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div class="card-header-left">
                            <div class="card-header-icon" style="background: #fdf2f8; color: #db2777;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="M9 9h.01"/><path d="M15 15h.01"/></svg>
                            </div>
                            <div>
                                <div class="card-header-title">{{ transLabel('profile.master_privileges', 'Master Administrative Privileges') }}</div>
                                <div class="card-header-desc">{{ transLabel('profile.master_priv_desc', 'Unrestricted system-wide governance and operational privileges') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="settings-card-body">
                        <div class="privilege-tags-grid">
                            <div class="privilege-tag-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>{{ transLabel('profile.priv_full_rbac', 'Full RBAC & Role Provisioning') }}</span>
                            </div>
                            <div class="privilege-tag-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>{{ transLabel('profile.priv_school_district', 'School District & Campus Onboarding') }}</span>
                            </div>
                            <div class="privilege-tag-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>{{ transLabel('profile.priv_merchant_gov', 'Merchant Governance & Menu Policy') }}</span>
                            </div>
                            <div class="privilege-tag-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>{{ transLabel('profile.priv_finance_settlement', 'Financial Settlements & Escrow Rules') }}</span>
                            </div>
                            <div class="privilege-tag-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>{{ transLabel('profile.priv_audit_inspection', 'System Audit Log Inspection') }}</span>
                            </div>
                            <div class="privilege-tag-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>{{ transLabel('profile.priv_database_encryption', 'Database Encryption & Key Vault') }}</span>
                            </div>
                            <div class="privilege-tag-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>{{ transLabel('profile.priv_i18n_management', 'Multi-Language Localization Engine') }}</span>
                            </div>
                            <div class="privilege-tag-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>{{ transLabel('profile.priv_cluster_telemetry', 'Live Server & Infrastructure Telemetry') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Platform Core Infrastructure & Recent Audit Actions -->
            <div>
                <!-- Platform Core Infrastructure Matrix -->
                <div class="settings-card" style="margin-bottom: 1.5rem;">
                    <div class="settings-card-header">
                        <div class="card-header-left">
                            <div class="card-header-icon" style="background: #eff6ff; color: #1d4ed8;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            </div>
                            <div>
                                <div class="card-header-title">{{ transLabel('profile.platform_infrastructure', 'Platform Core Infrastructure') }}</div>
                                <div class="card-header-desc">{{ transLabel('profile.platform_infra_desc', 'Live operational telemetry across all participating entities') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="settings-card-body">
                        <div class="infra-grid">
                            <div class="infra-card">
                                <div class="infra-card-val" style="color: #1d4ed8;">{{ number_format($stats['total_schools']) }}</div>
                                <div class="infra-card-lbl">{{ transLabel('profile.registered_schools', 'Registered Schools') }}</div>
                            </div>
                            <div class="infra-card">
                                <div class="infra-card-val" style="color: #ea580c;">{{ number_format($stats['total_restaurants']) }}</div>
                                <div class="infra-card-lbl">{{ transLabel('profile.partner_restaurants', 'Partner Restaurants') }}</div>
                            </div>
                            <div class="infra-card">
                                <div class="infra-card-val" style="color: #059669;">{{ number_format($stats['total_orders']) }}</div>
                                <div class="infra-card-lbl">{{ transLabel('profile.total_food_orders', 'Total Food Orders') }}</div>
                            </div>
                            <div class="infra-card">
                                <div class="infra-card-val" style="color: #7c3aed;">{{ number_format($stats['audit_events']) }}</div>
                                <div class="infra-card-lbl">{{ transLabel('profile.audit_trail_events', 'Audit Trail Events') }}</div>
                            </div>
                        </div>

                        <!-- Runtime & Cluster Spec -->
                        <div style="margin-top: 1.25rem; display: flex; flex-wrap: wrap; gap: 0.75rem; padding: 0.85rem; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 0.8125rem;">
                            <span style="color: #475569;"><strong>{{ transLabel('profile.database_engine', 'Database Cluster') }}:</strong> PostgreSQL 16.2 (Multi-AZ Replica)</span>
                            <span style="color: #94a3b8;">&bull;</span>
                            <span style="color: #475569;"><strong>{{ transLabel('profile.framework_runtime', 'Platform Runtime') }}:</strong> Laravel {{ $stats['laravel_version'] }} (PHP {{ $stats['php_version'] }})</span>
                            <span style="color: #94a3b8;">&bull;</span>
                            <span style="color: #475569;"><strong>Build:</strong> {{ $stats['system_version'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Administrative Audit Trail Actions -->
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div class="card-header-left">
                            <div class="card-header-icon" style="background: #f1f5f9; color: #334155;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </div>
                            <div>
                                <div class="card-header-title">{{ transLabel('profile.recent_audit_trail', 'Recent Administrative Audit Actions') }}</div>
                                <div class="card-header-desc">{{ transLabel('profile.recent_audit_desc', 'Immutable audit record of your most recent governance actions') }}</div>
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
                            <div style="text-align: center; padding: 2rem; color: #94a3b8; font-size: 0.85rem;">
                                {{ transLabel('profile.no_audit_events', 'No recent audit events recorded for this account.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- 4. TAB PANE 2: ACCOUNT SETTINGS (EDIT & UPDATE PROFILE) -->
    <div class="tab-pane {{ $activeTab === 'settings' ? 'active' : '' }}" id="settingsTabPane">
        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" id="adminProfileForm">
            @csrf
            
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="card-header-left">
                        <div class="card-header-icon" style="background: #eef2ff; color: #4338ca;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        </div>
                        <div>
                            <div class="card-header-title">{{ transLabel('profile.personal_contact_profile', 'Personal & Contact Profile') }}</div>
                            <div class="card-header-desc">{{ transLabel('profile.personal_contact_desc', 'Update your staff name, contact channels, avatar and biography') }}</div>
                        </div>
                    </div>
                </div>

                <div class="settings-card-body">
                    <!-- Avatar Management Section -->
                    <div class="avatar-picker-section">
                        <div class="avatar-preview-box">
                            <img src="{{ $user->profile_image_url ?? asset('images/avatar_seng_sourng.jpg') }}" alt="Avatar Preview" class="avatar-preview-img" id="avatarPreviewTarget" onerror="this.src='https://ui-avatars.com/api/?name=Super+Admin&background=1E1B4B&color=fff'">
                        </div>
                        <div class="avatar-picker-content">
                            <div style="font-size: 0.875rem; font-weight: 700; color: #0f172a;">{{ transLabel('profile.profile_picture', 'Profile Picture') }}</div>
                            <div style="font-size: 0.775rem; color: #64748b; margin-top: 2px;">
                                {{ transLabel('profile.merchant_logo_help', 'Upload a square image (PNG, JPG, WebP max 2MB) or pick a preset avatar.') }}
                            </div>

                            <input type="file" name="avatar_file" id="avatarFileInput" accept="image/*" style="display: none;" onchange="previewAvatarUpload(this)">
                            <input type="hidden" name="preset_avatar" id="presetAvatarInput" value="{{ $user->avatar }}">

                            <div style="margin-top: 0.75rem; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                <button type="button" class="btn-brand-secondary" style="padding: 0.45rem 0.95rem; font-size: 0.8125rem;" onclick="document.getElementById('avatarFileInput').click()">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    {{ transLabel('profile.upload_photo', 'Upload Photo') }}
                                </button>
                                
                                <span style="font-size: 0.75rem; color: #94a3b8;">{{ transLabel('profile.or_select_preset', 'or select preset:') }}</span>

                                <div class="preset-avatar-chips">
                                    <div class="preset-chip {{ $user->avatar === 'images/avatar_seng_sourng.jpg' || empty($user->avatar) ? 'active' : '' }}" onclick="selectPresetAvatar('{{ asset('images/avatar_seng_sourng.jpg') }}', this)">
                                        <img src="{{ asset('images/avatar_seng_sourng.jpg') }}" alt="Admin Sourng" onerror="this.src='https://ui-avatars.com/api/?name=SENG+Sourng&background=1E1B4B&color=fff'">
                                        <span>Sourng</span>
                                    </div>
                                    <div class="preset-chip {{ $user->avatar === 'images/avatar_sourng.jpg' ? 'active' : '' }}" onclick="selectPresetAvatar('{{ asset('images/avatar_sourng.jpg') }}', this)">
                                        <img src="{{ asset('images/avatar_sourng.jpg') }}" alt="Executive Sourng" onerror="this.src='https://ui-avatars.com/api/?name=Sourng&background=1D4ED8&color=fff'">
                                        <span>Executive</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Owner Inputs in 2-column row -->
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="full_name">{{ transLabel('form.full_name', 'Full Name') }} <span class="required">*</span></label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </span>
                                <input type="text" name="full_name" id="full_name" class="form-control" value="{{ old('full_name', $user->full_name) }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">{{ transLabel('profile.work_email', 'Work Email Address') }} <span class="required">*</span></label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                </span>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="phone">{{ transLabel('form.phone_number', 'Phone Number') }}</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </span>
                                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+855 12 888 001">
                            </div>
                            <div class="form-help">{{ transLabel('profile.phone_help', 'Used for shift updates and urgent delivery contact.') }}</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ transLabel('profile.preferred_lang', 'Preferred Operations Language') }}</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                </span>
                                <select class="form-control" onchange="document.getElementById('adminLangSelectForm_' + this.value).submit()">
                                    <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English (US)</option>
                                    <option value="km" {{ app()->getLocale() === 'km' ? 'selected' : '' }}>ភាសាខ្មែរ (Khmer)</option>
                                </select>
                            </div>
                            <div class="form-help">{{ transLabel('profile.lang_help', 'Changes dashboard, queue alerts, and print ticket language.') }}</div>
                        </div>
                    </div>

                    <!-- Designation & Architectural Scope -->
                    <div class="form-group" style="margin-top: 0.5rem;">
                        <label class="form-label" for="bio">{{ transLabel('profile.admin_bio_label', 'Executive Designation & Architectural Scope') }}</label>
                        <textarea name="bio" id="bio" rows="3" class="form-control no-icon" placeholder="{{ transLabel('profile.default_admin_bio', 'Chief Platform Architect & Lead Administrator overseeing the national school meal e-commerce infrastructure, student nutrition compliance, and multi-school merchant operations.') }}">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <!-- Operational Preferences Toggles -->
                    <div style="margin-top: 1.75rem; border-top: 1px dashed #cbd5e1; padding-top: 1.5rem;">
                        <div class="card-header-title" style="margin-bottom: 0.25rem;">{{ transLabel('profile.admin_notifs', 'Executive Platform Telemetry & Notifications') }}</div>
                        <div class="card-header-desc" style="margin-bottom: 1rem;">{{ transLabel('profile.admin_notifs_desc', 'Configure high-priority platform security alarms and incident reports') }}</div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">{{ transLabel('profile.notif_outages', 'System Outage & Service Disruption Alerts') }}</div>
                                <div class="pref-label-desc">{{ transLabel('profile.notif_outages_desc', 'Instant dispatch when database response latency exceeds 500ms or microservices fail') }}</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="notify_orders" value="1" {{ ($user->notification_preferences['orders'] ?? true) ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">{{ transLabel('profile.notif_security', 'Critical Security & Intrusion Alerts') }}</div>
                                <div class="pref-label-desc">{{ transLabel('profile.notif_security_desc', 'Notify immediately on repeated brute-force attempts or privilege escalation events') }}</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="notify_alerts" value="1" {{ ($user->notification_preferences['alerts'] ?? true) ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">{{ transLabel('profile.notif_audit_digest', 'Daily Consolidated Audit Trail Digest') }}</div>
                                <div class="pref-label-desc">{{ transLabel('profile.notif_audit_digest_desc', 'Receive end-of-day encrypted executive report of all financial and administrative actions') }}</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="notify_sound" value="1" {{ ($user->notification_preferences['sound'] ?? true) ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">{{ transLabel('profile.notif_perf_reports', 'Weekly Platform Performance & Capacity Reports') }}</div>
                                <div class="pref-label-desc">{{ transLabel('profile.notif_perf_reports_desc', 'Analytics on school nutrition meal orders, database load, and storage metrics') }}</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="notify_reports" value="1" {{ ($user->notification_preferences['reports'] ?? false) ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="settings-card-footer">
                    <button type="reset" class="btn-brand-secondary" onclick="window.Toast ? window.Toast.info('{{ addslashes(transLabel('profile.toast_reset', 'Form inputs reset to saved state.')) }}') : null;">
                        {{ transLabel('profile.btn_reset', 'Reset') }}
                    </button>
                    <button type="submit" class="btn-brand-primary" id="saveAdminProfileBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ transLabel('profile.btn_save_changes', 'Save Changes') }}
                    </button>
                </div>
            </div>
        </form>

        <!-- Hidden forms for locale switcher from select -->
        <form id="adminLangSelectForm_en" method="POST" action="{{ route('locale.switch') }}" style="display:none;">@csrf<input type="hidden" name="locale" value="en"></form>
        <form id="adminLangSelectForm_km" method="POST" action="{{ route('locale.switch') }}" style="display:none;">@csrf<input type="hidden" name="locale" value="km"></form>
    </div>

    <!-- 5. TAB PANE 3: SECURITY & SESSIONS (/admin/profile/security) -->
    <div class="tab-pane {{ $activeTab === 'security' ? 'active' : '' }}" id="securityTabPane">
        <div class="profile-grid-2">
            
            <!-- Left Column: Password Update & 2FA -->
            <div>
                <form method="POST" action="{{ route('admin.profile.password') }}" id="adminPasswordForm">
                    @csrf
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="card-header-left">
                                <div class="card-header-icon" style="background: #eff6ff; color: #1D4ED8;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </div>
                                <div>
                                    <div class="card-header-title">{{ transLabel('profile.change_password', 'Change Password') }}</div>
                                    <div class="card-header-desc">{{ transLabel('profile.change_password_desc', 'Protect your administrator account with a strong passphrase') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="settings-card-body">
                            <div class="form-group">
                                <label class="form-label" for="adminCurrentPassword">{{ transLabel('profile.current_password', 'Current Password') }} <span class="required">*</span></label>
                                <div class="form-input-wrap">
                                    <span class="form-input-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 18v3c0 .6.4 1 1 1h4v-3h3v-3h2l1.4-1.4a6.5 6.5 0 1 0-4-4Z"/><circle cx="16.5" cy="7.5" r=".5"/></svg>
                                    </span>
                                    <input type="password" name="current_password" id="adminCurrentPassword" class="form-control" required placeholder="{{ transLabel('profile.current_password_placeholder', 'Enter your current password') }}" autocomplete="current-password">
                                    <button type="button" class="btn-brand-secondary" style="position: absolute; right: 6px; height: 32px; padding: 0 8px; border: none; background: transparent;" onclick="togglePasswordVisibility('adminCurrentPassword', this)" title="Show/Hide Password">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="adminNewPassword">{{ transLabel('profile.new_password', 'New Password') }} <span class="required">*</span></label>
                                <div class="form-input-wrap">
                                    <span class="form-input-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </span>
                                    <input type="password" name="password" id="adminNewPassword" class="form-control" required placeholder="{{ transLabel('profile.new_password_placeholder', 'Enter at least 8 strong characters') }}" oninput="evaluatePasswordStrength(this.value)" autocomplete="new-password">
                                    <button type="button" class="btn-brand-secondary" style="position: absolute; right: 6px; height: 32px; padding: 0 8px; border: none; background: transparent;" onclick="togglePasswordVisibility('adminNewPassword', this)" title="Show/Hide Password">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>

                                <!-- Password Strength Meter -->
                                <div class="password-strength-container">
                                    <div class="strength-meter-bar">
                                        <div class="strength-meter-fill" id="strengthFill"></div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span id="strengthText" style="font-size: 0.725rem; font-weight: 700; color: #64748b;">{{ transLabel('profile.password_security_level', 'Password security level') }}</span>
                                        <span style="font-size: 0.725rem; color: #94a3b8;">{{ transLabel('profile.min_chars_required', 'Min 8 chars required') }}</span>
                                    </div>

                                    <div class="password-rules-list">
                                        <div class="rule-item" id="ruleLength">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>{{ transLabel('profile.rule_8_chars', '8+ Characters') }}</span>
                                        </div>
                                        <div class="rule-item" id="ruleUpper">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>{{ transLabel('profile.rule_upper', 'Uppercase letter') }}</span>
                                        </div>
                                        <div class="rule-item" id="ruleLower">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>{{ transLabel('profile.rule_lower', 'Lowercase letter') }}</span>
                                        </div>
                                        <div class="rule-item" id="ruleNumber">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>{{ transLabel('profile.rule_number', 'Number or symbol') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="adminConfirmPassword">{{ transLabel('profile.confirm_new_password', 'Confirm New Password') }} <span class="required">*</span></label>
                                <div class="form-input-wrap">
                                    <span class="form-input-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                                    </span>
                                    <input type="password" name="password_confirmation" id="adminConfirmPassword" class="form-control" required placeholder="{{ transLabel('profile.repeat_password_placeholder', 'Repeat your new password') }}" autocomplete="new-password">
                                    <button type="button" class="btn-brand-secondary" style="position: absolute; right: 6px; height: 32px; padding: 0 8px; border: none; background: transparent;" onclick="togglePasswordVisibility('adminConfirmPassword', this)" title="Show/Hide Password">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="settings-card-footer">
                            <button type="submit" class="btn-brand-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                {{ transLabel('profile.btn_update_password', 'Update Password') }}
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Two-Factor Authentication (2FA) Status -->
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div class="card-header-left">
                            <div class="card-header-icon" style="background: #ecfdf5; color: #059669;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <div>
                                <div class="card-header-title">{{ transLabel('profile.two_factor_auth', 'Two-Factor Authentication') }}</div>
                                <div class="card-header-desc">{{ transLabel('profile.two_factor_desc', 'Multi-Factor Verification on Portal Sign-In') }}</div>
                            </div>
                        </div>
                        <span class="profile-status-pill" style="background: #dcfce7; color: #15803d; border-color: #bbf7d0;">
                            {{ transLabel('profile.recommended', 'Recommended') }}
                        </span>
                    </div>
                    <div class="settings-card-body">
                        <div style="display: flex; align-items: flex-start; gap: 1rem;">
                            <div style="font-size: 0.84rem; color: #475569; line-height: 1.5; flex: 1;">
                                {{ transLabel('profile.two_factor_body', 'Two-factor authentication (SMS OTP & Google Authenticator) safeguards account and order queues against unauthorized changes or cancellations.') }}
                            </div>
                        </div>
                        <div style="margin-top: 1rem; display: flex; align-items: center; justify-content: space-between; padding: 0.85rem; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.825rem; font-weight: 700; color: #0f172a;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                {{ transLabel('profile.hw_authenticator_active', 'Hardware Key & Authenticator App Active') }}
                            </div>
                            <button type="button" class="btn-brand-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;" onclick="if(window.Toast) window.Toast.info('{{ addslashes(transLabel('profile.root_2fa_enforced', 'Root 2FA authentication is enforced platform-wide.')) }}');">
                                {{ transLabel('profile.btn_configure', 'Configure') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Active Device Sessions -->
            <div>
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div class="card-header-left">
                            <div class="card-header-icon" style="background: #f1f5f9; color: #334155;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            </div>
                            <div>
                                <div class="card-header-title">{{ transLabel('profile.active_devices_title', 'Active Devices & Sessions') }}</div>
                                <div class="card-header-desc">{{ transLabel('profile.active_devices_desc', 'Manage all devices where your account is signed in') }}</div>
                            </div>
                        </div>

                        <!-- Bulk Logout Other Sessions Form -->
                        @if(count($sessions) > 1)
                        <form method="POST" action="{{ route('admin.profile.sessions.logout_other') }}" onsubmit="return confirm('{{ addslashes(transLabel('profile.confirm_logout_others', 'Log out of all other devices except this browser?')) }}')">
                            @csrf
                            <button type="submit" class="btn-brand-danger">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                                {{ transLabel('profile.btn_logout_others', 'Log Out Others') }}
                            </button>
                        </form>
                        @endif
                    </div>

                    <div class="settings-card-body">
                        <div style="font-size: 0.8125rem; color: #64748b; margin-bottom: 1.15rem;">
                            {{ transLabel('profile.session_warning', 'If you notice an unfamiliar device or IP address, revoke the session immediately and change your password.') }}
                        </div>

                        @forelse($sessions as $session)
                        <div class="session-item-card {{ $session['is_current'] ? 'current' : '' }}">
                            <div class="session-info-left">
                                <div class="session-device-icon-box">
                                    @if($session['icon'] === 'mobile')
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                                    @elseif($session['icon'] === 'tablet')
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                                    @elseif($session['icon'] === 'laptop')
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 16V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v11"/><path d="M2 20h20"/></svg>
                                    @else
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="session-meta-name">
                                        {{ $session['browser'] }} &bull; {{ $session['platform'] }}
                                    </div>
                                    <div class="session-meta-sub">
                                        <span>IP: {{ $session['ip_address'] }}</span>
                                        <span>&bull;</span>
                                        <span title="{{ $session['last_active_exact'] }}">{{ transLabel('profile.last_seen', ['time' => $session['last_active']], 'Last seen :time') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                @if($session['is_current'])
                                    <span class="current-session-badge">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        {{ transLabel('profile.this_device', 'This Device') }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('admin.profile.sessions.destroy', $session['id']) }}" onsubmit="return confirm('{{ addslashes(transLabel('profile.confirm_revoke', 'Revoke this device session?')) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-brand-danger" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">
                                            {{ transLabel('profile.btn_revoke', 'Revoke') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div style="text-align: center; padding: 2rem; color: #94a3b8;">
                            {{ transLabel('profile.no_other_sessions', 'No other active device sessions found.') }}
                        </div>
                        @endforelse

                        <!-- Security Best Practices Tip -->
                        <div style="margin-top: 1.5rem; padding: 0.85rem; background: #f0fdf4; border-radius: 10px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 0.75rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            <span style="font-size: 0.785rem; color: #166534; font-weight: 500;">
                                {{ transLabel('profile.admin_session_tip', 'Super Admin root sessions maintain the highest platform access privileges. Always revoke unused sessions.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

@include('profile.partials.scripts')

<script>
    /**
     * Client-side tab switching for Super Admin profile with URL path update
     */
    function switchAdminProfileTab(tabName, event) {
        if (event && !event.ctrlKey && !event.metaKey) {
            event.preventDefault();
        }

        // Hide all tabs
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.profile-tab-btn').forEach(el => el.classList.remove('active'));

        // Show active tab
        const targetTab = document.getElementById(tabName + 'TabPane');
        const targetBtn = document.getElementById(tabName + 'TabBtn');

        if (targetTab) targetTab.classList.add('active');
        if (targetBtn) targetBtn.classList.add('active');

        // Update URL path seamlessly
        if (window.history && window.history.pushState) {
            let newPath = '{{ route("admin.profile") }}';
            if (tabName === 'settings') {
                newPath = '{{ route("admin.profile.settings") }}';
            } else if (tabName === 'security') {
                newPath = '{{ route("admin.profile.security") }}';
            }
            window.history.pushState({ tab: tabName }, '', newPath);
        }
    }
</script>
@endsection
