@props(['active' => false, 'icon' => null])

@php
$classes = ($active ?? false)
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-600 hover:text-blue-500';
@endphp

<a {{ $attributes->merge(['class' => "py-2 px-2 border-b-2 -mb-px flex items-center space-x-1 $classes"]) }}>
    @if ($icon)
        <svg class="w-6 h-6 {{ $active ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"
             xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 24">
            <path d="{{ $icon }}" />
        </svg>
    @endif
    <span class="hidden md:inline">
        {{ $slot }}
    </span>
</a>
