<div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
    <div class="grid gap-6 mb-2 md:grid-cols-3">
        <div wire:key="field-norm-district_id" class="{{ $visible['district_dropdown'] ? '' : 'hidden' }}">
            <x-form.select name="district_id" label="District" data-wire="district_id"
                wire:model.live="formData.district_id">
                <option value="">-- Select District --</option>

                </x-form.select>
            </div>
        <div wire:key="field-norm-assemblie" class="{{ $visible['assembly_dropdown'] ? '' : 'hidden' }}">
            <x-form.select name="assemblie" label="Assemblie" data-wire="assemblie"
                wire:model.live="formData.assemblie">
                <option value="">-- Select Assemblie --</option>

                </x-form.select>
            </div>
        <div wire:key="field-norm-rural_urban" class="{{ $visible['rural_urban_dropdown'] ? '' : 'hidden' }}">
            <x-form.select name="rural_urban" label="Rural/Urbar" data-wire="rural_urban"
                wire:model.live="formData.rural_urban">
                <option value="">-- Select Rural/Urbar --</option>

                </x-form.select>
            </div>
        <div wire:key="field-norm-blockurban" class="{{ $visible['block_dropdown'] ? '' : 'hidden' }}">
            <x-form.select name="blockurban" label="Block/Municipality" data-wire="blockurban"
                wire:model.live="formData.blockurban">
                <option value="">-- Select Block/Municipality --</option>

                </x-form.select>
            </div>
        <div wire:key="field-norm-gpward">
            <x-form.select name="gpward" label="GP/Ward" data-wire="gpward"
                wire:model.live="formData.gpward">
                <option value="">-- Select GP/Ward --</option>

                </x-form.select>
            </div>
        </div>
    <div class="flex gap-4 mt-6">
        <button type="button" wire:click="filterData" class="px-4 py-2 bg-green-600 text-white rounded shadow">
            Go
            </button>

        <button type="button" wire:click="resetFilters" class="px-4 py-2 bg-red-600 text-white rounded shadow">
            Reset
            </button>
        </div>

    @push('scripts')
        <script src="{{ asset('js/master-data/master-data-v2.js') }}"></script>
    @endpush
</div>
