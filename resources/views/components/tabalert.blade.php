@props([
    'type' => 'success',
    'tab' => null,
])

@php
$styles = [
    'success' => 'bg-green-100 text-green-800 border-green-200',
    'error'   => 'bg-red-100 text-red-800 border-red-200',
    'warning' => 'bg-yellow-100 text-yellow-900 border-yellow-200',
    'info'    => 'bg-blue-100 text-blue-800 border-blue-200',
];
@endphp

<div
    x-data="{ open: true }"
    x-show="open"
    x-transition
    class="rounded mb-3 border p-2 flex items-start justify-between {{ $styles[$type] ?? $styles['success'] }}"
    role="alert"
>
    <div class="pr-2">
        {{ $slot }}
    </div>

    <button
        type="button"
        class="ml-2 inline-flex items-center justify-center rounded p-1/2 hover:opacity-70 focus:outline-none"
        aria-label="Close"
        x-on:click="open = false; $wire.clearTabMessage('{{ $tab }}')"
    >
        ✕
    </button>
</div>
