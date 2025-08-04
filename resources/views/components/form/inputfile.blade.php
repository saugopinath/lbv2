@props([
'disabled' => false,
'name',
'label' => null,
'required' => false,
'accept' => null,
'maxSize' => null
])
<x-form.field>
    {{-- Label with asterisk if required --}}
    @if ($required)
    <div class="flex items-center gap-1">
        <x-form.label name="{{ $name }}" label="{{ $label }}" />
        <span class="text-red-700 font-bold">*</span>
    </div>
    @else
    <x-form.label name="{{ $name }}" label="{{ $label }}" />
    @endif
    <input
        type="{{ $attributes->get('type', 'text') }}"
        class="border rounded-md shadow-sm
            border-gray-300 focus:border-indigo-300
            focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50
            p-2 w-full"
        autocomplete="off"
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $required ? 'required' : '' }}
        {{ $accept ? "accept=$accept" : '' }}
        {{ $attributes->merge(['value' => old($name)]) }}>
    @if ($accept || $maxSize)
    <p class="text-sm text-gray-500 mt-1">
        @if ($accept)
        (Image type must be {{ str_replace(',',',', $accept) }}
        @endif
        @if ($maxSize)
        and image size max {{ $maxSize }})
        @endif
    </p>
    @endif
    <x-form.error name="{{ $name }}" />
</x-form.field>