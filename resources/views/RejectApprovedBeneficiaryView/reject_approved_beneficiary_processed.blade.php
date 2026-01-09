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
        <div x-data="{ openSection: 'personal-details', toggleSection(section) { this.openSection = this.openSection === section ? '' : section; } }" class="space-y-2">
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

        <!-- {{-- GLOBAL ERROR BLOCK --}}
        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-red-700">
                <strong>There were some problems with your input:</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif -->

        <form action="{{ route('beneficiary.deActivebeneficiary') }}" method="POST" class="space-y-4">
            @csrf

            <input type="hidden" name="application_id" value="{{ old('application_id', Crypt::encryptString($application_id)) }}">
            <input type="hidden" name="beneficiary_id" value="{{ old('beneficiary_id', Crypt::encryptString($beneficiary_id ?? '')) }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-form.selected name="reject_reason" id="reject_reason" label="Reject Reason" required>
                        <option value="">--Select Reason--</option>
                        @foreach ($rejectRevertCause as $cause)
                        <option value="{{ $cause['id'] }}"
                            {{ (string) old('reject_reason') === (string) $cause['id'] ? 'selected' : '' }}>
                            {{ $cause['name'] }}
                        </option>
                        @endforeach
                    </x-form.selected>

                </div>

                <x-form.input type="textarea" name="remark" id="remark" label="Remark" placeholder="Enter remark" required />

                <div class="md:col-span-2">
                    <livewire:enclosure-list :application_id="$application_id" :doc_type_id_array_list="$doctypes" />
                    @if($errors->has('document'))
                    <p class="text-red-500 text-xs mt-2">{{ $errors->first('document') }}</p>
                    @endif
                </div>
            </div>

            <div class="flex justify-center">
                <x-button.loading-spiner-button
                    type="submit"
                    text="Submit"
                    lockPage="true" />
            </div>
        </form>
    </div>
</x-layouts.app>