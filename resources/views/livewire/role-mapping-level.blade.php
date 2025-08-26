<div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
    <div class="grid gap-6 mb-2 md:grid-cols-3">
        <x-form.select name="mapping_level" id="mapping_level" label="Mapping Level" wire:model.live="selectedMappingLevel">
            <option value="">----ALL----</option>
            @foreach ($mapping_levels as $mapping_level)
                <option value="{{ $mapping_level->code }}">{{ $mapping_level->name }}</option>
            @endforeach
        </x-form.select>

        <x-form.select name="role" id="role" label="Select Role" required wire:model="role">
            <option value="">Select</option>
            @foreach ($roles as $r)
                <option value="{{ $r->id }}">{{ $r->name }}</option>
            @endforeach
        </x-form.select>
    </div>
</div>
