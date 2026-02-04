<div class="mt-4 space-y-3">
<div   >
    <x-form.input
    type="text"
    name="bankaccountnumber"
    label="Bank Account Number"
    placeholder="Enter Bank Account Number"
    
    wire:model.live="formData.bankaccountnumber"
/>
</div><div   >
    <x-form.input
    type="text"
    name="confirmbankaccountnumber"
    label="Confirm Bank Account Number"
    placeholder="Enter Confirm Bank Account Number"
    
    wire:model.live="formData.confirmbankaccountnumber"
/>
</div>
</div>