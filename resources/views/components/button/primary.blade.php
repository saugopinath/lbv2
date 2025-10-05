{{--  <button {{ $attributes->merge(['type' => 'button', 'class' => 'bg-blue-600 text-white px-4 py-2 rounded']) }}>
<button 
    {{ $attributes->merge([
        'type' => 'button',
        'class' => 'px-4 py-2 rounded bg-blue-600 text-white disabled:opacity-50'
    ]) }}
    x-bind:class="{
        'cursor-pointer': !$el.disabled,
        'cursor-not-allowed': $el.disabled
    }"
    @if($attributes->get('disabled')) disabled @endif
>
    {{ $slot }}
</button>  --}}
@props([
    'href' => null,
])

@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge(['class' => 'bg-green-600 text-white px-4 py-2 rounded cursor-pointer inline-flex items-center justify-center cursor-pointer']) }}>
        {{ $slot }}
    </a>
@else
    <button
        {{ $attributes->merge(['type' => 'button', 'class' => 'bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center justify-center cursor-pointer']) }}>
        {{ $slot }}
    </button>
@endif
