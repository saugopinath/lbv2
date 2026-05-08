<div class="px-4 py-4">

    <!-- Search -->
    <div class="mb-6">

        <div class="relative flex gap-3">

            <div class="relative flex-1">

                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>

                <input
                    type="text"
                    wire:model.defer="search"
                    placeholder="Search by Application ID, Mobile Number or Name..."
                    class="w-full rounded-xl border-gray-300 ps-10 pe-4 py-3">

            </div>

            <button
                wire:click="searchBeneficiary"
                wire:loading.attr="disabled"
                class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition">

                <span wire:loading.remove wire:target="searchBeneficiary">
                    <i class="fa-solid fa-magnifying-glass mr-2"></i>
                    Search
                </span>

                <span wire:loading wire:target="searchBeneficiary">
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                    Searching...
                </span>

            </button>

        </div>

    </div>
    <!-- Gri -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($beneficiaries as $b)
        @php
        $paymentUrl = route(
        'beneficiary.payment.history',
        ['id' => $b->application_id]
        );
        $BenDetailsUrl = route(
        'beneficiary.details',
        ['id' => $b->application_id]
        );
        @endphp
        @include(
        'frontend.track-ben.beneficiary-card',
        [
        'status' => 'Approved',
        'applicationId' => $b->application_id,
        'beneficiaryId' => $b->beneficiary_id,
        'name' => $b->beneficiary_name,
        'relation' => 'Father',
        'relationName' => $b->ben_father_name,
        'schemeName' => $b->scheme->name ?? 'N/A',
        'location' => $b->contact->district->name ?? 'N/A',
        'mobile' => $b->other_details['mobile_no'] ?? 'N/A',
        'paymentUrl' => $paymentUrl,
        'beneficiaryDetailsUrl' => $BenDetailsUrl,
        'ben_profile_pic' => [],
        ]
        )

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