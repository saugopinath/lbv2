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
        </div>
    </div>

    <!-- Caste Modification Form -->
    <div x-data="{ selectedCaste: null }" class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">
        <h2 class="text-lg font-semibold text-indigo-800 dark:text-white mb-4">
            Update Caste Information
        </h2>

        <form action="{{ route('beneficiary.updateCaste') }}" method="POST" class="space-y-6">
            @csrf
            <input type="text" name="application_id" value="{{$application_id}}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-form.select id="caste" name="caste" label="New Caste" required
                        x-model="selectedCaste">
                        <option value="">-- Select New Caste --</option>
                        @foreach($castes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </x-form.select>
                </div>
                <div x-show="selectedCaste && selectedCaste != 19" x-cloak>
                    <x-form.input
                        name="cast_no"
                        id="cast_no"
                        label="New Caste Certificate No."
                        placeholder="Caste Certificate No." />
                </div>
            </div>
            <livewire:enclosure-list
                :application_id="$application_id"
                :doc_type_id_array_list="$doctype" :enclosureSource="5" />


            <div class="flex justify-end">
                <x-button.indigo type="submit">
                    Submit
                </x-button.indigo>
            </div>
        </form>
    </div>
</x-layouts.app>