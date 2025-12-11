<x-layouts.app>

    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-4 mb-6">
        <h1 class="text-2xl font-bold text-center text-indigo-800 dark:text-white tracking-wide">
            {{ $header }}
        </h1>
    </div>

    <!-- Import Section -->
    <div x-data="{ open: true }" class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 mb-6">

        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h2 class="text-xl font-semibold text-indigo-700 dark:text-indigo-300">
                Importing data from Janma-Mrityu Thathya portal (Next data fetch is scheduled for: {{ $lastFetch }})
            </h2>

            <button @click="open = !open" class="text-sm px-3 py-1 rounded-full bg-indigo-100 dark:bg-gray-700
                   text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200">
                <span x-show="open">Hide</span>
                <span x-show="!open">Show</span>
            </button>
        </div>

        <form x-data x-init="
    flatpickr($refs.fromDate, { dateFormat: 'd/m/Y' });
    flatpickr($refs.toDate, { dateFormat: 'd/m/Y' });
" x-show="open" x-transition action="{{ route('jnmp.pull') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div>
                    <x-form.input type="text" name="from_date" label="From Date" required x-ref="fromDate"
                        placeholder="dd/mm/yyyy" />
                </div>

                <div x-data x-init="
    flatpickr($refs.toDate, {
        dateFormat: 'd/m/Y',
        maxDate: 'today',
        disableMobile: true
    });
">
                    <x-form.input type="text" name="to_date" label="To Date" required x-ref="toDate"
                        placeholder="dd/mm/yyyy" />
                </div>
                <div>
                    <x-form.input id="page_size" name="page_size" label="Page Size (No. of records) :"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10);" />
                </div>
                <div class="mt-6 p-1.5">

                    <x-button.loading-button type="submit" text="Import" x-data x-on:click.prevent="
                    Livewire.dispatch('showLoader');
                    $el.form.submit();
                " />
                </div>
            </div>

        </form>

    </div>

    <!-- Callback Section -->
    <div x-data="{ open: true }" class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 mb-6">

        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h2 class="text-xl font-semibold text-indigo-700 dark:text-indigo-300">
                Calling Back data to Jonmo Mrityu Tothyo portal
            </h2>

            <button @click="open = !open" class="text-sm px-3 py-1 rounded-full bg-indigo-100 dark:bg-gray-700
                   text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200">
                <span x-show="open">Hide</span>
                <span x-show="!open">Show</span>
            </button>
        </div>

        <div x-data="{
                totalJnmp: 0,
                remainingJnmp: 0,
                updatedJnmp: 0,
                loadData() {
                    fetch('/jnmp-stats')
                        .then(r => r.json())
                        .then(d => {
                            this.totalJnmp = d.totalJnmp ?? 0;
                            this.remainingJnmp = d.remainingJnmp ?? 0;
                            this.updatedJnmp = d.updatedJnmp ?? 0;
                        });
                }
            }" x-init="loadData()" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 mb-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-700" x-text="totalJnmp"></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Data Captured</div>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600" x-text="updatedJnmp"></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Data CallBack Done</div>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600" x-text="remainingJnmp"></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Data Callback Pending</div>
                </div>
            </div>

        </div>

        <form x-show="open" action="{{ route('jnmp.details-callback') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4 items-start">
                <div>
                    <div class="relative">
                        <x-form.input id="limit" name="limit" label="Enter Limit:"
                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10);" />
                        <!-- Error message positioned absolutely to prevent layout shift -->
                    </div>
                </div>

                <div class="mt-6 p-1.5">
                    <x-button.loading-button type="submit" text="Send" class="h-full" x-data x-on:click.prevent="
                    Livewire.dispatch('showLoader');
                    $el.form.submit();
                " />
                </div>
            </div>

        </form>

    </div>

    <!-- Death Marking Section -->
    <div x-data="{ open: true }" class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">

        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h2 class="text-xl font-semibold text-indigo-700 dark:text-indigo-300">
                Marking Beneficiaries as Death case to Lakshmir Bhandar Portal
            </h2>

            <button @click="open = !open" class="text-sm px-3 py-1 rounded-full bg-indigo-100 dark:bg-gray-700
                   text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200">
                <span x-show="open">Hide</span>
                <span x-show="!open">Show</span>
            </button>
        </div>

        <div x-data="{
                data1: 0,
                data2: 0,
                data3: 0,
                loadData() {
                    fetch('/jnmp-stats')
                        .then(r => r.json())
                        .then(d => {
                            this.data1 = d.data1 ?? 0;
                            this.data2 = d.data2 ?? 0;
                            this.data3 = d.data3 ?? 0;
                        });
                }
            }" x-init="loadData()" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 mb-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-800 dark:text-white" x-text="data1"></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Beneficiary Marked as Death</div>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600" x-text="data2"></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Current Beneficiary Marked as Death</div>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600" x-text="data3"></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Re-activate Death Incident</div>
                </div>
            </div>

        </div>

        <form x-show="open" action="{{ route('jnmp.mark-as-death') }}" method="POST">
            @csrf
            <div class="pt-4">

                <x-button.loading-button type="submit" text="Mark as Death to Lakshmir Bhandar Portal" x-data
                    x-on:click.prevent="
                    Livewire.dispatch('showLoader');
                    $el.form.submit();
                " />
            </div>
        </form>
    </div>

</x-layouts.app>
