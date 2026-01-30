<div class="grid md:grid-cols-3 gap-4 mt-4">
<x-form.input
    type="text"
    name="bankname"
    label="Bank Name"
    wire:model="formData.bankname"
/><x-form.input
    type="text"
    name="bankaccountnumber"
    label="Bank Account Number"
    wire:model="formData.bankaccountnumber"
/><x-form.input
    type="text"
    name="ifscode"
    label="IFSC Code"
    wire:model="formData.ifscode"
/></div>
<div class="grid md:grid-cols-3 gap-4 mt-4">
<x-form.input
    type="text"
    name="bank_branch_name"
    label="Bank Branch Name"
    wire:model="formData.bank_branch_name"
/><x-form.input
    type="text"
    name="confirmbankaccountnumber"
    label="Confirm Bank Account Number"
    wire:model="formData.confirmbankaccountnumber"
/></div>
