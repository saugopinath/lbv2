<div class="grid md:grid-cols-2 gap-4 mt-4">
<div wire:key="field-mobile_no">
<x-form.input
    type="text"
    name="mobile_no"
    wire:model="formData.mobile_no"
    label="Mobile Number"
/></div>
<div wire:key="field-rural_urban">
<x-form.select
    name="rural_urban"
    wire:model.live="formData.rural_urban"
    label="Rural/Urbar"
>
    <option value="">-- Select Rural/Urbar --</option></x-form.select>
</div>
<div wire:key="field-gpWard">
<x-form.select
    name="gpWard"
    wire:model.live="formData.gpWard"
    label="GP / Ward"
>
    <option value="">-- Select GP / Ward --</option></x-form.select>
</div>
<div wire:key="field-policestation">
<x-form.input
    type="text"
    name="policestation"
    wire:model="formData.policestation"
    label="Police Station"
/></div>
<div wire:key="field-housepremiseno">
<x-form.input
    type="text"
    name="housepremiseno"
    wire:model="formData.housepremiseno"
    label="House / Premise No"
/></div>
<div wire:key="field-pincode">
<x-form.input
    type="text"
    name="pincode"
    wire:model="formData.pincode"
    label="Pin Code"
/></div>
<div wire:key="field-district_id">
<x-form.select
    name="district_id"
    wire:model.live="formData.district_id"
    label="District"
>
    <option value="">-- Select District --</option></x-form.select>
</div>
<div wire:key="field-blockurban">
<x-form.select
    name="blockurban"
    wire:model.live="formData.blockurban"
    label="Block/Municipality"
>
    <option value="">-- Select Block/Municipality --</option></x-form.select>
</div>
<div wire:key="field-state">
<x-form.input
    type="text"
    name="state"
    wire:model="formData.state"
    label="State"
/></div>
<div wire:key="field-villtowncity">
<x-form.input
    type="text"
    name="villtowncity"
    wire:model="formData.villtowncity"
    label="Village / Town / City"
/></div>
<div wire:key="field-postoffice">
<x-form.input
    type="text"
    name="postoffice"
    wire:model="formData.postoffice"
    label="Post Office"
/></div>
</div>