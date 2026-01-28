<div
    class="address-master-data"
    x-data
    x-init="
        const run = () => {
            if (window.initAddressMasterData) {
                window.initAddressMasterData();
            }
        };

        if (!window.__masterDataLoaded) {
            window.__masterDataLoaded = true;

            const s = document.createElement('script');
            s.src = '{{ asset('js/master-data/master-data-v2.js') }}';
            s.onload = () => setTimeout(run, 300);
            document.body.appendChild(s);
        } else {
            setTimeout(run, 100);
        }
    "
>
    <div class="grid md:grid-cols-3 gap-4 mt-4">
        {{-- DISTRICT --}}
        <x-form.select
            name="district_id"
            label="District"
            wire:ignore
            data-field="district"
            data-wire="district_id"
        >
            <option value="">-- Select District --</option>
        </x-form.select>

        {{-- RURAL / URBAN --}}
        <x-form.select
            name="rural_urban"
            label="Rural / Urban"
            wire:ignore
            data-field="rural_urban"
            data-wire="rural_urban"
        >
            <option value="">-- Select Rural / Urban --</option>
        </x-form.select>

        {{-- BLOCK / MUNICIPALITY --}}
        <x-form.select
            name="blockurban"
            label="Block / Municipality"
            wire:ignore
            data-field="block"
            data-wire="blockurban"
        >
            <option value="">-- Select Block / Municipality --</option>
        </x-form.select>
    </div>

    <div class="grid md:grid-cols-3 gap-4 mt-4">
        {{-- GP / WARD --}}
        <x-form.select
            name="gpWard"
            label="GP / Ward"
            wire:ignore
            data-field="panchayat"
            data-wire="gpWard"
        >
            <option value="">-- Select GP / Ward --</option>
        </x-form.select>

        <x-form.input name="state" label="State" wire:model="formData.state" />
        <x-form.input name="policestation" label="Police Station" wire:model="formData.policestation" />
    </div>
</div>
