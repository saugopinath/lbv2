
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div x-data="{
        showFull: false,
        masked: '{{ str_repeat('*', strlen($decryptedAadhaar) - 4) . substr($decryptedAadhaar, -4) }}',
        full: '{{ $decryptedAadhaar }}'
    }" class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Aadhaar No.</p>
        <p class="font-semibold text-gray-800">
            <span x-text="showFull ? full : masked"></span>
            <x-button.primary @click="showFull = !showFull" class="ml-2 text-blue-600 text-xs cursor-pointer">
                <span x-text="showFull ? 'Hide' : 'Show Original'"></span>
            </x-button.primary>
        </p>
    </div>

    @if ($ds_registration_no != null)
        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
            <p class="text-xs text-gray-500">Duare Sarkar Registration No.:</p>
            <p class="font-semibold text-gray-800">{{ $ds_registration_no }}</p>
        </div>
        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
            <p class="text-xs text-gray-500">Duare Sarkar Date.:</p>
            <p class="font-semibold text-gray-800">{{ $ds_date }}</p>
        </div>
    @endif
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Name</p>
        <p class="font-semibold text-gray-800">{{ $beneficiary_name }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Mobile No.</p>
        <p class="font-semibold text-gray-800">{{ $mobile }}</p>
    </div>
    @if ($email != null)
        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
            <p class="text-xs text-gray-500">Email</p>
            <p class="font-semibold text-gray-800">{{ $email }}</p>
        </div>
    @endif
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">DOB</p>
        <p class="font-semibold text-gray-800">{{ $dob }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Age (as on {{ $currentDate }}):</p>
        <p class="font-semibold text-gray-800">{{ $age }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Father Name</p>
        <p class="font-semibold text-gray-800">{{ $ben_father_name }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Mother Name</p>
        <p class="font-semibold text-gray-800">{{ $ben_mother_name }}</p>
    </div>
    @if ($ben_spouse_name != null)
        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
            <p class="text-xs text-gray-500">Spouse Name</p>
            <p class="font-semibold text-gray-800">{{ $ben_spouse_name }}</p>
        </div>
    @endif
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Caste</p>
        <p class="font-semibold text-gray-800">{{ $caste }}</p>
    </div>
    @if ($caste_cer_no != null)
        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
            <p class="text-xs text-gray-500">SC/ST Certificate No.</p>
            <p class="font-semibold text-gray-800">{{ $caste_cer_no }}</p>
        </div>
    @endif
</div>
