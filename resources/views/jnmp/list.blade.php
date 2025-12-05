<x-layouts.app>
    {{-- <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-4 mb-6">
        <h1 class="text-2xl font-bold text-center text-indigo-800 dark:text-white tracking-wide">
            {{ $header }}
        </h1>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <h2 class="text-xl font-bold mb-4">Importing data from Jonmo Mrityu Tothyo portal(Next data fetch is scheduled
            for: )</h2>
        <form action="{{ route('jnmp.pull') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <x-form.input type="date" name="from_date" label="From Date"
                        placeholder="Enter Duare Sakar Registration Number" required />
                </div>
                <div>
                    <x-form.input type="date" name="to_date" label="To Date"
                        placeholder="Enter Duare Sakar Registration Number" required />
                </div>

            </div>
            <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
                <div>
                    <x-form.input id="page_size" name="page_size" label="Page Size" required x-on:input="
        $el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10);
        ;
    " />
                </div>
                <div>
                    <x-form.input id="index" name="index" label="Index" required x-on:input="
        $el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10);
        ;
    " />
                </div>
            </div>
            <x-button.primary type="submit">
                Import Data
            </x-button.primary>
        </form>

    </div> --}}

    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-4 mb-6">
        <h1 class="text-2xl font-bold text-center text-indigo-800 dark:text-white tracking-wide">
            {{ $header }}
        </h1>
    </div>

    <div x-data="{ open: true }" class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 space-y-6">

        <!-- Title -->
        <div class="flex justify-between items-center border-b pb-3">
            <h2 class="text-xl font-semibold text-indigo-700 dark:text-indigo-300">
                Importing data from Jonmo Mrityu Tothyo portal (Next data fetch is scheduled for: )
            </h2>

            <button @click="open = !open" class="text-sm px-3 py-1 rounded-full bg-indigo-100 dark:bg-gray-700 
                   text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200">
                <span x-show="open">Hide</span>
                <span x-show="!open">Show</span>
            </button>
        </div>

        <!-- Form -->
        <form x-show="open" x-transition action="{{ route('jnmp.pull') }}" method="POST" class="space-y-6">
            @csrf

            <!-- All 4 fields in one row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- From Date -->
                <div>
                    <label for="from_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        From Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="from_date" name="from_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                              focus:ring-2 focus:ring-indigo-500 focus:border-transparent 
                              dark:bg-gray-700 dark:text-white">
                </div>

                <!-- To Date -->
                <div>
                    <label for="to_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        To Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="to_date" name="to_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                              focus:ring-2 focus:ring-indigo-500 focus:border-transparent 
                              dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Index -->


                <div>
                    <x-form.input id="index" name="index" label="Index" required x-on:input="
        $el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10);
        ;
    " />
                </div>
                <!-- Page Size -->
                <div>

                    <x-form.input id="page_size" name="page_size" label="Page Size (No. of records) : " required x-on:input="
        $el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10);
        ;
    " />
                </div>

            </div>

            <div class="pt-4 flex justify-center">
                <x-button.primary class="px-10" type="submit">
                    Import Data
                </x-button.primary>
            </div>

        </form>

    </div>

    <div x-data="{ open: true }" class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 space-y-6">

        <!-- Title -->
        <div class="flex justify-between items-center border-b pb-3">
            <h2 class="text-xl font-semibold text-indigo-700 dark:text-indigo-300">
                Calling Back data to Jonmo Mrityu Tothyo portal
            </h2>

            <button @click="open = !open" class="text-sm px-3 py-1 rounded-full bg-indigo-100 dark:bg-gray-700 
                   text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200">
                <span x-show="open">Hide</span>
                <span x-show="!open">Show</span>
            </button>
        </div>

        <!-- Content -->
        <form x-show="open" x-transition action="{{ route('jnmp.details-callback') }}" method="POST" class="space-y-4">

            @csrf

            <div class="flex flex-wrap justify-between text-lg font-semibold text-gray-700 dark:text-gray-300 py-2">

                <div class="flex items-center gap-2">
                    <span>Data Captured :</span>
                    <span class="text-indigo-700 dark:text-indigo-300">2213755</span>
                </div>

                <div class="flex items-center gap-2">
                    <span>Data CallBack Done :</span>
                    <span class="text-indigo-700 dark:text-indigo-300">2213755</span>
                </div>

                <div class="flex items-center gap-2">
                    <span>Data Callback Pending :</span>
                    <span class="text-indigo-700 dark:text-indigo-300">0</span>
                </div>

            </div>


            <!-- Limit Input (Left Side + Small Width) -->
            <div class="flex justify-start">
                <div class="w-40">
                    <x-form.input id="limit" name="limit" label="Enter Limit:" required
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10);" />
                </div>
            </div>

            <!-- Centered Button -->
            <div class="pt-4 flex justify-center">
                <x-button.primary class="px-10" type="submit">
                    Send
                </x-button.primary>
            </div>

        </form>

    </div>

      <div x-data="{ open: true }" class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 space-y-6">

        <!-- Title -->
        <div class="flex justify-between items-center border-b pb-3">
            <h2 class="text-xl font-semibold text-indigo-700 dark:text-indigo-300">
                Marking Beneficiaries as Death case to Lakshmir Bhandar Portal(As per the data came from Jonmo Mrityu Tothyo Portal)
            </h2>

            <button @click="open = !open" class="text-sm px-3 py-1 rounded-full bg-indigo-100 dark:bg-gray-700 
                   text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200">
                <span x-show="open">Hide</span>
                <span x-show="!open">Show</span>
            </button>
        </div>

        <!-- Content -->
        <form x-show="open" x-transition action="{{ route('jnmp.details-callback') }}" method="POST" class="space-y-4">

            @csrf

            <div class="flex flex-wrap justify-between text-lg font-semibold text-gray-700 dark:text-gray-300 py-2">

                <div class="flex items-center gap-2">
                    <span>Total Beneficiary Marked as Death :</span>
                    <span class="text-indigo-700 dark:text-indigo-300">165839</span>
                </div>

                <div class="flex items-center gap-2">
                    <span>Current Beneficiary Marked as Death :</span>
                    <span class="text-indigo-700 dark:text-indigo-300">0</span>
                </div>

                <div class="flex items-center gap-2">
                    <span>Re-activate Death Incident :</span>
                    <span class="text-indigo-700 dark:text-indigo-300">165839</span>
                </div>

            </div>

            <!-- Centered Button -->
            <div class="pt-4 flex justify-center">
                <x-button.primary class="px-10" type="submit">
                    Mark as Death to Lakshmir Bhandar Portal                                    
                </x-button.primary>
            </div>

        </form>

    </div>



    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-2">

    <div class="flex justify-between">
        <span>Total JNMP Records:</span>
        <span class="text-indigo-700">{{ $totalJnmp }}</span>
    </div>

    <div class="flex justify-between">
        <span>Remaining for Callback:</span>
        <span class="text-indigo-700">{{ $remainingJnmp }}</span>
    </div>

    <div class="flex justify-between">
        <span>Callback Completed:</span>
        <span class="text-indigo-700">{{ $updatedJnmp }}</span>
    </div>

    <hr>

    <div class="flex justify-between">
        <span>Total Marked as Death:</span>
        <span class="text-indigo-700">{{ $data1 }}</span>
    </div>

    <div class="flex justify-between">
        <span>Current Marked as Death:</span>
        <span class="text-indigo-700">{{ $data2 }}</span>
    </div>

    <div class="flex justify-between">
        <span>Re-activated Cases:</span>
        <span class="text-indigo-700">{{ $data3 }}</span>
    </div>

</div>

</x-layouts.app>