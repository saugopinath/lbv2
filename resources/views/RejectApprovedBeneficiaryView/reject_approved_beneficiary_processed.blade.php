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
        <x-form.back-button :url="route('reject-approved-beneficiary')" />
    </div>
    <!-- Accordion Section -->

    <!-- Caste Modification Form -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">
        <h2 class="text-lg font-semibold text-indigo-800 dark:text-white mb-4">
            Reject Approved Beneficiary
        </h2>
        <div class="mb-2">
            <livewire:application-details.tab-wise-application-view :id="$application_id" :schemeId="$scheme_id"
                :allowedTabCodes="[101]" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4 p-4">

            <form action="{{ route('beneficiary.deActivebeneficiary') }}" method="POST" class="space-y-4">
                @csrf

                <input type="hidden" name="application_id" value="{{ old('application_id', Crypt::encryptString($application_id)) }}">
                <input type="hidden" name="beneficiary_id" value="{{ old('beneficiary_id', Crypt::encryptString($beneficiary_id ?? '')) }}">
                <input type="hidden" name="scheme_id" value="{{ old('scheme_id', Crypt::encryptString($scheme_id ?? '')) }}">

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
                        <livewire:enclosure-list :application_id="$application_id" :scheme_id="$scheme_id" :doc_type_id_array_list="[$doctypes]" />
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
    </div>
</x-layouts.app>