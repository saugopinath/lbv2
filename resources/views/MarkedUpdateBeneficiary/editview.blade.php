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

        @if ($sectionType==1)
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 md:p-8">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-4">
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                            Update Marked Beneficiary Details
                        </h1>
                    </div>
                </div>
            </div>
            <!-- form for update Beneficiary Details -->
            {{-- Name --}}
            <form method="POST" action="{{ route('marked-beneficiary-details-update') }}">
                @csrf
                <div class="beneficiary-form-container">
                    <div class="form-fields-grid mb-2">

                        {{-- Row 1: Name and DOB --}}
                        @if($visible_Name || $visible_DOB || $visible_Mobile)
                        <div class="mb-6 rounded-lg border border-gray-200 overflow-hidden">
                            <!-- Section Header -->
                            <div class="bg-gradient-to-r from-gray-50 to-white p-2 border-b border-gray-200">
                                <div class="flex items-center space-x-1">
                                    <span class="h-6 w-1.5 bg-pink-500 rounded-full"></span>
                                    <h3 class="text-lg font-semibold text-gray-800 ">Update Personal Details</h3>
                                </div>
                            </div>

                            <!-- Section Content -->
                            <div class="p-6 bg-white">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @if($visible_Name)
                                    <div class="space-y-2">
                                        <x-form.input name="name" label="Beneficiary Name" />
                                    </div>
                                    @endif

                                    @if($visible_DOB)
                                    <div class="space-y-2">
                                        <x-form.input type="date" name="dob" label="Date of Birth" />
                                    </div>
                                    @endif

                                    @if($visible_Mobile)
                                    <div class="md:col-span-2 space-y-2">
                                        <x-form.input name="mobile" label="Mobile Number" />
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Address Section -->
                        @if($visible_Address)
                        <div class="mb-6 rounded-lg border border-gray-200 overflow-hidden">
                            <!-- Section Header -->
                            <div class="bg-gradient-to-r from-gray-50 to-white p-2 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <span class="h-6 w-1.5 bg-blue-500 rounded-full"></span>
                                    <h3 class="text-lg font-semibold text-gray-800">Update Address Information</h3>
                                </div>
                            </div>
                            <!-- Section Content -->
                            <livewire:filter-lgd-master-entry :login_type="'state_office'" :selectedDistrict="$selectedDistrict"
                                :selectedRuralurban="$selectedRuralurban"
                                :selectedBlockurban="$selectedBlockurban"
                                :selectedGpWard="$selectedGpWard" />
                            <div class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
                                <div>
                                    <x-form.input name="policestation" label="Police Station" value="{{ $policestation }}" required x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
                                </div>
                                <div>
                                    <x-form.input name="villtowncity" label="Village/Town/City" value={{$villtowncity}} required x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
                                </div>
                                <div>
                                    <x-form.input name="housepremiseno" label="House / Premise No." value="{{$housepremiseno}}" />
                                </div>
                                <div>
                                    <x-form.input name="postoffice" label="Post Office" value="{{$postoffice}}" required x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
                                </div>
                                <div>
                                    <x-form.input
                                        name="pincode"
                                        label="Pin Code"
                                        value="{{$pincode}}"
                                        required
                                        x-on:input=" $el.value = $el.value.replace(/[^0-9]/g, '').slice(0,6); $wire.set('pincode', $el.value);" />

                                </div>
                            </div>
                        </div>
                        @endif
                        @if($visible_Aadhar)
                        <div class="mb-6 rounded-lg border border-gray-200 overflow-hidden">
                            <!-- Section Header -->
                            <div class="bg-gradient-to-r from-gray-50 to-white p-2 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <span class="h-6 w-1.5 bg-blue-500 rounded-full"></span>
                                    <h3 class="text-lg font-semibold text-gray-800">Update Aadhar Information</h3>
                                </div>
                            </div>
                            <!-- Section Content -->
                            <livewire:dup-aadhaar-check />

                        </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <x-button.loading-spiner-button
                                type="submit"
                                text="Update Details"
                                lockPage="true"
                                class="w-full md:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow hover:shadow-lg transition-all" />
                        </div>
                    </div>

            </form>
        </div>
        @endif
    </div>
</x-layouts.app>