<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.select
    name="app_type"
    label="Application Type"
    data-wire="app_type"
    
     
      required
    wire:model.live="formData.app_type"
>
    <option value="">-- Select Application Type --</option>
    <option value="1">Normal Entry</option>
<option value="2">Duare Sarkar Entry</option>

</x-form.select>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div x-data="{formData: @entangle('formData').live,visible: false,
    sync() {this.visible = ['2'].includes(String(this.formData.app_type));
        if (!this.visible) {
            this.formData.reg_no = null;
        }
    },
    init() {
        this.sync();
        this.$watch('formData.app_type', () => this.sync());
    }
}" x-show="visible" x-cloak>
    <x-form.input
    type="text"
    name="reg_no"
    label="Duare Sarkar Registration Number"
    placeholder="Enter Duare Sarkar Registration Number"
    
    
    
    
    
    wire:model.live="formData.reg_no"
/>
</div><div x-data="{formData: @entangle('formData').live,visible: false,
    sync() {this.visible = ['2'].includes(String(this.formData.app_type));
        if (!this.visible) {
            this.formData.ds_date = null;
        }
    },
    init() {
        this.sync();
        this.$watch('formData.app_type', () => this.sync());
    }
}" x-show="visible" x-cloak>
    <x-form.input
    type="date"
    name="ds_date"
    label="Duare Sarkar Date"
    placeholder="Enter Duare Sarkar Date"
    
    
    
    :min="$minDate"
    :max="$maxDate"
    wire:model.live="formData.ds_date"
/>
</div><div   >
    <x-form.input
    type="text"
    name="full_name"
    label="Applicant Name"
    placeholder="Enter Applicant Name"
    
    
    required
    
    
    wire:model.live="formData.full_name"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="mobile_no"
    label="Mobile Number"
    placeholder="Enter Mobile Number"
    
    
    required
    
    
    wire:model.live="formData.mobile_no"
/>
</div><div   >
    <x-form.input
    type="date"
    name="app_date"
    label="Application Date"
    placeholder="Enter Application Date"
    
    
    required
    :min="$minDate"
    :max="$maxDate"
    wire:model.live="formData.app_date"
/>
</div><div   >
    <x-form.input
    type="date"
    name="dob"
    label="Date of Birth"
    placeholder="Enter Date of Birth"
    
    
    required
    :min="$minDOB"
    :max="$maxDOB"
    wire:model.live="formData.dob"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="age"
    label="Age"
    placeholder="Enter Age"
    
    readonly
    required
    
    
    wire:model.live="formData.age"
/>
</div><div   >
    <x-form.input
    type="text"
    name="email_id"
    label="Email Address"
    placeholder="Enter Email Address"
    
    
    
    
    
    wire:model.live="formData.email_id"
/>
</div><div   >
    <x-form.input
    type="text"
    name="ffname"
    label="Father's Name"
    placeholder="Enter Father's Name"
    
    
    required
    
    
    wire:model.live="formData.ffname"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="mfname"
    label="Mother's Name"
    placeholder="Enter Mother's Name"
    
    
    required
    
    
    wire:model.live="formData.mfname"
/>
</div><div   >
    <x-form.select
    name="mar_statu"
    label="Marital Status"
    data-wire="mar_statu"
    
     
      required
    wire:model.live="formData.mar_statu"
>
    <option value="">-- Select Marital Status --</option>
    <option value="1">Un Married</option>
<option value="2">Married</option>
<option value="3">Widow</option>
<option value="4">Divorcee</option>
<option value="5">Widower</option>

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
    placeholder="Enter Spouse's Name"
    
    
    
    
    
    wire:model.live="formData.sfname"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.select
    name="caste"
    label="Caste"
    data-wire="caste"
    
     
      required
    wire:model.live="formData.caste"
>
    <option value="">-- Select Caste --</option>
    <option value="1">SC</option>
<option value="2">ST</option>
<option value="3">OBC</option>
<option value="4">General</option>

</x-form.select>
</div><div x-data="{formData: @entangle('formData').live,visible: false,
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
    placeholder="Enter Caste Certificate Number"
    
    
    
    
    
    wire:model.live="formData.cas_cer_no"
/>
</div></div>
