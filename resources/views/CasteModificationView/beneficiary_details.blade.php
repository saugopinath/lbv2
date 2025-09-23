<x-layouts.app>
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-4">
        <div class="flex justify-between items-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">
                {{ $header }}
            </h1>
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
                <x-apllicant-modal.personal-detail-view :id=$application_id :reportType="3" />
            </x-accordion-section>
            <x-accordion-section title="Personal Details" sectionId="personal-details" color="pink-500">
                <x-apllicant-modal.personal-detail-view :id=$application_id :reportType="3" />
            </x-accordion-section>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-6">

        
    </div>

</x-layouts.app>