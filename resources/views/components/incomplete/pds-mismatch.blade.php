<p class="text-sm text-gray-600">Old Aadhaar Number: {{ $item->old_value ?? 'N/A' }}</p>

<x-form.input id="pds_{{ $item->id }}" name="pds[{{ $item->id }}]" label="Aadhaar Number" required
    wire:model="formData.pds.{{ $item->id }}" placeholder="Enter Correct Aadhaar Number"
    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)" />

@error("formData.pds.$item->id")
    <span class="text-red-600 text-sm">{{ $message }}</span>
@enderror
