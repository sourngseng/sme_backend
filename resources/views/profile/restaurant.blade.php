@extends('layouts.master_restaurant_owner')

@section('title', transLabel('profile.restaurant_title', 'Restaurant Merchant Profile & Security | SchoolMeal'))

@section('content')
@include('profile.partials.styles')

<div class="profile-page-wrapper">

    <!-- 1. HERO HEADER BANNER -->
    <div class="profile-hero">
        <div class="profile-hero-pattern"></div>
        <div class="profile-hero-glow" style="background: radial-gradient(circle, rgba(234, 88, 12, 0.35) 0%, rgba(234, 88, 12, 0) 70%);"></div>
        <div class="profile-hero-content">
            <div class="profile-hero-left">
                <div class="profile-avatar-container">
                    <img src="{{ $user->profile_image_url }}" alt="{{ $user->full_name }}" class="profile-hero-avatar" id="heroAvatarTarget" onerror="this.src='https://ui-avatars.com/api/?name=Restaurant+Owner&background=EA580C&color=fff'">
                    <div class="profile-avatar-badge" style="background: #f59e0b;" title="{{ transLabel('profile.verified_merchant_partner', 'Verified Merchant Partner') }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                </div>
                <div class="profile-meta-header">
                    <div class="profile-meta-title-row">
                        <h1 class="profile-meta-name">{{ $user->full_name }}</h1>
                        <div class="profile-badges-row">
                            <span class="profile-role-badge" style="background: rgba(234, 88, 12, 0.25); color: #fed7aa; border-color: rgba(253, 186, 116, 0.35);">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/></svg>
                                {{ transLabel('form.role_restaurant_owner', 'Restaurant Owner') }}
                            </span>
                            <span class="profile-status-pill">
                                <span class="status-pulse-dot"></span>
                                {{ transLabel('profile.verified_merchant', 'Verified Merchant') }}
                            </span>
                        </div>
                    </div>
                    <div class="profile-meta-subtitle">
                        {{ $restaurant->name ?? 'Green Garden Organic Kitchen' }} &bull; {{ transLabel('profile.facility_code', 'Code') }}: {{ $restaurant->code ?? 'REST-001' }}
                    </div>
                    <div class="profile-meta-sub">
                        <div class="profile-meta-sub-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <span>{{ $user->email }}</span>
                        </div>
                        <div class="profile-meta-sub-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>{{ $user->phone ?? '+855 12 000 004' }}</span>
                        </div>
                        <div class="profile-meta-sub-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span>{{ transLabel('profile.partner_since', ['date' => $user->created_at ? $user->created_at->format('M Y') : 'Oct 2026'], 'Partner since :date') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Merchant Quick Stats -->
            <div class="profile-hero-quick-stats">
                <div class="hero-stat-box">
                    <div class="hero-stat-val">{{ $restaurant->menu_items_count ?? 18 }}</div>
                    <div class="hero-stat-lbl">{{ transLabel('profile.active_menus', 'Active Menus') }}</div>
                </div>
                <div class="hero-stat-box">
                    <div class="hero-stat-val" style="color: #fbbf24;">{{ $restaurant->commission_default_pct ?? 12.5 }}%</div>
                    <div class="hero-stat-lbl">{{ transLabel('profile.commission', 'Commission') }}</div>
                </div>
                <div class="hero-stat-box">
                    <div class="hero-stat-val" style="color: #60a5fa;">{{ count($sessions) }}</div>
                    <div class="hero-stat-lbl">{{ transLabel('profile.active_sessions', 'Active Sessions') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. NAVIGATION TABS -->
    <div class="profile-tabs-nav" role="tablist">
        <button type="button" class="profile-tab-btn {{ $activeTab === 'overview' ? 'active' : '' }}" id="overviewTabBtn" onclick="switchProfileTab('overview', event)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>{{ transLabel('profile.tab_overview', 'My Profile') }}</span>
        </button>

        <button type="button" class="profile-tab-btn {{ $activeTab === 'settings' ? 'active' : '' }}" id="settingsTabBtn" onclick="switchProfileTab('settings', event)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span>{{ transLabel('profile.tab_settings', 'Account Settings') }}</span>
        </button>

        <button type="button" class="profile-tab-btn {{ $activeTab === 'security' ? 'active' : '' }}" id="securityTabBtn" onclick="switchProfileTab('security', event)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>{{ transLabel('profile.tab_security', 'Security & Sessions') }}</span>
            <span class="profile-tab-badge">{{ count($sessions) }}</span>
        </button>
    </div>

    <!-- 3. TAB PANE 1: MY PROFILE (VIEW ONLY) -->
    <div class="tab-pane {{ $activeTab === 'overview' ? 'active' : '' }}" id="overviewTabPane">
        <div class="profile-grid-sidebar">
            
            <!-- Left Column: Merchant Identity Card -->
            <div>
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div class="card-header-left">
                            <div class="card-header-icon" style="background: #fff7ed; color: #ea580c;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <div>
                                <div class="card-header-title">{{ transLabel('profile.merchant_principal', 'Merchant Principal') }}</div>
                                <div class="card-header-desc">{{ transLabel('profile.licensed_partner_id', 'Licensed Partner Identity') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="settings-card-body">
                        <div class="info-list-group">
                            <div class="info-item">
                                <span class="info-label">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                    {{ transLabel('profile.merchant_id', 'Merchant ID') }}
                                </span>
                                <span class="info-value" style="font-family: monospace; color: #1D4ED8; cursor: pointer;" onclick="copyProfileText('{{ $user->id }}', '{{ addslashes(transLabel('profile.merchant_id', 'Merchant ID')) }}')">
                                    {{ substr($user->id, 0, 13) }}...
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                    {{ transLabel('profile.settlement_freq', 'Settlement Frequency') }}
                                </span>
                                <span class="info-value">{{ transLabel('profile.weekly_auto', 'Weekly Automatic') }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                                    {{ transLabel('profile.partner_status', 'Partner Status') }}
                                </span>
                                <span class="info-value" style="color: #10b981;">{{ transLabel('profile.active_tier_1', '● Active (Tier 1 Verified)') }}</span>
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem;">
                            <div class="form-label">{{ transLabel('profile.merchant_bio_vision', 'Merchant Bio & Vision') }}</div>
                            <div style="padding: 0.85rem; background: #f8fafc; border-radius: 10px; font-size: 0.825rem; color: #475569; line-height: 1.5; border: 1px solid #e2e8f0;">
                                {{ $user->bio ?? 'Promoting healthy organic school meals across Phnom Penh campuses. Committed to zero-MSG, fresh morning preparation, and farm-to-school ingredient transparency.' }}
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem;">
                            <button type="button" class="btn-brand-primary" style="flex: 1; justify-content: center;" onclick="switchProfileTab('settings', event)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                {{ transLabel('profile.edit_settings', 'Edit Settings') }}
                            </button>
                            <button type="button" class="btn-brand-secondary" style="flex: 1; justify-content: center;" onclick="switchProfileTab('security', event)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                {{ transLabel('profile.security', 'Security') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Restaurant Business & Entity Details -->
            <div>
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div class="card-header-left">
                            <div class="card-header-icon" style="background: #eff6ff; color: #1D4ED8;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/><path d="M5 21V10.85"/><path d="M19 21V10.85"/><path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/></svg>
                            </div>
                            <div>
                                <div class="card-header-title">{{ transLabel('profile.restaurant_biz_entity', 'Restaurant Business Entity') }}</div>
                                <div class="card-header-desc">{{ transLabel('profile.commercial_profile_desc', 'Commercial Profile & Campus Delivery Partner') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="settings-card-body">
                        <div class="profile-grid-2">
                            <div class="info-list-group">
                                <div class="info-item">
                                    <span class="info-label">{{ transLabel('profile.brand_name', 'Brand Name') }}</span>
                                    <span class="info-value">{{ $restaurant->name ?? 'Green Garden Organic Kitchen' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">{{ transLabel('profile.license_code', 'License Code') }}</span>
                                    <span class="info-value" style="font-family: monospace;">{{ $restaurant->code ?? 'REST-001' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">{{ transLabel('profile.platform_rate', 'Platform Rate') }}</span>
                                    <span class="info-value" style="color: #2563EB;">{{ $restaurant->commission_default_pct ?? 12.5 }}% {{ transLabel('profile.commission', 'Commission') }}</span>
                                </div>
                            </div>

                            <div class="info-list-group">
                                <div class="info-item">
                                    <span class="info-label">{{ transLabel('profile.kitchen_inquiries', 'Kitchen Inquiries') }}</span>
                                    <span class="info-value">{{ $restaurant->email ?? 'kitchen@greengarden.test' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">{{ transLabel('profile.direct_hotline', 'Direct Hotline') }}</span>
                                    <span class="info-value">{{ $restaurant->phone ?? '+855 12 999 001' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">{{ transLabel('profile.hygiene_rating', 'Hygiene Rating') }}</span>
                                    <span class="info-value" style="color: #10b981;">{{ transLabel('profile.a_plus_standard', 'A+ Standard') }}</span>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 1.25rem;">
                            <div class="form-label">{{ transLabel('profile.central_kitchen_location', 'Central Kitchen & Pickup Location') }}</div>
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 0.84rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; color: #334155;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span>{{ $restaurant->address ?? 'No. 45 Street 310, Chamkarmon, Phnom Penh' }}</span>
                                </div>
                                <a href="https://maps.google.com/?q={{ $restaurant->latitude ?? 11.5451 }},{{ $restaurant->longitude ?? 104.9185 }}" target="_blank" class="btn-brand-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">
                                    {{ transLabel('profile.view_map', 'View Map') }}
                                </a>
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem;">
                            <div class="form-label">{{ transLabel('profile.auth_admin_capabilities', 'Authorized Administrative Capabilities') }}</div>
                            <div class="info-chips-container">
                                <span class="info-chip">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    {{ transLabel('profile.priv_menu_pricing', 'Menu Item Pricing & Recipe Management') }}
                                </span>
                                <span class="info-chip">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    {{ transLabel('profile.priv_order_acceptance', 'Real-Time School Order Dispatch Acceptance') }}
                                </span>
                                <span class="info-chip">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    {{ transLabel('profile.priv_staff_scheduling', 'Kitchen Staff Roster & Shift Scheduling') }}
                                </span>
                                <span class="info-chip">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    {{ transLabel('profile.priv_financial_statements', 'Payout Settlements & Commission Reports') }}
                                </span>
                                <span class="info-chip">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    {{ transLabel('profile.priv_dietary_standards', 'Dietary Certification & Hygiene Compliance') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- 4. TAB PANE 2: ACCOUNT SETTINGS (EDIT & UPDATE PROFILE) -->
    <div class="tab-pane {{ $activeTab === 'settings' ? 'active' : '' }}" id="settingsTabPane">
        <form method="POST" action="{{ route('restaurant.profile.update') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="card-header-left">
                        <div class="card-header-icon" style="background: #fff7ed; color: #ea580c;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        </div>
                        <div>
                            <div class="card-header-title">{{ transLabel('profile.personal_contact_profile', 'Personal & Contact Profile') }}</div>
                            <div class="card-header-desc">{{ transLabel('profile.personal_contact_desc', 'Update your merchant account, business brand contacts, and operating details') }}</div>
                        </div>
                    </div>
                </div>

                <div class="settings-card-body">
                    <!-- Avatar Management Section -->
                    <div class="avatar-picker-section">
                        <div class="avatar-preview-box">
                            <img src="{{ $user->profile_image_url }}" alt="Avatar Preview" class="avatar-preview-img" id="avatarPreviewTarget" onerror="this.src='https://ui-avatars.com/api/?name=Merchant&background=EA580C&color=fff'">
                        </div>
                        <div class="avatar-picker-content">
                            <div style="font-size: 0.875rem; font-weight: 700; color: #0f172a;">{{ transLabel('profile.merchant_profile_picture', 'Merchant Brand Logo / Avatar') }}</div>
                            <div style="font-size: 0.775rem; color: #64748b; margin-top: 2px;">
                                {{ transLabel('profile.merchant_logo_help', 'Upload a square image (PNG, JPG, WebP max 2MB) or pick a preset avatar.') }}
                            </div>
                            
                            <input type="file" name="avatar_file" id="avatarFileInput" accept="image/*" style="display: none;" onchange="previewAvatarUpload(this)">
                            <input type="hidden" name="preset_avatar" id="presetAvatarInput" value="">

                            <div style="margin-top: 0.75rem; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                <button type="button" class="btn-brand-secondary" style="padding: 0.45rem 0.95rem; font-size: 0.8125rem;" onclick="document.getElementById('avatarFileInput').click()">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    {{ transLabel('profile.upload_photo', 'Upload Photo') }}
                                </button>
                                
                                <span style="font-size: 0.75rem; color: #94a3b8;">{{ transLabel('profile.or_select_preset', 'or select preset:') }}</span>
                                
                                <div class="preset-avatar-chips">
                                    <div class="preset-chip" onclick="selectPresetAvatar('{{ asset('images/khmer_kitchen_avatar.jpg') }}', this)">
                                        <img src="{{ asset('images/khmer_kitchen_avatar.jpg') }}" alt="Khmer Kitchen">
                                        <span>Khmer Kitchen</span>
                                    </div>
                                    <div class="preset-chip" onclick="selectPresetAvatar('{{ asset('images/sokha_ngin.jpg') }}', this)">
                                        <img src="{{ asset('images/sokha_ngin.jpg') }}" alt="Sokha Ngin">
                                        <span>Sokha Ngin</span>
                                    </div>
                                    <div class="preset-chip" onclick="selectPresetAvatar('{{ asset('images/avatar_seng_sourng.jpg') }}', this)">
                                        <img src="{{ asset('images/avatar_seng_sourng.jpg') }}" alt="Admin Sourng">
                                        <span>Admin Sourng</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Owner Inputs -->
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="full_name">{{ transLabel('profile.merchant_name_label', 'Merchant Principal Full Name') }} <span class="required">*</span></label>
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
                            <label class="form-label" for="phone">{{ transLabel('profile.contact_phone', 'Contact Mobile Phone') }}</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </span>
                                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+855 12 345 678">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ transLabel('profile.preferred_lang', 'Preferred Operations Language') }}</label>
                            <div class="form-input-wrap">
                                <span class="form-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                </span>
                                <select class="form-control" onchange="document.getElementById('langSelectForm_' + this.value).submit()">
                                    <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English (US)</option>
                                    <option value="km" {{ app()->getLocale() === 'km' ? 'selected' : '' }}>ភាសាខ្មែរ (Khmer)</option>
                                </select>
                            </div>
                            <div class="form-help">{{ transLabel('profile.lang_help', 'Changes dashboard, queue alerts, and print ticket language.') }}</div>
                        </div>
                    </div>

                    <!-- Commercial Business Fields -->
                    <div style="margin-top: 1.5rem; border-top: 1px dashed #cbd5e1; padding-top: 1.25rem;">
                        <div class="card-header-title" style="margin-bottom: 0.25rem; font-size: 0.95rem;">{{ transLabel('profile.restaurant_biz_entity', 'Restaurant Business Entity') }}</div>
                        <div class="card-header-desc" style="margin-bottom: 1rem;">{{ transLabel('profile.commercial_profile_desc', 'Update partner venue branding, direct telephone, and central kitchen address') }}</div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label" for="restaurant_name">{{ transLabel('profile.restaurant_brand_label', 'Official Restaurant Brand Name') }}</label>
                                <div class="form-input-wrap">
                                    <span class="form-input-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/><path d="M5 21V10.85"/><path d="M19 21V10.85"/></svg>
                                    </span>
                                    <input type="text" name="restaurant_name" id="restaurant_name" class="form-control" value="{{ old('restaurant_name', $restaurant->name ?? '') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="restaurant_phone">{{ transLabel('profile.restaurant_hotline_label', 'Kitchen Hotline Number') }}</label>
                                <div class="form-input-wrap">
                                    <span class="form-input-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    </span>
                                    <input type="text" name="restaurant_phone" id="restaurant_phone" class="form-control" value="{{ old('restaurant_phone', $restaurant->phone ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label" for="restaurant_email">{{ transLabel('profile.restaurant_email_label', 'Business / Invoicing Email') }}</label>
                                <div class="form-input-wrap">
                                    <span class="form-input-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    </span>
                                    <input type="email" name="restaurant_email" id="restaurant_email" class="form-control" value="{{ old('restaurant_email', $restaurant->email ?? '') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="restaurant_address">{{ transLabel('profile.restaurant_address_label', 'Physical Kitchen Operating Address') }}</label>
                                <div class="form-input-wrap">
                                    <span class="form-input-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    </span>
                                    <input type="text" name="restaurant_address" id="restaurant_address" class="form-control" value="{{ old('restaurant_address', $restaurant->address ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label class="form-label" for="bio">{{ transLabel('profile.merchant_bio_label', 'Merchant Philosophy & Culinary Mission') }}</label>
                        <textarea name="bio" id="bio" class="form-control no-icon" rows="3" placeholder="{{ transLabel('profile.bio_placeholder', 'Describe your food philosophy, kitchen certifications, or sourcing commitments...') }}">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <!-- Operational Preferences Toggles -->
                    <div style="margin-top: 1.75rem; border-top: 1px solid #f1f5f9; padding-top: 1.5rem;">
                        <div class="card-header-title" style="margin-bottom: 0.25rem;">{{ transLabel('profile.merchant_notifs', 'Merchant Operations & Financial Notifications') }}</div>
                        <div class="card-header-desc" style="margin-bottom: 1rem;">{{ transLabel('profile.merchant_notifs_desc', 'Configure your SMS, sound chimes, and order settlement alerts') }}</div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">{{ transLabel('profile.auto_payout_alerts', 'Settlement & Payout Alerts') }}</div>
                                <div class="pref-label-desc">{{ transLabel('profile.auto_payout_desc', 'Receive instant notice whenever weekly school lunch proceeds are transferred') }}</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="notify_sound" value="1" {{ ($user->notification_preferences['sound'] ?? true) ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">{{ transLabel('profile.stock_shortage_warnings', 'Ingredient Stock Shortage Warnings') }}</div>
                                <div class="pref-label-desc">{{ transLabel('profile.stock_shortage_desc', 'Notify when high-demand recipe supplies fall below daily kitchen thresholds') }}</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="notify_alerts" value="1" {{ ($user->notification_preferences['alerts'] ?? true) ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>

                        <div class="pref-item-row">
                            <div>
                                <div class="pref-label-title">{{ transLabel('profile.parent_feedback_flashes', 'Parent & Student Feedback Flashes') }}</div>
                                <div class="pref-label-desc">{{ transLabel('profile.parent_feedback_desc', 'Receive real-time 5-star ratings and special culinary appreciation notes') }}</div>
                            </div>
                            <label class="switch-toggle">
                                <input type="checkbox" name="notify_orders" value="1" {{ ($user->notification_preferences['orders'] ?? true) ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="settings-card-footer">
                    <button type="reset" class="btn-brand-secondary" onclick="window.Toast ? window.Toast.info('{{ addslashes(transLabel('profile.toast_reset', 'Form inputs reset to saved state.')) }}') : null;">
                        {{ transLabel('profile.btn_reset', 'Reset') }}
                    </button>
                    <button type="submit" class="btn-brand-primary" id="saveRestaurantProfileBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ transLabel('profile.btn_save_changes', 'Save Changes') }}
                    </button>
                </div>
            </div>
        </form>

        <!-- Hidden forms for locale switcher from select -->
        <form id="langSelectForm_en" method="POST" action="{{ route('locale.switch') }}" style="display:none;">@csrf<input type="hidden" name="locale" value="en"></form>
        <form id="langSelectForm_km" method="POST" action="{{ route('locale.switch') }}" style="display:none;">@csrf<input type="hidden" name="locale" value="km"></form>
    </div>

    <!-- 5. TAB PANE 3: SECURITY & SESSIONS -->
    <div class="tab-pane {{ $activeTab === 'security' ? 'active' : '' }}" id="securityTabPane">
        <div class="profile-grid-2">
            
            <!-- Left Column: Password Update -->
            <div>
                <form method="POST" action="{{ route('restaurant.profile.password') }}">
                    @csrf
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="card-header-left">
                                <div class="card-header-icon" style="background: #eff6ff; color: #1D4ED8;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </div>
                                <div>
                                    <div class="card-header-title">{{ transLabel('profile.change_password', 'Change Password') }}</div>
                                    <div class="card-header-desc">{{ transLabel('profile.change_password_desc', 'Protect your restaurant merchant account with a strong passphrase') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="settings-card-body">
                            <div class="form-group">
                                <label class="form-label" for="current_password">{{ transLabel('profile.current_password', 'Current Password') }} <span class="required">*</span></label>
                                <div class="form-input-wrap">
                                    <span class="form-input-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 18v3c0 .6.4 1 1 1h4v-3h3v-3h2l1.4-1.4a6.5 6.5 0 1 0-4-4Z"/><circle cx="16.5" cy="7.5" r=".5"/></svg>
                                    </span>
                                    <input type="password" name="current_password" id="current_password" class="form-control" required placeholder="{{ transLabel('profile.current_password_placeholder', 'Enter your current password') }}">
                                    <button type="button" class="btn-brand-secondary" style="position: absolute; right: 6px; height: 32px; padding: 0 8px; border: none; background: transparent;" onclick="togglePasswordVisibility('current_password', this)" title="Show/Hide Password">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="new_password">{{ transLabel('profile.new_password', 'New Password') }} <span class="required">*</span></label>
                                <div class="form-input-wrap">
                                    <span class="form-input-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </span>
                                    <input type="password" name="password" id="new_password" class="form-control" required placeholder="{{ transLabel('profile.new_password_placeholder', 'Enter at least 8 strong characters') }}" oninput="evaluatePasswordStrength(this.value)">
                                    <button type="button" class="btn-brand-secondary" style="position: absolute; right: 6px; height: 32px; padding: 0 8px; border: none; background: transparent;" onclick="togglePasswordVisibility('new_password', this)" title="Show/Hide Password">
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
                                <label class="form-label" for="password_confirmation">{{ transLabel('profile.confirm_new_password', 'Confirm New Password') }} <span class="required">*</span></label>
                                <div class="form-input-wrap">
                                    <span class="form-input-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                                    </span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="{{ transLabel('profile.repeat_password_placeholder', 'Repeat your new password') }}">
                                    <button type="button" class="btn-brand-secondary" style="position: absolute; right: 6px; height: 32px; padding: 0 8px; border: none; background: transparent;" onclick="togglePasswordVisibility('password_confirmation', this)" title="Show/Hide Password">
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

                <!-- Two-Factor Authentication & Account Security Rating -->
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div class="card-header-left">
                            <div class="card-header-icon" style="background: #ecfdf5; color: #059669;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <div>
                                <div class="card-header-title">{{ transLabel('profile.two_factor_auth', 'Two-Factor Authentication') }}</div>
                                <div class="card-header-desc">{{ transLabel('profile.two_factor_merchant_desc', 'Multi-Factor Verification on Restaurant Admin & Settlement Authorizations') }}</div>
                            </div>
                        </div>
                        <span class="profile-status-pill" style="background: #dcfce7; color: #15803d; border-color: #bbf7d0;">
                            {{ transLabel('profile.recommended', 'Recommended') }}
                        </span>
                    </div>
                    <div class="settings-card-body">
                        <div style="display: flex; align-items: flex-start; gap: 1rem;">
                            <div style="font-size: 0.84rem; color: #475569; line-height: 1.5; flex: 1;">
                                {{ transLabel('profile.two_factor_merchant_body', 'Multi-factor authentication (SMS OTP & Google Authenticator) protects payout account details and sensitive order manifests.') }}
                            </div>
                        </div>
                        <div style="margin-top: 1rem; display: flex; align-items: center; justify-content: space-between; padding: 0.85rem; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.825rem; font-weight: 700; color: #0f172a;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                {{ transLabel('profile.sms_otp_ready', 'SMS OTP Verification Ready') }}
                            </div>
                            <button type="button" class="btn-brand-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;" onclick="window.Toast ? window.Toast.info('2FA settings synced with platform authentication.') : null;">
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
                        <form method="POST" action="{{ route('restaurant.profile.sessions.logout_other') }}" onsubmit="return confirm('{{ addslashes(transLabel('profile.confirm_logout_others', 'Log out of all other devices except this browser?')) }}')">
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
                                    <form method="POST" action="{{ route('restaurant.profile.sessions.destroy', $session['id']) }}" onsubmit="return confirm('{{ addslashes(transLabel('profile.confirm_revoke', 'Revoke this device session?')) }}')">
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
                                {{ transLabel('profile.merchant_session_tip', 'Always sign out of shared restaurant office terminals before end-of-day closure.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

@include('profile.partials.scripts')
@endsection