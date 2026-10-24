<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
    <style>
        /* Exact Left Sidebar Design from Image 1 */
        .school-admin-sidebar {
            background-color: #051329 !important; /* Deep Navy from Image 1 */
            border-right: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* 3D Glossy Book Logo Container */
        .school-brand-box {
            padding: 1.25rem 1.15rem 1.15rem 1.15rem;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            min-height: 72px;
            flex-shrink: 0;
            position: relative;
        }

        .school-3d-logo {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: radial-gradient(circle at 30% 30%, #3b82f6 0%, #1e3a8a 100%);
            box-shadow: 0 6px 16px rgba(0, 102, 255, 0.4);
            position: relative;
        }

        .school-brand-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.15;
            letter-spacing: -0.01em;
        }

        .school-brand-subtitle {
            font-size: 0.68rem;
            color: #94a3b8;
            font-weight: 500;
            letter-spacing: 0.01em;
            margin-top: 0.15rem;
        }

        /* Active School Profile Card (Wat Bo Primary School) */
        .school-context-card {
            margin: 0.35rem 0.85rem 1rem 0.85rem;
            padding: 0.75rem 0.85rem;
            background: #0d2346;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.65rem;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(5, 19, 41, 0.4);
            text-decoration: none;
        }

        .school-context-card:hover {
            background: #112d59;
            border-color: rgba(56, 189, 248, 0.3);
            transform: translateY(-1px);
        }

        .school-card-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            object-fit: cover;
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            flex-shrink: 0;
        }

        .school-card-meta {
            min-width: 0;
            flex: 1;
            line-height: 1.25;
        }

        .school-card-name {
            font-size: 0.825rem;
            font-weight: 800;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .school-card-role {
            font-size: 0.7rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .school-card-chevron {
            color: #94a3b8;
            flex-shrink: 0;
        }

        /* Left Sidebar Items & Section Titles (Matching http://localhost:8000/admin/dashboard) */
        .school-nav-section-title {
            font-family: var(--font-sans);
            font-size: 0.6875rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0.85rem 0.65rem 0.35rem 0.65rem;
            user-select: none;
            white-space: nowrap;
        }

        .school-nav-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            color: #cbd5e1;
            text-decoration: none;
            font-family: var(--font-sans);
            font-size: 1.1rem !important; /* Identical to http://localhost:8000/admin/dashboard .sidebar-item */
            font-weight: 500;
            transition: all 0.15s ease;
            cursor: pointer;
            margin-bottom: 0.15rem;
            user-select: none;
            position: relative;
        }

        .school-nav-item:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
        }

        /* Image 1 Active State: Vibrant Electric Blue Pill with white text */
        .school-nav-item.active {
            background: #0066FF !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(0, 102, 255, 0.4);
        }

        .school-nav-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .school-nav-left svg {
            flex-shrink: 0;
        }

        /* Bottom Pinned Switch School Card */
        .switch-school-bottom-box {
            padding: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            margin-top: auto;
            background: #051329;
        }

        .btn-switch-school {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0.85rem;
            background: #0d2346;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: #ffffff;
            font-family: var(--font-sans);
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .btn-switch-school:hover {
            background: #112d59;
            border-color: rgba(0, 102, 255, 0.4);
            transform: translateY(-1px);
        }

        .btn-switch-school-left {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        /* Khmer Language Typography Adjustments (Matching http://localhost:8000/admin/dashboard) */
        html[lang="km"] .school-brand-title {
            font-size: 1.25rem !important;
            font-weight: 900 !important;
            letter-spacing: 0 !important;
            font-family: var(--font-khmer) !important;
        }

        html[lang="km"] .school-brand-subtitle {
            font-size: 0.785rem !important;
            font-weight: 700 !important;
            line-height: 1.4 !important;
            letter-spacing: 0 !important;
            font-family: var(--font-khmer) !important;
        }

        html[lang="km"] .school-card-role {
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            font-family: var(--font-khmer) !important;
        }

        html[lang="km"] .school-nav-section-title {
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.02em !important;
            text-transform: none !important;
            color: #64748b;
            font-family: var(--font-khmer) !important;
        }

        html[lang="km"] .school-nav-item {
            font-size: 1.1rem !important;
            font-weight: 500 !important;
            line-height: 1.55 !important;
            padding: 0.62rem 0.8rem !important;
            font-family: var(--font-khmer) !important;
        }

        html[lang="km"] .btn-switch-school {
            font-size: 0.95rem !important;
            font-family: var(--font-khmer) !important;
        }

        html[lang="km"] .mobile-tab-label {
            font-size: 0.76rem !important;
            font-weight: 700 !important;
            line-height: 1.3 !important;
            letter-spacing: 0 !important;
            font-family: var(--font-khmer) !important;
        }
    </style>
</head>
<body>

    <!-- Mobile Drawer Overlay Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebarCollapse()"></div>

    <!-- School Admin Sidebar (Image 1 Exact Layout) -->
    <aside class="admin-sidebar school-admin-sidebar" id="adminSidebar">
        <!-- 1. Brand Logo Header (Identical to Super Admin) -->
        @include('layouts.partials.sidebar_brand')

        <!-- 2. Active School Card (Wat Bo Primary School) -->
        <a href="{{ route('school.profile') }}" class="school-context-card" title="Active Campus: {{ $activeSchool->name ?? 'Wat Bo Primary School' }}">
            <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=100&h=100&fit=crop" alt="Campus" class="school-card-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Wat+Bo&background=10B981&color=fff';">
            <div class="school-card-meta">
                <div class="school-card-name">{{ $activeSchool->name ?? 'Wat Bo Primary School' }}</div>
                <div class="school-card-role">{{ transLabel('roles.school_admin', 'School Admin') }}</div>
            </div>
            <div class="school-card-chevron">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </a>

        <!-- 3. Navigation Menu Items (All prefixed with localhost:8000/school/...) -->
        <div class="sidebar-menu" style="padding: 0 0.65rem 1rem 0.65rem;">
            <!-- Primary Dashboard -->
            <a href="{{ route('school.dashboard') }}" class="school-nav-item sidebar-item {{ request()->routeIs('school.dashboard') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span>{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
                </div>
            </a>

            <!-- SECTION: SCHOOL MANAGEMENT -->
            <div class="school-nav-section-title sidebar-section-title">{{ transLabel('menu.school_management_sec', 'SCHOOL MANAGEMENT') }}</div>

            <!-- School Profile -->
            <a href="{{ route('school.profile') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'profile' || request()->is('school/profile*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m4 6 8-4 8 4"/><path d="M18 10 22 12v10H2V12l4-2"/><path d="M6 5v17"/><path d="M18 5v17"/></svg>
                    <span>{{ transLabel('menu.school_profile', 'School Profile') }}</span>
                </div>
            </a>

            <!-- School Shifts -->
            <a href="{{ route('school.shifts') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'shifts' || request()->is('school/shifts*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><circle cx="12" cy="14" r="2"/></svg>
                    <span>{{ transLabel('menu.school_shifts', 'School Shifts') }}</span>
                </div>
            </a>

            <!-- Locations -->
            <a href="{{ route('school.locations') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'locations' || request()->is('school/locations*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>{{ transLabel('menu.locations', 'Locations') }}</span>
                </div>
            </a>

            <!-- Students (Image 1 Active Highlight: Blue Pill) -->
            <a href="{{ route('school.students') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'students' || request()->is('school/students*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span>{{ transLabel('menu.students', 'Students') }}</span>
                </div>
            </a>

            <!-- Classes & Grades -->
            <a href="{{ route('school.classes_grades') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'classes-grades' || request()->is('school/classes*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><rect width="7" height="9" x="7" y="5" rx="1"/><rect width="7" height="5" x="7" y="14" rx="1"/></svg>
                    <span>{{ transLabel('menu.classes_grades', 'Classes & Grades') }}</span>
                </div>
            </a>

            <!-- SECTION: MEAL OPERATIONS -->
            <div class="school-nav-section-title sidebar-section-title">{{ transLabel('menu.meal_ops_sec', 'MEAL OPERATIONS') }}</div>

            <!-- Meal Roster -->
            <a href="{{ route('school.roster') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'roster' || request()->is('school/roster*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="m9 16 2 2 4-4"/></svg>
                    <span>{{ transLabel('menu.meal_roster', 'Meal Roster') }}</span>
                </div>
            </a>

            <!-- Orders -->
            <a href="{{ route('school.orders') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'orders' || request()->is('school/orders*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    <span>{{ transLabel('menu.orders', 'Orders') }}</span>
                </div>
            </a>

            <!-- Delivery -->
            <a href="{{ route('school.delivery') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'delivery' || request()->is('school/delivery*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17h4V5H2v12h3"/><path d="M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5v8h2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                    <span>{{ transLabel('menu.delivery', 'Delivery') }}</span>
                </div>
            </a>

            <!-- Nutrition & Allergies -->
            <a href="{{ route('school.nutrition') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'nutrition' || request()->is('school/nutrition*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10H12V2z"/><path d="M12 12 4.93 4.93"/></svg>
                    <span>{{ transLabel('menu.nutrition_allergies', 'Nutrition & Allergies') }}</span>
                </div>
            </a>

            <!-- SECTION: REPORTS -->
            <div class="school-nav-section-title sidebar-section-title">{{ transLabel('menu.reports_sec', 'REPORTS') }}</div>

            <!-- Participation -->
            <a href="{{ route('school.participation') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'participation' || request()->is('school/participation*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    <span>{{ transLabel('menu.participation', 'Participation') }}</span>
                </div>
            </a>

            <!-- Consumption -->
            <a href="{{ route('school.consumption') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'consumption' || request()->is('school/consumption*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    <span>{{ transLabel('menu.consumption', 'Consumption') }}</span>
                </div>
            </a>

            <!-- Reports -->
            <a href="{{ route('school.reports') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'reports' || request()->is('school/reports*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span>{{ transLabel('menu.reports_list', 'Reports') }}</span>
                </div>
            </a>

            <!-- SECTION: COMMUNICATION -->
            <div class="school-nav-section-title sidebar-section-title">{{ transLabel('menu.communication_sec', 'COMMUNICATION') }}</div>

            <!-- Announcements -->
            <a href="{{ route('school.announcements') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'announcements' || request()->is('school/announcements*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                    <span>{{ transLabel('menu.announcements', 'Announcements') }}</span>
                </div>
            </a>

            <!-- Support Tickets -->
            <a href="{{ route('school.support') }}" class="school-nav-item sidebar-item {{ ($currentSection ?? '') === 'support' || request()->is('school/support*') ? 'active' : '' }}">
                <div class="school-nav-left sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>{{ transLabel('menu.support_tickets', 'Support Tickets') }}</span>
                </div>
            </a>
        </div>

        <!-- 4. Bottom Action: Switch School Button (Image 1 Exact Layout) -->
        <div class="switch-school-bottom-box">
            <button type="button" class="btn-switch-school" onclick="openGlobalSwitchSchoolModal()">
                <div class="btn-switch-school-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m4 6 8-4 8 4"/><path d="M18 10 22 12v10H2V12l4-2"/><path d="M6 5v17"/><path d="M18 5v17"/></svg>
                    <span>{{ transLabel('menu.switch_school', 'Switch School') }}</span>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </aside>

    <!-- Main Workspace -->
    <div class="admin-main">
        <!-- Topbar -->
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

        <!-- School Admin Mobile Bottom Navigation Bar -->
        <nav class="mobile-bottom-bar" id="mobileBottomBar" aria-label="Mobile Navigation">
            <a href="{{ route('school.dashboard') }}" class="mobile-nav-tab {{ request()->routeIs('school.dashboard') ? 'active' : '' }}">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
            </a>

            <a href="{{ route('school.students') }}" class="mobile-nav-tab {{ request()->is('school/students*') ? 'active' : '' }}">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.students', 'Students') }}</span>
            </a>

            <a href="{{ route('school.roster') }}" class="mobile-nav-tab {{ request()->is('school/roster*') ? 'active' : '' }}">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.roster_short', 'Roster') }}</span>
            </a>

            <a href="{{ route('school.delivery') }}" class="mobile-nav-tab {{ request()->is('school/delivery*') ? 'active' : '' }}">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 17h4V5H2v12h3"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.delivery', 'Delivery') }}</span>
            </a>

            <button type="button" class="mobile-nav-tab" onclick="toggleSidebarCollapse()" id="mobileTabMenu">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.menu', 'Menu') }}</span>
            </button>
        </nav>
    </div>

    <!-- Global School Switcher Modal -->
    <div class="custom-modal-backdrop" id="globalSwitchSchoolModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 18px; width: 100%; max-width: 480px; padding: 1.5rem; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0;">{{ transLabel('school.switch_modal_title', 'Switch School Campus') }}</h3>
                <button type="button" onclick="closeGlobalSwitchSchoolModal()" style="background: none; border: none; font-size: 1.25rem; color: #94A3B8; cursor: pointer;">✕</button>
            </div>
            <form action="{{ route('school.set_school') }}" method="POST">
                @csrf
                <p style="font-size: 0.85rem; color: #64748B; margin-bottom: 1rem;">{{ transLabel('school.switch_modal_desc', 'Select the educational institution you wish to coordinate:') }}</p>
                <select name="school_id" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 10px; border: 1px solid #CBD5E1; font-size: 0.85rem; font-weight: 700; color: #0F172A; margin-bottom: 1.25rem;">
                    @foreach(\App\Models\School::orderBy('name')->get() as $sItem)
                        <option value="{{ $sItem->id }}" {{ ($activeSchool->id ?? '') === $sItem->id ? 'selected' : '' }}>
                            {{ $sItem->name }} ({{ $sItem->code }})
                        </option>
                    @endforeach
                </select>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" onclick="closeGlobalSwitchSchoolModal()" style="padding: 0.55rem 1rem; border-radius: 8px; border: 1px solid #E2E8F0; background: #fff; font-size: 0.85rem; font-weight: 600; cursor: pointer;">{{ transLabel('common.cancel', 'Cancel') }}</button>
                    <button type="submit" style="padding: 0.55rem 1.25rem; border-radius: 8px; border: none; background: #0066FF; color: #fff; font-size: 0.85rem; font-weight: 700; cursor: pointer;">{{ transLabel('school.switch_modal_btn', 'Switch Active Campus') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating Global Toast Notifications Container (Mandatory AGENTS.md rule) -->
    <x-toast-container />

    <!-- Interactive Scripts -->
    @include('layouts.partials.scripts')

    <script>
        function openGlobalSwitchSchoolModal() {
            const m = document.getElementById('globalSwitchSchoolModal');
            if (m) {
                m.style.display = 'flex';
            }
        }
        function closeGlobalSwitchSchoolModal() {
            const m = document.getElementById('globalSwitchSchoolModal');
            if (m) {
                m.style.display = 'none';
            }
        }
    </script>
</body>
</html>
