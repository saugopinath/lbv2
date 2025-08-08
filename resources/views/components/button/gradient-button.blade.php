<button type="{{ $type }}"
    {{ $attributes->merge(['class' => 'text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 w-full md:w-auto']) }}>
    {{ $slot }}
</button>
