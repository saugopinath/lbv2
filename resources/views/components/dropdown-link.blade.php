@props(['href' => null])

@if($href)
<a href="{{ $href }}"
    {{ $attributes->merge(['class' => 'flex items-center justify-start w-full px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-700 dark:hover:text-indigo-300 transition-all duration-150 ease-in-out group text-left']) }}>
    {{ $slot }}
</a>
@else
<button type="button"
    {{ $attributes->merge(['class' => 'flex items-center justify-start w-full px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-700 dark:hover:text-indigo-300 transition-all duration-150 ease-in-out group text-left']) }}>
    {{ $slot }}
</button>
@endif