@aware(['tableName','isTailwind','isBootstrap','isBootstrap4','isBootstrap5','localisationPath'])
<div
    x-data="{ open: false, selected: @entangle('perPage') }"
    x-cloak
    class="{{ $isTailwind ? 'inline-block relative' : '' }}"
>
    <button
        type="button"
        x-on:click="open = !open"
        class="{{ $isTailwind 
            ? 'inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600' 
            : ($isBootstrap4 ? 'btn btn-secondary dropdown-toggle' : 'btn btn-secondary dropdown-toggle') 
        }}"
    >
        <span x-text="selected"></span>
        @if($isTailwind)
            <x-heroicon-m-chevron-down class="-mr-1 ml-2 h-5 w-5" />
        @endif
    </button>

    <div
        x-show="open"
        x-on:click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="{{ $isTailwind ? 'origin-top-right absolute right-0 mt-2 w-24 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:bg-gray-700 dark:text-white z-50' : 'dropdown-menu' }}"
    >
        <div class="py-1" role="menu" aria-orientation="vertical">
            @foreach ($this->getPerPageAccepted() as $item)
                <button
                    type="button"
                    class="{{ $isTailwind
                        ? 'block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-600 dark:text-white focus:outline-none'
                        : 'dropdown-item'
                    }}"
                    x-on:click="selected = {{ $item }}; open = false"
                    wire:click="$set('perPage', {{ $item }})"
                >
                    {{ $item === -1 ? __('All') : $item }}
                </button>
            @endforeach
        </div>
    </div>
</div>
                    