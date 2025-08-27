@props(['disabled' => false, 'name', 'label' => null])

<x-form.field>

    @if ($attributes->has('required'))
        <div class="flex items-center gap-1">
            <x-form.label name="{{ $name }}" label="{{ $label }}" />
            <span class="text-red-700 font-bold">*</span>
        </div>
    @else
        <x-form.label name="{{ $name }}" label="{{ $label }}"/>
    @endif

    <input
        class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400"
        autocomplete="off"
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes(['value' => old($name)]) }}
        {{ $attributes }}
    >

    <x-form.error name="{{ $name }}"/>

</x-form.field>

