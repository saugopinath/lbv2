<p class="text-sm text-gray-600">Old Mobile: {{ $item->old_value ?? 'N/A' }}</p>

<x-form.input 
    id="dup_mobile_{{ $item->id }}" 
    name="dup_mobile[{{ $item->id }}]"
    label="New Mobile Number" required
    wire:model="formData.new_mobile.{{ $item->id }}"
    placeholder="Enter New Mobile"
    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10)" />

     @error("formData.aadhar.$item->id")
        <span class="text-red-600 text-sm">{{ $message }}</span>
    @enderror