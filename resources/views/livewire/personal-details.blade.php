<div x-data="{ appType: '{{ old('app_type') }}',caste: '{{ old('caste') }}'}">
    <form wire:submit.prevent="save({{ $mode == '0' ? 0 : 1 }})">
        <x-form.select name="app_type" label="Application Type:" required x-model="appType" wire:model="app_type">
            <option value="">Select</option>
            @foreach ($app_types as $app_type)
            <option value="{{ $app_type->id }}">{{ $app_type->name }}</option>
            @endforeach
        </x-form.select>
        <x-form.date name="app_date" label="Application Date:" required wire:model="app_date" />
        <template x-if="appType == '30'">
            <div>
                <x-form.input name="reg_no" label="Registration no." required wire:model="reg_no" />
                <x-form.date name="ds_date" label="Date:" required wire:model="ds_date" />
            </div>
        </template>
        <x-form.input name="name" label="Name" required wire:model="name" />
        <x-form.input name="mobile" label="Mobile Number" required wire:model="mobile" />
        <x-form.input name="email" label="Email Id" wire:model="email" />
        <x-form.date name="dob" wire:model.lazy="dob" label="Date of Birth" required />
        <x-form.input name="age" wire:model="age" label="Age" readonly />
        <x-form.label name="name" label="Father's Name" />
        <x-form.input name="ffname" label="First Name" required wire:model="ffname" />
        <x-form.input name="fmname" label="Middle Name" wire:model="fmname" />
        <x-form.input name="flname" label="Last Name" required wire:model="flname" />
        <x-form.label name="name" label="Mother's Name" />
        <x-form.input name="mfname" label="First Name" required wire:model="mfname" />
        <x-form.input name="mmname" label="Middle Name" wire:model="mmname" />
        <x-form.input name="mlname" label="Last Name" required wire:model="mlname" />
        <x-form.label name="name" label="Spouse Name (if applicable)" />
        <x-form.input name="sfname" label="First Name" wire:model="sfname" />
        <x-form.input name="smname" label="Middle Name" wire:model="smname" />
        <x-form.input name="slname" label="Last Name" wire:model="slname" />
        <x-form.select name="caste" label="Caste" required x-model="caste" wire:model="caste">
            <option value="">Select</option>
            @foreach ($castes as $caste)
            <option value="{{ $caste->id }}">{{ $caste->name }}</option>
            @endforeach
        </x-form.select>
        <template x-if="caste && caste != '19'">
            <x-form.input name="cas_cer_no" label="SC/ST Certificate No." required wire:model="cas_cer_no" />
        </template>
        <x-button.danger type="submit">
            {{ $mode == '0' ? 'Save' : 'Save & Next' }}
        </x-button.danger>
    </form>
</div>