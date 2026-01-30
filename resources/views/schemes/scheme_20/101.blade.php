<div class="grid md:grid-cols-3 gap-4 mt-4">
<x-form.select
    name="app_type"
    label="Application Type"
    wire:model="app_type"
>
    <option value="">-- Select Application Type --</option>
    <option value="Normal Entry">Normal Entry</option>
<option value="Duare Sarkar">Duare Sarkar</option>

</x-form.select><x-form.input
    type="text"
    name="full_name"
    label="Applicant Name"
    wire:model="full_name"
/><x-form.input
    type="text"
    name="email_id"
    label="Email Address"
    wire:model="email_id"
/></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<x-form.input
    type="text"
    name="mobile_no"
    label="Mobile Number"
    wire:model="mobile_no"
/><x-form.input
    type="date"
    name="app_date"
    label="Application Date"
    wire:model="app_date"
/><x-form.input
    type="date"
    name="ds_date"
    label="Duare Sarkar Date"
    wire:model="ds_date"
/></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<x-form.input
    type="text"
    name="age"
    label="Age"
    wire:model="age"
/></div>
<div class="grid md:grid-cols-2 gap-4 mt-4">
<x-form.input
    type="date"
    name="dob"
    label="Date of Birth"
    wire:model="dob"
/><x-form.input
    type="text"
    name="mfname"
    label="Mother's Name"
    wire:model="mfname"
/></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<x-form.input
    type="text"
    name="sfname"
    label="Spouse's Name"
    wire:model="sfname"
/><x-form.input
    type="text"
    name="cas_cer_no"
    label="Caste Certificate Number"
    wire:model="cas_cer_no"
/><x-form.input
    type="text"
    name="ffname"
    label="Father's Name"
    wire:model="ffname"
/></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<x-form.select
    name="mar_statu"
    label="Marital Status"
    wire:model="mar_statu"
>
    <option value="">-- Select Marital Status --</option>
    <option value="Un Married">Un Married</option>
<option value="Married">Married</option>
<option value="Widow">Widow</option>
<option value="Divorcee">Divorcee</option>
<option value="Widower">Widower</option>

</x-form.select><x-form.select
    name="castes"
    label="Caste"
    wire:model="castes"
>
    <option value="">-- Select Caste --</option>
    <option value="SC">SC</option>
<option value="ST">ST</option>
<option value="OBC">OBC</option>
<option value="General">General</option>

</x-form.select><x-form.input
    type="text"
    name="reg_no"
    label="Duare Sarkar Registration Number"
    wire:model="reg_no"
/></div>
