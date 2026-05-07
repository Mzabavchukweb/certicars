@if ($paginator->hasPages())
<nav role="navigation" aria-label="Paginacja" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;justify-content:center">
    @if ($paginator->onFirstPage())
        <span aria-disabled="true" style="display:inline-flex;align-items:center;gap:4px;padding:10px 14px;border:1px solid var(--border-l);border-radius:10px;font-size:13px;color:var(--text-4);background:#fff;cursor:not-allowed">
            <i data-lucide="chevron-left" style="width:14px;height:14px" aria-hidden="true"></i> Poprzednia
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Poprzednia strona" style="display:inline-flex;align-items:center;gap:4px;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;color:var(--text);background:#fff;font-weight:500;transition:all .15s">
            <i data-lucide="chevron-left" style="width:14px;height:14px" aria-hidden="true"></i> Poprzednia
        </a>
    @endif

    @foreach ($elements ?? [] as $element)
        @if (is_string($element))
            <span style="padding:10px 6px;color:var(--text-4);font-size:13px">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span aria-current="page" style="display:inline-flex;align-items:center;justify-content:center;min-width:40px;padding:10px 12px;border-radius:10px;font-size:13px;font-weight:600;background:var(--blue);color:#fff;border:1px solid var(--blue)">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" aria-label="Strona {{ $page }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:40px;padding:10px 12px;border:1px solid var(--border-l);border-radius:10px;font-size:13px;color:var(--text-2);background:#fff;font-weight:500;transition:all .15s">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Następna strona" style="display:inline-flex;align-items:center;gap:4px;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;color:var(--text);background:#fff;font-weight:500;transition:all .15s">
            Następna <i data-lucide="chevron-right" style="width:14px;height:14px" aria-hidden="true"></i>
        </a>
    @else
        <span aria-disabled="true" style="display:inline-flex;align-items:center;gap:4px;padding:10px 14px;border:1px solid var(--border-l);border-radius:10px;font-size:13px;color:var(--text-4);background:#fff;cursor:not-allowed">
            Następna <i data-lucide="chevron-right" style="width:14px;height:14px" aria-hidden="true"></i>
        </span>
    @endif
</nav>
@endif
