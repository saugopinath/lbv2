@props([
    'action',  // the Livewire method name like "applyFilters"
    'text' => 'Submit',
    'color' => 'indigo',
])

<button
    wire:click="{{ $action }}"
    wire:loading.attr="disabled"
    {{ $attributes->merge([
        'class' => "px-4 py-2 bg-{$color}-500 text-white rounded-lg hover:bg-{$color}-700 flex items-center gap-2"
    ]) }}
>
    {{-- Loader shown only while this action is running --}}
    <svg wire:loading wire:target="{{ $action }}"
        class="animate-spin h-5 w-5 text-white"
        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10"
            stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
    </svg>

    <span wire:loading.remove wire:target="{{ $action }}">
        {{ $text }}
    </span>
</button>
