@if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator && $logs->hasPages())
    @php
        $currentPage = $logs->currentPage();
        $lastPage = $logs->lastPage();
        $start = max(1, $currentPage - 2);
        $end = min($lastPage, $currentPage + 2);
    @endphp
    <div class="audit-pagination-wrapper">
        <div class="pagination-info text-muted">
            Showing <span class="font-semibold text-slate-800">{{ number_format($logs->firstItem() ?? 0) }}</span>
            to <span class="font-semibold text-slate-800">{{ number_format($logs->lastItem() ?? 0) }}</span>
            of <span class="font-semibold text-slate-800">{{ number_format($logs->total()) }}</span> total entries
        </div>
        <div class="pagination-nav">
            {{-- Previous Page Button --}}
            <button type="button" 
                    class="page-nav-btn prev-btn {{ $logs->onFirstPage() ? 'disabled' : '' }}" 
                    {{ $logs->onFirstPage() ? 'disabled' : '' }}
                    onclick="changePage({{ $currentPage - 1 }})"
                    title="Previous Page">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                <span>Prev</span>
            </button>

            {{-- First page and leading ellipsis if needed --}}
            @if($start > 1)
                <button type="button" class="page-num-btn {{ $currentPage == 1 ? 'active' : '' }}" onclick="changePage(1)">1</button>
                @if($start > 2)
                    <span class="pagination-ellipsis">...</span>
                @endif
            @endif

            {{-- Window of pages around current page --}}
            @for($page = $start; $page <= $end; $page++)
                <button type="button" 
                        class="page-num-btn {{ $page == $currentPage ? 'active' : '' }}" 
                        onclick="changePage({{ $page }})">
                    {{ $page }}
                </button>
            @endfor

            {{-- Trailing ellipsis and last page if needed --}}
            @if($end < $lastPage)
                @if($end < $lastPage - 1)
                    <span class="pagination-ellipsis">...</span>
                @endif
                <button type="button" class="page-num-btn {{ $currentPage == $lastPage ? 'active' : '' }}" onclick="changePage({{ $lastPage }})">{{ $lastPage }}</button>
            @endif

            {{-- Next Page Button --}}
            <button type="button" 
                    class="page-nav-btn next-btn {{ !$logs->hasMorePages() ? 'disabled' : '' }}" 
                    {{ !$logs->hasMorePages() ? 'disabled' : '' }}
                    onclick="changePage({{ $currentPage + 1 }})"
                    title="Next Page">
                <span>Next</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
@elseif($logs instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="audit-pagination-wrapper">
        <div class="pagination-info text-muted">
            Showing <span class="font-semibold text-slate-800">{{ number_format($logs->firstItem() ?? 0) }}</span>
            to <span class="font-semibold text-slate-800">{{ number_format($logs->lastItem() ?? 0) }}</span>
            of <span class="font-semibold text-slate-800">{{ number_format($logs->total()) }}</span> total entries
        </div>
        <div class="pagination-nav">
            <span class="pagination-single-page">Page 1 of 1</span>
        </div>
    </div>
@endif
