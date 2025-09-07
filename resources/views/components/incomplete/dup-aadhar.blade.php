<p class="text-sm text-gray-600">Old Aadhaar: {{ $item->old_value ?? 'N/A' }}</p>

{{--  <x-form.input id="dup_aadhar_{{ $item->id }}" name="dup_aadhar[{{ $item->id }}]" label="New Aadhaar Number"
    required wire:model="formData.new_aadhar.{{ $item->id }}" placeholder="Enter Correct Aadhaar"
    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)" />

@error("formData.new_aadhar.$item->id")
    <span class="text-red-600 text-sm">{{ $message }}</span>
@enderror  --}}


@if ($stage === 'verifier')
    <x-form.input id="dup_aadhar_{{ $item->id }}" name="dup_aadhar[{{ $item->id }}]" label="New Aadhaar Number"
        required wire:model="formData.new_aadhar.{{ $item->id }}" placeholder="Enter Correct Aadhaar"
        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)" />

    @error("formData.new_aadhar.$item->id")
        <span class="text-red-600 text-sm">{{ $message }}</span>
    @enderror
    
@elseif ($stage === 'approver')
    {{-- Readonly / display for approver --}}
    <div class="mb-2">
        <label class="block text-gray-700 font-medium">New Aadhaar Number</label>
        <div class="px-3 py-2 border rounded bg-gray-100">
            {{ $aadhaarDecrypted ?? 'N/A' }}
        </div>
    </div>
@endif
