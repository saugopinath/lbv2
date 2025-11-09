@props([
    'href' => null,
])

@if ($href)
    <a href="{{ $href }}"
<<<<<<< HEAD
        {{ $attributes->merge(['class' => 'bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center justify-center']) }}>
=======
        {{ $attributes->merge(['class' => 'bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center justify-center cursor-pointer']) }}>
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
        {{ $slot }}
    </a>
@else
    <button
<<<<<<< HEAD
        {{ $attributes->merge(['type' => 'button', 'class' => 'bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center justify-center']) }}>
        {{ $slot }}
    </button>
@endif
=======
        {{ $attributes->merge(['type' => 'button', 'class' => 'bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center justify-center cursor-pointer']) }}>
        {{ $slot }}
    </button>
@endif
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
