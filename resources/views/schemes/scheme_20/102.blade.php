<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.select
    name="rural_urban"
    label="Rural/Urbar"
    wire:ignore
    wire:model.live="formData.rural_urban"
>
    <option value="">-- Select Rural/Urbar --</option>
    
</x-form.select>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.select
    name="district_id"
    label="District"
    wire:ignore
    wire:model.live="formData.district_id"
>
    <option value="">-- Select District --</option>
    
</x-form.select>
</div></div>
<div class="grid md:grid-cols-1 gap-4 mt-4">
<div   >
    <x-form.select
    name="blockurban"
    label="Block/Municipality"
    wire:ignore
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
    wire:ignore
    wire:model.live="formData.gpWard"
>
    <option value="">-- Select GP / Ward --</option>
    
</x-form.select>
</div></div>
