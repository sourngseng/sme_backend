@if(isset($paginator) && $paginator->total() > 0)
    <div>
        {{ transLabel('school.showing', 'Showing') }}
        <strong>{{ $paginator->firstItem() ?? 0 }}</strong>
        {{ transLabel('school.to', 'to') }}
        <strong>{{ $paginator->lastItem() ?? 0 }}</strong>
        {{ transLabel('school.of', 'of') }}
        <strong>{{ number_format($paginator->total()) }}</strong>
        {{ transLabel('menu.students', 'students') }}
    </div>

    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span style="font-size: 0.775rem; color: #64748B;">{{ transLabel('school.show', 'Show') }}</span>
        @foreach([10, 25, 50] as $size)
            <span class="page-box {{ $paginator->perPage() == $size ? 'active' : '' }}"
                  style="cursor: pointer;"
                  onclick="setServerEntriesPerPage({{ $size }})">
                {{ $size }}
            </span>
        @endforeach
    </div>

    <div class="pagination-pages">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="page-box disabled" style="opacity: 0.45; cursor: not-allowed;">&lt;</span>
        @else
            <a href="javascript:void(0)" class="page-box" onclick="fetchStudentsPage({{ $paginator->currentPage() - 1 }})">&lt;</a>
        @endif

        {{-- Pagination Elements --}}
        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();
            $startPage = max(1, $currentPage - 2);
            $endPage = min($lastPage, $currentPage + 2);
        @endphp

        @if($startPage > 1)
            <a href="javascript:void(0)" class="page-box" onclick="fetchStudentsPage(1)">1</a>
            @if($startPage > 2)
                <span class="page-box" style="border: none; background: transparent; cursor: default;">...</span>
            @endif
        @endif

        @for ($page = $startPage; $page <= $endPage; $page++)
            @if ($page == $currentPage)
                <span class="page-box active" style="cursor: default;">{{ $page }}</span>
            @else
                <a href="javascript:void(0)" class="page-box" onclick="fetchStudentsPage({{ $page }})">{{ $page }}</a>
            @endif
        @endfor

        @if($endPage < $lastPage)
            @if($endPage < $lastPage - 1)
                <span class="page-box" style="border: none; background: transparent; cursor: default;">...</span>
            @endif
            <a href="javascript:void(0)" class="page-box" onclick="fetchStudentsPage({{ $lastPage }})">{{ $lastPage }}</a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="javascript:void(0)" class="page-box" onclick="fetchStudentsPage({{ $paginator->currentPage() + 1 }})">&gt;</a>
        @else
            <span class="page-box disabled" style="opacity: 0.45; cursor: not-allowed;">&gt;</span>
        @endif
    </div>
@else
    <div style="font-size: 0.8rem; color: #94A3B8;">
        {{ transLabel('school.no_matching_records', '0 student records matching current filters') }}
    </div>
@endif
