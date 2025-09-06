<p class="text-sm text-gray-600">Old Account: {{ $item->old_value ?? 'N/A' }}</p>
<p class="text-sm text-gray-600">
    IFSC: {{ optional($item->beneficiaryCommonList->beneficiaryBank)->ifsc ?? 'N/A' }}
</p>

<x-form.input 
    id="dup_bank_account_{{ $item->id }}"
    name="dup_bank_account[{{ $item->id }}]" 
    label="New Bank Account Number" required
    wire:model="formData.new_bank_account.{{ $item->id }}" 
    placeholder="Enter New Bank Account"
    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,16)" />

     @error("formData.aadhar.$item->id")
        <span class="text-red-600 text-sm">{{ $message }}</span>
    @enderror