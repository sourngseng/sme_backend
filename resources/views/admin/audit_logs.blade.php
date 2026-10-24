@extends('layouts.master_super_admin')

@section('title', 'Security & Operational Audit Trail | SchoolMeal Super Admin')

@section('content')
<style>
    /* Executive Audit Hero */
    .audit-hero {
        position: relative;
        background: linear-gradient(135deg, #090d16 0%, #0f172a 40%, #1e1b4b 80%, #064e3b 100%);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 16px 40px -10px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.08) inset;
        border-radius: 20px;
        padding: 2rem 2.25rem 1.85rem 2.25rem;
        color: #ffffff;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .audit-hero-glow {
        position: absolute;
        top: -60px;
        right: -60px;
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, rgba(16, 185, 129, 0.15) 50%, rgba(99, 102, 241, 0) 70%);
        pointer-events: none;
        border-radius: 50%;
    }
    .audit-hero-pattern {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1.5px, transparent 1.5px);
        background-size: 24px 24px;
        pointer-events: none;
        opacity: 0.6;
    }
    .audit-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    .audit-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.25rem 0.7rem;
        border-radius: 9999px;
        background: rgba(99, 102, 241, 0.25);
        color: #a5b4fc;
        border: 1px solid rgba(165, 180, 252, 0.3);
        margin-bottom: 0.5rem;
    }
    .pulse-dot-green {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10B981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseGreen 2s infinite;
    }
    @keyframes pulseGreen {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .audit-hero-title {
        font-size: 1.85rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.15;
        margin-bottom: 0.35rem;
    }
    .audit-hero-subtitle {
        font-size: 0.88rem;
        color: rgba(255, 255, 255, 0.75);
        max-width: 620px;
        line-height: 1.5;
    }
    .audit-hero-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    /* Executive Buttons */
    .btn-audit-backup {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.25rem;
        border-radius: 12px;
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: #ffffff !important;
        font-size: 0.85rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-audit-backup:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
    }
    .btn-audit-export {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.15rem;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.2);
        cursor: pointer;
        backdrop-filter: blur(8px);
        transition: all 0.2s ease;
    }
    .btn-audit-export:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.35);
        color: #ffffff;
    }
    .btn-audit-refresh {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.18);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-audit-refresh:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* 4 Stat KPI Cards Row */
    .audit-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1024px) {
        .audit-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .audit-stats-grid {
            grid-template-columns: 1fr;
        }
    }
    .audit-stat-card {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 1.25rem 1.35rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
    }
    .audit-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.08);
        border-color: #CBD5E1;
    }
    .audit-stat-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.35rem;
    }
    .audit-stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: -0.025em;
        line-height: 1.1;
    }
    .audit-stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        margin-top: 0.35rem;
    }
    .audit-stat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Advance Filter Card */
    .advance-filter-card {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
        margin-bottom: 1.5rem;
    }
    .filter-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    .filter-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        font-weight: 700;
        color: #0F172A;
    }
    .filter-title svg {
        color: #1D4ED8;
    }
    .date-preset-pills {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-wrap: wrap;
    }
    .preset-pill-btn {
        padding: 0.35rem 0.8rem;
        border-radius: 8px;
        background: #F1F5F9;
        color: #475569;
        font-size: 0.8rem;
        font-weight: 600;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .preset-pill-btn:hover {
        background: #E2E8F0;
        color: #0F172A;
    }
    .preset-pill-btn.active {
        background: #1D4ED8;
        color: #ffffff;
        border-color: #1D4ED8;
        box-shadow: 0 2px 6px rgba(29, 78, 216, 0.25);
    }

    .filter-inputs-grid {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1.2fr auto;
        gap: 0.85rem;
        align-items: center;
    }
    @media (max-width: 1024px) {
        .filter-inputs-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    @media (max-width: 640px) {
        .filter-inputs-grid {
            grid-template-columns: 1fr;
        }
    }

    .search-box-wrapper {
        position: relative;
    }
    .search-box-wrapper svg.search-icon {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        pointer-events: none;
    }
    .search-input {
        width: 100%;
        height: 42px;
        padding-left: 2.35rem;
        padding-right: 2rem;
        border-radius: 10px;
        border: 1px solid #CBD5E1;
        background: #F8FAFC;
        font-size: 0.85rem;
        color: #0F172A;
        transition: all 0.15s ease;
    }
    .search-input:focus {
        background: #ffffff;
        border-color: #1D4ED8;
        outline: none;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
    }
    .search-clear-btn {
        position: absolute;
        right: 0.65rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94A3B8;
        cursor: pointer;
        padding: 4px;
        display: none;
    }
    .search-clear-btn:hover {
        color: #475569;
    }

    .filter-select {
        height: 42px;
        padding: 0 0.85rem;
        border-radius: 10px;
        border: 1px solid #CBD5E1;
        background: #F8FAFC;
        font-size: 0.85rem;
        color: #0F172A;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .filter-select:focus {
        background: #ffffff;
        border-color: #1D4ED8;
        outline: none;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
    }

    .custom-range-row {
        margin-top: 0.85rem;
        padding-top: 0.85rem;
        border-top: 1px dashed #E2E8F0;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        flex-wrap: wrap;
    }
    .date-input-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .date-input-group label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748B;
    }
    .date-picker-input {
        height: 38px;
        padding: 0 0.75rem;
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        background: #ffffff;
        font-size: 0.82rem;
        color: #0F172A;
    }
    .btn-apply-range {
        height: 38px;
        padding: 0 1rem;
        border-radius: 8px;
        background: #0F172A;
        color: #ffffff;
        font-size: 0.82rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-apply-range:hover {
        background: #1E293B;
    }
    .btn-reset-filters {
        height: 42px;
        padding: 0 1rem;
        border-radius: 10px;
        background: #F1F5F9;
        border: 1px solid #E2E8F0;
        color: #475569;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.15s ease;
    }
    .btn-reset-filters:hover {
        background: #E2E8F0;
        color: #0F172A;
    }

    /* DataTable Card & Table Styling */
    .audit-table-card {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
        position: relative;
        overflow: hidden;
    }
    .audit-table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #F1F5F9;
        flex-wrap: wrap;
        gap: 0.85rem;
    }
    .toolbar-left, .toolbar-right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .per-page-select {
        height: 34px;
        padding: 0 0.65rem;
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        font-size: 0.8rem;
        font-weight: 600;
        color: #334155;
        background: #F8FAFC;
    }

    .table-container {
        position: relative;
        overflow-x: auto;
        min-height: 320px;
    }
    .audit-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.85rem;
    }
    .audit-table th {
        background: #F8FAFC;
        padding: 0.85rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748B;
        border-bottom: 1px solid #E2E8F0;
        white-space: nowrap;
    }
    .audit-table td {
        padding: 0.95rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
        color: #334155;
    }
    .audit-table tr:hover td {
        background: #F8FAFC;
    }
    .audit-table tr:last-child td {
        border-bottom: none;
    }

    /* Badges & Pills */
    .audit-action-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        white-space: nowrap;
    }
    .badge-blue { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }
    .badge-emerald { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .badge-amber { background: #FFFBEB; color: #D97706; border: 1px solid #FDE68A; }
    .badge-rose { background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; }
    .badge-purple { background: #F5F3FF; color: #7C3AED; border: 1px solid #DDD6FE; }
    .badge-cyan { background: #ECFEFF; color: #0891B2; border: 1px solid #A5F3FC; }
    .badge-slate { background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; }

    .audit-event-id {
        font-size: 0.72rem;
        margin-top: 0.2rem;
    }
    .audit-event-id code {
        color: #64748B;
        background: #F1F5F9;
        padding: 0.1rem 0.3rem;
        border-radius: 4px;
    }

    /* Flex Utilities (Vanilla CSS) */
    .d-flex { display: flex !important; }
    .d-inline-flex { display: inline-flex !important; }
    .flex-column { flex-direction: column !important; }
    .flex-row { flex-direction: row !important; }
    .align-items-center { align-items: center !important; }
    .align-items-start { align-items: flex-start !important; }
    .justify-content-between { justify-content: space-between !important; }
    .justify-content-end { justify-content: flex-end !important; }
    .justify-content-center { justify-content: center !important; }
    .gap-1 { gap: 0.25rem !important; }
    .gap-1-5 { gap: 0.375rem !important; }
    .gap-2 { gap: 0.5rem !important; }
    .gap-2-5 { gap: 0.625rem !important; }
    .gap-3 { gap: 0.75rem !important; }

    /* Actor Cell Alignment: Avatar in front of Name & Email */
    .actor-cell-wrapper {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 0.75rem !important;
        text-align: left !important;
        min-width: 220px;
    }
    .actor-avatar-container {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        width: 38px;
        height: 38px;
    }
    .actor-avatar-img {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        min-height: 38px !important;
        max-width: 38px !important;
        max-height: 38px !important;
        border-radius: 50% !important;
        object-fit: cover !important;
        flex-shrink: 0 !important;
        display: block !important;
        border: 1.5px solid #E2E8F0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
        background: #F8FAFC;
    }
    .actor-avatar-fallback {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        min-height: 38px !important;
        border-radius: 50% !important;
        background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 60%, #4338CA 100%) !important;
        color: #ffffff !important;
        font-size: 0.78rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.02em !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        border: 1.5px solid rgba(255, 255, 255, 0.4) !important;
        box-shadow: 0 2px 5px rgba(29, 78, 216, 0.25) !important;
    }
    .actor-info {
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: flex-start !important;
        text-align: left !important;
        min-width: 0 !important;
    }
    .actor-name {
        font-size: 0.865rem !important;
        font-weight: 700 !important;
        color: #0F172A !important;
        line-height: 1.25 !important;
        white-space: nowrap !important;
    }
    .actor-email {
        font-size: 0.75rem !important;
        color: #64748B !important;
        line-height: 1.25 !important;
        margin-top: 1px !important;
        white-space: nowrap !important;
    }

    .entity-pill {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        background: #F1F5F9;
        color: #334155;
    }
    .entity-id-code {
        font-size: 0.72rem;
        margin-left: 0.3rem;
        font-family: monospace;
    }

    .ip-address-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        font-size: 0.75rem;
        font-family: monospace;
        color: #334155;
        font-weight: 600;
    }
    .ip-subnet-label {
        font-size: 0.72rem;
        margin-top: 0.15rem;
    }

    .timestamp-relative {
        font-size: 0.82rem;
        font-weight: 600;
        color: #0F172A;
    }
    .timestamp-exact {
        font-size: 0.72rem;
        color: #64748B;
    }

    .btn-inspect-diff {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        color: #1D4ED8;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-inspect-diff:hover {
        background: #EFF6FF;
        border-color: #BFDBFE;
        color: #1E40AF;
        transform: translateY(-1px);
    }

    /* Loading Overlay */
    .table-loading-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(2px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 16px;
    }
    .table-loading-overlay.active {
        display: flex;
    }
    .spinner-border-blue {
        width: 36px;
        height: 36px;
        border: 3.5px solid #E2E8F0;
        border-top-color: #1D4ED8;
        border-radius: 50%;
        animation: spin 0.75s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Server-Side Pagination */
    .audit-pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        border-top: 1px solid #F1F5F9;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .pagination-info {
        font-size: 0.82rem;
        color: #64748B;
    }
    .pagination-nav {
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .page-nav-btn, .page-num-btn {
        height: 34px;
        min-width: 34px;
        padding: 0 0.55rem;
        border-radius: 8px;
        border: 1px solid #E2E8F0;
        background: #ffffff;
        color: #334155;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        transition: all 0.15s ease;
    }
    .page-nav-btn:hover:not(.disabled), .page-num-btn:hover:not(.active) {
        background: #F8FAFC;
        border-color: #CBD5E1;
        color: #0F172A;
    }
    .page-num-btn.active {
        background: #1D4ED8;
        color: #ffffff;
        border-color: #1D4ED8;
    }
    .page-nav-btn.disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: #F8FAFC;
    }
    .pagination-ellipsis {
        padding: 0 0.4rem;
        color: #94A3B8;
        font-weight: 700;
    }

    /* Modal Backdrop & Inspector */
    .modal-backdrop-custom {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 1rem;
    }
    .modal-backdrop-custom.active {
        display: flex;
    }
    .modal-dialog-custom {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
        width: 100%;
        max-width: 780px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        border: 1px solid #E2E8F0;
    }
    .modal-header-custom {
        padding: 1.5rem 1.75rem;
        border-bottom: 1px solid #F1F5F9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #F8FAFC;
        border-radius: 20px 20px 0 0;
    }
    .modal-title-custom {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: -0.02em;
    }
    .modal-close-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #E2E8F0;
        border: none;
        color: #475569;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: all 0.15s ease;
    }
    .modal-close-btn:hover {
        background: #CBD5E1;
        color: #0F172A;
    }
    .modal-body-custom {
        padding: 1.75rem;
    }

    /* State Diff Table */
    .diff-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.82rem;
        margin-top: 1rem;
    }
    .diff-table th {
        background: #F1F5F9;
        padding: 0.65rem 0.85rem;
        font-weight: 700;
        color: #475569;
        text-align: left;
        border: 1px solid #E2E8F0;
    }
    .diff-table td {
        padding: 0.65rem 0.85rem;
        border: 1px solid #E2E8F0;
        vertical-align: top;
        font-family: monospace;
    }
    .diff-old {
        background: #FEF2F2;
        color: #991B1B;
    }
    .diff-new {
        background: #ECFDF5;
        color: #065F46;
    }
    .diff-key {
        font-weight: 700;
        color: #1E293B;
        background: #F8FAFC;
    }

    /* Export Dropdown Menu */
    .dropdown-export-menu {
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 0.5rem;
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);
        min-width: 240px;
        padding: 0.5rem;
        display: none;
        z-index: 100;
    }
    .dropdown-export-menu.show {
        display: block;
    }
    .dropdown-export-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.65rem 0.85rem;
        border-radius: 8px;
        color: #1E293B;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .dropdown-export-item:hover {
        background: #F1F5F9;
        color: #1D4ED8;
    }
    .dropdown-export-item svg {
        flex-shrink: 0;
    }
</style>

<div class="audit-page-container">

    {{-- 1. Executive Section Hero --}}
    <div class="audit-hero">
        <div class="audit-hero-glow"></div>
        <div class="audit-hero-pattern"></div>
        <div class="audit-hero-content">
            <div>
                <div class="audit-hero-badge">
                    <span class="pulse-dot-green"></span>
                    <span>IMMUTABLE AUDIT TRAIL & COMPLIANCE</span>
                </div>
                <h1 class="audit-hero-title">Security & Operational Audit Trail</h1>
                <p class="audit-hero-subtitle">
                    Server-side chronological chronicle of privileged administrative operations, authentication events, and data mutations with cryptographically signed JSON state diffs.
                </p>
            </div>
            <div class="audit-hero-actions">
                {{-- Backup Data Modal Trigger --}}
                <button type="button" class="btn-audit-backup" onclick="openBackupModal()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    <span>Backup & Retention</span>
                </button>

                {{-- Export Dropdown --}}
                <div style="position: relative;">
                    <button type="button" class="btn-audit-export" onclick="toggleExportDropdown(event)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span>Export Data</span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="dropdown-export-menu" id="exportDropdown">
                        <a href="javascript:void(0)" class="dropdown-export-item" onclick="triggerExport('csv')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            <div>
                                <div>Export CSV Spreadsheet</div>
                                <span class="text-muted" style="font-size: 0.72rem; font-weight: normal;">Microsoft Excel & CSV compatible</span>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-export-item" onclick="triggerExport('json')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1D4ED8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="m10 13-2 2 2 2"/><path d="m14 17 2-2-2-2"/></svg>
                            <div>
                                <div>Export JSON Archive</div>
                                <span class="text-muted" style="font-size: 0.72rem; font-weight: normal;">Full state diffs & signatures for audit</span>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Quick Refresh Button --}}
                <button type="button" class="btn-audit-refresh" onclick="fetchAuditLogs()" title="Refresh Telemetry">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- 2. 4 Stat KPI Cards Row --}}
    <div class="audit-stats-grid">
        {{-- Card 1: Total Events --}}
        <div class="audit-stat-card">
            <div>
                <div class="audit-stat-label">Total Audit Events</div>
                <div class="audit-stat-value" id="statTotalEvents">{{ number_format($stats['total']) }}</div>
                <div class="audit-stat-badge" style="background: #F5F3FF; color: #7C3AED;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Immutable Records</span>
                </div>
            </div>
            <div class="audit-stat-icon-box" style="background: #F5F3FF; color: #7C3AED;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
        </div>

        {{-- Card 2: Today's Activity --}}
        <div class="audit-stat-card">
            <div>
                <div class="audit-stat-label">Today's Activity</div>
                <div class="audit-stat-value" id="statTodayEvents">{{ number_format($stats['today']) }}</div>
                <div class="audit-stat-badge" style="background: #ECFDF5; color: #059669;">
                    <span class="pulse-dot-green"></span>
                    <span>Real-time Logged</span>
                </div>
            </div>
            <div class="audit-stat-icon-box" style="background: #ECFDF5; color: #059669;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
        </div>

        {{-- Card 3: Privileged Writes --}}
        <div class="audit-stat-card">
            <div>
                <div class="audit-stat-label">Privileged Writes</div>
                <div class="audit-stat-value" id="statWritesEvents">{{ number_format($stats['writes']) }}</div>
                <div class="audit-stat-badge" style="background: #FFFBEB; color: #D97706;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/></svg>
                    <span>Data Mutations</span>
                </div>
            </div>
            <div class="audit-stat-icon-box" style="background: #FFFBEB; color: #D97706;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
        </div>

        {{-- Card 4: Unique Actors & Active Nodes --}}
        <div class="audit-stat-card">
            <div>
                <div class="audit-stat-label">Active Nodes / IPs</div>
                <div class="audit-stat-value" id="statActorsIps">{{ $stats['actors'] }} / {{ $stats['ips'] }}</div>
                <div class="audit-stat-badge" style="background: #EFF6FF; color: #1D4ED8;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Zero Breaches</span>
                </div>
            </div>
            <div class="audit-stat-icon-box" style="background: #EFF6FF; color: #1D4ED8;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
        </div>
    </div>

    {{-- 3. Advance Filter Card (Day, Weeks, Month, Year, Range) --}}
    <div class="advance-filter-card">
        <div class="filter-header-row">
            <div class="filter-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                <span>Advance Audit Filter Engine</span>
            </div>

            {{-- Advance Filter Preset Pills: Day, Weeks, Month, Year, Range --}}
            <div class="date-preset-pills">
                <button type="button" class="preset-pill-btn active" data-preset="all" onclick="selectDatePreset('all')">All Time</button>
                <button type="button" class="preset-pill-btn" data-preset="today" onclick="selectDatePreset('today')">Today (Day)</button>
                <button type="button" class="preset-pill-btn" data-preset="yesterday" onclick="selectDatePreset('yesterday')">Yesterday</button>
                <button type="button" class="preset-pill-btn" data-preset="week" onclick="selectDatePreset('week')">Last 7 Days (Weeks)</button>
                <button type="button" class="preset-pill-btn" data-preset="month" onclick="selectDatePreset('month')">Last 30 Days (Month)</button>
                <button type="button" class="preset-pill-btn" data-preset="year" onclick="selectDatePreset('year')">This Year (Year)</button>
                <button type="button" class="preset-pill-btn" data-preset="custom" onclick="selectDatePreset('custom')">Custom Range</button>
            </div>
        </div>

        <div class="filter-inputs-grid">
            {{-- Search Box --}}
            <div class="search-box-wrapper">
                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="auditSearchInput" class="search-input" placeholder="Search by Action, Actor Name, Email, Entity, IP..." value="{{ request('search') }}">
                <button type="button" id="searchClearBtn" class="search-clear-btn" onclick="clearSearch()" title="Clear Search">✕</button>
            </div>

            {{-- Action Filter --}}
            <select id="actionFilterSelect" class="filter-select" onchange="handleFilterChange()">
                <option value="all">All Action Types</option>
                <option value="login">User Authentication (Logins)</option>
                <option value="profile">User Profile Updates</option>
                <option value="created">Model Insertions (Created)</option>
                <option value="updated">Model Mutations (Updated)</option>
                <option value="deleted">Model Purges (Deleted)</option>
                <option value="settings">System Settings Overrides</option>
                <option value="system">System Daemon & Cache Ops</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}">{{ strtoupper($act) }}</option>
                @endforeach
            </select>

            {{-- Entity Filter --}}
            <select id="entityFilterSelect" class="filter-select" onchange="handleFilterChange()">
                <option value="all">All Auditable Entities</option>
                @foreach($entities as $ent)
                    <option value="{{ $ent['value'] }}">{{ $ent['label'] }}</option>
                @endforeach
            </select>

            {{-- Reset Button --}}
            <button type="button" class="btn-reset-filters" onclick="resetAllFilters()" title="Reset all search filters">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                <span>Reset</span>
            </button>
        </div>

        {{-- Collapsible Custom Date Range Picker --}}
        <div class="custom-range-row" id="customDateRangeRow" style="display: none;">
            <div class="date-input-group">
                <label for="startDateInput">From Date:</label>
                <input type="date" id="startDateInput" class="date-picker-input">
            </div>
            <div class="date-input-group">
                <label for="endDateInput">To Date:</label>
                <input type="date" id="endDateInput" class="date-picker-input">
            </div>
            <button type="button" class="btn-apply-range" onclick="applyCustomRange()">Apply Range</button>
        </div>
    </div>

    {{-- 4. DataTable Card with Server-Side Pagination --}}
    <div class="audit-table-card">
        {{-- Table Toolbar --}}
        <div class="audit-table-toolbar">
            <div class="toolbar-left">
                <span class="text-muted" style="font-size: 0.8rem; font-weight: 600;">Show</span>
                <select id="perPageSelect" class="per-page-select" onchange="changePerPage(this.value)">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-muted" style="font-size: 0.8rem; font-weight: 600;">records per page</span>
            </div>
            <div class="toolbar-right">
                <span id="bulkSelectCount" style="font-size: 0.78rem; font-weight: 700; color: #1D4ED8; display: none;">0 selected</span>
            </div>
        </div>

        {{-- Table Container with Loading Spinner --}}
        <div class="table-container">
            <div class="table-loading-overlay" id="tableLoadingOverlay">
                <div class="d-flex flex-column align-items-center gap-2">
                    <div class="spinner-border-blue"></div>
                    <span style="font-size: 0.82rem; font-weight: 700; color: #1D4ED8;">Querying Server Logs...</span>
                </div>
            </div>

            <table class="audit-table">
                <thead>
                    <tr>
                        <th style="width: 44px; padding-left: 1.25rem;">
                            <input type="checkbox" id="selectAllCheckbox" class="form-check-input" onchange="toggleSelectAll(this)">
                        </th>
                        <th>Action / Event</th>
                        <th>Actor & Clearance</th>
                        <th>Target Entity</th>
                        <th>IP Address & Subnet</th>
                        <th>Timestamp (ICT)</th>
                        <th class="text-end" style="padding-right: 1.25rem;">Actions</th>
                    </tr>
                </thead>
                <tbody id="auditTableBody">
                    {!! $initialRowsHtml !!}
                </tbody>
            </table>
        </div>

        {{-- Server-Side Pagination Container --}}
        <div id="auditPaginationContainer">
            {!! $initialPaginationHtml !!}
        </div>
    </div>
</div>

{{-- 5. Deep Audit Inspector Modal --}}
<div class="modal-backdrop-custom" id="auditDetailModal" onclick="handleModalBackdropClick(event, 'auditDetailModal')">
    <div class="modal-dialog-custom">
        <div class="modal-header-custom">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span id="modalActionBadge" class="audit-action-badge badge-blue">ACTION</span>
                    <span id="modalEventId" style="font-size: 0.78rem; font-family: monospace; color: #64748B;">UUID</span>
                </div>
                <h3 class="modal-title-custom mt-1" id="modalEntityTitle">Audit Event Inspection</h3>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal('auditDetailModal')">✕</button>
        </div>
        <div class="modal-body-custom">
            {{-- Quick Overview Cards --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1rem;">
                    <div style="font-size: 0.72rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Actor Details</div>
                    <div class="actor-cell-wrapper" style="margin-top: 0.5rem;">
                        <div id="modalActorAvatarContainer" class="actor-avatar-container">
                            <div id="modalActorAvatar" class="actor-avatar-fallback">SA</div>
                        </div>
                        <div class="actor-info">
                            <div id="modalActorName" class="actor-name" style="font-size: 0.92rem;">Super Administrator</div>
                            <div id="modalActorEmail" class="actor-email">admin@schoolmeal.test</div>
                        </div>
                    </div>
                </div>

                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1rem;">
                    <div style="font-size: 0.72rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Origin & Timestamp</div>
                    <div class="mt-2" style="font-size: 0.85rem;">
                        <div>IP Node: <code id="modalIpAddress" style="color: #1D4ED8; font-weight: 700;">127.0.0.1</code></div>
                        <div class="text-muted small mt-1" id="modalTimestamp">24 Oct 2026, 04:07:56 PM</div>
                    </div>
                </div>
            </div>

            {{-- Target Entity Banner --}}
            <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; padding: 0.75rem 1rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 600; color: #1E40AF;">Target Entity Model:</span>
                    <strong id="modalAuditableType" style="color: #1D4ED8; margin-left: 0.35rem;">User</strong>
                </div>
                <div>
                    <span style="font-size: 0.75rem; font-weight: 600; color: #1E40AF;">Entity ID:</span>
                    <code id="modalAuditableId" style="color: #1E40AF; background: #DBEAFE; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;">None</code>
                </div>
            </div>

            {{-- Visual State Diff --}}
            <div>
                <div class="d-flex align-items-center justify-content-between">
                    <h4 style="font-size: 0.95rem; font-weight: 800; color: #0F172A;">State Mutation Changes (Diff)</h4>
                    <button type="button" class="btn-inspect-diff" onclick="copyModalDiffJson()">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                        <span>Copy JSON</span>
                    </button>
                </div>
                <div id="modalDiffContainer">
                    {{-- Dynamically populated --}}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 6. Backup & Retention Archive Modal --}}
<div class="modal-backdrop-custom" id="auditBackupModal" onclick="handleModalBackdropClick(event, 'auditBackupModal')">
    <div class="modal-dialog-custom" style="max-width: 600px;">
        <div class="modal-header-custom">
            <div>
                <h3 class="modal-title-custom">Audit Log Backup & Cold Storage</h3>
                <p class="text-muted small mt-0.5 mb-0">Generate immutable regulatory snapshots of audit trail data</p>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal('auditBackupModal')">✕</button>
        </div>
        <div class="modal-body-custom">
            <form id="backupAuditForm" onsubmit="handleBackupSubmit(event)">
                <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 12px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
                    <div class="d-flex align-items-start gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2.5" style="flex-shrink: 0; margin-top: 2px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <div style="font-size: 0.8rem; color: #166534; line-height: 1.45;">
                            Audit records are exported with cryptographic timestamps. Once exported, you can preserve them in compliance cold storage or offsite institutional archives.
                        </div>
                    </div>
                </div>

                {{-- Retention Cutoff Selection --}}
                <div class="mb-3">
                    <label class="font-bold text-slate-800 small mb-1.5 d-block">Backup Retention Scope</label>
                    <select id="backupCutoffSelect" class="filter-select w-100" onchange="toggleBackupCustomDates(this.value)">
                        <option value="all">All Historical Records (Complete Platform Trail)</option>
                        <option value="30_days">Logs Older than 30 Days</option>
                        <option value="60_days">Logs Older than 60 Days</option>
                        <option value="90_days" selected>Logs Older than 90 Days (Quarterly Audit Archive)</option>
                        <option value="180_days">Logs Older than 180 Days (Semi-Annual Policy)</option>
                        <option value="365_days">Logs Older than 1 Year (Annual Retention)</option>
                        <option value="custom">Custom Date Range...</option>
                    </select>
                </div>

                {{-- Custom Date Range for Backup (Hidden by default) --}}
                <div id="backupCustomDatesWrapper" style="display: none; margin-bottom: 1rem; background: #F8FAFC; padding: 0.85rem; border-radius: 10px; border: 1px solid #E2E8F0;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label class="small text-muted font-semibold mb-1 d-block">Start Date:</label>
                            <input type="date" id="backupStartDate" class="date-picker-input w-100">
                        </div>
                        <div>
                            <label class="small text-muted font-semibold mb-1 d-block">End Date:</label>
                            <input type="date" id="backupEndDate" class="date-picker-input w-100">
                        </div>
                    </div>
                </div>

                {{-- Format Selection --}}
                <div class="mb-3">
                    <label class="font-bold text-slate-800 small mb-1.5 d-block">Backup Archive Format</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <label style="border: 2px solid #10B981; border-radius: 10px; padding: 0.75rem; background: #F0FDF4; cursor: pointer; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <input type="radio" name="backup_format" value="json" checked style="margin-top: 3px;">
                            <div>
                                <strong style="font-size: 0.82rem; color: #065F46; display: block;">JSON Compliance Archive</strong>
                                <span class="text-muted" style="font-size: 0.72rem;">Full state before/after diffs & signatures</span>
                            </div>
                        </label>
                        <label style="border: 1px solid #CBD5E1; border-radius: 10px; padding: 0.75rem; background: #ffffff; cursor: pointer; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <input type="radio" name="backup_format" value="csv" style="margin-top: 3px;">
                            <div>
                                <strong style="font-size: 0.82rem; color: #1E293B; display: block;">CSV Spreadsheet</strong>
                                <span class="text-muted" style="font-size: 0.72rem;">Table format for Excel & Business Intelligence</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Safety Purge Checkbox --}}
                <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 0.75rem; margin-bottom: 1.5rem;">
                    <label class="d-flex align-items-center gap-2" style="cursor: pointer;">
                        <input type="checkbox" id="backupPurgeCheckbox" class="form-check-input">
                        <span style="font-size: 0.78rem; font-weight: 700; color: #92400E;">
                            Purge backed-up logs from live database after download completes
                        </span>
                    </label>
                    <span class="text-muted d-block mt-1" style="font-size: 0.72rem; margin-left: 1.5rem;">
                        Optional: Free up operational database storage by archiving older logs into cold storage.
                    </span>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2">
                    <button type="button" class="btn-reset-filters" onclick="closeModal('auditBackupModal')">Cancel</button>
                    <button type="submit" class="btn-audit-backup" id="btnSubmitBackup">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span>Download Backup Archive</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // State management for high-performance server-side data table
    const auditState = {
        page: 1,
        per_page: 25,
        search: "{{ request('search') }}",
        action_filter: 'all',
        entity_filter: 'all',
        date_preset: 'all',
        start_date: '',
        end_date: '',
        sort_by: 'created_at',
        sort_dir: 'desc',
    };

    let searchDebounceTimer = null;
    let currentModalLog = null;

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('auditSearchInput');
        const clearBtn = document.getElementById('searchClearBtn');

        if (searchInput.value.trim().length > 0) {
            clearBtn.style.display = 'block';
        }

        searchInput.addEventListener('input', function () {
            clearBtn.style.display = this.value.trim().length > 0 ? 'block' : 'none';
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                auditState.search = searchInput.value.trim();
                auditState.page = 1;
                fetchAuditLogs();
            }, 350);
        });

        // Close export dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('exportDropdown');
            if (dropdown && !e.target.closest('.btn-audit-export') && !e.target.closest('#exportDropdown')) {
                dropdown.classList.remove('show');
            }
        });
    });

    function toggleExportDropdown(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('exportDropdown');
        dropdown.classList.toggle('show');
    }

    function clearSearch() {
        const searchInput = document.getElementById('auditSearchInput');
        searchInput.value = '';
        document.getElementById('searchClearBtn').style.display = 'none';
        auditState.search = '';
        auditState.page = 1;
        fetchAuditLogs();
    }

    function selectDatePreset(preset) {
        document.querySelectorAll('.preset-pill-btn').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-preset') === preset);
        });

        auditState.date_preset = preset;
        auditState.page = 1;

        const customRangeRow = document.getElementById('customDateRangeRow');
        if (preset === 'custom') {
            customRangeRow.style.display = 'flex';
        } else {
            customRangeRow.style.display = 'none';
            auditState.start_date = '';
            auditState.end_date = '';
            fetchAuditLogs();
        }
    }

    function applyCustomRange() {
        const start = document.getElementById('startDateInput').value;
        const end = document.getElementById('endDateInput').value;

        if (!start && !end) {
            if (window.Toast) window.Toast.warning('Please select a start or end date');
            return;
        }

        auditState.start_date = start;
        auditState.end_date = end;
        auditState.date_preset = 'custom';
        auditState.page = 1;
        fetchAuditLogs();
    }

    function handleFilterChange() {
        auditState.action_filter = document.getElementById('actionFilterSelect').value;
        auditState.entity_filter = document.getElementById('entityFilterSelect').value;
        auditState.page = 1;
        fetchAuditLogs();
    }

    function changePerPage(perPage) {
        auditState.per_page = parseInt(perPage, 10);
        auditState.page = 1;
        fetchAuditLogs();
    }

    function changePage(page) {
        if (page < 1) return;
        auditState.page = page;
        fetchAuditLogs();
    }

    function resetAllFilters() {
        auditState.search = '';
        auditState.action_filter = 'all';
        auditState.entity_filter = 'all';
        auditState.date_preset = 'all';
        auditState.start_date = '';
        auditState.end_date = '';
        auditState.page = 1;

        document.getElementById('auditSearchInput').value = '';
        document.getElementById('searchClearBtn').style.display = 'none';
        document.getElementById('actionFilterSelect').value = 'all';
        document.getElementById('entityFilterSelect').value = 'all';
        document.getElementById('customDateRangeRow').style.display = 'none';
        document.getElementById('startDateInput').value = '';
        document.getElementById('endDateInput').value = '';

        document.querySelectorAll('.preset-pill-btn').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-preset') === 'all');
        });

        fetchAuditLogs();
        if (window.Toast) window.Toast.info('Audit filters reset to default');
    }

    /**
     * Core AJAX Server-Side Fetcher
     */
    function fetchAuditLogs() {
        const overlay = document.getElementById('tableLoadingOverlay');
        if (overlay) overlay.classList.add('active');

        const params = new URLSearchParams();
        params.append('page', auditState.page);
        params.append('per_page', auditState.per_page);
        if (auditState.search) params.append('search', auditState.search);
        if (auditState.action_filter !== 'all') params.append('action_filter', auditState.action_filter);
        if (auditState.entity_filter !== 'all') params.append('entity_filter', auditState.entity_filter);
        if (auditState.date_preset !== 'all') params.append('date_preset', auditState.date_preset);
        if (auditState.start_date) params.append('start_date', auditState.start_date);
        if (auditState.end_date) params.append('end_date', auditState.end_date);
        params.append('sort_by', auditState.sort_by);
        params.append('sort_dir', auditState.sort_dir);

        fetch(`/admin/audit-logs?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Server returned ' + res.status);
            return res.json();
        })
        .then(data => {
            if (overlay) overlay.classList.remove('active');
            if (data.html !== undefined) {
                document.getElementById('auditTableBody').innerHTML = data.html;
            }
            if (data.pagination_html !== undefined) {
                document.getElementById('auditPaginationContainer').innerHTML = data.pagination_html;
            }

            // Update stats if provided
            if (data.stats) {
                if (data.stats.total_count !== undefined) {
                    document.getElementById('statTotalEvents').textContent = new Intl.NumberFormat().format(data.stats.total_count);
                }
                if (data.stats.today_count !== undefined) {
                    document.getElementById('statTodayEvents').textContent = new Intl.NumberFormat().format(data.stats.today_count);
                }
                if (data.stats.writes_count !== undefined) {
                    document.getElementById('statWritesEvents').textContent = new Intl.NumberFormat().format(data.stats.writes_count);
                }
            }

            // Reset select-all checkbox
            const selectAll = document.getElementById('selectAllCheckbox');
            if (selectAll) selectAll.checked = false;
            updateBulkSelectCount();
        })
        .catch(err => {
            if (overlay) overlay.classList.remove('active');
            console.error('Audit Log Fetch Error:', err);
            if (window.Toast) {
                window.Toast.error('Failed to load audit logs. Please refresh.');
            }
        });
    }

    /**
     * Trigger CSV or JSON download with current active filters
     */
    function triggerExport(format) {
        document.getElementById('exportDropdown').classList.remove('show');

        const params = new URLSearchParams();
        params.append('format', format);
        if (auditState.search) params.append('search', auditState.search);
        if (auditState.action_filter !== 'all') params.append('action_filter', auditState.action_filter);
        if (auditState.entity_filter !== 'all') params.append('entity_filter', auditState.entity_filter);
        if (auditState.date_preset !== 'all') params.append('date_preset', auditState.date_preset);
        if (auditState.start_date) params.append('start_date', auditState.start_date);
        if (auditState.end_date) params.append('end_date', auditState.end_date);

        if (window.Toast) {
            window.Toast.info(`Generating ${format.toUpperCase()} export file...`);
        }

        window.location.href = `/admin/audit-logs/export?${params.toString()}`;
    }

    /**
     * Row Selection Management
     */
    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.audit-select-row');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateBulkSelectCount();
    }

    function handleRowSelect(checkbox) {
        updateBulkSelectCount();
    }

    function updateBulkSelectCount() {
        const checked = document.querySelectorAll('.audit-select-row:checked');
        const countSpan = document.getElementById('bulkSelectCount');
        if (checked.length > 0) {
            countSpan.style.display = 'inline';
            countSpan.textContent = `${checked.length} record(s) selected`;
        } else {
            countSpan.style.display = 'none';
        }
    }

    /**
     * Deep Audit Inspector Modal
     */
    function openAuditDetailModal(id) {
        fetch(`/admin/audit-logs/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.log) {
                throw new Error('Log not found');
            }
            const log = data.log;
            currentModalLog = log;

            document.getElementById('modalActionBadge').textContent = log.action.toUpperCase();
            document.getElementById('modalEventId').textContent = log.id;
            document.getElementById('modalEntityTitle').textContent = `Audited Operation: ${log.action}`;
            document.getElementById('modalActorName').textContent = log.actor.name;
            document.getElementById('modalActorEmail').textContent = log.actor.email || 'N/A';
            document.getElementById('modalIpAddress').textContent = log.ip_address || '127.0.0.1';
            document.getElementById('modalTimestamp').textContent = `${log.created_at} (${log.created_at_relative})`;
            document.getElementById('modalAuditableType').textContent = log.auditable_type || 'System Target';
            document.getElementById('modalAuditableId').textContent = log.auditable_id || 'N/A';

            // Actor Avatar
            const avatarContainer = document.getElementById('modalActorAvatarContainer');
            if (avatarContainer) {
                if (log.actor.avatar) {
                    avatarContainer.innerHTML = `<img src="${log.actor.avatar}" alt="${log.actor.name}" class="actor-avatar-img" onerror="this.outerHTML='<div class=\\'actor-avatar-fallback\\'>${(log.actor.name || 'SA').substring(0, 2).toUpperCase()}</div>'">`;
                } else {
                    const initials = (log.actor.name || 'SA').substring(0, 2).toUpperCase();
                    avatarContainer.innerHTML = `<div class="actor-avatar-fallback">${initials}</div>`;
                }
            }

            // Build Diff Table
            const diffContainer = document.getElementById('modalDiffContainer');
            if (log.diff && log.diff.length > 0) {
                let html = `
                    <table class="diff-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Attribute Key</th>
                                <th style="width: 37%;">Original Value (Old)</th>
                                <th style="width: 38%;">Mutated Value (New)</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                log.diff.forEach(item => {
                    const oldStr = item.old !== null ? (typeof item.old === 'object' ? JSON.stringify(item.old, null, 2) : String(item.old)) : '<span style="color:#94a3b8;">(empty / unset)</span>';
                    const newStr = item.new !== null ? (typeof item.new === 'object' ? JSON.stringify(item.new, null, 2) : String(item.new)) : '<span style="color:#94a3b8;">(empty / unset)</span>';
                    html += `
                        <tr>
                            <td class="diff-key">${item.key}</td>
                            <td class="diff-old">${oldStr}</td>
                            <td class="diff-new">${newStr}</td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                diffContainer.innerHTML = html;
            } else {
                diffContainer.innerHTML = `
                    <div style="background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 10px; padding: 1.5rem; text-align: center; color: #64748B; font-size: 0.85rem; margin-top: 1rem;">
                        No explicit model field mutations recorded for this event. Event represents an access authentication or system-level trigger.
                    </div>
                `;
            }

            openModal('auditDetailModal');
        })
        .catch(err => {
            console.error(err);
            if (window.Toast) window.Toast.error('Failed to load audit event details');
        });
    }

    function copyModalDiffJson() {
        if (!currentModalLog) return;
        const payload = JSON.stringify(currentModalLog, null, 2);
        navigator.clipboard.writeText(payload).then(() => {
            if (window.Toast) window.Toast.success('Audit log JSON copied to clipboard');
        }).catch(() => {
            if (window.Toast) window.Toast.error('Could not copy JSON to clipboard');
        });
    }

    /**
     * Backup Modal Handling
     */
    function openBackupModal() {
        openModal('auditBackupModal');
    }

    function toggleBackupCustomDates(val) {
        document.getElementById('backupCustomDatesWrapper').style.display = val === 'custom' ? 'block' : 'none';
    }

    function handleBackupSubmit(e) {
        e.preventDefault();

        const cutoff = document.getElementById('backupCutoffSelect').value;
        const format = document.querySelector('input[name="backup_format"]:checked').value;
        const purge = document.getElementById('backupPurgeCheckbox').checked;
        const startDate = document.getElementById('backupStartDate').value;
        const endDate = document.getElementById('backupEndDate').value;

        if (purge) {
            const confirmed = confirm('WARNING: You have selected to purge backed-up logs from the database. Are you sure you wish to continue?');
            if (!confirmed) return;
        }

        if (window.Toast) {
            window.Toast.info('Preparing audit backup archive...');
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/audit-logs/backup';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const cutoffInput = document.createElement('input');
        cutoffInput.type = 'hidden';
        cutoffInput.name = 'cutoff';
        cutoffInput.value = cutoff;
        form.appendChild(cutoffInput);

        const formatInput = document.createElement('input');
        formatInput.type = 'hidden';
        formatInput.name = 'format';
        formatInput.value = format;
        form.appendChild(formatInput);

        const purgeInput = document.createElement('input');
        purgeInput.type = 'hidden';
        purgeInput.name = 'purge_after_backup';
        purgeInput.value = purge ? '1' : '0';
        form.appendChild(purgeInput);

        if (cutoff === 'custom') {
            const startInput = document.createElement('input');
            startInput.type = 'hidden';
            startInput.name = 'start_date';
            startInput.value = startDate;
            form.appendChild(startInput);

            const endInput = document.createElement('input');
            endInput.type = 'hidden';
            endInput.name = 'end_date';
            endInput.value = endDate;
            form.appendChild(endInput);
        }

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);

        closeModal('auditBackupModal');

        setTimeout(() => {
            if (purge) {
                fetchAuditLogs();
                if (window.Toast) window.Toast.success('Backup archive downloaded and live database purged');
            } else {
                if (window.Toast) window.Toast.success('Audit backup archive generated successfully');
            }
        }, 1500);
    }

    /**
     * Modal Helper Functions
     */
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('active');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('active');
    }

    function handleModalBackdropClick(e, id) {
        if (e.target.id === id) {
            closeModal(id);
        }
    }
</script>
@endsection
