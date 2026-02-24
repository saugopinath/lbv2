<x-layouts.app>
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <h1 class="text-2xl font-bold text-indigo-800 dark:text-white px-2 py-2">
                {{ $header }}
            </h1>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold 
             bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 
             border border-blue-200 dark:border-blue-800 shadow-sm
             flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                        clip-rule="evenodd" />
                </svg>
                {{ $schemeName }}
            </span>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold 
             bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-400 
             border border-gray-200 dark:border-gray-700 shadow-sm
             flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd" />
                </svg>
                Application ID: {{ $application_id }}
            </span>

        </div>
        <x-form.back-button :url="route('caste-modification-list', ['retain_filters' => 1])" />
    </div>
    <!-- Accordion Section -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-4">
        <div class="mb-2">
            <livewire:application-details.tab-wise-application-view :id="$application_id" :schemeId="$scheme_id"
                :allowedTabCodes="[101]" />
        </div>


        <div class="flex justify-between items-center border-b border-blue-200 dark:border-blue-800 p-4">
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
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
                    <li>
                        <div class="mt-2">
                            <span class="font-medium text-gray-600 dark:text-gray-300"> previous Uploaded Documents:</span>
                        </div>
                        <div class="mt-2">
                            <livewire:enclosure-list :application_id="$application_id" :doc_type_id_array_list="[104]" :is_page="1" :scheme_id="$scheme_id" />
                        </div>
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
                    <li>
                        <div class="mt-2">
                            <span class="font-medium text-gray-600 dark:text-gray-300">New Uploaded Documents:</span>
                        </div>
                        <div class="mt-2">
                            <livewire:enclosure-list :application_id="$application_id" :doc_type_id_array_list="[104]" enclosureSource="5" :is_page="1" :scheme_id="$scheme_id" />
                        </div>
                    </li>
            </div>
            </ul>
        </div>
        <div class="rounded-xl p-4 mt-4">
            {{-- @if(\App\Helpers\WorkFlowPermissionHelper::canTakeActionForCaste())  --}}
            <livewire:caste-modification.caste-modification-action :applicationId="$application_id" :scheme_id="$scheme_id" />
            {{-- @endif  --}}
        </div>

    </div>
</x-layouts.app>