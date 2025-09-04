<div class="p-6 bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">Update Incomplete</h1>

    <form wire:submit.prevent="submit">
        @foreach ($page as $item)
            <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
                <h2 class="font-semibold text-lg text-blue-700 mb-2">
                    {{ $item->incompletType->name ?? 'Unknown Type' }}
                </h2>

                {{-- NO AADHAR NUMBER --}}
                @if ($item->incompletType->name === 'NO AADHAR NUMBER')
                    <x-form.input id="no_aadhar_{{ $item->id }}" name="no_aadhar[{{ $item->id }}]"
                        label="Aadhaar Number" required wire:model="formData.aadhar.{{ $item->id }}"
                        placeholder="Enter New Aadhaar Number"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)" />

                    <label
                        class="mt-2 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded cursor-pointer">
                        Upload Aadhaar
                        <input type="file" wire:model="formData.aadhar_doc.{{ $item->id }}" class="hidden">
                    </label>
                @endif

                {{-- NO MOBILE NUMBER --}}
                @if ($item->incompletType->name === 'NO MOBILE NUMBER')
                    <x-form.input id="no_mobile_{{ $item->id }}" name="no_mobile[{{ $item->id }}]"
                        label="Mobile Number" required wire:model="formData.mobile.{{ $item->id }}"
                        placeholder="Enter Mobile Number"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10)" />
                @endif

                {{-- NAME VALIDATION FAILED IN BANK --}}
                @if ($item->incompletType->name === 'NAME VALIDATION FAILED IN BANK')
                    <p class="text-sm text-gray-600">Old Name: {{ $item->old_value ?? 'N/A' }}</p>
                    <x-form.input id="bank_name_{{ $item->id }}" name="bank_name[{{ $item->id }}]"
                        label="Correct Name" required wire:model="formData.bank_name.{{ $item->id }}"
                        placeholder="Enter Correct Name"
                        x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
                @endif

                {{-- ACCOUNT NUMBER VALIDATION FAILED IN BANK --}}
                @if ($item->incompletType->name === 'ACCOUNT NUMBER VALIDATION FAILED IN BANK')
                    <p class="text-sm text-gray-600">Old Account: {{ $item->old_value ?? 'N/A' }}</p>
                    <x-form.input id="bank_account_{{ $item->id }}" name="bank_account[{{ $item->id }}]"
                        label="Bank Account Number" required wire:model="formData.bank_account.{{ $item->id }}"
                        placeholder="Enter New Account Number"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,16)" />

                    <label
                        class="mt-2 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded cursor-pointer">
                        Upload Bank Document
                        <input type="file" wire:model="formData.bank_doc.{{ $item->id }}" class="hidden">
                    </label>
                @endif

                {{-- DUPLICATE AADHAR NUMBER --}}
                @if ($item->incompletType->name === 'DUPLICATE AADHAR NUMBER')
                    <p class="text-sm text-gray-600">Old Aadhaar: {{ $item->old_value ?? 'N/A' }}</p>
                    <x-form.input id="dup_aadhar_{{ $item->id }}" name="dup_aadhar[{{ $item->id }}]"
                        label="New Aadhaar Number" required wire:model="formData.new_aadhar.{{ $item->id }}"
                        placeholder="Enter Correct Aadhaar"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)" />
                @endif

                {{-- DUPLICATE MOBILE NUMBER --}}
                @if ($item->incompletType->name === 'DUPLICATE MOBILE NUMBER')
                    <p class="text-sm text-gray-600">Old Mobile: {{ $item->old_value ?? 'N/A' }}</p>
                    <x-form.input id="dup_mobile_{{ $item->id }}" name="dup_mobile[{{ $item->id }}]"
                        label="New Mobile Number" required wire:model="formData.new_mobile.{{ $item->id }}"
                        placeholder="Enter New Mobile"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10)" />
                @endif

                {{-- DUPLICATE BANK ACCOUNT NUMBER --}}
                @if ($item->incompletType->name === 'DUPLICATE BANK ACCOUNT NUMBER')
                    <p class="text-sm text-gray-600">Old Account: {{ $item->old_value ?? 'N/A' }}</p>
                    <x-form.input id="dup_bank_account_{{ $item->id }}"
                        name="dup_bank_account[{{ $item->id }}]" label="New Bank Account Number" required
                        wire:model="formData.new_bank_account.{{ $item->id }}" placeholder="Enter New Bank Account"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,16)" />
                @endif

                {{-- MINOR MISMATCH (40% - 89%) --}}
                @if ($item->incompletType->name === 'MINOR MISMATCH(40% - 89%)')
                    <x-form.textarea id="mismatch_low_{{ $item->id }}" name="mismatch_low[{{ $item->id }}]"
                        label="Mismatch Details (40%-89%)" placeholder="Enter Corrected Details" required
                        wire:model="formData.mismatch_low.{{ $item->id }}" />
                @endif

                {{-- MINOR MISMATCH (90% - 100%) --}}
                @if ($item->incompletType->name === 'MINOR MISMATCH(90% - 100%)')
                    <x-form.textarea id="mismatch_high_{{ $item->id }}" name="mismatch_high[{{ $item->id }}]"
                        label="Mismatch Details (90%-100%)" placeholder="Enter Corrected Details" required
                        wire:model="formData.mismatch_high.{{ $item->id }}" />
                @endif

                {{-- PDS MISMATCH --}}
                @if ($item->incompletType->name === 'PDS MISMATCH')
                    <p class="text-sm text-gray-600">Old PDS: {{ $item->old_value ?? 'N/A' }}</p>
                    <x-form.input id="pds_{{ $item->id }}" name="pds[{{ $item->id }}]" label="PDS Number"
                        required wire:model="formData.pds.{{ $item->id }}" placeholder="Enter Correct PDS"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />
                @endif
            </div>
        @endforeach

        <div class="flex justify-end mt-4">
            <x-button.primary type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                {{ $page->count() > 1 ? 'Submit All Updates' : 'Submit' }}
            </x-button.primary>

        </div>
    </form>
</div>
