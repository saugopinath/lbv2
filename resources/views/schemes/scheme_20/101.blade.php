<div class="grid md:grid-cols-3 gap-4 mt-4">
    <x-form.select name="app_type" label="Application Type" wire:model="formData.app_type">
        <option value="">-- Select Application Type --</option>
        <option value="Normal Entry">Normal Entry</option>
        <option value="Duare Sarkar">Duare Sarkar</option>

    </x-form.select><x-form.input type="date" name="app_date" label="Application Date"
        wire:model="formData.app_date" /><x-form.input type="text" name="mobile_no" label="Mobile Number"
        wire:model="formData.mobile_no" />
</div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
    <x-form.input type="date" name="ds_date" label="Duare Sarkar Date" wire:model="formData.ds_date" />
</div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
    <x-form.input type="text" name="age" label="Age" wire:model="formData.age" /><x-form.input type="date" name="dob"
        label="Date of Birth" wire:model="formData.dob" /><x-form.input type="text" name="mfname" label="Mother's Name"
        wire:model="formData.mfname" />
</div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
    <x-form.input type="text" name="sfname" label="Spouse's Name" wire:model="formData.sfname" /><x-form.input
        type="text" name="cas_cer_no" label="Caste Certificate Number" wire:model="formData.cas_cer_no" /><x-form.input
        type="text" name="full_name" label="Applicant Name" wire:model="formData.full_name" />
</div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
    <x-form.input type="text" name="reg_no" label="Duare Sarkar Registration Number" wire:model="formData.reg_no" />
</div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
    <x-form.input type="text" name="email_id" label="Email Address" wire:model="formData.email_id" /><x-form.input
        type="text" name="ffname" label="Father's Name" wire:model="formData.ffname" /><x-form.select name="mar_statu"
        label="Marital Status" wire:model="formData.mar_statu">
        <option value="">-- Select Marital Status --</option>
        <option value="Un Married">Un Married</option>
        <option value="Married">Married</option>
        <option value="Widow">Widow</option>
        <option value="Divorcee">Divorcee</option>
        <option value="Widower">Widower</option>

    </x-form.select>
</div>
<div class="grid md:grid-cols-1 gap-4 mt-4">


    <x-form.select name="castes" label="Caste" wire:model="formData.castes">
        <option value="">-- Select Caste --</option>
    </x-form.select>
    
    <div id="caste-cert-wrapper" class="grid md:grid-cols-1 gap-4 mt-4 hidden">
        <x-form.input type="text" name="caste_certificate_no" label="Caste Certificate Number"
            wire:model="formData.caste_certificate_no" data-wire="caste_certificate_no" />
    </div>

</div>