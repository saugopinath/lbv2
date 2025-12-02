<x-layouts.app>
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-4">
        <div class="flex items-center space-x-3">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">
                {{ $header }}
            </h1>
            <span
                class="px-4 py-1.5 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 shadow-sm">
                Application Id {{ $application_id }}
            </span>

        </div>
    </div>
    <!-- Accordion Section -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-6">
        <div x-data="{
            openSection: 'personal-details',
            toggleSection(section) {
                this.openSection = this.openSection === section ? '' : section;
            }
        }" class="space-y-2">

            <x-accordion-section title="Personal Details" sectionId="personal-details" color="pink-500">
                <x-apllicant-modal.personal-details :id=$application_id :reportType="$reportType" mode="page" />
            </x-accordion-section>
        </div>
    </div>
    <!-- Caste Modification Form -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">

        <h2 class="text-lg font-semibold text-indigo-800 dark:text-white mb-4">
           Reject Approved Beneficiary
        </h2>

        <form action="{{ route('beneficiary.deActivebeneficiary') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="application_id" value="{{ Crypt::encryptString($application_id) }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                 <x-form.select name="reject_reason" id="reject_reason" label="Reject Reason" required>
                    <option value="">Select</option>
                    @foreach ($rejectRevertCause as $cause)
                    <option value="{{ $cause['id'] }}">{{ $cause['name'] }}</option>
                    @endforeach
                </x-form.select>
                <x-form.input type="textarea" wire:model.defer="remark" placeholder="Enter remark" id="remark"
                    name="remark"
                    label="Remark" required />

                <x-form.select name="doctype" id="doctype" required>
                    <option value="">Select</option>
                    @foreach ($doctypes as $doctype)
                    <option value="{{ $doctype['id'] }}">{{ $doctype['name'] }}</option>
                    @endforeach
                </x-form.select> 


            </div>
            <div class="flex justify-center">
                <x-button.loading-button type="submit" text="Submit" x-data
                    x-on:click.prevent="Livewire.dispatch('showLoader'); $el.form.submit();" />
            </div>
        </form>
    </div>


</x-layouts.app>
