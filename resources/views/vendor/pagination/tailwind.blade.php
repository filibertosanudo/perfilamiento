@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-4">
        
        {{-- Texto de resultados --}}
        <div class="text-sm text-gray-500">
            Mostrando
            <span class="font-medium text-gray-700">{{ $paginator->firstItem() }}</span>
            a
            <span class="font-medium text-gray-700">{{ $paginator->lastItem() }}</span>
            de
            <span class="font-medium text-gray-700">{{ $paginator->total() }}</span>
            resultados
        </div>

        {{-- Links --}}
        <div class="flex items-center gap-1">
            
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-sm rounded-lg text-gray-400 bg-gray-100 cursor-not-allowed">
                    Anterior
                </span>
            @else
                <button
                    wire:click="previousPage"
                    class="px-3 py-2 text-sm rounded-lg text-gray-600 bg-white border border-gray-200 hover:bg-teal-50 hover:text-teal-600 transition"
                >
                    Anterior
                </button>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-2 text-sm text-gray-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                class="px-3 py-2 text-sm font-semibold rounded-lg bg-teal-600 text-white shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <button
                                wire:click="gotoPage({{ $page }})"
                                class="px-3 py-2 text-sm rounded-lg text-gray-600 bg-white border border-gray-200 hover:bg-teal-50 hover:text-teal-600 transition"
                            >
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <button
                    wire:click="nextPage"
                    class="px-3 py-2 text-sm rounded-lg text-gray-600 bg-white border border-gray-200 hover:bg-teal-50 hover:text-teal-600 transition"
                >
                    Siguiente
                </button>
            @else
                <span class="px-3 py-2 text-sm rounded-lg text-gray-400 bg-gray-100 cursor-not-allowed">
                    Siguiente
                </span>
            @endif
        </div>
    </nav>
@endif
