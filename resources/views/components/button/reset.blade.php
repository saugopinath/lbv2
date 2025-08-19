<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'flex items-center gap-2 bg-amber-400 text-white px-4 py-2 rounded hover:bg-amber-600 transition-colors duration-200'
]) }}>
    {{ $slot }}
</button>
