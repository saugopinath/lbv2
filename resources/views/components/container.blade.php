@props([
    'dark' => false,
])
<div class="bg-white mb-4 border rounded">
    <div class="{{ $dark ? 'bg-gray-700' : 'bg-gray-100' }} py-3 px-4 flex justify-between border-b rounded-t">
        <h2 class="font-bold {{ $dark ? 'text-gray-50' : 'text-gray-500' }} uppercase">
            {{ $title }}
        </h2>
        <div class="{{ $dark ? 'text-gray-50' : 'text-gray-500' }}">
            {{ $icon ?? '' }}
        </div>
    </div>
    <div class="py-3 px-4">
        {{ $body }}
    </div>
</div>
