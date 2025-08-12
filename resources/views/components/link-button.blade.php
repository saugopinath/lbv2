@props(['href', 'color' => 'blue'])

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => "inline-flex items-center px-4 py-2 bg-{$color}-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-{$color}-700 focus:outline-none focus:ring-2 focus:ring-{$color}-300 transition ease-in-out duration-150"]) }}>
    {{ $slot }}
</a>
