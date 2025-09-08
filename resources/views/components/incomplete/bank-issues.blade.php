@if (!empty($bankIssues))
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
        <h2 class="font-semibold text-lg text-blue-700 mb-2">
            Bank Related Issues
        </h2>

        <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
            @foreach ($bankIssues as $issueItem)
                <li>{{ $issueItem->incompletType->name }}</li>
            @endforeach
        </ul>

        {{--  <p class="text-sm text-gray-600">
            Old Account: {{ $bankIssues[0]->old_value ?? 'N/A' }}
        </p>
        <p class="text-sm text-gray-600">
            IFSC: {{ optional($bankIssues[0]->beneficiaryCommonList->beneficiaryBank)->ifsc ?? 'N/A' }}
        </p>

        <x-form.input id="dup_bank_account_{{ $bankIssues[0]->id }}" name="dup_bank_account[{{ $bankIssues[0]->id }}]"
            label="New Bank Account Number" required wire:model="formData.new_bank_account.{{ $bankIssues[0]->id }}"
            placeholder="Enter New Bank Account" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,16)" />

        <livewire:enclosure-list :application_id="$bankIssues[0]->id" :doc_type_id_array_list="[112]" enclosureSource="5" />  --}}

        {{-- Action Selection --}}
        <div class="mt-4">
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600"
                        wire:model="formData.bank_action.{{ $bankIssues[0]->id }}.keep_same" value="1" />
                    <span>KEEP SAME</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600"
                        wire:model="formData.bank_action.{{ $bankIssues[0]->id }}.change" value="2" />
                    <span>CHANGE</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600"
                        wire:model="formData.bank_action.{{ $bankIssues[0]->id }}.reject" value="3" />
                    <span>REJECT</span>
                </label>

            </div>
            @error("formData.bank_action.{$bankIssues[0]->id}")
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>
@endif
