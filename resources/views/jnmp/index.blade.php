<x-layouts.app>
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Reactivate Death Incident
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    These beneficiaries were deactivated based on death incidents received from Janma Mrityu Portal.
                </p>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center mb-4">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Search Criteria</h2>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <livewire:filter-lgd-master :button_show="$button_show" />
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Search Results
                </h2>

            </div>

            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <livewire:jnmp-details-data-table />
                  <livewire:reactivate-modal />
            </div>
        </div>
    </div>


</x-layouts.app>
