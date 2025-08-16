     {{--  <div x-data="{ open: false }" class="rounded overflow-hidden">
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
                        <p class="text-xs text-gray-500">District</p>
                        <p class="font-semibold text-gray-800">{{$distname}}</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Police Station</p>
                        <p class="font-semibold text-gray-800">{{$ps}}</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Block/Municipality/Corp</p>
                        <p class="font-semibold text-gray-800">{{$blockmunicorp}}</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">GP/Ward No.:</p>
                        <p class="font-semibold text-gray-800">{{$gpward}}</p>
                    </div>
                     <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Village/Town/City:</p>
                        <p class="font-semibold text-gray-800">{{$villtown}}</p>
                    </div>
                     <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">House/Premise No.:</p>
                        <p class="font-semibold text-gray-800">{{$houseno}}</p>
                    </div>
                      <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Post Office:</p>
                        <p class="font-semibold text-gray-800">{{$po}}</p>
                    </div>
                      <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                        <p class="text-xs text-gray-500">Pin Code:</p>
                        <p class="font-semibold text-gray-800">{{$pin}}</p>
                    </div>
                </div>
            </div>
        </div>
  --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">District</p>
        <p class="font-semibold text-gray-800">{{ $distname }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Police Station</p>
        <p class="font-semibold text-gray-800">{{ $ps }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Block/Municipality/Corp</p>
        <p class="font-semibold text-gray-800">{{ $blockmunicorp }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">GP/Ward No.:</p>
        <p class="font-semibold text-gray-800">{{ $gpward }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Village/Town/City:</p>
        <p class="font-semibold text-gray-800">{{ $villtown }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">House/Premise No.:</p>
        <p class="font-semibold text-gray-800">{{ $houseno }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Post Office:</p>
        <p class="font-semibold text-gray-800">{{ $po }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Pin Code:</p>
        <p class="font-semibold text-gray-800">{{ $pin }}</p>
    </div>
</div>
