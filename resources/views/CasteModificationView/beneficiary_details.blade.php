<x-layouts.app>
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-4">
        <div class="flex items-center space-x-3">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">
                {{ $header }}
            </h1>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 shadow-sm">
                Application Id {{ $application_id }}
            </span>

        </div>
    </div>

    <!-- Accordion Section -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-6">
        <div
            x-data="{
                openSection: 'personal-details',
                toggleSection(section) {
                    this.openSection = this.openSection === section ? '' : section;
                }
            }"
            class="space-y-2">
            <x-accordion-section title="Personal Details" sectionId="personal-details" color="pink-500">
                <x-apllicant-modal.personal-details :id=$application_id :reportType="$reportType" mode="page" />
            </x-accordion-section>
        </div>
    </div>

    <!-- Modification Details -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-4">

        <div class="flex justify-between items-center mb-4 p-2">
            <h2 class="text-lg font-semibold text-indigo-700 dark:text-indigo-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1 ml-1 text-indigo-500"
                    fill="none" viewBox="-2 -2 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m2 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Modification Details
            </h2>
        </div>
        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Old Data Card -->
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-xl p-5 shadow-sm">
                <h3 class="text-red-700 dark:text-red-400 font-semibold text-lg mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Old Details
                </h3>
                <ul class="space-y-3 text-sm">
                    <li>
                        <span class="font-medium text-gray-600 dark:text-gray-300">Caste:</span>
                        <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $oldCasteName }}</span>
                    </li>
                    <li>
                        <span class="font-medium text-gray-600 dark:text-gray-300">Caste Certificate No:</span>
                        <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $oldCasteNumber ?? 'N/A' }}</span>
                    </li>
                </ul>
            </div>

            <!-- New Data Card -->
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-xl p-5 shadow-sm">
                <h3 class="text-green-700 dark:text-green-400 font-semibold text-lg mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7" />
                    </svg>
                    New Details
                </h3>
                <ul class="space-y-3 text-sm">
                    <li>
                        <span class="font-medium text-gray-600 dark:text-gray-300">Caste:</span>
                        <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $newCasteName }}</span>
                    </li>
                    <li>
                        <span class="font-medium text-gray-600 dark:text-gray-300">Caste Certificate No:</span>
                        <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $newCasteNumber ?? 'N/A' }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="rounded-xl p-4 mt-4">
            @if(\App\Helpers\WorkFlowPermissionHelper::canTakeActionForCaste())
            <livewire:caste-modification.caste-modification-action :applicationId="$application_id" />
            @endif
        </div>

    </div>
</x-layouts.app>
