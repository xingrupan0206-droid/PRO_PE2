@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Paginering">
        @if ($paginator->onFirstPage())
            <span class="btn btn-light" aria-disabled="true">Vorige</span>
        @else
            <a class="btn btn-light" href="{{ $paginator->previousPageUrl() }}" rel="prev">Vorige</a>
        @endif

        <span>Pagina {{ $paginator->currentPage() }} van {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a class="btn btn-light" href="{{ $paginator->nextPageUrl() }}" rel="next">Volgende</a>
        @else
            <span class="btn btn-light" aria-disabled="true">Volgende</span>
        @endif
    </nav>
@endif
