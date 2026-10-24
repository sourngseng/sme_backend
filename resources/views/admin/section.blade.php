@extends('layouts.master_super_admin')

@section('title', ($title ?? 'Admin Management') . ' | SchoolMeal Super Admin')

@section('content')
<style>
    /* Executive Section Hero */
    .section-hero {
        position: relative;
        background: linear-gradient(135deg, #090d16 0%, #0f172a 35%, #1e1b4b 75%, #064e3b 100%);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 16px 40px -10px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.08) inset;
        border-radius: 20px;
        padding: 2rem 2rem 1.75rem 2rem;
        color: #ffffff;
        overflow: hidden;
        margin-bottom: 1.75rem;
    }
    .section-hero-glow {
        position: absolute;
        top: -60px;
        right: -60px;
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, rgba(16, 185, 129, 0.15) 50%, rgba(99, 102, 241, 0) 70%);
        pointer-events: none;
        border-radius: 50%;
    }
    .section-hero-pattern {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1.5px, transparent 1.5px);
        background-size: 24px 24px;
        pointer-events: none;
        opacity: 0.6;
    }
    .section-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    .section-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        background: rgba(99, 102, 241, 0.25);
        color: #a5b4fc;
        border: 1px solid rgba(165, 180, 252, 0.3);
        margin-bottom: 0.5rem;
    }
    .section-hero-title {
        font-size: 1.65rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0 0 0.35rem 0;
        color: #ffffff;
    }
    .section-hero-subtitle {
        font-size: 0.875rem;
        color: #cbd5e1;
        margin: 0;
        max-width: 620px;
        line-height: 1.45;
    }
    .section-hero-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .btn-hero-primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.65rem 1.25rem;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-hero-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
        color: #ffffff;
    }
    .btn-hero-secondary {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.65rem 1.15rem;
        border-radius: 10px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-hero-secondary:hover {
        background: rgba(255, 255, 255, 0.16);
        color: #ffffff;
    }

    /* Metric KPI Cards Grid */
    .section-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.15rem 1.25rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
    }
    .stat-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }
    .stat-kpi-label {
        font-size: 0.775rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.35rem;
    }
    .stat-kpi-value {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .stat-kpi-trend {
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        margin-top: 0.5rem;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        width: fit-content;
    }
    .trend-blue { background: #eff6ff; color: #1d4ed8; }
    .trend-emerald { background: #ecfdf5; color: #047857; }
    .trend-amber { background: #fffbeb; color: #b45309; }
    .trend-purple { background: #faf5ff; color: #6d28d9; }
    .trend-rose { background: #fff1f2; color: #be123c; }

    /* Filter & Table Container */
    .table-container-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .table-toolbar {
        padding: 1.15rem 1.35rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        border-bottom: 1px solid #f1f5f9;
        background: #fcfdfe;
    }
    .table-toolbar-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
        min-width: 260px;
    }
    .table-search-box {
        position: relative;
        flex: 1;
        max-width: 380px;
    }
    .table-search-input {
        width: 100%;
        padding: 0.55rem 0.85rem 0.55rem 2.25rem;
        font-size: 0.85rem;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        color: #0f172a;
        transition: all 0.2s ease;
    }
    .table-search-input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    .table-search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }
    .table-toolbar-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .filter-btn {
        padding: 0.5rem 0.85rem;
        font-size: 0.825rem;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        border-radius: 8px;
        color: #475569;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.15s ease;
    }
    .filter-btn:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    /* Modern Data Table */
    .section-data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.825rem;
        text-align: left;
    }
    .section-data-table thead tr {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .section-data-table th {
        padding: 0.75rem 1.25rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }
    .section-data-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s ease;
    }
    .section-data-table tbody tr:hover {
        background: #f8fafc;
    }
    .section-data-table td {
        padding: 0.85rem 1.25rem;
        vertical-align: middle;
        color: #1e293b;
    }
    .cell-title {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.15rem;
    }
    .cell-subtitle {
        font-size: 0.75rem;
        color: #64748b;
    }
    .cell-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .cell-badge-active { background: #dcfce7; color: #15803d; }
    .cell-badge-verified { background: #e0e7ff; color: #4338ca; }
    .cell-badge-pending { background: #fef3c7; color: #b45309; }
    .cell-badge-suspended { background: #fee2e2; color: #b91c1c; }

    .table-footer {
        padding: 0.9rem 1.35rem;
        background: #fcfdfe;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.8rem;
        color: #64748b;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
</style>

<!-- Hero Banner -->
<div class="section-hero">
    <div class="section-hero-glow"></div>
    <div class="section-hero-pattern"></div>
    <div class="section-hero-content">
        <div>
            <div class="section-hero-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>
                {{ $badge ?? 'SUPER ADMIN PORTAL' }}
            </div>
            <h1 class="section-hero-title">{{ $title ?? 'Section Title' }}</h1>
            <p class="section-hero-subtitle">{{ $subtitle ?? 'Enterprise governance, real-time records and operational monitoring.' }}</p>
        </div>
        <div class="section-hero-actions">
            <button type="button" class="btn-hero-secondary" onclick="window.Toast.info('Refreshing live {{ $title ?? 'module' }} records...'); setTimeout(() => location.reload(), 600);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                Sync Data
            </button>
            <button type="button" class="btn-hero-primary" onclick="window.Toast.success('Exporting {{ $title ?? 'records' }} CSV dataset to downloads...');">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export Dataset
            </button>
        </div>
    </div>
</div>

<!-- KPI Cards Grid -->
@if(!empty($stats))
<div class="section-stats-grid">
    @foreach($stats as $st)
    <div class="stat-kpi-card">
        <div>
            <div class="stat-kpi-label">{{ $st['label'] }}</div>
            <div class="stat-kpi-value">{{ $st['value'] }}</div>
        </div>
        <div class="stat-kpi-trend trend-{{ $st['color'] ?? 'blue' }}">
            {{ $st['change'] ?? 'Up to date' }}
        </div>
    </div>
    @endforeach
</div>
@endif

<!-- Data Table Card -->
<div class="table-container-card">
    <div class="table-toolbar">
        <div class="table-toolbar-left">
            <div class="table-search-box">
                <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="table-search-input" id="tableFilterInput" placeholder="Search {{ strtolower($title ?? 'records') }}..." oninput="filterTableRows(this.value)">
            </div>
            <button type="button" class="filter-btn" onclick="window.Toast.info('Filters applied: Active status only')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filter
            </button>
        </div>
        <div class="table-toolbar-right">
            <span style="font-size: 0.8rem; font-weight: 700; color: #475569;">
                Showing {{ count($rows ?? []) }} records
            </span>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="section-data-table" id="sectionDataTable">
            <thead>
                <tr>
                    @foreach($columns ?? ['Item', 'Details', 'Attributes', 'Status', 'Timestamp'] as $col)
                    <th>{{ $col }}</th>
                    @endforeach
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows ?? [] as $row)
                <tr class="data-row">
                    <td>
                        <div class="cell-title">{{ $row['title'] ?? 'N/A' }}</div>
                        <div class="cell-subtitle">{{ $row['subtitle'] ?? '' }}</div>
                    </td>
                    <td>{{ $row['contact'] ?? '—' }}</td>
                    <td>{{ $row['roles'] ?? '—' }}</td>
                    <td>
                        @php
                            $st = strtolower($row['status'] ?? 'active');
                            $badgeClass = match($st) {
                                'verified', 'completed' => 'cell-badge-verified',
                                'active', 'online' => 'cell-badge-active',
                                'pending', 'preparing', 'open' => 'cell-badge-pending',
                                default => 'cell-badge-suspended',
                            };
                        @endphp
                        <span class="cell-badge {{ $badgeClass }}">{{ $row['status'] ?? 'Active' }}</span>
                    </td>
                    <td style="color: #64748b; font-size: 0.8rem;">{{ $row['timestamp'] ?? '—' }}</td>
                    <td style="text-align: right; white-space: nowrap;">
                        <button type="button" class="filter-btn" style="padding: 0.35rem 0.65rem; font-size: 0.75rem;" onclick="window.Toast.info('Viewing details for {{ addslashes($row['title'] ?? 'Record') }}')">
                            View
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 0.5rem; color: #cbd5e1;"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                        <div style="font-weight: 600; font-size: 0.9rem;">No records found</div>
                        <div style="font-size: 0.775rem;">New entries will appear here in real-time.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-footer">
        <div>Platform Timezone: <strong>Asia/Phnom_Penh (UTC+7)</strong></div>
        <div style="display: flex; gap: 0.5rem;">
            <button type="button" class="filter-btn" style="padding: 0.35rem 0.75rem;" onclick="window.Toast.info('Navigating to previous page')">Previous</button>
            <button type="button" class="filter-btn" style="padding: 0.35rem 0.75rem; background: #f8fafc;" onclick="window.Toast.info('Navigating to next page')">Next</button>
        </div>
    </div>
</div>

<script>
function filterTableRows(query) {
    const q = query.toLowerCase().trim();
    const rows = document.querySelectorAll('#sectionDataTable tbody tr.data-row');
    rows.forEach(r => {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>
@endsection
