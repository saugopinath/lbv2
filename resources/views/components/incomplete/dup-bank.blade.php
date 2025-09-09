<div x-data="{ selected: '' }" class="mt-4">
    <div class="flex flex-wrap gap-6">
        <label class="flex items-center space-x-2">
            <input type="radio" class="form-radio h-4 w-4 text-blue-600" value="1" x-model="selected" />
            <span>KEEP SAME</span>
        </label>

        <label class="flex items-center space-x-2">
            <input type="radio" class="form-radio h-4 w-4 text-blue-600" value="2" x-model="selected" />
            <span>CHANGE</span>
        </label>
    </div>

    <div x-show="selected == 1" class="mt-3">
        <div class="grid gap-6 mb-4 md:grid-cols-2 pl-4 pr-4">
            <div>
                <x-form.input name="ifscode" label="IFS Code" required wire:model.lazy="ifscode"
                    x-on:input="if ($el.value.length > 11) $el.value = $el.value.slice(0, 11)" />
            </div>

            <div class="relative">
                <x-form.input name="bankname" label="Bank Name" value="{{ $bankname }}" disabled />
                <x-loading-spinner wire:target="ifscode" />
            </div>
        </div>

        <div class="grid gap-6 mb-4 md:grid-cols-2 pl-4 pr-4">
            {{--  <p class="text-sm text-gray-600">Old Account: {{ $item->old_value ?? 'N/A' }}</p>  --}}
            <div class="relative">
                <x-form.input name="bankbranchname" label="Branch Name" value="{{ $bankbranchname }}" disabled />
                <x-loading-spinner wire:target="ifscode" />
            </div>
            <div>
                <x-form.input id="dup_bank_account_{{ $item->id }}"
                    name="dup_bank_account[{{ $item->application_id }}]" label="New Bank Account Number" required
                    wire:model="formData.new_bank_account.{{ $item->application_id }}"
                    placeholder="Enter New Bank Account" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                    x-on:copy.prevent />
            </div>


            <livewire:enclosure-list :application_id="$item->application_id" :doc_type_id_array_list="[112]" enclosureSource="5" />
        </div>
    </div>

    <div x-show="selected == 2" class="mt-3">
        <div class="grid gap-6 mb-4 md:grid-cols-2 pl-4 pr-4">
            <div>
                <x-form.input name="ifscode" label="IFS Code" required wire:model.lazy="ifscode"
                    x-on:input="if ($el.value.length > 11) $el.value = $el.value.slice(0, 11)" />
            </div>

            <div class="relative">
                <x-form.input name="bankname" label="Bank Name" value="{{ $bankname }}" disabled />
                <x-loading-spinner wire:target="ifscode" />
            </div>
        </div>

        <div class="grid gap-6 mb-4 md:grid-cols-2 pl-4 pr-4">
            <p class="text-sm text-gray-600">Old Account: {{ $item->old_value ?? 'N/A' }}</p>
            <div class="relative">
                <x-form.input name="bankbranchname" label="Branch Name" value="{{ $bankbranchname }}" disabled />
                <x-loading-spinner wire:target="ifscode" />
            </div>
            <div>
                <x-form.input id="dup_bank_account_{{ $item->id }}"
                    name="dup_bank_account[{{ $item->application_id }}]" label="New Bank Account Number" required
                    wire:model="formData.new_bank_account.{{ $item->application_id }}"
                    placeholder="Enter New Bank Account" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                    x-on:copy.prevent />
            </div>


            <livewire:enclosure-list :application_id="$item->application_id" :doc_type_id_array_list="[112]" enclosureSource="5" />
        </div>
    </div>
</div>










{{--  @if (data_get($formData['bank_action'] ?? [], $item->application_id) == '2')
    <p class="text-sm text-gray-600">Old Account: {{ $item->old_value ?? 'N/A' }}</p>
    <p class="text-sm text-gray-600">
        IFSC: {{ optional($item->beneficiaryCommonList->beneficiaryBank)->ifsc ?? 'N/A' }}
    </p>
@endif  --}}
