<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body>

    <!-- Mobile Drawer Overlay Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebarCollapse()"></div>

    <!-- Customer Care & Support Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Brand / Header -->
        <div class="sidebar-brand-box">
            <div style="width: 38px; height: 38px; background: linear-gradient(135deg, #0D9488 0%, #06B6D4 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 900; font-size: 1.15rem; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.4); flex-shrink: 0;">
                CS
            </div>
            <div style="line-height: 1.2; overflow: hidden;">
                <div class="sidebar-brand-title">SchoolMeal</div>
                <div class="sidebar-brand-subtitle">{{ transLabel('form.role_support', 'Customer Care & Support Portal') }}</div>
            </div>
            <button type="button" onclick="toggleSidebarCollapse()" class="sidebar-close-btn" style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer; display: none;">✕</button>
        </div>

        <!-- Navigation Menu -->
        <div class="sidebar-menu">
            <!-- Primary Dashboard -->
            <a href="{{ Route::has('support.dashboard') ? route('support.dashboard') : route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('support.dashboard*') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span>{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
                </div>
            </a>

            <!-- Category: TICKETS & INQUIRIES -->
            <div class="sidebar-section-title">{{ transLabel('menu.tickets_inquiries_sec', 'TICKETS & INQUIRIES') }}</div>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>{{ transLabel('menu.inbound_tickets', 'Inbound Tickets') }}</span>
                </div>
                <span class="sidebar-badge" style="background: rgba(13, 148, 136, 0.15); color: #0d9488; font-size: 0.7rem; font-weight: 800; padding: 2px 7px; border-radius: 999px;">11</span>
            </a>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span>{{ transLabel('menu.escalations_disputes', 'Escalations & Disputes') }}</span>
                </div>
            </a>

            <!-- Category: RESOLUTION & AUDIT -->
            <div class="sidebar-section-title">{{ transLabel('menu.resolution_audit_sec', 'RESOLUTION & AUDIT') }}</div>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    <span>{{ transLabel('menu.refund_authorizations', 'Refund Authorizations') }}</span>
                </div>
            </a>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span>{{ transLabel('menu.knowledge_base', 'Knowledge Base') }}</span>
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

        <!-- Support Mobile Bottom Navigation Bar -->
        <nav class="mobile-bottom-bar" id="mobileBottomBar" aria-label="Mobile Navigation">
            <a href="{{ Route::has('support.dashboard') ? route('support.dashboard') : route('admin.dashboard') }}" class="mobile-nav-tab active">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
            </a>

            <a href="javascript:void(0)" class="mobile-nav-tab">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.tickets', 'Tickets') }}</span>
            </a>

            <a href="javascript:void(0)" class="mobile-nav-tab">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.disputes', 'Disputes') }}</span>
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
