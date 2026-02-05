<div class='space-y-2'><div class="pl-6">
    <x-form.input
        type="text"
        name="das"
        label="fsgd"  
            wire:model.live="formData.das"                  
    />
</div><div class="mt-4 mb-2 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
    <span class="font-semibold text-indigo-700">
        sfas
    </span>
</div><div class="pl-6">
    <x-form.input
        type="number"
        name="vsz"
        label="fdhgd"    
         wire:model.live="formData.vsz"                
    />
</div><div class='my-3'></div><div class="pl-6">
    <x-form.input
        type="text"
        name="ram"
        label="zdsfgh"  
            wire:model.live="formData.ram"                  
    />
</div><div class="pl-6">
    
        <x-form.checkbox type="checkbox"   label="zsfdhgzd" name="gvsz" wire:model.live="formData.gvsz" value="1"/>
      
</div><div class="pl-6">
    <x-form.select name="fffs" label="sdgfvs" wire:model.live="formData.fffs">
        <option value="">-- Select sdgfvs --</option>
        <option value="1">fdgvbfd</option>
<option value="2">dfgbd</option>

    </x-form.select>
</div><div class='my-3'></div></div>