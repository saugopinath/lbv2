
<div class="bg-white dark:bg-gray-800 shadow-md rounded p-8 space-y-4">
    <div class="bg-blue-200 dark:bg-gray-800 shadow-md rounded p-2 space-y-2 text-center border border-blue-300">
        <h2 class="text-lg font-semibold">Application Name: {{ $application->full_name ?? '-' }}</h2>
        <h2 class="text-lg font-semibold">{{ $label }}: {{ $value }}</h2>
    </div>

    <div x-data="{
        openSection: 'personal-details',
        toggleSection(section) {
            this.openSection = this.openSection === section ? null : section;
        }
    }" class="space-y-2">
        <x-accordion-section title="Personal Details" sectionId="personal-details" color="pink-500">
            <x-apllicant-modal.personal-details :id="$passId" :reportType="$reportType" />
        </x-accordion-section>

        <x-accordion-section title="Address Details" sectionId="address-details" color="indigo-500">
            <x-apllicant-modal.contact-details :id="$passId" :reportType="$reportType" />
        </x-accordion-section>

        <x-accordion-section title="Bank Details" sectionId="bank-details" color="green-500">
            <x-apllicant-modal.bank-account-details :id="$passId" :reportType="$reportType" />
        </x-accordion-section>

         <x-accordion-section title="Encloser Details" sectionId="encloser-details" color="orange-500">
            <x-apllicant-modal.encloser-list :id="$passId" :reportType="$reportType" />
        </x-accordion-section>

    </div>
</div>
