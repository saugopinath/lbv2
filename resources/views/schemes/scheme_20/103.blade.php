<div class="grid md:grid-cols-2 gap-4 mt-4">
<x-form.input
    type="text"
    name="ifscode"
    label="IFSC Code"
    wire:model="formData.ifscode"
/><x-form.input
    type="text"
    name="bankname"
    label="Bank Name"
    wire:model="formData.bankname"
/></div>
<div class="grid md:grid-cols-2 gap-4 mt-4">
<x-form.input
    type="text"
    name="bank_branch_name"
    label="Bank Branch Name"
    wire:model="formData.bank_branch_name"
/><x-form.input
    type="text"
    name="bankaccountnumber"
    label="Bank Account Number"
    wire:model="formData.bankaccountnumber"
/></div>
<div class="grid md:grid-cols-2 gap-4 mt-4">
<x-form.input
    type="text"
    name="confirmbankaccountnumber"
    label="Confirm Bank Account Number"
    wire:model="formData.confirmbankaccountnumber"
/></div>
