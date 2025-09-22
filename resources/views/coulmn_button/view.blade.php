@props([
    'wireClick' => null,
    'tooltip' => null,
    'icon' => null,
])

@php
    $defaultIcon = <<<SVG
       <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-4 h-4 text-blue-700"
             fill="currentColor"><path d="M320 96C239.2 96 174.5 132.8 127.4 176.6C80.6 220.1 49.3 272 34.4 307.7C31.1 315.6 31.1 324.4 34.4 332.3C49.3 368 80.6 420 127.4 463.4C174.5 507.1 239.2 544 320 544C400.8 544 465.5 507.2 512.6 463.4C559.4 419.9 590.7 368 605.6 332.3C608.9 324.4 608.9 315.6 605.6 307.7C590.7 272 559.4 220 512.6 176.6C465.5 132.9 400.8 96 320 96zM176 320C176 240.5 240.5 176 320 176C399.5 176 464 240.5 464 320C464 399.5 399.5 464 320 464C240.5 464 176 399.5 176 320zM320 256C320 291.3 291.3 320 256 320C244.5 320 233.7 317 224.3 311.6C223.3 322.5 224.2 333.7 227.2 344.8C240.9 396 293.6 426.4 344.8 412.7C396 399 426.4 346.3 412.7 295.1C400.5 249.4 357.2 220.3 311.6 224.3C316.9 233.6 320 244.4 320 256z"/></svg>
    SVG;
@endphp

<div x-data="{ show: false }" class="relative inline-block">
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
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 
                   text-xs text-white bg-gray-800 rounded shadow z-10 whitespace-nowrap"
        >
            {{ $tooltip }}
        </div>
    @endif
</div>
