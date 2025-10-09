<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Applicant Status Filter --}}
    <div>
        <x-form.select id="status" name="status" label="Application Status" wire:model="applicantStatus">
            <option value="">-- Select Status --</option>
            @foreach ($statusOptions as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </x-form.select>
        @error('applicantStatus')
            <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Caste Filter --}}
    <div>
        <x-form.select id="caste" name="caste" label="New Requested Caste" wire:model="casteId">
            <option value="">-- Select Caste --</option>
            @foreach ($casteOptions as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </x-form.select>
    </div>

    {{-- Buttons --}}
    <div class="flex justify-end gap-2">
        <x-button.loading-button action="applyFilters" text="Search" color="indigo" x-data
            x-on:click.prevent="Livewire.dispatch('showLoader'); $wire.applyFilters();" />

        <x-button.loading-button action="resetFilters" text="Reset" color="red" />
    </div>
</div>
