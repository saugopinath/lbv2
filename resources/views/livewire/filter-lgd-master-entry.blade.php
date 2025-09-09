<div class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
    <div>
        @if ($visible['district_dropdown'])
        <x-form.select name="district_id" label="District" wire:model.live="selectedDistrict" required>
            <option value="">Select</option>
            @foreach ($districts as $district)
            <option value="{{ $district->id }}">{{ $district->name }}</option>
            @endforeach
        </x-form.select>
        @endif
    </div>
    <div>
        @if ($visible['rural_urban_dropdown'])
        <x-form.select name="rural_urban" label="Rural / Urban" wire:model.live="selectedRuralurban" required>
            <option value="">Select</option>
            @foreach (Config::get('constants.rural_urban') as $key => $val)
            <option value="{{ $key }}">{{ $val }}</option>
            @endforeach
        </x-form.select>
        @endif
    </div>
    <div>
        @if ($visible['block_dropdown'])
        <x-form.select name="blockurban"
            label="{{ $selectedRuralurban == 2 ? 'Block' : ($selectedRuralurban == 1 ? 'Municipality' : 'Block / Municipality') }}"
            wire:model.live="selectedBlockurban" required>
            <option value="">Select</option>
            @if ($selectedRuralurban == 2)
            @foreach ($blocks as $block)
            <option value="{{ $block->id }}">{{ $block->name }}</option>
            @endforeach
            @elseif ($selectedRuralurban == 1)
            @foreach ($urbanbodys as $urban)
            <option value="{{ $urban->id }}">{{ $urban->name }}</option>
            @endforeach
            @endif
        </x-form.select>
        @endif
    </div>
    <div>
        @if ($visible['gp_ward_dropdown'])
        <x-form.select name="gpWard"
            label="{{ $selectedRuralurban == 2 ? 'Gram Panchayat' : ($selectedRuralurban == 1 ? 'Ward No' : 'GP / Ward No') }}"
            wire:model.live="selectedGpWard" required>
            <option value="">Select</option>
            @foreach ($gps as $gp)
            <option value="{{ $gp->id }}">{{ $gp->name }}</option>
            @endforeach
            @foreach ($wards as $ward)
            <option value="{{ $ward->id }}">{{ $ward->name }}</option>
            @endforeach
        </x-form.select>
        @endif
    </div>
</div>