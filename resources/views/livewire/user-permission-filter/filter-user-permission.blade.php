<div class="bg-white shadow rounded-lg p-4 border border-gray-200">
    <fieldset class="space-y-4">
        <legend class="text-sm font-semibold text-gray-600 mb-2">Filter Users</legend>

        <div class="grid gap-4 md:grid-cols-2">
            {{-- Role --}}
            <div class="relative">
                <x-form.select name="role" label="Role" wire:model.live="role" required>
                    <option value="">Select Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </x-form.select>
                <x-loading-indicator target="role" />
            </div>

            {{-- Office Type --}}
            <div class="relative">
                <x-form.select name="mapping_level" label="Office Type" wire:model.live="selectedMappingLevel" required>
                    <option value="">Select Office Type</option>
                    @foreach ($mapping_levels as $mapping_level)
                        <option value="{{ $mapping_level->office_type_id }}">{{ $mapping_level->officeType?->name }}</option>
                    @endforeach
                </x-form.select>
                <x-loading-indicator target="selectedMappingLevel" />
            </div>

            {{-- State --}}
            <div class="relative">
                <x-form.select name="state" label="State" wire:model.live="selectedState" required>
                    <option value="">Select State</option>
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </x-form.select>
                <x-loading-indicator target="selectedState" />
            </div>

            {{-- District (only for specific mapping levels) --}}
            @if (in_array($selectedMappingLevel, [153, 154]))
                <div class="relative">
                    <x-form.select name="district" label="District" wire:model.live="selectedDistrict" required>
                        <option value="">Select District</option>
                        @foreach ($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                        @endforeach
                    </x-form.select>
                    <x-loading-indicator target="selectedDistrict" />
                </div>
            @endif

            {{-- Office --}}
            <div class="relative md:col-span-2">
                <x-form.select name="office" label="Office" wire:model="office" required>
                    <option value="">Select Office</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </x-form.select>
                <x-loading-indicator target="office" />
            </div>
        </div>
    </fieldset>

    {{-- Buttons --}}
    <div class="flex justify-end items-center gap-3 mt-4">
        <x-button.primary
             x-on:click="
        Livewire.dispatch('showLoader');
        $wire.applyFilters();
    "
            type="button"
            class="bg-blue-500 text-white whitespace-nowrap cursor-pointer"
        >
            Search
        </x-button.primary >

        <x-button.primary
            wire:click="resetFilters"
            type="button"
            class="bg-green-500 text-white whitespace-nowrap cursor-pointer"
        >
            Reset
        </x-button.primary >
    </div>
</div>
