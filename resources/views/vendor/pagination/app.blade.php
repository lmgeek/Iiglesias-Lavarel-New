@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Paginación">
        <div class="pagination-info">
            <p>Mostrando {{ $paginator->firstItem() ?? 0 }} a {{ $paginator->lastItem() ?? 0 }} de {{ $paginator->total() }} resultados</p>
        </div>

        <div class="pagination-pages">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="page-btn page-btn-disabled" aria-disabled="true">&laquo; Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="page-btn" rel="prev">&laquo; Anterior</a>
            @endif

            {{-- Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="page-btn page-btn-dots" aria-disabled="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-btn page-btn-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="page-btn" rel="next">Siguiente &raquo;</a>
            @else
                <span class="page-btn page-btn-disabled" aria-disabled="true">Siguiente &raquo;</span>
            @endif
        </div>
    </nav>
@endif