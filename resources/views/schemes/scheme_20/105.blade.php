<div class='mt-4 space-y-4'><div   >
    <x-form.input
    type="text"
    name="res"
    label="rter"
    placeholder="Enter rter"
    
    
    required
    wire:model.live="formData.res"
/>
</div><div   >
        <x-form.checkbox
        name="zx"
        value="1"
        label="dvz"
        wire:model.live="formData.zx"
    />

</div></div>