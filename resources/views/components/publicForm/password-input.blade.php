@props([
    'id' => null,
    'name' => null,
    'placeholder' => 'Password',
    'required' => false,
    'class' => ''
])
<div x-data="{ showPassword: false }" class="relative">
    <input
        :type="showPassword ? 'text' : 'password'"
        id="{{ $id ?? 'password' }}"
        name="{{ $name ?? 'password' }}"
        placeholder="{{ $placeholder ?? 'Password' }}"
        {{ $attributes->merge([
            'class' => $class ?: 'form-input h-[56px] bg-transparent dark:bg-transparent text-base rounded-[10px] ps-5 pe-14'
        ]) }}
    >
    <svg @click="showPassword = !showPassword"
         xmlns="http://www.w3.org/2000/svg"
         class="absolute top-1/2 right-4 -translate-y-1/2 w-6 h-6 text-gray-500 cursor-pointer"
         fill="none" viewBox="0 0 24 24" stroke="currentColor">
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
               d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
               d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
    </svg>
</div>
