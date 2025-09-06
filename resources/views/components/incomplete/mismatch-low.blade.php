<x-form.textarea id="mismatch_low_{{ $item->id }}" name="mismatch_low[{{ $item->id }}]"
    label="Mismatch Details (40%-89%)" required placeholder="Enter Corrected Details"
    wire:model="formData.mismatch_low.{{ $item->id }}" />
@error("formData.aadhar.$item->id")
    <span class="text-red-600 text-sm">{{ $message }}</span>
@enderror
