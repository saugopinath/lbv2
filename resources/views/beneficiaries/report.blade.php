<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">
            Applicant Address
        </h2>
        <livewire:filter-lgd-master :login_type="$login_type" />
    </div>
    <div class="bg-white shadow-xl rounded-2xl ">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 p-4">
             Beneficiary Report
        </h2>
        <div>
            <livewire:beneficiary-details-table :login_type="$login_type" :reportType="$reportType" />
        </div>
    </div>
</x-layouts.app>
