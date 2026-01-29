

    <div class="grid md:grid-cols-3 gap-4 mt-4">
        <x-form.select
            name="district_id"
            label="District"
            wire:ignore
            id="district"
            wire:model="formData.district_id">
            <option value="">-- Select District --</option>
        </x-form.select>

        <x-form.select
            name="rural_urban"
            label="Rural / Urban"
            wire:ignore
            id="rural_urban"
            wire:model="formData.rural_urban">
            <option value="">-- Select Rural / Urban --</option>
        </x-form.select>

        <x-form.select
            name="blockurban"
            label="Block / Municipality"
            wire:ignore
            id="block"
            wire:model="formData.blockurban">
            <option value="">-- Select Block / Municipality --</option>
        </x-form.select>
    </div>

    <div class="grid md:grid-cols-3 gap-4 mt-4">
        <x-form.select
            name="gpWard"
            label="GP / Ward"
            wire:ignore
            id="gpWard"
            wire:model="formData.gpWard">
            <option value="">-- Select GP / Ward --</option>
        </x-form.select>
    </div>

