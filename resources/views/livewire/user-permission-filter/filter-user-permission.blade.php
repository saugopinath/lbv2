<div class="bg-white shadow rounded-lg p-4 border border-gray-200">
    <fieldset class="space-y-4">
<<<<<<< HEAD
        <legend class="text-sm font-semibold text-gray-600 mb-2">🔎 Filter Users</legend>
=======
        <legend class="text-sm font-semibold text-gray-600 mb-2">Filter Users</legend>
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5

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
<<<<<<< HEAD
                    <x-form.select name="district" label="District" wire:model="selectedDistrict" required>
=======
                    <x-form.select name="district" label="District" wire:model.live="selectedDistrict" required>
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
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
<<<<<<< HEAD
        <button
            wire:click="applyFilters"
            type="button"
            class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded transition duration-200"
        >
            🔍 Search
        </button>

        <button
            wire:click="resetFilters"
            type="button"
            class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded transition duration-200"
        >
            ♻️ Reset
        </button>
=======
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
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
    </div>
</div>
