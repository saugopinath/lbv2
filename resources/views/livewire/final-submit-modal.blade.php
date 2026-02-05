<div {{-- ✅ ROOT TAG (VERY IMPORTANT) --}}>

    <div
        x-data="{ open: @entangle('show') }"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 flex items-center justify-center z-50 bg-black/50 bg-opacity-50 p-4">
        <div
            @click.away="$wire.close()"
            class="bg-white w-full max-w-5xl rounded-lg shadow-lg overflow-hidden max-h-[90vh] flex flex-col">
            <div class="bg-green-100 px-6 py-4 text-center text-lg font-semibold text-green-800">
                Final Review Before Submit
            </div>
            <div class="p-6 space-y-6 text-sm text-gray-700 overflow-y-auto flex-1">
                <div class="flex justify-between items-center border-b pb-4 px-4">

                    <!-- Left Section -->
                    <div class="flex items-center gap-5">
                        <img src="https://c.animaapp.com/mdn4r47eB5hzlO/img/biswo-2.png"
                            alt="Logo"
                            class="w-20 h-20 object-contain">

                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">
                                Government of West Bengal
                            </h2>
                            <h3 class="text-lg text-blue-700">
                                {{ $schemeName . ' SCHEME' }}
                            </h3>

                        </div>
                    </div>

                    <!-- Right Section -->
                    <div class="flex items-center flex-shrink-0 pr-6">
                        <img
                            src="{{ $applicantPhoto }}"
                            alt="Applicant Photo"
                            class="w-24 h-24 object-cover rounded-md border-2 border-gray-300 shadow">
                    </div>
                </div>


                @if($applicationId)
                <p
                    class="px-4 py-1.5 mb-4 text-center rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 shadow-sm">
                    Application Id {{ $applicationId }}
                </p>
                @endif
                @foreach($tabsData as $tab)
                @if($tab['tab_code'] !== '104')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-2 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">{{ $tab['tab_name'] }}</h3>
                        </div>
                    </div>

                    <!-- Fields Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($tab['fields'] as $label => $value)
                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">{{ $label }}</div>
                            <div class="text-lg font-semibold text-gray-800 ml-2">
                                {{ ($value !== null && $value !== '' && $value !== '-') ? $value : 'Not Applicable' }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
                {{-- ================= DOCUMENT TAB (104) ================= --}}
                @php
                $documentTab = collect($tabsData)->firstWhere('tab_code', '104');
                @endphp

                @if($documentTab)

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-2 hover:shadow-md transition-shadow duration-300"">

                    <div class=" flex items-center mb-4">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $documentTab['tab_name'] ?? 'Enclosure Details' }}</h3>
                    </div>
                </div>

                <div class="rounded-lg p-3 bg-gray-50">
                    <livewire:enclosure-list
                        :application_id="$applicationId"
                        :scheme_id="$schemeId"
                        :tabCode="104"
                        :is_page="1"
                        wire:key="enclosure-preview-{{ $applicationId }}-104" />
                </div>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="flex justify-end p-6 border-t border-gray-200 space-x-4 bg-gray-50">
            <button
                wire:click="close"
                class="px-6 py-2.5 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium transition-colors">
                Cancel
            </button>
            <x-form.confirm-action
                action="confirmSubmit"
                title="Final Submit"
                message="Are you sure to submit this application?"
                confirmLabel="Yes, Submit">
                Final Submit
            </x-form.confirm-action>
        </div>

    </div>

</div>

</div>