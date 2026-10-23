<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SchoolMeal') - SchoolMeal Portal</title>

    <!-- Google Fonts: Plus Jakarta Sans & Hanuman (Khmer) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanuman:wght@100;300;400;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        if (window.Chart) {
            const isKhmerDoc = document.documentElement.lang === 'km';
            Chart.defaults.font.family = isKhmerDoc
                ? "'Hanuman', 'Plus Jakarta Sans', system-ui, sans-serif"
                : "'Plus Jakarta Sans', 'Hanuman', system-ui, sans-serif";
            if (isKhmerDoc) {
                Chart.defaults.font.size = 12;
            }
        }
    </script>

    <!-- Leaflet.js for Live Delivery Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        :root {
            --font-sans: 'Plus Jakarta Sans', 'Hanuman', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-khmer: 'Hanuman', 'Plus Jakarta Sans', system-ui, serif, sans-serif;
            --brand-blue: #1D4ED8;
            --brand-blue-hover: #1E40AF;
            --brand-blue-light: #eff6ff;
            --brand-emerald: #10B981;
            --brand-orange: #EA580C;
            --brand-purple: #9333EA;
            --sidebar-bg: #0b1528;
            --sidebar-hover: rgba(255, 255, 255, 0.07);
            --sidebar-active: #1D4ED8;
            --body-bg: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --sidebar-width: 265px;
            --sidebar-collapsed-width: 70px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--body-bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* ================= KHMER FONT ("HANUMAN") & SCALE ENHANCEMENT ================= */
        html[lang="km"],
        :lang(km),
        .lang-km,
        .font-khmer,
        .khmer-font,
        [data-lang="km"] {
            font-family: var(--font-khmer) !important;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        html[lang="km"] {
            font-size: 16px;
        }

        html[lang="km"] body {
            font-family: var(--font-khmer);
            font-size: 0.95rem;
            line-height: 1.65;
            letter-spacing: normal;
        }

        /* Khmer font size increases for all layout components */
        html[lang="km"] .sidebar-brand-title {
            font-size: 1.25rem !important;
            font-weight: 900 !important;
            letter-spacing: 0 !important;
        }

        html[lang="km"] .sidebar-brand-subtitle {
            font-size: 0.785rem !important;
            font-weight: 700 !important;
            line-height: 1.4 !important;
            letter-spacing: 0 !important;
        }

        html[lang="km"] .sidebar-section-title {
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.02em !important;
            text-transform: none !important;
            color: #64748b;
        }

        html[lang="km"] .sidebar-item {
            font-size: 1.1rem !important; /* 0.94rem standard */
            font-weight: 500 !important;
            line-height: 1.55 !important;
            padding: 0.62rem 0.8rem !important;
        }

        html[lang="km"] .sidebar-item-left {
            gap: 0.75rem;
        }

        html[lang="km"] .submenu-item {
            font-size: 0.885rem !important;
            font-weight: 600 !important;
            line-height: 1.5 !important;
            padding: 0.5rem 0.75rem 0.5rem 2.6rem !important;
        }

        html[lang="km"] .sidebar-badge {
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            padding: 0.2rem 0.55rem !important;
        }

        /* Topbar Controls */
        html[lang="km"] .search-container input {
            font-size: 0.935rem !important;
            font-family: var(--font-khmer) !important;
            line-height: 1.5 !important;
        }

        html[lang="km"] .search-container input::placeholder {
            font-size: 0.875rem !important;
            font-family: var(--font-khmer) !important;
        }

        html[lang="km"] .topbar-icon-btn {
            font-size: 0.925rem !important;
            font-weight: 700 !important;
        }

        html[lang="km"] .user-profile-btn {
            font-family: var(--font-khmer);
        }

        html[lang="km"] .user-profile-meta div:first-child {
            font-size: 0.935rem !important;
            font-weight: 700 !important;
            line-height: 1.3 !important;
        }

        html[lang="km"] .user-profile-meta div:last-child {
            font-size: 0.785rem !important;
            font-weight: 600 !important;
        }

        html[lang="km"] .profile-menu-link {
            font-size: 0.925rem !important;
            font-weight: 600 !important;
            line-height: 1.55 !important;
            padding: 0.65rem 0.85rem !important;
        }

        html[lang="km"] .profile-dropdown-header div div:first-child {
            font-size: 0.95rem !important;
            font-weight: 800 !important;
        }

        html[lang="km"] .profile-dropdown-header div div:nth-child(2) {
            font-size: 0.8rem !important;
        }

        /* Notification Dropdown */
        html[lang="km"] .notif-header-title {
            font-size: 0.95rem !important;
            font-weight: 800 !important;
        }

        html[lang="km"] .notif-item .notif-title {
            font-size: 0.925rem !important;
            font-weight: 700 !important;
            line-height: 1.4 !important;
        }

        html[lang="km"] .notif-item .notif-desc {
            font-size: 0.85rem !important;
            line-height: 1.5 !important;
        }

        html[lang="km"] .notif-item .notif-time {
            font-size: 0.75rem !important;
        }

        /* Mobile Bottom Bar Navigation */
        html[lang="km"] .mobile-tab-label {
            font-size: 0.76rem !important;
            font-weight: 700 !important;
            line-height: 1.3 !important;
            letter-spacing: 0 !important;
            font-family: var(--font-khmer) !important;
        }

        html[lang="km"] .mobile-tab-badge {
            font-size: 0.7rem !important;
            font-weight: 800 !important;
        }

        /* Buttons, Inputs, Selects, Forms */
        html[lang="km"] .btn,
        html[lang="km"] button,
        html[lang="km"] input,
        html[lang="km"] select,
        html[lang="km"] textarea,
        html[lang="km"] .dash-action-btn,
        html[lang="km"] .form-control {
            font-family: var(--font-khmer) !important;
        }

        html[lang="km"] .btn,
        html[lang="km"] .dash-action-btn {
            font-size: 0.935rem !important;
            font-weight: 700 !important;
            line-height: 1.5 !important;
        }

        html[lang="km"] input,
        html[lang="km"] select,
        html[lang="km"] textarea,
        html[lang="km"] .form-control {
            font-size: 0.935rem !important;
            line-height: 1.55 !important;
        }

        /* Dashboard & Content Components */
        html[lang="km"] .dashboard-main-title {
            font-size: 1.95rem !important;
            font-weight: 900 !important;
            line-height: 1.35 !important;
        }

        html[lang="km"] .dashboard-main-subtitle {
            font-size: 0.965rem !important;
            line-height: 1.6 !important;
        }

        html[lang="km"] .dash-card-title,
        html[lang="km"] .card-title {
            font-size: 1.2rem !important;
            font-weight: 700 !important;
            line-height: 1.45 !important;
        }

        html[lang="km"] .dash-filter-select {
            font-size: 0.875rem !important;
            font-weight: 700 !important;
            padding: 0.38rem 0.75rem !important;
        }

        html[lang="km"] .metric-label {
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            line-height: 1.45 !important;
        }

        html[lang="km"] .metric-sub {
            font-size: 0.825rem !important;
            line-height: 1.45 !important;
        }

        html[lang="km"] .insights-badge {
            font-size: 0.9rem !important;
            font-weight: 700 !important;
            line-height: 1.45 !important;
        }

        html[lang="km"] .insights-pill-info {
            font-size: 0.785rem !important;
            font-weight: 600 !important;
        }

        html[lang="km"] .dash-table th,
        html[lang="km"] .table th {
            font-size: 0.875rem !important;
            font-weight: 700 !important;
            line-height: 1.5 !important;
        }

        html[lang="km"] .dash-table td,
        html[lang="km"] .table td {
            font-size: 0.875rem !important;
            line-height: 1.55 !important;
        }

        html[lang="km"] .status-badge {
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            padding: 0.3rem 0.65rem !important;
            line-height: 1.35 !important;
        }

        /* Floating Toasts */
        html[lang="km"] .toast-item {
            font-family: var(--font-khmer) !important;
        }

        html[lang="km"] .toast-title {
            font-family: var(--font-khmer) !important;
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            line-height: 1.4 !important;
        }

        html[lang="km"] .toast-message {
            font-family: var(--font-khmer) !important;
            font-size: 0.925rem !important;
            line-height: 1.55 !important;
        }

        /* Universal class helpers for Khmer */
        .font-khmer,
        .khmer-font,
        .km-text {
            font-family: var(--font-khmer) !important;
            letter-spacing: 0 !important;
        }

        /* ================= SIDEBAR STYLING ================= */
        aside.admin-sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: #94a3b8;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            transition: width 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: sticky;
            top: 0;
            height: 100vh;
            z-index: 100;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Custom scrollbar for sidebar */
        aside.admin-sidebar::-webkit-scrollbar {
            width: 4px;
        }
        aside.admin-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }

        /* Brand / Header */
        .sidebar-brand-box {
            padding: 1.15rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            min-height: 64px;
            position: relative;
        }

        .sidebar-brand-title {
            font-size: 1.125rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }

        .sidebar-brand-subtitle {
            font-size: 0.675rem;
            color: #38bdf8;
            font-weight: 600;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }

        /* Sidebar Navigation Menu */
        .sidebar-menu {
            padding: 1rem 0.65rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .sidebar-section-title {
            font-size: 0.6875rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0.85rem 0.65rem 0.35rem 0.65rem;
            user-select: none;
            white-space: nowrap;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 1.1rem !important;
            font-weight: 500;
            transition: all 0.15s ease;
            cursor: pointer;
            user-select: none;
            position: relative;
        }

        .sidebar-item:hover {
            color: #ffffff;
            background: var(--sidebar-hover);
        }

        .sidebar-item.active {
            background: var(--sidebar-active);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(29, 78, 216, 0.35);
        }

        .sidebar-item-left {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            white-space: nowrap;
        }

        .sidebar-chevron {
            color: #64748b;
            transition: transform 0.25s ease;
            flex-shrink: 0;
        }

        .sidebar-item:hover .sidebar-chevron {
            color: #cbd5e1;
        }

        /* Dropdown Accordion Submenu */
        .sidebar-dropdown-group.open .sidebar-chevron {
            transform: rotate(180deg);
            color: #38bdf8;
        }

        .sidebar-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0, 1, 0, 1);
            background: rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            margin: 0.1rem 0 0.25rem 0;
        }

        .sidebar-dropdown-group.open .sidebar-submenu {
            max-height: 240px;
            transition: max-height 0.35s ease-in-out;
            padding: 0.25rem 0;
        }

        .submenu-item {
            display: flex;
            align-items: center;
            padding: 0.45rem 0.65rem 0.45rem 2.45rem;
            font-size: 0.775rem;
            font-weight: 500;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 6px;
            margin: 0.1rem 0.4rem;
            transition: all 0.15s ease;
        }

        .submenu-item:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
            padding-left: 2.65rem;
        }

        .submenu-item.active {
            color: #38bdf8;
            font-weight: 700;
        }

        /* Bottom Sidebar User Box */
        .sidebar-user-box {
            padding: 0.85rem 1rem;
            background: rgba(0, 0, 0, 0.25);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 64px;
        }

        .sidebar-user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid #2563EB;
            flex-shrink: 0;
        }

        /* ================= COLLAPSED SIDEBAR MODE ================= */
        aside.admin-sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        aside.admin-sidebar.collapsed .sidebar-brand-title,
        aside.admin-sidebar.collapsed .sidebar-brand-subtitle,
        aside.admin-sidebar.collapsed .sidebar-section-title,
        aside.admin-sidebar.collapsed .sidebar-item-left span,
        aside.admin-sidebar.collapsed .sidebar-chevron,
        aside.admin-sidebar.collapsed .sidebar-submenu,
        aside.admin-sidebar.collapsed .sidebar-user-info {
            display: none !important;
        }

        aside.admin-sidebar.collapsed .sidebar-item {
            justify-content: center;
            padding: 0.65rem 0;
        }

        aside.admin-sidebar.collapsed .sidebar-item-left {
            gap: 0;
        }

        aside.admin-sidebar.collapsed .sidebar-brand-box {
            justify-content: center;
            padding: 1.15rem 0;
        }

        aside.admin-sidebar.collapsed .sidebar-user-box {
            justify-content: center;
            padding: 0.85rem 0;
        }

        /* ================= TOPBAR NAVIGATION ================= */
        .admin-topbar {
            height: 64px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.75rem;
            position: sticky;
            top: 0;
            z-index: 80;
            transition: all 0.2s ease;
        }

        .search-container {
            display: flex;
            align-items: center;
            background: #f1f5f9;
            border-radius: 9px;
            padding: 0.45rem 0.85rem;
            width: 310px;
            gap: 0.5rem;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .search-container:focus-within {
            background: #ffffff;
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-container input {
            border: none;
            background: transparent;
            font-size: 0.8125rem;
            font-family: inherit;
            color: #0f172a;
            outline: none;
            width: 100%;
        }

        .kbd-badge {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 0.1rem 0.35rem;
            font-size: 0.65rem;
            font-weight: 700;
            color: #64748b;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .topbar-icon-btn {
            position: relative;
            background: transparent;
            border: none;
            color: #475569;
            cursor: pointer;
            padding: 0.45rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s, color 0.15s;
        }

        .topbar-icon-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .lang-toggle-btn {
            width: auto !important;
            height: 38px !important;
            border-radius: 999px !important;
            padding: 0.35rem 0.75rem !important;
            font-size: 0.8125rem;
            font-weight: 600;
            gap: 0.45rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .lang-flag-current {
            width: 22px;
            height: 15px;
            border-radius: 2.5px;
            object-fit: cover;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(0, 0, 0, 0.12);
            flex-shrink: 0;
            display: inline-block;
        }

        .badge-dot {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 16px;
            height: 16px;
            background: #ef4444;
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 800;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
        }

        /* Profile Trigger Button */
        .user-profile-btn {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            padding: 0.35rem 0.65rem 0.35rem 0.45rem;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .user-profile-btn:hover, .user-profile-btn.active {
            background: #f8fafc;
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }

        .topbar-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid #2563EB;
        }

        /* ================= DROPDOWN MENUS (FLOATING) ================= */
        .dropdown-panel {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 15px 35px -5px rgba(15, 23, 42, 0.15), 0 5px 15px -3px rgba(15, 23, 42, 0.08);
            z-index: 150;
            overflow: hidden;
            animation: dropdownFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dropdown-panel.show {
            display: block;
        }

        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Profile Dropdown Specific */
        .profile-dropdown {
            width: 270px;
        }

        .profile-dropdown-header {
            padding: 1rem 1.15rem;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            border-bottom: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .profile-dropdown-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #2563EB;
        }

        .profile-dropdown-body {
            padding: 0.45rem;
        }

        .profile-menu-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.75rem;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .profile-menu-link:hover {
            background: #f1f5f9;
            color: #1D4ED8;
        }

        .profile-menu-link.logout-link {
            color: #dc2626;
        }

        .profile-menu-link.logout-link:hover {
            background: #fef2f2;
            color: #b91c1c;
        }

        .dropdown-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 0.35rem 0.5rem;
        }

        /* ================= IMPROVED NOTIFICATION DROPDOWN ================= */
        .notification-dropdown {
            width: 380px;
            max-width: 95vw;
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.2), 0 0 0 1px rgba(226, 232, 240, 0.85);
            overflow: hidden;
            z-index: 1050;
        }

        .notif-header {
            padding: 0.9rem 1.15rem;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            border-bottom: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .notif-header-title {
            font-size: 0.875rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .notif-unread-badge {
            font-size: 0.6875rem;
            font-weight: 800;
            color: #1D4ED8;
            background: #eff6ff;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            border: 1px solid #bfdbfe;
            letter-spacing: 0.02em;
        }

        .notif-list-container {
            max-height: 360px;
            overflow-y: auto;
            padding: 0.45rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .notif-list-container::-webkit-scrollbar {
            width: 5px;
        }
        .notif-list-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .notif-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.65rem 0.75rem;
            border-radius: 10px;
            text-decoration: none;
            transition: background 0.15s ease, transform 0.1s ease;
            position: relative;
            cursor: pointer;
        }

        .notif-item:hover {
            background: #f8fafc;
        }

        .notif-item.unread {
            background: rgba(239, 246, 255, 0.55);
        }
        .notif-item.unread:hover {
            background: #eff6ff;
        }

        .notif-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notif-content {
            flex: 1;
            min-width: 0;
        }

        .notif-title {
            font-size: 0.8125rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.35;
            white-space: normal;
            word-break: break-word;
        }

        .notif-desc {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 2px;
            line-height: 1.4;
            white-space: normal;
            word-break: break-word;
        }

        .notif-time {
            font-size: 0.6875rem;
            color: #94a3b8;
            margin-top: 3px;
            font-weight: 500;
        }

        .notif-unread-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #2563EB;
            flex-shrink: 0;
            margin-top: 6px;
        }

        .notif-footer {
            padding: 0.65rem 1rem;
            border-top: 1px solid #eef2f6;
            background: #f8fafc;
            text-align: center;
        }

        .notif-footer a {
            font-size: 0.75rem;
            font-weight: 700;
            color: #1D4ED8;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .notif-footer a:hover {
            text-decoration: underline;
        }

        /* MOBILE NOTIFICATION DROPDOWN POSITIONING - NO CLIPPING */
        @media (max-width: 768px) {
            .notification-dropdown {
                position: fixed !important;
                top: 64px !important;
                left: 12px !important;
                right: 12px !important;
                width: auto !important;
                max-width: calc(100vw - 24px) !important;
                margin: 0 auto !important;
                border-radius: 16px !important;
                box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.28), 0 0 0 1px rgba(226, 232, 240, 0.95) !important;
                max-height: calc(85vh - 72px) !important;
                z-index: 1050 !important;
            }

            .dropdown-panel {
                max-width: calc(100vw - 24px) !important;
            }
        }

        /* Main Workspace Wrapper */
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background-color: var(--body-bg);
            transition: margin-left 0.25s ease;
        }

        .admin-content {
            padding: 1.75rem;
            flex: 1;
        }

        /* ================= MOBILE BOTTOM NAVIGATION BAR ================= */
        .mobile-bottom-bar {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 -4px 20px rgba(15, 23, 42, 0.06);
            z-index: 90;
            align-items: center;
            justify-content: space-around;
            padding: 0 0.5rem;
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }

        .mobile-nav-tab {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            height: 100%;
            color: #64748b;
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 4px 2px;
            position: relative;
            user-select: none;
            transition: color 0.15s ease, transform 0.1s ease;
        }

        .mobile-nav-tab:active {
            transform: scale(0.92);
        }

        .mobile-tab-icon-box {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            transition: background 0.15s, color 0.15s;
        }

        .mobile-nav-tab svg {
            width: 20px;
            height: 20px;
            transition: stroke 0.15s;
        }

        .mobile-tab-label {
            font-size: 0.65rem;
            font-weight: 700;
            line-height: 1.15;
            margin-top: 2px;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }

        .mobile-nav-tab.active {
            color: #1D4ED8;
        }

        .mobile-nav-tab.active .mobile-tab-icon-box {
            color: #1D4ED8;
            background: rgba(29, 78, 216, 0.08);
        }

        .mobile-nav-tab.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 3px;
            background: #1D4ED8;
            border-radius: 0 0 3px 3px;
        }

        .mobile-tab-badge {
            position: absolute;
            top: -2px;
            right: -4px;
            min-width: 15px;
            height: 15px;
            padding: 0 3px;
            border-radius: 999px;
            background: #ef4444;
            color: #ffffff;
            font-size: 0.58rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1.5px solid #ffffff;
        }

        /* Mobile Drawer & Overlay Backdrop */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(3px);
            z-index: 95;
            transition: opacity 0.25s ease;
        }

        .sidebar-backdrop.active {
            display: block;
        }

        .sidebar-mobile-close {
            display: none;
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.35rem;
            border-radius: 6px;
            margin-left: auto;
            transition: color 0.15s, background 0.15s;
        }

        .sidebar-mobile-close:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 1024px) {
            aside.admin-sidebar {
                position: fixed;
                left: -260px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            }
            aside.admin-sidebar.open {
                left: 0;
            }
            .sidebar-mobile-close {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .search-container {
                width: 220px;
            }
            .user-profile-meta {
                display: none;
            }
        }

        .mobile-only-spacer {
            display: none !important;
        }

        /* Meta (Facebook) Insights Shared Rail Styling */
        .portal-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .insights-track-container {
            position: relative;
        }

        .insights-fade-edge {
            position: absolute;
            top: 0;
            bottom: 12px;
            width: 32px;
            z-index: 10;
            pointer-events: none;
            transition: opacity 0.2s ease;
            opacity: 0;
        }
        .insights-fade-edge.fade-left {
            left: 0;
            background: linear-gradient(90deg, #f8fafc 0%, rgba(248, 250, 252, 0) 100%);
        }
        .insights-fade-edge.fade-right {
            right: 0;
            background: linear-gradient(270deg, #f8fafc 0%, rgba(248, 250, 252, 0) 100%);
        }

        @media (max-width: 768px) {
            .admin-topbar {
                height: 56px;
                padding: 0 0.85rem;
            }
            .admin-content {
                padding: 1rem 0.85rem calc(60px + env(safe-area-inset-bottom, 0px) + 1rem) 0.85rem !important;
            }
            .mobile-bottom-bar {
                display: flex;
                min-height: 60px;
                height: calc(60px + env(safe-area-inset-bottom, 0px));
                padding-bottom: env(safe-area-inset-bottom, 0px);
                box-sizing: border-box;
            }
            .kitchen-dashboard-container,
            .dashboard-container,
            .admin-container {
                padding-bottom: 0 !important;
                margin-bottom: 0.5rem !important;
            }
            .mobile-only-spacer {
                display: block !important;
                height: 0px !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100%;
                clear: both;
            }

            /* Meta Insights Horizontal Scrollable Rails on Mobile */
            .portal-kpi-grid {
                display: flex !important;
                gap: 0.85rem !important;
                overflow-x: auto !important;
                scroll-snap-type: x mandatory !important;
                scroll-behavior: smooth !important;
                -webkit-overflow-scrolling: touch !important;
                scrollbar-width: none !important;
                padding: 4px 2px 14px 2px !important;
                margin-bottom: 1.25rem !important;
            }
            .portal-kpi-grid::-webkit-scrollbar {
                display: none !important;
            }
            .portal-kpi-grid > div {
                flex: 0 0 76vw !important;
                min-width: 230px !important;
                max-width: 280px !important;
                scroll-snap-align: start !important;
                margin: 0 !important;
            }
            .search-container {
                display: none;
            }
            .kbd-badge {
                display: none !important;
            }
            .topbar-actions {
                gap: 0.35rem;
            }
            .topbar-icon-btn {
                padding: 0.35rem 0.45rem;
            }
            .help-center-btn {
                display: none;
            }
            .lang-toggle-btn {
                width: 38px !important;
                height: 38px !important;
                border-radius: 50% !important;
                padding: 0 !important;
                gap: 0 !important;
                justify-content: center !important;
            }
            .lang-text-desktop,
            .lang-text-mobile,
            .lang-chevron {
                display: none !important;
            }
        }

        @media (min-width: 1025px) {
            .lang-toggle-btn {
                width: auto !important;
                height: 38px !important;
                border-radius: 999px !important;
                padding: 0.35rem 0.75rem !important;
                gap: 0.45rem !important;
            }
            .lang-text-desktop {
                display: inline !important;
            }
            .lang-chevron {
                display: inline-block !important;
            }
            .lang-text-mobile {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Overlay backdrop for mobile drawer dismissal -->
    <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="closeSidebarMobile()"></div>

    <!-- ================= LEFT MASTER SIDEBAR ================= -->
    <aside class="admin-sidebar" id="adminSidebar">

        <!-- Brand Box -->
        <div class="sidebar-brand-box">
            <svg width="34" height="34" viewBox="0 0 64 64" fill="none" style="flex-shrink: 0;">
                <path d="M8 44C18 40.5 27 43 31 46.5V20.5C27 17 18 14.5 8 18V44Z" fill="#10B981"/>
                <path d="M56 44C46 40.5 37 43 33 46.5V20.5C37 17 46 14.5 56 18V44Z" fill="#F97316"/>
                <path d="M31 46.5C31.5 46.8 32.5 46.8 33 46.5V20.5C32.5 20.2 31.5 20.2 31 20.5V46.5Z" fill="#0284C7"/>
                <path d="M32 24V11" stroke="#22C55E" stroke-width="3" stroke-linecap="round"/>
                <path d="M32 17C26 17 25 12 28 10C31 10 32 14 32 17Z" fill="#22C55E"/>
                <path d="M32 15C38 15 39 9 36 8C33 8 32 12 32 15Z" fill="#16A34A"/>
            </svg>
            <div>
                <div class="sidebar-brand-title">@yield('portal_title', 'SchoolMeal')</div>
                <div class="sidebar-brand-subtitle">@yield('portal_subtitle', 'E-Commerce Platform')</div>
            </div>
            <button type="button" class="sidebar-mobile-close" onclick="closeSidebarMobile()" aria-label="Close Sidebar" title="Close Sidebar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="sidebar-menu">
            @yield('sidebar_menu')
        </nav>

        <!-- Bottom Sidebar User Box -->
        <div class="sidebar-user-box">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <img src="{{ auth()->user()->profile_image_url ?? asset('images/avatar_sourng.jpg') }}" alt="User Avatar" class="sidebar-user-avatar" onerror="this.src='https://ui-avatars.com/api/?name=User&background=1D4ED8&color=fff'">
                <div class="sidebar-user-info" style="line-height: 1.2;">
                    <div style="color: #ffffff; font-weight: 700; font-size: 0.8125rem;">{{ auth()->user()->full_name ?? 'SENG Sourng' }}</div>
                    <div style="color: #64748b; font-size: 0.6875rem;">{{ strtoupper(str_replace('_', ' ', auth()->user()->role ?? 'Super Admin')) }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #64748b; cursor: pointer; padding: 4px; display: flex;" title="{{ transLabel('menu.sign_out', 'Sign Out') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>

    </aside>

    <!-- ================= MAIN WORKSPACE AREA ================= -->
    <div class="admin-main">

        <!-- Top Header Navigation -->
        <header class="admin-topbar">
            <!-- Left: Toggle Sidebar & Global Search -->
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button type="button" class="topbar-icon-btn" onclick="toggleSidebarCollapse()" aria-label="Toggle Sidebar Menu" title="Collapse / Expand Sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>

                <div class="search-container">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="globalSearchInput" placeholder="{{ transLabel('common.search_placeholder', 'Search schools, restaurants, orders, users...') }}">
                    <span class="kbd-badge">Ctrl + K</span>
                </div>
            </div>

            <!-- Right: Alerts, Language & User Profile Dropdown -->
            <div class="topbar-actions">

                <!-- 1. Notifications with Dropdown -->
                <div style="position: relative;">
                    <button type="button" class="topbar-icon-btn" id="notifBtn" onclick="toggleNotifDropdown(event)" title="{{ transLabel('menu.notifications', 'Notifications') }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        <span class="badge-dot">5</span>
                    </button>

                    <div id="notifDropdown" class="dropdown-panel notification-dropdown">
                        <div class="notif-header">
                            <div class="notif-header-title">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                                <span>{{ transLabel('menu.notifications', 'Notifications') }}</span>
                            </div>
                            <span class="notif-unread-badge">5 {{ transLabel('common.new', 'New') }}</span>
                        </div>

                        <div class="notif-list-container">
                            <!-- Item 1: Restaurant Registered -->
                            <div class="notif-item unread">
                                <div class="notif-icon-box" style="background: #eff6ff; color: #1D4ED8;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                </div>
                                <div class="notif-content">
                                    <div class="notif-title">New Restaurant Registered</div>
                                    <div class="notif-desc">Healthy Food Co. submitted documents for review.</div>
                                    <div class="notif-time">2 mins ago</div>
                                </div>
                                <div class="notif-unread-dot"></div>
                            </div>

                            <!-- Item 2: School Approved -->
                            <div class="notif-item unread">
                                <div class="notif-icon-box" style="background: #ecfdf5; color: #059669;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m4 6 8-4 8 4"/><path d="M18 10 22 12v10H2V12l4-2"/><path d="M6 5v17"/><path d="M18 5v17"/></svg>
                                </div>
                                <div class="notif-content">
                                    <div class="notif-title">New School Approved</div>
                                    <div class="notif-desc">Wat Bo Primary School onboarding setup complete.</div>
                                    <div class="notif-time">12 mins ago</div>
                                </div>
                                <div class="notif-unread-dot"></div>
                            </div>

                            <!-- Item 3: Payment Received -->
                            <div class="notif-item unread">
                                <div class="notif-icon-box" style="background: #faf5ff; color: #9333ea;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                </div>
                                <div class="notif-content">
                                    <div class="notif-title">Payment Received (KHQR)</div>
                                    <div class="notif-desc">ORD-00121 - $3.50 settled via Bakong KHQR.</div>
                                    <div class="notif-time">18 mins ago</div>
                                </div>
                                <div class="notif-unread-dot"></div>
                            </div>

                            <!-- Item 4: Delivery Dispatched -->
                            <div class="notif-item unread">
                                <div class="notif-icon-box" style="background: #fffbeb; color: #d97706;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                                </div>
                                <div class="notif-content">
                                    <div class="notif-title">Delivery Batch Dispatched</div>
                                    <div class="notif-desc">Route #4 is out for delivery to Angkor Primary.</div>
                                    <div class="notif-time">25 mins ago</div>
                                </div>
                                <div class="notif-unread-dot"></div>
                            </div>

                            <!-- Item 5: Nutrition Audit -->
                            <div class="notif-item">
                                <div class="notif-icon-box" style="background: #f1f5f9; color: #475569;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                                </div>
                                <div class="notif-content">
                                    <div class="notif-title">Nutrition Safety Audit</div>
                                    <div class="notif-desc">Daily calorie and allergen checks passed 100%.</div>
                                    <div class="notif-time">42 mins ago</div>
                                </div>
                            </div>
                        </div>

                        <div class="notif-footer">
                            <a href="javascript:void(0)">
                                <span>{{ transLabel('menu.view_all_notifications', 'View all notifications') }}</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 2. Help Center -->
                <button type="button" class="topbar-icon-btn help-center-btn" title="{{ transLabel('common.help_center', 'Help Center') }}" onclick="alert('SchoolMeal Knowledge Base & Support Help Center')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </button>

                <!-- 3. Language Selector Dropdown -->
                <div style="position: relative;">
                    @php
                        $activeLocale = app()->getLocale();
                    @endphp
                    <button type="button" class="topbar-icon-btn lang-toggle-btn" id="langToggleBtn" onclick="toggleLangDropdown(event)" title="{{ $activeLocale === 'km' ? 'ភាសាខ្មែរ' : 'English' }}" aria-label="Select Language">
                        <img src="{{ $activeLocale === 'km' ? asset('images/flags/km.svg') : asset('images/flags/gb.svg') }}" 
                             alt="{{ $activeLocale === 'km' ? 'Cambodia Flag' : 'UK Flag' }}" 
                             class="lang-flag-current" 
                             width="22" 
                             height="15">
                        <span id="currentLangText" class="lang-text-desktop">{{ $activeLocale === 'km' ? 'ភាសាខ្មែរ' : 'English' }}</span>
                        <svg class="lang-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>

                    <div id="langDropdownMenu" class="dropdown-panel" style="width: 185px; padding: 0.35rem;">
                        <form method="POST" action="{{ route('locale.switch') }}" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="locale" value="en">
                            <button type="submit" style="width: 100%; border: none; background: transparent; padding: 0.55rem 0.75rem; font-size: 0.8125rem; font-weight: 600; color: {{ $activeLocale === 'en' ? '#1D4ED8' : '#1e293b' }}; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; text-align: left; font-family: inherit;">
                                <span style="display: flex; align-items: center; gap: 0.6rem;">
                                    <img src="{{ asset('images/flags/gb.svg') }}" alt="UK Flag" style="width: 20px; height: 14px; border-radius: 2px; object-fit: cover; box-shadow: 0 1px 2px rgba(0,0,0,0.15); border: 1px solid rgba(0,0,0,0.08);">
                                    <span>English</span>
                                </span>
                                @if($activeLocale === 'en') <span style="color: #2563EB; font-weight: 800;">✓</span> @endif
                            </button>
                        </form>
                        <form method="POST" action="{{ route('locale.switch') }}" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="locale" value="km">
                            <button type="submit" style="width: 100%; border: none; background: transparent; padding: 0.55rem 0.75rem; font-size: 0.8125rem; font-weight: 600; color: {{ $activeLocale === 'km' ? '#1D4ED8' : '#1e293b' }}; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; text-align: left; font-family: inherit;">
                                <span style="display: flex; align-items: center; gap: 0.6rem;">
                                    <img src="{{ asset('images/flags/km.svg') }}" alt="Cambodia Flag" style="width: 20px; height: 14px; border-radius: 2px; object-fit: cover; box-shadow: 0 1px 2px rgba(0,0,0,0.15); border: 1px solid rgba(0,0,0,0.08);">
                                    <span style="font-family: 'Hanuman', serif; font-size: 0.875rem;">ភាសាខ្មែរ</span>
                                </span>
                                @if($activeLocale === 'km') <span style="color: #2563EB; font-weight: 800;">✓</span> @endif
                            </button>
                        </form>
                        @if(Route::has('admin.languages.index'))
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('admin.languages.index') }}" style="display: flex; align-items: center; gap: 0.45rem; padding: 0.45rem 0.75rem; font-size: 0.75rem; font-weight: 700; color: #1D4ED8; text-decoration: none; border-radius: 6px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                <span>{{ transLabel('menu.manage_languages', 'Manage Languages') }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 4. User Profile Dropdown Button -->
                <div style="position: relative;">
                    <button type="button" class="user-profile-btn" id="userProfileBtn" onclick="toggleProfileDropdown(event)">
                        <img src="{{ auth()->user()->profile_image_url ?? asset('images/avatar_sourng.jpg') }}" alt="User Avatar" class="topbar-avatar" onerror="this.src='https://ui-avatars.com/api/?name=User&background=1D4ED8&color=fff'">
                        <div class="user-profile-meta" style="text-align: left; line-height: 1.2;">
                            <div style="font-size: 0.8125rem; font-weight: 700; color: #0f172a;">{{ auth()->user()->full_name ?? 'SENG Sourng' }}</div>
                            <div style="font-size: 0.6875rem; color: #64748b;">{{ strtoupper(str_replace('_', ' ', auth()->user()->role ?? 'Super Admin')) }}</div>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>

                    <!-- Floating Profile Menu -->
                    <div id="userProfileDropdown" class="dropdown-panel profile-dropdown">
                        <div class="profile-dropdown-header">
                            <img src="{{ auth()->user()->profile_image_url ?? asset('images/avatar_sourng.jpg') }}" alt="User" class="profile-dropdown-avatar" onerror="this.src='https://ui-avatars.com/api/?name=User&background=1D4ED8&color=fff'">
                            <div>
                                <div style="font-weight: 800; font-size: 0.875rem; color: #0f172a;">{{ auth()->user()->full_name ?? 'SENG Sourng' }}</div>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 1px;">{{ auth()->user()->email ?? 'superadmin@schoolmeal.com' }}</div>
                                <div style="display: inline-block; margin-top: 4px; padding: 0.15rem 0.5rem; background: #eff6ff; color: #1D4ED8; font-size: 0.675rem; font-weight: 700; border-radius: 999px;">
                                    {{ strtoupper(str_replace('_', ' ', auth()->user()->role ?? 'Super Admin')) }}
                                </div>
                            </div>
                        </div>

                        <div class="profile-dropdown-body">
                            <a href="javascript:void(0)" class="profile-menu-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <span>{{ transLabel('menu.my_profile', 'My Profile') }}</span>
                            </a>
                            <a href="javascript:void(0)" class="profile-menu-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                <span>{{ transLabel('menu.account_settings', 'Account Settings') }}</span>
                            </a>
                            <a href="javascript:void(0)" class="profile-menu-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                <span>{{ transLabel('menu.security_logs', 'Security & Sessions') }}</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                @csrf
                                <button type="submit" class="profile-menu-link logout-link" style="width: 100%; border: none; background: transparent; cursor: pointer; font-family: inherit; text-align: left;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                    <span>{{ transLabel('menu.sign_out', 'Sign Out') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </header>

        <!-- Main Content Area -->
        <main class="admin-content">
            @hasSection('header_title')
                <div style="margin-bottom: 1.25rem;">
                    <h1 style="font-size: 1.45rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em;">@yield('header_title')</h1>
                </div>
            @endif
            @yield('content')
        </main>

        <!-- Mobile Floating / Sticky Bottom Navigation Bar -->
        @section('mobile_bottom_bar')
            <nav class="mobile-bottom-bar" id="mobileBottomBar" aria-label="Mobile Navigation">
                <a href="{{ route('admin.dashboard') }}" class="mobile-nav-tab {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" id="mobileTabDashboard">
                    <div class="mobile-tab-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <span class="mobile-tab-label">{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
                </a>

                <a href="#graphsTrack" class="mobile-nav-tab" onclick="scrollSection('graphsTrack', event)" id="mobileTabAnalytics">
                    <div class="mobile-tab-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <span class="mobile-tab-label">{{ transLabel('dashboard.analytics', 'Analytics') }}</span>
                </a>

                <a href="#listsTrack" class="mobile-nav-tab" onclick="scrollSection('listsTrack', event)" id="mobileTabOrders">
                    <div class="mobile-tab-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    </div>
                    <span class="mobile-tab-label">{{ transLabel('dashboard.orders', 'Orders') }}</span>
                </a>

                <button type="button" class="mobile-nav-tab" onclick="toggleNotifDropdown(event)" id="mobileTabNotifs" aria-label="Notifications">
                    <div class="mobile-tab-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        <span class="mobile-tab-badge">5</span>
                    </div>
                    <span class="mobile-tab-label">{{ transLabel('menu.alerts', 'Alerts') }}</span>
                </button>

                <button type="button" class="mobile-nav-tab" onclick="toggleSidebarCollapse()" id="mobileTabMenu" aria-label="Open Full Menu">
                    <div class="mobile-tab-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                    </div>
                    <span class="mobile-tab-label">{{ transLabel('menu.menu', 'Menu') }}</span>
                </button>
            </nav>
        @show

    </div>

    <!-- Floating Global Toast Notifications Container -->
    <x-toast-container />

    <!-- ================= INTERACTIVE SCRIPTS ================= -->
    <script>
        // 1. Toggle Sidebar Collapse (Full <-> Mini mode on desktop, or mobile drawer)
        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (window.innerWidth <= 1024) {
                const isOpen = sidebar.classList.toggle('open');
                if (backdrop) {
                    if (isOpen) {
                        backdrop.classList.add('active');
                    } else {
                        backdrop.classList.remove('active');
                    }
                }
            } else {
                sidebar.classList.toggle('collapsed');
            }
        }

        function closeSidebarMobile() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.remove('open');
            if (backdrop) backdrop.classList.remove('active');
        }

        // 2. Toggle Sidebar Accordion Submenus
        function toggleSidebarDropdown(groupId) {
            const group = document.getElementById(groupId);
            if (!group) return;

            const sidebar = document.getElementById('adminSidebar');
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.remove('collapsed');
            }

            const wasOpen = group.classList.contains('open');
            document.querySelectorAll('.sidebar-dropdown-group').forEach(el => {
                if (el !== group) el.classList.remove('open');
            });

            if (!wasOpen) {
                group.classList.add('open');
            } else {
                group.classList.remove('open');
            }
        }

        // 3. User Profile Dropdown Toggle
        function toggleProfileDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('userProfileDropdown');
            const isOpen = menu.classList.contains('show');
            closeAllDropdowns();
            if (!isOpen) {
                menu.classList.add('show');
                document.getElementById('userProfileBtn').classList.add('active');
            }
        }

        // 4. Notifications Dropdown Toggle
        function toggleNotifDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('notifDropdown');
            const isOpen = menu.classList.contains('show');
            closeAllDropdowns();
            if (!isOpen) {
                menu.classList.add('show');
            }
        }

        // 5. Language Dropdown Toggle
        function toggleLangDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('langDropdownMenu');
            const isOpen = menu.classList.contains('show');
            closeAllDropdowns();
            if (!isOpen) {
                menu.classList.add('show');
            }
        }

        // 6. Close All Dropdowns
        function closeAllDropdowns() {
            document.querySelectorAll('.dropdown-panel').forEach(p => p.classList.remove('show'));
            const userBtn = document.getElementById('userProfileBtn');
            if (userBtn) userBtn.classList.remove('active');
        }

        // 7. Smooth Section Scroll for Mobile Bottom Bar
        function scrollSection(targetId, event) {
            const target = document.getElementById(targetId);
            if (target) {
                if (event) event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Global Event: Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-profile-btn') && !e.target.closest('.profile-dropdown') &&
                !e.target.closest('#notifBtn') && !e.target.closest('#notifDropdown') &&
                !e.target.closest('#langToggleBtn') && !e.target.closest('#langDropdownMenu')) {
                closeAllDropdowns();
            }
        });

        // Global Shortcut: Ctrl + K focuses search
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                const search = document.getElementById('globalSearchInput');
                if (search) search.focus();
            }
            if (e.key === 'Escape') {
                closeAllDropdowns();
                closeSidebarMobile();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
