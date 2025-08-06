<div>
    <div class="p-6 bg-white rounded shadow">
        <h2 class="text-xl font-semibold text-center mb-6">View Application Form</h2>
        <div class="mb-4 text-center text-red-500 font-bold">
            Application ID: {{ $application->application_id }}
        </div>

        <!-- <div class="mb-6">
            <h3 class="text-lg font-semibold border-b mb-2">Personal Details</h3>

            <p><strong>Duare Sarkar Registration no:</strong> {{ $application->ds_registration_no ?? '-' }}</p>
            <p><strong>Name:</strong> {{ $application->full_name ?? '-' }}</p>
            <p><strong>Mobile Number:</strong> {{ $application->mobile_no }}</p>
            <p><strong>Gender:</strong> {{ $application->gender }}</p>
            <p><strong>Age:</strong> {{ $application->age }}</p>
            <p><strong>Father's name:</strong> {{ $application->ben_relationships->where('relation_type_id', 79)->first()?->full_name ?? '-' }}</p>
            <p><strong>Mother's name:</strong> {{ $application->ben_relationships->where('relation_type_id', 80)->first()?->full_name ?? '-' }}</p>
            <p><strong>Spouse Name:</strong> {{ $application->spouse_name }}</p>
            <p><strong>Caste:</strong> {{ $application->caste }}</p>
            <p><strong>Date of Birth:</strong> {{ $application->dob }}</p>
            <p><strong>Email ID:</strong> {{ $application->email }}</p>
            <p><strong>Duare Sarkar Date:</strong> {{ $application->registration_date }}</p>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-semibold border-b mb-2">Contact Details</h3>
            <p><strong>State:</strong> {{ $application->state }}</p>
            <p><strong>District:</strong> {{ $application->district }}</p>
            <p><strong>Block:</strong> {{ $application->block }}</p>
            <p><strong>Village/Town:</strong> {{ $application->village }}</p>
            <p><strong>Post Office:</strong> {{ $application->post_office }}</p>
            <p><strong>Police Station:</strong> {{ $application->police_station }}</p>
            <p><strong>GP/Ward No:</strong> {{ $application->gp_ward_no }}</p>
            <p><strong>House/Premise No:</strong> {{ $application->house_no }}</p>
            <p><strong>Pin Code:</strong> {{ $application->pin_code }}</p>
        </div>

        <div>
            <h3 class="text-lg font-semibold border-b mb-2">Bank Details</h3>
            <p><strong>Bank Name:</strong> {{ $application->bank->ifscMaster->bankMaster->name }}</p>
            <p><strong>Branch Name:</strong> {{ $application->bank->ifscMaster->branch }}</p>
            <p><strong>Account No:</strong> {{ $application->bank->bank_account_number }}</p>
            <p><strong>IFSC Code:</strong> {{ $application->bank->ifsc }}</p>
        </div> -->
    </div>

    <div class="bg-gray-100 font-sans">
        <div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-200">
            <div class="container mx-auto px-4 py-10 max-w-5xl">
                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row justify-between items-center mb-10">
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">View Application Form</h1>

                    <div class="flex space-x-4 mt-4 sm:mt-0">
                        <button type="button"
                            class="px-5 py-2.5 bg-purple-500 text-black rounded-full hover:bg-gray-400 transition hover:scale-105">
                            Cancel
                        </button>

                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 rounded-full hover:bg-indigo-700 transition hover:scale-105 flex items-center">
                            <i class="fas fa-print mr-2"></i> Print
                        </button>

                        <button type="button"
                            class="px-5 py-2.5 bg-gray-300 text-gray-800 rounded-full hover:bg-gray-400 transition hover:scale-105 flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </button>
                    </div>

                </div>

                <!-- Application Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-slide-in">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 py-5">
                        <div class="flex flex-col sm:flex-row justify-between items-center">
                            <h2 class="text-2xl font-semibold text-white">Application Details</h2>
                            <span
                                class="bg-indigo-100 text-indigo-800 px-4 py-1.5 rounded-full text-sm font-medium mt-3 sm:mt-0">
                                Application ID: {{ $application->application_id }}
                            </span>
                        </div>
                    </div>

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
                                    <p class="text-sm text-gray-500">Gender</p>
                                    <p class="font-medium text-gray-900"></p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Age</p>
                                    <p class="font-medium text-gray-900"></p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Father's Name</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $application->ben_relationships->where('relation_type_id', 79)->first()?->full_name ?? '-' }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Mother's Name</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $application->ben_relationships->where('relation_type_id', 80)->first()?->full_name ?? '-' }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Caste</p>
                                    <p class="font-medium text-gray-900"></p>
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
                                    <p class="font-medium text-gray-900">{{ $application->state->name ?? '-' }}</p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Police Station</p>
                                    <p class="font-medium text-gray-900"></p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">GP/Ward No.</p>
                                    <p class="font-medium text-gray-900"></p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Pin Code</p>
                                    <p class="font-medium text-gray-900"></p>
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
                                        {{ $application->bank->ifscMaster->bankMaster->name }}</p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Bank Account No.</p>
                                    <p class="font-medium text-gray-900">{{ $application->bank->bank_account_number }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">Bank Branch Name</p>
                                    <p class="font-medium text-gray-900">{{ $application->bank->ifscMaster->branch }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                                    <p class="text-sm text-gray-500">IFS Code</p>
                                    <p class="font-medium text-gray-900">{{ $application->bank->ifsc }}</p>
                                </div>
                            </div>
                        </div>


                        <!-- Additional Information Section -->

                    </div>

                    <!-- Card Footer -->
                    <div class="bg-gray-50 px-8 py-5 border-t border-gray-200">
                        <div class="flex justify-end space-x-4">
                            <button
                                class="px-6 py-2.5 bg-red-600 rounded-full hover:bg-green-700 transition hover-scale flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>Approve
                            </button>
                            <button
                                class="px-6 py-2.5 bg-red-600 rounded-full hover:bg-red-700 transition hover-scale flex items-center">
                                <i class="fas fa-times-circle mr-2"></i>Reject
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
