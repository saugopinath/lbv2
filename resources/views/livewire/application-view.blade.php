<div>
    <div class="p-6 bg-white rounded shadow">
        <h2 class="text-xl font-semibold text-center mb-6">View Application Form</h2>
        <div class="mb-4 text-center text-red-500 font-bold">
            Application ID: {{ $application->application_id }}
        </div>
    </div>

    <div class="bg-gray-100 font-sans">
        <div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-200">
            <div class="container mx-auto px-4 py-10 max-w-5xl">
                <!-- Application Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-slide-in">
                    <!-- Card Body -->
                    <div class="p-8">
                        <!-- Personal Details Section -->
                        <div class="mb-10">
                            <div class="flex items-center mb-6 border-b border-gray-200 pb-3">
                                <div class="h-12 w-1.5 bg-indigo-600 mr-4 rounded"></div>
                                <h3 class="text-xl font-semibold text-gray-900">Personal Details</h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Duare Sarkar Registration no</p>
                                    <p class="font-medium text-gray-900">{{ $application->ds_registration_no ?? '-' }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Name</p>
                                    <p class="font-medium text-gray-900">{{ $application->full_name ?? '-' }}</p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Mobile Number</p>
                                    <p class="font-medium text-gray-900">{{ $application->mobile_no ?? '-' }}</p>
                                </div>                              
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Age</p>
                                    <p class="font-medium text-gray-900">{{ $application->dob ? \Carbon\Carbon::parse($application->dob)->age : '-' }}</p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Father's Name</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $application->father->where('relation_type_id', 79)->first()?->full_name ?? '-' }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Mother's Name</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $application->father->where('relation_type_id', 80)->first()?->full_name ?? '-' }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Caste</p>
                                    <p class="font-medium text-gray-900">{{ $application->casteName->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Details Section -->
                        <div class="mb-10">
                            <div class="flex items-center mb-6 border-b border-gray-200 pb-3">
                                <div class="h-12 w-1.5 bg-green-600 mr-4 rounded"></div>
                                <h3 class="text-xl font-semibold text-gray-900">Contact Details</h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">State</p>
                                    <p class="font-medium text-gray-900">WEST BENGAL</p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Police Station</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $application->contact->police_station ?? '-' }}</p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">GP/Ward No.</p>
                                    <p class="font-medium text-gray-900">
                                        @if (!empty($application->contact->panchayat))
                                            {{ $application->contact->panchayat->name }}
                                        @elseif(!empty($application->contact->ward))
                                            {{ $application->contact->ward->name }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Pin Code</p>
                                    <p class="font-medium text-gray-900">{{ $application->contact->pin_code ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Bank Details Section -->
                        <div class="mb-10">
                            <div class="flex items-center mb-6 border-b border-gray-200 pb-3">
                                <div class="h-12 w-1.5 bg-purple-600 mr-4 rounded"></div>
                                <h3 class="text-xl font-semibold text-gray-900">Bank Details</h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Bank Name</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $application->bank->ifscCodeMaster->bankMaster->name ?? '' }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Bank Account No.</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $application->bank->bank_account_number ?? '' }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Bank Branch Name</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $application->bank->ifscCodeMaster->branch ?? '' }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">IFS Code</p>
                                    <p class="font-medium text-gray-900">{{ $application->bank->ifsc ?? '' }}</p>
                                </div>
                            </div>
                        </div>




                    </div>


                </div>
            </div>
        </div>
    </div>


</div>
