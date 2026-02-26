<div class="p-6 bg-white rounded-xl shadow"
    x-data="{
        capacity_type: @entangle('capacity_type'),
        location_level: @entangle('location_level')
     }">

    {{-- Success Message --}}
    @if (session()->has('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header with Toggle --}}
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Scheme Capacity Configuration</h2>
        <div class="flex items-center space-x-3">
            <span class="text-sm font-medium text-gray-600">Capacity Type:</span>
            <div class="flex rounded-lg border border-gray-300 overflow-hidden">
                @foreach(['full_scheme' => 'Full Scheme', 'location' => 'Location Wise'] as $value => $label)
                <button type="button"
                    wire:click="$set('capacity_type', '{{ $value }}')"
                    class="px-4 py-2 text-sm font-medium transition-colors {{ $loop->first ? '' : 'border-l border-gray-300' }}"
                    x-bind:class="{
                            'bg-blue-600 text-white': capacity_type === '{{ $value }}',
                            'bg-white text-gray-700 hover:bg-gray-50': capacity_type !== '{{ $value }}'
                        }">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Full Scheme View --}}
    <div x-show="capacity_type === 'full_scheme'" x-transition.duration.300ms>
        <x-capacity-table
            :items="$schemes"
            type="scheme"
            data="schemes_data"
            saveMethod="saveScheme"
            titleField="name"
            subtitleField="id"
            subtitlePrefix="ID: " />
    </div>

    {{-- Location Wise View --}}
    <div x-show="capacity_type === 'location'" x-transition.duration.100ms>
        {{-- Scheme Dropdown --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-500 mb-1">
                Scheme <span class="text-red-500">*</span>
            </label>
            <select wire:model.live="location_scheme_id"
                class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select Scheme</option>
                @foreach($schemes as $scheme)
                <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                @endforeach
            </select>
            @error('location_scheme_id')
            <span class="text-xs text-red-600">{{ $message }}</span>
            @enderror
        </div>

        @if($location_scheme_id)
        {{-- Location Tabs --}}
        <x-location-tabs :location-level="$location_level" set-method="setLocationLevel" />

        {{-- Filters Row --}}
        <div class="mb-4 grid grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">District</label>
                <select wire:model.live="district_id"
                    class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Districts</option>
                    @foreach($districts as $district)
                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="location_level === 'block'">
                <label class="block text-xs font-medium text-gray-500 mb-1">Block</label>
                <select wire:model.live="block_id"
                    class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Blocks</option>
                    @foreach($blocks as $block)
                    <option value="{{ $block->id }}">{{ $block->name }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="location_level === 'sub_district'">
                <label class="block text-xs font-medium text-gray-500 mb-1">Sub District</label>
                <select wire:model.live="sub_district_id"
                    class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Sub Districts</option>
                    @foreach($subdivisions as $subdivision)
                    <option value="{{ $subdivision->id }}">{{ $subdivision->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Locations Table --}}
        @php
        $locations = match($location_level) {
        'district' => $district_id ? $districts->where('id', $district_id) : $districts,
        'block' => $block_id ? $blocks->where('id', $block_id) : $blocks,
        'sub_district' => $sub_district_id ? $subdivisions->where('id', $sub_district_id) : $subdivisions,
        default => collect()
        };
        @endphp

        <x-capacity-table
            :items="$locations"
            :type="$location_level"
            data="locations_data"
            saveMethod="saveLocation"
            titleField="name"
            subtitleField="id"
            subtitlePrefix="Code: "
            :disabled="!$location_scheme_id" />
        @endif
    </div>
</div>