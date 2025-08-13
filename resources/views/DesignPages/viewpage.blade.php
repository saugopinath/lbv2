<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-8 space-y-4">
        <div class="bg-blue-200 dark:bg-gray-800 shadow-md rounded p-2 space-y-2 text-center border border-blue-300">
            <h2 class="text-lg font-semibold">Application Name: Sanjoy</h2>
            <h2 class="text-lg font-semibold">Application Id: 123456</h2>
        </div>
        <!-- PERSONAL DETAILS -->
        <div x-data="{ open: true }" class="rounded overflow-hidden">
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
                    <path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM296 408L296 344L232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L296 296L296 232C296 218.7 306.7 208 320 208C333.3 208 344 218.7 344 232L344 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L344 344L344 408C344 421.3 333.3 432 320 432C306.7 432 296 421.3 296 408z" />
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-6 w- text-white">
                    <path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L232 344z" />
                </svg>
            </button>

            <!-- Content -->
            <div x-show="open"
                class="p-4 bg-green-50 shadow border-l-4 border-pink-500 space-x-2">
                <span class="max-h-fit w-1 bg-pink-500 rounded-full"></span>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Swasthya Sathi Card No.</p>
                        <p class="font-semibold text-gray-800">**********</p>
                    </div>

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Aadhaar No.</p>
                        <p class="font-semibold text-gray-800">
                            ****6875
                            <button class="ml-2 text-blue-600 text-xs underline">Show Original</button>
                        </p>
                    </div>

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Registration No.</p>
                        <p class="font-semibold text-gray-800">123</p>
                    </div>

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Samadhan Date</p>
                        <p class="font-semibold text-gray-800">11/07/2025</p>
                    </div>

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Mobile No.</p>
                        <p class="font-semibold text-gray-800">8348691761</p>
                    </div>

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="font-semibold text-gray-800">-</p>
                    </div>
                    
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">DOB</p>
                        <p class="font-semibold text-gray-800">12/02/2000</p>
                    </div>

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Age</p>
                        <p class="font-semibold text-gray-800">25</p>
                    </div>

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Father Name</p>
                        <p class="font-semibold text-gray-800">Test Roy</p>
                    </div>

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Mother Name</p>
                        <p class="font-semibold text-gray-800">Sumitra Roy</p>
                    </div>

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Spouse Name</p>
                        <p class="font-semibold text-gray-800">-</p>
                    </div>

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Caste</p>
                        <p class="font-semibold text-gray-800">SC</p>
                    </div>

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">SC/ST Certificate No.</p>
                        <p class="font-semibold text-gray-800">dhhhh344</p>
                    </div>

                </div>
            </div>
        </div>

        <!-- ADDRESS DETAILS -->
        <div x-data="{ open: false }" class="rounded overflow-hidden">
            <button @click="open = !open"
                class="w-full flex justify-between items-center text-left p-3 bg-gray-200 font-semibold">
                <div class="flex items-center space-x-3">
                    <span class="h-6 w-1 bg-indigo-500 rounded-full"></span>
                    <span>Address Details</span>
                </div>
                <!-- Plus/Minus Icon -->
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-6 w-6 text-white">
                    <path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM296 408L296 344L232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L296 296L296 232C296 218.7 306.7 208 320 208C333.3 208 344 218.7 344 232L344 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L344 344L344 408C344 421.3 333.3 432 320 432C306.7 432 296 421.3 296 408z" />
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-6 w- text-white">
                    <path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L232 344z" />
                </svg>
            </button>

            <div x-show="open" class="p-4 bg-green-50 shadow border-l-4 border-indigo-500 space-x-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Father Name</p>
                        <p class="font-semibold text-gray-800">Test Roy</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Father Name</p>
                        <p class="font-semibold text-gray-800">Test Roy</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Father Name</p>
                        <p class="font-semibold text-gray-800">Test Roy</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Father Name</p>
                        <p class="font-semibold text-gray-800">Test Roy</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- BANK DETAILS -->
        <div x-data="{ open: false }" class="rounded overflow-hidden">
            <button @click="open = !open"
                class="w-full flex justify-between items-center text-left p-3 bg-gray-200 font-semibold">
                <div class="flex items-center space-x-3">
                    <span class="h-6 w-1 bg-green-500 rounded-full"></span>
                    <span>Bank Details</span>
                </div>
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-6 w-6 text-white">
                    <path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM296 408L296 344L232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L296 296L296 232C296 218.7 306.7 208 320 208C333.3 208 344 218.7 344 232L344 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L344 344L344 408C344 421.3 333.3 432 320 432C306.7 432 296 421.3 296 408z" />
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-6 w- text-white">
                    <path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L232 344z" />
                </svg>
            </button>
            <div x-show="open" class="p-4 bg-green-50 shadow border-l-4 border-green-500 space-x-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Bank Name</p>
                        <p class="font-semibold text-gray-800">Test Bank</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Branch Name</p>
                        <p class="font-semibold text-gray-800">Test Branch</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Account Number</p>
                        <p class="font-semibold text-gray-800">1234567890</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">IFSC Code</p>
                        <p class="font-semibold text-gray-800">ABC1234567</p>
                    </div>

                </div>
            </div>
        </div>

        <div x-data="{ open: false }" class="rounded overflow-hidden">
            <button @click="open = !open"
                class="w-full flex justify-between items-center text-left p-3 bg-gray-200 font-semibold">
                <div class="flex items-center space-x-3">
                    <span class="h-6 w-1 bg-orange-500 rounded-full"></span>
                    <span>Encloser Details</span>
                </div>
                <!-- Plus/Minus Icon -->
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-6 w-6 text-white">
                    <path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM296 408L296 344L232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L296 296L296 232C296 218.7 306.7 208 320 208C333.3 208 344 218.7 344 232L344 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L344 344L344 408C344 421.3 333.3 432 320 432C306.7 432 296 421.3 296 408z" />
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-6 w- text-white">
                    <path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L232 344z" />
                </svg>
            </button>
            <div x-show="open" class="p-4 bg-green-50 shadow border-l-4 border-orange-500 space-x-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">IFSC Code</p>
                        <p class="font-semibold text-gray-800">ABC1234567</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">IFSC Code</p>
                        <p class="font-semibold text-gray-800">ABC1234567</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">IFSC Code</p>
                        <p class="font-semibold text-gray-800">ABC1234567</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition hover:scale-105 w-full max-w-xs">
                        <p class="text-xs text-gray-500 break-words">
                            IFSC Code
                        </p>
                        <p class="font-semibold text-gray-800 truncate">
                            ABC1234567
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>