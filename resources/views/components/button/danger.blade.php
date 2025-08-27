<button {{ $attributes->merge(['type' => 'button', 'class' => 'bg-red-500 text-white px-4 py-2 rounded']) }}>
    {{ $slot }}
</button>
