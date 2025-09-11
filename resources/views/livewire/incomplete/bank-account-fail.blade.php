
<div class="mt-4">
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">

        <div x-data="{ action: @entangle('bank_action') }">

            <div x-show="action === '' || action === '1' || action === '2'" class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="ifscode" label="IFSC Code" value="{{ $ifscode }}" disabled />
                <x-form.input name="bankname" label="Bank Name" value="{{ $bankname }}" disabled />
                <x-form.input name="bankbranchname" label="Branch Name" value="{{ $bankbranchname }}" disabled />
                <x-form.input name="new_bank_account" label="Existing Bank Account Number"
                    value="{{ $new_bank_account }}" disabled />


            </div>


            <div x-show="action === '2'" class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="ifscode" label="IFSC Code" wire:model.lazy="ifscode" />
                <x-form.input name="bankname" label="Bank Name" value="{{ $bankname }}" disabled />
                <x-form.input name="bankbranchname" label="Branch Name" value="{{ $bankbranchname }}" disabled />
                <x-form.input name="new_bank_account" label="New Bank Account Number" wire:model.defer="new_bank_account" />
                   <livewire:enclosure-list :application_id="$old->application_id" :doc_type_id_array_list="[112]" enclosureSource="5" />
            </div>


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
{{--  <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
    <h2 class="font-semibold text-lg text-blue-700 mb-2">{{ $old->incompletType->name }}</h2>
    <div x-data="{ action: @entangle('formData.bank_action.' . $old->id) }">
        <div x-show="action === '' || action === '1' || action === '2'" class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
            <x-form.input name="ifscode" label="IFSC Code" value="{{ $ifscode }}" disabled />
            <x-form.input name="bankname" label="Bank Name" value="{{ $bankname }}" disabled />
            <x-form.input name="bankbranchname" label="Branch Name" value="{{ $bankbranchname }}" disabled />
            <x-form.input name="new_bank_account" label="Existing Bank Account Number" value="{{ $new_bank_account }}" disabled />
        </div>
        <div x-show="action === '3'" class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
            <x-form.input name="ifscode" label="IFSC Code" wire:model.lazy="formData.ifscode.{{ $old->id }}"
                x-on:input="if ($el.value.length > 11) $el.value = $el.value.slice(0, 11)" />
            <x-form.input name="bankname" label="Bank Name" value="{{ $bankname }}" disabled />
            <x-form.input name="bankbranchname" label="Branch Name" value="{{ $bankbranchname }}" disabled />
            <x-form.input name="new_bank_account" label="New Bank Account Number" wire:model.defer="formData.new_bank_account.{{ $old->id }}"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,18)" />
            <livewire:enclosure-list :application_id="$old->application_id" :doc_type_id_array_list="[112]" enclosureSource="5" />
        </div>
        <div class="flex gap-6 mt-6 pl-4 pr-4">
            <label class="flex items-center space-x-2">
                <input type="radio" class="form-radio text-blue-600" wire:model.live="formData.bank_action.{{ $old->id }}" value="1"
                    @if($dupAction === '2') disabled @endif />
                <span>KEEP SAME</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="radio" class="form-radio text-blue-600" wire:model.live="formData.bank_action.{{ $old->id }}" value="3"
                    @if($dupAction === '1' || $dupAction === '2') disabled @endif />
                <span>CHANGE</span>
            </label>
        </div>
        @error("formData.ifscode.{$old->id}") <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        @error("formData.new_bank_account.{$old->id}") <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
    </div>
</div>  --}}
