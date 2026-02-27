<div class="p-6 bg-white rounded-xl shadow"
    x-data="{
        capacity_type: @entangle('capacity_type'),
        location_level: @entangle('location_level')
     }">

    @if (session()->has('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if (session()->has('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded flex items-center">
        {{ session('error') }}
    </div>
    @endif
    @error('validation')
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded flex items-center">
        {{ $message }}
    </div>
    @enderror
    @error('location_scheme_id')
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded flex items-center">
        {{ $message }}
    </div>
    @enderror
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center space-x-3">
            <span class="text-md font-medium text-gray-600">Capacity Type:</span>
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
    {{-- Application Type (Common for Full Scheme & Location Wise) --}}
    <div class="mb-4 grid grid-cols-3 gap-4" x-show="capacity_type">
        {{-- Action Type --}}
        {{-- Scheme Dropdown - This will show only when capacity_type is 'location' --}}
        <div x-show="capacity_type === 'location'" x-transition>
            <x-form.select name="location_scheme_id" label="Scheme" wire:model.live="location_scheme_id" required>
                <option value="">-- Select Scheme --</option>
                @foreach ($schemes as $scheme)
                <option value="{{ $scheme->id }}">
                    {{ $scheme->name }}
                </option>
                @endforeach
            </x-form.select>
        </div>
        <x-form.select name="action_type" label="Action Type" wire:model.live="action_type" required>
            <option value="">-- Select Action Type --</option>
            @foreach($appTypeOptions as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </x-form.select>
    </div>
    {{-- Location Wise View --}}
    <div x-show="capacity_type === 'location'" x-transition.duration.100ms>
        @if($location_scheme_id && $action_type !=='')
        {{-- Location Tabs --}}
        <x-location-tabs :location-level="$location_level" set-method="setLocationLevel" />
        {{-- Filters Row --}}
        <div class="mb-4 grid grid-cols-3 gap-4">
            <div>
                <x-form.select name="district_id" label="District" wire:model.live="district_id" required>
                    <option value="">-- All District --</option>
                    @foreach ($districts as $district)
                    <option value="{{ $district->id }}">
                        {{ $district->name }}
                    </option>
                    @endforeach
                </x-form.select>
            </div>

            <div x-show="location_level === 'block'">
                <x-form.select name="block_id" label="Block" wire:model.live="block_id" required>
                    <option value="">-- All Block --</option>
                    @foreach ($blocks as $block)
                    <option value="{{ $block->id }}">
                        {{ $block->name }}
                    </option>
                    @endforeach
                </x-form.select>
            </div>

            <div x-show="location_level === 'sub_district'">
                <x-form.select name="sub_district_id" label="Sub District" wire:model.live="sub_district_id" required>
                    <option value="">-- All Sub District --</option>
                    @foreach ($subdivisions as $subdivision)
                    <option value="{{ $subdivision->id }}">
                        {{ $subdivision->name }}
                    </option>
                    @endforeach
                </x-form.select>
            </div>
        </div>

        {{-- Locations Table --}}

        <x-capacity-table
            :items="$locations"
            :type="$location_level"
            data="locations_data"
            saveMethod="saveLocation"
            deleteMethod="deleteLocationCapacity"
            titleField="name"
            subtitleField="id"
            subtitlePrefix="Code: "
            :disabled="!$location_scheme_id"
            :showExtraCondition="true" />
        @endif
    </div>
    {{-- Full Scheme View --}}
    <div x-show="capacity_type === 'full_scheme'" x-transition>
        @if($action_type !== '')
        <x-capacity-table
            :items="$schemes"
            type="scheme"
            data="schemes_data"
            saveMethod="saveScheme"
            deleteMethod="deleteSchemeCapacity"
            titleField="name"
            subtitleField="id"
            subtitlePrefix="ID: "
            :showExtraCondition="true" />
        @endif
    </div>
</div>