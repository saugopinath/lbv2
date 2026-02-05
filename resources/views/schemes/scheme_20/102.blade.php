<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.select
    name="district_id"
    label="District"
    wire:ignore
     data-wire="district_id"
      required
    wire:model.live="formData.district_id"
>
    <option value="">-- Select District --</option>
    
</x-form.select>
</div><div   >
    <x-form.select
    name="rural_urban"
    label="Rural/Urbar"
    wire:ignore
     data-wire="rural_urban"
      required
    wire:model.live="formData.rural_urban"
>
    <option value="">-- Select Rural/Urbar --</option>
    
</x-form.select>
</div><div   >
    <x-form.select
    name="blockurban"
    label="Block/Municipality"
    wire:ignore
     data-wire="blockurban"
      required
    wire:model.live="formData.blockurban"
>
    <option value="">-- Select Block/Municipality --</option>
    
</x-form.select>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.select
    name="gpWard"
    label="GP / Ward"
    wire:ignore
     data-wire="gpWard"
      required
    wire:model.live="formData.gpWard"
>
    <option value="">-- Select GP / Ward --</option>
    
</x-form.select>
</div><div   >
    <x-form.select
    name="state"
    label="State"
    
     
      required
    wire:model.live="formData.state"
>
    <option value="">-- Select State --</option>
    <option value="19">West Bengal</option>

</x-form.select>
</div><div   >
    <x-form.input
    type="text"
    name="policestation"
    label="Police Station"
    placeholder="Enter Police Station"
    
    
    required
    wire:model.live="formData.policestation"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="villtowncity"
    label="Village / Town / City"
    placeholder="Enter Village / Town / City"
    
    
    required
    wire:model.live="formData.villtowncity"
/>
</div><div   >
    <x-form.input
    type="text"
    name="housepremiseno"
    label="House / Premise No"
    placeholder="Enter House / Premise No"
    
    
    
    wire:model.live="formData.housepremiseno"
/>
</div><div   >
    <x-form.input
    type="text"
    name="postoffice"
    label="Post Office"
    placeholder="Enter Post Office"
    
    
    required
    wire:model.live="formData.postoffice"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="pincode"
    label="Pin Code"
    placeholder="Enter Pin Code"
    
    
    required
    wire:model.live="formData.pincode"
/>
</div></div>
