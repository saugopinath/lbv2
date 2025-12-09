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
                Importing data from Jonmo Mrityu Tothyo portal (Next data fetch is scheduled for: )
            </h2>

            <button @click="open = !open" class="text-sm px-3 py-1 rounded-full bg-indigo-100 dark:bg-gray-700
                   text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200">
                <span x-show="open">Hide</span>
                <span x-show="!open">Show</span>
            </button>
        </div>
        <form x-data="jnmpImport()" @submit.prevent="submitForm" class="space-y-6">

            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block mb-1">From Date *</label>
                    <input type="date" name="from_date" x-model="form.from_date" required class="w-full border rounded">
                </div>

                <div>
                    <label class="block mb-1">To Date *</label>
                    <input type="date" name="to_date" x-model="form.to_date" required class="w-full border rounded">
                </div>

                <div>
                    <x-form.input id="index" name="index" label="Index" required x-model="form.index"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />
                </div>

                <div>
                    <x-form.input id="page_size" name="page_size" label="Page Size" required x-model="form.page_size"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />
                </div>
            </div>

            <div class="pt-4 flex justify-center">
                <x-button.primary class="px-10" type="submit">Import</x-button.primary>
            </div>

            <!-- ALERT POPUP -->
            <div x-show="showAlert" class="fixed inset-0 flex items-center justify-center bg-black/40" x-transition>

                <div class="bg-white rounded-lg shadow-xl p-6 w-96 text-center">
                    <h2 class="text-xl font-bold text-green-600" x-text="alert.title"></h2>

                    <p class="mt-2 text-gray-700">
                        Total <span x-text="alert.inserted"></span> out of
                        <span x-text="alert.total"></span> imported successfully.
                    </p>

                    <div class="mt-4 flex justify-center gap-4">
                        <button @click="finalSubmit" class="bg-blue-600 text-white px-4 py-2 rounded">
                            SEND RESPONSE
                        </button>

                        <button @click="showAlert = false" class="bg-gray-300 px-4 py-2 rounded">
                            CANCEL
                        </button>
                    </div>
                </div>
            </div>

        </form>


        {{-- <form x-show="open" x-transition action="{{ route('jnmp.pull') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        From Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="from_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                                  dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        To Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="to_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                                  dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <x-form.input id="index" name="index" label="Index" required
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10);" />
                </div>

                <div>
                    <x-form.input id="page_size" name="page_size" label="Page Size (No. of records) :" required
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10);" />
                </div>

            </div>

            <div class="pt-4 flex justify-center">
                <x-button.primary class="px-10" type="submit">Import Data</x-button.primary>
            </div>

        </form> --}}

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
            <div class="flex items-end gap-4">

                <div class="w-40">
                    <x-form.input id="limit" name="limit" label="Enter Limit:" required
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10);" />
                </div>

                <div>
                    <x-button.primary class="px-10 justify-center" type="submit">
                        Send
                    </x-button.primary>
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
                <x-button.danger class="px-10" type="submit">
                    Mark as Death to Lakshmir Bhandar Portal
                </x-button.danger>
            </div>
        </form>
    </div>
    <script>
        function jnmpImport() {
            return {
                showAlert: false,

                form: {
                    from_date: "",
                    to_date: "",
                    index: "",
                    page_size: ""
                },

                alert: {
                    title: "",
                    inserted: 0,
                    total: 0
                },

                async submitForm() {
                    let fd = new FormData();
                    fd.append('_token', '{{ csrf_token() }}');
                    fd.append('from_date', this.form.from_date);
                    fd.append('to_date', this.form.to_date);
                    fd.append('index', this.form.index);
                    fd.append('page_size', this.form.page_size);

                    let res = await fetch("{{ route('jnmp.pull') }}", {
                        method: "POST",
                        body: fd
                    });

                    let data = await res.json();

                    if (data.status === 200) {
                        this.alert = data;
                        this.showAlert = true;
                    }
                },

                finalSubmit() {
                    window.location.reload();
                }
            }
        }
    </script>

</x-layouts.app>