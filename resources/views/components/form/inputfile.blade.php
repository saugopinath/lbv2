@props([
    'disabled' => false,
    'name',
    'label' => null,
    'required' => false,
    'accept' => null,
    'maxSize' => null,
])

<x-form.field>
    {{-- Label with optional required asterisk --}}
    <div class="flex items-center gap-1">
        <x-form.label name="{{ $name }}" label="{{ $label }}" />
        @if ($required)
            <span class="text-red-700 font-bold">*</span>
        @endif
    </div>

    <input
        type="{{ $attributes->get('type', 'file') }}"
        name="{{ $name }}"
        id="{{ $name }}"
        autocomplete="off"
        {{ $disabled ? 'disabled' : '' }}
        {{ $required ? 'required' : '' }}
        {{ $accept ? "accept=$accept" : '' }}
        {{ $attributes->merge(['class' => 'border rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 w-full']) }}
    >

    @if ($accept || $maxSize)
        <p class="text-sm text-gray-500 mt-1">
            (
            @if ($accept)
                File type: {{ str_replace(',', ', ', $accept) }}
            @endif

            @if ($accept && $maxSize)
                , 
            @endif

            @if ($maxSize)
                Max size: {{ $maxSize }}
            @endif
            )
        </p>
    @endif

    <x-form.error name="{{ $name }}" />
</x-form.field>
