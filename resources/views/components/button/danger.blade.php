<button {{ $attributes->merge(['type' => 'button', 'class' => 'px-3 py-1.5 text-sm bg-red-100 text-red-500 hover:bg-red-200 hover:text-red-700 border border-red-200 rounded-lg transition-colors duration-150']) }}>
    {{ $slot }}
</button>
