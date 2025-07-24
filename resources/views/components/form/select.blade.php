@props(['disabled' => false, 'name', 'label' => ''])

<x-form.field>

    @if ($attributes->has('required'))
        <div class="flex items-center gap-1">
            <x-form.label name="{{ $name }}" label="{{ $label }}" />
            <span class="text-red-700 font-bold">*</span>
        </div>
    @else
        <x-form.label name="{{ $name }}" label="{{ $label }}"/>
    @endif

    <select {{ $disabled ? 'disabled' : '' }}
            {!! $attributes->merge(['class' => 'w-full rounded-md shadow-sm border-gray-300
                                    focus:border-indigo-300 focus:ring focus:ring-indigo-200
                                    focus:ring-opacity-50']) !!}
            id = "{{ $name }}"
            name = "{{ $name }}"
    >
        {{ $slot }}
    </select>

    <x-form.error name="{{ $name }}"/>

</x-form.field>
