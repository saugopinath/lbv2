<div class="bg-gray-100 p-4 rounded shadow mb-4">
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <form wire:submit.prevent="submit">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-700">
                Create Role Office Type Mappings
            </h2>
            <x-form.back-button :url="route('role-office-master-mappings')" />
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
            <div class="grid gap-6 mb-2 md:grid-cols-3">

                <x-form.select name="role" id="role" label="Role" wire:model="role">
                    <option value="">----Select Role----</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </x-form.select>
                @error('role')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror

                <x-form.select name="mapping_level" id="mapping_level" required label="Office Type"
                    wire:model="selectedMappingLevel">
                    <option value="">----ALL----</option>
                    @foreach ($mapping_levels as $mapping_level)
                        <option value="{{ $mapping_level->code }}">{{ $mapping_level->name }}</option>
                    @endforeach
                </x-form.select>
                @error('selectedMappingLevel')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror


                <div class="flex items-center mt-6 gap-3">
                    <x-button.primary type="submit" class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                        Create
                    </x-button.primary>
                    <x-button.success class="bg-blue-500 text-white whitespace-nowrap cursor-pointer"
                        wire:click="updateReset">
                        Reset
                    </x-button.success>
                </div>
            </div>
        </div>
    </form>
</div>