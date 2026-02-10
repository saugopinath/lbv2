<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="ifscode"
    label="IFSC Code"
    placeholder="Enter IFSC Code"
    
    
    required
    
    
    wire:model.live="formData.ifscode"
/>
</div><div   >
    <x-form.input
    type="text"
    name="bankname"
    label="Bank Name"
    placeholder="Enter Bank Name"
    
    readonly
    required
    
    
    wire:model.live="formData.bankname"
/>
</div><div   >
    <x-form.input
    type="text"
    name="bank_branch_name"
    label="Bank Branch Name"
    placeholder="Enter Bank Branch Name"
    
    readonly
    required
    
    
    wire:model.live="formData.bank_branch_name"
/>
</div></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<div   >
    <x-form.input
    type="text"
    name="bankaccountnumber"
    label="Bank Account Number"
    placeholder="Enter Bank Account Number"
    
    
    required
    
    
    wire:model.live="formData.bankaccountnumber"
/>
</div><div   >
    <x-form.input
    type="text"
    name="confirmbankaccountnumber"
    label="Confirm Bank Account Number"
    placeholder="Enter Confirm Bank Account Number"
    
    
    required
    
    
    wire:model.live="formData.confirmbankaccountnumber"
/>
</div></div>
