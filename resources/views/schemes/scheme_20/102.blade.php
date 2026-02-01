<div class="grid md:grid-cols-3 gap-4 mt-4">
<div x-data="{ formData: @entangle('formData').live }"  x-cloak>
    <x-form.select
    name="district_id"
    label="District"
    wire:model.live="formData.district_id"
>
    <option value="">-- Select District --</option>
    
</x-form.select>
</div><div x-data="{ formData: @entangle('formData').live }"  x-cloak>
    <x-form.select
    name="rural_urban"
    label="Rural/Urbar"
    wire:model.live="formData.rural_urban"
>
    <option value="">-- Select Rural/Urbar --</option>
    
</x-form.select>
</div><div x-data="{ formData: @entangle('formData').live }"  x-cloak>
    <x-form.select
    name="blockurban"
    label="Block/Municipality"
    wire:model.live="formData.blockurban"
>
    <option value="">-- Select Block/Municipality --</option>
    
</x-form.select>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div x-data="{ formData: @entangle('formData').live }"  x-cloak>
    <x-form.input
    type="text"
    name="state"
    label="State"
    wire:model.live="formData.state"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div x-data="{ formData: @entangle('formData').live }"  x-cloak>
    <x-form.select
    name="gpWard"
    label="GP / Ward"
    wire:model.live="formData.gpWard"
>
    <option value="">-- Select GP / Ward --</option>
    
</x-form.select>
</div><div x-data="{ formData: @entangle('formData').live }"  x-cloak>
    <x-form.input
    type="text"
    name="policestation"
    label="Police Station"
    wire:model.live="formData.policestation"
/>
</div><div x-data="{ formData: @entangle('formData').live }"  x-cloak>
    <x-form.input
    type="text"
    name="housepremiseno"
    label="House / Premise No"
    wire:model.live="formData.housepremiseno"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div x-data="{ formData: @entangle('formData').live }"  x-cloak>
    <x-form.input
    type="text"
    name="pincode"
    label="Pin Code"
    wire:model.live="formData.pincode"
/>
</div><div x-data="{ formData: @entangle('formData').live }"  x-cloak>
    <x-form.input
    type="text"
    name="villtowncity"
    label="Village / Town / City"
    wire:model.live="formData.villtowncity"
/>
</div><div x-data="{ formData: @entangle('formData').live }"  x-cloak>
    <x-form.input
    type="text"
    name="postoffice"
    label="Post Office"
    wire:model.live="formData.postoffice"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div x-data="{ formData: @entangle('formData').live }"  x-cloak>
    <x-form.input
    type="text"
    name="mobile_no"
    label="Mobile Number"
    wire:model.live="formData.mobile_no"
/>
</div></div>
