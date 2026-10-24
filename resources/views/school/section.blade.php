@extends('layouts.master_school_admin')

@section('title', ($title ?? 'School Management') . ' | ' . ($activeSchool->name ?? 'Wat Bo Primary School'))

@section('content')
<style>
    .school-section-hero {
        position: relative;
        background: linear-gradient(135deg, #091224 0%, #0d2144 40%, #0f3567 75%, #059669 100%);
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
        background: radial-gradient(circle, rgba(0, 102, 255, 0.35) 0%, rgba(16, 185, 129, 0.2) 50%, rgba(0, 102, 255, 0) 70%);
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
        background: rgba(0, 102, 255, 0.25);
        color: #93c5fd;
        border: 1px solid rgba(147, 197, 253, 0.3);
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
    .btn-school-hero {
        background: #0066FF;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.65rem 1.25rem;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 14px rgba(0, 102, 255, 0.35);
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-school-hero:hover {
        background: #0052CC;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(0, 102, 255, 0.45);
    }
    .btn-school-ghost {
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
    .btn-school-ghost:hover {
        background: rgba(255, 255, 255, 0.16);
        color: #ffffff;
    }

    /* Stat Cards */
    .section-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem 1.35rem;
        box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 110px;
        transition: all 0.2s ease;
    }
    .section-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.08);
    }
    .stat-meta-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .stat-meta-val {
        font-size: 1.75rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
        margin-top: 0.25rem;
    }
    .stat-meta-change {
        font-size: 0.725rem;
        font-weight: 700;
        margin-top: 0.35rem;
    }

    /* Table & Card */
    .section-table-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }
    .section-table-header {
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .section-table-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .section-table-search {
        position: relative;
        max-width: 280px;
        width: 100%;
    }
    .section-table-search input {
        width: 100%;
        padding: 0.45rem 0.85rem 0.45rem 2.2rem;
        border-radius: 9999px;
        border: 1px solid #cbd5e1;
        font-size: 0.8rem;
        background: #f8fafc;
        outline: none;
    }
    .section-table-search input:focus {
        background: #ffffff;
        border-color: #0066FF;
        box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.12);
    }
    .search-icon-svg {
        position: absolute;
        left: 0.8rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }
    .school-data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.825rem;
    }
    .school-data-table th {
        background: #f8fafc;
        padding: 0.85rem 1.25rem;
        font-size: 0.725rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .school-data-table td {
        padding: 0.95rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
    }
    .school-data-table tr:hover td {
        background: #f8fafc;
    }
    .status-badge-school {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.725rem;
        font-weight: 700;
    }
    .status-verified {
        background: #dcfce7;
        color: #15803d;
    }
    .status-active {
        background: #e0f2fe;
        color: #0369a1;
    }
    .status-pending {
        background: #fef3c7;
        color: #b45309;
    }
</style>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Hero Banner -->
    <div class="school-section-hero">
        <div class="section-hero-glow"></div>
        <div class="section-hero-pattern"></div>
        <div class="section-hero-content">
            <div>
                <div class="section-hero-badge">
                    <span>{{ $badge ?? 'CAMPUS GOVERNANCE' }}</span>
                </div>
                <h1 class="section-hero-title">{{ $title ?? 'School Management' }}</h1>
                <p class="section-hero-subtitle">{{ $subtitle ?? 'Operational coordination and records for ' . ($activeSchool->name ?? 'Wat Bo Primary School') }}</p>
            </div>

            <div class="section-hero-actions">
                @if(($currentSection ?? '') === 'shifts')
                    <button type="button" class="btn-school-hero" onclick="openShiftModal()">+ Add Operating Shift</button>
                @elseif(($currentSection ?? '') === 'locations')
                    <button type="button" class="btn-school-hero" onclick="openLocationModal()">+ Add Drop Location</button>
                @elseif(($currentSection ?? '') === 'announcements')
                    <button type="button" class="btn-school-hero" onclick="openAnnouncementModal()">+ New Announcement</button>
                @elseif(($currentSection ?? '') === 'support')
                    <button type="button" class="btn-school-hero" onclick="openSupportModal()">+ Submit Inquiry</button>
                @else
                    <a href="{{ route('school.students') }}" class="btn-school-hero">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>View Students Roster</span>
                    </a>
                @endif

                <button type="button" class="btn-school-ghost" onclick="window.location.reload()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 4 KPI Metrics Grid -->
    @if(!empty($stats))
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
            @foreach($stats as $st)
                <div class="section-stat-card">
                    <div>
                        <div class="stat-meta-label">{{ $st['label'] }}</div>
                        <div class="stat-meta-val">{{ $st['value'] }}</div>
                    </div>
                    <div class="stat-meta-change" style="color: {{ ($st['color'] ?? 'blue') === 'emerald' ? '#10b981' : (($st['color'] ?? 'blue') === 'rose' ? '#ef4444' : '#2563eb') }};">
                        {{ $st['change'] }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Data Table Card -->
    <div class="section-table-box">
        <div class="section-table-header">
            <h3 class="section-table-title">{{ $tableTitle ?? 'Records & Logs' }}</h3>

            <div class="section-table-search">
                <svg class="search-icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="sectionSearchInput" placeholder="Filter records..." onkeyup="filterSectionTable()">
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="school-data-table" id="sectionDataTable">
                <thead>
                    <tr>
                        @foreach($columns ?? ['Title', 'Description', 'Attributes', 'Status', 'Timestamp'] as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="sectionDataTableBody">
                    @forelse($rows ?? [] as $r)
                        <tr class="section-data-row">
                            <td>
                                <strong style="color: #0f172a; display: block;">{{ $r['title'] ?? 'N/A' }}</strong>
                                <span style="font-size: 0.75rem; color: #64748b;">{{ $r['subtitle'] ?? '' }}</span>
                            </td>
                            <td>{{ $r['contact'] ?? 'N/A' }}</td>
                            <td>{{ $r['roles'] ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $stVal = strtolower($r['status'] ?? 'active');
                                    $bClass = $stVal === 'verified' ? 'status-verified' : ($stVal === 'pending' ? 'status-pending' : 'status-active');
                                @endphp
                                <span class="status-badge-school {{ $bClass }}">
                                    ● {{ ucfirst($r['status'] ?? 'active') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">
                                    {{ $r['timestamp'] ?? 'N/A' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                                No records currently available for this section.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function filterSectionTable() {
        const query = document.getElementById('sectionSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.section-data-row');
        rows.forEach(r => {
            const txt = r.textContent.toLowerCase();
            r.style.display = txt.includes(query) ? '' : 'none';
        });
    }

    function openShiftModal() {
        if (window.Toast) window.Toast.info('Shift configuration panel ready.');
    }
    function openLocationModal() {
        if (window.Toast) window.Toast.info('Drop station registration panel ready.');
    }
    function openAnnouncementModal() {
        if (window.Toast) window.Toast.info('Campus broadcast editor ready.');
    }
    function openSupportModal() {
        if (window.Toast) window.Toast.info('Guardian support ticketing form ready.');
    }
</script>
@endsection
