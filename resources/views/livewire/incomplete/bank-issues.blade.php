<div x-data="{ selected: '{{ $formData['bank_action'][$bankIssues[0]->id] ?? '1' }}' }" class="mt-4">
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
        <h2 class="font-semibold text-lg text-blue-700 mb-2">Bank Related Issues</h2>

        <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
            @foreach ($bankIssues as $issueItem)
                <li>{{ $issueItem->incompletType->name }}</li>
            @endforeach
        </ul>

        {{-- KEEP SAME --}}
        <div x-show="selected == 1" class="grid gap-6 mb-4 md:grid-cols-2 pl-4 pr-4">
            <div>
                <x-form.input name="ifscode" label="IFSC Code" wire:model.lazy="ifscode"
                    x-on:input="if ($el.value.length > 11) $el.value = $el.value.slice(0, 11)" disabled />
            </div>

            <div>
                <x-form.input name="bankname" label="Bank Name" value="{{ $bankname }}" disabled />
            </div>

            <div>
                <x-form.input name="bankbranchname" label="Branch Name" value="{{ $bankbranchname }}" disabled />
            </div>

            <div>
                <x-form.input name="new_bank_account" label="Existing Bank Account Number"
                    value="{{ $new_bank_account }}" disabled />
            </div>
            
            <livewire:enclosure-list :application_id="$bankIssues[0]->application_id" :is_page="1" :doc_type_id_array_list="[112]" :enclosureSource="5"
                wire:key="enclosure-keep-{{ $bankIssues[0]->application_id }}" />
        </div>

        {{-- CHANGE --}}
        <div x-show="selected == 2" class="grid gap-6 mb-4 md:grid-cols-2 pl-4 pr-4">
            <div>
                <x-form.input name="ifscode" label="IFSC Code" wire:model.lazy="ifscode"
                    x-on:input="if ($el.value.length > 11) $el.value = $el.value.slice(0, 11)" />
            </div>

            <div>
                <x-form.input name="bankname" label="Bank Name" value="{{ $bankname }}" disabled />
            </div>

            <div>
                <x-form.input name="bankbranchname" label="Branch Name" value="{{ $bankbranchname }}" disabled />
            </div>

            <div>
                <x-form.input name="new_bank_account" label="New Bank Account Number" value="{{ $new_bank_account }}" />
            </div>

            <livewire:enclosure-list :application_id="$bankIssues[0]->application_id" :doc_type_id_array_list="[112]" :enclosureSource="5"
                wire:key="enclosure-change-{{ $bankIssues[0]->application_id }}" />
        </div>



        {{-- Radio Buttons --}}
        <div class="flex gap-6 mt-4">
            <label class="flex items-center space-x-2">
                <input type="radio" class="form-radio text-blue-600"
                    wire:model="formData.bank_action.{{ $bankIssues[0]->id }}" value="1" x-model="selected" />
                <span>KEEP SAME</span>
            </label>

            <label class="flex items-center space-x-2">
                <input type="radio" class="form-radio text-blue-600"
                    wire:model="formData.bank_action.{{ $bankIssues[0]->id }}" value="2" x-model="selected" />
                <span>CHANGE</span>
            </label>
        </div>
    </div>
</div>
