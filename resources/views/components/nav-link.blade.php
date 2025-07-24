@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center p-2 bg-indigo-700 rounded-md dark:bg-indigo-800 text-sm font-medium leading-5 text-white dark:text-gray-100 focus:outline-none focus:bg-indigo-900 transition duration-150 ease-in-out'
            : 'flex items-center p-2 bg-transparent hover:bg-indigo-700 rounded-md dark:bg-indigo-800 text-sm font-medium leading-5 text-gray-300 hover:text-white dark:text-gray-100 focus:outline-none focus:bg-indigo-900 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
