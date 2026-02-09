<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="full_name"
    label="Applicant Name"
    placeholder="Enter Applicant Name"
    
    
    required
    wire:model.live="formData.full_name"
/>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="mobile_no"
    label="Mobile Number"
    placeholder="Enter Mobile Number"
    
    
    required
    wire:model.live="formData.mobile_no"
/>
</div></div>
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
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.input
    type="date"
    name="app_date"
    label="Application Date"
    placeholder="Enter Application Date"
    
    
    required
    wire:model.live="formData.app_date"
/>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div x-data="{formData: @entangle('formData').live,visible: false,
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
    
    
    
    wire:model.live="formData.ds_date"
/>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
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
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.input
    type="date"
    name="dob"
    label="Date of Birth"
    placeholder="Enter Date of Birth"
    
    
    required
    wire:model.live="formData.dob"
/>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="ffname"
    label="Father's Name"
    placeholder="Enter Father's Name"
    
    
    required
    wire:model.live="formData.ffname"
/>
</div></div>
