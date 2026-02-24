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
        <x-form.back-button :url="url()->previous()" />
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-6">
        <livewire:application-details.tab-wise-application-view :id="$application_id" :schemeId="$scheme_id"
            :allowedTabCodes="[101]" />
        <div x-data="{
        selectedCaste: '{{ old('caste', $oldData['caste'] ?? '') }}',
        showCasteDetails() {
            return this.selectedCaste == 1 || this.selectedCaste == 2;
        }
    }" class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6 mt-4">

            <h2 class="text-lg font-semibold text-indigo-800 dark:text-white mb-4">
                {{ $isReverted ? 'Update Reverted Caste Information' : 'Update Caste Information' }}
            </h2>

            <form action="{{ route('beneficiary.updateCaste') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="application_id" value="{{ Crypt::encryptString($application_id) }}">
                <input type="hidden" name="scheme_id" value="{{ Crypt::encryptString(20) }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Dropdown -->
                    <div>
                        <x-form.select id="caste" name="caste" label="New Caste" x-model="selectedCaste">
                            <option value="">-- Select New Caste --</option>
                            @foreach ($castes as $key => $label)
                            <option value="{{ $key }}" {{ old('caste', $oldData['caste'] ?? '') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </x-form.select>
                    </div>

                    <!-- Certificate No Input -->
                    <div x-show="showCasteDetails()" x-cloak>
                        <x-form.input name="cast_no" id="cast_no" label="New Caste Certificate No."
                            placeholder="Caste Certificate No."
                            value="{{ old('cast_no', $oldData['caste_certificate_no'] ?? '') }}" />
                    </div>
                </div>

                <!-- Document Upload Section -->
                <div x-show="showCasteDetails()" x-cloak>
                    <livewire:enclosure-list :application_id="$application_id" :doc_type_id_array_list="$doctype" :scheme_id="$scheme_id"
                        enclosureSource="5" />
                    @error('document_upload')
                    <div class="text-sm text-red-600 mb-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <x-button.loading-spiner-button
                        type="submit"
                        text="Submit"
                        lockPage="true" />
                </div>
            </form>
        </div>
    </div>


    <!-- Accordion Section -->

    <!-- Caste Modification Form -->
    {{-- <div x-data="{ selectedCaste: '{{ old('caste', $oldData['caste'] ?? '') }}' }"
    class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">

    <h2 class="text-lg font-semibold text-indigo-800 dark:text-white mb-4">
        {{ $isReverted ? 'Update Reverted Caste Information' : 'Update Caste Information' }}
    </h2>

    <form action="{{ route('beneficiary.updateCaste') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="application_id" value="{{ Crypt::encryptString($application_id) }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-form.select id="caste" name="caste" label="New Caste" x-model="selectedCaste">
                    <option value="">-- Select New Caste --</option>
                    @foreach ($castes as $key => $label)
                    <option value="{{ $key }}" {{ old('caste', $oldData['caste'] ?? '' )==$key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </x-form.select>
            </div>

            <div x-show="selectedCaste && selectedCaste != 19" x-cloak>
                <x-form.input name="cast_no" id="cast_no" label="New Caste Certificate No."
                    placeholder="Caste Certificate No."
                    value="{{ old('cast_no', $oldData['caste_certificate_no'] ?? '') }}" />
            </div>
        </div>

        <livewire:enclosure-list :application_id="$application_id" :doc_type_id_array_list="$doctype"
            enclosureSource="5" />

        <div class="flex justify-end">
            <x-button.loading-button type="submit" text="Submit" x-data x-on:click.prevent="
            Livewire.dispatch('showLoader');
            $el.form.submit();
        " />
        </div>
    </form>
    </div> --}}
    <!-- Caste Modification Form -->


</x-layouts.app>