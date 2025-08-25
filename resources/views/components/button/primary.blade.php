@props([
    'disabled' => false,
    'wireTarget' => null,
])

<button {{ $disabled ? 'disabled' : '' }} wire:loading.attr="disabled" wire:target="{{ $wireTarget }}"
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150']) }}>

    <span wire:loading wire:target="{{ $wireTarget }}">
        <x-loader class="h-4 w-4 mr-2" />
    </span>


    <span wire:loading.class="text-transparent" wire:target="{{ $wireTarget }}">
        {{ $slot }}
    </span>
</button>
