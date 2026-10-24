@extends('layouts.master_school_admin')

@section('title', 'Students | ' . ($activeSchool->name ?? 'Wat Bo Primary School'))
@section('header_title', '')

@section('content')
<style>
    /* Design Tokens matching Image 2 */
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

    /* Page Header Bar */
    .school-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 0.25rem;
    }

    .school-page-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-slate-900);
        letter-spacing: -0.025em;
        line-height: 1.15;
    }

    .school-page-subtitle {
        font-size: 0.875rem;
        color: var(--text-slate-500);
        margin-top: 0.25rem;
    }

    .header-controls {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: nowrap;
        overflow-x: auto;
        max-width: 100%;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        padding-bottom: 2px;
    }

    .header-controls::-webkit-scrollbar {
        display: none;
    }

    .header-controls .pill-btn,
    .header-controls .btn-add-primary {
        flex-shrink: 0;
        white-space: nowrap;
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

        .student-stat-card {
            flex: 0 0 280px;
            min-width: 280px;
            max-width: 340px;
        }
    }

    .student-stat-card {
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

    .stat-icon-blue {
        background: #0066FF;
        color: #FFFFFF;
    }

    .stat-icon-green {
        background: #10B981;
        color: #FFFFFF;
    }

    .stat-icon-orange {
        background: #F97316;
        color: #FFFFFF;
    }

    .stat-icon-red {
        background: #EF4444;
        color: #FFFFFF;
    }

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
    }

    .trend-up {
        color: #10B981;
    }

    .trend-neutral {
        color: #64748B;
    }

    /* Watermark background illustrations */
    .stat-watermark {
        position: absolute;
        right: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.12;
        pointer-events: none;
        z-index: 1;
    }

    /* Circular Progress Ring for 93% active */
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
        stroke-dashoffset: 9.6; /* 93% */
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

    /* Filter Bar (Image 2 style) */
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

    /* Table Container */
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

    .students-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.825rem;
        text-align: left;
        white-space: nowrap;
    }

    .students-table th {
        background: #F8FAFC;
        padding: 0.8rem 1rem;
        font-size: 0.725rem;
        font-weight: 700;
        color: #64748B;
        text-transform: capitalize;
        border-bottom: 1px solid #E2E8F0;
        white-space: nowrap;
    }

    .students-table td {
        padding: 0.8rem 1rem;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
        color: #334155;
        background-color: #FFFFFF;
    }

    .students-table tr:hover td {
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
    .students-table th.sticky-col-select {
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
    .students-table th.sticky-col-code {
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
    .students-table th.sticky-col-student {
        background-color: #F8FAFC;
        z-index: 12;
        border-right: 2px solid #CBD5E1 !important;
        box-shadow: 4px 0 10px -2px rgba(15, 23, 42, 0.08);
    }

    .students-table tr:hover td.sticky-col-select,
    .students-table tr:hover td.sticky-col-code,
    .students-table tr:hover td.sticky-col-student {
        background-color: #F8FAFC !important;
    }

    .student-cell-layout {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        width: 100%;
    }

    .student-cell-main {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
        flex: 1;
    }

    .student-cell-meta {
        min-width: 0;
    }

    /* Action button as Context Menu (Meta Content List Concept matching user screenshot red box) */
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

    /* Column Specific Styles */
    .id-code-badge {
        color: #0066FF;
        font-weight: 700;
        font-family: inherit;
        text-decoration: none;
    }

    .id-code-badge:hover {
        text-decoration: underline;
    }

    .student-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 170px;
    }

    .student-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #E2E8F0;
        flex-shrink: 0;
    }

    .student-name-en {
        font-size: 0.85rem;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.25;
    }

    .student-name-km {
        font-size: 0.75rem;
        color: #64748B;
        font-family: var(--font-khmer), 'Kantumruy Pro', sans-serif;
    }

    /* Gender Pill */
    .gender-pill {
        display: inline-block;
        padding: 0.2rem 0.65rem;
        border-radius: 999px;
        font-size: 0.725rem;
        font-weight: 600;
        text-align: center;
    }

    .gender-male {
        background: #E0F2FE;
        color: #0284C7;
    }

    .gender-female {
        background: #FCE7F3;
        color: #DB2777;
    }

    /* Parent Guardian Cell */
    .parent-meta {
        line-height: 1.3;
        min-width: 150px;
    }

    .parent-name {
        font-weight: 700;
        color: #0F172A;
        font-size: 0.825rem;
    }

    .parent-phone {
        font-size: 0.725rem;
        color: #64748B;
    }

    /* Meal Plan Pill */
    .meal-pill {
        display: inline-block;
        padding: 0.22rem 0.65rem;
        border-radius: 999px;
        font-size: 0.725rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .meal-regular {
        background: #E0F2FE;
        color: #0369A1;
    }

    .meal-halal {
        background: #DCFCE7;
        color: #15803D;
    }

    .meal-vegetarian {
        background: #F3E8FF;
        color: #7E22CE;
    }

    /* Allergy Badge */
    .allergy-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        font-size: 0.725rem;
        font-weight: 700;
        background: #FEE2E2;
        color: #DC2626;
        border: 1px solid #FECACA;
    }

    /* Status Pill with Dot */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        font-size: 0.725rem;
        font-weight: 700;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-active {
        background: #DCFCE7;
        color: #16A34A;
    }
    .status-active .status-dot {
        background: #16A34A;
    }

    .status-inactive {
        background: #FEE2E2;
        color: #DC2626;
    }
    .status-inactive .status-dot {
        background: #DC2626;
    }

    .status-alert {
        background: #FEF3C7;
        color: #D97706;
    }
    .status-alert .status-dot {
        background: #D97706;
    }

    /* Action Icon Buttons */
    .action-icons-wrap {
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .action-icon-btn {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        background: transparent;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #64748B;
        cursor: pointer;
        transition: all 0.15s;
    }

    .action-icon-btn:hover {
        background: #E2E8F0;
        color: #0F172A;
    }

    /* Pagination Bar (Image 2 style) */
    .table-pagination-bar {
        padding: 1rem 1.35rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        border-top: 1px solid #F1F5F9;
        font-size: 0.8rem;
        color: #64748B;
    }

    .pagination-pages {
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .page-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
    }

    .page-box:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
        color: #0F172A;
    }

    .page-box.active {
        background: #0066FF;
        color: #FFFFFF;
        border-color: #0066FF;
    }

    /* Sortable Headers & Loading Overlay */
    .students-table th.sortable-th {
        cursor: pointer;
        user-select: none;
        transition: color 0.15s ease, background-color 0.15s ease;
    }

    .students-table th.sortable-th:hover {
        background-color: #F1F5F9;
        color: #0066FF;
    }

    .sort-icon {
        display: inline-flex;
        align-items: center;
        margin-left: 0.35rem;
        opacity: 0.45;
        transition: opacity 0.15s ease, transform 0.15s ease;
        vertical-align: middle;
    }

    .sortable-th.active-sort .sort-icon {
        opacity: 1;
        color: #0066FF;
        font-weight: 800;
    }

    .table-container-relative {
        position: relative;
    }

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
        transition: opacity 0.2s ease;
    }

    .table-loading-overlay.active {
        display: flex;
    }

    .spinner-loader {
        width: 38px;
        height: 38px;
        border: 3px solid #E2E8F0;
        border-top-color: #0066FF;
        border-radius: 50%;
        animation: spinLoader 0.7s linear infinite;
    }

    @keyframes spinLoader {
        to { transform: rotate(360deg); }
    }

    /* Modal Styling */
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

    .custom-modal-backdrop.show {
        display: flex;
    }

    .custom-modal-card {
        background: #FFFFFF;
        border-radius: 20px;
        width: 100%;
        max-width: 620px;
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

    .modal-head-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0F172A;
    }

    .modal-close-btn {
        background: transparent;
        border: none;
        color: #94A3B8;
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.25rem;
    }

    .modal-close-btn:hover {
        color: #0F172A;
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
</style>

<div class="school-page-container">
    <!-- Header Row (Image 2) -->
    <div class="school-page-header">
        <div>
            <h1 class="school-page-title">{{ transLabel('menu.students', 'Students') }}</h1>
            <p class="school-page-subtitle">{{ transLabel('school.manage_students_desc', 'Manage students enrolled in your school.') }}</p>
        </div>

        <div class="header-controls">
            <!-- Date Filter Pill -->
            <button type="button" class="pill-btn" onclick="openDateModal()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>{{ $dateLabel ?? 'Mon, Oct 16, 2026' }}</span>
            </button>

            <!-- School Context Pill -->
            <button type="button" class="pill-btn" onclick="openSwitchSchoolModal()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m4 6 8-4 8 4"/><path d="M18 10 22 12v10H2V12l4-2"/><path d="M6 5v17"/><path d="M18 5v17"/></svg>
                <span>{{ $activeSchool->name ?? 'Wat Bo Primary School' }}</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </button>

            <!-- Export Button -->
            <button type="button" class="pill-btn" onclick="exportStudentsCsv()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>{{ transLabel('common.export', 'Export') }}</span>
            </button>

            <!-- Add Student Primary Button -->
            <button type="button" class="btn-add-primary" onclick="openAddStudentModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>{{ transLabel('school.add_student', 'Add Student') }}</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Stat Cards Row (Scrollable when overflow or responsive) -->
    <div class="stat-cards-scroll-wrapper">
        <div class="stat-cards-grid">
            <!-- 1. Total Students -->
            <div class="student-stat-card">
                <div class="stat-card-left">
                    <div class="stat-icon-box stat-icon-blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <div class="stat-card-label" style="color: #0066FF;">{{ transLabel('dashboard.total_students', 'Total Students') }}</div>
                        <div class="stat-card-value">{{ $stats['total_students'] ?? '1,050' }}</div>
                        <div class="stat-card-trend trend-up">
                            <span>{{ $stats['total_students_change'] ?? '↑ 2% vs last month' }}</span>
                        </div>
                    </div>
                </div>
                <!-- Ghost Watermark -->
                <div class="stat-watermark">
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="#0066FF" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>

            <!-- 2. Active Students (with 93% circular ring) -->
            <div class="student-stat-card">
                <div class="stat-card-left">
                    <div class="stat-icon-box stat-icon-green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"/><path d="M4 6v12c0 1.1.9 2 2 2h14v-4"/><path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"/></svg>
                    </div>
                    <div>
                        <div class="stat-card-label" style="color: #10B981;">{{ transLabel('school.active_students', 'Active Students') }}</div>
                        <div class="stat-card-value">{{ $stats['active_students'] ?? '980' }}</div>
                        <div class="stat-card-trend trend-up">
                            <span>{{ $stats['active_students_change'] ?? '↑ 5% vs last month' }}</span>
                        </div>
                    </div>
                </div>
                <!-- Circular Gauge -->
                <div class="gauge-wrapper">
                    <svg width="52" height="52" viewBox="0 0 52 52">
                        <circle class="gauge-circle-bg" cx="26" cy="26" r="22"/>
                        <circle class="gauge-circle-progress" cx="26" cy="26" r="22"/>
                    </svg>
                    <div class="gauge-text">{{ $stats['active_students_pct'] ?? '93' }}%</div>
                </div>
            </div>

            <!-- 3. Meal Eligible -->
            <div class="student-stat-card">
                <div class="stat-card-left">
                    <div class="stat-icon-box stat-icon-orange">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 2v6a3 3 0 0 1-3 3 3 3 0 0 1-3-3V2"/><path d="M15 2v19"/><path d="M6 2v19"/><path d="M9 2a3 3 0 0 1-3 3 3 3 0 0 1-3-3"/></svg>
                    </div>
                    <div>
                        <div class="stat-card-label" style="color: #F97316;">{{ transLabel('school.meal_eligible', 'Meal Eligible') }}</div>
                        <div class="stat-card-value">{{ $stats['meal_eligible'] ?? '820' }}</div>
                        <div class="stat-card-trend trend-up">
                            <span>{{ $stats['meal_eligible_change'] ?? '↑ 8% vs last month' }}</span>
                        </div>
                    </div>
                </div>
                <!-- Ghost Watermark -->
                <div class="stat-watermark">
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="#F97316" stroke-width="2"><path d="M18 2v6a3 3 0 0 1-3 3 3 3 0 0 1-3-3V2"/><path d="M15 2v19"/><path d="M6 2v19"/></svg>
                </div>
            </div>

            <!-- 4. Allergy Alerts -->
            <div class="student-stat-card">
                <div class="stat-card-left">
                    <div class="stat-icon-box stat-icon-red">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div>
                        <div class="stat-card-label" style="color: #EF4444;">{{ transLabel('school.allergy_alerts', 'Allergy Alerts') }}</div>
                        <div class="stat-card-value">{{ $stats['allergy_alerts'] ?? '42' }}</div>
                        <div class="stat-card-trend trend-up">
                            <span>{{ $stats['allergy_alerts_change'] ?? '↑ 1% vs last month' }}</span>
                        </div>
                    </div>
                </div>
                <!-- Ghost Watermark -->
                <div class="stat-watermark">
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar (Image 2 style) -->
    <div class="filter-bar-card">
        <!-- Search Student -->
        <div class="filter-item filter-item-search">
            <span class="filter-label">{{ transLabel('school.search_student', 'Search Student') }}</span>
            <div class="search-input-wrapper">
                <svg class="search-icon-left" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="studentSearchInput" value="{{ request('search') }}" placeholder="{{ transLabel('school.search_placeholder', 'Search by name, student ID...') }}" oninput="onSearchInput(this.value)">
            </div>
        </div>

        <!-- Grade Filter -->
        <div class="filter-item">
            <span class="filter-label">{{ transLabel('school.grade', 'Grade') }}</span>
            <select class="filter-select" id="gradeFilterSelect" onchange="onFilterChange()">
                <option value="all">{{ transLabel('school.all_grades', 'All Grades') }}</option>
                @foreach($gradesList ?? [] as $gr)
                    <option value="{{ $gr }}" {{ request('grade') === $gr ? 'selected' : '' }}>{{ $gr }}</option>
                @endforeach
            </select>
        </div>

        <!-- Class Filter -->
        <div class="filter-item">
            <span class="filter-label">{{ transLabel('school.class', 'Class') }}</span>
            <select class="filter-select" id="classFilterSelect" onchange="onFilterChange()">
                <option value="all">{{ transLabel('school.all_classes', 'All Classes') }}</option>
                @foreach($classesList ?? [] as $cl)
                    <option value="{{ $cl }}" {{ request('class') === $cl ? 'selected' : '' }}>{{ $cl }}</option>
                @endforeach
            </select>
        </div>

        <!-- Meal Status Filter -->
        <div class="filter-item">
            <span class="filter-label">{{ transLabel('school.meal_status', 'Meal Status') }}</span>
            <select class="filter-select" id="mealFilterSelect" onchange="onFilterChange()">
                <option value="all">{{ transLabel('common.all', 'All') }}</option>
                @foreach($mealPlansList ?? [] as $mp)
                    <option value="{{ $mp }}" {{ request('meal') === $mp ? 'selected' : '' }}>{{ $mp }}</option>
                @endforeach
            </select>
        </div>

        <!-- Allergy Filter -->
        <div class="filter-item">
            <span class="filter-label">{{ transLabel('school.allergy', 'Allergy') }}</span>
            <select class="filter-select" id="allergyFilterSelect" onchange="onFilterChange()">
                <option value="all">{{ transLabel('common.all', 'All') }}</option>
                <option value="none" {{ request('allergy') === 'none' ? 'selected' : '' }}>No Allergy (-)</option>
                @foreach($allergiesList ?? [] as $alg)
                    <option value="{{ $alg }}" {{ request('allergy') === $alg ? 'selected' : '' }}>{{ $alg }}</option>
                @endforeach
            </select>
        </div>

        <!-- Account Status Filter -->
        <div class="filter-item">
            <span class="filter-label">{{ transLabel('school.account_status', 'Account Status') }}</span>
            <select class="filter-select" id="statusFilterSelect" onchange="onFilterChange()">
                <option value="all">{{ transLabel('common.all', 'All') }}</option>
                <option value="Active" {{ strtolower(request('status')) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ strtolower(request('status')) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="Allergy Alert" {{ strtolower(request('status')) === 'allergy alert' ? 'selected' : '' }}>Allergy Alert</option>
            </select>
        </div>

        <!-- Reset Button -->
        <button type="button" class="btn-reset-filter" onclick="resetStudentFilters()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            <span>{{ transLabel('school.reset_filters', 'Reset Filters') }}</span>
        </button>
    </div>

    <!-- Table Card (Students 1,050) -->
    <div class="table-card">
        <div class="table-topbar">
            <div class="table-heading" id="tableRecordCountHeading">{{ transLabel('menu.students', 'Students') }} ({{ number_format($totalCount ?? count($students)) }})</div>
            
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <!-- Column Customizer Dropdown (Meta Content List Concept) -->
                <div style="position: relative;">
                    <button type="button" class="column-customizer-btn" id="btnCustomizeColumns" onclick="toggleColumnCustomizer(event)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7m0-18H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7m0-18v18"/></svg>
                        <span>{{ transLabel('common.columns', 'Columns') }}</span>
                        <span class="columns-count-badge" id="visibleColumnsCountBadge">8/12</span>
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="column-customizer-popover" id="columnCustomizerPopover">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid #F1F5F9; margin-bottom: 0.5rem;">
                            <div style="font-weight: 800; font-size: 0.825rem; color: #0F172A;">Customize Columns</div>
                            <button type="button" onclick="resetColumnsToDefault()" style="background: none; border: none; font-size: 0.725rem; color: #0066FF; font-weight: 700; cursor: pointer;">Reset</button>
                        </div>
                        <div style="font-size: 0.725rem; color: #64748B; margin-bottom: 0.5rem;">Select columns to display in this list:</div>
                        <div id="columnChecklistContainer" style="max-height: 280px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.2rem;">
                            <!-- Dynamically generated via JS -->
                        </div>
                    </div>
                </div>

                <!-- Show Entries Select -->
                <div class="show-entries-box">
                    <span>Show</span>
                    <select class="show-entries-select" id="showEntriesSelect" onchange="changePerPage(this.value)">
                        <option value="10" {{ ($paginator->perPage() ?? 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ ($paginator->perPage() ?? 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ ($paginator->perPage() ?? 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ ($paginator->perPage() ?? 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span>entries</span>
                </div>
            </div>
        </div>

        <div class="table-responsive table-container-relative">
            <!-- Loading Overlay during Server Fetch -->
            <div class="table-loading-overlay" id="tableLoadingOverlay">
                <div class="spinner-loader"></div>
            </div>

            <table class="students-table" id="studentsTable">
                <thead>
                    <tr>
                        {{-- 1. Pinned: Checkbox --}}
                        <th class="sticky-col-select" data-col="select">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAllStudents(this)" style="cursor: pointer;">
                        </th>

                        {{-- 2. Pinned: Student ID --}}
                        <th class="sticky-col-code sortable-th" data-col="code" onclick="handleSortStudents('student_code')">
                            <span>{{ transLabel('school.student_id', 'Student ID') }}</span>
                            <span class="sort-icon" id="sortIcon_student_code">↕</span>
                        </th>

                        {{-- 3. Pinned: Student Name & Context Menu --}}
                        <th class="sticky-col-student sortable-th" data-col="student" onclick="handleSortStudents('full_name')">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span>{{ transLabel('school.student', 'Student') }}</span>
                                <span class="sort-icon" id="sortIcon_full_name">↕</span>
                            </div>
                        </th>

                        {{-- 4. Scrollable: Gender --}}
                        <th class="sortable-th" data-col="gender" onclick="handleSortStudents('gender')">
                            <span>{{ transLabel('school.gender', 'Gender') }}</span>
                            <span class="sort-icon" id="sortIcon_gender">↕</span>
                        </th>

                        {{-- 5. Scrollable: Grade --}}
                        <th class="sortable-th" data-col="grade" onclick="handleSortStudents('grade_level')">
                            <span>{{ transLabel('school.grade', 'Grade') }}</span>
                            <span class="sort-icon" id="sortIcon_grade_level">↕</span>
                        </th>

                        {{-- 6. Scrollable: Class --}}
                        <th class="sortable-th" data-col="class" onclick="handleSortStudents('class_name')">
                            <span>{{ transLabel('school.class', 'Class') }}</span>
                            <span class="sort-icon" id="sortIcon_class_name">↕</span>
                        </th>

                        {{-- 7. Scrollable: Parent / Guardian --}}
                        <th data-col="parent">{{ transLabel('school.parent_guardian', 'Parent / Guardian') }}</th>

                        {{-- 8. Scrollable: Parent Phone (Extra Column) --}}
                        <th data-col="phone">Parent Phone</th>

                        {{-- 9. Scrollable: Parent Email (Extra Column) --}}
                        <th data-col="email">Parent Email</th>

                        {{-- 10. Scrollable: Meal Plan --}}
                        <th data-col="meal">{{ transLabel('school.meal_plan', 'Meal Plan') }}</th>

                        {{-- 11. Scrollable: Allergy --}}
                        <th data-col="allergy">{{ transLabel('school.allergy', 'Allergy') }}</th>

                        {{-- 12. Scrollable: School Shift (Extra Column) --}}
                        <th data-col="shift">School Shift</th>

                        {{-- 13. Scrollable: DOB & Age (Extra Column) --}}
                        <th data-col="dob">DOB & Age</th>

                        {{-- 14. Scrollable: Status --}}
                        <th class="sortable-th" data-col="status" onclick="handleSortStudents('status')">
                            <span>{{ transLabel('school.status', 'Status') }}</span>
                            <span class="sort-icon" id="sortIcon_status">↕</span>
                        </th>

                        {{-- 15. Scrollable: Enrolled Date (Extra Column) --}}
                        <th data-col="enrolled">Enrolled Date</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    @include('school.partials.student_rows')
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar (Image 2 style with Server-Side AJAX Partial) -->
        <div class="table-pagination-bar" id="studentsPaginationContainer">
            @include('school.partials.student_pagination')
        </div>
    </div>
</div>

<!-- Floating Context Menu for Student Action (Meta Content List Concept) -->
<div class="floating-context-menu" id="studentContextMenuPop">
    <div style="padding: 0.5rem 0.95rem; font-size: 0.725rem; font-weight: 700; color: #64748B; border-bottom: 1px solid #F1F5F9; margin-bottom: 0.25rem;" id="contextMenuTitle">
        Student Options
    </div>
    <button type="button" class="context-menu-item" onclick="execContextMenuAction('view')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
        <span>View Full Profile</span>
    </button>
    <button type="button" class="context-menu-item" onclick="execContextMenuAction('edit')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
        <span>Edit Student Info</span>
    </button>
    <button type="button" class="context-menu-item" onclick="execContextMenuAction('copy_phone')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
        <span>Copy Parent Phone</span>
    </button>
    <button type="button" class="context-menu-item" onclick="execContextMenuAction('call_parent')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <span>Call Parent Guardian</span>
    </button>
    <button type="button" class="context-menu-item" onclick="execContextMenuAction('qr_badge')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
        <span>Student QR & Meal Badge</span>
    </button>
    <div class="context-menu-divider"></div>
    <button type="button" class="context-menu-item danger" onclick="execContextMenuAction('archive')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/></svg>
        <span>Archive Record</span>
    </button>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Add Student Modal -->
<div class="custom-modal-backdrop" id="addStudentModal">
    <div class="custom-modal-card">
        <div class="modal-head">
            <div class="modal-head-title">+ Enroll New Student</div>
            <button type="button" class="modal-close-btn" onclick="closeAddStudentModal()">✕</button>
        </div>
        <form action="{{ route('school.students.store') }}" method="POST" onsubmit="handleAddStudentSubmit(event)">
            @csrf
            <div class="modal-body">
                <div class="form-group-grid">
                    <div class="form-field-wrap">
                        <label>Student Full Name (Latin) *</label>
                        <input type="text" name="full_name" required placeholder="e.g. Sokha Piseth" class="form-input-control">
                    </div>
                    <div class="form-field-wrap">
                        <label>Khmer Name (អក្សរខ្មែរ)</label>
                        <input type="text" name="khmer_name" placeholder="e.g. សុខា ពិសិដ្ឋ" class="form-input-control">
                    </div>
                </div>

                <div class="form-group-grid">
                    <div class="form-field-wrap">
                        <label>Grade Level *</label>
                        <select name="grade_level" required class="form-input-control">
                            <option value="Grade 1">Grade 1</option>
                            <option value="Grade 2">Grade 2</option>
                            <option value="Grade 3">Grade 3</option>
                            <option value="Grade 4" selected>Grade 4</option>
                            <option value="Grade 5">Grade 5</option>
                            <option value="Grade 6">Grade 6</option>
                        </select>
                    </div>
                    <div class="form-field-wrap">
                        <label>Classroom / Section *</label>
                        <input type="text" name="class_name" required value="4A" class="form-input-control">
                    </div>
                </div>

                <div class="form-group-grid">
                    <div class="form-field-wrap">
                        <label>Gender *</label>
                        <select name="gender" required class="form-input-control">
                            <option value="male" selected>Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-field-wrap">
                        <label>Meal Plan</label>
                        <select name="meal_plan" class="form-input-control">
                            <option value="Regular Meal" selected>Regular Meal</option>
                            <option value="Halal Meal">Halal Meal</option>
                            <option value="Vegetarian">Vegetarian</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-grid">
                    <div class="form-field-wrap">
                        <label>Parent / Guardian Name</label>
                        <input type="text" name="parent_name" placeholder="e.g. Sokha Rithy" class="form-input-control">
                    </div>
                    <div class="form-field-wrap">
                        <label>Parent Phone (+855)</label>
                        <input type="text" name="parent_phone" placeholder="+855 12 345 678" class="form-input-control">
                    </div>
                </div>

                <div class="form-field-wrap" style="margin-top: 0.5rem;">
                    <label>Medical Allergens (Multi-select check)</label>
                    <div style="display: flex; gap: 0.85rem; flex-wrap: wrap; margin-top: 0.35rem;">
                        <label style="font-size: 0.8rem; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.3rem;">
                            <input type="checkbox" name="allergens[]" value="Egg"> Egg
                        </label>
                        <label style="font-size: 0.8rem; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.3rem;">
                            <input type="checkbox" name="allergens[]" value="Peanut"> Peanut
                        </label>
                        <label style="font-size: 0.8rem; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.3rem;">
                            <input type="checkbox" name="allergens[]" value="Milk"> Milk / Lactose
                        </label>
                        <label style="font-size: 0.8rem; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.3rem;">
                            <input type="checkbox" name="allergens[]" value="Seafood"> Seafood / Fish
                        </label>
                        <label style="font-size: 0.8rem; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.3rem;">
                            <input type="checkbox" name="allergens[]" value="Soy"> Soy
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="pill-btn" onclick="closeAddStudentModal()">Cancel</button>
                <button type="submit" class="btn-add-primary">Save & Enroll Pupil</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. View Student Modal -->
<div class="custom-modal-backdrop" id="viewStudentModal">
    <div class="custom-modal-card" style="max-width: 520px;">
        <div class="modal-head">
            <div class="modal-head-title">Student Profile Details</div>
            <button type="button" class="modal-close-btn" onclick="closeViewStudentModal()">✕</button>
        </div>
        <div class="modal-body" id="viewStudentContent">
            <!-- Dynamic Injection -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-add-primary" onclick="closeViewStudentModal()">Close</button>
        </div>
    </div>
</div>

<!-- 3. Switch School Modal -->
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
                    Select the campus you wish to administer. All student rosters, deliveries, and shifts will update instantly.
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

<!-- 4. Edit Student Modal -->
<div class="custom-modal-backdrop" id="editStudentModal">
    <div class="custom-modal-card">
        <div class="modal-head">
            <div class="modal-head-title">Edit Student Profile</div>
            <button type="button" class="modal-close-btn" onclick="closeEditStudentModal()">✕</button>
        </div>
        <form id="editStudentForm" onsubmit="handleEditStudentSubmit(event)">
            @csrf
            <input type="hidden" id="editStudentId" name="student_id">
            <div class="modal-body">
                <div class="form-group-grid">
                    <div class="form-field-wrap">
                        <label>Student ID (Read-only)</label>
                        <input type="text" id="editStudentCode" readonly class="form-input-control" style="background: #F1F5F9; color: #64748B; font-weight: 700;">
                    </div>
                    <div class="form-field-wrap">
                        <label>Enrollment Status</label>
                        <select id="editStudentStatus" name="status" class="form-input-control" style="font-weight: 700;">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="allergy_alert">Allergy Alert</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-grid">
                    <div class="form-field-wrap">
                        <label>Student Full Name (Latin) *</label>
                        <input type="text" id="editStudentFullName" name="full_name" required class="form-input-control">
                    </div>
                    <div class="form-field-wrap">
                        <label>Khmer Name (អក្សរខ្មែរ)</label>
                        <input type="text" id="editStudentKhmerName" name="khmer_name" class="form-input-control">
                    </div>
                </div>

                <div class="form-group-grid">
                    <div class="form-field-wrap">
                        <label>Grade Level *</label>
                        <select id="editStudentGrade" name="grade_level" required class="form-input-control">
                            <option value="Grade 1">Grade 1</option>
                            <option value="Grade 2">Grade 2</option>
                            <option value="Grade 3">Grade 3</option>
                            <option value="Grade 4">Grade 4</option>
                            <option value="Grade 5">Grade 5</option>
                            <option value="Grade 6">Grade 6</option>
                        </select>
                    </div>
                    <div class="form-field-wrap">
                        <label>Classroom / Section *</label>
                        <input type="text" id="editStudentClass" name="class_name" required class="form-input-control">
                    </div>
                </div>

                <div class="form-group-grid">
                    <div class="form-field-wrap">
                        <label>Gender *</label>
                        <select id="editStudentGender" name="gender" required class="form-input-control">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-field-wrap">
                        <label>Meal Plan</label>
                        <select id="editStudentMeal" name="meal_plan" class="form-input-control">
                            <option value="Regular Meal">Regular Meal</option>
                            <option value="Halal Meal">Halal Meal</option>
                            <option value="Vegetarian">Vegetarian</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-grid">
                    <div class="form-field-wrap">
                        <label>Parent / Guardian Name</label>
                        <input type="text" id="editStudentParentName" name="parent_name" class="form-input-control">
                    </div>
                    <div class="form-field-wrap">
                        <label>Parent Phone (+855)</label>
                        <input type="text" id="editStudentParentPhone" name="parent_phone" class="form-input-control">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="pill-btn" onclick="closeEditStudentModal()">Cancel</button>
                <button type="submit" class="btn-add-primary" id="editStudentSubmitBtn">Update Student</button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts for Server-Side AJAX DataTable, Filters, Sorting, CSV Export, Toasts -->
<script>
    // Meta Content List Columns Schema & Customizer (User Requested Meta Concept)
    const COLUMNS_SCHEMA = [
        { key: 'select', label: 'Checkbox', frozen: true, default: true, alwaysVisible: true },
        { key: 'code', label: 'Student ID', frozen: true, default: true, alwaysVisible: true },
        { key: 'student', label: 'Student & Context Action', frozen: true, default: true, alwaysVisible: true },
        { key: 'gender', label: 'Gender', frozen: false, default: true },
        { key: 'grade', label: 'Grade Level', frozen: false, default: true },
        { key: 'class', label: 'Class / Section', frozen: false, default: true },
        { key: 'parent', label: 'Parent / Guardian', frozen: false, default: true },
        { key: 'phone', label: 'Parent Phone (Direct)', frozen: false, default: false },
        { key: 'email', label: 'Parent Email', frozen: false, default: false },
        { key: 'meal', label: 'Meal Plan', frozen: false, default: true },
        { key: 'allergy', label: 'Medical Allergens', frozen: false, default: true },
        { key: 'shift', label: 'School Shift', frozen: false, default: false },
        { key: 'dob', label: 'DOB & Age', frozen: false, default: false },
        { key: 'status', label: 'Account Status', frozen: false, default: true },
        { key: 'enrolled', label: 'Enrollment Date', frozen: false, default: false },
    ];

    let activeColumns = loadColumnPreferences();

    function loadColumnPreferences() {
        const saved = localStorage.getItem('sme_students_columns_v1');
        if (saved) {
            try {
                return JSON.parse(saved);
            } catch (e) {
                console.error('Failed to parse columns pref', e);
            }
        }
        const initial = {};
        COLUMNS_SCHEMA.forEach(c => {
            initial[c.key] = c.default;
        });
        return initial;
    }

    function saveColumnPreferences() {
        localStorage.setItem('sme_students_columns_v1', JSON.stringify(activeColumns));
        applyColumnStyles();
        renderColumnChecklist();
    }

    function applyColumnStyles() {
        let css = '';
        const customizableTotal = COLUMNS_SCHEMA.filter(c => !c.frozen).length;
        let visibleCustomizable = 0;

        COLUMNS_SCHEMA.forEach(col => {
            const isVisible = col.alwaysVisible ? true : (activeColumns[col.key] !== false);
            if (!isVisible) {
                css += `[data-col="${col.key}"] { display: none !important; }\n`;
            } else {
                if (!col.frozen) visibleCustomizable++;
            }
        });

        let styleEl = document.getElementById('dynamicColumnStyles');
        if (!styleEl) {
            styleEl = document.createElement('style');
            styleEl.id = 'dynamicColumnStyles';
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
            renderColumnChecklist();
            pop.classList.add('show');
            btn?.classList.add('active');
        }
    }

    function renderColumnChecklist() {
        const container = document.getElementById('columnChecklistContainer');
        if (!container) return;

        let html = '';
        COLUMNS_SCHEMA.forEach(col => {
            if (col.frozen) return;
            const isChecked = activeColumns[col.key] !== false;
            html += `
                <label class="col-check-item">
                    <span>${col.label}</span>
                    <input type="checkbox" ${isChecked ? 'checked' : ''} onchange="onColumnCheckboxToggle('${col.key}', this.checked)">
                </label>
            `;
        });
        container.innerHTML = html;
    }

    function onColumnCheckboxToggle(key, isChecked) {
        activeColumns[key] = isChecked;
        saveColumnPreferences();
    }

    function resetColumnsToDefault() {
        const initial = {};
        COLUMNS_SCHEMA.forEach(c => {
            initial[c.key] = c.default;
        });
        activeColumns = initial;
        saveColumnPreferences();
        if (window.Toast) {
            window.Toast.success('Columns reset to default view.');
        }
    }

    // Context Menu State & Actions (Meta Concept)
    let activeContextStudent = null;

    function openStudentContextMenu(e, id, code, nameEn, nameKm, grade, classVal, gender, parentName, parentPhone, meal, allergy, status, avatar) {
        if (e && e.stopPropagation) e.stopPropagation();
        activeContextStudent = { id, code, nameEn, nameKm, grade, classVal, gender, parentName, parentPhone, meal, allergy, status, avatar };

        const btn = (e && e.currentTarget) ? e.currentTarget : (e && e.target ? e.target.closest('.btn-context-menu') : null);

        document.querySelectorAll('.btn-context-menu').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const pop = document.getElementById('studentContextMenuPop');
        if (!pop) return;

        const titleEl = document.getElementById('contextMenuTitle');
        if (titleEl) {
            titleEl.textContent = `${code} • ${nameEn}`;
        }

        pop.classList.add('show');

        // Position popover relative to button
        if (btn) {
            const rect = btn.getBoundingClientRect();
            const popWidth = 220;
            const popHeight = 250;

            let top = rect.bottom + 6;
            let left = rect.left;

            if (top + popHeight > window.innerHeight - 10) {
                top = rect.top - popHeight - 6;
            }
            if (left + popWidth > window.innerWidth - 10) {
                left = window.innerWidth - popWidth - 10;
            }

            pop.style.top = `${Math.max(10, top)}px`;
            pop.style.left = `${Math.max(10, left)}px`;
        }
    }

    function closeStudentContextMenu() {
        const pop = document.getElementById('studentContextMenuPop');
        if (pop) pop.classList.remove('show');
        document.querySelectorAll('.btn-context-menu').forEach(b => b.classList.remove('active'));
    }

    function execContextMenuAction(action) {
        if (!activeContextStudent) return;
        const st = activeContextStudent;
        closeStudentContextMenu();

        if (action === 'view') {
            viewStudentDetails(st.code, st.nameEn, st.nameKm, st.grade, st.classVal, st.parentName, st.parentPhone, st.meal, st.allergy, st.status, st.avatar);
        } else if (action === 'edit') {
            openEditStudentModal(st.id, st.code, st.nameEn, st.nameKm, st.grade, st.classVal, st.gender, st.parentName, st.parentPhone, st.meal, st.status);
        } else if (action === 'copy_phone') {
            if (st.parentPhone && st.parentPhone !== '-') {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(st.parentPhone).then(() => {
                        if (window.Toast) window.Toast.success(`Copied phone: ${st.parentPhone}`);
                    }).catch(() => {
                        if (window.Toast) window.Toast.info(`Parent phone: ${st.parentPhone}`);
                    });
                } else {
                    if (window.Toast) window.Toast.info(`Parent phone: ${st.parentPhone}`);
                }
            } else {
                if (window.Toast) window.Toast.warning('No phone number recorded for this student.');
            }
        } else if (action === 'call_parent') {
            if (st.parentPhone && st.parentPhone !== '-') {
                window.location.href = `tel:${st.parentPhone.replace(/[^0-9+]/g, '')}`;
            } else {
                if (window.Toast) window.Toast.warning('No phone number available to call.');
            }
        } else if (action === 'qr_badge') {
            if (window.Toast) window.Toast.info(`Meal Badge & QR verified for ${st.nameEn} (${st.code}) - ${st.meal}`);
        } else if (action === 'archive') {
            triggerMoreStudentOptions(st.code, st.id);
        }
    }

    // Dismiss popovers on outside click or scroll
    document.addEventListener('click', function (e) {
        const pop = document.getElementById('studentContextMenuPop');
        if (pop && pop.classList.contains('show')) {
            if (!pop.contains(e.target) && !e.target.closest('.btn-context-menu')) {
                closeStudentContextMenu();
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
        closeStudentContextMenu();
    }, true);

    // State management for Server-Side DataTable
    let currentParams = {
        search: "{{ request('search') }}",
        grade: "{{ request('grade', 'all') }}",
        class: "{{ request('class', 'all') }}",
        meal: "{{ request('meal', 'all') }}",
        allergy: "{{ request('allergy', 'all') }}",
        status: "{{ request('status', 'all') }}",
        page: {{ (int) request('page', 1) }},
        per_page: {{ (int) request('per_page', 10) }},
        sort: "{{ request('sort', 'student_code') }}",
        order: "{{ request('order', 'asc') }}"
    };

    let searchDebounceTimer = null;

    // Fast debounced search input (300ms)
    function onSearchInput(val) {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            currentParams.search = val.trim();
            currentParams.page = 1;
            fetchStudentsServer();
        }, 300);
    }

    // Filter Dropdown change handler
    function onFilterChange() {
        currentParams.grade = document.getElementById('gradeFilterSelect').value;
        currentParams.class = document.getElementById('classFilterSelect').value;
        currentParams.meal = document.getElementById('mealFilterSelect').value;
        currentParams.allergy = document.getElementById('allergyFilterSelect').value;
        currentParams.status = document.getElementById('statusFilterSelect').value;
        currentParams.page = 1;
        fetchStudentsServer();
    }

    // Change per-page records count
    function changePerPage(size) {
        currentParams.per_page = parseInt(size) || 10;
        currentParams.page = 1;
        fetchStudentsServer();
    }

    function setServerEntriesPerPage(size) {
        const sel = document.getElementById('showEntriesSelect');
        if (sel) sel.value = size;
        changePerPage(size);
    }

    // Navigate to page
    function fetchStudentsPage(page) {
        currentParams.page = page;
        fetchStudentsServer();
    }

    // Column Sorting
    function handleSortStudents(field) {
        if (currentParams.sort === field) {
            currentParams.order = currentParams.order === 'asc' ? 'desc' : 'asc';
        } else {
            currentParams.sort = field;
            currentParams.order = 'asc';
        }
        updateSortIcons();
        fetchStudentsServer();
    }

    function updateSortIcons() {
        ['student_code', 'full_name', 'gender', 'grade_level', 'class_name', 'status'].forEach(col => {
            const iconEl = document.getElementById('sortIcon_' + col);
            const thEl = iconEl ? iconEl.closest('th') : null;
            if (!iconEl) return;
            if (currentParams.sort === col) {
                thEl?.classList.add('active-sort');
                iconEl.innerHTML = currentParams.order === 'asc' ? '↑' : '↓';
                iconEl.style.opacity = '1';
            } else {
                thEl?.classList.remove('active-sort');
                iconEl.innerHTML = '↕';
                iconEl.style.opacity = '0.4';
            }
        });
    }

    // Central Server-Side Fetcher via AJAX
    function fetchStudentsServer() {
        const overlay = document.getElementById('tableLoadingOverlay');
        if (overlay) overlay.classList.add('active');

        const q = new URLSearchParams();
        if (currentParams.search) q.set('search', currentParams.search);
        if (currentParams.grade && currentParams.grade !== 'all') q.set('grade', currentParams.grade);
        if (currentParams.class && currentParams.class !== 'all') q.set('class', currentParams.class);
        if (currentParams.meal && currentParams.meal !== 'all') q.set('meal', currentParams.meal);
        if (currentParams.allergy && currentParams.allergy !== 'all') q.set('allergy', currentParams.allergy);
        if (currentParams.status && currentParams.status !== 'all') q.set('status', currentParams.status);
        if (currentParams.page > 1) q.set('page', currentParams.page);
        if (currentParams.per_page !== 10) q.set('per_page', currentParams.per_page);
        if (currentParams.sort && currentParams.sort !== 'student_code') q.set('sort', currentParams.sort);
        if (currentParams.order && currentParams.order !== 'asc') q.set('order', currentParams.order);

        const queryString = q.toString();
        const url = '/school/students' + (queryString ? '?' + queryString : '');

        // Sync browser URL silently so reload/bookmark preserves exact filters
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
                const tbody = document.getElementById('studentsTableBody');
                if (tbody && data.html) {
                    tbody.innerHTML = data.html;
                }
                const pagWrap = document.getElementById('studentsPaginationContainer');
                if (pagWrap && data.pagination_html) {
                    pagWrap.innerHTML = data.pagination_html;
                }
                const heading = document.getElementById('tableRecordCountHeading');
                if (heading && data.total !== undefined) {
                    heading.textContent = `Students (${new Intl.NumberFormat().format(data.total)})`;
                }
                const selectAll = document.getElementById('selectAllCheckbox');
                if (selectAll) selectAll.checked = false;
                closeStudentContextMenu();
                applyColumnStyles();
            }
        })
        .catch(err => {
            if (overlay) overlay.classList.remove('active');
            console.error('Fetch error:', err);
            if (window.Toast) {
                window.Toast.error('Failed to load students data. Please try again.');
            }
        });
    }

    // Reset filters back to default
    function resetStudentFilters() {
        document.getElementById('studentSearchInput').value = '';
        document.getElementById('gradeFilterSelect').value = 'all';
        document.getElementById('classFilterSelect').value = 'all';
        document.getElementById('mealFilterSelect').value = 'all';
        document.getElementById('allergyFilterSelect').value = 'all';
        document.getElementById('statusFilterSelect').value = 'all';

        currentParams = {
            search: '',
            grade: 'all',
            class: 'all',
            meal: 'all',
            allergy: 'all',
            status: 'all',
            page: 1,
            per_page: currentParams.per_page,
            sort: 'student_code',
            order: 'asc'
        };

        updateSortIcons();
        fetchStudentsServer();

        if (window.Toast) {
            window.Toast.success('Filters reset to default view.');
        }
    }

    // Checkbox selection
    function toggleSelectAllStudents(source) {
        const checkboxes = document.querySelectorAll('.student-row-checkbox');
        checkboxes.forEach(cb => {
            if (cb.closest('tr').style.display !== 'none') {
                cb.checked = source.checked;
            }
        });
    }

    // Export CSV
    function exportStudentsCsv() {
        const rows = document.querySelectorAll('.student-row');
        let csv = "Student ID,Student Name,Gender,Grade,Class,Parent Guardian,Meal Plan,Allergy,Status\n";

        rows.forEach(r => {
            if (r.style.display !== 'none') {
                const code = r.getAttribute('data-code');
                const name = r.querySelector('.student-name-en')?.textContent.trim() || '';
                const gender = r.querySelector('.gender-pill')?.textContent.trim() || '';
                const grade = r.getAttribute('data-grade');
                const className = r.getAttribute('data-class');
                const parent = (r.querySelector('.parent-name')?.textContent.trim() || '') + ' (' + (r.querySelector('.parent-phone')?.textContent.trim() || '') + ')';
                const meal = r.getAttribute('data-meal');
                const allergy = r.querySelector('.allergy-badge')?.textContent.trim() || 'None';
                const status = r.getAttribute('data-status');

                csv += `"${code}","${name}","${gender}","${grade}","${className}","${parent}","${meal}","${allergy}","${status}"\n`;
            }
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', `students_export_${new Date().toISOString().slice(0, 10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        if (window.Toast) {
            window.Toast.success('Student roster CSV exported successfully!');
        }
    }

    // Modal Control Handlers
    function openAddStudentModal() {
        document.getElementById('addStudentModal').classList.add('show');
    }
    function closeAddStudentModal() {
        document.getElementById('addStudentModal').classList.remove('show');
    }

    function openSwitchSchoolModal() {
        document.getElementById('switchSchoolModal').classList.add('show');
    }
    function closeSwitchSchoolModal() {
        document.getElementById('switchSchoolModal').classList.remove('show');
    }

    function openDateModal() {
        if (window.Toast) {
            window.Toast.info('Viewing roster calendar for: {{ $dateLabel ?? now()->format("D, M d, Y") }}');
        }
    }

    // Detailed Profile Modal
    function viewStudentDetails(code, nameEn, nameKm, grade, classVal, parentName, parentPhone, meal, allergy, status, avatar) {
        const content = document.getElementById('viewStudentContent');
        const avatarSrc = avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(nameEn)}&background=0066FF&color=fff`;
        content.innerHTML = `
            <div style="text-align: center; margin-bottom: 1.25rem;">
                <img src="${avatarSrc}" alt="${nameEn}" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid #E2E8F0; margin-bottom: 0.5rem;" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(nameEn)}&background=0066FF&color=fff';">
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin: 0;">${nameEn}</h3>
                <p style="font-size: 0.85rem; color: #64748B; margin: 0.2rem 0 0 0;">${nameKm} &bull; <strong style="color: #0066FF;">${code}</strong></p>
            </div>
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1.15rem; font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.65rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <strong style="color: #475569;">Classroom:</strong> <span>${grade} (${classVal})</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <strong style="color: #475569;">Meal Plan:</strong> <span style="color: #0066FF; font-weight: 700;">${meal}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <strong style="color: #475569;">Allergies:</strong> <span style="color: #DC2626; font-weight: 700;">${allergy}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <strong style="color: #475569;">Parent / Guardian:</strong> <span style="font-weight: 600;">${parentName}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <strong style="color: #475569;">Contact Phone:</strong> <span>${parentPhone}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <strong style="color: #475569;">Enrollment Status:</strong> <span style="color: #10B981; font-weight: 700;">${status}</span>
                </div>
            </div>
        `;
        document.getElementById('viewStudentModal').classList.add('show');
    }

    function closeViewStudentModal() {
        document.getElementById('viewStudentModal').classList.remove('show');
    }

    // Edit Modal Open & Submit
    function openEditStudentModal(id, code, nameEn, nameKm, grade, classVal, gender, parentName, parentPhone, meal, status) {
        document.getElementById('editStudentId').value = id;
        document.getElementById('editStudentCode').value = code;
        document.getElementById('editStudentFullName').value = nameEn;
        document.getElementById('editStudentKhmerName').value = nameKm || '';
        document.getElementById('editStudentGrade').value = grade;
        document.getElementById('editStudentClass').value = classVal;
        document.getElementById('editStudentGender').value = (gender || 'male').toLowerCase();
        document.getElementById('editStudentParentName').value = parentName || '';
        document.getElementById('editStudentParentPhone').value = parentPhone || '';
        document.getElementById('editStudentMeal').value = meal || 'Regular Meal';

        const statusSelect = document.getElementById('editStudentStatus');
        if (statusSelect) {
            const stClean = (status || 'active').toLowerCase().replace(' ', '_');
            statusSelect.value = stClean.includes('alert') ? 'allergy_alert' : (stClean.includes('inactive') ? 'inactive' : 'active');
        }

        document.getElementById('editStudentModal').classList.add('show');
    }

    function closeEditStudentModal() {
        document.getElementById('editStudentModal').classList.remove('show');
    }

    function handleEditStudentSubmit(event) {
        event.preventDefault();
        const id = document.getElementById('editStudentId').value;
        const form = document.getElementById('editStudentForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const submitBtn = document.getElementById('editStudentSubmitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
        }

        fetch(`/school/students/${id}`, {
            method: 'PUT',
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
                submitBtn.textContent = 'Update Student';
            }
            if (result.success) {
                closeEditStudentModal();
                if (window.Toast) {
                    window.Toast.success(result.message || 'Student updated successfully!');
                }
                fetchStudentsServer();
            } else {
                if (window.Toast) {
                    window.Toast.error(result.message || 'Error updating student.');
                }
            }
        })
        .catch(err => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Update Student';
            }
            console.error('Update error:', err);
            if (window.Toast) {
                window.Toast.error('An error occurred while updating student record.');
            }
        });
    }

    // Archive / delete action
    function triggerMoreStudentOptions(code, id) {
        if (!confirm(`Archive student ${code}? This student record will be archived.`)) {
            return;
        }

        fetch(`/school/students/${id}`, {
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
                if (window.Toast) {
                    window.Toast.success(result.message || `Student ${code} archived.`);
                }
                fetchStudentsServer();
            } else {
                if (window.Toast) {
                    window.Toast.error(result.message || 'Failed to archive student.');
                }
            }
        })
        .catch(err => {
            if (window.Toast) {
                window.Toast.error('Failed to archive student.');
            }
        });
    }

    function handleAddStudentSubmit(event) {
        // Form submits normally via POST; controller redirects with flash toast
    }

    // Initialize sort indicator & column visibility on load
    document.addEventListener('DOMContentLoaded', function () {
        updateSortIcons();
        applyColumnStyles();
    });
</script>
@endsection
