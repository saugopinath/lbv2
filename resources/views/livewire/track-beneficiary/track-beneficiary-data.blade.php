<div class="px-4 py-4">

    <!-- Search -->
    <div class="lg:col-span-1 space-y-6 mb-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search Beneficiary
                </h2>
            </div>
            <div class="p-4">
                @livewire('beneficiary-search', [
                'isShownScheme' => true,
                'excludeFields' => ['bank_account_number'],
                'isReset' => true
                ])
            </div>
        </div>
    </div>
    <div class="{{ $isSingle ? 'flex justify-center' : 'grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4' }} gap-6">
        @forelse($beneficiaries as $b)
        @if($isSingle)
        <div class="w-full max-w-sm md:max-w-md lg:max-w-lg">
            @endif

            @include('frontend.track-ben.beneficiary-card', [
            'status' => $b->status,
            'statusColor' => $b->statusColor,
            'applicationId' => $b->application_id,
            'beneficiaryId' => $b->beneficiary_id,
            'name' => $b->beneficiary_name,
            'relation' => $b->relation,
            'relationName' => $b->relationName,
            'schemeName' => $b->scheme->name ?? 'N/A',
            'location' => $b->location,
            'mobile' => $b->maskedMobile,
            'paymentUrl' => $b->paymentUrl,
            'beneficiaryDetailsUrl' => $b->BenDetailsUrl,
            'ben_profile_pic' => $b->ben_profile_pic,
            ])

            @if($isSingle)
        </div>
        @endif

        @empty
        <div class="col-span-full text-center py-10">
            No Beneficiary Found
        </div>
        @endforelse

    </div>
    <div
        wire:loading.flex
        class="justify-center items-center py-6">
        Loading...
    </div>

    <!-- Pagination -->
    <div class="mt-10">

        {{ $beneficiaries->links() }}

    </div>

</div>