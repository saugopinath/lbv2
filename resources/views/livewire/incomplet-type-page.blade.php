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
                <p><strong>Name:</strong> {{ optional($applicantInfo->beneficiaryPersonal)->full_name ?? 'N/A' }}</p>
                <p><strong>Father Name:</strong>
                    {{ $applicantInfo->beneficiaryPersonal?->father?->first()?->full_name ?? 'N/A' }}</p>
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
        @php
            $aadhaarIssues = [];
            $bankIssues = [];
            $mobileIssues = [];
        @endphp

        @foreach ($page as $item)
            {{-- Categorize issues --}}
            @if (in_array($item->incompletType->name, ['PDS MISMATCH', 'NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER']))
                @php $aadhaarIssues[] = $item; @endphp
            @elseif (in_array($item->incompletType->name, [
                    'DUPLICATE BANK ACCOUNT NUMBER',
                    'NAME VALIDATION  FAILED IN BANK',
                    'ACCOUNT NUMBER VALIDATION  FAILED IN BANK',
                ]))
                @php $bankIssues[] = $item; @endphp
            @elseif (in_array($item->incompletType->name, ['NO MOBILE NUMBER', 'DUPLICATE MOBILE NUMBER']))
                @php $mobileIssues[] = $item; @endphp
            @endif

            {{-- Display mismatch blocks --}}
            @if (in_array($item->incompletType->name, ['MINOR MISMATCH(40% - 89%)', 'MINOR MISMATCH(90% - 100%)']))
                <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
                    <h2 class="font-semibold text-lg text-blue-700 mb-2">{{ $item->incompletType->name }}</h2>

                    @if ($item->incompletType->name === 'MINOR MISMATCH(40% - 89%)')
                        <x-incomplete.mismatch-low :item="$item" />
                    @elseif ($item->incompletType->name === 'MINOR MISMATCH(90% - 100%)')
                        <x-incomplete.mismatch-high :item="$item" />
                    @endif
                </div>
            @endif
        @endforeach

        {{-- Issues Components --}}
        @if (!empty($mobileIssues))
            <x-incomplete.mobile-issues :mobile-issues="$mobileIssues" />
        @endif

        @if (!empty($aadhaarIssues))
            <x-incomplete.aadhar-modification :aadhaar-issues="$aadhaarIssues" />
        @endif

        @if (!empty($bankIssues))
            <x-incomplete.bank-issues :bank-issues="$bankIssues" />
        @endif

        {{-- Submit Buttons --}}
        <div class="flex justify-end mt-4 space-x-2">
            @if ($stage === 'verifier')
                <x-button.primary type="submit">
                    {{ $page->count() > 1 ? 'Submit All Updates' : 'Submit' }}
                </x-button.primary>
            @elseif ($stage === 'approver')
                <div class="flex justify-center w-full space-x-4">
                    <x-button.primary type="submit" wire:click="approve">Approve</x-button.primary>
                    <x-button.danger x-data x-on:click="$dispatch('open-revert-modal')">Revert</x-button.danger>

                    {{-- Revert Modal --}}
                    <div x-data="{ open: false }" x-on:open-revert-modal.window="open = true" x-show="open"
                        class="fixed inset-0 flex items-center justify-center text-gray-800 bg-opacity-50 z-50"
                        style="display:none">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-96 border-gray-800">
                            <h2 class="text-lg font-semibold mb-4">Revert Application</h2>

                            {{-- Dropdown --}}
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
                                <x-button.primary x-on:click="open = false">Cancel</x-button.primary>
                                <x-button.primary wire:click="revert"
                                    x-on:click="open = false">Submit</x-button.primary>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($stage === 'revert')
                <x-button.primary wire:click="revertVerify">Revert Verify</x-button.primary>
            @endif
        </div>
    </form>
</div>
