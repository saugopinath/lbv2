
<div class="mt-4">
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">

        <div x-data="{ action: @entangle('bank_action') }">
            {{-- KEEP SAME --}}
            <div x-show="action === '' || action === '1' || action === '2'" class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="ifscode" label="IFSC Code" value="{{ $ifscode }}" disabled />
                <x-form.input name="bankname" label="Bank Name" value="{{ $bankname }}" disabled />
                <x-form.input name="bankbranchname" label="Branch Name" value="{{ $bankbranchname }}" disabled />
                <x-form.input name="new_bank_account" label="Existing Bank Account Number"
                    value="{{ $new_bank_account }}" disabled />


            </div>

            {{-- CHANGE --}}
            <div x-show="action === '2'" class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="ifscode" label="IFSC Code" wire:model.lazy="ifscode" />
                <x-form.input name="bankname" label="Bank Name" value="{{ $bankname }}" disabled />
                <x-form.input name="bankbranchname" label="Branch Name" value="{{ $bankbranchname }}" disabled />
                <x-form.input name="new_bank_account" label="New Bank Account Number" wire:model.defer="new_bank_account" />
                   <livewire:enclosure-list :application_id="$old->application_id" :doc_type_id_array_list="[112]" enclosureSource="5" />
            </div>

            {{-- Radio --}}
            <div class="flex gap-6 mt-6 pl-4 pr-4">
                <label class="flex items-center space-x-2">
                    <input type="radio" class="form-radio text-blue-600"
                        wire:model="bank_action" value="1"
                        @if($dupAction === '2') disabled @endif />
                    <span>KEEP SAME</span>
                </label>

                <label class="flex items-center space-x-2">
                    <input type="radio" class="form-radio text-blue-600"
                        wire:model="bank_action" value="3"
                        @if($dupAction === '1' || $dupAction === '2') disabled @endif />
                    <span>CHANGE</span>
                </label>
            </div>
        </div>
    </div>
</div>
