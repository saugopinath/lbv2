{{--  <button {{ $attributes->merge(['type' => 'button', 'class' => 'bg-blue-600 text-white px-4 py-2 rounded']) }}>
    {{ $slot }}
</button>  --}}
@props([
    'href' => null,
])

@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge(['class' => 'bg-green-600 text-white px-4 py-2 rounded cursor-pointer inline-flex items-center justify-center']) }}>
        {{ $slot }}
    </a>
@else
    <button
        {{ $attributes->merge(['type' => 'button', 'class' => 'bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center justify-center']) }}>
        {{ $slot }}
    </button>
@endif
