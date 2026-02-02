<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.input
    type="date"
    name="app_date"
    label="Application Date"
    
    wire:model.live="formData.app_date"
/>
</div><div   >
    <x-form.input
    type="text"
    name="mobile_no"
    label="Mobile Number"
    
    wire:model.live="formData.mobile_no"
/>
</div><div   >
    <x-form.input
    type="date"
    name="ds_date"
    label="Duare Sarkar Date"
    
    wire:model.live="formData.ds_date"
/>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="age"
    label="Age"
    
    wire:model.live="formData.age"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.input
    type="date"
    name="dob"
    label="Date of Birth"
    
    wire:model.live="formData.dob"
/>
</div><div   >
    <x-form.input
    type="text"
    name="mfname"
    label="Mother's Name"
    
    wire:model.live="formData.mfname"
/>
</div><div   >
    <x-form.input
    type="text"
    name="full_name"
    label="Applicant Name"
    
    wire:model.live="formData.full_name"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="email_id"
    label="Email Address"
    
    wire:model.live="formData.email_id"
/>
</div><div   >
    <x-form.input
    type="text"
    name="ffname"
    label="Father's Name"
    
    wire:model.live="formData.ffname"
/>
</div><div   >
    <x-form.select
    name="app_type"
    label="Application Type"
    
    wire:model.live="formData.app_type"
>
    <option value="">-- Select Application Type --</option>
    <option value="0">Normal Entry</option>
<option value="1">Duare Sarkar</option>

</x-form.select>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="reg_no"
    label="Duare Sarkar Registration Number"
    
    wire:model.live="formData.reg_no"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.select
    name="mar_statu"
    label="Marital Status"
    
    wire:model.live="formData.mar_statu"
>
    <option value="">-- Select Marital Status --</option>
    <option value="1">Un Married</option>
<option value="2">Married</option>
<option value="3">Widow</option>
<option value="4">Divorcee</option>
<option value="5">Widower</option>

</x-form.select>
</div><div   >
    <x-form.select
    name="caste"
    label="Caste"
    
    wire:model.live="formData.caste"
>
    <option value="">-- Select Caste --</option>
    <option value="1">SC</option>
<option value="2">ST</option>
<option value="3">OBC</option>
<option value="4">General</option>

</x-form.select>
</div><div x-data="{formData: @entangle('formData').live,visible: false,
    sync() {this.visible = ['2','3','5'].includes(String(this.formData.mar_statu));
        if (!this.visible) {
            this.formData.sfname = null;
        }
    },
    init() {
        this.sync();
        this.$watch('formData.mar_statu', () => this.sync());
    }
}" x-show="visible" x-cloak>
    <x-form.input
    type="text"
    name="sfname"
    label="Spouse's Name"
    
    wire:model.live="formData.sfname"
/>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div x-data="{formData: @entangle('formData').live,visible: false,
    sync() {this.visible = ['1','2','3'].includes(String(this.formData.caste));
        if (!this.visible) {
            this.formData.cas_cer_no = null;
        }
    },
    init() {
        this.sync();
        this.$watch('formData.caste', () => this.sync());
    }
}" x-show="visible" x-cloak>
    <x-form.input
    type="text"
    name="cas_cer_no"
    label="Caste Certificate Number"
    
    wire:model.live="formData.cas_cer_no"
/>
</div></div>
