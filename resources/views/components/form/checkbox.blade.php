@props(['disabled' => false, 'name', 'value', 'label' => ''])
<x-form.field>
    @php $uniqueId = $name . '_' . $value; @endphp
    <div class="flex items-start gap-2">
        <input 
            class="w-4 h-4 mt-1 flex-shrink-0 text-yellow-400 bg-gray-100 border-gray-300 rounded-sm focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" 
            name="{{ $name }}" 
            id="{{ $uniqueId }}" 
            type="checkbox" 
            value="{{ $value }}"
            {{ $disabled ? 'disabled' : '' }} 
            {{ $attributes }}
        >
        <label for="{{ $uniqueId }}" class="text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
            {{ $label }}
        </label>
    </div>


    <x-form.error name="{{ $name }}" />
</x-form.field>