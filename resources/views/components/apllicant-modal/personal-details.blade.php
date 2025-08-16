{{--  <div x-data="{ open: true }" class="rounded overflow-hidden">
    <!-- Header -->
    <button @click="open = !open"
        class="w-full flex justify-between items-center text-left p-3 bg-gray-200 font-semibold">

        <!-- Vertical Accent Line -->
        <div class="flex items-center space-x-3">
            <span class="h-6 w-1 bg-pink-500 rounded-full"></span>
            <span>Personal Details</span>
        </div>
        <!-- Icon -->
        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-6 w-6 text-white">
            <path
                d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM296 408L296 344L232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L296 296L296 232C296 218.7 306.7 208 320 208C333.3 208 344 218.7 344 232L344 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L344 344L344 408C344 421.3 333.3 432 320 432C306.7 432 296 421.3 296 408z" />
        </svg>
        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-6 w- text-white">
            <path
                d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L232 344z" />
        </svg>
    </button>

    <!-- Content -->
    <div x-show="open" class="p-4 bg-green-50 shadow border-l-4 border-pink-500 space-x-2">
        <span class="max-h-fit w-1 bg-pink-500 rounded-full"></span>
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


            @if ($dsregno != null)
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Duare Sarkar Registration No.:</p>
                    <p class="font-semibold text-gray-800">{{ $dsregno }}</p>
                </div>
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Duare Sarkar Date.:</p>
                    <p class="font-semibold text-gray-800">{{ $dsdate }}</p>
                </div>
            @endif

            <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                <p class="text-xs text-gray-500">Mobile No.</p>
                <p class="font-semibold text-gray-800"> {{ $mobile }}</p>
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
                <p class="font-semibold text-gray-800">{{ $ffname }}</p>
            </div>

            <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                <p class="text-xs text-gray-500">Mother Name</p>
                <p class="font-semibold text-gray-800">{{ $mfname }}</p>
            </div>
            @if ($sfname != null)
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">Spouse Name</p>
                    <p class="font-semibold text-gray-800">{{ $sfname }}</p>
                </div>
            @endif
            <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                <p class="text-xs text-gray-500">Caste</p>
                <p class="font-semibold text-gray-800">{{ $caste }}</p>
            </div>

            @if ($cascerno != null)
                <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                    <p class="text-xs text-gray-500">SC/ST Certificate No.</p>
                    <p class="font-semibold text-gray-800">{{ $cascerno }}</p>
                </div>
            @endif
        </div>
    </div>
</div>  --}}
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

    @if ($dsregno != null)
        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
            <p class="text-xs text-gray-500">Duare Sarkar Registration No.:</p>
            <p class="font-semibold text-gray-800">{{ $dsregno }}</p>
        </div>
        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
            <p class="text-xs text-gray-500">Duare Sarkar Date.:</p>
            <p class="font-semibold text-gray-800">{{ $dsdate }}</p>
        </div>
    @endif

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
        <p class="font-semibold text-gray-800">{{ $ffname }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Mother Name</p>
        <p class="font-semibold text-gray-800">{{ $mfname }}</p>
    </div>
    @if ($sfname != null)
        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
            <p class="text-xs text-gray-500">Spouse Name</p>
            <p class="font-semibold text-gray-800">{{ $sfname }}</p>
        </div>
    @endif
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Caste</p>
        <p class="font-semibold text-gray-800">{{ $caste }}</p>
    </div>
    @if ($cascerno != null)
        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
            <p class="text-xs text-gray-500">SC/ST Certificate No.</p>
            <p class="font-semibold text-gray-800">{{ $cascerno }}</p>
        </div>
    @endif
</div>
