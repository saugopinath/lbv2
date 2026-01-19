<div class="grid md:grid-cols-2 gap-4 mt-4">
<div wire:key="field-app_type">
<x-form.select
    name="app_type"
    wire:model.live="formData.app_type"
    label="Application Type"
>
    <option value="">-- Select Application Type --</option><option value="0">Normal Entry</option>
<option value="1">Duare Sarkar</option>
</x-form.select>
</div>
<div wire:key="field-app_date">
<x-form.input
    type="date"
    name="app_date"
    wire:model="formData.app_date"
    label="Application Date"
/></div>
<div wire:key="field-ds_date">
<x-form.input
    type="date"
    name="ds_date"
    wire:model="formData.ds_date"
    label="Duare Sarkar Date"
/></div>
<div wire:key="field-age">
<x-form.input
    type="text"
    name="age"
    wire:model="formData.age"
    label="Age"
/></div>
<div wire:key="field-dob">
<x-form.input
    type="date"
    name="dob"
    wire:model="formData.dob"
    label="Date of Birth"
/></div>
<div wire:key="field-mfname">
<x-form.input
    type="text"
    name="mfname"
    wire:model="formData.mfname"
    label="Mother's Name"
/></div>
<div wire:key="field-sfname">
<x-form.input
    type="text"
    name="sfname"
    wire:model="formData.sfname"
    label="Spouse's Name"
/></div>
<div wire:key="field-cas_cer_no">
<x-form.input
    type="text"
    name="cas_cer_no"
    wire:model="formData.cas_cer_no"
    label="Caste Certificate Number"
/></div>
<div wire:key="field-reg_no">
<x-form.input
    type="text"
    name="reg_no"
    wire:model="formData.reg_no"
    label="Duare Sarkar Registration Number"
/></div>
<div wire:key="field-email_id">
<x-form.input
    type="text"
    name="email_id"
    wire:model="formData.email_id"
    label="Email Address"
/></div>
<div wire:key="field-ffname">
<x-form.input
    type="text"
    name="ffname"
    wire:model="formData.ffname"
    label="Father's Name"
/></div>
<div wire:key="field-mar_statu">
<x-form.select
    name="mar_statu"
    wire:model.live="formData.mar_statu"
    label="Marital Status"
>
    <option value="">-- Select Marital Status --</option><option value="0">Un Married</option>
<option value="1">Married</option>
<option value="2">Widow</option>
<option value="3">Divorcee</option>
<option value="4">Widower</option>
</x-form.select>
</div>
<div wire:key="field-full_name">
<x-form.input
    type="text"
    name="full_name"
    wire:model="formData.full_name"
    label="Applicant Name"
/></div>
</div>