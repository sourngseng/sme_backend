<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body>

    <!-- Mobile Drawer Overlay Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebarCollapse()"></div>

    <!-- Parent & Family Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Brand / Header -->
        <div class="sidebar-brand-box">
            <div style="width: 38px; height: 38px; background: linear-gradient(135deg, #E11D48 0%, #F43F5E 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 900; font-size: 1.15rem; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.4); flex-shrink: 0;">
                PR
            </div>
            <div style="line-height: 1.2; overflow: hidden;">
                <div class="sidebar-brand-title">SchoolMeal</div>
                <div class="sidebar-brand-subtitle">{{ transLabel('form.role_parent', 'Parent & Family Portal') }}</div>
            </div>
            <button type="button" onclick="toggleSidebarCollapse()" class="sidebar-close-btn" style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer; display: none;">✕</button>
        </div>

        <!-- Navigation Menu -->
        <div class="sidebar-menu">
            <!-- Primary Dashboard -->
            <a href="{{ Route::has('parent.dashboard') ? route('parent.dashboard') : route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('parent.dashboard*') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span>{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
                </div>
            </a>

            <!-- Category: MY FAMILY -->
            <div class="sidebar-section-title">{{ transLabel('menu.my_family_sec', 'MY FAMILY') }}</div>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span>{{ transLabel('menu.my_children', 'My Children') }}</span>
                </div>
            </a>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                    <span>{{ transLabel('menu.dietary_allergies', 'Dietary & Allergies') }}</span>
                </div>
            </a>

            <!-- Category: MEAL ORDERS -->
            <div class="sidebar-section-title">{{ transLabel('menu.orders', 'MEAL ORDERS') }}</div>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2v6a3 3 0 0 1-3 3 3 3 0 0 1-3-3V2"/><path d="M15 2v19"/><path d="M6 2v19"/><path d="M9 2a3 3 0 0 1-3 3 3 3 0 0 1-3-3"/></svg>
                    <span>{{ transLabel('menu.browse_menus', 'Browse Menus') }}</span>
                </div>
            </a>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    <span>{{ transLabel('menu.meal_plans_orders', 'Meal Plans & Orders') }}</span>
                </div>
            </a>

            <!-- Category: WALLET & BILLING -->
            <div class="sidebar-section-title">{{ transLabel('menu.wallet_billing_sec', 'WALLET & BILLING') }}</div>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    <span>{{ transLabel('menu.family_wallet', 'Family Wallet (KHQR)') }}</span>
                </div>
            </a>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    <span>{{ transLabel('menu.settings', 'Family Preferences') }}</span>
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

        <!-- Parent Mobile Bottom Navigation Bar -->
        <nav class="mobile-bottom-bar" id="mobileBottomBar" aria-label="Mobile Navigation">
            <a href="{{ Route::has('parent.dashboard') ? route('parent.dashboard') : route('admin.dashboard') }}" class="mobile-nav-tab active">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
            </a>

            <a href="javascript:void(0)" class="mobile-nav-tab">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.children', 'Children') }}</span>
            </a>

            <a href="javascript:void(0)" class="mobile-nav-tab">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.orders', 'Orders') }}</span>
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
