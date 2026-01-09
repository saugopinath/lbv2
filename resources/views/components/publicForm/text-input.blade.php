@props([
    'id' => null,
    'name' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'class' => ''
])

<input
    id="{{ $id }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}"
    @if($required) required @endif
    {{ $attributes->merge([
        'class' => $class ?: 'form-input h-[56px] bg-transparent dark:bg-transparent text-base rounded-[10px] ps-5 pe-14'
    ]) }}
>

