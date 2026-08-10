@if ($paginator->hasPages())
<div class="pagination">
    @if ($paginator->onFirstPage())
    <span class="disabled">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
        <span class="disabled">{{ $element }}</span>
        @elseif ($element->isCurrent)
        <span class="active">{{ $element->page }}</span>
        @else
        <a href="{{ $element->url }}">{{ $element->page }}</a>
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </a>
    @else
    <span class="disabled">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </span>
    @endif
</div>
@endif