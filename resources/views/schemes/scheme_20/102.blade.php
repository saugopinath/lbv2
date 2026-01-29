

    <div class="grid md:grid-cols-3 gap-4 mt-4">
        <x-form.select
            name="district_id"
            label="District"
            wire:ignore
            data-field="district"
            data-wire="district_id">
            <option value="">-- Select District --</option>
        </x-form.select>

        <x-form.select
            name="rural_urban"
            label="Rural / Urban"
            wire:ignore
            data-field="rural_urban"
            data-wire="rural_urban">
            <option value="">-- Select Rural / Urban --</option>
        </x-form.select>

        <x-form.select
            name="blockurban"
            label="Block / Municipality"
            wire:ignore
            data-field="block"
            data-wire="blockurban">
            <option value="">-- Select Block / Municipality --</option>
        </x-form.select>
    </div>

    <div class="grid md:grid-cols-3 gap-4 mt-4">
        <x-form.select
            name="gpWard"
            label="GP / Ward"
            wire:ignore
            data-field="gpWard"
            data-wire="gpWard">
            <option value="">-- Select GP / Ward --</option>
        </x-form.select>
    </div>

