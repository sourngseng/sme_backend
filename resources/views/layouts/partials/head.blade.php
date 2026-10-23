<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'SchoolMeal') - SchoolMeal Portal</title>

<!-- Google Fonts: Plus Jakarta Sans, Kantumruy Pro & Hanuman (Khmer) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hanuman:wght@100;300;400;700;900&family=Kantumruy+Pro:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    if (window.Chart) {
        const isKhmerDoc = document.documentElement.lang === 'km';
        Chart.defaults.font.family = isKhmerDoc
            ? "'Kantumruy Pro', 'Hanuman', 'Plus Jakarta Sans', system-ui, sans-serif"
            : "'Plus Jakarta Sans', 'Kantumruy Pro', 'Hanuman', system-ui, sans-serif";
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
        --font-sans: 'Plus Jakarta Sans', 'Kantumruy Pro', 'Hanuman', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        --font-khmer: 'Kantumruy Pro', 'Plus Jakarta Sans', 'Hanuman', system-ui, sans-serif;
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

    /* Topbar Controls in Khmer */
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

    /* Notification Dropdown in Khmer */
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

    /* Mobile Bottom Bar in Khmer */
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
        max-height: 100vh;
        z-index: 100;
        overflow: hidden;
    }

    aside.admin-sidebar::-webkit-scrollbar {
        display: none;
    }

    .sidebar-brand-box {
        padding: 1.15rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        min-height: 64px;
        flex-shrink: 0;
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

    .sidebar-menu {
        padding: 0.85rem 0.65rem;
        flex: 1 1 auto;
        min-height: 0;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.16) transparent;
    }

    .sidebar-menu::-webkit-scrollbar {
        width: 4px;
    }
    .sidebar-menu::-webkit-scrollbar-track {
        background: transparent;
    }
    .sidebar-menu::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.16);
        border-radius: 4px;
    }
    .sidebar-menu::-webkit-scrollbar-thumb:hover {
        background: rgba(56, 189, 248, 0.5);
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
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        flex-shrink: 0;
    }

    .sidebar-item:hover .sidebar-chevron {
        color: #cbd5e1;
    }

    .sidebar-dropdown-group.open > .sidebar-item .sidebar-chevron {
        transform: rotate(90deg);
        color: #38bdf8;
    }

    .sidebar-submenu {
        display: none;
        flex-direction: column;
        gap: 0.15rem;
        padding: 0.25rem 0 0.25rem 0;
    }

    .sidebar-dropdown-group.open > .sidebar-submenu {
        display: flex;
    }

    .submenu-item {
        display: flex;
        align-items: center;
        padding: 0.45rem 0.75rem 0.45rem 2.4rem;
        border-radius: 8px;
        color: #94a3b8;
        font-size: 0.775rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
    }

    .submenu-item:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.04);
    }

    .submenu-item.active {
        color: #38bdf8;
        font-weight: 700;
        background: rgba(56, 189, 248, 0.1);
    }

    .sidebar-badge {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.15rem 0.45rem;
        border-radius: 999px;
        background: #ef4444;
        color: #ffffff;
        line-height: 1;
    }

    /* Bottom Pinned User Profile Card */
    .sidebar-user-box {
        padding: 0.75rem 0.95rem;
        background: rgba(15, 23, 42, 0.85);
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 60px;
        flex-shrink: 0;
        margin-top: auto;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease;
        text-decoration: none;
    }

    .sidebar-user-box:hover {
        background: rgba(255, 255, 255, 0.05);
        border-top-color: rgba(56, 189, 248, 0.25);
    }

    .sidebar-user-content {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        min-width: 0;
    }

    .sidebar-user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid #2563EB;
        flex-shrink: 0;
    }

    .sidebar-user-info {
        line-height: 1.25;
        min-width: 0;
        overflow: hidden;
    }

    .sidebar-user-name {
        color: #ffffff;
        font-weight: 700;
        font-size: 0.8125rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-user-role {
        color: #94a3b8;
        font-size: 0.6875rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-user-chevron {
        color: #64748b;
        flex-shrink: 0;
        transition: transform 0.2s, color 0.15s;
    }

    .sidebar-user-box:hover .sidebar-user-chevron {
        color: #38bdf8;
        transform: translateX(2px);
    }

    /* Collapsed Sidebar on Desktop */
    aside.admin-sidebar.collapsed {
        width: var(--sidebar-collapsed-width);
    }
    aside.admin-sidebar.collapsed .sidebar-brand-title,
    aside.admin-sidebar.collapsed .sidebar-brand-subtitle,
    aside.admin-sidebar.collapsed .sidebar-item-left span,
    aside.admin-sidebar.collapsed .sidebar-chevron,
    aside.admin-sidebar.collapsed .sidebar-section-title,
    aside.admin-sidebar.collapsed .sidebar-submenu,
    aside.admin-sidebar.collapsed .sidebar-badge,
    aside.admin-sidebar.collapsed .sidebar-user-info,
    aside.admin-sidebar.collapsed .sidebar-user-chevron {
        display: none !important;
    }
    aside.admin-sidebar.collapsed .sidebar-item {
        justify-content: center;
        padding: 0.75rem 0;
    }
    aside.admin-sidebar.collapsed .sidebar-item-left {
        justify-content: center;
        gap: 0;
    }
    aside.admin-sidebar.collapsed .sidebar-user-box {
        justify-content: center;
        padding: 0.75rem 0;
    }
    aside.admin-sidebar.collapsed .sidebar-user-content {
        justify-content: center;
    }

    /* ================= TOPBAR HEADER ================= */
    header.admin-topbar {
        height: 64px;
        background: #ffffff;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        position: sticky;
        top: 0;
        z-index: 90;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
        max-width: 500px;
    }

    .sidebar-toggle-btn {
        background: transparent;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #64748b;
        transition: all 0.15s;
    }
    .sidebar-toggle-btn:hover {
        background: #f1f5f9;
        color: var(--text-dark);
    }

    .search-container {
        position: relative;
        width: 100%;
        max-width: 380px;
    }

    .search-container input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.4rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        font-size: 0.8125rem;
        color: var(--text-dark);
        outline: none;
        transition: all 0.2s;
        font-family: inherit;
    }

    .search-container input:focus {
        background: #ffffff;
        border-color: #2563EB;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .search-icon {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }

    .topbar-actions {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }

    .topbar-icon-btn {
        background: transparent;
        border: 1px solid #e2e8f0;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #475569;
        position: relative;
        transition: all 0.15s;
    }

    .topbar-icon-btn:hover {
        background: #f1f5f9;
        color: #1D4ED8;
        border-color: #cbd5e1;
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

    .topbar-icon-btn .badge-dot {
        position: absolute;
        top: -2px;
        right: -2px;
        width: 18px;
        height: 18px;
        background: #ef4444;
        border: 2px solid #ffffff;
        border-radius: 50%;
        font-size: 0.625rem;
        font-weight: 700;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-profile-btn {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.3rem 0.65rem 0.3rem 0.35rem;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.15s;
    }

    .user-profile-btn:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .topbar-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid #2563EB;
    }

    /* ================= FLOATING DROPDOWN PANELS ================= */
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
        display: block !important;
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
        z-index: 95;
        align-items: center;
        justify-content: space-around;
        padding: 0 0.5rem;
        padding-bottom: env(safe-area-inset-bottom, 0px);
    }

    .mobile-nav-tab {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #64748b;
        text-decoration: none;
        border: none;
        background: transparent;
        cursor: pointer;
        padding: 0.25rem 0;
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
        -webkit-backdrop-filter: blur(3px);
        z-index: 99;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .sidebar-backdrop.active {
        display: block;
        opacity: 1;
    }

    /* Mobile Responsive Viewport Rules */
    @media (max-width: 1024px) {
        aside.admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 280px !important;
            transform: translateX(-100%);
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.25);
        }

        aside.admin-sidebar.open {
            transform: translateX(0);
        }

        .search-container {
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
        .help-center-btn {
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
        .mobile-bottom-bar {
            display: flex;
            min-height: 60px;
            height: calc(60px + env(safe-area-inset-bottom, 0px));
            padding-bottom: env(safe-area-inset-bottom, 0px);
            box-sizing: border-box;
        }
        .admin-content {
            padding: 1rem 0.85rem !important;
            padding-bottom: calc(60px + env(safe-area-inset-bottom, 0px) + 1rem) !important;
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
        .user-profile-meta {
            display: none !important;
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
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.insights-track-container').forEach(function(container) {
            const track = container.querySelector('.portal-kpi-grid, .insights-scroll-track, .kitchen-grid-metrics');
            const fadeLeft = container.querySelector('.insights-fade-edge.fade-left, .kitchen-fade-edge.fade-left');
            const fadeRight = container.querySelector('.insights-fade-edge.fade-right, .kitchen-fade-edge.fade-right');
            if (!track) return;

            function updateRailFades() {
                const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
                const current = track.scrollLeft;
                const hasOverflow = maxScroll > 8;
                if (fadeLeft) fadeLeft.style.opacity = (hasOverflow && current > 12) ? '1' : '0';
                if (fadeRight) fadeRight.style.opacity = (hasOverflow && current < maxScroll - 12) ? '1' : '0';
            }

            // Desktop Mouse Drag-To-Scroll (Meta-style)
            let isDragging = false;
            let startPageX = 0;
            let initialScrollLeft = 0;
            let dragDistance = 0;

            track.addEventListener('mousedown', function(e) {
                if (e.target.closest('input, select, button, a')) return;
                isDragging = true;
                dragDistance = 0;
                track.classList.add('is-dragging');
                startPageX = e.pageX - track.offsetLeft;
                initialScrollLeft = track.scrollLeft;
            });

            const stopDrag = function() {
                if (!isDragging) return;
                isDragging = false;
                track.classList.remove('is-dragging');
                updateRailFades();
            };

            track.addEventListener('mouseleave', stopDrag);
            track.addEventListener('mouseup', stopDrag);

            track.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                e.preventDefault();
                const currentX = e.pageX - track.offsetLeft;
                const walk = (currentX - startPageX) * 1.35;
                dragDistance = Math.abs(walk);
                track.scrollLeft = initialScrollLeft - walk;
            });

            track.addEventListener('click', function(e) {
                if (dragDistance > 6) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);

            track.addEventListener('scroll', updateRailFades, { passive: true });
            window.addEventListener('resize', updateRailFades, { passive: true });
            setTimeout(updateRailFades, 200);
        });
    });
</script>

@stack('styles')
