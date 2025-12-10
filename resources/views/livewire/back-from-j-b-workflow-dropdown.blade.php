<div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
    <x-form.select wire:model.live="application_type" label="Application Type" required>
        @foreach ($types as $type)
        <option value="{{ $type->id }}">{{ $type->name }}</option>
        @endforeach
    </x-form.select>
    <livewire:filter-lgd-master />
    <div class="flex gap-3">
        <x-button.primary
            x-on:click="$wire.search();"
            class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">Search</x-button.primary>
        <x-button.primary wire:click="resetAll"
            class="bg-green-500 text-white whitespace-nowrap cursor-pointer">Reset</x-button.primary>
    </div>
</div>