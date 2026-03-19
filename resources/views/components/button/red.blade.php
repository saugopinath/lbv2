<button {{ $attributes->merge(['type' => 'button', 'class' => 'bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded cursor-pointer']) }}>
    {{ $slot }}
</button>
