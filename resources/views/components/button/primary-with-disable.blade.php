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
</button>
