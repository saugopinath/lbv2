<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{$header}}</h1>
        </div>
    </div>

    <div
        x-data="{
        openSection: 'grievance-details',
        toggleSection(section) {
            if (this.openSection === section) {
                this.openSection = 'grievance-details';
            } else {
                this.openSection = section;
            }
        }
    }"
        class="bg-white dark:bg-gray-800 shadow-md rounded p-8 space-y-2">
        <x-accordion-section title="Grievance Details" sectionId="grievance-details" color="pink-500">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Grievance ID :</p>
                    <p class="font-semibold text-gray-800">{{$record->grievance_id}}</p>
                </div>

                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Grievance No :</p>
                    <p class="font-semibold text-gray-800">{{$record->grievance_no}}</p>
                </div>

                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Caller Name :</p>
                    <p class="font-semibold text-gray-800">{{$record->applicant_name}}</p>
                </div>

                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Caller Mobile No. :</p>
                    <p class="font-semibold text-gray-800">{{$record->pri_cont_no}}</p>
                </div>

                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Age :</p>
                    <p class="font-semibold text-gray-800">{{$record->applicant_age}}</p>
                </div>

                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Address :</p>
                    <p class="font-semibold text-gray-800">{{ $record->applicant_address ?: 'N/A' }}</p>
                </div>

                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition lg:col-span-4">
                    <p class="text-xs text-gray-500">Description :</p>
                    <p class="font-semibold text-gray-800">{{$record->grievance_description}}</p>
                </div>

            </div>
        </x-accordion-section>
        <x-accordion-section title="ATR Tagging" sectionId="atr_tagging" color="indigo-500">
            <div class="">
                <div
                    x-data="{ atr: { id: '', can_find_applicant: '', atr_code: '' } }"
                    class="space-y-3">
                    <!-- ATR Type Dropdown -->
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <x-form.select
                            name="atr_type"
                            required
                            class="border rounded p-2 w-full"
                            x-on:change="atr = JSON.parse($event.target.value)" label="ATR Type">
                            <option value="">-----ATR Type----</option>
                            @foreach ($atrs as $type)
                            <option value='@json(["id" => $type->atn_id, "can_find_applicant" => $type->can_find_applicant,
                "atr_code" => $type->atr_code])'>
                                {{ $type->atr_desc }}
                            </option>
                            @endforeach
                        </x-form.select>
                    </div>

                    <!-- Remarks -->
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <x-form.input
                            id="remarks"
                            name="remarks"
                            label="Remarks"
                            required
                            type="text" />
                    </div>

                    <!-- Conditional Section -->
                    <template x-if="atr.can_find_applicant == 1 && atr.atr_code != '002'">
                        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                            <x-button.primary>Map Applicant</x-button.primary>
                        </div>
                    </template>

                    <template x-if="atr.can_find_applicant == null">
                        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                            <x-button.danger>Grievance Redressed</x-button.danger>
                        </div>
                    </template>

                    <template x-if="atr.can_find_applicant == 1 && atr.atr_code == '002'">
                        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                            <livewire:filter-lgd-master-entry :login_type="'state_office'" />
                            <x-button.primary>Send to another Block/Subdivision</x-button.primary>
                        </div>
                    </template>

                </div>
            </div>
        </x-accordion-section>

    </div>

</x-layouts.app>