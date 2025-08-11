@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center">
        <ul class="flex space-x-2 items-center">
            <!-- Previous Arrow -->
            @if (!$paginator->onFirstPage())
                <li>
                    <a href="#" wire:click.prevent="previousPage" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        &laquo; Prev
                    </a>
                </li>
            @else
                <li>
                    <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                        &laquo; Prev
                    </span>
                </li>
            @endif

            <!-- Page Numbers -->
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="px-3 py-1 bg-teal-500 text-white rounded-md">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="#" wire:click.prevent="gotoPage({{ $page }})" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            <!-- Next Arrow -->
            @if ($paginator->hasMorePages())
                <li>
                    <a href="#" wire:click.prevent="nextPage" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Next &raquo;
                    </a>
                </li>
            @else
                <li>
                    <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                        Next &raquo;
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif