<div class="grid md:grid-cols-3 gap-4 mt-4">
<x-form.select
    name="app_type"
    label="Application Type"
    wire:model="formData.app_type"
>
    <option value="">-- Select Application Type --</option>
    <option value="Normal Entry">Normal Entry</option>
<option value="Duare Sarkar">Duare Sarkar</option>

</x-form.select><x-form.input
    type="text"
    name="full_name"
    label="Applicant Name"
    wire:model="formData.full_name"
/><x-form.input
    type="text"
    name="email_id"
    label="Email Address"
    wire:model="formData.email_id"
/></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<x-form.input
    type="text"
    name="mobile_no"
    label="Mobile Number"
    wire:model="formData.mobile_no"
/></div>
