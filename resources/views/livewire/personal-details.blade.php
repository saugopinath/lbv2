<div x-data="{ appType: '{{ $app_type }}',caste: '{{ $caste }}',MarStatu: '{{ $mar_statu }}'}">
    <form wire:submit.prevent="save">
        @if($this->hideAppTypeSection)
        <x-form.select name="app_type" label="Application Type:" required x-model="appType" wire:model="app_type">
            <option value="">Select</option>
            @foreach ($app_types as $app_type)
            <option value="{{ $app_type->id }}">{{ $app_type->name }}</option>
            @endforeach
        </x-form.select>
        <x-form.date name="app_date" label="Application Date:" required wire:model="app_date" />
        @endif
        <template x-if="appType == 30">
            <div>
                <x-form.input name="reg_no" label="Registration no." required wire:model="reg_no" />
                <x-form.date name="ds_date" label="Date:" required wire:model="ds_date" />
            </div>
        </template>
        <x-form.input name="name" label="Name" required wire:model="name" />
        <x-form.input name="mobile" label="Mobile Number" required wire:model="mobile" />
        <x-form.input name="email" label="Email Id" wire:model="email" />
        <x-form.date name="dob" wire:model.lazy="dob" label="Date of Birth" required />
        <x-form.input name="age" wire:model="age" label="Age (as on {{ $currentDate }})" readonly />
        <x-form.label name="name" label="Father's Name" />
        <x-form.input name="ffname" label="Full Name" required wire:model="ffname" />
        <x-form.label name="name" label="Mother's Name" />
        <x-form.input name="mfname" label="Full Name" required wire:model="mfname" />
        <x-form.select name="mar_statu" label="Mar Status:" required x-model="MarStatu" wire:model="mar_statu">
            <option value="">Select</option>
            @foreach ($mar_status as $mar_statu)
            <option value="{{ $mar_statu->id }}">{{ $mar_statu->name }}</option>
            @endforeach
        </x-form.select>
        <template x-if="MarStatu && MarStatu != 24 && MarStatu != 26">
            <div>
                <x-form.label name="name" label="Spouse Name" />
                <x-form.input name="sfname" label="Full Name" required wire:model="sfname" />
            </div>
        </template>
        <x-form.select name="caste" label="Caste" required x-model="caste" wire:model="caste">
            <option value="">Select</option>
            @foreach ($castes as $caste)
            <option value="{{ $caste->id }}">{{ $caste->name }}</option>
            @endforeach
        </x-form.select>
        <template x-if="caste && caste != 19">
            <x-form.input name="cas_cer_no" label="SC/ST Certificate No." required wire:model="cas_cer_no" />
        </template>
        <x-button.danger type="submit">
            {{ $mode == '0' ? 'Save' : 'Save & Next' }}
        </x-button.danger>
    </form>
</div>