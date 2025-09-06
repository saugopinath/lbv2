<div class="p-6 bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">Update Incomplete</h1>

    @if ($applicantInfo)
        <div class="mb-6 p-4 border rounded-lg bg-gray-100 shadow-sm">
            <div class="flex flex-wrap gap-6 text-sm">
                <p><strong>Application ID:</strong> {{ $id }}</p>

                <p><strong>Name:</strong>
                    {{ optional($applicantInfo->beneficiaryPersonal)->full_name ?? 'N/A' }}
                </p>

                <p><strong>Father Name:</strong>
                    {{ optional(optional($applicantInfo->beneficiaryPersonal)->father)->full_name ?? 'N/A' }}
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
                @endif

                {{-- DUPLICATE AADHAR NUMBER --}}
                @if ($item->incompletType->name === 'DUPLICATE AADHAR NUMBER')
                    <x-incomplete.dup-aadhar :item="$item" />
                @endif

                {{-- DUPLICATE BANK ACCOUNT NUMBER --}}
                @if ($item->incompletType->name === 'DUPLICATE BANK ACCOUNT NUMBER')
                    <x-incomplete.dup-bank :item="$item" />
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

        <div class="flex justify-end mt-4">
            <x-button.primary type="submit"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded cursor-pointer">
                {{ $page->count() > 1 ? 'Submit All Updates' : 'Submit' }}
            </x-button.primary>
        </div>
    </form>
</div>
