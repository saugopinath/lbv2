<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Applicant Status Filter --}}
    <div>
        <x-form.select id="status" name="status" label="Application Status" wire:model="applicantStatus">
            <option value="">-- Select Status --</option>
<<<<<<< HEAD
            @foreach($statusOptions as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </x-form.select>
           @error('applicantStatus')
        <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
    @enderror
=======
            @foreach ($statusOptions as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </x-form.select>
        @error('applicantStatus')
            <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
        @enderror
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
    </div>

    {{-- Caste Filter --}}
    <div>
        <x-form.select id="caste" name="caste" label="New Requested Caste" wire:model="casteId">
            <option value="">-- Select Caste --</option>
<<<<<<< HEAD
            @foreach($casteOptions as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
=======
            @foreach ($casteOptions as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
            @endforeach
        </x-form.select>
    </div>

    {{-- Buttons --}}
    <div class="flex justify-end gap-2">
<<<<<<< HEAD
        <x-button.loading-button action="applyFilters" text="Search"  color="indigo" />
        <x-button.loading-button action="resetFilters" text="Reset" color="red" />
    </div>
</div>
=======
        <x-button.loading-button action="applyFilters" text="Search" color="indigo" x-data
            x-on:click.prevent="Livewire.dispatch('showLoader'); $wire.applyFilters();" />

        <x-button.loading-button action="resetFilters" text="Reset" color="red" />
    </div>
</div>
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
