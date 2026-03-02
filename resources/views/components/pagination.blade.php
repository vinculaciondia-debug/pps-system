@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="w-full flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="text-sm text-gray-600 order-2 sm:order-1">
        Mostrando
        <span class="font-semibold">{{ $paginator->firstItem() }}</span>
        a
        <span class="font-semibold">{{ $paginator->lastItem() }}</span>
        de
        <span class="font-semibold">{{ $paginator->total() }}</span>
        resultados
    </div>

    <div class="order-1 sm:order-2 flex items-center flex-wrap gap-2">
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">Anterior</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Anterior</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-2 text-gray-500">...</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-2 rounded-lg bg-blue-600 text-white shadow-sm">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Siguiente</a>
        @else
            <span class="px-3 py-2 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">Siguiente</span>
        @endif
    </div>
</nav>
@endif