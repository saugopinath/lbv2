<x-form.textarea 
    id="mismatch_high_{{ $item->id }}" 
    name="mismatch_high[{{ $item->id }}]"
    label="Mismatch Details (90%-100%)" required
    placeholder="Enter Corrected Details"
    wire:model="formData.mismatch_high.{{ $item->id }}" />

     @error("formData.aadhar.$item->id")
        <span class="text-red-600 text-sm">{{ $message }}</span>
    @enderror