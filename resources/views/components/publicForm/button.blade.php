@props(['type' => 'submit',
        'class' => 'btn w-full h-[66px] flex justify-center items-center gap-2 bg-red-500 hover:bg-red-600 text-white',
        'icon' => null,
        'id' => null,
        'name' => null
])

<button :type="{{ $type }}"
    id="{{ $id }}"
    name="{{ $name }}"

    
    {{ $attributes->merge([
        'class' => $class ?: ' btn w-full h-[66px] flex justify-center items-center gap-2 bg-red-500 hover:bg-red-600 text-white'
    ]) }}>
    {{ $slot }}
</button>
