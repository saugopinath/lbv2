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
             remarks: '',
        atr_type: '',
        errorMessage: '',
            toggleSection(section) {
                if (this.openSection === section) {
                    this.openSection = 'grievance-details';
                } else {
                    this.openSection = section;
                }
            },
            openSearchSection() {
            if (!this.remarks || this.remarks.trim() === '') {
        this.errorMessage = 'Remarks field is required.';
            return;
    }
            this.errorMessage = '';
             Livewire.dispatch('updateGrievanceData', { 
                remarks: this.remarks, 
                atr_type: this.atr_type 
            });
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
        @if($isaddvisible)
        <x-accordion-section title="ATR Tagging" sectionId="atr_tagging" color="indigo-500">
            <form action="{{ route('cmo-grievance-action') . '?id=' . Crypt::encryptString($record->grievance_id) }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <x-form.select
                            name="atr_type" x-model="atr_type"
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
                        <x-form.input id="remarks" name="remarks" label="Remarks" required type="text" x-model="remarks" />
                        <template x-if="errorMessage">
                            <p class="text-red-500 text-xs mt-2" x-text="errorMessage"></p>
                        </template>
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
                <livewire:grievance-search :mobile-number="$record->pri_cont_no" :grievance-id="Crypt::encryptString($record->grievance_id)" />
                <form action="{{ route('cmo-grievance-search') . '?id=' . Crypt::encryptString($record->grievance_id) }}" method="POST">
                    @csrf
                    <x-button.danger
                        type="submit"
                        name="action_type"
                        value="send_to_operator"
                        class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                        Send To Operator For New Entry
                    </x-button.danger>
                </form>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <livewire:cmo-details-data-table
                        :initial-mobile="$record->pri_cont_no"
                        :grievance-id="Crypt::encryptString($record->grievance_id)" />

                </div>
            </x-accordion-section>
        </div>
        @endif
        @if($isaddvisible == 0 && $atr)
        <x-accordion-section title="ATR Type Details" sectionId="atr-type-details" color="pink-500">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">ATR Type :</p>
                    <p class="font-semibold text-gray-800">{{$atr->atr_desc}}</p>
                </div>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Remarks :</p>
                    <p class="font-semibold text-gray-800">{{$record->remarks}}</p>
                </div>
            </div>
        </x-accordion-section>
        @endif
        @if($isaddbutton && $applicant_details)
        <x-accordion-section title="Applicant Details" sectionId="applicant-details" color="pink-500">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Application Id :</p>
                    <p class="font-semibold text-gray-800">{{$applicant_details['applicationId']}}</p>
                </div>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Name :</p>
                    <p class="font-semibold text-gray-800">{{$applicant_details['name']}}</p>
                </div>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Mobile Number :</p>
                    <p class="font-semibold text-gray-800">{{$applicant_details['mobileNo']}}</p>
                </div>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Date of Birth :</p>
                    <p class="font-semibold text-gray-800">{{$applicant_details['dob']}}</p>
                </div>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Father's Name :</p>
                    <p class="font-semibold text-gray-800">{{$applicant_details['fatherName']}}</p>
                </div>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Block/Municipality/Corp :</p>
                    <p class="font-semibold text-gray-800">{{$applicant_details['blockMuni']}}</p>
                </div>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">GP/Ward No :</p>
                    <p class="font-semibold text-gray-800">{{$applicant_details['gpWard']}}</p>
                </div>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Bank Name :</p>
                    <p class="font-semibold text-gray-800">{{$applicant_details['bankName']}}</p>
                </div>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Bank Branch Name :</p>
                    <p class="font-semibold text-gray-800">{{$applicant_details['branchName']}}</p>
                </div>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Bank Account No :</p>
                    <p class="font-semibold text-gray-800">{{$applicant_details['accNo']}}</p>
                </div>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">IFS Code :</p>
                    <p class="font-semibold text-gray-800">{{$applicant_details['ifscCode']}}</p>
                </div>
            </div>
        </x-accordion-section>
        @endif
        <form action="{{ route('cmo-add-actions') . '?id=' . Crypt::encryptString($record->grievance_id) }}" method="POST">
            @csrf
            @if($isaddbutton == 1)
            <div class="flex justify-end gap-2">
                <x-button.danger
                    type="submit"
                    name="action_type"
                    value="approve"
                    class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                    Approve
                </x-button.danger>
                <x-button.danger
                    type="submit"
                    name="action_type"
                    value="revert"
                    class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                    Revert
                </x-button.danger>
            </div>
            @elseif($isaddbutton == 2)
            <x-button.danger
                type="submit"
                name="action_type"
                value="pushtocmo"
                class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                Push to CMO
            </x-button.danger>
            @endif
        </form>
    </div>
</x-layouts.app>