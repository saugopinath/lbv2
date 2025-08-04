<div>
    <form wire:submit.prevent="save">
        <x-form.input name="ifscode" label="IFS Code" wire:model.lazy="ifscode" required />
        <x-form.input
            name="bank_name"
            label="Bank Name"
            wire:model.defer="bankname"
            required
            disabled />
        <x-form.input
            name="bank_branch_name"
            label="Bank Branch Name"
            wire:model.defer="bankbranchname"
            required
            disabled />
        <x-form.input name="bankaccountnumber" label="Bank Account Number" wire:model="bankaccountnumber" />
        <x-form.input name="confirmbankaccountnumber" label="Confirm Bank Account Number" wire:model="confirmbankaccountnumber" required />
        @if ($mode != '0')
        <x-button.danger>Previous</x-button.danger>
        @endif
        <x-button.danger type="submit">
            {{ $mode == '0' ? 'Save' : 'Save & Next' }}
        </x-button.danger>
    </form>
</div>