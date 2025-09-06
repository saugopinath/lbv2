<x-form.input id="no_mobile_{{ $item->id }}" name="no_mobile[{{ $item->id }}]" label="Mobile Number" required
    wire:model="formData.mobile.{{ $item->id }}" placeholder="Enter Mobile Number"
    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10)" />
    
    @error("formData.mobile.$item->id")
        <span class="text-red-600 text-sm">{{ $message }}</span>
    @enderror