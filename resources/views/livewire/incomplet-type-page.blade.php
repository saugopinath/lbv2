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
                    <x-incomplete.no-aadhar :item="$item" :stage="$stage" :formData="$formData" />
                @endif

                {{-- DUPLICATE AADHAR NUMBER --}}
                @if ($item->incompletType->name === 'DUPLICATE AADHAR NUMBER')
                    <x-incomplete.dup-aadhar :item="$item" :stage="$stage" :formData="$formData" />
                @endif

                {{-- DUPLICATE BANK ACCOUNT NUMBER --}}
                @if ($item->incompletType->name === 'DUPLICATE BANK ACCOUNT NUMBER')
                    <x-incomplete.dup-bank :item="$item" :stage="$stage" :formData="$formData" />
                @endif

                {{-- NO MOBILE NUMBER --}}
                @if ($item->incompletType->name === 'NO MOBILE NUMBER')
                    <x-incomplete.no-mobile :item="$item" :stage="$stage" :formData="$formData" />
                @endif

                {{-- DUPLICATE MOBILE NUMBER --}}
                @if ($item->incompletType->name === 'DUPLICATE MOBILE NUMBER')
                    <x-incomplete.dup-mobile :item="$item" :stage="$stage" :formData="$formData" />
                @endif

                {{-- BANK NAME MISMATCH --}}
                @if ($item->incompletType->name === 'NAME VALIDATION  FAILED IN BANK')
                    <x-incomplete.bank-name-fail :item="$item" :stage="$stage" :formData="$formData" />
                @endif

                {{-- BANK ACCOUNT FAIL --}}
                @if ($item->incompletType->name === 'ACCOUNT NUMBER VALIDATION  FAILED IN BANK')
                    <x-incomplete.bank-account-fail :item="$item" :stage="$stage" :formData="$formData" />
                @endif

                {{-- MISMATCH LOW --}}
                @if ($item->incompletType->name === 'MINOR MISMATCH(40% - 89%)')
                    <x-incomplete.mismatch-low :item="$item" :stage="$stage" :formData="$formData" />
                @endif

                {{-- MISMATCH HIGH --}}
                @if ($item->incompletType->name === 'MINOR MISMATCH(90% - 100%)')
                    <x-incomplete.mismatch-high :item="$item" :stage="$stage" :formData="$formData" />
                @endif

                {{-- PDS MISMATCH --}}
                @if ($item->incompletType->name === 'PDS MISMATCH')
                    <x-incomplete.pds-mismatch :item="$item" :stage="$stage" :formData="$formData" />
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
                    <x-button.primary type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded cursor-pointer">
                        Approve
                    </x-button.primary>

                    {{-- Revert Button --}}
                    <x-button.danger type="button" wire:click="revertUpdate"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded cursor-pointer">
                        Revert
                    </x-button.danger>
                </div>
            @elseif ($stage === 'revert')
                {{-- Revert Verify Button --}}
                <x-button.primary type="submit"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded cursor-pointer">
                    Revert Verify
                </x-button.primary>
            @endif
        </div>
    </form>
</div>
