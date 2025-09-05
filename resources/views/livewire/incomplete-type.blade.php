<div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
    <div class="grid gap-6 mb-2 md:grid-cols-3">

        <!-- Dropdown -->
        <x-form.select wire:model="incompleteList" id="incomplete_list" name="incomplete_list" label="Incomplete List"
            required>
            <option value="">-- Select --</option>
            @foreach ($results as $result)
                <option value="{{ $result->code }}">{{ $result->name }}</option>
            @endforeach
        </x-form.select>

        <!-- Search Button -->
        <div class="flex items-center mt-6 gap-3">
            <x-button.primary wire:click="resetIncompleteFilters"
                class="bg-green-500 text-white whitespace-nowrap cursor-pointer">
                Reset
            </x-button.primary>
            <x-button.primary type="button" wire:click="search"
                class="bg-blue-500 text-white px-6 py-2 rounded-lg shadow whitespace-nowrap cursor-pointer">
                Filter
            </x-button.primary>            
        </div>
    </div>
</div>
