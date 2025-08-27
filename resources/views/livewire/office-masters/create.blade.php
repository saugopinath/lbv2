<div class="bg-gray-100 p-4 rounded shadow mb-4">
    <form wire:submit.prevent="submit">

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-700">
                Create OfficeMaster
            </h2>
        </div>

        <!-- Form Body -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
            <div class="grid gap-6 mb-2 md:grid-cols-3">

                <x-form.input id="name" name="name" label="Name" wire:model="name"
                    x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />

                <x-form.textarea id="address" name="address" label="Address" required wire:model="address" />

                <x-form.input name="zip" label="Pin Code" wire:model="zip" required
                    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,6)" />

                <x-form.select name="mapping_level" id="mapping_level" required label="Office Type"
                    wire:model="selectedMappingLevel">
                    <option value="">----ALL----</option>
                    @foreach ($mapping_levels as $mapping_level)
                        <option value="{{ $mapping_level->code }}">{{ $mapping_level->name }}</option>
                    @endforeach
                </x-form.select>
                <x-form.select name="state" id="state" required label="State" wire:model="selectedState">
                    <option value="">----ALL----</option>
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </x-form.select>
            </div>


            <livewire:filter-lgd-master  />

            {{--  <livewire:filter-lgd-master :selectedDistrict="$selectedDistrict"
            :selectedSubdivision="$selectedSubdivision"
            :selectedBlockurban="$selectedBlockurban"
            :selectedGpWard="$selectedGpWard" />  --}}


            <!-- Buttons -->
            <div class="flex items-center mt-6 gap-3">
                <x-button.primary type="submit" class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                    Create
                </x-button.primary>

                <a href="{{ route('role-office-master-mappings.index') }}"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow-md whitespace-nowrap">
                    Back
                </a>
            </div>
        </div>
    </form>
</div>
