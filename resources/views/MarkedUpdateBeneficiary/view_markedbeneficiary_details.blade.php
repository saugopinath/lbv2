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
    
</x-layouts.app>