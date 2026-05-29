<form wire:submit.prevent="showConfirmation" class="w-full my-4 bg-white border-2 rounded-lg shadow-xl overflow-hidden"
    style="border-color: #b45309;">
    {{-- Custom Theme Color Overrides for Government brand style --}}
    <style>
        .active-tab {
            background-color: #b45309 !important;
            color: #ffffff !important;
            border-color: #b45309 !important;
        }

        .inactive-tab {
            background-color: #fff7ed !important;
            color: #b45309 !important;
            border-color: #fed7aa !important;
        }

        .active-sidebar {
            background-color: #b45309 !important;
            color: #ffffff !important;
        }

        .active-sidebar-badge {
            background-color: #78350f !important;
            color: #ffffff !important;
        }

        .inactive-sidebar-badge {
            background-color: #f3f4f6 !important;
            color: #6b7280 !important;
        }

        .inactive-sidebar {
            color: #78350f !important;
        }

        .inactive-sidebar:hover {
            background-color: #ffedd5 !important;
        }

        .form-container-flex {
            display: flex;
            flex-direction: row;
            gap: 24px;
            padding: 24px;
            align-items: flex-start;
        }

        .form-sidebar-left {
            width: 280px;
            flex-shrink: 0;
        }

        .form-content-right {
            flex-grow: 1;
            min-width: 0;
        }

        @media (max-width: 1024px) {
            .form-container-flex {
                flex-direction: column;
                gap: 16px;
                padding: 16px;
            }

            .form-sidebar-left {
                width: 100%;
            }

            .form-content-right {
                width: 100%;
            }
        }

        .border-indigo-900 {
            border-color: #b45309 !important;
        }

        .text-indigo-950 {
            color: #78350f !important;
        }

        .bg-indigo-50 {
            background-color: #fff7ed !important;
        }

        .border-indigo-200 {
            border-color: #ffedd5 !important;
        }

        .text-indigo-900 {
            color: #b45309 !important;
        }

        input[type="checkbox"]:checked {
            background-color: #b45309 !important;
            border-color: #b45309 !important;
        }

        input[type="text"],
        select,
        input[type="number"],
        input[type="date"] {
            border-color: #fed7aa !important;
            background-color: #ffffff !important;
            transition: all 0.15s ease-in-out;
        }

        input[type="text"]:focus,
        select:focus,
        input[type="number"]:focus,
        input[type="date"]:focus {
            border-color: #b45309 !important;
            --tw-ring-color: #b45309 !important;
            background-color: #fffdfa !important;
            outline: 2px solid transparent !important;
            outline-offset: 2px !important;
        }

        /* Custom styles to replace cold grays with warm amber theme colors */
        .bg-gray-50 {
            background-color: #fffcf9 !important;
        }

        .border-gray-200 {
            border-color: #fed7aa !important;
        }

        .border-gray-300 {
            border-color: #fdba74 !important;
        }

        .bg-white.border.border-gray-200 {
            border-color: #fed7aa !important;
        }

        /* Custom overrides to fix indigo/navy circles and outline colors */
        span[style*="background-color: #1e1b4b"] {
            background-color: #78350f !important;
        }

        button:focus,
        button:active,
        input:focus,
        select:focus {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>

    {{-- Government Style Header --}}
    <div class="p-6 border-b-4 border-amber-500" style="background-color: #9a3412; color: #ffffff;">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-inner">
                    <span class="font-bold text-3xl font-serif" style="color: #9a3412;">AY</span>
                </div>
                <div>
                    <h2 class="text-xs md:text-sm font-semibold uppercase tracking-wider" style="color: #fed7aa;">
                        Government of West Bengal</h2>
                    <h1 class="text-xl md:text-2xl font-bold font-serif text-amber-400">ANNAPURNA YOJANA</h1>
                </div>
            </div>
            <div class="text-center md:text-right">
                <span class="font-bold text-xs uppercase px-3 py-1 rounded shadow"
                    style="background-color: #f59e0b; color: #78350f;">
                    Family Level Data Collection Form
                </span>
                <p class="text-xs mt-2" style="color: #ffedd5;">পারিবারিক স্তরের তথ্য সংগ্রহপত্র</p>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    <div class="p-6 pb-0">
        @if ($successMessage)
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ $successMessage }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errorMessage)
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ $errorMessage }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('member_limit'))
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded shadow-sm mb-4">
                <p class="text-sm font-medium text-amber-800">{{ session('member_limit') }}</p>
            </div>
        @endif
    </div>

    {{-- Main Layout Flexbox --}}
    <div class="form-container-flex">

        {{-- Left Sidebar: Vertical Navigation Menu --}}
        @include('livewire.annapurna.sidebar')

        {{-- Right Section: Tabs and Contents --}}
        <div class="form-content-right space-y-6">

            {{-- Horizontal Member Navigation Tabs --}}
            @include('livewire.annapurna.member-tabs')

            {{-- Form Active Section Contents Container --}}
            <div class="bg-white border border-gray-200 rounded-b-lg rounded-tr-lg p-6 shadow-sm min-h-[400px]">

                @if ($activeSection === 'family_identity')
                    @include('livewire.annapurna.section-a')
                @endif

                @if ($activeSection === 'ration_subsidy')
                    @include('livewire.annapurna.section-b')
                @endif

                @if ($activeSection === 'assets')
                    @include('livewire.annapurna.section-c')
                @endif

                @if ($activeSection === 'income_profession')
                    @include('livewire.annapurna.section-d')
                @endif

                @if ($activeSection === 'other_docs')
                    @include('livewire.annapurna.section-e')
                @endif

                @if ($activeSection === 'social_dependents')
                    @include('livewire.annapurna.section-f')
                @endif

                @if ($activeSection === 'gov_benefits')
                    @include('livewire.annapurna.section-g')
                @endif


                @if ($activeSection === 'declaration')
                    @include('livewire.annapurna.section-h')
                @endif

            </div>

            {{-- Bottom Navigation Control Bar --}}
            <div class="flex justify-between items-center pt-4 border-t border-gray-200 mt-6">

                {{-- Back button --}}
                <div>
                    @if ($activeSection !== 'family_identity')
                        <button type="button" wire:click="previousSection"
                            class="hover:bg-gray-300 text-gray-800 font-bold px-6 py-2.5 rounded shadow transition text-sm flex items-center gap-1 uppercase tracking-wider bg-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Back / পিছনে
                        </button>
                    @endif
                </div>

                {{-- Add Member button at the bottom (shows when current member is fully filled) --}}
                <div>
                    @if ($this->isMemberFullyFilled($activeMemberIndex) && $activeSection !== 'declaration')
                        <button type="button" wire:click="addMember"
                            class="hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded shadow transition text-sm flex items-center gap-1.5 uppercase tracking-wider bg-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Member / সদস্য যোগ করুন
                        </button>
                    @endif
                </div>

                {{-- Next / Submit buttons --}}
                @php
                    $sectionsKeys = array_keys($this->getSections());
                    $inputSections = array_filter($sectionsKeys, function ($s) {
                        return $s !== 'declaration';
                    });
                    $lastInputSection = end($inputSections);
                    $isLastInputSection = $activeSection === $lastInputSection;
                @endphp
                <div>
                    @if ($activeSection === 'declaration')
                        <button type="submit"
                            class="hover:bg-opacity-90 text-white font-bold px-8 py-3 rounded-lg shadow-md hover:shadow-lg transition flex items-center gap-2 text-sm uppercase tracking-wider bg-amber-700"
                            style="background-color: #b45309;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Submit Application / আবেদন জমা দিন
                        </button>
                    @elseif ($activeMemberIndex > 0)
                        {{-- Member tab flow --}}
                        @if ($isLastInputSection)
                            @if ($activeMemberIndex < count($members))
                                {{-- Next member tab --}}
                                <button type="button"
                                    wire:click="selectMember({{ $activeMemberIndex + 1 }}); selectSection('family_identity')"
                                    class="hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded shadow transition text-sm flex items-center gap-1 uppercase tracking-wider bg-emerald-600">
                                    Next Member / পরবর্তী সদস্য
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @else
                                {{-- Last member: guide to common Declaration tab --}}
                                <button type="button" wire:click="selectSection('declaration')"
                                    class="hover:bg-amber-800 text-white font-bold px-6 py-2.5 rounded shadow transition text-sm flex items-center gap-1 uppercase tracking-wider bg-amber-700">
                                    Go to Declaration / ঘোষণা ও সম্মতি
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif
                        @else
                            {{-- Normal next section inside member tab --}}
                            <button type="button" wire:click="nextSection"
                                class="hover:bg-amber-800 text-white font-bold px-6 py-2.5 rounded shadow transition text-sm flex items-center gap-1 uppercase tracking-wider bg-amber-700">
                                Next / এগিয়ে চলুন
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @endif
                    @else
                        {{-- HOF tab flow --}}
                        <button type="button" wire:click="nextSection"
                            class="hover:bg-amber-800 text-white font-bold px-6 py-2.5 rounded shadow transition text-sm flex items-center gap-1 uppercase tracking-wider bg-amber-700">
                            Next / এগিয়ে চলুন
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @endif
                </div>

            </div>

        </div>

    </div>

    {{-- Confirmation Modal --}}
    @if ($showSubmitModal)
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity backdrop-blur-sm"
            style="background-color: rgba(0,0,0,0.55); z-index: 40;" wire:click="closeSubmitModal"></div>

        <!-- Modal Wrapper -->
        <div class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto" style="z-index: 50;">
            <!-- Modal Panel -->
            <div
                class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all max-w-lg w-full border-2 border-amber-600">
                <div class="bg-amber-700 px-4 py-3 sm:px-6 flex items-center justify-between">
                    <h3 class="text-lg font-bold leading-6 text-white flex items-center gap-2" id="modal-title">
                        <svg class="w-6 h-6 text-amber-200" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Confirm Submission | নিশ্চিতকরণ
                    </h3>
                    <button type="button" wire:click="closeSubmitModal"
                        class="text-white hover:text-amber-200 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <p class="text-sm text-gray-700 font-semibold mb-3">
                                Are you sure you want to submit this Annapurna Yojana Application?
                            </p>
                            <p class="text-xs text-gray-500 leading-relaxed mb-4">
                                আপনি কি নিশ্চিত যে আপনি এই অন্নপূর্ণা যোজনা আবেদনপত্রটি জমা দিতে চান? একবার জমা দিলে আর
                                কোনো পরিবর্তন করা যাবে না।
                            </p>

                            <div
                                class="bg-amber-50 border border-amber-200 rounded p-3 text-xs text-amber-900 space-y-1.5">
                                <div><strong>Head of Family:</strong> {{ $formData['hof_name'] ?? 'N/A' }}</div>
                                <div><strong>Contact Number:</strong> {{ $formData['contact_no'] ?? 'N/A' }}</div>
                                <div><strong>Total Family Members:</strong> {{ count($members) + 1 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-amber-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" wire:click="save"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                        Yes, Submit / হ্যাঁ, জমা দিন
                    </button>
                    <button type="button" wire:click="closeSubmitModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition duration-150">
                        Cancel / বাতিল করুন
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Global Upload Modal for the entire form --}}
    @include('livewire.annapurna.global-upload-modal')

</form>
