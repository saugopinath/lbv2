<div class="grid md:grid-cols-3 gap-4 mt-4">
<div x-data="{ formData: @entangle('formData').live }"   x-cloak x-transition>
    <x-form.select name="app_type" label="Application Type" wire:model="formData.app_type">
    <option value="">-- Select Application Type --</option>
    <option value="0">Normal Entry</option>
<option value="1">Duare Sarkar</option>

</x-form.select>
</div><div x-data="{ formData: @entangle('formData').live }"   x-cloak x-transition>
    <x-form.input type="date" name="app_date" label="Application Date" wire:model="formData.app_date" />
</div><div x-data="{ formData: @entangle('formData').live }"   x-cloak x-transition>
    <x-form.input type="date" name="ds_date" label="Duare Sarkar Date" wire:model="formData.ds_date" />
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div x-data="{ formData: @entangle('formData').live }"   x-cloak x-transition>
    <x-form.input type="text" name="age" label="Age" wire:model="formData.age" />
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div x-data="{ formData: @entangle('formData').live }"   x-cloak x-transition>
    <x-form.input type="date" name="dob" label="Date of Birth" wire:model="formData.dob" />
</div><div x-data="{ formData: @entangle('formData').live }"   x-cloak x-transition>
    <x-form.input type="text" name="mfname" label="Mother's Name" wire:model="formData.mfname" />
</div><div x-data="{ formData: @entangle('formData').live }"   x-cloak x-transition>
    <x-form.input type="text" name="reg_no" label="Duare Sarkar Registration Number" wire:model="formData.reg_no" />
</div></div>
<div class="grid md:grid-cols-2 gap-4 mt-4">
<div x-data="{ formData: @entangle('formData').live }"   x-cloak x-transition>
    <x-form.input type="text" name="full_name" label="Applicant Name" wire:model="formData.full_name" />
</div><div x-data="{ formData: @entangle('formData').live }"   x-cloak x-transition>
    <x-form.input type="text" name="email_id" label="Email Address" wire:model="formData.email_id" />
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div x-data="{ formData: @entangle('formData').live }"   x-cloak x-transition>
    <x-form.input type="text" name="ffname" label="Father's Name" wire:model="formData.ffname" />
</div><div x-data="{ formData: @entangle('formData').live }"   x-cloak x-transition>
    <x-form.select name="caste" label="Caste" wire:model="formData.caste">
    <option value="">-- Select Caste --</option>
    <option value="1">SC</option>
<option value="2">ST</option>
<option value="3">OBC</option>
<option value="4">General</option>

</x-form.select>
</div><div x-data="{
    formData: @entangle('formData').live,
    visible: false,
    init() {
        this.$watch('formData.caste', value => {
            this.visible = ['1','2','3'].includes(String(value));
            if (!this.visible) {
                this.formData.cas_cer_no = null;
            }
        });
    }
}" x-show="visible" x-effect="!visible && (formData.cas_cer_no = null)" x-cloak x-transition>
    <x-form.input type="text" name="cas_cer_no" label="Caste Certificate Number" wire:model="formData.cas_cer_no" />
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div x-data="{ formData: @entangle('formData').live }"   x-cloak x-transition>
    <x-form.select name="mar_statu" label="Marital Status" wire:model="formData.mar_statu">
    <option value="">-- Select Marital Status --</option>
    <option value="1">Un Married</option>
<option value="2">Married</option>
<option value="3">Widow</option>
<option value="4">Divorcee</option>
<option value="5">Widower</option>

</x-form.select>
</div><div x-data="{
    formData: @entangle('formData').live,
    visible: false,
    init() {
        this.$watch('formData.mar_statu', value => {
            this.visible = ['2','3','5'].includes(String(value));
            if (!this.visible) {
                this.formData.sfname = null;
            }
        });
    }
}" x-show="visible" x-effect="!visible && (formData.sfname = null)" x-cloak x-transition>
    <x-form.input type="text" name="sfname" label="Spouse's Name" wire:model="formData.sfname" />
</div></div>
