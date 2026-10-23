@extends('layouts.master_restaurant_owner')

@section('title', transLabel('dashboard.kitchen_ops_dashboard', 'Kitchen Operations Dashboard'))

@push('styles')
<style>
    /* Kitchen Operations Dashboard Custom Styling - Follows Uploaded Reference UI */
    .kitchen-dashboard-container {
        max-width: 1600px;
        margin: 0 auto;
    }

    /* Top 5 KPI Cards Row */
    .kitchen-grid-metrics {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.15rem;
        margin-bottom: 1.5rem;
    }

    .metric-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.15rem 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.95rem;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    }

    .metric-icon-square {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .metric-content {
        flex: 1;
        min-width: 0;
    }

    .metric-label {
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .metric-val {
        font-size: 1.85rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.02em;
    }

    .metric-trend {
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 3px;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        color: #64748b;
    }

    .metric-trend .trend-val-up {
        color: #16a34a;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }
    .metric-trend .trend-val-down {
        color: #ef4444;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }

    /* 2-Column Middle Grid (~67% left, ~33% right) */
    .kitchen-main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.35rem;
        margin-bottom: 1.5rem;
    }

    /* Base Dash Box */
    .dash-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }

    .dash-box-header {
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .dash-box-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    /* Table Filter Pills */
    .filter-pill-btn {
        padding: 0.35rem 0.85rem;
        font-size: 0.775rem;
        font-weight: 700;
        border-radius: 8px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #f1f5f9;
        color: #475569;
        font-family: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .filter-pill-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .filter-pill-btn.active {
        background: #2563eb !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    }
    .filter-pill-btn.danger-pill {
        color: #ef4444;
        background: #fef2f2;
        border-color: #fee2e2;
    }

    /* Queue Toolbar & Scrollable Filter Rail */
    .queue-toolbar {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }
    .queue-filter-rail-wrapper {
        position: relative;
        max-width: 100%;
        overflow: hidden;
    }
    .queue-filter-rail {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        padding: 2px 1px;
    }
    .queue-filter-rail::-webkit-scrollbar {
        display: none;
    }
    .queue-filter-rail .filter-pill-btn {
        flex-shrink: 0;
        white-space: nowrap;
    }
    .queue-actions-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Search Input Toolbar */
    .search-sub-bar {
        position: relative;
        display: flex;
        align-items: center;
        width: 230px;
    }
    .search-sub-bar input {
        width: 100%;
        padding: 0.4rem 0.75rem 0.4rem 2.1rem;
        font-size: 0.785rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #ffffff;
        outline: none;
        font-family: inherit;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .search-sub-bar input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .search-sub-bar svg {
        position: absolute;
        left: 0.7rem;
        color: #94a3b8;
    }

    /* Table Styling with Fixed Sticky Columns & Horizontal Scrolling */
    .queue-table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 12px;
        position: relative;
    }
    .queue-table-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    .queue-table-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 999px;
    }
    .queue-table-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }
    .queue-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .queue-table {
        width: 100%;
        min-width: 742px;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.8125rem;
        text-align: left;
    }
    .queue-table th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        padding: 0.75rem 0.65rem;
        font-size: 0.725rem;
        letter-spacing: 0.02em;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .queue-table td {
        padding: 0.65rem 0.65rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
        background: #ffffff;
    }
    .queue-table tr.queue-row {
        cursor: default;
        transition: background-color 0.15s ease;
    }
    .queue-table tr.queue-row:hover td {
        background-color: #f8fafc !important;
    }

    /* Fixed Sticky Column 1: Row sequence # */
    .col-sticky-seq {
        position: sticky;
        left: 0;
        width: 42px;
        min-width: 42px;
        max-width: 42px;
        background: #ffffff;
        z-index: 5;
    }
    th.col-sticky-seq {
        background: #f8fafc;
        z-index: 8;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Fixed Sticky Column 2: Order ID & Student */
    .col-sticky-order {
        position: sticky;
        left: 42px;
        width: 270px;
        min-width: 270px;
        max-width: 270px;
        background: #ffffff;
        z-index: 5;
        border-right: 2px solid #e2e8f0;
        box-shadow: 4px 0 8px -2px rgba(15, 23, 42, 0.06);
    }
    th.col-sticky-order {
        background: #f8fafc;
        z-index: 8;
        border-right: 2px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: 4px 0 8px -2px rgba(15, 23, 42, 0.06);
    }

    /* Status & Time Composite Column */
    .status-time-composite {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 0.35rem;
    }

    /* Composite Column 1: Order ID & Student + Action Option */
    .order-student-composite {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .order-student-composite .thumb-container {
        flex-shrink: 0;
    }
    .order-student-composite .dish-thumb {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        border: 1.5px solid #e2e8f0;
        display: block;
    }
    .order-student-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
        flex: 1;
    }
    .order-id-line {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .cell-order-id {
        font-weight: 800;
        color: #0f172a;
        white-space: nowrap;
        font-family: monospace;
        font-size: 0.85rem;
        letter-spacing: -0.01em;
    }
    .student-action-line {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        min-width: 0;
    }
    .cell-student {
        font-weight: 700;
        color: #475569;
        font-size: 0.8rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 120px;
    }
    .action-options-inline {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        flex-shrink: 0;
    }
    .btn-micro-action {
        padding: 0.2rem 0.55rem;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-family: inherit;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.1s, box-shadow 0.15s;
        line-height: 1.25;
    }
    .btn-micro-action:active {
        transform: scale(0.95);
    }
    .btn-micro-action.btn-continue {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 1px 4px rgba(37, 99, 235, 0.25);
    }
    .btn-micro-action.btn-continue:hover {
        background: #1d4ed8;
    }
    .btn-micro-action.btn-start {
        background: #10b981;
        color: #ffffff;
        box-shadow: 0 1px 4px rgba(16, 185, 129, 0.25);
    }
    .btn-micro-action.btn-start:hover {
        background: #059669;
    }

    /* Composite Column 2: Meal Item, Qty and Dietary Info */
    .meal-composite {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 0;
    }
    .meal-name-row {
        display: flex;
        align-items: center;
        min-width: 0;
    }
    .cell-meal {
        color: #0f172a;
        font-weight: 700;
        font-size: 0.825rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 230px;
    }
    .meal-meta-row {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        flex-wrap: wrap;
    }
    .meal-qty-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.72rem;
        color: #334155;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 0.12rem 0.45rem;
        border-radius: 5px;
        font-weight: 600;
    }
    .meal-qty-badge strong {
        font-weight: 800;
        color: #0f172a;
    }

    /* Ellipsis Truncation Utilities */
    .cell-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        max-width: 100%;
    }

    /* Time Left Badge */
    .cell-time-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.75rem;
        font-weight: 800;
        color: #ef4444;
        background: #fef2f2;
        padding: 0.22rem 0.55rem;
        border-radius: 6px;
        border: 1px solid #fee2e2;
        white-space: nowrap;
    }
    .cell-time-badge.time-delayed {
        background: #ef4444;
        color: #ffffff;
        border-color: #dc2626;
    }

    /* Status Pills */
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.65rem;
        border-radius: 6px;
        font-size: 0.725rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .status-pill.preparing {
        background: #e0f2fe;
        color: #0284c7;
    }
    .status-pill.ready {
        background: #dcfce7;
        color: #15803d;
    }
    .status-pill.new {
        background: #f1f5f9;
        color: #475569;
    }
    .status-pill.delayed {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Dietary Badges */
    .diet-badge {
        display: inline-block;
        padding: 0.15rem 0.55rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .diet-badge.egg {
        background: #fef3c7;
        color: #b45309;
    }
    .diet-badge.dairy {
        background: #ffedd5;
        color: #c2410c;
    }
    .diet-badge.vegetarian {
        background: #dcfce7;
        color: #15803d;
    }
    .diet-badge.standard {
        background: #f8fafc;
        color: #94a3b8;
        border: 1px solid #e2e8f0;
    }

    /* Action Buttons & Context Menu in Table */
    .btn-context-trigger {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        flex-shrink: 0;
    }
    .btn-context-trigger:hover, .btn-context-trigger:active {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
        transform: scale(1.05);
    }

    /* DataTable Server-Side Pagination Bar */
    .dt-server-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.8rem 1rem;
        border-top: 1px solid #edf2f7;
        background: #ffffff;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
        gap: 1rem;
    }
    .dt-server-meta {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }
    .dt-server-info {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
    }
    .dt-info-icon {
        color: #94a3b8;
        flex-shrink: 0;
    }
    .dt-server-info strong {
        font-weight: 800;
        color: #0f172a;
    }
    .dt-server-perpage {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
    }
    .dt-select-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    .dt-server-perpage select {
        appearance: none;
        -webkit-appearance: none;
        padding: 0.28rem 1.6rem 0.28rem 0.6rem;
        border-radius: 7px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #0f172a;
        font-weight: 700;
        font-size: 0.8rem;
        cursor: pointer;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .dt-server-perpage select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12);
    }
    .dt-select-chevron {
        position: absolute;
        right: 0.45rem;
        pointer-events: none;
        color: #64748b;
    }
    .dt-server-nav {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .dt-nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.32rem 0.75rem;
        border-radius: 7px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        font-size: 0.775rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .dt-nav-btn:hover:not(:disabled) {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .dt-nav-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }
    .dt-nav-pages {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .dt-page-num {
        min-width: 30px;
        height: 30px;
        padding: 0 0.35rem;
        border-radius: 7px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 0.775rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.12s ease;
    }
    .dt-page-num:hover:not(.active) {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }
    .dt-page-num.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 1px 3px rgba(37, 99, 235, 0.3);
    }
    .mobile-only-pagination {
        display: none;
    }

    /* Mobile-Specific Modern Pagination Layout */
    @media (max-width: 768px) {
        .dt-server-pagination {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            padding: 0.75rem 0.85rem;
            margin: 0.65rem 0 0.85rem 0;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }
        .dt-server-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            gap: 0.5rem;
        }
        .dt-server-info {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
            white-space: nowrap;
        }
        .dt-server-info strong {
            color: #0f172a;
            font-weight: 800;
        }
        .dt-info-icon {
            display: none;
        }
        .dt-server-perpage {
            font-size: 0.75rem;
            gap: 0.35rem;
            white-space: nowrap;
        }
        .dt-server-perpage label {
            color: #64748b;
            font-weight: 600;
        }
        .dt-server-perpage select {
            padding: 0.22rem 1.35rem 0.22rem 0.45rem;
            font-size: 0.75rem;
            font-weight: 800;
            border-radius: 6px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
        }
        .dt-server-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            border-top: 1px solid #f1f5f9;
            padding-top: 0.65rem;
            gap: 0.4rem;
        }
        .dt-nav-btn {
            flex: 1;
            max-width: 85px;
            height: 38px;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
        }
        .dt-nav-btn:active:not(:disabled) {
            background: #f1f5f9;
            transform: scale(0.97);
        }
        .dt-nav-pages {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            flex: 2;
        }
        .dt-page-num {
            min-width: 36px;
            height: 36px;
            border-radius: 8px;
            font-size: 0.825rem;
            font-weight: 800;
        }
        .dt-page-num:active:not(.active) {
            transform: scale(0.95);
        }
        .mobile-only-pagination {
            display: flex;
            margin-top: 1rem;
            margin-bottom: 1.5rem;
        }
    }

    /* Mobile Card Context Trigger */
    .btn-card-context {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        transition: all 0.15s ease;
        flex-shrink: 0;
    }
    .btn-card-context:active {
        background: #f1f5f9;
        color: #0f172a;
        transform: scale(0.95);
    }

    /* Floating Context Menu for Kitchen Orders */
    .kitchen-context-menu {
        position: fixed;
        z-index: 9999;
        min-width: 235px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.14), 0 8px 10px -6px rgba(15, 23, 42, 0.08);
        padding: 0.45rem;
        font-family: inherit;
        animation: contextMenuFadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes contextMenuFadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    .context-menu-header {
        padding: 0.45rem 0.65rem 0.35rem 0.65rem;
    }
    .context-menu-id {
        font-size: 0.8rem;
        font-weight: 800;
        color: #0f172a;
        font-family: monospace;
    }
    .context-menu-sub {
        font-size: 0.725rem;
        color: #64748b;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 205px;
        margin-top: 2px;
    }
    .context-menu-divider {
        height: 1px;
        background: #f1f5f9;
        margin: 0.35rem 0;
    }
    .context-menu-item {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.5rem 0.65rem;
        font-size: 0.785rem;
        font-weight: 700;
        color: #334155;
        background: transparent;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-family: inherit;
        text-align: left;
        transition: background 0.12s ease, color 0.12s ease;
    }
    .context-menu-item:hover {
        background: #f8fafc;
        color: #0f172a;
    }
    .context-menu-item svg {
        color: #64748b;
        flex-shrink: 0;
        transition: color 0.12s ease;
    }
    .context-menu-item:hover svg {
        color: #0f172a;
    }
    .context-menu-item.action-primary {
        color: #2563eb;
    }
    .context-menu-item.action-primary svg {
        color: #2563eb;
    }
    .context-menu-item.action-primary:hover {
        background: #eff6ff;
    }
    .context-menu-item.action-success {
        color: #059669;
    }
    .context-menu-item.action-success svg {
        color: #059669;
    }
    .context-menu-item.action-success:hover {
        background: #ecfdf5;
    }
    .context-menu-item.action-warning {
        color: #d97706;
    }
    .context-menu-item.action-warning svg {
        color: #d97706;
    }
    .context-menu-item.action-warning:hover {
        background: #fffbeb;
    }

    /* Mobile Order Cards Styling */
    .queue-cards-wrapper {
        display: none;
    }
    .queue-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 0.95rem 1rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .queue-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }
    .order-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding-bottom: 0.65rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .order-card-id-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .order-card-seq {
        font-weight: 800;
        font-size: 0.825rem;
        color: #94a3b8;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        width: 26px;
        height: 26px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .order-card-id {
        font-weight: 800;
        font-size: 0.925rem;
        color: #0f172a;
        font-family: monospace;
        letter-spacing: -0.01em;
    }
    .order-card-status-group {
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }
    .order-card-time {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.725rem;
        font-weight: 800;
        color: #ef4444;
        background: #fef2f2;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        border: 1px solid #fee2e2;
    }
    .order-card-time.time-delayed {
        background: #ef4444;
        color: #ffffff;
        border-color: #dc2626;
    }
    .order-card-body {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .order-card-img-wrap {
        position: relative;
        flex-shrink: 0;
    }
    .order-card-img {
        width: 62px;
        height: 62px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1.5px solid #e2e8f0;
        display: block;
    }
    .order-card-qty-badge {
        position: absolute;
        bottom: -4px;
        right: -4px;
        background: #0f172a;
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 800;
        padding: 0.1rem 0.35rem;
        border-radius: 9999px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.25);
    }
    .order-card-info {
        flex: 1;
        min-width: 0;
    }
    .order-card-meal {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.25;
        margin: 0 0 0.35rem 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .order-card-student {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.8rem;
        color: #475569;
        font-weight: 600;
        margin-bottom: 0.4rem;
    }
    .order-card-student svg {
        color: #94a3b8;
        flex-shrink: 0;
    }
    .order-card-tags {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-wrap: wrap;
    }
    .order-card-qty-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.15rem 0.5rem;
        background: #f1f5f9;
        color: #1e293b;
        font-size: 0.7rem;
        font-weight: 800;
        border-radius: 6px;
    }
    .order-card-footer {
        padding-top: 0.5rem;
        border-top: 1px solid #f8fafc;
    }
    .btn-card-action {
        width: 100%;
        padding: 0.6rem 1rem;
        font-size: 0.825rem;
        font-weight: 800;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        font-family: inherit;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: transform 0.1s ease, box-shadow 0.15s ease;
    }
    .btn-card-action:active {
        transform: scale(0.98);
    }
    .btn-card-action.btn-continue {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.28);
    }
    .btn-card-action.btn-start {
        background: #10b981;
        color: #ffffff;
        box-shadow: 0 3px 10px rgba(16, 185, 129, 0.28);
    }

    /* Bottom 4-Grid */
    .kitchen-bottom-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
    }

    /* Progress & Breakdown Bars */
    .breakdown-row {
        margin-bottom: 0.85rem;
    }
    .breakdown-labels {
        display: flex;
        justify-content: space-between;
        font-size: 0.785rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.35rem;
    }
    .progress-bar-rail {
        height: 8px;
        background: #f1f5f9;
        border-radius: 999px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.3s ease;
    }

    /* Meta (Facebook) Insights Rail for Kitchen Top Metrics */
    .kitchen-insights-wrapper {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .kitchen-insights-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        padding: 0 0.25rem;
    }
    .kitchen-insights-title-box {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }
    .kitchen-insights-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.825rem;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.28rem 0.65rem;
        border-radius: 8px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }
    .kitchen-insights-badge svg {
        flex-shrink: 0;
    }
    .kitchen-insights-pill {
        font-size: 0.725rem;
        font-weight: 600;
        color: #64748b;
        background: #ffffff;
        padding: 0.2rem 0.65rem;
        border-radius: 9999px;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .kitchen-insights-pill .pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10B981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    .kitchen-insights-controls {
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }
    .kitchen-insights-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        padding: 0;
    }
    .kitchen-insights-btn:hover:not(:disabled) {
        background: #f8fafc;
        color: #1d4ed8;
        border-color: #cbd5e1;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.1);
        transform: scale(1.06);
    }
    .kitchen-insights-btn:disabled, .kitchen-insights-btn.disabled {
        opacity: 0.35;
        cursor: not-allowed;
        pointer-events: none;
    }
    .kitchen-track-container {
        position: relative;
    }
    .kitchen-fade-edge {
        position: absolute;
        top: 0;
        bottom: 12px;
        width: 36px;
        z-index: 10;
        pointer-events: none;
        transition: opacity 0.2s ease;
        opacity: 0;
    }
    .kitchen-fade-edge.fade-left {
        left: 0;
        background: linear-gradient(90deg, #f8fafc 0%, rgba(248, 250, 252, 0) 100%);
    }
    .kitchen-fade-edge.fade-right {
        right: 0;
        background: linear-gradient(270deg, #f8fafc 0%, rgba(248, 250, 252, 0) 100%);
    }

    /* Responsive adjustments */
    @media (max-width: 1280px) {
        .kitchen-grid-metrics {
            grid-template-columns: repeat(3, 1fr);
        }
        .kitchen-bottom-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (min-width: 1281px) {
        .kitchen-insights-header .kitchen-insights-controls {
            display: none;
        }
        .kitchen-insights-header .kitchen-insights-pill {
            display: none;
        }
    }
    @media (max-width: 1024px) {
        .kitchen-main-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 768px) {
        .kitchen-grid-metrics {
            display: flex !important;
            gap: 0.85rem !important;
            overflow-x: auto !important;
            scroll-snap-type: x mandatory !important;
            scroll-behavior: smooth !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: none !important;
            padding: 4px 2px 14px 2px !important;
            margin-bottom: 1.25rem !important;
            cursor: grab;
            user-select: none;
        }
        .kitchen-grid-metrics::-webkit-scrollbar {
            display: none !important;
        }
        .kitchen-grid-metrics.is-dragging {
            cursor: grabbing !important;
            scroll-snap-type: none !important;
            scroll-behavior: auto !important;
        }
        .kitchen-grid-metrics .metric-card {
            flex: 0 0 76vw !important;
            width: 76vw !important;
            min-width: 230px !important;
            max-width: 280px !important;
            scroll-snap-align: start !important;
            margin: 0 !important;
        }
        .kitchen-fade-edge.fade-right {
            opacity: 1;
        }
        .kitchen-bottom-grid {
            grid-template-columns: 1fr;
            margin-bottom: 0.5rem !important;
        }
        .kitchen-dashboard-container {
            padding-bottom: 0 !important;
        }
        .dash-box-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .queue-toolbar {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }
        .queue-filter-rail-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .queue-filter-rail {
            width: max-content;
            min-width: 100%;
            padding-bottom: 2px;
        }
        .queue-actions-row {
            width: 100%;
            display: flex;
            gap: 0.5rem;
        }
        .queue-actions-row .search-sub-bar {
            flex: 1;
            width: auto !important;
        }
        .queue-table-wrapper {
            display: none !important;
        }
        .queue-cards-wrapper {
            display: flex !important;
            flex-direction: column;
            gap: 0.85rem;
            margin-top: 0.75rem;
        }
    }
    @media (min-width: 769px) {
        .queue-cards-wrapper {
            display: none !important;
        }
        .mobile-only-spacer {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="kitchen-dashboard-container">

    <!-- Top Greeting & Header Controls -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.85rem; font-weight: 900; color: #0f172a; line-height: 1.2; letter-spacing: -0.02em; margin: 0;">
                {{ transLabel('dashboard.good_morning', 'Good morning') }}, Sokha!
            </h1>
            <p style="font-size: 0.95rem; color: #64748b; margin-top: 0.25rem; margin-bottom: 0;">
                {{ transLabel('dashboard.kitchen_greeting_sub', "Let's prepare healthy meals for our students.") }}
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <!-- Interactive Date Filter (Matches Screenshot Style: Calendar Icon + Date Pill) -->
            <x-dashboard.date-filter
                :active-range="$range ?? 'today'"
                :active-label="$displayDate ?? ($label ?? 'Mon, Oct 16, 2026')"
                filter-id="kitchenOwnerDateFilter"
                on-select="window.applyKitchenDateFilter"
            />

            <!-- Lunch Shift Dropdown Pill (Matches Screenshot Style: Sun Icon + Shift Hours) -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.45rem 0.95rem; display: flex; align-items: center; gap: 0.65rem; cursor: pointer; box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);" onclick="window.Toast.info('Lunch Shift active (10:00 - 14:00)')">
                <div style="display: flex; align-items: center; justify-content: center; color: #f59e0b;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                </div>
                <div style="line-height: 1.2;">
                    <div style="font-size: 0.8125rem; font-weight: 800; color: #0f172a;">{{ transLabel('dashboard.lunch_shift', 'Lunch Shift') }}</div>
                    <div style="font-size: 0.7rem; color: #64748b;">10:00 - 14:00</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meta (Facebook) Insights Rail for Top 5 Metric Cards -->
    <div class="kitchen-insights-wrapper">
        <div class="kitchen-insights-header">
            <div class="kitchen-insights-title-box">
                <div class="kitchen-insights-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                    <span>{{ transLabel('dashboard.performance_insights', 'Key Insights') }}</span>
                </div>
                <div class="kitchen-insights-pill">
                    <span class="pulse-dot"></span>
                    <span>{{ transLabel('dashboard.metrics_count', ':count Metrics', ['count' => 5]) }} • {{ transLabel('dashboard.scroll_to_explore', 'Scroll to explore') }}</span>
                </div>
            </div>

            <div class="kitchen-insights-controls">
                <button
                    type="button"
                    class="kitchen-insights-btn disabled"
                    id="kitchenMetricsPrevBtn"
                    onclick="scrollKitchenMetrics('left')"
                    title="{{ transLabel('dashboard.scroll_left', 'Scroll left') }}"
                    aria-label="{{ transLabel('dashboard.scroll_left', 'Scroll left') }}"
                    disabled
                >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button
                    type="button"
                    class="kitchen-insights-btn"
                    id="kitchenMetricsNextBtn"
                    onclick="scrollKitchenMetrics('right')"
                    title="{{ transLabel('dashboard.scroll_right', 'Scroll right') }}"
                    aria-label="{{ transLabel('dashboard.scroll_right', 'Scroll right') }}"
                >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>

        <div class="kitchen-track-container">
            <div class="kitchen-fade-edge fade-left" id="kitchenMetricsFadeLeft"></div>
            <div class="kitchen-fade-edge fade-right" id="kitchenMetricsFadeRight"></div>

            <div class="kitchen-grid-metrics" id="kitchenMetricsTrack">
                <!-- 1. Today's Orders -->
                <div class="metric-card">
                    <div class="metric-icon-square" style="background: #2563eb;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    </div>
                    <div class="metric-content">
                        <div class="metric-label" style="color: #2563eb;">{{ transLabel('dashboard.todays_orders', "Today's Orders") }}</div>
                        <div class="metric-val" id="kitchen_kpi_todays_orders">{{ $metrics['todays_orders']['val'] }}</div>
                        <div class="metric-trend" id="kitchen_kpi_todays_orders_trend">
                            <span class="trend-val-up">↑ <span id="kitchen_kpi_todays_orders_trend_text">{{ $metrics['todays_orders']['trend'] ?? '12%' }}</span></span>
                            <span>vs yesterday</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Meals to Prepare -->
                <div class="metric-card">
                    <div class="metric-icon-square" style="background: #f59e0b;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" y1="17" x2="18" y2="17"/></svg>
                    </div>
                    <div class="metric-content">
                        <div class="metric-label" style="color: #d97706;">{{ transLabel('dashboard.meals_to_prepare', 'Meals to Prepare') }}</div>
                        <div class="metric-val" id="kitchen_kpi_meals_to_prepare">{{ $metrics['meals_to_prepare']['val'] }}</div>
                        <div class="metric-trend" id="kitchen_kpi_meals_to_prepare_trend">
                            <span class="trend-val-up">↑ <span id="kitchen_kpi_meals_to_prepare_trend_text">{{ $metrics['meals_to_prepare']['trend'] ?? '8%' }}</span></span>
                            <span>vs yesterday</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Prepared Meals (with 68% progress bar) -->
                <div class="metric-card">
                    <div class="metric-icon-square" style="background: #10b981;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="metric-content">
                        <div class="metric-label" style="color: #059669;">{{ transLabel('dashboard.prepared_meals', 'Prepared Meals') }}</div>
                        <div class="metric-val" id="kitchen_kpi_prepared_meals">{{ $metrics['prepared_meals']['val'] }}</div>
                        <div style="margin-top: 4px;">
                            <div id="kitchen_kpi_prepared_meals_pct" style="font-size: 0.75rem; font-weight: 800; color: #10b981; margin-bottom: 3px;">{{ $metrics['prepared_meals']['pct'] ?? 68 }}%</div>
                            <div style="height: 6px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                                <div id="kitchen_kpi_prepared_meals_bar" style="width: {{ $metrics['prepared_meals']['pct'] ?? 68 }}%; height: 100%; background: #10b981; border-radius: 999px; transition: width 0.4s ease;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Ready for Handover -->
                <div class="metric-card">
                    <div class="metric-icon-square" style="background: #8b5cf6;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                    </div>
                    <div class="metric-content">
                        <div class="metric-label" style="color: #7c3aed;">{{ transLabel('dashboard.ready_for_handover', 'Ready for Handover') }}</div>
                        <div class="metric-val" id="kitchen_kpi_ready_handover">{{ $metrics['ready_handover']['val'] }}</div>
                        <div class="metric-trend" id="kitchen_kpi_ready_handover_trend">
                            <span class="trend-val-up">↑ <span id="kitchen_kpi_ready_handover_trend_text">{{ $metrics['ready_handover']['trend'] ?? '6%' }}</span></span>
                            <span>vs yesterday</span>
                        </div>
                    </div>
                </div>

                <!-- 5. Delayed Meals -->
                <div class="metric-card">
                    <div class="metric-icon-square" style="background: #ef4444;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="metric-content">
                        <div class="metric-label" style="color: #dc2626;">{{ transLabel('dashboard.delayed_meals', 'Delayed Meals') }}</div>
                        <div class="metric-val" id="kitchen_kpi_delayed_meals" style="color: #0f172a;">{{ $metrics['delayed_meals']['val'] }}</div>
                        <div class="metric-trend" id="kitchen_kpi_delayed_meals_trend">
                            <span class="trend-val-down">↓ <span id="kitchen_kpi_delayed_meals_trend_text">{{ str_replace('-', '', $metrics['delayed_meals']['trend'] ?? '50%') }}</span></span>
                            <span>vs yesterday</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Two-Column Middle Section -->
    <div class="kitchen-main-grid">

        <!-- Left Column: Kitchen Queue (12) -->
        <div class="dash-box" id="kitchenQueueSection">
            <div class="dash-box-header">
                <div class="dash-box-title">
                    <span>{{ transLabel('menu.kitchen_queue', 'Kitchen Queue') }}</span>
                    <span id="queueTotalHeaderBadge" style="color: #ef4444; font-size: 1.15rem; font-weight: 800;">({{ $queuePagination['recordsTotal'] ?? 12 }})</span>
                </div>

                <div class="queue-toolbar">
                    <!-- Scrollable Filter Rail -->
                    <div class="queue-filter-rail-wrapper">
                        <div class="queue-filter-rail" id="queueFilterRail">
                            <button type="button" class="filter-pill-btn active" data-filter="all" onclick="filterQueueTable('all', this)">All (<span id="count_all">{{ $queuePagination['counts']['all'] ?? 12 }}</span>)</button>
                            <button type="button" class="filter-pill-btn" data-filter="Preparing" onclick="filterQueueTable('Preparing', this)">Preparing (<span id="count_preparing">{{ $queuePagination['counts']['preparing'] ?? 6 }}</span>)</button>
                            <button type="button" class="filter-pill-btn" data-filter="Ready" onclick="filterQueueTable('Ready', this)">Ready (<span id="count_ready">{{ $queuePagination['counts']['ready'] ?? 4 }}</span>)</button>
                            <button type="button" class="filter-pill-btn danger-pill" data-filter="Delayed" onclick="filterQueueTable('Delayed', this)">Delayed (<span id="count_delayed">{{ $queuePagination['counts']['delayed'] ?? 2 }}</span>)</button>
                        </div>
                    </div>

                    <div class="queue-actions-row">
                        <!-- Search Input -->
                        <div class="search-sub-bar">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" id="queueSearchInput" placeholder="Search order, student, or menu..." oninput="searchQueueTable(this.value)">
                        </div>

                        <!-- Filter Button -->
                        <button type="button" class="filter-pill-btn" onclick="window.Toast.info('Server-Side Queue: Paginated & filtered real-time.')" style="border: 1px solid #e2e8f0; background: #ffffff;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Desktop Table View (min-width: 769px) -->
            <div class="queue-table-wrapper">
                <table class="queue-table" id="kitchenQueueTable">
                    <colgroup>
                        <col style="width: 42px;">
                        <col style="width: 270px;">
                        <col style="width: 270px;">
                        <col style="width: 160px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="col-sticky-seq" style="text-align: center;">#</th>
                            <th class="col-sticky-order">Order ID & Student</th>
                            <th>Meal, Qty & Dietary</th>
                            <th style="text-align: center;">Status & Time Left</th>
                        </tr>
                    </thead>
                    <tbody id="queueTableBody">
                        @foreach($kitchenQueue as $index => $item)
                            <tr data-status="{{ $item['status'] }}"
                                data-id="{{ $item['id'] }}"
                                data-meal="{{ $item['meal'] }}"
                                data-student="{{ $item['student'] }}"
                                data-qty="{{ $item['qty'] }}"
                                class="queue-row"
                                title="Right-click for options">
                                <td style="font-weight: 700; color: #94a3b8; text-align: center;" class="col-sticky-seq row-seq">
                                    {{ ($queuePagination['from'] ?? 1) + $index }}
                                </td>
                                <!-- Fixed Column 1: Order ID & Student + Action Option -->
                                <td class="col-sticky-order">
                                    <div class="order-student-composite">
                                        <div class="thumb-container">
                                            <img src="{{ $item['img'] }}" alt="{{ $item['meal'] }}" class="dish-thumb" onerror="this.src='{{ asset('images/food_dish_1.jpg') }}'">
                                        </div>
                                        <div class="order-student-info">
                                            <div class="order-id-line">
                                                <span class="cell-order-id">{{ $item['id'] }}</span>
                                            </div>
                                            <div class="student-action-line">
                                                <span class="cell-student cell-truncate" title="{{ $item['student'] }}">{{ $item['student'] }}</span>
                                                <div class="action-options-inline">
                                                    @if($item['action'] === 'Continue')
                                                        <button type="button" class="btn-micro-action btn-continue" onclick="handleKitchenAction('continue', '{{ $item['id'] }}', '{{ addslashes($item['meal']) }}')" title="Continue Preparation">
                                                            Continue
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn-micro-action btn-start" onclick="handleKitchenAction('start', '{{ $item['id'] }}', '{{ addslashes($item['meal']) }}')" title="Start Preparation">
                                                            Start
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn-context-trigger" onclick="openKitchenContextMenu(event, '{{ $item['id'] }}', '{{ addslashes($item['meal']) }}', '{{ addslashes($item['student']) }}', '{{ $item['status'] }}', '{{ $item['qty'] }}')" title="More order options">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/><circle cx="5" cy="12" r="1.5"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Scrollable Column 2: Meal Item, Qty and Dietary Info -->
                                <td>
                                    <div class="meal-composite">
                                        <div class="meal-name-row">
                                            <span class="cell-meal cell-truncate" title="{{ $item['meal'] }}">{{ $item['meal'] }}</span>
                                        </div>
                                        <div class="meal-meta-row">
                                            <span class="meal-qty-badge">
                                                <span class="qty-label">Qty:</span>
                                                <strong>{{ $item['qty'] }}</strong>
                                            </span>
                                            @if($item['dietary'] === 'Egg')
                                                <span class="diet-badge egg">Egg</span>
                                            @elseif($item['dietary'] === 'Dairy')
                                                <span class="diet-badge dairy">Dairy</span>
                                            @elseif($item['dietary'] === 'Vegetarian')
                                                <span class="diet-badge vegetarian">Vegetarian</span>
                                            @elseif(!empty($item['dietary']) && $item['dietary'] !== '-')
                                                <span class="diet-badge">{{ $item['dietary'] }}</span>
                                            @else
                                                <span class="diet-badge standard">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <!-- Scrollable Column 3: Status & Time Left Combined -->
                                <td style="text-align: center;">
                                    <div class="status-time-composite">
                                        @if($item['status'] === 'Preparing')
                                            <span class="status-pill preparing">Preparing</span>
                                        @elseif($item['status'] === 'Ready')
                                            <span class="status-pill ready">Ready</span>
                                        @elseif($item['status'] === 'Delayed')
                                            <span class="status-pill delayed">Delayed</span>
                                        @else
                                            <span class="status-pill new">{{ $item['status'] }}</span>
                                        @endif
                                        <span class="cell-time-badge {{ $item['status'] === 'Delayed' ? 'time-delayed' : '' }}">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            <span>{{ $item['time'] }}</span>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="queueTableEmptyRow" style="{{ count($kitchenQueue) === 0 ? '' : 'display: none;' }}">
                            <td colspan="4" style="text-align: center; padding: 2.5rem 1rem; color: #94a3b8; font-weight: 600;">
                                No orders found matching the criteria.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- DataTable Server-Side Pagination Bar -->
            <div class="dt-server-pagination" id="kitchenQueuePagination">
                <div class="dt-server-meta">
                    <div class="dt-server-info" id="queuePaginationInfo">
                        <svg class="dt-info-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                        <span>Showing <strong id="queueFrom">{{ $queuePagination['from'] ?? 1 }}</strong> to <strong id="queueTo">{{ $queuePagination['to'] ?? count($kitchenQueue) }}</strong> of <strong id="queueTotal">{{ $queuePagination['total'] ?? 12 }}</strong> orders</span>
                    </div>

                    <div class="dt-server-perpage">
                        <label for="queuePerPageSelect">Per page:</label>
                        <div class="dt-select-wrap">
                            <select id="queuePerPageSelect" onchange="changeQueuePerPage(this.value)">
                                <option value="5" {{ ($queuePagination['per_page'] ?? 8) == 5 ? 'selected' : '' }}>5</option>
                                <option value="8" {{ ($queuePagination['per_page'] ?? 8) == 8 ? 'selected' : '' }}>8</option>
                                <option value="10" {{ ($queuePagination['per_page'] ?? 8) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ ($queuePagination['per_page'] ?? 8) == 25 ? 'selected' : '' }}>25</option>
                            </select>
                            <svg class="dt-select-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                    </div>
                </div>

                <div class="dt-server-nav">
                    <button type="button" class="dt-nav-btn btn-prev" id="queuePrevPageBtn" onclick="goToQueuePage(currentQueuePage - 1)" {{ ($queuePagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }} title="Previous Page">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        <span>Prev</span>
                    </button>

                    <div class="dt-nav-pages" id="queuePageNumbers">
                        @php
                            $lastPage = $queuePagination['last_page'] ?? 2;
                            $curPage = $queuePagination['current_page'] ?? 1;
                        @endphp
                        @for($p = 1; $p <= $lastPage; $p++)
                            <button type="button" class="dt-page-num {{ $p === $curPage ? 'active' : '' }}" onclick="goToQueuePage({{ $p }})">
                                {{ $p }}
                            </button>
                        @endfor
                    </div>

                    <button type="button" class="dt-nav-btn btn-next" id="queueNextPageBtn" onclick="goToQueuePage(currentQueuePage + 1)" {{ ($queuePagination['current_page'] ?? 1) >= ($queuePagination['last_page'] ?? 2) ? 'disabled' : '' }} title="Next Page">
                        <span>Next</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Card View (max-width: 768px) - Full Information by Row -->
            <div class="queue-cards-wrapper" id="kitchenQueueCards">
                @foreach($kitchenQueue as $index => $item)
                    <div class="queue-card" data-status="{{ $item['status'] }}" data-search="{{ strtolower($item['id'] . ' ' . $item['student'] . ' ' . $item['meal'] . ' ' . ($item['dietary'] ?? '') . ' ' . $item['status']) }}">
                        <!-- Card Header: Sequence, ID, Status, Time Left, and Context Menu Trigger -->
                        <div class="order-card-header">
                            <div class="order-card-id-group">
                                <span class="order-card-seq">#{{ $index + 1 }}</span>
                                <span class="order-card-id">{{ $item['id'] }}</span>
                            </div>
                            <div class="order-card-status-group">
                                @if($item['status'] === 'Preparing')
                                    <span class="status-pill preparing">Preparing</span>
                                @elseif($item['status'] === 'Ready')
                                    <span class="status-pill ready">Ready</span>
                                @elseif($item['status'] === 'Delayed')
                                    <span class="status-pill delayed">Delayed</span>
                                @else
                                    <span class="status-pill new">{{ $item['status'] }}</span>
                                @endif
                                <span class="order-card-time {{ $item['status'] === 'Delayed' ? 'time-delayed' : '' }}">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span>{{ $item['time'] }}</span>
                                </span>
                                <button type="button" class="btn-card-context" onclick="openKitchenContextMenu(event, '{{ $item['id'] }}', '{{ addslashes($item['meal']) }}', '{{ addslashes($item['student']) }}', '{{ $item['status'] }}', '{{ $item['qty'] }}')" title="More actions">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/><circle cx="5" cy="12" r="1.5"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Card Body: Dish photo, Meal Name, Student, Quantity & Dietary Info by Row -->
                        <div class="order-card-body">
                            <div class="order-card-img-wrap">
                                <img src="{{ $item['img'] }}" alt="{{ $item['meal'] }}" class="order-card-img" onerror="this.src='{{ asset('images/food_dish_1.jpg') }}'">
                                <span class="order-card-qty-badge">x{{ $item['qty'] }}</span>
                            </div>
                            <div class="order-card-info">
                                <h4 class="order-card-meal" title="{{ $item['meal'] }}">{{ $item['meal'] }}</h4>
                                <div class="order-card-student" title="{{ $item['student'] }}">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item['student'] }}</span>
                                </div>
                                <div class="order-card-tags">
                                    <span class="order-card-qty-pill">Qty: {{ $item['qty'] }}</span>
                                    @if(!empty($item['dietary']) && $item['dietary'] !== '-')
                                        @if($item['dietary'] === 'Egg')
                                            <span class="diet-badge egg">Egg</span>
                                        @elseif($item['dietary'] === 'Dairy')
                                            <span class="diet-badge dairy">Dairy</span>
                                        @elseif($item['dietary'] === 'Vegetarian')
                                            <span class="diet-badge vegetarian">Vegetarian</span>
                                        @else
                                            <span class="diet-badge">{{ $item['dietary'] }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer: Full-Width Action Button -->
                        <div class="order-card-footer">
                            @if($item['action'] === 'Continue')
                                <button type="button" class="btn-card-action btn-continue" onclick="handleKitchenAction('continue', '{{ $item['id'] }}', '{{ $item['meal'] }}')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                    <span>Continue Prep</span>
                                </button>
                            @else
                                <button type="button" class="btn-card-action btn-start" onclick="handleKitchenAction('start', '{{ $item['id'] }}', '{{ $item['meal'] }}')">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2v2"/><path d="M7 3v2"/><path d="M17 3v2"/><path d="M4 11h16a1 1 0 0 1 1 1v2a6 6 0 0 1-6 6h-6a6 6 0 0 1-6-6v-2a1 1 0 0 1 1-1Z"/></svg>
                                    <span>Start Prep</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Empty State when Filter or Search matches nothing -->
                <div id="queueCardsEmptyState" style="display: none; text-align: center; padding: 2.25rem 1rem; background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 14px; margin-top: 0.25rem;">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" style="margin: 0 auto 0.5rem auto;"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    <div style="font-weight: 800; color: #475569; font-size: 0.925rem;">No orders match your filter</div>
                    <div style="font-size: 0.775rem; color: #94a3b8; margin-top: 0.25rem;">Try selecting "All" or searching for another student or menu item.</div>
                </div>
            </div>

            <!-- Mobile Bottom Pagination Bar: lets mobile operators paginate seamlessly after reviewing cards -->
            <div class="dt-server-pagination mobile-only-pagination" id="kitchenQueuePaginationBottom">
                <div class="dt-server-meta">
                    <div class="dt-server-info" id="queuePaginationInfoBottom">
                        <svg class="dt-info-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                        <span>Showing <strong id="queueFromBottom">{{ $queuePagination['from'] ?? 1 }}</strong> to <strong id="queueToBottom">{{ $queuePagination['to'] ?? count($kitchenQueue) }}</strong> of <strong id="queueTotalBottom">{{ $queuePagination['total'] ?? 12 }}</strong> orders</span>
                    </div>

                    <div class="dt-server-perpage">
                        <label for="queuePerPageSelectBottom">Per page:</label>
                        <div class="dt-select-wrap">
                            <select id="queuePerPageSelectBottom" onchange="changeQueuePerPage(this.value)">
                                <option value="5" {{ ($queuePagination['per_page'] ?? 8) == 5 ? 'selected' : '' }}>5</option>
                                <option value="8" {{ ($queuePagination['per_page'] ?? 8) == 8 ? 'selected' : '' }}>8</option>
                                <option value="10" {{ ($queuePagination['per_page'] ?? 8) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ ($queuePagination['per_page'] ?? 8) == 25 ? 'selected' : '' }}>25</option>
                            </select>
                            <svg class="dt-select-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                    </div>
                </div>

                <div class="dt-server-nav">
                    <button type="button" class="dt-nav-btn btn-prev" id="queuePrevPageBtnBottom" onclick="goToQueuePage(currentQueuePage - 1)" {{ ($queuePagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }} title="Previous Page">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        <span>Prev</span>
                    </button>

                    <div class="dt-nav-pages" id="queuePageNumbersBottom">
                        @for($p = 1; $p <= $lastPage; $p++)
                            <button type="button" class="dt-page-num {{ $p === $curPage ? 'active' : '' }}" onclick="goToQueuePage({{ $p }})">
                                {{ $p }}
                            </button>
                        @endfor
                    </div>

                    <button type="button" class="dt-nav-btn btn-next" id="queueNextPageBtnBottom" onclick="goToQueuePage(currentQueuePage + 1)" {{ ($queuePagination['current_page'] ?? 1) >= ($queuePagination['last_page'] ?? 2) ? 'disabled' : '' }} title="Next Page">
                        <span>Next</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>

            <!-- Floating Kitchen Order Context Menu -->
            <div id="kitchenOrderContextMenu" class="kitchen-context-menu" style="display: none;" role="menu">
                <div class="context-menu-header">
                    <div class="context-menu-id" id="contextMenuOrderId">ORD-00121</div>
                    <div class="context-menu-sub" id="contextMenuDishName">Chicken Rice Bowl</div>
                </div>
                <div class="context-menu-divider"></div>
                <button type="button" class="context-menu-item action-primary" onclick="executeContextAction('toggle_prep')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2v2"/><path d="M7 3v2"/><path d="M17 3v2"/><path d="M4 11h16a1 1 0 0 1 1 1v2a6 6 0 0 1-6 6h-6a6 6 0 0 1-6-6v-2a1 1 0 0 1 1-1Z"/><path d="M6 11V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v4"/></svg>
                    <span id="contextMenuTogglePrepText">Continue Preparation</span>
                </button>
                <button type="button" class="context-menu-item action-success" onclick="executeContextAction('mark_ready')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>Mark as 100% Ready</span>
                </button>
                <button type="button" class="context-menu-item action-warning" onclick="executeContextAction('mark_delayed')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Mark as Delayed (+10 min)</span>
                </button>
                <div class="context-menu-divider"></div>
                <button type="button" class="context-menu-item" onclick="executeContextAction('view_specs')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <span>Recipe & Allergen Specs</span>
                </button>
                <button type="button" class="context-menu-item" onclick="executeContextAction('print_kot')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                    <span>Print Kitchen Ticket (KOT)</span>
                </button>
            </div>
        </div>

        <!-- Right Column: Preparing Now & Next in Queue -->
        <div style="display: flex; flex-direction: column; gap: 1.35rem;">

            <!-- Preparing Now Card -->
            <div class="dash-box" id="preparingNowSection" style="padding: 1.35rem;">
                <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 1.15rem;">
                    {{ transLabel('dashboard.preparing_now', 'Preparing Now') }}
                </div>

                <div style="display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 1.15rem;">
                    <img src="{{ $preparingNow['img'] }}" alt="{{ $preparingNow['name'] }}" style="width: 82px; height: 82px; border-radius: 14px; object-fit: cover; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08); border: 1.5px solid #e2e8f0; flex-shrink: 0;" onerror="this.src='{{ asset('images/food_dish_1.jpg') }}'">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 1.15rem; font-weight: 900; color: #0f172a; line-height: 1.2;">
                            {{ $preparingNow['name'] }}
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.35rem; flex-wrap: wrap;">
                            <span style="font-size: 0.725rem; font-weight: 800; color: #2563eb; background: #eff6ff; padding: 0.15rem 0.55rem; border-radius: 6px;">{{ $preparingNow['id'] }}</span>
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">{{ $preparingNow['student'] }} ({{ $preparingNow['total_qty'] }} meals)</span>
                        </div>

                        <!-- Progress Bar -->
                        <div style="margin-top: 0.65rem;">
                            <div style="height: 6px; background: #e2e8f0; border-radius: 999px; overflow: hidden; margin-bottom: 0.3rem;">
                                <div id="prepProgressBar" style="width: {{ $preparingNow['pct'] }}%; height: 100%; background: #2563eb; border-radius: 999px; transition: width 0.25s ease;"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.725rem; font-weight: 700; color: #64748b;">
                                <span><span id="prepQtyText">{{ $preparingNow['prepared_qty'] }}</span> / {{ $preparingNow['total_qty'] }} prepared</span>
                                <span style="color: #0f172a; font-weight: 800;" id="prepPctText">{{ $preparingNow['pct'] }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert Banner -->
                <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 10px; padding: 0.6rem 0.85rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; color: #ef4444; font-size: 0.8125rem; font-weight: 800; margin-bottom: 1.15rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>12 min left</span>
                </div>

                <!-- Controls & Stepper -->
                <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                    <!-- Stepper -->
                    <div style="display: inline-flex; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <button type="button" onclick="stepPrep(-1)" style="width: 32px; height: 36px; border: none; background: transparent; cursor: pointer; font-size: 1.1rem; font-weight: 700; color: #64748b;">−</button>
                        <span id="stepperVal" style="width: 28px; text-align: center; font-weight: 800; color: #0f172a; font-size: 0.9rem;">6</span>
                        <button type="button" onclick="stepPrep(1)" style="width: 32px; height: 36px; border: none; background: transparent; cursor: pointer; font-size: 1.1rem; font-weight: 700; color: #64748b;">+</button>
                    </div>

                    <button type="button" onclick="markDishReady('{{ $preparingNow['id'] }}', '{{ $preparingNow['name'] }}')" style="flex: 1; height: 36px; background: #10b981; color: #ffffff; border: none; border-radius: 8px; font-weight: 800; font-size: 0.8125rem; cursor: pointer; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3); font-family: inherit;">
                        Mark as Ready
                    </button>

                    <button type="button" onclick="window.Toast.info('Recipe Specs: Chicken breast, fragrant jasmine rice, steamed broccoli, ginger broth.')" style="height: 36px; background: #ffffff; color: #334155; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; padding: 0 0.85rem; cursor: pointer; font-family: inherit;">
                        View Details
                    </button>
                </div>
            </div>

            <!-- Next in Queue Card -->
            <div class="dash-box" style="padding: 1.25rem 1.35rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a;">
                        {{ transLabel('dashboard.next_in_queue', 'Next in Queue') }}
                    </div>
                    <a href="#kitchenQueueSection" style="color: #2563eb; font-size: 0.8125rem; font-weight: 800; text-decoration: none;">View All</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    @foreach($nextInQueue as $next)
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.65rem 0.75rem; background: #ffffff; border: 1px solid #f1f5f9; border-radius: 12px; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);">
                            <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                                <img src="{{ $next['img'] }}" alt="{{ $next['name'] }}" style="width: 44px; height: 44px; border-radius: 10px; object-fit: cover; flex-shrink: 0;" onerror="this.src='{{ asset('images/food_dish_1.jpg') }}'">
                                <div style="min-width: 0;">
                                    <div style="font-weight: 800; font-size: 0.875rem; color: #0f172a;">{{ $next['name'] }}</div>
                                    <div style="color: #64748b; font-size: 0.725rem; margin-top: 1px;">{{ $next['id'] }} • {{ $next['student'] }} • {{ $next['qty'] }} meals</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.55rem; flex-shrink: 0;">
                                @if($next['time'])
                                    <span style="color: #ea580c; background: #ffedd5; padding: 0.15rem 0.5rem; border-radius: 6px; font-size: 0.75rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        {{ $next['time'] }}
                                    </span>
                                @endif
                                @if($next['dietary'])
                                    <span class="diet-badge dairy">{{ $next['dietary'] }}</span>
                                @endif
                                <button type="button" onclick="handleKitchenAction('start', '{{ $next['id'] }}', '{{ $next['name'] }}')" style="padding: 0.35rem 0.85rem; background: #dcfce7; color: #15803d; border: 1px solid rgba(22, 163, 74, 0.25); border-radius: 8px; font-weight: 800; font-size: 0.75rem; cursor: pointer; font-family: inherit;">
                                    Start
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

    <!-- Bottom 4-Card Summary Grid (Matches Screenshot) -->
    <div class="kitchen-bottom-grid">

        <!-- Card 1: Today's Meal Progress -->
        <div class="dash-box" style="padding: 1.35rem;">
            <div style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 1.15rem;">
                {{ transLabel('dashboard.todays_meal_progress', "Today's Meal Progress") }}
            </div>

            <div style="display: flex; align-items: center; justify-content: space-around; gap: 0.85rem; flex-wrap: wrap;">
                <!-- Circular Donut Graphic -->
                <div style="position: relative; width: 110px; height: 110px; flex-shrink: 0;">
                    <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
                        <!-- Background Circle -->
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#f1f5f9" stroke-width="4.2"/>
                        <!-- Prepared Segment -->
                        <path id="kitchen_donut_circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10b981" stroke-width="4.2" stroke-dasharray="{{ $mealProgress['pct'] ?? 68 }}, 100" stroke-linecap="round" style="transition: stroke-dasharray 0.5s ease;"/>
                    </svg>
                    <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; line-height: 1.15;">
                        <span id="kitchen_donut_pct" style="font-size: 1.35rem; font-weight: 900; color: #0f172a;">{{ $mealProgress['pct'] ?? 68 }}%</span>
                        <span id="kitchen_donut_count" style="font-size: 0.725rem; color: #334155; font-weight: 800; margin-top: 1px;">{{ $mealProgress['prepared_count'] ?? '42 / 58' }}</span>
                        <span style="font-size: 0.6rem; color: #64748b;">Meals Prepared</span>
                    </div>
                </div>

                <!-- Legend -->
                <div style="display: flex; flex-direction: column; gap: 0.45rem; font-size: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                        <span style="color: #64748b;">Prepared</span>
                        <span style="font-weight: 800; color: #0f172a;">42 (72%)</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></span>
                        <span style="color: #64748b;">Preparing</span>
                        <span style="font-weight: 800; color: #0f172a;">12 (21%)</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></span>
                        <span style="color: #64748b;">Pending</span>
                        <span style="font-weight: 800; color: #0f172a;">4 (7%)</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></span>
                        <span style="color: #64748b;">Delayed</span>
                        <span style="font-weight: 800; color: #ef4444;">4 (7%)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Meal Type Breakdown -->
        <div class="dash-box" style="padding: 1.35rem;">
            <div style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 1.15rem;">
                {{ transLabel('dashboard.meal_type_breakdown', 'Meal Type Breakdown') }}
            </div>

            @foreach($mealTypeBreakdown as $type)
                <div class="breakdown-row">
                    <div class="breakdown-labels">
                        <span>{{ $type['type'] }}</span>
                        <span style="font-weight: 800; color: #0f172a;">{{ $type['count'] }}</span>
                    </div>
                    <div class="progress-bar-rail">
                        <div class="progress-bar-fill" style="width: {{ $type['pct'] }}%; background: {{ $type['color'] }};"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Card 3: Allergen Alerts Today -->
        <div class="dash-box" style="padding: 1.35rem;">
            <div style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 1.15rem; display: flex; align-items: center; gap: 0.4rem;">
                <span style="color: #f59e0b; font-size: 1.1rem;">⚠️</span>
                <span>{{ transLabel('dashboard.allergen_alerts_today', 'Allergen Alerts Today') }}</span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                @foreach($allergenAlerts as $alert)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.45rem 0.65rem; background: #ffffff; border-radius: 8px; font-size: 0.8125rem;">
                        <div style="display: flex; align-items: center; gap: 0.55rem; font-weight: 700; color: #334155;">
                            <span>{{ $alert['icon'] }}</span>
                            <span>{{ $alert['name'] }}</span>
                        </div>
                        <span style="font-weight: 800; color: {{ $alert['count'] === '0 meals' ? '#94a3b8' : '#0f172a' }};">
                            {{ $alert['count'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Card 4: Quick Actions (Matches 4 colored buttons in Screenshot) -->
        <div class="dash-box" style="padding: 1.35rem;">
            <div style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 1.15rem;">
                {{ transLabel('dashboard.quick_actions', 'Quick Actions') }}
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <!-- 1. Start Preparing (Green) -->
                <button type="button" onclick="window.Toast.success('Started batch kitchen prep for 58 orders.')" style="background: #10b981; color: #ffffff; border: none; border-radius: 10px; padding: 0.85rem 0.4rem; display: flex; align-items: center; justify-content: center; gap: 0.45rem; cursor: pointer; font-family: inherit; font-size: 0.775rem; font-weight: 800; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.28); transition: transform 0.1s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2v2"/><path d="M7 3v2"/><path d="M17 3v2"/><path d="M4 11h16a1 1 0 0 1 1 1v2a6 6 0 0 1-6 6h-6a6 6 0 0 1-6-6v-2a1 1 0 0 1 1-1Z"/><path d="M6 11V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v4"/></svg>
                    <span>Start Preparing</span>
                </button>

                <!-- 2. Mark as Ready (Blue) -->
                <button type="button" onclick="window.Toast.success('Batch of 10 meals marked as Ready for Handover.')" style="background: #2563eb; color: #ffffff; border: none; border-radius: 10px; padding: 0.85rem 0.4rem; display: flex; align-items: center; justify-content: center; gap: 0.45rem; cursor: pointer; font-family: inherit; font-size: 0.775rem; font-weight: 800; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.28); transition: transform 0.1s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>Mark as Ready</span>
                </button>

                <!-- 3. View Orders (Purple) -->
                <button type="button" onclick="window.Toast.info('Viewing all 124 scheduled meal orders.')" style="background: #8b5cf6; color: #ffffff; border: none; border-radius: 10px; padding: 0.85rem 0.4rem; display: flex; align-items: center; justify-content: center; gap: 0.45rem; cursor: pointer; font-family: inherit; font-size: 0.775rem; font-weight: 800; box-shadow: 0 2px 8px rgba(139, 92, 246, 0.28); transition: transform 0.1s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <span>View Orders</span>
                </button>

                <!-- 4. Delivery Handover (Orange) -->
                <button type="button" onclick="window.Toast.success('Delivery Handover manifest sent to dispatch driver.')" style="background: #f97316; color: #ffffff; border: none; border-radius: 10px; padding: 0.85rem 0.4rem; display: flex; align-items: center; justify-content: center; gap: 0.45rem; cursor: pointer; font-family: inherit; font-size: 0.775rem; font-weight: 800; box-shadow: 0 2px 8px rgba(249, 115, 22, 0.28); transition: transform 0.1s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                    <span>Delivery Handover</span>
                </button>
            </div>
        </div>

    </div>

    <!-- Mobile Bottom Safe Spacer: Main content now cleanly handles bottom bar clearance -->
    <div class="mobile-only-spacer" style="height: 0; margin: 0; padding: 0; width: 100%; clear: both;" aria-hidden="true"></div>

</div>

<!-- Client-side Interactions Script -->
<script>
    // Kitchen Stepper
    let currentPrepQty = 6;
    const totalPrepQty = 10;

    function stepPrep(delta) {
        const newQty = Math.max(0, Math.min(totalPrepQty, currentPrepQty + delta));
        if (newQty !== currentPrepQty) {
            currentPrepQty = newQty;
            document.getElementById('stepperVal').innerText = currentPrepQty;
            document.getElementById('prepQtyText').innerText = currentPrepQty;
            const pct = Math.round((currentPrepQty / totalPrepQty) * 100);
            document.getElementById('prepPctText').innerText = pct + '%';
            document.getElementById('prepProgressBar').style.width = pct + '%';
            if (window.Toast) {
                window.Toast.info('Updated prep count: ' + currentPrepQty + ' / ' + totalPrepQty);
            }
        }
    }

    function markDishReady(orderId, dishName) {
        currentPrepQty = totalPrepQty;
        document.getElementById('stepperVal').innerText = currentPrepQty;
        document.getElementById('prepQtyText').innerText = currentPrepQty;
        document.getElementById('prepPctText').innerText = '100%';
        document.getElementById('prepProgressBar').style.width = '100%';
        if (window.Toast) {
            window.Toast.success(orderId + ' (' + dishName + ') marked as 100% Ready for Handover!');
        }
    }

    function handleKitchenAction(actionType, orderId, dishName) {
        if (actionType === 'continue') {
            if (window.Toast) window.Toast.info('Continuing prep for ' + orderId + ' (' + dishName + ')');
        } else {
            if (window.Toast) window.Toast.success('Started kitchen preparation for ' + orderId + ' (' + dishName + ')');
        }
    }

    // Kitchen Order Context Menu Controller
    let currentContextOrder = null;

    function openKitchenContextMenu(event, id, meal, student, status, qty) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        currentContextOrder = { id, meal, student, status, qty };

        const menu = document.getElementById('kitchenOrderContextMenu');
        if (!menu) return;

        const idEl = document.getElementById('contextMenuOrderId');
        if (idEl) idEl.textContent = id + (qty ? ` (${qty} meals)` : '');

        const dishEl = document.getElementById('contextMenuDishName');
        if (dishEl) dishEl.textContent = meal + (student ? ` • ${student}` : '');

        const toggleText = document.getElementById('contextMenuTogglePrepText');
        if (toggleText) {
            toggleText.textContent = (status === 'Preparing') ? 'Continue Preparation' : 'Start Preparation';
        }

        menu.style.display = 'block';
        menu.style.visibility = 'hidden';

        const menuWidth = menu.offsetWidth || 235;
        const menuHeight = menu.offsetHeight || 230;

        let clientX = event ? event.clientX : 0;
        let clientY = event ? event.clientY : 0;

        if ((!clientX || !clientY) && event && event.target) {
            const rect = event.target.getBoundingClientRect();
            clientX = rect.left;
            clientY = rect.bottom + 6;
        }

        const winWidth = window.innerWidth;
        const winHeight = window.innerHeight;

        let left = clientX;
        let top = clientY;

        if (left + menuWidth > winWidth - 12) {
            left = Math.max(12, winWidth - menuWidth - 12);
        }
        if (top + menuHeight > winHeight - 12) {
            top = Math.max(12, clientY - menuHeight - 6);
        }

        menu.style.left = left + 'px';
        menu.style.top = top + 'px';
        menu.style.visibility = 'visible';
    }

    function closeKitchenContextMenu() {
        const menu = document.getElementById('kitchenOrderContextMenu');
        if (menu) menu.style.display = 'none';
    }

    function executeContextAction(action) {
        if (!currentContextOrder) return;
        const { id, meal, student } = currentContextOrder;
        closeKitchenContextMenu();

        switch (action) {
            case 'toggle_prep':
                handleKitchenAction('continue', id, meal);
                break;
            case 'mark_ready':
                if (window.Toast) window.Toast.success(`${id} (${meal}) marked 100% Ready for Handover!`);
                break;
            case 'mark_delayed':
                if (window.Toast) window.Toast.warning(`${id} marked as Delayed (+10 min). Alerted kitchen staff.`);
                break;
            case 'view_specs':
                if (window.Toast) window.Toast.info(`Recipe Specs for ${meal}: Chef standard ingredients & certified allergy-safe prep.`);
                break;
            case 'print_kot':
                if (window.Toast) window.Toast.success(`Kitchen Ticket printed for ${id} (${meal} for ${student}).`);
                break;
        }
    }

    // Attach right-click contextmenu event to desktop rows and click-outside dismissal
    document.addEventListener('DOMContentLoaded', function() {
        setupQueueContextMenu();
    });

    function setupQueueContextMenu() {
        document.querySelectorAll('.queue-row').forEach(row => {
            row.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id') || '';
                const meal = this.getAttribute('data-meal') || '';
                const student = this.getAttribute('data-student') || '';
                const status = this.getAttribute('data-status') || '';
                const qty = this.getAttribute('data-qty') || '';
                openKitchenContextMenu(e, id, meal, student, status, qty);
            });
        });
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('kitchenOrderContextMenu');
        if (menu && menu.style.display !== 'none' && !menu.contains(e.target) && !e.target.closest('.btn-context-trigger') && !e.target.closest('.btn-card-context')) {
            closeKitchenContextMenu();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeKitchenContextMenu();
    });

    window.addEventListener('scroll', function() {
        closeKitchenContextMenu();
    }, { passive: true });

    // DataTable Server-Side State & Pagination
    let currentQueuePage = {{ $queuePagination['current_page'] ?? 1 }};
    let queuePerPage = {{ $queuePagination['per_page'] ?? 8 }};
    let queueTotalPages = {{ $queuePagination['last_page'] ?? 2 }};
    let activeQueueStatus = 'all';
    let queueSearchQuery = '';
    let queueDraw = 1;
    let queueSearchDebounceTimer = null;

    function getQueuePaginateUrl() {
        return window.location.pathname.includes('restaurant') 
            ? '{{ route('restaurant.queue.paginate') }}' 
            : '{{ route('kitchen.queue.paginate') }}';
    }

    function fetchQueueServerData(page, perPage, status, search) {
        const table = document.getElementById('kitchenQueueTable');
        const cardsWrapper = document.getElementById('kitchenQueueCards');
        if (table) table.style.opacity = '0.55';
        if (cardsWrapper) cardsWrapper.style.opacity = '0.55';

        const url = `${getQueuePaginateUrl()}?page=${page}&per_page=${perPage}&status=${encodeURIComponent(status)}&search=${encodeURIComponent(search)}&draw=${++queueDraw}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (table) table.style.opacity = '1';
            if (cardsWrapper) cardsWrapper.style.opacity = '1';
            if (!data.success && !data.data) return;

            currentQueuePage = data.current_page || 1;
            queuePerPage = data.per_page || 8;
            queueTotalPages = data.last_page || 1;

            renderQueueTableRows(data.data || [], data.from || 1);
            renderQueueCards(data.data || [], data.from || 1);
            updateQueuePaginationUI(data);

            if (data.counts) {
                updateQueueFilterCounts(data.counts);
            }

            setupQueueContextMenu();
        })
        .catch(err => {
            if (table) table.style.opacity = '1';
            if (cardsWrapper) cardsWrapper.style.opacity = '1';
            console.error('Queue pagination error:', err);
            if (window.Toast) window.Toast.error('Could not load queue page from server.');
        });
    }

    function renderQueueTableRows(items, startSeq) {
        const tbody = document.getElementById('queueTableBody');
        if (!tbody) return;

        if (items.length === 0) {
            tbody.innerHTML = `
                <tr id="queueTableEmptyRow">
                    <td colspan="4" style="text-align: center; padding: 2.5rem 1rem; color: #94a3b8; font-weight: 600;">
                        No orders found matching the criteria.
                    </td>
                </tr>`;
            return;
        }

        let html = '';
        items.forEach((item, idx) => {
            const seq = startSeq + idx;
            const dietBadge = renderDietaryBadge(item.dietary);
            const statusPill = renderStatusPill(item.status);
            const timeClass = item.status === 'Delayed' ? 'time-delayed' : '';
            const actionBtn = item.action === 'Continue'
                ? `<button type="button" class="btn-micro-action btn-continue" onclick="handleKitchenAction('continue', '${item.id}', '${escapeHtml(item.meal)}')" title="Continue Prep">Continue</button>`
                : `<button type="button" class="btn-micro-action btn-start" onclick="handleKitchenAction('start', '${item.id}', '${escapeHtml(item.meal)}')" title="Start Prep">Start</button>`;

            html += `
                <tr data-status="${escapeHtml(item.status)}"
                    data-id="${escapeHtml(item.id)}"
                    data-meal="${escapeHtml(item.meal)}"
                    data-student="${escapeHtml(item.student)}"
                    data-qty="${item.qty}"
                    class="queue-row"
                    title="Right-click for options">
                    <td style="font-weight: 700; color: #94a3b8; text-align: center;" class="col-sticky-seq row-seq">${seq}</td>
                    <td class="col-sticky-order">
                        <div class="order-student-composite">
                            <div class="thumb-container">
                                <img src="${item.img || '/images/food_dish_1.jpg'}" alt="${escapeHtml(item.meal)}" class="dish-thumb" onerror="this.src='/images/food_dish_1.jpg'">
                            </div>
                            <div class="order-student-info">
                                <div class="order-id-line">
                                    <span class="cell-order-id">${escapeHtml(item.id)}</span>
                                </div>
                                <div class="student-action-line">
                                    <span class="cell-student cell-truncate" title="${escapeHtml(item.student)}">${escapeHtml(item.student)}</span>
                                    <div class="action-options-inline">
                                        ${actionBtn}
                                        <button type="button" class="btn-context-trigger" onclick="openKitchenContextMenu(event, '${escapeHtml(item.id)}', '${escapeJs(item.meal)}', '${escapeJs(item.student)}', '${escapeHtml(item.status)}', '${item.qty}')" title="More order options">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/><circle cx="5" cy="12" r="1.5"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="meal-composite">
                            <div class="meal-name-row">
                                <span class="cell-meal cell-truncate" title="${escapeHtml(item.meal)}">${escapeHtml(item.meal)}</span>
                            </div>
                            <div class="meal-meta-row">
                                <span class="meal-qty-badge">
                                    <span class="qty-label">Qty:</span>
                                    <strong>${item.qty}</strong>
                                </span>
                                ${dietBadge}
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <div class="status-time-composite">
                            ${statusPill}
                            <span class="cell-time-badge ${timeClass}">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>${escapeHtml(item.time || '')}</span>
                            </span>
                        </div>
                    </td>
                </tr>`;
        });

        tbody.innerHTML = html;
    }

    function renderQueueCards(items, startSeq) {
        const container = document.getElementById('kitchenQueueCards');
        if (!container) return;

        if (items.length === 0) {
            container.innerHTML = `
                <div id="queueCardsEmptyState" style="text-align: center; padding: 2.25rem 1rem; background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 14px; margin-top: 0.25rem;">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" style="margin: 0 auto 0.5rem auto;"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    <div style="font-weight: 800; color: #475569; font-size: 0.925rem;">No orders match your filter</div>
                    <div style="font-size: 0.775rem; color: #94a3b8; margin-top: 0.25rem;">Try selecting "All" or searching for another student or menu item.</div>
                </div>`;
            return;
        }

        let html = '';
        items.forEach((item, idx) => {
            const seq = startSeq + idx;
            const dietBadge = renderDietaryBadge(item.dietary);
            const statusPill = renderStatusPill(item.status);
            const timeClass = item.status === 'Delayed' ? 'time-delayed' : '';
            const actionBtn = item.action === 'Continue'
                ? `<button type="button" class="btn-card-action btn-continue" onclick="handleKitchenAction('continue', '${item.id}', '${escapeHtml(item.meal)}')">
                       <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                       <span>Continue Prep</span>
                   </button>`
                : `<button type="button" class="btn-card-action btn-start" onclick="handleKitchenAction('start', '${item.id}', '${escapeHtml(item.meal)}')">
                       <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2v2"/><path d="M7 3v2"/><path d="M17 3v2"/><path d="M4 11h16a1 1 0 0 1 1 1v2a6 6 0 0 1-6 6h-6a6 6 0 0 1-6-6v-2a1 1 0 0 1 1-1Z"/></svg>
                       <span>Start Prep</span>
                   </button>`;

            html += `
                <div class="queue-card" data-status="${escapeHtml(item.status)}" data-search="${escapeHtml((item.id + ' ' + item.student + ' ' + item.meal + ' ' + (item.dietary || '') + ' ' + item.status).toLowerCase())}">
                    <div class="order-card-header">
                        <div class="order-card-id-group">
                            <span class="order-card-seq">#${seq}</span>
                            <span class="order-card-id">${escapeHtml(item.id)}</span>
                        </div>
                        <div class="order-card-status-group">
                            ${statusPill}
                            <span class="order-card-time ${timeClass}">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>${escapeHtml(item.time)}</span>
                            </span>
                            <button type="button" class="btn-card-context" onclick="openKitchenContextMenu(event, '${escapeHtml(item.id)}', '${escapeJs(item.meal)}', '${escapeJs(item.student)}', '${escapeHtml(item.status)}', '${item.qty}')" title="More actions">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/><circle cx="5" cy="12" r="1.5"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="order-card-body">
                        <div class="order-card-img-wrap">
                            <img src="${item.img || '/images/food_dish_1.jpg'}" alt="${escapeHtml(item.meal)}" class="order-card-img" onerror="this.src='/images/food_dish_1.jpg'">
                            <span class="order-card-qty-badge">x${item.qty}</span>
                        </div>
                        <div class="order-card-info">
                            <h4 class="order-card-meal" title="${escapeHtml(item.meal)}">${escapeHtml(item.meal)}</h4>
                            <div class="order-card-student" title="${escapeHtml(item.student)}">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${escapeHtml(item.student)}</span>
                            </div>
                            <div class="order-card-tags">
                                <span class="order-card-qty-pill">Qty: ${item.qty}</span>
                                ${dietBadge}
                            </div>
                        </div>
                    </div>
                    <div class="order-card-footer">
                        ${actionBtn}
                    </div>
                </div>`;
        });

        container.innerHTML = html;
    }

    function renderDietaryBadge(dietary) {
        if (!dietary || dietary === '-') return '<span class="diet-badge standard">-</span>';
        const dLower = String(dietary).toLowerCase();
        if (dLower === 'egg') return '<span class="diet-badge egg">Egg</span>';
        if (dLower === 'dairy') return '<span class="diet-badge dairy">Dairy</span>';
        if (dLower === 'vegetarian') return '<span class="diet-badge vegetarian">Vegetarian</span>';
        return `<span class="diet-badge">${escapeHtml(dietary)}</span>`;
    }

    function renderStatusPill(status) {
        const sLower = String(status || '').toLowerCase();
        if (sLower === 'preparing') return '<span class="status-pill preparing">Preparing</span>';
        if (sLower === 'ready') return '<span class="status-pill ready">Ready</span>';
        if (sLower === 'delayed') return '<span class="status-pill delayed">Delayed</span>';
        return `<span class="status-pill new">${escapeHtml(status)}</span>`;
    }

    function updateQueuePaginationUI(data) {
        // Sync Top and Bottom elements
        const fromEls = [document.getElementById('queueFrom'), document.getElementById('queueFromBottom')];
        const toEls = [document.getElementById('queueTo'), document.getElementById('queueToBottom')];
        const totalEls = [document.getElementById('queueTotal'), document.getElementById('queueTotalBottom')];
        const prevBtns = [document.getElementById('queuePrevPageBtn'), document.getElementById('queuePrevPageBtnBottom')];
        const nextBtns = [document.getElementById('queueNextPageBtn'), document.getElementById('queueNextPageBtnBottom')];
        const containers = [document.getElementById('queuePageNumbers'), document.getElementById('queuePageNumbersBottom')];
        const perPageSelects = [document.getElementById('queuePerPageSelect'), document.getElementById('queuePerPageSelectBottom')];

        const fromVal = data.from || 0;
        const toVal = data.to || 0;
        const totalVal = data.recordsFiltered ?? data.total ?? 0;
        const isPrevDisabled = (data.current_page <= 1);
        const isNextDisabled = (data.current_page >= data.last_page);

        fromEls.forEach(el => { if (el) el.textContent = fromVal; });
        toEls.forEach(el => { if (el) el.textContent = toVal; });
        totalEls.forEach(el => { if (el) el.textContent = totalVal; });

        prevBtns.forEach(btn => {
            if (btn) {
                btn.disabled = isPrevDisabled;
                btn.onclick = function() { goToQueuePage(data.current_page - 1); };
            }
        });
        nextBtns.forEach(btn => {
            if (btn) {
                btn.disabled = isNextDisabled;
                btn.onclick = function() { goToQueuePage(data.current_page + 1); };
            }
        });

        perPageSelects.forEach(sel => { if (sel && data.per_page) sel.value = String(data.per_page); });

        let pagesHtml = '';
        for (let p = 1; p <= data.last_page; p++) {
            const active = (p === data.current_page) ? 'active' : '';
            pagesHtml += `<button type="button" class="dt-page-num ${active}" onclick="goToQueuePage(${p})">${p}</button>`;
        }
        containers.forEach(c => { if (c) c.innerHTML = pagesHtml; });
    }

    window.goToQueuePage = function(page) {
        page = parseInt(page, 10);
        if (isNaN(page) || page < 1 || page > queueTotalPages || page === currentQueuePage) return;
        currentQueuePage = page;
        fetchQueueServerData(page, queuePerPage, activeQueueStatus, queueSearchQuery);
        const cardsEl = document.getElementById('kitchenQueueCards');
        if (window.innerWidth <= 768 && cardsEl) {
            const y = cardsEl.getBoundingClientRect().top + window.pageYOffset - 90;
            window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
        }
    };
    function goToQueuePage(page) {
        return window.goToQueuePage(page);
    }

    function updateQueueFilterCounts(counts) {
        const cAll = document.getElementById('count_all');
        const cPrep = document.getElementById('count_preparing');
        const cReady = document.getElementById('count_ready');
        const cDelayed = document.getElementById('count_delayed');
        const headBadge = document.getElementById('queueTotalHeaderBadge');

        if (cAll && counts.all !== undefined) cAll.textContent = counts.all;
        if (cPrep && counts.preparing !== undefined) cPrep.textContent = counts.preparing;
        if (cReady && counts.ready !== undefined) cReady.textContent = counts.ready;
        if (cDelayed && counts.delayed !== undefined) cDelayed.textContent = counts.delayed;
        if (headBadge && counts.all !== undefined) headBadge.textContent = `(${counts.all})`;
    }

    window.changeQueuePerPage = function(val) {
        queuePerPage = parseInt(val, 10) || 8;
        const selTop = document.getElementById('queuePerPageSelect');
        const selBottom = document.getElementById('queuePerPageSelectBottom');
        if (selTop) selTop.value = queuePerPage;
        if (selBottom) selBottom.value = queuePerPage;
        currentQueuePage = 1;
        fetchQueueServerData(1, queuePerPage, activeQueueStatus, queueSearchQuery);
    };
    function changeQueuePerPage(val) {
        return window.changeQueuePerPage(val);
    }

    function filterQueueTable(status, btnElement) {
        activeQueueStatus = status;
        document.querySelectorAll('.queue-filter-rail .filter-pill-btn').forEach(b => {
            if (b === btnElement || b.getAttribute('data-filter') === status) {
                b.classList.add('active');
            } else {
                b.classList.remove('active');
            }
        });
        currentQueuePage = 1;
        fetchQueueServerData(1, queuePerPage, activeQueueStatus, queueSearchQuery);
    }

    function searchQueueTable(query) {
        queueSearchQuery = (query || '').trim();
        clearTimeout(queueSearchDebounceTimer);
        queueSearchDebounceTimer = setTimeout(() => {
            currentQueuePage = 1;
            fetchQueueServerData(1, queuePerPage, activeQueueStatus, queueSearchQuery);
        }, 280);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeJs(str) {
        if (!str) return '';
        return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    }

    // Kitchen Date Filter Dynamic AJAX Handler
    window.applyKitchenDateFilter = function(range, label, startDate, endDate) {
        let url = window.location.pathname + '?range=' + encodeURIComponent(range) + '&format=json';
        if (startDate && endDate) {
            url += '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
        }

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;

            // 1. Update 5 Metric Cards
            if (data.metrics) {
                const mOrders = document.getElementById('kitchen_kpi_todays_orders');
                if (mOrders && data.metrics.todays_orders) mOrders.textContent = data.metrics.todays_orders.val;
                const mOrdersTrend = document.getElementById('kitchen_kpi_todays_orders_trend_text');
                if (mOrdersTrend && data.metrics.todays_orders) mOrdersTrend.textContent = data.metrics.todays_orders.trend;

                const mPrep = document.getElementById('kitchen_kpi_meals_to_prepare');
                if (mPrep && data.metrics.meals_to_prepare) mPrep.textContent = data.metrics.meals_to_prepare.val;
                const mPrepTrend = document.getElementById('kitchen_kpi_meals_to_prepare_trend_text');
                if (mPrepTrend && data.metrics.meals_to_prepare) mPrepTrend.textContent = data.metrics.meals_to_prepare.trend;

                const mDone = document.getElementById('kitchen_kpi_prepared_meals');
                if (mDone && data.metrics.prepared_meals) mDone.textContent = data.metrics.prepared_meals.val;
                const mDonePct = document.getElementById('kitchen_kpi_prepared_meals_pct');
                if (mDonePct && data.metrics.prepared_meals) mDonePct.textContent = data.metrics.prepared_meals.pct + '%';
                const mDoneBar = document.getElementById('kitchen_kpi_prepared_meals_bar');
                if (mDoneBar && data.metrics.prepared_meals) mDoneBar.style.width = data.metrics.prepared_meals.pct + '%';

                const mReady = document.getElementById('kitchen_kpi_ready_handover');
                if (mReady && data.metrics.ready_handover) mReady.textContent = data.metrics.ready_handover.val;
                const mReadyTrend = document.getElementById('kitchen_kpi_ready_handover_trend_text');
                if (mReadyTrend && data.metrics.ready_handover) mReadyTrend.textContent = data.metrics.ready_handover.trend;

                const mDelayed = document.getElementById('kitchen_kpi_delayed_meals');
                if (mDelayed && data.metrics.delayed_meals) mDelayed.textContent = data.metrics.delayed_meals.val;
                const mDelayedTrend = document.getElementById('kitchen_kpi_delayed_meals_trend_text');
                if (mDelayedTrend && data.metrics.delayed_meals) mDelayedTrend.textContent = data.metrics.delayed_meals.trend;
            }

            // 2. Update Meal Progress Donut
            if (data.mealProgress) {
                const donutPct = document.getElementById('kitchen_donut_pct');
                if (donutPct) donutPct.textContent = data.mealProgress.pct + '%';
                const donutCount = document.getElementById('kitchen_donut_count');
                if (donutCount) donutCount.textContent = data.mealProgress.prepared_count;
                const donutCircle = document.getElementById('kitchen_donut_circle');
                if (donutCircle) donutCircle.setAttribute('stroke-dasharray', data.mealProgress.pct + ', 100');
            }

            // 3. Update Browser URL
            let cleanUrl = window.location.pathname + '?range=' + encodeURIComponent(range);
            if (startDate && endDate) {
                cleanUrl += '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
            }
            window.history.pushState({ range, startDate, endDate }, '', cleanUrl);

            // 4. Toast Notification
            if (window.Toast) {
                window.Toast.success('Kitchen schedule filtered: ' + (data.label || label));
            }
        })
        .catch(err => {
            console.error('Kitchen date filter error:', err);
            if (window.Toast) window.Toast.error('Could not apply kitchen date filter.');
        });
    };

    // Kitchen Insights Meta-Style Scrollable Rail
    function initKitchenMetricsRail() {
        const track = document.getElementById('kitchenMetricsTrack');
        const prevBtn = document.getElementById('kitchenMetricsPrevBtn');
        const nextBtn = document.getElementById('kitchenMetricsNextBtn');
        const fadeLeft = document.getElementById('kitchenMetricsFadeLeft');
        const fadeRight = document.getElementById('kitchenMetricsFadeRight');

        if (!track) return;

        function updateRailState() {
            const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
            const currentScroll = track.scrollLeft;
            const hasOverflow = maxScroll > 8;

            if (prevBtn) {
                if (currentScroll <= 8 || !hasOverflow) {
                    prevBtn.classList.add('disabled');
                    prevBtn.disabled = true;
                } else {
                    prevBtn.classList.remove('disabled');
                    prevBtn.disabled = false;
                }
            }

            if (nextBtn) {
                if (currentScroll >= maxScroll - 8 || !hasOverflow) {
                    nextBtn.classList.add('disabled');
                    nextBtn.disabled = true;
                } else {
                    nextBtn.classList.remove('disabled');
                    nextBtn.disabled = false;
                }
            }

            if (fadeLeft) fadeLeft.style.opacity = (hasOverflow && currentScroll > 15) ? '1' : '0';
            if (fadeRight) fadeRight.style.opacity = (hasOverflow && currentScroll < maxScroll - 15) ? '1' : '0';
        }

        window.scrollKitchenMetrics = function(direction) {
            const delta = direction === 'left' ? -280 : 280;
            track.scrollBy({ left: delta, behavior: 'smooth' });
            setTimeout(updateRailState, 320);
        };

        // Desktop Mouse Drag-To-Scroll (Meta-style)
        let isDragging = false;
        let startPageX = 0;
        let initialScrollLeft = 0;
        let dragDistance = 0;

        track.addEventListener('mousedown', (e) => {
            if (e.target.closest('input, select, button, a')) return;
            isDragging = true;
            dragDistance = 0;
            track.classList.add('is-dragging');
            startPageX = e.pageX - track.offsetLeft;
            initialScrollLeft = track.scrollLeft;
        });

        const stopDragging = () => {
            if (!isDragging) return;
            isDragging = false;
            track.classList.remove('is-dragging');
            updateRailState();
        };

        track.addEventListener('mouseleave', stopDragging);
        track.addEventListener('mouseup', stopDragging);

        track.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const currentX = e.pageX - track.offsetLeft;
            const walk = (currentX - startPageX) * 1.35;
            dragDistance = Math.abs(walk);
            track.scrollLeft = initialScrollLeft - walk;
        });

        track.addEventListener('click', (e) => {
            if (dragDistance > 6) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);

        track.addEventListener('scroll', updateRailState, { passive: true });
        window.addEventListener('resize', updateRailState, { passive: true });

        setTimeout(updateRailState, 150);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKitchenMetricsRail);
    } else {
        initKitchenMetricsRail();
    }
</script>
@endsection
