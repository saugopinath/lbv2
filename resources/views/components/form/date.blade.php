@props([
    'id',
    'label' => '',
    'name' => '',
    'required' => false,
    'placeholder' => 'Select Date',
])

<label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
    {{ $label }}
    @if ($required)
        <span class="text-red-600">*</span>
    @endif
</label>

<input
    type="date"
    id="{{ $id }}"
    name="{{ $name }}"
    placeholder="{{ $placeholder }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400']) }}
/>
