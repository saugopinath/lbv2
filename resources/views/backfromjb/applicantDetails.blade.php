<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">View Application Form</h1>
        </div>
    </div>
    <form action="{{ route('backfromjbactions') . '?id=' . Crypt::encryptString($applicant_details['applicationId']) }}" method="POST">
        @csrf
        <div
            x-data="{
            openSection: 'applicant-details',
    }"
            class="bg-white dark:bg-gray-800 shadow-md rounded p-8 space-y-2">
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
                        <p class="text-xs text-gray-500">Father's Name :</p>
                        <p class="font-semibold text-gray-800">{{$applicant_details['fatherName']}}</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Mother's Name :</p>
                        <p class="font-semibold text-gray-800">{{$applicant_details['motherName']}}</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Email Id., if available :</p>
                        <p class="font-semibold text-gray-800">{{$applicant_details['email']}}</p>
                    </div>
                </div>
            </x-accordion-section>
            <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Current Date of Birth (DD-MM-YYYY):</p>
                        <p class="font-semibold text-gray-800">{{$applicant_details['dob']}}</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Proposed Date of Birth from JB (DD-MM-YYYY):</p>
                        <p class="font-semibold text-gray-800">{{$applicant_details['jb_poposed_dob_show']}}</p>
                    </div>
                </div>
            </div>
            @if($role == 'verifier')
            <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <x-form.input type="date"
                            name="new_dob"
                            id="new_dob"
                            label="New Date of Birth"
                            required
                            value="{{ $applicant_details['jb_poposed_dob'] }}" max="{{ $applicant_details['maxDOB'] }}"
                            min="{{ $applicant_details['minDOB'] }}" />
                    </div>
                </div>
            </div>
            @endif
            @if($role == 'approver')
            <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">New Date of Birth <span class="text-red-700 font-bold">*</span></p>
                        <p class="font-semibold text-gray-800">{{$applicant_details['jb_poposed_dob_show']}}</p>
                    </div>
                </div>
            </div>
            @endif
            <div class="flex mt-4 pr-4">
                <x-button.primary type="submit" name="action"
                    value="{{$btnAction}}">{{$btnActionText}}
                </x-button.primary>
            </div>
        </div>
    </form>
</x-layouts.app>