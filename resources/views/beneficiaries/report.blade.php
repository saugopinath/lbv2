<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4 space-y-2 flex items-center justify-between">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white mt-2 pl-4">
                Applicant Address
            </h1>
            <x-form.back-button url="/beneficiaries_selection" />
        </div>
        <livewire:filterlgdmasternew />



    </div>
    <div class="bg-white shadow-xl rounded-2xl ">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 p-4">
            Beneficiary Report
        </h2>
        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
            <livewire:beneficiary-table :reportType="$reportType" :schemeId="$scheme" />
        </div>
    </div>
</x-layouts.app>