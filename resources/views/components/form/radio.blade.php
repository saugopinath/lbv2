@props(['disabled' => false, 'name', 'value', 'label' => ''])

<x-form.field>

    <div class="flex gap-2">
        <input
            class="border rounded-full shadow-sm
                border-gray-300 focus:border-indigo-300
                focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                p-2"
            name="{{ $name }}"
            id="{{ $name }}"
            type="radio"
            value="{{ $value }}"
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes }}
        >

        <label class="font-medium text-xs text-gray-700"
            for="{{ $value }}"
        >
            {{ empty($label) ? ucwords(str_replace('_',' ',$value)) : $label}}
        </label>
    </div>

    <x-form.error name="{{ $name }}"/>

</x-form.field>
