<div class="bg-white dark:bg-gray-800 shadow-md rounded p-8 space-y-4">
    <div class="bg-blue-200 dark:bg-gray-800 shadow-md rounded p-2 space-y-2 text-center border border-blue-300">
        <h2 class="text-lg font-semibold">Application Name: {{ $application->full_name ?? '-' }}</h2>
        {{-- <h2 class="text-lg font-semibold">{{ $label }}: {{ $value }}</h2> --}}
    </div>

    <div x-data="{
        openSection: 'personal-details',
        toggleSection(section) {
            if (this.openSection === section) {
                this.openSection = 'personal-details';
            } else {
                this.openSection = section;
            }
        }
    }" class="space-y-2">
        <x-accordion-section title="Personal Detailsss" sectionId="personal-details" color="pink-500">
            <x-apllicant-modal.personal-details :id="$applicationId" mode="page" />


        </x-accordion-section>

        <x-accordion-section title="Address Details" sectionId="address-details" color="indigo-500">
            <x-apllicant-modal.contact-details :id="$applicationId" mode="page" />

        </x-accordion-section>

        <x-accordion-section title="Bank Details" sectionId="bank-details" color="green-500">
            <x-apllicant-modal.bank-account-details :id="$applicationId" mode="page" />

        </x-accordion-section>

        <x-accordion-section title="Encloser Details" sectionId="encloser-details" color="orange-500">
            <livewire:enclosure-list :application_id="$applicationId" :is_page="1" />
        </x-accordion-section>

        {{-- <x-button.primary> Action </x-button.primary>  --}}
        <div class="flex space-x-2">

            <x-button.primary wire:click="openActionModal" wire-target="openActionModal"  x-on:click="Livewire.dispatch('showLoader')">
                Action
            </x-button.primary>
            @livewire('process-application.bulk-action-modal')

        </div>



    </div>
