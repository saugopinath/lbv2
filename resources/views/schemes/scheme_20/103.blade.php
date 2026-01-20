<div class="grid md:grid-cols-2 gap-4 mt-4">
<div wire:key="field-ifscode">
<x-form.input
    type="text"
    name="ifscode"
    wire:model="formData.ifscode"
    label="IFSC Code"
/></div>
<div wire:key="field-bankname">
<x-form.input
    type="text"
    name="bankname"
    wire:model="formData.bankname"
    label="Bank Name"
/></div>
<div wire:key="field-bank_branch_name">
<x-form.input
    type="text"
    name="bank_branch_name"
    wire:model="formData.bank_branch_name"
    label="Bank Branch Name"
/></div>
<div wire:key="field-bankaccountnumber">
<x-form.input
    type="text"
    name="bankaccountnumber"
    wire:model="formData.bankaccountnumber"
    label="Bank Account Number"
/></div>
<div wire:key="field-confirmbankaccountnumber">
<x-form.input
    type="text"
    name="confirmbankaccountnumber"
    wire:model="formData.confirmbankaccountnumber"
    label="Confirm Bank Account Number"
/></div>
</div>