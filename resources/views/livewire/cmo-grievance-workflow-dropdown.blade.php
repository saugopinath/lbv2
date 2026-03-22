<div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-xl font-bold mb-4">Enter Filter Criteria</h2>
    <form wire:submit.prevent="submit">
        <x-form.select name="process_type" label="Process Type" wire:model="process_type" required>
            @foreach ($types as $type)
            <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </x-form.select>
        @if($districts)
         <x-form.select name="district" label="Districts" wire:model="district">
            <option value="">--Choose--</option>
            @foreach ($districts as $district)
            <option value="{{ $district->lgd_code }}">{{ $district->name }}</option>
            @endforeach
        </x-form.select>
        @endif
        <div class="flex justify-end mt-4">
            <div class="flex justify-end">
                <x-button.primary type="submit" class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                    GO
                </x-button.primary>
            </div>
        </div>
    </form>
</div>