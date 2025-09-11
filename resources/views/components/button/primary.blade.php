<button {{ $attributes->merge(['type' => 'button', 'class' => 'cursor-pointer bg-blue-600 text-white px-4 py-2 rounded']) }}>
    {{ $slot }}
</button>
