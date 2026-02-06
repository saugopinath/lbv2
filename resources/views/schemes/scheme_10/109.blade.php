<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.select
    name="land"
    label="land"
    data-wire="land"
    
     
      required
    wire:model.live="formData.land"
>
    <option value="">-- Select land --</option>
    <option value="1">SC</option>
<option value="2">ST</option>
<option value="3">OBC</option>
<option value="4">General</option>

</x-form.select>
</div><div x-data="{formData: @entangle('formData').live,visible: false,
    sync() {this.visible = ['1','2','3'].includes(String(this.formData.land));
        if (!this.visible) {
            this.formData.ccc = null;
        }
    },
    init() {
        this.sync();
        this.$watch('formData.land', () => this.sync());
    }
}" x-show="visible" x-cloak>
    <x-form.input
    type="text"
    name="ccc"
    label="ccc"
    placeholder="Enter ccc"
    
    
    
    wire:model.live="formData.ccc"
/>
</div><div   >
    <x-form.input
    type="number"
    name="mobile"
    label="mobile"
    placeholder="Enter mobile"
    
    
    required
    wire:model.live="formData.mobile"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="name"
    label="name"
    placeholder="Enter name"
    
    
    required
    wire:model.live="formData.name"
/>
</div></div>
