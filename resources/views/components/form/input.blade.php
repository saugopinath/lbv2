@props([
    'disabled' => false,
    'name',
    'label' => null,
    'id' => null,
    'type' => 'text',
    'name_on_label' => true,
    'required_star' => true,
    'label_placement' => 'top',
])

@php
    $isRadioOrCheckbox = in_array($type, ['radio', 'checkbox']);
    $defaultClasses = $isRadioOrCheckbox ? 'w-4 h-4 text-cyan-600 border-gray-300 focus:ring-cyan-500 dark:focus:ring-cyan-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed' : 'border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400';
    $nameOnLabel = $name_on_label ? $name : '';
@endphp

<x-form.field>

    @if ($label_placement === 'top')

        <x-form.label class="!mb-0 inline-block" label="{{ $label }}" name="{{ $nameOnLabel }}" />
        @if ($attributes->has('required') && $required_star)
            <span class="text-red-700 font-bold leading-none">*</span>
        @endif

        <input {{ $attributes }} {{ $disabled ? 'disabled' : '' }} {{ $attributes(['value' => old($name)]) }} autocomplete="off" class="{{ $defaultClasses }}" id="{{ $id ?? $name }}" name="{{ $name }}" type="{{ $type }}">
    @elseif(in_array($label_placement, ['left', 'right']))
        <div class="flex items-center gap-2">
            {{-- Left Label --}}
            @if ($label_placement === 'left')
                <div class="inline-flex items-center gap-1 leading-none">
                    <x-form.label class="inline-block" label="{{ $label }}" name="{{ $nameOnLabel }}" style="margin-bottom:0 !important;padding-bottom:0 !important;" />
                    @if ($attributes->has('required') && $required_star)
                        <span class="text-red-700 font-bold leading-none">*</span>
                    @endif
                </div>
            @endif

            {{-- Input Field --}}
            <input {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['value' => old($name)]) }} autocomplete="off" class="{{ $defaultClasses }}" id="{{ $id ?? $name }}" name="{{ $name }}" type="{{ $type }}">

            {{-- Right Label --}}
            @if ($label_placement === 'right')
                <div class="inline-flex items-center gap-1 leading-none">
                    <x-form.label class="inline-block" label="{{ $label }}" name="{{ $nameOnLabel }}" style="margin-bottom:0 !important;padding-bottom:0 !important;" />
                    @if ($attributes->has('required') && $required_star)
                        <span class="text-red-700 font-bold leading-none">*</span>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <x-form.error name="{{ $name }}" />

</x-form.field>
