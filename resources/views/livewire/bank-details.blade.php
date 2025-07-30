<div>
<!-- IFSC Code Input -->
    <x-form.input name="ifscode" label="IFS Code" wire:model.lazy="ifscode" required />

    <!-- Auto-filled Bank Name -->
    <x-form.input 
        name="bank_name" 
        label="Bank Name" 
        wire:model.defer="bankname" 
        required 
        disabled 
    />

    <!-- Auto-filled Branch Name -->
    <x-form.input 
        name="bank_branch_name" 
        label="Bank Branch Name" 
        wire:model.defer="bankbranchname" 
        required 
        disabled 
    />
    <x-form.input name="bankaccountnumber" label="Bank Account Number" />
    <x-form.input name="confirmbankaccountnumber" label="Confirm Bank Account Number" required />
    @if ($mode != '0')
    <x-button.danger>Previous</x-button.danger>
    @endif
    <x-button.danger>
        {{ $mode == '0' ? 'Save' : 'Save & Next' }}
    </x-button.danger>
</div>