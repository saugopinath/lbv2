{{--  <button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-yellow-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>  --}}
<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex items-center px-4 py-2
                bg-red-600 border border-transparent
                rounded-md font-semibold text-xs text-white uppercase tracking-widest
                hover:bg-red-700 active:bg-red-800
                focus:outline-none focus:border-red-800 focus:ring ring-red-300
                disabled:opacity-25 transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>
