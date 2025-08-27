<div class="bg-gray-100 p-4 rounded shadow mb-4">
    <form wire:submit.prevent="submit">
        <div class="bg-white shadow-md rounded-2xl p-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-700">Create OfficeMaster</h2>
        </div>

        <div class="bg-white shadow-md rounded p-4 space-y-4">
            <div class="grid gap-6 mb-2 md:grid-cols-3">
                <x-form.input id="name" name="name" label="Name" wire:model="name"
                    x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />

                <x-form.textarea id="address" name="address" label="Address" required wire:model="address" />

                <x-form.input name="zip" label="Pin Code" wire:model="zip" required
                    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,6)" />

                <!-- Office Type -->
                <x-form.select name="mapping_level" label="Office Type" wire:model.live="selectedMappingLevel" required>
                    <option value="">-- Select Office Type --</option>
                    @foreach ($mapping_levels as $mapping_level)
                        <option value="{{ $mapping_level->code }}">{{ $mapping_level->name }}</option>
                    @endforeach
                </x-form.select>

                <!-- State -->
                <x-form.select name="state" label="State" wire:model.live="selectedState" required>
                    <option value="">-- Select State --</option>
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </x-form.select>

                <!-- District Dropdown -->
                @if (in_array($selectedMappingLevel, ['152', '153', '154']))
                    <x-form.select name="district_id" label="District" wire:model.live="selectedDistrict" required>
                        <option value="">-- Select District --</option>
                        @foreach ($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                        @endforeach
                    </x-form.select>
                @endif

                <!-- Subdivision Dropdown -->
                @if ($selectedMappingLevel == '154')
                    <x-form.select name="subdivision_id" label="Subdivision" wire:model.live="selectedSubdivision" required>
                        <option value="">-- Select Subdivision --</option>
                        @foreach ($subdivisions as $subdivision)
                            <option value="{{ $subdivision->id }}">{{ $subdivision->name }}</option>
                        @endforeach
                    </x-form.select>
                @endif

                <!-- Block Dropdown -->
                @if ($selectedMappingLevel == '153')
                    <x-form.select name="blockurban" label="Block" wire:model.live="selectedBlockurban" required>
                        <option value="">-- Select Block --</option>
                        @foreach ($blocks as $block)
                            <option value="{{ $block->id }}">{{ $block->name }}</option>
                        @endforeach
                    </x-form.select>
                @endif
            </div>

            <!-- Buttons -->
            <div class="flex items-center mt-6 gap-3">
                <x-button.primary type="submit" class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                    Create
                </x-button.primary>
                <a href="{{ route('officemasters.index') }}"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow-md whitespace-nowrap">
                    Back
                </a>
            </div>
        </div>
    </form>
</div>
