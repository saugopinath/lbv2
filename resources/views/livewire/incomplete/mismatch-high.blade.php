<div class="mt-4">
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">

        @if ($bank_action === '' || $bank_action === '1' || $bank_action === '2')
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="application_id" label="Application Id" wire:model.defer="application_id" disabled />
                <x-form.input name="beneficiary_name" label="Beneficiary Name" wire:model.defer="beneficiary_name" disabled />
                <x-form.input name="mobile_no" label="Mobile No" wire:model.defer="mobile_no" disabled />
                <x-form.input name="father_name" label="Father's Name" wire:model.defer="father_name" disabled />
                <x-form.input name="ifscode" label="IFSC Code" wire:model.defer="ifscode" disabled />
                <x-form.input name="bankname" label="Bank Name" wire:model.defer="bankname" disabled />
                <x-form.input name="bankbranchname" label="Branch Name" wire:model.defer="bankbranchname" disabled />
                <x-form.input name="new_bank_account" label="Existing Bank Account Number"
                    wire:model.defer="new_bank_account" disabled />
            </div>
        @endif

        @if ($bank_action === '3')
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="application_id" label="Application Id" wire:model.defer="application_id" disabled />
                <x-form.input name="beneficiary_name" label="Beneficiary Name" wire:model.defer="beneficiary_name" disabled />
                <x-form.input name="mobile_no" label="Mobile No" wire:model.defer="mobile_no" disabled />
                <x-form.input name="father_name" label="Father's Name" wire:model.defer="father_name" disabled />

                <x-form.input name="ifscode" label="IFSC Code" wire:model.lazy="ifscode"
                    x-on:input="if ($el.value.length > 11) $el.value = $el.value.slice(0, 11)" />

                <x-form.input name="bankname" label="Bank Name" wire:model.defer="bankname" disabled />
                <x-form.input name="bankbranchname" label="Branch Name" wire:model.defer="bankbranchname" disabled />

                <x-form.input name="new_bank_account" label="New Bank Account Number"
                    wire:model.defer="new_bank_account"/>
            </div>
        @endif

        {{-- Name Matching Info --}}
        <div class="mt-6 grid gap-4 md:grid-cols-3 pl-4 pr-4 text-center">
            <div><label class="block text-gray-700 font-medium">Name As In Portal :
                    {{ $name_as_in_portal ?? 'N/A' }}</label></div>
            <div><label class="block text-gray-700 font-medium">Name Response For Bank :
                    {{ $name_response_for_bank ?? 'N/A' }}</label></div>
            <div><label class="block text-gray-700 font-medium">Name Matching Score :
                    {{ $name_matching_score ?? 'N/A' }}</label></div>
        </div>

        {{-- Radio buttons --}}
        <div class="flex gap-6 mt-6 pl-4 pr-4">
            <label class="flex items-center space-x-2">
                <input type="radio" class="form-radio text-blue-600"
                    wire:model.live="bank_action" value="1"
                    @if ($dupAction === '2') disabled @endif />
                <span>KEEP SAME</span>
            </label>

            <label class="flex items-center space-x-2">
                <input type="radio" class="form-radio text-blue-600"
                    wire:model.live="bank_action" value="3"
                    @if ($dupAction === '1' || $dupAction === '2') disabled @endif />
                <span>CHANGE</span>
            </label>
        </div>

        {{-- Submit button --}}
        <div class="mt-6 pl-4 pr-4">
            <x-button.primary type="button" wire:click="getdata">Submit</x-button.primary>
        </div>

    </div>
</div>

