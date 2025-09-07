<div class="p-6 bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">
        @if ($stage === 'verifier')
            Update Incomplete
        @elseif ($stage === 'approver')
            Approver Incomplete
        @elseif ($stage === 'revert')
            Revert Incomplete
        @else
            Update Incomplete
        @endif
    </h1>

    @if ($applicantInfo)
        <div class="mb-6 p-4 border rounded-lg bg-gray-100 shadow-sm">
            <div class="flex flex-wrap gap-6 text-sm">
                <p><strong>Application ID:</strong> {{ $id }}</p>

                <p><strong>Name:</strong>
                    {{ optional($applicantInfo->beneficiaryPersonal)->full_name ?? 'N/A' }}
                </p>

                <p><strong>Father Name:</strong>
                    {{ $applicantInfo->beneficiaryPersonal?->father?->first()?->full_name ?? 'N/A' }}
                </p>

                <p><strong>Address:</strong>
                    @if ($applicantInfo->panchayat)
                        {{ $applicantInfo->panchayat->name }}
                    @elseif ($applicantInfo->ward)
                        {{ $applicantInfo->ward->name }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    @endif


    <form wire:submit.prevent="submit">
        @foreach ($page as $item)
            <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
                <h2 class="font-semibold text-lg text-blue-700 mb-2">
                    {{ $item->incompletType->name ?? 'Unknown Type' }}
                </h2>

                @if ($item->incompletType->name === 'NO AADHAR NUMBER')
                    <x-incomplete.no-aadhar :item="$item" />
                    <livewire:enclosure-list :application_id="$id" :doc_type_id_array_list="[108]" enclosureSource="temp" />
                @endif

                {{-- DUPLICATE AADHAR NUMBER --}}
                @if ($item->incompletType->name === 'DUPLICATE AADHAR NUMBER')
                    <x-incomplete.dup-aadhar :item="$item" />
                    <livewire:enclosure-list :application_id="$id" :doc_type_id_array_list="[108]" enclosureSource="temp" />
                @endif

                {{-- DUPLICATE BANK ACCOUNT NUMBER --}}
                @if ($item->incompletType->name === 'DUPLICATE BANK ACCOUNT NUMBER')
                    <x-incomplete.dup-bank :item="$item" />
                      <livewire:enclosure-list :application_id="$id" :doc_type_id_array_list="[112]" enclosureSource="temp" />
                @endif

                {{-- NO MOBILE NUMBER --}}
                @if ($item->incompletType->name === 'NO MOBILE NUMBER')
                    <x-incomplete.no-mobile :item="$item" />
                @endif

                {{-- DUPLICATE MOBILE NUMBER --}}
                @if ($item->incompletType->name === 'DUPLICATE MOBILE NUMBER')
                    <x-incomplete.dup-mobile :item="$item" />
                @endif

                {{-- BANK NAME MISMATCH --}}
                @if ($item->incompletType->name === 'NAME VALIDATION  FAILED IN BANK')
                    <x-incomplete.bank-name-fail :item="$item" />
                @endif

                {{-- BANK ACCOUNT FAIL --}}
                @if ($item->incompletType->name === 'ACCOUNT NUMBER VALIDATION  FAILED IN BANK')
                    <x-incomplete.bank-account-fail :item="$item" />
                      <livewire:enclosure-list :application_id="$id" :doc_type_id_array_list="[112]" enclosureSource="temp" />
                @endif

                {{-- MISMATCH LOW --}}
                @if ($item->incompletType->name === 'MINOR MISMATCH(40% - 89%)')
                    <x-incomplete.mismatch-low :item="$item" />
                @endif

                {{-- MISMATCH HIGH --}}
                @if ($item->incompletType->name === 'MINOR MISMATCH(90% - 100%)')
                    <x-incomplete.mismatch-high :item="$item" />
                @endif

                {{-- PDS MISMATCH --}}
                @if ($item->incompletType->name === 'PDS MISMATCH')
                    <x-incomplete.pds-mismatch :item="$item" />
                @endif
            </div>
        @endforeach

        <div class="flex justify-end mt-4 space-x-2">
            @if ($stage === 'verifier')
                {{-- Verify Submit Button --}}
                <x-button.primary type="submit"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded cursor-pointer">
                    {{ $page->count() > 1 ? 'Submit All Updates' : 'Submit' }}
                </x-button.primary>
            @elseif ($stage === 'approver')
                <div class="flex justify-center w-full space-x-4">
                    {{-- Approve Button --}}
                    <x-button.primary type="submit" wire:click="approve"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded cursor-pointer">
                        Approve
                    </x-button.primary>

                    <x-button.danger type="button" x-data x-on:click="$dispatch('open-revert-modal')"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded cursor-pointer">
                        Revert
                    </x-button.danger>

                    {{-- Revert Modal --}}
                    <div x-data="{ open: false }" x-on:open-revert-modal.window="open = true" x-show="open"
                        class="fixed inset-0 flex items-center justify-center text-gray-800 bg-opacity-50 z-50"
                        style="display:none">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-96 border-gray-800">
                            <h2 class="text-lg font-semibold mb-4">Revert Application</h2>

                            {{-- Dropdown from Codemaster --}}
                            <div class="mb-4">
                                <x-form.select name="revert_reason_cause_id" id="revert_reason_cause_id"
                                    label="Revert Reason" required wire:model.live="revert_reason_cause_id">

                                    <option value="">-- Select Reason --</option>
                                    @foreach ($revertReasons as $reason)
                                        <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                    @endforeach
                                </x-form.select>


                                @error('revert_reason_cause_id')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Remarks --}}
                            <div class="mb-4">
                                <x-form.textarea id="revert_reason_remarks" name="revert_reason_remarks" label="Remarks"
                                    required wire:model="revert_reason_remarks" />

                                @error('revert_reason_remarks')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex justify-end space-x-2">
                                <x-button.primary type="button"
                                    class="px-4 py-2 bg-blue-500 text-white rounded cursor-pointer"
                                    x-on:click="open = false">
                                    Cancel
                                </x-button.primary>
                                <x-button.primary type="button"
                                    class="px-4 py-2 bg-red-600 text-white rounded cursor-pointer" wire:click="revert"
                                    x-on:click="open = false">
                                    Submit
                                </x-button.primary>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($stage === 'revert')
                {{-- Revert Verify Button --}}
                <x-button.primary type="button"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded cursor-pointer"
                    wire:click="revertVerify">
                    Revert Verify
                </x-button.primary>
            @endif
        </div>
    </form>
</div>
