<x-layouts.app>
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-md">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <!-- Icon Section -->
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-red-100 to-red-50 dark:from-red-900/30 dark:to-red-900/10 border border-red-200 dark:border-red-800/30">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                Reactivate Death Incident
                            </h1>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed max-w-3xl">
                            These beneficiaries were deactivated based on death incidents received from Janma Mrityu Portal.
                            Select a beneficiary below to review and reactivate their status.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-md">
            <!-- Search Header -->
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                <div class="flex items-center justify-between mt-3">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Search Criteria</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Content -->
            <div class="p-6">
                <div class="space-y-4">
                    <livewire:filter-lgd-master :button_show="$button_show" />
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300">
        <!-- Table Header -->
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3 mt-3">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Search Results</h2>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <div class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-lg">
                        <span class="hidden sm:inline">Click on any row to </span>List of deactivated beneficiaries
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <div class="p-1">
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden m-5 mt-4">
                <livewire:jnmp-details-data-table />
                <livewire:reactivate-modal />
            </div>
        </div>
    </div>

    <!-- Success Toast (Example) -->
    <div class="fixed bottom-4 right-4 z-50 hidden">
        <div class="bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-up">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Beneficiary reactivated successfully!</span>
        </div>
    </div>


</x-layouts.app>
