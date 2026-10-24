@extends('layouts.master_school_admin')

@section('title', 'Classes & Grades | ' . ($activeSchool->name ?? 'Wat Bo Primary School'))
@section('header_title', '')

@section('content')
<style>
    /* Design Tokens matching School Admin Suite */
    :root {
        --primary-blue: #0066FF;
        --primary-blue-hover: #0052CC;
        --border-subtle: #E2E8F0;
        --text-slate-900: #0F172A;
        --text-slate-500: #64748B;
        --card-bg: #FFFFFF;
    }

    .school-page-container {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .school-page-header {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0.85rem;
        margin-bottom: 0.25rem;
    }

    .header-title-block {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .header-controls-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        gap: 0.75rem;
    }

    .header-controls-left {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .header-controls-left::-webkit-scrollbar {
        display: none;
    }

    .header-controls-right {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-left: auto;
        position: relative;
        flex-shrink: 0;
    }

    /* Content Menu with ("...") Trigger */
    .header-menu-trigger {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #334155;
        cursor: pointer;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        flex-shrink: 0;
    }

    .header-menu-trigger:hover,
    .header-menu-trigger.active {
        background: #EFF6FF;
        border-color: #0066FF;
        color: #0066FF;
        box-shadow: 0 2px 6px rgba(0, 102, 255, 0.18);
    }

    /* Content Menu with ("...") Popover */
    .header-context-popover {
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        box-shadow: 0 14px 35px -4px rgba(15, 23, 42, 0.16), 0 6px 12px -2px rgba(15, 23, 42, 0.08);
        width: 290px;
        z-index: 9999;
        padding: 0.5rem;
        display: none;
        animation: ctxSlideIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .header-context-popover.show {
        display: block;
    }

    .popover-header-title {
        font-size: 0.725rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748B;
        padding: 0.35rem 0.65rem 0.25rem 0.65rem;
    }

    .header-popover-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        padding: 0.6rem 0.65rem;
        border: none;
        background: transparent;
        border-radius: 10px;
        cursor: pointer;
        text-align: left;
        transition: background 0.15s ease;
    }

    .header-popover-item:hover {
        background: #F8FAFC;
    }

    .popover-item-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .popover-item-icon.blue { background: #EFF6FF; color: #0066FF; }
    .popover-item-icon.green { background: #F0FDF4; color: #16A34A; }
    .popover-item-icon.slate { background: #F1F5F9; color: #475569; }
    .popover-item-icon.emerald { background: #ECFDF5; color: #059669; }

    .popover-item-title {
        font-size: 0.825rem;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.2;
    }

    .popover-item-desc {
        font-size: 0.725rem;
        color: #64748B;
        margin-top: 0.1rem;
    }

    .header-popover-divider {
        height: 1px;
        background: #F1F5F9;
        margin: 0.35rem 0;
    }

    .pill-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 0.95rem;
        border-radius: 10px;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        font-size: 0.825rem;
        font-weight: 600;
        color: #334155;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        flex-shrink: 0;
        white-space: nowrap;
    }

    .pill-btn:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
        color: var(--text-slate-900);
    }

    .btn-add-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 1.15rem;
        border-radius: 10px;
        background: var(--primary-blue);
        color: #FFFFFF !important;
        font-size: 0.85rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 102, 255, 0.28);
        transition: all 0.15s ease;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .btn-add-primary:hover {
        background: var(--primary-blue-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0, 102, 255, 0.35);
    }

    /* 4 Stat Cards Row (Scrollable when overflow or responsive) */
    .stat-cards-scroll-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #CBD5E1 transparent;
        padding-bottom: 6px;
    }

    .stat-cards-scroll-wrapper::-webkit-scrollbar {
        height: 5px;
    }

    .stat-cards-scroll-wrapper::-webkit-scrollbar-track {
        background: transparent;
    }

    .stat-cards-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 999px;
    }

    .stat-cards-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }

    .stat-cards-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.15rem;
        width: 100%;
    }

    @media (max-width: 1240px) {
        .stat-cards-grid {
            display: flex;
            flex-wrap: nowrap;
            width: max-content;
            gap: 1.15rem;
        }

        .stat-card-custom,
        .student-stat-card {
            flex: 0 0 280px;
            min-width: 280px;
            max-width: 340px;
        }
    }

    .student-stat-card,
    .stat-card-custom {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 1.25rem 1.35rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-card-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        z-index: 2;
    }

    .stat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon-blue { background: #0066FF; color: #FFFFFF; }
    .stat-icon-green { background: #10B981; color: #FFFFFF; }
    .stat-icon-orange { background: #F97316; color: #FFFFFF; }
    .stat-icon-red { background: #EF4444; color: #FFFFFF; }
    .stat-icon-purple { background: #8B5CF6; color: #FFFFFF; }

    .stat-card-label {
        font-size: 0.775rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .stat-card-value {
        font-size: 1.75rem;
        font-weight: 900;
        color: #0F172A;
        line-height: 1.15;
        margin-top: 0.2rem;
    }

    .stat-card-trend {
        font-size: 0.725rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.2rem;
        margin-top: 0.2rem;
        color: #10B981;
    }

    .stat-watermark {
        position: absolute;
        right: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.12;
        pointer-events: none;
        z-index: 1;
    }

    /* Circular Progress Ring */
    .gauge-wrapper {
        position: relative;
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        z-index: 2;
    }

    .gauge-wrapper svg {
        transform: rotate(-90deg);
    }

    .gauge-circle-bg {
        fill: none;
        stroke: #E2E8F0;
        stroke-width: 4;
    }

    .gauge-circle-progress {
        fill: none;
        stroke: #10B981;
        stroke-width: 4;
        stroke-linecap: round;
        stroke-dasharray: 138;
        stroke-dashoffset: 26; /* 81% */
        transition: stroke-dashoffset 0.6s ease;
    }

    .gauge-text {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.725rem;
        font-weight: 800;
        color: #0F172A;
    }

    /* Tabs Navigation Bar (User Requested Tabs Concept) */
    .tabs-nav-bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 2px solid #E2E8F0;
        padding-bottom: 0.15rem;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .tabs-nav-bar::-webkit-scrollbar {
        display: none;
    }

    .tab-nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.75rem 1.25rem;
        border-radius: 12px 12px 0 0;
        border: none;
        background: transparent;
        font-size: 0.875rem;
        font-weight: 700;
        color: #64748B;
        cursor: pointer;
        transition: all 0.15s ease;
        position: relative;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .tab-nav-btn:hover {
        color: #0F172A;
        background: #F8FAFC;
    }

    .tab-nav-btn.active {
        color: #0066FF;
        background: #FFFFFF;
        box-shadow: 0 -2px 6px rgba(15, 23, 42, 0.04);
    }

    .tab-nav-btn.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: #0066FF;
        border-radius: 3px 3px 0 0;
    }

    .tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.15rem 0.55rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 800;
        background: #F1F5F9;
        color: #475569;
    }

    .tab-nav-btn.active .tab-badge {
        background: #EFF6FF;
        color: #0066FF;
    }

    /* Filter Bar */
    .filter-bar-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 0.95rem 1.15rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        flex-wrap: wrap;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
    }

    .filter-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        min-width: 140px;
        flex: 1;
    }

    .filter-item-search {
        min-width: 220px;
        flex: 1.5;
    }

    .filter-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #64748B;
        text-transform: capitalize;
    }

    .search-input-wrapper {
        position: relative;
        width: 100%;
    }

    .search-input-wrapper input {
        width: 100%;
        padding: 0.48rem 0.75rem 0.48rem 2.2rem;
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        font-size: 0.8125rem;
        color: #0F172A;
        outline: none;
        transition: border 0.15s, box-shadow 0.15s;
    }

    .search-input-wrapper input:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.12);
    }

    .search-icon-left {
        position: absolute;
        left: 0.7rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        pointer-events: none;
    }

    .filter-select {
        width: 100%;
        padding: 0.48rem 0.75rem;
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        font-size: 0.8125rem;
        color: #0F172A;
        background-color: #FFFFFF;
        outline: none;
        cursor: pointer;
        transition: border 0.15s;
    }

    .filter-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.12);
    }

    .btn-reset-filter {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 0.85rem;
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        background: #FFFFFF;
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        align-self: flex-end;
        height: 35px;
        transition: all 0.15s ease;
    }

    .btn-reset-filter:hover {
        background: #F1F5F9;
        color: #0F172A;
    }

    /* Table Container with Meta Content List Architecture */
    .table-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }

    .table-topbar {
        padding: 1rem 1.35rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #F1F5F9;
        position: relative;
        z-index: 30;
    }

    .table-heading {
        font-size: 1rem;
        font-weight: 800;
        color: #0F172A;
    }

    .show-entries-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.775rem;
        color: #64748B;
        font-weight: 600;
    }

    .show-entries-select {
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        border: 1px solid #CBD5E1;
        font-size: 0.775rem;
        font-weight: 700;
        outline: none;
        background: #FFF;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        position: relative;
    }

    .academic-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.825rem;
        text-align: left;
        white-space: nowrap;
    }

    .academic-table th {
        background: #F8FAFC;
        padding: 0.8rem 1rem;
        font-size: 0.725rem;
        font-weight: 700;
        color: #64748B;
        text-transform: capitalize;
        border-bottom: 1px solid #E2E8F0;
        white-space: nowrap;
    }

    .academic-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
        color: #334155;
        background-color: #FFFFFF;
    }

    .academic-table tr:hover td {
        background-color: #F8FAFC;
    }

    /* Fixed / Frozen Left Columns (Meta Content List Concept) */
    .sticky-col-select {
        position: sticky;
        left: 0;
        z-index: 10;
        width: 44px;
        min-width: 44px;
        max-width: 44px;
        background-color: #FFFFFF;
        text-align: center;
    }
    .academic-table th.sticky-col-select {
        background-color: #F8FAFC;
        z-index: 12;
    }

    .sticky-col-code {
        position: sticky;
        left: 44px;
        z-index: 10;
        width: 95px;
        min-width: 95px;
        background-color: #FFFFFF;
    }
    .academic-table th.sticky-col-code {
        background-color: #F8FAFC;
        z-index: 12;
    }

    .sticky-col-student {
        position: sticky;
        left: 139px;
        z-index: 10;
        min-width: 310px;
        width: 310px;
        background-color: #FFFFFF;
        border-right: 2px solid #CBD5E1 !important;
        box-shadow: 4px 0 10px -2px rgba(15, 23, 42, 0.08);
    }
    .academic-table th.sticky-col-student {
        background-color: #F8FAFC;
        z-index: 12;
        border-right: 2px solid #CBD5E1 !important;
        box-shadow: 4px 0 10px -2px rgba(15, 23, 42, 0.08);
    }

    .academic-table tr:hover td.sticky-col-select,
    .academic-table tr:hover td.sticky-col-code,
    .academic-table tr:hover td.sticky-col-student {
        background-color: #F8FAFC !important;
    }

    .entity-cell-layout {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        width: 100%;
    }

    .entity-cell-main {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
        flex: 1;
    }

    .class-icon-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .grade-badge-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #F0FDF4;
        border: 1px solid #BBF7D0;
        color: #16A34A;
        font-weight: 800;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .room-icon-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #F5F3FF;
        border: 1px solid #DDD6FE;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .entity-cell-meta {
        min-width: 0;
    }

    .entity-name-en {
        font-size: 0.85rem;
        font-weight: 700;
        color: #0F172A;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .entity-name-km {
        font-size: 0.75rem;
        color: #64748B;
        font-family: var(--font-khmer), 'Kantumruy Pro', sans-serif;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .grade-inline-pill {
        display: inline-block;
        padding: 0.1rem 0.45rem;
        border-radius: 6px;
        background: #F1F5F9;
        color: #475569;
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* Action Button as Context Menu (Meta Content List Concept) */
    .btn-context-menu {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        background: #FFFFFF;
        color: #475569;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .btn-context-menu:hover,
    .btn-context-menu.active {
        background: #EFF6FF;
        border-color: #0066FF;
        color: #0066FF;
        box-shadow: 0 2px 4px rgba(0, 102, 255, 0.15);
    }

    /* Floating Context Menu Popover */
    .floating-context-menu {
        position: fixed;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.18), 0 8px 10px -6px rgba(15, 23, 42, 0.08);
        min-width: 220px;
        z-index: 9999;
        padding: 0.35rem 0;
        display: none;
    }

    .floating-context-menu.show {
        display: block;
        animation: ctxSlideIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes ctxSlideIn {
        from { transform: translateY(4px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .context-menu-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        width: 100%;
        padding: 0.55rem 0.95rem;
        border: none;
        background: transparent;
        text-align: left;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease;
        text-decoration: none;
    }

    .context-menu-item:hover {
        background: #F1F5F9;
        color: #0F172A;
    }

    .context-menu-item.danger {
        color: #DC2626;
    }

    .context-menu-item.danger:hover {
        background: #FEF2F2;
        color: #DC2626;
    }

    .context-menu-divider {
        height: 1px;
        background: #F1F5F9;
        margin: 0.35rem 0;
    }

    /* Column Customizer (Meta Content List Concept) */
    .column-customizer-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.85rem;
        border-radius: 8px;
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        font-size: 0.775rem;
        font-weight: 700;
        color: #334155;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .column-customizer-btn:hover,
    .column-customizer-btn.active {
        background: #F8FAFC;
        border-color: #0066FF;
        color: #0066FF;
    }

    .columns-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.1rem 0.45rem;
        background: #EFF6FF;
        color: #0066FF;
        font-size: 0.7rem;
        font-weight: 800;
        border-radius: 999px;
        border: 1px solid #BFDBFE;
    }

    .column-customizer-popover {
        position: absolute;
        right: 0;
        top: calc(100% + 6px);
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.15);
        width: 290px;
        z-index: 100;
        display: none;
        padding: 0.85rem;
        animation: ctxSlideIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .column-customizer-popover.show {
        display: block;
    }

    .col-check-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.4rem 0.5rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        color: #334155;
        transition: background 0.15s ease;
    }

    .col-check-item:hover {
        background: #F8FAFC;
    }

    .col-check-item input[type="checkbox"] {
        cursor: pointer;
        accent-color: #0066FF;
    }

    /* Badges & Pills */
    .id-code-badge {
        color: #0066FF;
        font-weight: 800;
        font-family: inherit;
        text-decoration: none;
    }

    .id-code-badge:hover {
        text-decoration: underline;
    }

    .shift-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        font-size: 0.725rem;
        font-weight: 700;
        background: #EFF6FF;
        color: #0369A1;
        border: 1px solid #BAE6FD;
    }

    .stage-pill {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        font-size: 0.725rem;
        font-weight: 700;
        background: #F1F5F9;
        color: #334155;
    }

    .meal-pill {
        display: inline-block;
        padding: 0.22rem 0.65rem;
        border-radius: 999px;
        font-size: 0.725rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .meal-regular { background: #E0F2FE; color: #0369A1; }
    .meal-halal { background: #DCFCE7; color: #15803D; }

    .allergy-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        font-size: 0.725rem;
        font-weight: 700;
        background: #FEE2E2;
        color: #DC2626;
        border: 1px solid #FECACA;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        font-size: 0.725rem;
        font-weight: 700;
    }

    .status-dot { width: 6px; height: 6px; border-radius: 50%; }
    .status-active { background: #DCFCE7; color: #16A34A; }
    .status-active .status-dot { background: #16A34A; }
    .status-inactive { background: #FEE2E2; color: #DC2626; }
    .status-inactive .status-dot { background: #DC2626; }

    .gender-pill {
        display: inline-block;
        padding: 0.2rem 0.65rem;
        border-radius: 999px;
        font-size: 0.725rem;
        font-weight: 600;
    }
    .gender-male { background: #E0F2FE; color: #0284C7; }

    .teacher-meta-cell { line-height: 1.3; }
    .teacher-name-title { font-weight: 700; color: #0F172A; font-size: 0.825rem; }
    .teacher-phone-sub { font-size: 0.725rem; color: #64748B; display: flex; align-items: center; gap: 0.3rem; margin-top: 0.1rem; }

    /* Modal Styling with Multilingual Language Tabs */
    .custom-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .custom-modal-backdrop.show { display: flex; }

    .custom-modal-card {
        background: #FFFFFF;
        border-radius: 20px;
        width: 100%;
        max-width: 650px;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
        border: 1px solid #E2E8F0;
        overflow: hidden;
        animation: modalSlideIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes modalSlideIn {
        from { transform: translateY(15px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #E2E8F0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-head-title { font-size: 1.15rem; font-weight: 800; color: #0F172A; }

    .modal-close-btn {
        background: transparent;
        border: none;
        color: #94A3B8;
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.25rem;
    }

    .modal-close-btn:hover { color: #0F172A; }

    .modal-lang-tabs {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        background: #F1F5F9;
        padding: 0.25rem;
        border-radius: 8px;
        margin-bottom: 1.15rem;
    }

    .modal-lang-tab {
        flex: 1;
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
        border: none;
        background: transparent;
        font-size: 0.775rem;
        font-weight: 700;
        color: #64748B;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        transition: all 0.15s ease;
    }

    .modal-lang-tab.active {
        background: #FFFFFF;
        color: #0F172A;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
    }

    .modal-body {
        padding: 1.5rem;
        max-height: 75vh;
        overflow-y: auto;
    }

    .form-group-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.95rem;
        margin-bottom: 0.95rem;
    }

    .form-field-wrap {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .form-field-wrap label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #334155;
    }

    .form-input-control {
        width: 100%;
        padding: 0.55rem 0.85rem;
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        font-size: 0.825rem;
        color: #0F172A;
        outline: none;
    }

    .form-input-control:focus {
        border-color: #0066FF;
        box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.12);
    }

    .modal-footer {
        padding: 1.15rem 1.5rem;
        border-top: 1px solid #E2E8F0;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        background: #F8FAFC;
    }

    /* Loading Spinner */
    .table-loading-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(2px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 25;
        border-radius: 12px;
    }

    .table-loading-overlay.active { display: flex; }

    .spinner-loader {
        width: 38px;
        height: 38px;
        border: 3px solid #E2E8F0;
        border-top-color: #0066FF;
        border-radius: 50%;
        animation: spinLoader 0.7s linear infinite;
    }

    @keyframes spinLoader { to { transform: rotate(360deg); } }
</style>

<div class="school-page-container">
    <!-- Header Row -->
    <div class="school-page-header">
        <div class="header-title-block">
            <div>
                <h1 class="school-page-title">{{ transLabel('menu.classes_grades', 'Classes & Grades') }}</h1>
                <p class="school-page-subtitle">{{ transLabel('school.manage_classes_desc', 'Manage classrooms, grade levels, homeroom educators, and meal drop-off logistics.') }}</p>
            </div>
        </div>

        <div class="header-controls-bar">
            <!-- Left Controls: Date & School Pills -->
            <div class="header-controls-left">
                <!-- Date Filter Pill (Identical to Students) -->
                <button type="button" class="pill-btn" onclick="openDateModal()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>{{ $dateLabel ?? now()->format('D, M j, Y') }}</span>
                </button>

                <!-- Campus Context Pill -->
                <button type="button" class="pill-btn" onclick="openSwitchSchoolModal()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m4 6 8-4 8 4"/><path d="M18 10 22 12v10H2V12l4-2"/><path d="M6 5v17"/><path d="M18 5v17"/></svg>
                    <span>{{ $activeSchool->name ?? 'Wat Bo Primary School' }}</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
            </div>

            <!-- Right Controls: Content Menu with ("...") -->
            <div class="header-controls-right">
                <button type="button" class="header-menu-trigger" id="headerActionsMenuBtn" onclick="toggleHeaderActionsMenu(event)" title="{{ transLabel('common.options', 'Options') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="1.5"></circle>
                        <circle cx="6" cy="12" r="1.5"></circle>
                        <circle cx="18" cy="12" r="1.5"></circle>
                    </svg>
                </button>

                <!-- Floating Content Menu Popover -->
                <div class="header-context-popover" id="headerActionsMenuPop">
                    <div class="popover-header-title">{{ transLabel('common.actions', 'Academic Actions') }}</div>

                    <!-- 1. Add Class -->
                    <button type="button" class="header-popover-item" onclick="closeHeaderActionsMenu(); openAddClassModal();">
                        <div class="popover-item-icon blue">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </div>
                        <div>
                            <div class="popover-item-title">{{ transLabel('school.add_class', 'Add Class') }}</div>
                            <div class="popover-item-desc">Create classroom, section & room</div>
                        </div>
                    </button>

                    <!-- 2. Add Grade -->
                    <button type="button" class="header-popover-item" onclick="closeHeaderActionsMenu(); openAddGradeModal();">
                        <div class="popover-item-icon green">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                        </div>
                        <div>
                            <div class="popover-item-title">{{ transLabel('school.add_grade', 'Add Grade') }}</div>
                            <div class="popover-item-desc">Define grade level & curriculum stage</div>
                        </div>
                    </button>

                    <div class="header-popover-divider"></div>
                    <div class="popover-header-title">{{ transLabel('common.data_reports', 'Data & Reports') }}</div>

                    <!-- 3. Export CSV -->
                    <button type="button" class="header-popover-item" onclick="closeHeaderActionsMenu(); exportAcademicCsv('csv');">
                        <div class="popover-item-icon slate">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </div>
                        <div>
                            <div class="popover-item-title">{{ transLabel('common.export', 'Export') }} (CSV)</div>
                            <div class="popover-item-desc">Spreadsheet of classrooms and grades</div>
                        </div>
                    </button>

                    <!-- 4. Export Excel -->
                    <button type="button" class="header-popover-item" onclick="closeHeaderActionsMenu(); exportAcademicCsv('excel');">
                        <div class="popover-item-icon emerald">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/><path d="M15 3v18"/><path d="M3 9h18"/><path d="M3 15h18"/></svg>
                        </div>
                        <div>
                            <div class="popover-item-title">{{ transLabel('common.export', 'Export') }} (Excel)</div>
                            <div class="popover-item-desc">Complete academic roster and logistics</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 KPI Stat Cards Row (Scrollable when overflow or responsive) -->
    <div class="stat-cards-scroll-wrapper">
        <div class="stat-cards-grid">
            <!-- 1. Total Classes -->
            <div class="student-stat-card">
                <div class="stat-card-left">
                    <div class="stat-icon-box stat-icon-blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
                    </div>
                    <div>
                        <div class="stat-card-label" style="color: #0066FF;">{{ transLabel('school.total_classes', 'Total Classes') }}</div>
                        <div class="stat-card-value" id="kpiTotalClasses">{{ $stats['total_classes'] ?? '12' }}</div>
                        <div class="stat-card-trend trend-up">
                            <span>{{ $stats['total_classes_change'] ?? '↑ 2% vs last month' }}</span>
                        </div>
                    </div>
                </div>
                <div class="stat-watermark">
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="#0066FF" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
                </div>
            </div>

            <!-- 2. Active Grades (with 81% circular ring matching Students Card 2) -->
            <div class="student-stat-card">
                <div class="stat-card-left">
                    <div class="stat-icon-box stat-icon-green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                    <div>
                        <div class="stat-card-label" style="color: #10B981;">{{ transLabel('school.active_grades', 'Active Grades') }}</div>
                        <div class="stat-card-value" id="kpiActiveGrades">{{ $stats['active_grades'] ?? '6' }}</div>
                        <div class="stat-card-trend trend-up">
                            <span>{{ $stats['active_grades_change'] ?? '↑ 5% vs last month' }}</span>
                        </div>
                    </div>
                </div>
                <!-- Circular Gauge -->
                <div class="gauge-wrapper">
                    <svg width="52" height="52" viewBox="0 0 52 52">
                        <circle class="gauge-circle-bg" cx="26" cy="26" r="22"/>
                        <circle class="gauge-circle-progress" id="gaugeCircleProgress" cx="26" cy="26" r="22" style="stroke: #10B981; stroke-dasharray: 138.23; stroke-dashoffset: {{ round(138.23 * (1 - (($stats['active_grades_pct'] ?? 81) / 100)), 2) }};"/>
                    </svg>
                    <div class="gauge-text" id="kpiActiveGradesPct">{{ $stats['active_grades_pct'] ?? '81' }}%</div>
                </div>
            </div>

            <!-- 3. Homeroom Educators (Orange Theme matching Students Card 3) -->
            <div class="student-stat-card">
                <div class="stat-card-left">
                    <div class="stat-icon-box stat-icon-orange">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <div class="stat-card-label" style="color: #F97316;">{{ transLabel('school.homeroom_staff', 'Homeroom Staff') }}</div>
                        <div class="stat-card-value" id="kpiHomeroomStaff">{{ $stats['homeroom_staff'] ?? '12' }}</div>
                        <div class="stat-card-trend trend-up">
                            <span>{{ $stats['homeroom_staff_change'] ?? '↑ 8% vs last month' }}</span>
                        </div>
                    </div>
                </div>
                <div class="stat-watermark">
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="#F97316" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>

            <!-- 4. Pupil Capacity (Red Theme matching Students Card 4) -->
            <div class="student-stat-card">
                <div class="stat-card-left">
                    <div class="stat-icon-box stat-icon-red">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                    </div>
                    <div>
                        <div class="stat-card-label" style="color: #EF4444;">{{ transLabel('school.total_capacity', 'Pupil Capacity') }}</div>
                        <div class="stat-card-value" id="kpiCapacity">{{ $stats['total_capacity'] ?? '480' }}</div>
                        <div class="stat-card-trend trend-up">
                            <span>{{ $stats['total_capacity_change'] ?? '↑ 1% vs last month' }}</span>
                        </div>
                    </div>
                </div>
                <div class="stat-watermark">
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation Bar (User Requested Tabs Concept) -->
    <div class="tabs-nav-bar">
        <button type="button" class="tab-nav-btn {{ ($activeTab ?? 'classes') === 'classes' ? 'active' : '' }}" id="tabBtn_classes" data-tab="classes" onclick="switchAcademicTab('classes')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
            <span>{{ transLabel('school.classes_and_sections', 'Classes & Sections') }}</span>
            <span class="tab-badge" id="classesTabBadge">{{ count($classes) }}</span>
        </button>

        <button type="button" class="tab-nav-btn {{ ($activeTab ?? '') === 'grades' ? 'active' : '' }}" id="tabBtn_grades" data-tab="grades" onclick="switchAcademicTab('grades')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            <span>{{ transLabel('school.grade_levels', 'Grade Levels') }}</span>
            <span class="tab-badge" id="gradesTabBadge">{{ count($grades) }}</span>
        </button>

        <button type="button" class="tab-nav-btn {{ ($activeTab ?? '') === 'rooms' ? 'active' : '' }}" id="tabBtn_rooms" data-tab="rooms" onclick="switchAcademicTab('rooms')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>{{ transLabel('school.campus_rooms', 'Campus Rooms & Drops') }}</span>
            <span class="tab-badge" id="roomsTabBadge">{{ count($rooms) }}</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar-card">
        <!-- Search -->
        <div class="filter-item filter-item-search">
            <span class="filter-label" id="searchFilterLabel">{{ transLabel('common.search', 'Search') }}</span>
            <div class="search-input-wrapper">
                <svg class="search-icon-left" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="academicSearchInput" value="{{ request('search') }}" placeholder="Search by class code, teacher, room..." oninput="onAcademicSearchInput(this.value)">
            </div>
        </div>

        <!-- Grade Filter -->
        <div class="filter-item" id="filterWrapGrade">
            <span class="filter-label">{{ transLabel('school.grade', 'Grade') }}</span>
            <select class="filter-select" id="academicGradeFilterSelect" onchange="onAcademicFilterChange()">
                <option value="all">{{ transLabel('school.all_grades', 'All Grades') }}</option>
                @foreach($gradesDropdown as $gr)
                    <option value="{{ $gr->id }}" {{ request('grade') == $gr->id ? 'selected' : '' }}>{{ $gr->name }} ({{ $gr->code }})</option>
                @endforeach
            </select>
        </div>

        <!-- Shift Filter -->
        <div class="filter-item" id="filterWrapShift">
            <span class="filter-label">{{ transLabel('school.shift', 'Operating Shift') }}</span>
            <select class="filter-select" id="academicShiftFilterSelect" onchange="onAcademicFilterChange()">
                <option value="all">{{ transLabel('common.all', 'All') }}</option>
                @foreach($shiftsDropdown as $sh)
                    <option value="{{ $sh->id }}" {{ request('shift') == $sh->id ? 'selected' : '' }}>{{ $sh->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div class="filter-item">
            <span class="filter-label">{{ transLabel('school.status', 'Status') }}</span>
            <select class="filter-select" id="academicStatusFilterSelect" onchange="onAcademicFilterChange()">
                <option value="all">{{ transLabel('common.all', 'All') }}</option>
                <option value="active" {{ strtolower(request('status')) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ strtolower(request('status')) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Reset Button -->
        <button type="button" class="btn-reset-filter" onclick="resetAcademicFilters()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            <span>{{ transLabel('school.reset_filters', 'Reset Filters') }}</span>
        </button>
    </div>

    <!-- Table Card (Meta Content List Concept) -->
    <div class="table-card">
        <div class="table-topbar">
            <div class="table-heading" id="academicTableHeading">
                {{ ($activeTab ?? 'classes') === 'grades' ? 'Grade Levels' : (($activeTab ?? '') === 'rooms' ? 'Campus Rooms & Logistics' : 'Classes & Sections') }} ({{ $totalCount ?? count($classes) }})
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <!-- Column Customizer Dropdown (Meta Content List Concept) -->
                <div style="position: relative;">
                    <button type="button" class="column-customizer-btn customizerMenuBtn" id="btnCustomizeColumns" onclick="toggleColumnCustomizer(event)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7m0-18H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7m0-18v18"/></svg>
                        <span>{{ transLabel('common.columns', 'Columns') }}</span>
                        <span class="columns-count-badge" id="visibleColumnsCountBadge">8/9</span>
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="column-customizer-popover" id="columnCustomizerPopover">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid #F1F5F9; margin-bottom: 0.5rem;">
                            <div style="font-weight: 800; font-size: 0.825rem; color: #0F172A;">Customize Columns</div>
                            <button type="button" onclick="resetColumnsToDefault()" style="background: none; border: none; font-size: 0.725rem; color: #0066FF; font-weight: 700; cursor: pointer;">Reset</button>
                        </div>
                        <div style="font-size: 0.725rem; color: #64748B; margin-bottom: 0.5rem;">Select columns to display in this list:</div>
                        <div id="columnChecklistContainer" style="max-height: 280px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.2rem;">
                            <!-- Rendered by JS -->
                        </div>
                    </div>
                </div>

                <!-- Show entries select -->
                <div class="show-entries-box">
                    <span>Show</span>
                    <select class="show-entries-select" id="showEntriesSelect" onchange="changeAcademicPerPage(this.value)">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                    </select>
                    <span>entries</span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <!-- Loading Overlay -->
            <div class="table-loading-overlay" id="academicTableLoadingOverlay">
                <div class="spinner-loader"></div>
            </div>

            <!-- Dynamic Table Structure Based on Active Tab -->
            <table class="academic-table" id="academicTable">
                <thead id="academicTableHead">
                    @if(($activeTab ?? 'classes') === 'grades')
                        <tr>
                            <th class="sticky-col-select" data-col="select"><input type="checkbox" onchange="toggleSelectAllRows(this)" style="cursor: pointer;"></th>
                            <th class="sticky-col-code" data-col="code">Grade Code</th>
                            <th class="sticky-col-student" data-col="grade_name">Grade Level & Actions</th>
                            <th data-col="stage">Academic Stage</th>
                            <th data-col="classes">Classes / Sections</th>
                            <th data-col="enrolled">Enrolled Pupils</th>
                            <th data-col="meal_rate">Meal Participation</th>
                            <th data-col="allergies">Dietary Allergens</th>
                            <th data-col="status">Status</th>
                            <th data-col="desc">Curriculum / Notes</th>
                        </tr>
                    @elseif(($activeTab ?? '') === 'rooms')
                        <tr>
                            <th class="sticky-col-select" data-col="select"><input type="checkbox" onchange="toggleSelectAllRows(this)" style="cursor: pointer;"></th>
                            <th class="sticky-col-code" data-col="code">Room Code</th>
                            <th class="sticky-col-student" data-col="room_name">Room Location & Actions</th>
                            <th data-col="building">Building Block</th>
                            <th data-col="assigned_class">Assigned Class</th>
                            <th data-col="teacher">Homeroom Lead</th>
                            <th data-col="capacity">Utilization Ratio</th>
                            <th data-col="drop">Meal Crate Station</th>
                            <th data-col="shift">Shift</th>
                            <th data-col="status">Status</th>
                        </tr>
                    @else
                        <tr>
                            <th class="sticky-col-select" data-col="select"><input type="checkbox" onchange="toggleSelectAllRows(this)" style="cursor: pointer;"></th>
                            <th class="sticky-col-code" data-col="code">Class Code</th>
                            <th class="sticky-col-student" data-col="class_name">Classroom & Actions</th>
                            <th data-col="teacher">Homeroom Teacher</th>
                            <th data-col="grade">Grade Level</th>
                            <th data-col="room">Room Location</th>
                            <th data-col="shift">Shift</th>
                            <th data-col="enrolled">Enrolled Pupils</th>
                            <th data-col="meal">Meal Subscribed</th>
                            <th data-col="allergy">Allergies</th>
                            <th data-col="status">Status</th>
                            <th data-col="notes">Delivery Instructions</th>
                        </tr>
                    @endif
                </thead>
                <tbody id="academicTableBody">
                    @if(($activeTab ?? 'classes') === 'grades')
                        @include('school.partials.grade_rows')
                    @elseif(($activeTab ?? '') === 'rooms')
                        @include('school.partials.room_rows')
                    @else
                        @include('school.partials.class_rows')
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= CONTEXT MENUS (Meta Content List Concept) ================= -->

<!-- 1. Class Context Menu Popover -->
<div class="floating-context-menu" id="classContextMenuPop">
    <div style="padding: 0.5rem 0.95rem; font-size: 0.725rem; font-weight: 700; color: #64748B; border-bottom: 1px solid #F1F5F9; margin-bottom: 0.25rem;" id="classContextMenuTitle">
        Class Options
    </div>
    <button type="button" class="context-menu-item" onclick="execClassContextMenuAction('view')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
        <span>View Class Roster</span>
    </button>
    <button type="button" class="context-menu-item" onclick="execClassContextMenuAction('edit')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
        <span>Edit Class Info</span>
    </button>
    <button type="button" class="context-menu-item" onclick="execClassContextMenuAction('view_students')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>View Students in Class</span>
    </button>
    <button type="button" class="context-menu-item" onclick="execClassContextMenuAction('copy_phone')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
        <span>Copy Teacher Phone</span>
    </button>
    <button type="button" class="context-menu-item" onclick="execClassContextMenuAction('call_teacher')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <span>Call Homeroom Teacher</span>
    </button>
    <button type="button" class="context-menu-item" onclick="execClassContextMenuAction('print_crate')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        <span>Print Crate Drop Label</span>
    </button>
    <div class="context-menu-divider"></div>
    <button type="button" class="context-menu-item danger" onclick="execClassContextMenuAction('archive')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/></svg>
        <span>Archive Class</span>
    </button>
</div>

<!-- 2. Grade Context Menu Popover -->
<div class="floating-context-menu" id="gradeContextMenuPop">
    <div style="padding: 0.5rem 0.95rem; font-size: 0.725rem; font-weight: 700; color: #64748B; border-bottom: 1px solid #F1F5F9; margin-bottom: 0.25rem;" id="gradeContextMenuTitle">
        Grade Options
    </div>
    <button type="button" class="context-menu-item" onclick="execGradeContextMenuAction('view_classes')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/></svg>
        <span>View Grade Classes</span>
    </button>
    <button type="button" class="context-menu-item" onclick="execGradeContextMenuAction('edit')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
        <span>Edit Grade Info</span>
    </button>
    <div class="context-menu-divider"></div>
    <button type="button" class="context-menu-item danger" onclick="execGradeContextMenuAction('archive')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/></svg>
        <span>Archive Grade</span>
    </button>
</div>

<!-- ================= MULTILINGUAL FORM MODALS ================= -->

<!-- 1. Add / Edit Class Modal (User Requested Multilingual Modal) -->
<div class="custom-modal-backdrop" id="classModal">
    <div class="custom-modal-card">
        <div class="modal-head">
            <div class="modal-head-title" id="classModalTitle">+ Add Classroom Section</div>
            <button type="button" class="modal-close-btn" onclick="closeClassModal()">✕</button>
        </div>

        <form id="classForm" onsubmit="handleClassFormSubmit(event)">
            @csrf
            <input type="hidden" id="classFormId" name="class_id">

            <div class="modal-body">
                <!-- Multilingual Language Switcher inside Modal -->
                <div class="modal-lang-tabs">
                    <button type="button" class="modal-lang-tab active" id="classLangBtn_en" onclick="switchClassModalLang('en')">
                        <span>🇬🇧 English (Latin)</span>
                    </button>
                    <button type="button" class="modal-lang-tab" id="classLangBtn_km" onclick="switchClassModalLang('km')">
                        <span>🇰🇭 ភាសាខ្មែរ (Khmer)</span>
                    </button>
                </div>

                <!-- Code & Capacity Row -->
                <div class="form-group-grid">
                    <div class="form-field-wrap">
                        <label>Class Code (e.g. 4A) *</label>
                        <input type="text" id="classInputCode" name="code" required placeholder="e.g. 4A" class="form-input-control">
                    </div>
                    <div class="form-field-wrap">
                        <label>Max Pupil Capacity *</label>
                        <input type="number" id="classInputCapacity" name="capacity" required value="40" min="1" max="100" class="form-input-control">
                    </div>
                </div>

                <!-- English Language Inputs -->
                <div id="classLangSection_en">
                    <div class="form-group-grid">
                        <div class="form-field-wrap">
                            <label>Class Name (English) *</label>
                            <input type="text" id="classInputName" name="name" required placeholder="e.g. Class 4A" class="form-input-control">
                        </div>
                        <div class="form-field-wrap">
                            <label>Room / Location (English)</label>
                            <input type="text" id="classInputRoom" name="room_number" placeholder="e.g. Room 204 - Building B" class="form-input-control">
                        </div>
                    </div>
                    <div class="form-group-grid">
                        <div class="form-field-wrap">
                            <label>Homeroom Teacher (English)</label>
                            <input type="text" id="classInputTeacher" name="teacher_name" placeholder="e.g. Chanthy Dara" class="form-input-control">
                        </div>
                        <div class="form-field-wrap">
                            <label>Teacher Contact Phone</label>
                            <input type="text" id="classInputPhone" name="teacher_phone" placeholder="+855 16 234 567" class="form-input-control">
                        </div>
                    </div>
                    <div class="form-field-wrap" style="margin-bottom: 0.95rem;">
                        <label>Meal Delivery Notes (English)</label>
                        <input type="text" id="classInputDesc" name="description" placeholder="e.g. Direct crate drop inside front entrance." class="form-input-control">
                    </div>
                </div>

                <!-- Khmer Language Inputs -->
                <div id="classLangSection_km" style="display: none;">
                    <div class="form-group-grid">
                        <div class="form-field-wrap">
                            <label>ឈ្មោះថ្នាក់ (Khmer Name)</label>
                            <input type="text" id="classInputNameKm" name="name_km" placeholder="ឧ. ថ្នាក់ ៤A" class="form-input-control" style="font-family: var(--font-khmer), 'Kantumruy Pro', sans-serif;">
                        </div>
                        <div class="form-field-wrap">
                            <label>បន្ទប់ / អគារ (Khmer Location)</label>
                            <input type="text" id="classInputRoomKm" name="room_number_km" placeholder="ឧ. បន្ទប់ ២០៤ - អគារ B" class="form-input-control" style="font-family: var(--font-khmer), 'Kantumruy Pro', sans-serif;">
                        </div>
                    </div>
                    <div class="form-group-grid">
                        <div class="form-field-wrap">
                            <label>គ្រូបន្ទុកថ្នាក់ (Khmer Teacher)</label>
                            <input type="text" id="classInputTeacherKm" name="teacher_name_km" placeholder="ឧ. ចាន់ធី ដារ៉ា" class="form-input-control" style="font-family: var(--font-khmer), 'Kantumruy Pro', sans-serif;">
                        </div>
                        <div class="form-field-wrap">
                            <label>ព័ត៌មានបន្ថែម (Khmer Delivery Notes)</label>
                            <input type="text" id="classInputDescKm" name="description_km" placeholder="ឧ. ដាក់កន្ត្រកបាយនៅមាត់ទ្វារខាងមុខ" class="form-input-control" style="font-family: var(--font-khmer), 'Kantumruy Pro', sans-serif;">
                        </div>
                    </div>
                </div>

                <!-- Grade & Shift Selection -->
                <div class="form-group-grid">
                    <div class="form-field-wrap">
                        <label>Grade Level</label>
                        <select id="classInputGradeId" name="school_grade_id" class="form-input-control">
                            <option value="">-- Select Grade Level --</option>
                            @foreach($gradesDropdown as $gr)
                                <option value="{{ $gr->id }}">{{ $gr->name }} ({{ $gr->name_km }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field-wrap">
                        <label>Operating Shift</label>
                        <select id="classInputShiftId" name="school_shift_id" class="form-input-control">
                            <option value="">-- Select Shift --</option>
                            @foreach($shiftsDropdown as $sh)
                                <option value="{{ $sh->id }}">{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-field-wrap">
                    <label>Status</label>
                    <select id="classInputStatus" name="status" class="form-input-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="pill-btn" onclick="closeClassModal()">Cancel</button>
                <button type="submit" class="btn-add-primary" id="classSubmitBtn">Save Classroom</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Add / Edit Grade Modal (User Requested Multilingual Modal) -->
<div class="custom-modal-backdrop" id="gradeModal">
    <div class="custom-modal-card" style="max-width: 580px;">
        <div class="modal-head">
            <div class="modal-head-title" id="gradeModalTitle">+ Add Grade Level</div>
            <button type="button" class="modal-close-btn" onclick="closeGradeModal()">✕</button>
        </div>

        <form id="gradeForm" onsubmit="handleGradeFormSubmit(event)">
            @csrf
            <input type="hidden" id="gradeFormId" name="grade_id">

            <div class="modal-body">
                <!-- Multilingual Language Switcher inside Modal -->
                <div class="modal-lang-tabs">
                    <button type="button" class="modal-lang-tab active" id="gradeLangBtn_en" onclick="switchGradeModalLang('en')">
                        <span>🇬🇧 English (Latin)</span>
                    </button>
                    <button type="button" class="modal-lang-tab" id="gradeLangBtn_km" onclick="switchGradeModalLang('km')">
                        <span>🇰🇭 ភាសាខ្មែរ (Khmer)</span>
                    </button>
                </div>

                <div class="form-group-grid">
                    <div class="form-field-wrap">
                        <label>Grade Code (e.g. G4, K1) *</label>
                        <input type="text" id="gradeInputCode" name="code" required placeholder="e.g. G4" class="form-input-control">
                    </div>
                    <div class="form-field-wrap">
                        <label>Level Number (1 to 12) *</label>
                        <input type="number" id="gradeInputLevel" name="level_number" required value="4" min="1" max="12" class="form-input-control">
                    </div>
                </div>

                <!-- English Section -->
                <div id="gradeLangSection_en">
                    <div class="form-group-grid">
                        <div class="form-field-wrap">
                            <label>Grade Name (English) *</label>
                            <input type="text" id="gradeInputName" name="name" required placeholder="e.g. Grade 4" class="form-input-control">
                        </div>
                        <div class="form-field-wrap">
                            <label>Academic Stage (English)</label>
                            <input type="text" id="gradeInputStage" name="academic_stage" placeholder="e.g. Upper Primary" class="form-input-control">
                        </div>
                    </div>
                    <div class="form-field-wrap" style="margin-bottom: 0.95rem;">
                        <label>Curriculum / Nutrition Notes (English)</label>
                        <textarea id="gradeInputDesc" name="description" rows="2" placeholder="e.g. Standard healthy lunchboxes with allergen badges." class="form-input-control"></textarea>
                    </div>
                </div>

                <!-- Khmer Section -->
                <div id="gradeLangSection_km" style="display: none;">
                    <div class="form-group-grid">
                        <div class="form-field-wrap">
                            <label>ឈ្មោះកម្រិតថ្នាក់ (Khmer Name)</label>
                            <input type="text" id="gradeInputNameKm" name="name_km" placeholder="ឧ. ថ្នាក់ទី ៤" class="form-input-control" style="font-family: var(--font-khmer), 'Kantumruy Pro', sans-serif;">
                        </div>
                        <div class="form-field-wrap">
                            <label>វគ្គសិក្សា (Khmer Stage)</label>
                            <input type="text" id="gradeInputStageKm" name="academic_stage_km" placeholder="ឧ. បឋមសិក្សាជាន់ខ្ពស់" class="form-input-control" style="font-family: var(--font-khmer), 'Kantumruy Pro', sans-serif;">
                        </div>
                    </div>
                    <div class="form-field-wrap" style="margin-bottom: 0.95rem;">
                        <label>ព័ត៌មានបន្ថែម (Khmer Notes)</label>
                        <textarea id="gradeInputDescKm" name="description_km" rows="2" placeholder="ឧ. ការតាមដានរបបអាហារសកម្មជាមួយផ្លាកសញ្ញាអាឡែរហ្ស៊ី" class="form-input-control" style="font-family: var(--font-khmer), 'Kantumruy Pro', sans-serif;"></textarea>
                    </div>
                </div>

                <div class="form-field-wrap">
                    <label>Status</label>
                    <select id="gradeInputStatus" name="status" class="form-input-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="pill-btn" onclick="closeGradeModal()">Cancel</button>
                <button type="submit" class="btn-add-primary" id="gradeSubmitBtn">Save Grade Level</button>
            </div>
        </form>
    </div>
</div>

<!-- Switch School Modal -->
<div class="custom-modal-backdrop" id="switchSchoolModal">
    <div class="custom-modal-card" style="max-width: 480px;">
        <div class="modal-head">
            <div class="modal-head-title">Select Active School Campus</div>
            <button type="button" class="modal-close-btn" onclick="closeSwitchSchoolModal()">✕</button>
        </div>
        <form action="{{ route('school.set_school') }}" method="POST">
            @csrf
            <div class="modal-body">
                <p style="font-size: 0.85rem; color: #64748B; margin-bottom: 1rem;">
                    Select the campus you wish to administer. All classrooms, rosters, and shifts will update instantly.
                </p>
                <div class="form-field-wrap">
                    <label>Choose School Campus</label>
                    <select name="school_id" class="form-input-control" style="font-weight: 700;">
                        @foreach($allSchools as $sch)
                            <option value="{{ $sch->id }}" {{ $sch->id === $activeSchool->id ? 'selected' : '' }}>
                                {{ $sch->name }} ({{ $sch->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="pill-btn" onclick="closeSwitchSchoolModal()">Cancel</button>
                <button type="submit" class="btn-add-primary">Switch Campus</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= JAVASCRIPT CONTROLLERS ================= -->
<script>
    // State Management for Academic Portal
    let currentTab = "{{ $activeTab ?? 'classes' }}";
    let academicParams = {
        tab: "{{ $activeTab ?? 'classes' }}",
        search: "{{ request('search') }}",
        grade: "{{ request('grade', 'all') }}",
        shift: "{{ request('shift', 'all') }}",
        status: "{{ request('status', 'all') }}"
    };

    let searchTimer = null;

    // Meta Content List Columns Schema per Tab
    const TAB_SCHEMAS = {
        classes: [
            { key: 'select', label: 'Checkbox', frozen: true, default: true, alwaysVisible: true },
            { key: 'code', label: 'Class Code', frozen: true, default: true, alwaysVisible: true },
            { key: 'class_name', label: 'Classroom & Action', frozen: true, default: true, alwaysVisible: true },
            { key: 'teacher', label: 'Homeroom Teacher', frozen: false, default: true },
            { key: 'grade', label: 'Grade Level', frozen: false, default: true },
            { key: 'room', label: 'Room Location', frozen: false, default: true },
            { key: 'shift', label: 'Operating Shift', frozen: false, default: true },
            { key: 'enrolled', label: 'Enrolled Pupils', frozen: false, default: true },
            { key: 'meal', label: 'Meal Subscribed', frozen: false, default: true },
            { key: 'allergy', label: 'Allergies', frozen: false, default: true },
            { key: 'status', label: 'Account Status', frozen: false, default: true },
            { key: 'notes', label: 'Delivery Notes', frozen: false, default: false }
        ],
        grades: [
            { key: 'select', label: 'Checkbox', frozen: true, default: true, alwaysVisible: true },
            { key: 'code', label: 'Grade Code', frozen: true, default: true, alwaysVisible: true },
            { key: 'grade_name', label: 'Grade Level & Action', frozen: true, default: true, alwaysVisible: true },
            { key: 'stage', label: 'Academic Stage', frozen: false, default: true },
            { key: 'classes', label: 'Class Sections', frozen: false, default: true },
            { key: 'enrolled', label: 'Enrolled Pupils', frozen: false, default: true },
            { key: 'meal_rate', label: 'Meal Participation', frozen: false, default: true },
            { key: 'allergies', label: 'Dietary Allergens', frozen: false, default: true },
            { key: 'status', label: 'Status', frozen: false, default: true },
            { key: 'desc', label: 'Curriculum Notes', frozen: false, default: true }
        ],
        rooms: [
            { key: 'select', label: 'Checkbox', frozen: true, default: true, alwaysVisible: true },
            { key: 'code', label: 'Room Code', frozen: true, default: true, alwaysVisible: true },
            { key: 'room_name', label: 'Room Title & Action', frozen: true, default: true, alwaysVisible: true },
            { key: 'building', label: 'Building Block', frozen: false, default: true },
            { key: 'assigned_class', label: 'Assigned Class', frozen: false, default: true },
            { key: 'teacher', label: 'Homeroom Lead', frozen: false, default: true },
            { key: 'capacity', label: 'Utilization Ratio', frozen: false, default: true },
            { key: 'drop', label: 'Meal Crate Station', frozen: false, default: true },
            { key: 'shift', label: 'Shift', frozen: false, default: true },
            { key: 'status', label: 'Status', frozen: false, default: true }
        ]
    };

    let activeTabColumns = loadAcademicColumnPreferences();

    function loadAcademicColumnPreferences() {
        const saved = localStorage.getItem('sme_classes_grades_cols_v1');
        if (saved) {
            try { return JSON.parse(saved); } catch (e) {}
        }
        const initial = {};
        Object.keys(TAB_SCHEMAS).forEach(tab => {
            initial[tab] = {};
            TAB_SCHEMAS[tab].forEach(col => {
                initial[tab][col.key] = col.default;
            });
        });
        return initial;
    }

    function saveAcademicColumnPreferences() {
        localStorage.setItem('sme_classes_grades_cols_v1', JSON.stringify(activeTabColumns));
        applyAcademicColumnStyles();
        renderAcademicColumnChecklist();
    }

    function applyAcademicColumnStyles() {
        const schema = TAB_SCHEMAS[currentTab] || TAB_SCHEMAS.classes;
        const currentTabPrefs = activeTabColumns[currentTab] || {};
        let css = '';
        let visibleCustomizable = 0;
        const customizableTotal = schema.filter(c => !c.frozen).length;

        schema.forEach(col => {
            const isVisible = col.alwaysVisible ? true : (currentTabPrefs[col.key] !== false);
            if (!isVisible) {
                css += `[data-col="${col.key}"] { display: none !important; }\n`;
            } else {
                if (!col.frozen) visibleCustomizable++;
            }
        });

        let styleEl = document.getElementById('academicDynamicColumnStyles');
        if (!styleEl) {
            styleEl = document.createElement('style');
            styleEl.id = 'academicDynamicColumnStyles';
            document.head.appendChild(styleEl);
        }
        styleEl.textContent = css;

        const badge = document.getElementById('visibleColumnsCountBadge');
        if (badge) {
            badge.textContent = `${visibleCustomizable}/${customizableTotal}`;
        }
    }

    function toggleColumnCustomizer(e) {
        e.stopPropagation();
        const pop = document.getElementById('columnCustomizerPopover');
        const btn = document.getElementById('btnCustomizeColumns');
        if (!pop) return;

        if (pop.classList.contains('show')) {
            pop.classList.remove('show');
            btn?.classList.remove('active');
        } else {
            renderAcademicColumnChecklist();
            pop.classList.add('show');
            btn?.classList.add('active');
        }
    }

    function renderAcademicColumnChecklist() {
        const container = document.getElementById('columnChecklistContainer');
        if (!container) return;
        const schema = TAB_SCHEMAS[currentTab] || TAB_SCHEMAS.classes;
        const currentTabPrefs = activeTabColumns[currentTab] || {};

        let html = '';
        schema.forEach(col => {
            if (col.frozen) return;
            const isChecked = currentTabPrefs[col.key] !== false;
            html += `
                <label class="col-check-item">
                    <span>${col.label}</span>
                    <input type="checkbox" ${isChecked ? 'checked' : ''} onchange="onAcademicColumnToggle('${col.key}', this.checked)">
                </label>
            `;
        });
        container.innerHTML = html;
    }

    function onAcademicColumnToggle(key, isChecked) {
        if (!activeTabColumns[currentTab]) activeTabColumns[currentTab] = {};
        activeTabColumns[currentTab][key] = isChecked;
        saveAcademicColumnPreferences();
    }

    function resetColumnsToDefault() {
        const schema = TAB_SCHEMAS[currentTab] || TAB_SCHEMAS.classes;
        if (!activeTabColumns[currentTab]) activeTabColumns[currentTab] = {};
        schema.forEach(col => {
            activeTabColumns[currentTab][col.key] = col.default;
        });
        saveAcademicColumnPreferences();
        if (window.Toast) {
            window.Toast.success('Column layout reset to standard view.');
        }
    }

    // Tab Switching (User Requested Tabs Concept)
    function switchAcademicTab(tabKey) {
        currentTab = tabKey;
        academicParams.tab = tabKey;

        // Update tab buttons active classes
        document.querySelectorAll('.tab-nav-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(`tabBtn_${tabKey}`)?.classList.add('active');

        // Adjust filter inputs relevance
        const gradeFilterWrap = document.getElementById('filterWrapGrade');
        const shiftFilterWrap = document.getElementById('filterWrapShift');
        const searchInput = document.getElementById('academicSearchInput');

        if (tabKey === 'grades') {
            if (gradeFilterWrap) gradeFilterWrap.style.display = 'none';
            if (shiftFilterWrap) shiftFilterWrap.style.display = 'none';
            if (searchInput) searchInput.placeholder = 'Search by grade code, name, stage...';
        } else {
            if (gradeFilterWrap) gradeFilterWrap.style.display = 'flex';
            if (shiftFilterWrap) shiftFilterWrap.style.display = 'flex';
            if (searchInput) searchInput.placeholder = 'Search by class code, teacher, room...';
        }

        // Render appropriate thead structure
        renderTableHeaderForTab(tabKey);

        // Fetch server data for this tab
        fetchAcademicServer();
    }

    function renderTableHeaderForTab(tab) {
        const thead = document.getElementById('academicTableHead');
        if (!thead) return;

        if (tab === 'grades') {
            thead.innerHTML = `
                <tr>
                    <th class="sticky-col-select" data-col="select"><input type="checkbox" onchange="toggleSelectAllRows(this)" style="cursor: pointer;"></th>
                    <th class="sticky-col-code" data-col="code">Grade Code</th>
                    <th class="sticky-col-student" data-col="grade_name">Grade Level & Actions</th>
                    <th data-col="stage">Academic Stage</th>
                    <th data-col="classes">Classes / Sections</th>
                    <th data-col="enrolled">Enrolled Pupils</th>
                    <th data-col="meal_rate">Meal Participation</th>
                    <th data-col="allergies">Dietary Allergens</th>
                    <th data-col="status">Status</th>
                    <th data-col="desc">Curriculum / Notes</th>
                </tr>
            `;
        } else if (tab === 'rooms') {
            thead.innerHTML = `
                <tr>
                    <th class="sticky-col-select" data-col="select"><input type="checkbox" onchange="toggleSelectAllRows(this)" style="cursor: pointer;"></th>
                    <th class="sticky-col-code" data-col="code">Room Code</th>
                    <th class="sticky-col-student" data-col="room_name">Room Location & Actions</th>
                    <th data-col="building">Building Block</th>
                    <th data-col="assigned_class">Assigned Class</th>
                    <th data-col="teacher">Homeroom Lead</th>
                    <th data-col="capacity">Utilization Ratio</th>
                    <th data-col="drop">Meal Crate Station</th>
                    <th data-col="shift">Shift</th>
                    <th data-col="status">Status</th>
                </tr>
            `;
        } else {
            thead.innerHTML = `
                <tr>
                    <th class="sticky-col-select" data-col="select"><input type="checkbox" onchange="toggleSelectAllRows(this)" style="cursor: pointer;"></th>
                    <th class="sticky-col-code" data-col="code">Class Code</th>
                    <th class="sticky-col-student" data-col="class_name">Classroom & Actions</th>
                    <th data-col="teacher">Homeroom Teacher</th>
                    <th data-col="grade">Grade Level</th>
                    <th data-col="room">Room Location</th>
                    <th data-col="shift">Shift</th>
                    <th data-col="enrolled">Enrolled Pupils</th>
                    <th data-col="meal">Meal Subscribed</th>
                    <th data-col="allergy">Allergies</th>
                    <th data-col="status">Status</th>
                    <th data-col="notes">Delivery Instructions</th>
                </tr>
            `;
        }
    }

    // Fast debounced search input (300ms)
    function onAcademicSearchInput(val) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            academicParams.search = val.trim();
            fetchAcademicServer();
        }, 300);
    }

    // Filter change handler
    function onAcademicFilterChange() {
        academicParams.grade = document.getElementById('academicGradeFilterSelect').value;
        academicParams.shift = document.getElementById('academicShiftFilterSelect').value;
        academicParams.status = document.getElementById('academicStatusFilterSelect').value;
        fetchAcademicServer();
    }

    function resetAcademicFilters() {
        document.getElementById('academicSearchInput').value = '';
        document.getElementById('academicGradeFilterSelect').value = 'all';
        document.getElementById('academicShiftFilterSelect').value = 'all';
        document.getElementById('academicStatusFilterSelect').value = 'all';

        academicParams.search = '';
        academicParams.grade = 'all';
        academicParams.shift = 'all';
        academicParams.status = 'all';

        fetchAcademicServer();
        if (window.Toast) {
            window.Toast.success('Filters reset to default view.');
        }
    }

    function changeAcademicPerPage(size) {
        // Can reload or apply client slice
    }

    // Central Server Fetcher via AJAX
    function fetchAcademicServer() {
        const overlay = document.getElementById('academicTableLoadingOverlay');
        if (overlay) overlay.classList.add('active');

        const q = new URLSearchParams();
        q.set('tab', academicParams.tab);
        if (academicParams.search) q.set('search', academicParams.search);
        if (academicParams.grade && academicParams.grade !== 'all') q.set('grade', academicParams.grade);
        if (academicParams.shift && academicParams.shift !== 'all') q.set('shift', academicParams.shift);
        if (academicParams.status && academicParams.status !== 'all') q.set('status', academicParams.status);

        const url = '/school/classes-grades?' + q.toString();

        if (window.history && window.history.pushState) {
            window.history.pushState(null, '', url);
        }

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (overlay) overlay.classList.remove('active');
            if (data.success) {
                const tbody = document.getElementById('academicTableBody');
                if (tbody && data.html) {
                    tbody.innerHTML = data.html;
                }
                const heading = document.getElementById('academicTableHeading');
                if (heading) {
                    const tabTitle = currentTab === 'grades' ? 'Grade Levels' : (currentTab === 'rooms' ? 'Campus Rooms & Logistics' : 'Classes & Sections');
                    heading.textContent = `${tabTitle} (${data.total || 0})`;
                }
                closeClassContextMenu();
                closeGradeContextMenu();
                applyAcademicColumnStyles();
            }
        })
        .catch(err => {
            if (overlay) overlay.classList.remove('active');
            console.error('Fetch error:', err);
            if (window.Toast) {
                window.Toast.error('Failed to load academic records.');
            }
        });
    }

    // Context Menu Handling for Classes
    let activeContextClass = null;

    function openClassContextMenu(e, id, code, nameEn, nameKm, gradeId, gradeName, room, roomKm, teacher, teacherKm, phone, capacity, shiftId, status, desc, descKm) {
        if (e && e.stopPropagation) e.stopPropagation();
        activeContextClass = { id, code, nameEn, nameKm, gradeId, gradeName, room, roomKm, teacher, teacherKm, phone, capacity, shiftId, status, desc, descKm };

        const btn = (e && e.currentTarget) ? e.currentTarget : (e && e.target ? e.target.closest('.btn-context-menu') : null);
        document.querySelectorAll('.btn-context-menu').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const pop = document.getElementById('classContextMenuPop');
        if (!pop) return;

        const titleEl = document.getElementById('classContextMenuTitle');
        if (titleEl) {
            titleEl.textContent = `${code} • ${nameEn}`;
        }

        pop.classList.add('show');

        if (btn) {
            const rect = btn.getBoundingClientRect();
            const popWidth = 220;
            const popHeight = 260;

            let top = rect.bottom + 6;
            let left = rect.left;

            if (top + popHeight > window.innerHeight - 10) top = rect.top - popHeight - 6;
            if (left + popWidth > window.innerWidth - 10) left = window.innerWidth - popWidth - 10;

            pop.style.top = `${Math.max(10, top)}px`;
            pop.style.left = `${Math.max(10, left)}px`;
        }
    }

    function closeClassContextMenu() {
        const pop = document.getElementById('classContextMenuPop');
        if (pop) pop.classList.remove('show');
        document.querySelectorAll('.btn-context-menu').forEach(b => b.classList.remove('active'));
    }

    function execClassContextMenuAction(action) {
        if (!activeContextClass) return;
        const c = activeContextClass;
        closeClassContextMenu();

        if (action === 'view') {
            viewClassDetails(c.id);
        } else if (action === 'edit') {
            openEditClassModal(c);
        } else if (action === 'view_students') {
            window.location.href = `/school/students?class=${encodeURIComponent(c.code)}`;
        } else if (action === 'copy_phone') {
            if (c.phone && c.phone !== '-') {
                navigator.clipboard.writeText(c.phone).then(() => {
                    if (window.Toast) window.Toast.success(`Copied phone: ${c.phone}`);
                }).catch(() => {
                    if (window.Toast) window.Toast.info(`Teacher phone: ${c.phone}`);
                });
            } else {
                if (window.Toast) window.Toast.warning('No phone number recorded.');
            }
        } else if (action === 'call_teacher') {
            if (c.phone && c.phone !== '-') {
                window.location.href = `tel:${c.phone.replace(/[^0-9+]/g, '')}`;
            } else {
                if (window.Toast) window.Toast.warning('No phone number available.');
            }
        } else if (action === 'print_crate') {
            if (window.Toast) {
                window.Toast.success(`Classroom meal crate label ready for ${c.code} (${c.room})`);
            }
        } else if (action === 'archive') {
            archiveClass(c.id, c.code);
        }
    }

    // Context Menu Handling for Grades
    let activeContextGrade = null;

    function openGradeContextMenu(e, id, code, nameEn, nameKm, level, stage, stageKm, status, desc, descKm) {
        if (e && e.stopPropagation) e.stopPropagation();
        activeContextGrade = { id, code, nameEn, nameKm, level, stage, stageKm, status, desc, descKm };

        const btn = (e && e.currentTarget) ? e.currentTarget : (e && e.target ? e.target.closest('.btn-context-menu') : null);
        document.querySelectorAll('.btn-context-menu').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const pop = document.getElementById('gradeContextMenuPop');
        if (!pop) return;

        const titleEl = document.getElementById('gradeContextMenuTitle');
        if (titleEl) {
            titleEl.textContent = `${code} • ${nameEn}`;
        }

        pop.classList.add('show');

        if (btn) {
            const rect = btn.getBoundingClientRect();
            const popWidth = 220;
            const popHeight = 160;

            let top = rect.bottom + 6;
            let left = rect.left;

            if (top + popHeight > window.innerHeight - 10) top = rect.top - popHeight - 6;
            if (left + popWidth > window.innerWidth - 10) left = window.innerWidth - popWidth - 10;

            pop.style.top = `${Math.max(10, top)}px`;
            pop.style.left = `${Math.max(10, left)}px`;
        }
    }

    function closeGradeContextMenu() {
        const pop = document.getElementById('gradeContextMenuPop');
        if (pop) pop.classList.remove('show');
        document.querySelectorAll('.btn-context-menu').forEach(b => b.classList.remove('active'));
    }

    function execGradeContextMenuAction(action) {
        if (!activeContextGrade) return;
        const g = activeContextGrade;
        closeGradeContextMenu();

        if (action === 'view_classes') {
            switchAcademicTab('classes');
            document.getElementById('academicGradeFilterSelect').value = g.id;
            onAcademicFilterChange();
        } else if (action === 'edit') {
            openEditGradeModal(g);
        } else if (action === 'archive') {
            archiveGrade(g.id, g.nameEn);
        }
    }

    // Header Content Menu with ("...") Handlers
    function toggleHeaderActionsMenu(event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        const pop = document.getElementById('headerActionsMenuPop');
        const btn = document.getElementById('headerActionsMenuBtn');
        if (!pop) return;

        const isOpen = pop.classList.contains('show');
        closeClassContextMenu();
        closeGradeContextMenu();

        if (isOpen) {
            pop.classList.remove('show');
            if (btn) btn.classList.remove('active');
        } else {
            pop.classList.add('show');
            if (btn) btn.classList.add('active');
        }
    }

    function closeHeaderActionsMenu() {
        const pop = document.getElementById('headerActionsMenuPop');
        const btn = document.getElementById('headerActionsMenuBtn');
        if (pop) pop.classList.remove('show');
        if (btn) btn.classList.remove('active');
    }

    // Dismiss popovers on outside click or scroll
    document.addEventListener('click', function (e) {
        const headerPop = document.getElementById('headerActionsMenuPop');
        if (headerPop && headerPop.classList.contains('show')) {
            if (!headerPop.contains(e.target) && !e.target.closest('#headerActionsMenuBtn')) {
                closeHeaderActionsMenu();
            }
        }

        const classPop = document.getElementById('classContextMenuPop');
        if (classPop && classPop.classList.contains('show')) {
            if (!classPop.contains(e.target) && !e.target.closest('.btn-context-menu')) {
                closeClassContextMenu();
            }
        }

        const gradePop = document.getElementById('gradeContextMenuPop');
        if (gradePop && gradePop.classList.contains('show')) {
            if (!gradePop.contains(e.target) && !e.target.closest('.btn-context-menu')) {
                closeGradeContextMenu();
            }
        }

        const colPop = document.getElementById('columnCustomizerPopover');
        if (colPop && colPop.classList.contains('show')) {
            if (!colPop.contains(e.target) && !e.target.closest('#btnCustomizeColumns')) {
                colPop.classList.remove('show');
                document.getElementById('btnCustomizeColumns')?.classList.remove('active');
            }
        }
    });

    window.addEventListener('scroll', function () {
        closeHeaderActionsMenu();
        closeClassContextMenu();
        closeGradeContextMenu();
    }, true);

    // Multilingual Modal Language Switchers
    function switchClassModalLang(lang) {
        document.getElementById('classLangBtn_en')?.classList.toggle('active', lang === 'en');
        document.getElementById('classLangBtn_km')?.classList.toggle('active', lang === 'km');
        document.getElementById('classLangSection_en').style.display = lang === 'en' ? 'block' : 'none';
        document.getElementById('classLangSection_km').style.display = lang === 'km' ? 'block' : 'none';
    }

    function switchGradeModalLang(lang) {
        document.getElementById('gradeLangBtn_en')?.classList.toggle('active', lang === 'en');
        document.getElementById('gradeLangBtn_km')?.classList.toggle('active', lang === 'km');
        document.getElementById('gradeLangSection_en').style.display = lang === 'en' ? 'block' : 'none';
        document.getElementById('gradeLangSection_km').style.display = lang === 'km' ? 'block' : 'none';
    }

    // Modal Control Handlers
    function openAddClassModal() {
        document.getElementById('classFormId').value = '';
        document.getElementById('classModalTitle').textContent = '+ Add Classroom Section';
        document.getElementById('classForm').reset();
        document.getElementById('classInputCode').readOnly = false;
        switchClassModalLang('en');
        document.getElementById('classModal').classList.add('show');
    }

    function openEditClassModal(c) {
        document.getElementById('classFormId').value = c.id;
        document.getElementById('classModalTitle').textContent = `Edit Class ${c.code}`;
        document.getElementById('classInputCode').value = c.code;
        document.getElementById('classInputCode').readOnly = true;
        document.getElementById('classInputName').value = c.nameEn;
        document.getElementById('classInputNameKm').value = c.nameKm || '';
        document.getElementById('classInputRoom').value = c.room || '';
        document.getElementById('classInputRoomKm').value = c.roomKm || '';
        document.getElementById('classInputTeacher').value = c.teacher || '';
        document.getElementById('classInputTeacherKm').value = c.teacherKm || '';
        document.getElementById('classInputPhone').value = c.phone || '';
        document.getElementById('classInputCapacity').value = c.capacity || 40;
        document.getElementById('classInputGradeId').value = c.gradeId || '';
        document.getElementById('classInputShiftId').value = c.shiftId || '';
        document.getElementById('classInputStatus').value = (c.status || 'active').toLowerCase();
        document.getElementById('classInputDesc').value = c.desc || '';
        document.getElementById('classInputDescKm').value = c.descKm || '';
        switchClassModalLang('en');
        document.getElementById('classModal').classList.add('show');
    }

    function closeClassModal() {
        document.getElementById('classModal').classList.remove('show');
    }

    function handleClassFormSubmit(event) {
        event.preventDefault();
        const form = document.getElementById('classForm');
        const id = document.getElementById('classFormId').value;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const url = id ? `/school/classes/${id}` : '/school/classes';
        const method = id ? 'PUT' : 'POST';

        const submitBtn = document.getElementById('classSubmitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
        }

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Classroom';
            }
            if (result.success) {
                closeClassModal();
                if (window.Toast) {
                    window.Toast.success(result.message || 'Class saved successfully!');
                }
                fetchAcademicServer();
            } else {
                if (window.Toast) {
                    window.Toast.error(result.message || 'Validation error saving class.');
                }
            }
        })
        .catch(err => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Classroom';
            }
            console.error('Error saving class:', err);
            if (window.Toast) {
                window.Toast.error('An error occurred while saving class.');
            }
        });
    }

    function archiveClass(id, code) {
        if (!confirm(`Archive class ${code}?`)) return;

        fetch(`/school/classes/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                if (window.Toast) window.Toast.success(result.message || `Class ${code} archived.`);
                fetchAcademicServer();
            } else {
                if (window.Toast) window.Toast.error(result.message || 'Failed to archive class.');
            }
        })
        .catch(err => {
            if (window.Toast) window.Toast.error('Failed to archive class.');
        });
    }

    // Grade Modal Handlers
    function openAddGradeModal() {
        document.getElementById('gradeFormId').value = '';
        document.getElementById('gradeModalTitle').textContent = '+ Add Grade Level';
        document.getElementById('gradeForm').reset();
        document.getElementById('gradeInputCode').readOnly = false;
        switchGradeModalLang('en');
        document.getElementById('gradeModal').classList.add('show');
    }

    function openEditGradeModal(g) {
        document.getElementById('gradeFormId').value = g.id;
        document.getElementById('gradeModalTitle').textContent = `Edit Grade ${g.code}`;
        document.getElementById('gradeInputCode').value = g.code;
        document.getElementById('gradeInputCode').readOnly = true;
        document.getElementById('gradeInputName').value = g.nameEn;
        document.getElementById('gradeInputNameKm').value = g.nameKm || '';
        document.getElementById('gradeInputLevel').value = g.level || 1;
        document.getElementById('gradeInputStage').value = g.stage || '';
        document.getElementById('gradeInputStageKm').value = g.stageKm || '';
        document.getElementById('gradeInputDesc').value = g.desc || '';
        document.getElementById('gradeInputDescKm').value = g.descKm || '';
        document.getElementById('gradeInputStatus').value = (g.status || 'active').toLowerCase();
        switchGradeModalLang('en');
        document.getElementById('gradeModal').classList.add('show');
    }

    function closeGradeModal() {
        document.getElementById('gradeModal').classList.remove('show');
    }

    function handleGradeFormSubmit(event) {
        event.preventDefault();
        const form = document.getElementById('gradeForm');
        const id = document.getElementById('gradeFormId').value;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const url = id ? `/school/grades/${id}` : '/school/grades';
        const method = id ? 'PUT' : 'POST';

        const submitBtn = document.getElementById('gradeSubmitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
        }

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Grade Level';
            }
            if (result.success) {
                closeGradeModal();
                if (window.Toast) {
                    window.Toast.success(result.message || 'Grade level saved successfully!');
                }
                fetchAcademicServer();
            } else {
                if (window.Toast) {
                    window.Toast.error(result.message || 'Validation error saving grade.');
                }
            }
        })
        .catch(err => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Grade Level';
            }
            console.error('Error saving grade:', err);
            if (window.Toast) {
                window.Toast.error('An error occurred while saving grade level.');
            }
        });
    }

    function archiveGrade(id, name) {
        if (!confirm(`Archive grade level ${name}?`)) return;

        fetch(`/school/grades/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                if (window.Toast) window.Toast.success(result.message || `Grade ${name} archived.`);
                fetchAcademicServer();
            } else {
                if (window.Toast) window.Toast.error(result.message || 'Failed to archive grade.');
            }
        })
        .catch(err => {
            if (window.Toast) window.Toast.error('Failed to archive grade.');
        });
    }

    function viewClassDetails(id) {
        if (window.Toast) {
            window.Toast.info('Viewing details for classroom record.');
        }
    }

    function viewGradeDetails(id) {
        if (window.Toast) {
            window.Toast.info('Viewing details for grade level record.');
        }
    }

    function openSwitchSchoolModal() {
        document.getElementById('switchSchoolModal').classList.add('show');
    }
    function closeSwitchSchoolModal() {
        document.getElementById('switchSchoolModal').classList.remove('show');
    }

    function openDateModal() {
        if (window.Toast) {
            window.Toast.info('Viewing academic schedule for: {{ $dateLabel ?? now()->format("D, M j, Y") }}');
        }
    }

    function toggleSelectAllRows(source) {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = source.checked;
        });
    }

    function exportAcademicCsv() {
        if (window.Toast) {
            window.Toast.success(`Academic roster exported for active tab: ${currentTab.toUpperCase()}`);
        }
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function () {
        applyAcademicColumnStyles();
    });
</script>
@endsection
