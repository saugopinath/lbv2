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

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-6">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-6">
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
                <x-accordion-section title="Personal Details" sectionId="personal-details" color="pink-500">
                    <x-apllicant-modal.personal-details :id="$application_id" :reportType="$reportType" mode="page" />
                </x-accordion-section>

                <x-accordion-section title="Address Details" sectionId="address-details" color="indigo-500">
                    <x-apllicant-modal.contact-details :id="$application_id" :reportType="$reportType" mode="page" />
                </x-accordion-section>
                <x-accordion-section title="Bank Details" sectionId="bank-details" color="green-500">
                    <x-apllicant-modal.bank-account-details :id="$application_id" :reportType="$reportType" mode="page" />
                </x-accordion-section>
            </div>
        </div>
       

        @if ($sectionType==0)
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">
                    Mark Beneficiary
                </h2>
            </div>

            <form action="{{ route('final-marked') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="application_id" value="{{ old('application_id', Crypt::encryptString($application_id)) }}">
                <input type="hidden" name="beneficiary_id" value="{{ old('beneficiary_id', Crypt::encryptString($beneficiary_id ?? '')) }}">

                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-5 md:p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Fields to Mark
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($marked_allowed_field as $field)
                        <div class="group relative">
                            <div class="flex items-start p-3 rounded-lg border border-gray-200 dark:border-gray-700 
                                  hover:bg-white dark:hover:bg-gray-800 hover:border-indigo-300 dark:hover:border-indigo-500 
                                  hover:shadow-sm transition-all duration-200">
                                <x-form.checkbox
                                    name="marked_fields[]"
                                    value="{{ $field->id }}"
                                    label="{{ $field->name }}"
                                    id="field_{{ $field->id }}"
                                    vertical
                                    class="checkbox-primary" />
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if(count($marked_allowed_field) == 0)
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="mt-3 text-gray-500 dark:text-gray-400">No fields available for marking.</p>
                    </div>
                    @endif
                </div>
                <x-button.loading-spiner-button
                    type="submit"
                    text="Submit Changes"
                    lockPage="true"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-indigo-700 
                           hover:from-indigo-700 hover:to-indigo-800 rounded-lg shadow-sm 
                           hover:shadow transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" />
            </form>
        </div>
        @endif
    </div>
</x-layouts.app>