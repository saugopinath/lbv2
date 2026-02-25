@props([
'locationLevel' => 'district',
'setMethod' => 'setLocationLevel'
])

<div class="mb-4 border-b border-gray-200">
    <nav class="flex space-x-8">
        @foreach(['district' => 'District', 'block' => 'Block', 'sub_district' => 'Sub District'] as $value => $label)
        <button type="button"
            wire:click="{{ $setMethod }}('{{ $value }}')"
            class="py-2 px-1 border-b-2 font-medium text-sm transition-colors"
            x-bind:class="{
                    'border-blue-500 text-blue-600': location_level === '{{ $value }}',
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': location_level !== '{{ $value }}'
                }">
            {{ $label }}
        </button>
        @endforeach
    </nav>
</div>