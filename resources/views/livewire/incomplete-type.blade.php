<div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
    <div class="grid gap-6 mb-2 md:grid-cols-3">

        <x-form.select wire:model.live="incompleteList" id="incomplete_list" name="incomplete_list" label="Incomplete List">
            <option value="">-- Select --</option>
            @foreach ($results as $result)
                <option value="{{ $result->code }}">{{ $result->name }}</option>
            @endforeach
        </x-form.select>

        @if ($button_show == 1)
            <div class="flex items-center mt-6 gap-3">
                <x-button.primary wire:click="resetIncompleteFilters"
                    class="bg-green-500 text-white">Reset</x-button.primary>
                <x-button.primary wire:click="search" class="bg-blue-500 text-white">Filter</x-button.primary>
            </div>
        @endif
    </div>
</div>
