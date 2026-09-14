@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación">
        <ul class="rows" style="display:flex;gap:6px;list-style:none;padding:0;margin:0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li aria-disabled="true">
                    <span style="padding:6px 12px;border:1px solid var(--border-light);border-radius:10px;color:var(--text-tertiary)">‹</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="padding:6px 12px;border:1px solid var(--border-light);border-radius:10px;color:var(--text-secondary)">‹</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li aria-disabled="true">
                        <span style="padding:6px 12px;color:var(--text-tertiary)">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li aria-current="page">
                                <span style="padding:6px 12px;border-radius:10px;background:var(--cat-600);color:#fff">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" style="padding:6px 12px;border:1px solid var(--border-light);border-radius:10px;color:var(--text-secondary)">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="padding:6px 12px;border:1px solid var(--border-light);border-radius:10px;color:var(--text-secondary)">›</a>
                </li>
            @else
                <li aria-disabled="true">
                    <span style="padding:6px 12px;border:1px solid var(--border-light);border-radius:10px;color:var(--text-tertiary)">›</span>
                </li>
            @endif
        </ul>
    </nav>
@endif