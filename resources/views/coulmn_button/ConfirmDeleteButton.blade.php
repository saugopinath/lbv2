@props([
'itemId' => null,
'action' => null,
'title' => 'Confirm Action',
'permissionName' => null,
'message' => 'Are you sure you want to perform this action?',
'tooltip' => null,
'icon' => null,
'confirmLabel' => 'Confirm',
'cancelLabel' => 'Cancel',
])

@php
    $defaultIcon = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4 text-red-600" fill="currentColor">
            <path d="M9 3a1 1 0 00-1 1v1H4a1 1 0 100 2h1v11a2 2 0 002 2h8a2 2 0 002-2V7h1a1 1 0 100-2h-4V4a1 1 0 00-1-1H9zm2 5a1 1 0 012 0v8a1 1 0 11-2 0V8zm-4 0a1 1 0 012 0v8a1 1 0 11-2 0V8z"/>
        </svg>
    SVG;
@endphp


    <div x-data="{ showTooltip: false, showModal: false }" class="relative inline-block">
        {{-- Icon Button --}}
        <button
            @mouseenter="showTooltip = true"
            @mouseleave="showTooltip = false"
            @click="showModal = true"
            class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded-md hover:bg-gray-300 transition">
            {!! $icon ?? $defaultIcon !!}
        </button>

        {{-- Tooltip --}}
        @if($tooltip)
        <div
            x-show="showTooltip"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 
                   text-xs text-white bg-gray-800 rounded shadow z-10 whitespace-nowrap">
            {{ $tooltip }}
        </div>
        @endif

        {{-- Confirmation Modal --}}
        <div
            x-show="showModal"
            x-cloak
            class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                <h2 class="text-lg font-semibold mb-4">{{ $title }}</h2>
                <p class="mb-6 text-gray-700 text-sm sm:text-base leading-relaxed text-center break-words whitespace-normal">
                    {{ $message }}
                    <br>
                    <span class="block font-semibold text-red-600 mt-1">
                        {{ $Name ?? '' }}
                    </span>

                </p>


                <div class="flex justify-end space-x-2">
                    <button
                        @click="showModal = false"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        {{ $cancelLabel }}
                    </button>

                    <button
                        wire:click="{{ $action }}({{ $itemId }})"
                        @click="showModal = false"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        {{ $confirmLabel }}
                    </button>
                </div>
            </div>
        </div>
    </div>