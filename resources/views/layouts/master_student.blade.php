<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body>

    <!-- Mobile Drawer Overlay Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebarCollapse()"></div>

    <!-- Student Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Brand / Header -->
        @include('layouts.partials.sidebar_brand')

        <!-- Navigation Menu -->
        <div class="sidebar-menu">
            <!-- Primary Dashboard -->
            <a href="{{ Route::has('student.dashboard') ? route('student.dashboard') : route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('student.dashboard*') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span>{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
                </div>
            </a>

            <!-- Category: MY MEALS -->
            <div class="sidebar-section-title">{{ transLabel('menu.my_meals_sec', 'MY MEALS') }}</div>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2v6a3 3 0 0 1-3 3 3 3 0 0 1-3-3V2"/><path d="M15 2v19"/><path d="M6 2v19"/><path d="M9 2a3 3 0 0 1-3 3 3 3 0 0 1-3-3"/></svg>
                    <span>{{ transLabel('menu.todays_lunch', "Today's Lunch") }}</span>
                </div>
            </a>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>{{ transLabel('menu.weekly_menu', 'Weekly Menu') }}</span>
                </div>
            </a>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/></svg>
                    <span>{{ transLabel('menu.my_meal_qr', 'My Meal QR Code') }}</span>
                </div>
            </a>

            <!-- Category: NUTRITION & HEALTH -->
            <div class="sidebar-section-title">{{ transLabel('menu.nutrition_health_sec', 'NUTRITION & HEALTH') }}</div>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    <span>{{ transLabel('menu.nutritional_score', 'Nutritional Score') }}</span>
                </div>
            </a>

            <a href="javascript:void(0)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <span>{{ transLabel('menu.meal_feedback', 'Meal Feedback & Ratings') }}</span>
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

        <!-- Student Mobile Bottom Navigation Bar -->
        <nav class="mobile-bottom-bar" id="mobileBottomBar" aria-label="Mobile Navigation">
            <a href="{{ Route::has('student.dashboard') ? route('student.dashboard') : route('admin.dashboard') }}" class="mobile-nav-tab active">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
            </a>

            <a href="javascript:void(0)" class="mobile-nav-tab">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2v6a3 3 0 0 1-3 3 3 3 0 0 1-3-3V2"/><path d="M15 2v19"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.lunch', 'Lunch') }}</span>
            </a>

            <a href="javascript:void(0)" class="mobile-nav-tab">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.meal_qr', 'QR Code') }}</span>
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
