<x-layouts.app>
    <div class="flex-1 p-2 overflow-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">
                Applicant Address
            </h2>
            <livewire:filter-lgd-master :login_type="$login_type" />
            <div class="mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl rounded-2xl ">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        {{ ucfirst($reportType) }} Beneficiary Report
                    </h2>
                    <div class="overflow-x-auto">
                        <livewire:beneficiary-details-table :login_type="$login_type" :reportType="$reportType" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
