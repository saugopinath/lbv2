<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">
            Applicant Address
        </h2>
        <livewire:filter-lgd-master :button_show="1" />
    </div>
    <div class="bg-white shadow-xl rounded-2xl ">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 p-4">
             Beneficiary Report
        </h2>
        <div>
            <livewire:beneficiary-table :reportType="$reportType" />
        </div>
    </div>
</x-layouts.app>
