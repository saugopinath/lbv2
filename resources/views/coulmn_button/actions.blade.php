@props([
<<<<<<< HEAD
=======
     'link' => null,
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
    'wireClick' => null,
    'tooltip' => null,
    'icon' => null,
])

@php
    $defaultIcon = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-4 h-4 text-green-600"
             fill="currentColor">
            <path d="M535.6 85.7C513.7 63.8 478.3 63.8 456.4 85.7L432 110.1L529.9 208L554.3 183.6C576.2 161.7 576.2 126.3 554.3 104.4L535.6 85.7zM236.4 305.7C230.3 311.8 225.6 319.3 222.9 327.6L193.3 416.4C190.4 425 192.7 434.5 199.1 441C205.5 447.5 215 449.7 223.7 446.8L312.5 417.2C320.7 414.5 328.2 409.8 334.4 403.7L496 241.9L398.1 144L236.4 305.7zM160 128C107 128 64 171 64 224L64 480C64 533 107 576 160 576L416 576C469 576 512 533 512 480L512 384C512 366.3 497.7 352 480 352C462.3 352 448 366.3 448 384L448 480C448 497.7 433.7 512 416 512L160 512C142.3 512 128 497.7 128 480L128 224C128 206.3 142.3 192 160 192L256 192C273.7 192 288 177.7 288 160C288 142.3 273.7 128 256 128L160 128z"/>
        </svg>
    SVG;
@endphp

<div x-data="{ show: false }" class="relative inline-block">
<<<<<<< HEAD
    <button 
        @if($wireClick) wire:click="{{ $wireClick }}" @endif
        @mouseenter="show = true" 
        @mouseleave="show = false"
        class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded-md 
               hover:bg-gray-300 transition"
    >
        {!! $icon ?? $defaultIcon !!}
    </button>

    @if($tooltip)
        <div 
            x-show="show" 
            x-cloak 
=======
    @if($link)
        <a href="{{ $link }}"
           @mouseenter="show = true"
           @mouseleave="show = false"
           class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded-md hover:bg-gray-300 transition"
           title="{{ $tooltip }}">
            {!! $icon ?? $defaultIcon !!}
        </a>
    @else
        <button
            @if($wireClick) wire:click="{{ $wireClick }}" @endif
            @mouseenter="show = true"
            @mouseleave="show = false"
            class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded-md
                   hover:bg-gray-300 transition"
        >
            {!! $icon ?? $defaultIcon !!}
        </button>
    @endif

    @if($tooltip)
        <div
            x-show="show"
            x-cloak
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
<<<<<<< HEAD
            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 
=======
            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
                   text-xs text-white bg-gray-800 rounded shadow z-10 whitespace-nowrap"
        >
            {{ $tooltip }}
        </div>
    @endif
</div>
