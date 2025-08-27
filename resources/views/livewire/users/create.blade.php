<div class="bg-gray-100 p-4 rounded shadow mb-4">
    <form wire:submit.prevent="submit">
        <div class="bg-white shadow-md rounded-2xl p-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-700">User</h2>
        </div>

        <div class="bg-white shadow-md rounded p-4 space-y-8">
            <fieldset class="border border-gray-200 rounded-xl p-4">
                <div class="grid gap-6 mt-3 md:grid-cols-3">
                    <x-form.input id="name" name="name" label="Name" wire:model="name"
                        x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />

                    <x-form.input id="mobile" name="mobile" label="Mobile Number" required wire:model="mobile"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10)" />

                    <x-form.input id="email" name="email" type="email" label="Email address" required
                        wire:model="email" />
                    <x-form.input id="password" name="password" type="password" label="Password" required
                        wire:model="password" />
                </div>
            </fieldset>

            <fieldset class="border border-gray-200 rounded-xl p-4">
                <div class="grid gap-6 mt-3 md:grid-cols-3">
                    <x-form.select name="scheme" id="scheme" label="Scheme" required wire:model="selectscheme">
                        <option value="">Select</option>
                        @foreach ($schemes as $scheme)
                            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.select name="role" id="role" label="Role" required wire:model.live="role">
                        <option value="">Select</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.select name="mapping_level" label="Office Type" wire:model.live="selectedMappingLevel"
                        required>
                        <option value="">-- Select Office Type --</option>
                        @foreach ($mapping_levels as $mapping_level)
                            <option value="{{ $mapping_level->code }}">{{ $mapping_level->name }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.select name="state" label="State" wire:model.live="selectedState" required>
                        <option value="">-- Select State --</option>
                        @foreach ($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </x-form.select>

                    @if (in_array($selectedMappingLevel, [153, 154]))
                        <x-form.select name="district" label="District" wire:model="selectedDistrict" required>
                            <option value="">-- Select District --</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                            @endforeach
                        </x-form.select>
                    @endif

                    <x-form.select name="office" id="office" label="Offices" required wire:model="office">
                        <option value="">-- Select Office --</option>
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                        @endforeach
                    </x-form.select>
                </div>
            </fieldset>
            <div class="flex items-center mt-6 gap-3">
                <x-button.primary type="submit" class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                    Create
                </x-button.primary>
                <a href="{{ route('user-managements.index') }}"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow-md whitespace-nowrap">
                    Back
                </a>
            </div>
        </div>
    </form>
</div>
