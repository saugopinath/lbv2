@props(['duration' => 4000, 'position' => 'top-right', 'width' => 'w-96'])

@php
    $message = session('success') ?? session('error') ?? session('info') ?? session('warning');
    $type = session('success') ? 'success' :
            (session('error') ? 'error' :
            (session('info') ? 'info' :
            (session('warning') ? 'warning' : null)));
    
    $classes = [
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'info' => 'bg-blue-100 border-blue-400 text-blue-700',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
    ];

    $positions = [
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'top-center' => 'top-4 left-1/2 transform -translate-x-1/2',
    ];
@endphp

@if($message)
<div 
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, {{ $duration }})"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="fixed {{ $positions[$position] }} {{ $width }} px-6 py-4 rounded-lg border font-medium shadow-lg z-50"
    role="alert"
    :class="'{{ $classes[$type] }}'"
>
    {{ $message }}
</div>
@endif
