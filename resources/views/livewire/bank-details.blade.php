<div>
    <form wire:submit.prevent="save">
        <div class="grid gap-6 mb-4 md:grid-cols-2 pl-4 pr-4">
            <div>
                <x-form.input name="ifscode" label="IFS Code" required wire:model.lazy="ifscode" />
            </div>
            <div>
                <x-form.input name="bankname" label="Bank Name" required wire:model.defer="bankname" disabled />
            </div>
        </div>
        <div class="grid gap-6 mb-4 md:grid-cols-2 pl-4 pr-4">
            <div>
                <x-form.input name="bank_branch_name" label="Bank Branch Name" required wire:model.defer="bankbranchname" />
            </div>
            <div>
                <x-form.input name="bankaccountnumber" label="Bank Account Number" required wire:model.defer="bankaccountnumber" disabled />
            </div>
        </div>
        <div class="grid gap-6 mb-4 md:grid-cols-2 pl-4 pr-4">
            <div>
                <x-form.input name="confirmbankaccountnumber" label="Confirm Bank Account Number" required wire:model.defer="confirmbankaccountnumber" />
            </div>
        </div>
        <div class="flex justify-between mt-4 pl-6 pr-6">
            @if ($mode != '0')
            <x-button.danger>Previous</x-button.danger>
            @endif
            <x-button.primary type="submit">
                {{ $mode == '0' ? 'Save' : 'Save & Next' }}
            </x-button.primary>
        </div>
    </form>
</div>