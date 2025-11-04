<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{ $header }}</h1>
        </div>
    </div>
    <div
        x-data="{
            openSection: 'grievance-details',
            atr: { id: '', can_find_applicant: '', atr_code: '' },
            toggleSection(section) {
                if (this.openSection === section) {
                    this.openSection = 'grievance-details';
                } else {
                    this.openSection = section;
                }
            },
            openSearchSection() {
                this.openSection = 'search-details';
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
            <form action="{{ route('cmo-grievance-action') . '?id=' . Crypt::encryptString($record->grievance_id) }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <x-form.select
                            name="atr_type"
                            required
                            class="border rounded p-2 w-full"
                            x-on:change="
                                if ($event.target.value) {
                                    atr = JSON.parse($event.target.value)
                                } else {
                                    atr = { id: '', can_find_applicant: '', atr_code: '' }
                                }
                            "
                            label="ATR Type">
                            <option value="">-----ATR Type----</option>
                            @foreach ($atrs as $type)
                            <option value='@json(["id" => $type->atn_id, "can_find_applicant" => $type->can_find_applicant, "atr_code" => $type->atr_code])'>
                                {{ $type->atr_desc }}
                            </option>
                            @endforeach
                        </x-form.select>
                    </div>
                    <template x-if="atr.can_find_applicant == 1 && atr.atr_code == '002'">
                        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                            <livewire:filter-lgd-master-entry :login_type="'state_office'" />
                        </div>
                    </template>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <x-form.input id="remarks" name="remarks" label="Remarks" required type="text" />
                    </div>
                    <template x-if="atr.can_find_applicant == 1 && atr.atr_code != '002'">
                        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                            <x-button.primary type="button" name="action_type" value="map_applicant" x-on:click="openSearchSection()">
                                Map Applicant
                            </x-button.primary>
                        </div>
                    </template>
                    <template x-if="atr.can_find_applicant == null">
                        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                            <x-button.danger type="submit" name="action_type" value="grievance_redressed">
                                Grievance Redressed
                            </x-button.danger>
                        </div>
                    </template>
                    <template x-if="atr.can_find_applicant == 1 && atr.atr_code == '002'">
                        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                            <x-button.primary type="submit" name="action_type" value="send_another_block">
                                Send to another Block/Subdivision
                            </x-button.primary>
                        </div>
                    </template>
                </div>
            </form>
        </x-accordion-section>
        <div
            x-show="openSection === 'search-details'"
            x-collapse
            class="">
            <x-accordion-section
                title="Search using Application Id, Beneficiary name, Applicant mobile no, Aadhaar no, Bank account number"
                sectionId="search-details"
                color="indigo-500">
                <form action="{{ route('cmo-grievance-search') }}" method="POST">
                    @csrf
                    <div x-data="{ selectedOption: 'Mobile Number' }">
                        <div class="bg-gray-50 p-3 rounded-lg shadow hover:shadow-md transition">
                            <x-form.label
                                name="Please select which one do you want to Search?"
                                label="Please select which one do you want to Search?">
                            </x-form.label>
                            <x-form.radio name="search-details" id="application-id" value="Application ID" x-model="selectedOption"></x-form.radio>
                            <x-form.radio name="search-details" id="beneficiary-name" value="Beneficiary Name" x-model="selectedOption"></x-form.radio>
                            <x-form.radio name="search-details" id="mobile-number" value="Mobile Number" x-model="selectedOption"></x-form.radio>
                            <x-form.radio name="search-details" id="aadhaar-number" value="Aadhaar Number" x-model="selectedOption"></x-form.radio>
                            <x-form.radio name="search-details" id="bank-account-number" value="Bank Account Number" x-model="selectedOption"></x-form.radio>
                        </div>
                        <div class="mt-4 bg-gray-50 p-3 rounded-lg shadow hover:shadow-md transition">
                            <label class="block text-sm font-medium text-gray-700">
                                <span x-text="selectedOption ? selectedOption : 'Enter value'"></span>
                                <span class="text-red-600 font-bold">*</span>
                            </label>
                            <input
                                type="text"
                                class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                x-bind:name="selectedOption"
                                x-bind:placeholder="selectedOption ? 'Enter ' + selectedOption : 'Enter value'"
                                required value="{{$record->pri_cont_no}}">
                            <div class="flex justify-end mt-4 space-x-2">
                                <x-button.primary
                                    type="submit"
                                    name="action_type"
                                    value="search"
                                    class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                                    GO
                                </x-button.primary>

                                <x-button.danger
                                    type="submit"
                                    name="action_type"
                                    value="send_to_operator"
                                    class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                                    Send To Operator For New Entry
                                </x-button.danger>
                            </div>

                        </div>
                    </div>
                </form>
            </x-accordion-section>
        </div>
    </div>
</x-layouts.app>