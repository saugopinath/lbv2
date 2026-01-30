<div class="grid md:grid-cols-3 gap-4 mt-4">
<x-form.select
    name="rural_urban"
    label="Rural/Urbar"
    wire:model="formData.rural_urban"
>
    <option value="">-- Select Rural/Urbar --</option>
    
</x-form.select><x-form.select
    name="gpWard"
    label="GP / Ward"
    wire:model="formData.gpWard"
>
    <option value="">-- Select GP / Ward --</option>
    
</x-form.select><x-form.input
    type="text"
    name="policestation"
    label="Police Station"
    wire:model="formData.policestation"
/></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<x-form.input
    type="text"
    name="housepremiseno"
    label="House / Premise No"
    wire:model="formData.housepremiseno"
/><x-form.input
    type="text"
    name="pincode"
    label="Pin Code"
    wire:model="formData.pincode"
/><x-form.select
    name="blockurban"
    label="Block/Municipality"
    wire:model="formData.blockurban"
>
    <option value="">-- Select Block/Municipality --</option>
    
</x-form.select></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<x-form.input
    type="text"
    name="state"
    label="State"
    wire:model="formData.state"
/><x-form.input
    type="text"
    name="villtowncity"
    label="Village / Town / City"
    wire:model="formData.villtowncity"
/><x-form.input
    type="text"
    name="postoffice"
    label="Post Office"
    wire:model="formData.postoffice"
/></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<x-form.select
    name="district_id"
    label="District"
    wire:model="formData.district_id"
>
    <option value="">-- Select District --</option>
    
</x-form.select></div>
