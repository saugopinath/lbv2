@props([
'locationLevel' => 'district',
'setMethod' => 'setLocationLevel'
])

<div class="mb-4">
    <div class="text-center mb-3">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Choose the Location</h3>
    </div>
    <nav class="flex p-1 bg-gray-100/80 backdrop-blur-sm rounded-2xl shadow-inner max-w-md mx-auto">
        @foreach(['district' => 'District', 'block' => 'Block', 'sub_district' => 'Sub District'] as $value => $label)
        <button type="button"
            wire:click="{{ $setMethod }}('{{ $value }}')"
            class="flex-1 py-2.5 px-4 rounded-xl font-medium text-sm transition-all duration-200 ease-out transform"
            x-bind:class="{
                    'bg-white text-blue-600 shadow-md shadow-blue-100 scale-105': location_level === '{{ $value }}',
                    'text-gray-600 hover:text-gray-900 hover:bg-white/70': location_level !== '{{ $value }}'
                }">
            {{ $label }}
        </button>
        @endforeach
    </nav>
</div>