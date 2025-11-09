@props([
<<<<<<< HEAD
    'action' => null,   
=======
    'action' => null,
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
    'text' => 'Submit',
    'color' => 'indigo',
    'type' => 'button', ])

<button
<<<<<<< HEAD
    @if($action) 
        wire:click="{{ $action }}" 
        wire:loading.attr="disabled" 
=======
    @if($action)
        wire:click="{{ $action }}"
        wire:loading.attr="disabled"
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
    @else
        x-data="{ loading: false }"
        x-on:click="if($el.type === 'submit'){ loading = true; $el.form.submit(); }"
        x-bind:disabled="loading"
    @endif

    {{ $attributes->merge([
<<<<<<< HEAD
        'class' => "px-4 py-2 bg-{$color}-500 text-white rounded-lg hover:bg-{$color}-700 flex items-center gap-2"
=======
        'class' => "px-4 py-2 bg-{$color}-500 text-white rounded-lg hover:bg-{$color}-700 flex items-center gap-2 cursor-pointer"
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
    ]) }}
    type="{{ $type }}"
>
    {{-- Spinner --}}
    @if($action)
        {{-- Livewire loader --}}
        <svg wire:loading wire:target="{{ $action }}"
            class="animate-spin h-5 w-5 text-white"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
    @else
        {{-- AlpineJS loader (non-Livewire) --}}
        <svg x-show="loading"
            class="animate-spin h-5 w-5 text-white"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
    @endif

    {{-- Button Text --}}
    <span @if($action) wire:loading.remove wire:target="{{ $action }}" @else x-show="!loading" @endif>
        {{ $text }}
    </span>
</button>
