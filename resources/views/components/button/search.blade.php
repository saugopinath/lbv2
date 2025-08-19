<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors duration-200'
]) }}>
    {{ $slot }}
</button>
