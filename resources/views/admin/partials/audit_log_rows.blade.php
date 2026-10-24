@forelse($logs as $log)
    @php
        $action = strtolower($log->action ?? 'access');
        $badgeClass = 'badge-slate';
        $badgeIcon = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';
        
        if (str_contains($action, 'login')) {
            $badgeClass = 'badge-blue';
            $badgeIcon = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>';
        } elseif (str_contains($action, 'creat')) {
            $badgeClass = 'badge-emerald';
            $badgeIcon = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>';
        } elseif (str_contains($action, 'updat') || str_contains($action, 'edit')) {
            $badgeClass = 'badge-amber';
            $badgeIcon = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
        } elseif (str_contains($action, 'delet') || str_contains($action, 'purge')) {
            $badgeClass = 'badge-rose';
            $badgeIcon = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>';
        } elseif (str_contains($action, 'setting')) {
            $badgeClass = 'badge-purple';
            $badgeIcon = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>';
        } elseif (str_contains($action, 'cache')) {
            $badgeClass = 'badge-cyan';
            $badgeIcon = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>';
        }

        $entityName = $log->auditable_type ? class_basename($log->auditable_type) : 'System';
        $actorName = $log->actor?->full_name ?? ($log->actor_id ? 'User #' . substr($log->actor_id, 0, 8) : 'System Worker');
        $actorEmail = $log->actor?->email ?? ($log->actor_id ? 'User ID: ' . substr($log->actor_id, 0, 8) : 'automated-system@platform');
        $initials = strtoupper(substr($actorName, 0, 2));
    @endphp
    <tr class="audit-row" data-id="{{ $log->id }}">
        <td class="text-center" style="width: 44px; padding-left: 1.25rem;">
            <input type="checkbox" class="audit-select-row form-check-input" value="{{ $log->id }}" onchange="handleRowSelect(this)">
        </td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <span class="audit-action-badge {{ $badgeClass }}">
                    {!! $badgeIcon !!}
                    <span>{{ strtoupper($log->action) }}</span>
                </span>
            </div>
            <div class="audit-event-id text-muted" title="{{ $log->id }}">
                ID: <code>{{ substr($log->id, 0, 8) }}...{{ substr($log->id, -4) }}</code>
            </div>
        </td>
        <td>
            <div class="actor-cell-wrapper">
                <div class="actor-avatar-container">
                    @if($log->actor && $log->actor->avatar)
                        <img src="{{ asset($log->actor->avatar) }}" alt="{{ $actorName }}" class="actor-avatar-img" onerror="this.outerHTML='<div class=\'actor-avatar-fallback\'>{{ $initials }}</div>'">
                    @else
                        <div class="actor-avatar-fallback">{{ $initials }}</div>
                    @endif
                </div>
                <div class="actor-info">
                    <div class="actor-name font-semibold text-slate-900">{{ $actorName }}</div>
                    <div class="actor-email text-muted small">{{ $actorEmail }}</div>
                </div>
            </div>
        </td>
        <td>
            <div class="entity-badge-wrapper">
                <span class="entity-pill entity-{{ strtolower($entityName) }}">
                    {{ $entityName }}
                </span>
                @if($log->auditable_id)
                    <span class="entity-id-code text-muted" title="{{ $log->auditable_id }}">
                        #{{ substr($log->auditable_id, 0, 8) }}
                    </span>
                @endif
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <span class="ip-address-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    <code>{{ $log->ip_address ?? '127.0.0.1' }}</code>
                </span>
                <span class="ip-subnet-label text-muted">
                    {{ ($log->ip_address === '127.0.0.1' || $log->ip_address === '::1') ? 'Loopback Node' : 'Client Gateway' }}
                </span>
            </div>
        </td>
        <td>
            <div class="timestamp-box">
                <div class="timestamp-relative font-semibold text-slate-800">
                    {{ $log->created_at?->diffForHumans() ?? 'Just now' }}
                </div>
                <div class="timestamp-exact text-muted small" title="Cambodia Time (ICT)">
                    {{ $log->created_at?->timezone('Asia/Phnom_Penh')->format('d M Y, h:i:s A') ?? 'N/A' }}
                </div>
            </div>
        </td>
        <td class="text-end" style="padding-right: 1.25rem;">
            <button type="button" class="btn-inspect-diff" onclick="openAuditDetailModal('{{ $log->id }}')" title="Inspect State Diff & Metadata">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                <span>Inspect</span>
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5">
            <div class="audit-empty-state">
                <div class="audit-empty-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.75">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        <line x1="8" y1="11" x2="14" y2="11"/>
                    </svg>
                </div>
                <h4 class="audit-empty-title">No Audit Logs Found</h4>
                <p class="audit-empty-desc">No system events or changes matched your active filters. Try broadening your date range or clearing keyword search.</p>
                <button type="button" class="btn-reset-empty" onclick="resetAllFilters()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    <span>Reset All Filters</span>
                </button>
            </div>
        </td>
    </tr>
@endforelse
