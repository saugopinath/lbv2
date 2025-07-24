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
        class="border rounded-md shadow-sm
            border-gray-300 focus:border-indigo-300
            focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50
            p-2 w-full"
        autocomplete="off"
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes(['value' => old($name)]) }}
        {{ $attributes }}
    >

    <x-form.error name="{{ $name }}"/>

</x-form.field>

