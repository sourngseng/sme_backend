<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body>

    <!-- Mobile Drawer Overlay Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebarCollapse()"></div>

    <!-- Driver Fleet Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Brand / Header -->
        <div class="sidebar-brand-box">
            <div style="width: 38px; height: 38px; background: linear-gradient(135deg, #059669 0%, #10B981 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 900; font-size: 1.15rem; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4); flex-shrink: 0;">
                DR
            </div>
            <div style="line-height: 1.2; overflow: hidden;">
                <div class="sidebar-brand-title">SchoolMeal</div>
                <div class="sidebar-brand-subtitle">{{ transLabel('form.role_driver', 'Driver Fleet Portal') }}</div>
            </div>
            <button type="button" onclick="toggleSidebarCollapse()" class="sidebar-close-btn" style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer; display: none;">✕</button>
        </div>

        <!-- Navigation Menu -->
        <div class="sidebar-menu">
            <!-- Primary Dashboard -->
            <a href="{{ Route::has('driver.dashboard') ? route('driver.dashboard') : route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('driver.dashboard*') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span>{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
                </div>
            </a>

            <!-- Category: DELIVERIES & ROUTES -->
            <div class="sidebar-section-title">{{ transLabel('menu.deliveries_routes_sec', 'DELIVERIES & ROUTES') }}</div>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                    <span>{{ transLabel('menu.active_deliveries', 'Active Deliveries') }}</span>
                </div>
                <span class="sidebar-badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; font-size: 0.7rem; font-weight: 800; padding: 2px 7px; border-radius: 999px;">8</span>
            </a>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/></svg>
                    <span>{{ transLabel('menu.live_nav_map', 'Live Navigation Map') }}</span>
                </div>
            </a>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ transLabel('menu.proof_delivery', 'Proof of Delivery') }}</span>
                </div>
            </a>

            <!-- Category: EARNINGS & STATS -->
            <div class="sidebar-section-title">{{ transLabel('menu.earnings_stats_sec', 'EARNINGS & STATS') }}</div>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 6v12"/></svg>
                    <span>{{ transLabel('menu.earnings_trips', 'Earnings & Trips') }}</span>
                </div>
            </a>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    <span>{{ transLabel('menu.driver_profile', 'Driver Profile') }}</span>
                </div>
            </a>
        </div>
    </aside>

    <!-- Main Workspace -->
    <div class="admin-main">
        <!-- Topbar with Improved Notification Dropdown -->
        @include('layouts.partials.topbar')

        <!-- Main Content View -->
        <main class="admin-content">
            @hasSection('header_title')
                <div style="margin-bottom: 1.25rem;">
                    <h1 style="font-size: 1.45rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em;">@yield('header_title')</h1>
                </div>
            @endif
            @yield('content')
        </main>

        <!-- Driver Mobile Bottom Navigation Bar -->
        <nav class="mobile-bottom-bar" id="mobileBottomBar" aria-label="Mobile Navigation">
            <a href="{{ Route::has('driver.dashboard') ? route('driver.dashboard') : route('admin.dashboard') }}" class="mobile-nav-tab active">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
            </a>

            <a href="javascript:void(0)" class="mobile-nav-tab">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.deliveries', 'Deliveries') }}</span>
            </a>

            <a href="javascript:void(0)" class="mobile-nav-tab">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.map', 'Map') }}</span>
            </a>

            <button type="button" class="mobile-nav-tab" onclick="toggleNotifDropdown(event)" id="mobileTabNotifs">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    <span class="mobile-tab-badge">5</span>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.alerts', 'Alerts') }}</span>
            </button>

            <button type="button" class="mobile-nav-tab" onclick="toggleSidebarCollapse()" id="mobileTabMenu">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.menu', 'Menu') }}</span>
            </button>
        </nav>
    </div>

    <!-- Floating Global Toast Notifications Container -->
    <x-toast-container />

    <!-- Interactive Scripts -->
    @include('layouts.partials.scripts')
</body>
</html>
