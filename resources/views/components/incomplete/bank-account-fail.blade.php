<p class="text-sm text-gray-600">Old Account: {{ $item->old_value ?? 'N/A' }}</p>
<p class="text-sm text-gray-600">
    IFSC: {{ optional($item->beneficiaryCommonList->beneficiaryBank)->ifsc ?? 'N/A' }}
</p>

<x-form.input id="bank_account_{{ $item->id }}" name="bank_account[{{ $item->id }}]" label="Bank Account Number"
    required wire:model="formData.bank_account.{{ $item->id }}" placeholder="Enter New Account Number"
    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,16)" />

@error("formData.bank_account.$item->id")
    <span class="text-red-600 text-sm">{{ $message }}</span>
@enderror
