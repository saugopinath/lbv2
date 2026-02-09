<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.select
    name="district_id"
    label="District"
    data-wire="district_id"
    wire:ignore
     
      required
    wire:model.live="formData.district_id"
>
    <option value="">-- Select District --</option>
    
</x-form.select>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.select
    name="rural_urban"
    label="Rural/Urbar"
    data-wire="rural_urban"
    wire:ignore
     
      required
    wire:model.live="formData.rural_urban"
>
    <option value="">-- Select Rural/Urbar --</option>
    
</x-form.select>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.select
    name="blockurban"
    label="Block/Municipality"
    data-wire="blockurban"
    wire:ignore
     
      required
    wire:model.live="formData.blockurban"
>
    <option value="">-- Select Block/Municipality --</option>
    
</x-form.select>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.select
    name="gpWard"
    label="GP / Ward"
    data-wire="gpWard"
    wire:ignore
     
      required
    wire:model.live="formData.gpWard"
>
    <option value="">-- Select GP / Ward --</option>
    
</x-form.select>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.select
    name="state"
    label="State"
    data-wire="state"
    
     
      required
    wire:model.live="formData.state"
>
    <option value="">-- Select State --</option>
    <option value="19">West Bengal</option>

</x-form.select>
</div></div>
