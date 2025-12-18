@props([
    'action' => null,
    'text' => 'Submit',
    'type' => 'button',
    'lockPage' => false,  // Add this new prop
])

@if($lockPage)
    <style>
        .page-locked {
            overflow: hidden !important;
            pointer-events: none !important;
            cursor: wait !important;
        }
        .page-locked::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9998;
        }
        .page-locked::after {
            content: '';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 9999;
        }
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
    </style>
@endif

<button
    @if($action)
        wire:click="{{ $action }}"
        @if($lockPage)
            wire:loading="document.body.classList.add('page-locked')"
            wire:loading.remove="document.body.classList.remove('page-locked')"
        @endif
        wire:loading.attr="disabled"
        wire:loading.class="opacity-75 cursor-not-allowed"
    @else
        x-data="{ loading: false }"
        x-on:click="if($el.type === 'submit') { 
            loading = true; 
            @if($lockPage) document.body.classList.add('page-locked'); @endif
            $el.form?.submit(); 
        }"
        x-bind:disabled="loading"
        x-bind:class="loading ? 'opacity-75 cursor-not-allowed' : ''"
    @endif
    
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2 cursor-pointer transition-colors duration-200'
    ]) }}
>
    {{-- Spinner --}}
    @if($action)
        <svg wire:loading wire:target="{{ $action }}"
            class="animate-spin h-5 w-5 text-white"
            fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
        </svg>
    @else
        <svg x-show="loading"
            class="animate-spin h-5 w-5 text-white"
            fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
        </svg>
    @endif

    {{-- Button Text --}}
    <span @if($action) wire:loading.remove wire:target="{{ $action }}" @else x-show="!loading" @endif>
        {{ $text }}
    </span>
</button>