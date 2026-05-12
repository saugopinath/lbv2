<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <h1 class="text-2xl font-bold text-indigo-800 dark:text-white px-2 py-2">
                Track Beneficiary Details
            </h1>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold 
             bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 
             border border-blue-200 dark:border-blue-800 shadow-sm
             flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                        clip-rule="evenodd" />
                </svg>
                {{ $schemename ?? 'N/A' }}
            </span>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold 
             bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-400 
             border border-gray-200 dark:border-gray-700 shadow-sm
             flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd" />
                </svg>
                Application ID: {{ $benPersonal->application_id }}
            </span>

        </div>
        <x-form.back-button :url="route('track-beneficiary-details')" />
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 mb-4 flex items-center justify-between">

        <div class="container mx-auto p-2">
            <div class="bg-white shadow-md rounded-xl border border-gray-200 p-3 md:p-6 mb-8">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 pl">
                    <!-- left side: name + ids -->
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-user text-indigo-600 text-[32px]"></i>
                            {{ $benPersonal->beneficiary_name }}
                        </h2>
                        <!-- --------------------------------------------------------------------- -->
                        <div
                            class="inline-flex items-center gap-2 bg-indigo-50/70 px-4 py-2 rounded-xl border border-indigo-100 ml-6">
                            <i class="fa-regular fa-id-card text-indigo-600 text-base"></i>
                            <span class="text-sm font-medium text-gray-700">Scheme Applied:</span>
                            <span
                                class="font-bold text-indigo-700 bg-white/60 px-2.5 py-0.5 rounded-lg text-sm">{{ $schemename }}</span>
                        </div>
                        <!-- IDs container: modern with icons and copy-friendly design -->
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-3 pt-1 ml-6">
                            <div
                                class="group flex items-center gap-2.5 bg-gray-50/80 px-4 py-4 rounded-xl border border-gray-100 hover:border-indigo-200 transition-all">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <i class="fa-regular fa-id-card text-indigo-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Beneficiary ID</p>
                                    <p class="font-mono font-semibold text-gray-800 text-sm md:text-base">
                                        {{ $benPersonal->beneficiary_id ?? '0000000000' }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="group flex items-center gap-2.5 bg-gray-50/80 px-4 py-2 rounded-xl border border-gray-100 hover:border-indigo-200 transition-all">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fa-regular fa-file-lines text-purple-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Application ID</p>
                                    <p class="font-mono font-semibold text-gray-800 text-sm md:text-base">
                                        {{ $benPersonal->application_id ?? '0000000000' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($benPayComment['comment'])
                        <div class="mt-4">
                            <span class="text-xs text-grey-500 uppercase tracking-wide">Comment : </span>
                            <span class="font-mono font-semibold text-{{ $benPayComment['color'] }}-800 text-sm md:text-base">
                                {{ $benPayComment['comment'] }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <div
                        class="bg-indigo-50/70 rounded-xl p-4 md:p-5 border border-indigo-100 w-full md:w-auto md:min-w-[360px]">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <div>
                                <p class="text-xs uppercase tracking-wider text-indigo-700 font-medium">🏦 Bank account
                                    Validation Status
                                </p>
                                <p
                                    class="text-[15px] font-semibold text-{{ $acc_validated['color'] }}-700 flex items-center gap-1.5 mt-1">
                                    <i class="{{ $acc_validated['icon'] }} text-{{ $acc_validated['color'] }}-500"></i>
                                    {{ $acc_validated['txt'] }}
                                </p>
                                @if($acc_validated['txt_name_1'])
                                <p
                                    class="text-[15px] font-semibold text-{{ $acc_validated['color'] }}-700 flex items-center gap-1.5 mt-1">
                                    <i class="{{ $acc_validated['icon'] }} text-{{ $acc_validated['color'] }}-500"></i>
                                    {{ $acc_validated['txt_name_1'] }}
                                </p>
                                @endif
                                @if($acc_validated['txt_name_2'])
                                <span
                                    class="text-[15px] font-semibold text-{{ $acc_validated['color'] }}-700 flex items-center gap-1.5 mt-1">
                                    Matching Score: {{ $acc_validated['txt_name_2'] }} %
                                </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="bg-{{ $ben_status_color === 'green' ? 'emerald' : ($ben_status_color === 'red' ? 'rose' : 'amber') }}-50 text-{{ $ben_status_color === 'green' ? 'emerald' : ($ben_status_color === 'red' ? 'rose' : 'amber') }}-700 text-sm font-semibold px-4 py-1.5 rounded-full whitespace-nowrap border border-{{ $ben_status_color === 'green' ? 'emerald' : ($ben_status_color === 'red' ? 'rose' : 'amber') }}-200 shadow-sm flex items-center gap-1.5">
                                    <i
                                        class="fa-solid fa-star text-{{ $ben_status_color === 'green' ? 'emerald' : ($ben_status_color === 'red' ? 'rose' : 'amber') }}-500 text-[12px]"></i>
                                    {{ $ben_status }}
                                </span>
                                @if($ben_status === 'Inactive')
                                <div class="relative flex items-center" x-data="{ tooltip: false }"
                                    @click.away="tooltip = false">
                                    <i class="fa-solid fa-circle-info text-rose-500 hover:text-rose-600 text-xl cursor-pointer transition-colors drop-shadow-sm"
                                        @mouseenter="tooltip = true" @mouseleave="tooltip = false"
                                        @click="tooltip = !tooltip"></i>

                                    <div x-show="tooltip" x-transition.opacity style="display: none;"
                                        class="absolute z-50 top-full right-0 mt-2.5 w-64 bg-white border border-rose-100 shadow-2xl rounded-xl p-3 text-sm text-gray-700">
                                        <!-- Arrow -->
                                        <div
                                            class="absolute -top-1.5 right-2 w-3 h-3 bg-white border-t border-l border-rose-100 transform rotate-45">
                                        </div>

                                        <div
                                            class="font-bold text-rose-700 mb-1.5 border-b border-rose-50 pb-1.5 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation"></i> Inactive Reason
                                        </div>
                                        <p class="text-[13px] leading-relaxed text-gray-600 font-medium">
                                            {{ $ben_status_reason }}
                                        </p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-6 text-[13px] border-t border-indigo-200/60 pt-4">

                            <!-- Left Column -->
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="text-gray-600 font-medium">Bank A/c No</div>
                                    <div class="font-mono text-gray-800 tracking-wider">{{ $encryptBankCode }}</div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div class="text-gray-600 font-medium">IFSC</div>
                                    <div class="font-mono text-gray-800">{{ $encryptIfsc }}</div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="text-gray-600 font-medium">Bank Name</div>
                                    <div class="font-mono text-gray-800">{{$bankName}}</div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div class="text-gray-600 font-medium">Bank Branch</div>
                                    <div class="font-mono text-gray-800">{{$bankBranch}}</div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <!-- Header -->
            <h2 class="text-xl font-extrabold text-indigo-700 text-center mb-6 mt-4 flex items-center justify-center gap-2">
                <i class="fa-solid fa-money-check-dollar"></i> Payment Status
            </h2>

            <!-- Accordion container -->
            <div class="border border-gray-200 rounded-xl shadow-sm bg-white overflow-hidden" x-data="{ open: true }">
                <!-- Accordion Header -->
                <button @click="open = !open"
                    class="w-full flex items-center justify-between p-4 md:px-6 bg-gray-50 hover:bg-gray-100 border-b border-gray-200 focus:outline-none transition-colors">
                    <div
                        class="text-[13px] font-bold tracking-wider text-gray-700 uppercase flex flex-wrap items-center gap-2 md:gap-4">
                        <span class="flex items-center gap-1 text-indigo-700"><i class="fa-solid fa-user"></i>
                            {{ $benPersonal->beneficiary_name }}</span>
                        <span class="hidden md:block text-gray-300">|</span>
                        <span class="flex items-center gap-1"><i class="fa-solid fa-id-card text-gray-400"></i> BEN ID:
                            {{ $benPersonal->beneficiary_id }}</span>
                        <span class="hidden md:block text-gray-300">|</span>
                        <span class="flex items-center gap-1"><i class="fa-solid fa-file-lines text-gray-400"></i> APP ID:
                            {{ $benPersonal->application_id }}</span>
                    </div>
                    <div class="bg-white p-1.5 rounded-full shadow-sm border border-gray-200 flex items-center justify-center transition-transform duration-300"
                        :class="open ? 'rotate-180' : ''">
                        <i class="fa-solid fa-chevron-down text-gray-500 text-sm"></i>
                    </div>
                </button>

                <!-- Accordion Body -->
                <div x-show="open" x-collapse class="p-6">
                    <!-- Wrapper Component rendering Dropdown, Status & Datatable -->
                    <livewire:frontend.track-ben.payment-status :ben_id="$benPersonal->beneficiary_id" :scheme_id="$scheme_id"
                        :ben_status="$ben_status" :bank_code="$encryptBankCode" :ifsc="$encryptIfsc" />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>