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
    <div x-data="{ selectedCaste: '{{ old('caste', $oldData['caste'] ?? '') }}' }" class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">

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
                            <option value="{{ $key }}"
                                {{ old('caste', $oldData['caste'] ?? '') == $key ? 'selected' : '' }}>
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
{{--  @dd($doctype);  --}}
            <livewire:enclosure-list :application_id="$application_id" :doc_type_id_array_list="$doctype" enclosureSource="5" />

            <div class="flex justify-end">
                <x-button.loading-button type="submit" text="Submit" x-data
                    x-on:click.prevent="
            Livewire.dispatch('showLoader');
            $el.form.submit();
        " />
            </div>
        </form>
    </div>


</x-layouts.app>
