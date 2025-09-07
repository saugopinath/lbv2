<x-form.input id="no_aadhar_{{ $item->id }}" name="no_aadhar[{{ $item->id }}]" label="Aadhaar Number" required
    wire:model="formData.aadhar.{{ $item->id }}" placeholder="Enter New Aadhaar Number"
    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)" />

@error("formData.aadhar.$item->id")
    <span class="text-red-600 text-sm">{{ $message }}</span>
@enderror
