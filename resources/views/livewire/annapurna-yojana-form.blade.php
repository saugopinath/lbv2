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
        <div class="form-sidebar-left space-y-2">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-orange-50 border-b border-orange-100">
                    <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wider">Form Sections</h4>
                    <p class="text-[10px] text-amber-600">আবেদনপত্রের বিভাগসমূহ</p>
                </div>
                <nav class="p-2 space-y-1">
                    @foreach ($this->getSections() as $secKey => $secVal)
                        @php
                            $isActive = $activeSection === $secKey;
                            $isHofOnly = false;
                            $isMember = $activeMemberIndex > 0;
                        @endphp
                        <button type="button" wire:click="selectSection('{{ $secKey }}')"
                            class="w-full text-left px-3 py-2.5 rounded-md flex items-center gap-3 transition-all duration-150 {{ $isActive ? 'active-sidebar shadow-sm' : 'inactive-sidebar' }}">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $isActive ? 'active-sidebar-badge' : 'inactive-sidebar-badge' }}">
                                @if ($secKey === 'family_identity')
                                    A
                                @elseif ($secKey === 'ration_subsidy')
                                    B
                                @elseif ($secKey === 'assets')
                                    C
                                @elseif ($secKey === 'income_profession')
                                    D
                                @elseif ($secKey === 'other_docs')
                                    E
                                @elseif ($secKey === 'social_dependents')
                                    F
                                @elseif ($secKey === 'gov_benefits')
                                    G
                                @elseif ($secKey === 'declaration')
                                    H
                                @endif
                            </div>
                            <div>
                                <div class="text-xs md:text-sm leading-tight font-bold">{{ $secVal['label'] }}</div>
                                <div class="text-[10px] opacity-80 leading-none mt-0.5">{{ $secVal['bengali'] }}</div>
                            </div>
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- Instructions Panel --}}
            <div
                class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-xs text-amber-900 leading-relaxed shadow-sm">
                <span class="font-bold flex items-center gap-1.5 mb-1.5 text-amber-950">
                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Key Instructions
                </span>
                <ul class="list-disc pl-4 space-y-1">
                    <li>Name must match official Aadhaar.</li>
                    <li>Address is common for all family members.</li>
                    <li>Each member applying for AY must declare bank details.</li>
                </ul>
            </div>
        </div>

        {{-- Right Section: Tabs and Contents --}}
        <div class="form-content-right space-y-6">

            {{-- Horizontal Member Navigation Tabs --}}
            <div class="flex flex-wrap items-stretch border-b-2 border-amber-600 gap-1 pb-0">
                <!-- HOF Tab -->
                <button type="button" wire:click="selectMember(0)"
                    class="px-4 py-2.5 rounded-t-lg font-bold text-xs md:text-sm transition-all duration-150 flex items-center gap-2 border-t border-x {{ $activeMemberIndex === 0 ? 'active-tab shadow-inner' : 'inactive-tab hover:bg-orange-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <div class="text-left">
                        <div class="leading-none">Head of Family (HoF)</div>
                        <div class="text-[9px] opacity-80 font-normal mt-0.5">পরিবার প্রধান</div>
                    </div>
                </button>

                <!-- Member Tabs -->
                @foreach ($members as $index => $member)
                    @php
                        $memberTabNo = $index + 2;
                        $memberName = trim($member['name']) !== '' ? $member['name'] : "Member {$memberTabNo}";
                        $isActive = $activeMemberIndex === $index + 1;
                    @endphp
                    <div class="relative flex items-stretch" wire:key="member-tab-{{ $index }}">
                        <button type="button" wire:click="selectMember({{ $index + 1 }})"
                            class="pl-4 pr-8 py-2.5 rounded-t-lg font-bold text-xs md:text-sm transition-all duration-150 flex items-center gap-2 border-t border-x {{ $isActive ? 'active-tab shadow-inner' : 'inactive-tab hover:bg-orange-100' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div class="text-left">
                                <div class="leading-none truncate max-w-[120px]">{{ $memberName }}</div>
                                <div class="text-[9px] opacity-80 font-normal mt-0.5">সদস্য {{ $memberTabNo }}</div>
                            </div>
                        </button>
                        <button type="button" wire:click="removeMember({{ $index }})"
                            class="absolute right-1.5 top-1/2 -translate-y-1/2 p-1 rounded-full {{ $isActive ? 'text-white hover:bg-amber-800' : 'text-red-500 hover:bg-red-50 hover:text-red-700' }} transition"
                            title="Remove Member">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach

                <!-- Add Member Tab Button -->
                @if ($this->isMemberFullyFilled($activeMemberIndex))
                    <button type="button" wire:click="addMember"
                        class="px-4 py-2 rounded-t-lg bg-emerald-600 text-white hover:bg-emerald-700 font-bold text-xs transition duration-150 flex items-center gap-1.5 self-center ml-2 border border-emerald-600 shadow shadow-emerald-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Add Member / সদস্য যোগ করুন</span>
                    </button>
                @endif
            </div>

            {{-- Form Active Section Contents Container --}}
            <div class="bg-white border border-gray-200 rounded-b-lg rounded-tr-lg p-6 shadow-sm min-h-[400px]">

                {{-- SECTION 1: BASIC INFO --}}
                {{-- SECTION A: FAMILY IDENTITY --}}
                @if ($activeSection === 'family_identity')
                    <div class="space-y-6">
                        @if ($activeMemberIndex === 0)
                            {{-- HOF Basic Identity --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">A1</span>
                                        Family Head Identity | পরিবার প্রধানের পরিচয়
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Name of Head of
                                            Family (HOF) * <br><span class="text-xs text-gray-500 font-normal">পরিবার
                                                প্রধানের নাম</span></label>
                                        <div x-data="{
                                            val: @entangle('formData.hof_name'),
                                            error: '',
                                            valid: false,
                                            check() {
                                                if (!this.val) {
                                                    this.valid = false;
                                                    this.error = '';
                                                    return;
                                                }
                                                let cleaned = window.cleanInput.lettersOnly(this.val);
                                                if (this.val !== cleaned) { this.val = cleaned; }
                                                this.valid = window.checkValid.name(this.val);
                                                this.error = this.val.length > 0 && !this.valid ? 'Letters/dots/spaces only' : '';
                                            }
                                        }" x-init="check();
                                        $watch('val', () => check())">
                                            <input type="text" x-model="val"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                                    Valid</span>
                                                <span x-show="error" x-text="error"
                                                    class="text-red-500 text-xs font-semibold"></span>
                                            </div>
                                        </div>
                                        @error('formData.hof_name')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Birth of
                                            HOF * <br><span class="text-xs text-gray-500 font-normal">জন্ম
                                                তারিখ</span></label>
                                        <input type="date" wire:model="formData.hof_dob"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('formData.hof_dob')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Gender of HOF *
                                            <br><span class="text-xs text-gray-500 font-normal">লিঙ্গ</span></label>
                                        <select wire:model="formData.hof_gender"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            @foreach ($genders as $gender)
                                                <option value="{{ $gender }}">
                                                    {{ $gender }}
                                                    @if ($gender === 'Male')
                                                        / পুরুষ
                                                    @elseif($gender === 'Female')
                                                        / মহিলা
                                                    @elseif($gender === 'Other')
                                                        / অন্যান্য
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('formData.hof_gender')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Applying for
                                            Annapurna Yojana? <br><span
                                                class="text-xs text-gray-500 font-normal">অন্নপূর্ণা যোজনার জন্য আবেদন
                                                করছেন?</span></label>
                                        <select wire:model="formData.hof_applying_for_ay"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                            <option value="No">No / না</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Aadhaar of HOF *
                                            <br><span class="text-xs text-gray-500 font-normal">আধার
                                                নম্বর</span></label>
                                        <div x-data="{
                                            val: @entangle('formData.hof_aadhaar'),
                                            error: '',
                                            valid: false,
                                            check() {
                                                if (!this.val) {
                                                    this.valid = false;
                                                    this.error = '';
                                                    return;
                                                }
                                                let cleaned = window.cleanInput.numeric(this.val, 12);
                                                if (this.val !== cleaned) { this.val = cleaned; }
                                                this.valid = window.checkValid.aadhaar(this.val);
                                                this.error = this.val.length > 0 && !this.valid ? 'Invalid checksum' : '';
                                            }
                                        }" x-init="check();
                                        $watch('val', () => check())">
                                            <input type="text" x-model="val" maxlength="12"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                                    Valid Aadhaar</span>
                                                <span x-show="error" x-text="error"
                                                    class="text-red-500 text-xs font-semibold"></span>
                                            </div>
                                        </div>
                                        @error('formData.hof_aadhaar')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Household ID of
                                            Digital Ration Card, if any <br><span
                                                class="text-xs text-gray-500 font-normal">রেশন কার্ডের গৃহস্থালি
                                                আইডি</span></label>
                                        <input type="text" wire:model="formData.hof_ration_card_id"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Family
                                            Members (number only)<br><span
                                                class="text-xs text-gray-500 font-normal">পরিবারের মোট সদস্য
                                                সংখ্যা</span></label>
                                        <input type="number" min="1"
                                            wire:model.live="formData.num_family_members"
                                            class="w-full border border-gray-300 rounded p-2 text-sm font-semibold text-gray-700 focus:ring-amber-500 focus:border-amber-500">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Contact No *
                                            <br><span class="text-xs text-gray-500 font-normal">যোগাযোগ নম্বর
                                                (মোবাইল)</span></label>
                                        <div x-data="{
                                            val: @entangle('formData.contact_no'),
                                            error: '',
                                            valid: false,
                                            check() {
                                                if (!this.val) {
                                                    this.valid = false;
                                                    this.error = '';
                                                    return;
                                                }
                                                let cleaned = window.cleanInput.numeric(this.val, 10);
                                                if (this.val !== cleaned) { this.val = cleaned; }
                                                this.valid = window.checkValid.contact_no(this.val);
                                                this.error = this.val.length > 0 && !this.valid ? 'Must be 10 digits' : '';
                                            }
                                        }" x-init="check();
                                        $watch('val', () => check())">
                                            <input type="text" x-model="val" maxlength="10"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                                    Valid</span>
                                                <span x-show="error" x-text="error"
                                                    class="text-red-500 text-xs font-semibold"></span>
                                            </div>
                                        </div>
                                        @error('formData.contact_no')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Category *
                                            <br><span class="text-xs text-gray-500 font-normal">শ্রেণী</span></label>
                                        <select wire:model.live="formData.category"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            @foreach ($categories as $cat)
                                                @php
                                                    $val = $cat === 'EWS' ? 'UR-EWS' : $cat;
                                                @endphp
                                                <option value="{{ $val }}">
                                                    {{ $val }}
                                                    @if ($val === 'UR')
                                                        / সাধারণ
                                                    @elseif($val === 'UR-EWS')
                                                        / সাধারণ (অর্থনৈতিকভাবে অনগ্রসর)
                                                    @elseif($val === 'SC')
                                                        / তফসিলি জাতি
                                                    @elseif($val === 'ST')
                                                        / তফসিলি উপজাতি
                                                    @elseif($val === 'OBC')
                                                        / অন্যান্য অনগ্রসর শ্রেণী
                                                    @endif
                                                </option>
                                            @endforeach
                                            @if (!in_array('PVTG', $categories))
                                                <option value="PVTG">PVTG / বিশেষ দুর্বল উপজাতি শ্রেণী</option>
                                            @endif
                                        </select>
                                        @error('formData.category')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                    @if (in_array($formData['category'] ?? '', ['SC', 'ST', 'OBC']))
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Caste
                                                Certificate No. * <br><span
                                                    class="text-xs text-gray-500 font-normal">জাতিগত সংশাপত্র
                                                    নং</span></label>
                                            <input type="text" wire:model="formData.caste_certificate_no"
                                                placeholder="Enter Caste Certificate Number"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @error('formData.caste_certificate_no')
                                                <span class="text-red-600 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @elseif (($formData['category'] ?? '') == 'UR-EWS')
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">EWS
                                                Certificate No. * <br><span
                                                    class="text-xs text-gray-500 font-normal">ইডব্লিউএস সংশাপত্র
                                                    নং</span></label>
                                            <input type="text" wire:model="formData.ews_certificate_no"
                                                placeholder="Enter EWS Certificate Number"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @error('formData.ews_certificate_no')
                                                <span class="text-red-600 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @elseif (($formData['category'] ?? '') == 'PVTG')
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">PVTG
                                                Certificate/Declaration No. * <br><span
                                                    class="text-xs text-gray-500 font-normal">পিভিটিজি সংশাপত্র
                                                    নং</span></label>
                                            <input type="text" wire:model="formData.pvtg_certificate_no"
                                                placeholder="Enter PVTG ID/Declaration No"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @error('formData.pvtg_certificate_no')
                                                <span class="text-red-600 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- HOF Address --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">A2</span>
                                        Address (Permanent Address) | স্থায়ী ঠিকানা
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">State * <br><span
                                                class="text-xs text-gray-500 font-normal">রাজ্য</span></label>
                                        <input type="text" wire:model="formData.state" readonly
                                            class="w-full bg-gray-100 border border-gray-300 rounded p-2 text-sm font-semibold">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">District *
                                            <br><span class="text-xs text-gray-500 font-normal">জেলা</span></label>
                                        <select wire:model.live="formData.district_id"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select District --</option>
                                            @foreach ($districts as $d)
                                                <option value="{{ $d->id }}">{{ strtoupper($d->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('formData.district_id')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Rural / Urban *
                                            <br><span class="text-xs text-gray-500 font-normal">গ্রামীণ / শহর
                                                এলাকা</span></label>
                                        <select wire:model.live="formData.rural_urban"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select Rural/Urban --</option>
                                            <option value="2">Rural (Block) / গ্রামীণ</option>
                                            <option value="1">Urban (Municipality/Corporation) / শহর</option>
                                        </select>
                                        @error('formData.rural_urban')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Block /
                                            Municipality * <br><span class="text-xs text-gray-500 font-normal">ব্লক /
                                                পৌরসভা</span></label>
                                        <select wire:model.live="formData.blockurban"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            {{ empty($blocks) ? 'disabled' : '' }}>
                                            <option value="">-- Select --</option>
                                            @foreach ($blocks as $b)
                                                <option value="{{ $b->id }}">{{ strtoupper($b->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('formData.blockurban')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">GP / Ward *
                                            <br><span class="text-xs text-gray-500 font-normal">গ্রাম পঞ্চায়েত /
                                                ওয়ার্ড</span></label>
                                        <select wire:model="formData.gpward"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            {{ empty($gps) ? 'disabled' : '' }}>
                                            <option value="">-- Select --</option>
                                            @foreach ($gps as $g)
                                                <option value="{{ $g->id }}">{{ strtoupper($g->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('formData.gpward')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Village / Town /
                                            City * <br><span class="text-xs text-gray-500 font-normal">গ্রাম /
                                                শহর</span></label>
                                        <input type="text" wire:model="formData.village_town"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('formData.village_town')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">House / Premise
                                            No <br><span class="text-xs text-gray-500 font-normal">বাড়ির
                                                নম্বর</span></label>
                                        <input type="text" wire:model="formData.house_no"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Police Station *
                                            <br><span class="text-xs text-gray-500 font-normal">থানা</span></label>
                                        <input type="text" wire:model="formData.police_station"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('formData.police_station')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Post Office *
                                            <br><span class="text-xs text-gray-500 font-normal">ডাকঘর</span></label>
                                        <input type="text" wire:model="formData.post_office"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('formData.post_office')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Pincode *
                                            <br><span class="text-xs text-gray-500 font-normal">পিন কোড</span></label>
                                        <div x-data="{
                                            val: @entangle('formData.pincode'),
                                            error: '',
                                            valid: false,
                                            check() {
                                                if (!this.val) {
                                                    this.valid = false;
                                                    this.error = '';
                                                    return;
                                                }
                                                let cleaned = window.cleanInput.numeric(this.val, 6);
                                                if (this.val !== cleaned) { this.val = cleaned; }
                                                this.valid = window.checkValid.pincode(this.val);
                                                this.error = this.val.length > 0 && !this.valid ? 'Must be 6 digits' : '';
                                            }
                                        }" x-init="check();
                                        $watch('val', () => check())">
                                            <input type="text" x-model="val" maxlength="6"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                                    Valid</span>
                                                <span x-show="error" x-text="error"
                                                    class="text-red-500 text-xs font-semibold"></span>
                                            </div>
                                        </div>
                                        @error('formData.pincode')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- HOF Bank Details --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">A3</span>
                                        HOF Bank Details (For Cash Transfer) | পরিবার প্রধানের ব্যাংক বিবরণী
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">HOF IFSC Code *
                                            <br><span class="text-xs text-gray-500 font-normal">আইএফএসসি
                                                কোড</span></label>
                                        <div x-data="{
                                            val: @entangle('formData.hof_ifsc'),
                                            error: '',
                                            valid: false,
                                            async check() {
                                                if (!this.val) {
                                                    this.valid = false;
                                                    this.error = '';
                                                    return;
                                                }
                                                let cleaned = window.cleanInput.alphaNumericUpper(this.val, 11);
                                                if (this.val !== cleaned) { this.val = cleaned; }
                                                this.valid = window.checkValid.ifsc(this.val);
                                                this.error = this.val.length > 0 && !this.valid ? 'Format: ABCD0123456' : '';
                                        
                                                if (this.valid) {
                                                    try {
                                                        let response = await fetch('/js/bank-ifsc-master.json');
                                                        if (response.ok) {
                                                            let banks = await response.json();
                                                            let found = banks.find(b => b.ifsc.toUpperCase() === this.val.toUpperCase());
                                                            if (found) {
                                                                $wire.set('formData.hof_bank_name', found.bankName);
                                                            }
                                                        }
                                                    } catch (e) {
                                                        console.error('Error looking up IFSC:', e);
                                                    }
                                                }
                                            }
                                        }" x-init="check();
                                        $watch('val', () => check())">
                                            <input type="text" x-model="val" maxlength="11"
                                                placeholder="e.g. SBIN0001234"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono">
                                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                                    Valid</span>
                                                <span x-show="error" x-text="error"
                                                    class="text-red-500 text-xs font-semibold"></span>
                                            </div>
                                        </div>
                                        @error('formData.hof_ifsc')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">HOF Bank Name *
                                            <br><span class="text-xs text-gray-500 font-normal">ব্যাংকের
                                                নাম</span></label>
                                        <div x-data="{
                                            val: @entangle('formData.hof_bank_name'),
                                            error: '',
                                            valid: false,
                                            check() {
                                                if (!this.val) {
                                                    this.valid = false;
                                                    this.error = '';
                                                    return;
                                                }
                                                let cleaned = window.cleanInput.lettersOnly(this.val);
                                                if (this.val !== cleaned) { this.val = cleaned; }
                                                this.valid = window.checkValid.name(this.val);
                                                this.error = this.val.length > 0 && !this.valid ? 'Letters only' : '';
                                            }
                                        }" x-init="check();
                                        $watch('val', () => check())">
                                            <input type="text" x-model="val"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                                    Valid</span>
                                                <span x-show="error" x-text="error"
                                                    class="text-red-500 text-xs font-semibold"></span>
                                            </div>
                                        </div>
                                        @error('formData.hof_bank_name')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">HOF Account
                                            Number * <br><span class="text-xs text-gray-500 font-normal">অ্যাকাউন্ট
                                                নম্বর</span></label>
                                        <div x-data="{
                                            val: @entangle('formData.hof_acc_no'),
                                            error: '',
                                            valid: false,
                                            check() {
                                                if (!this.val) {
                                                    this.valid = false;
                                                    this.error = '';
                                                    return;
                                                }
                                                let cleaned = window.cleanInput.numeric(this.val, 18);
                                                if (this.val !== cleaned) { this.val = cleaned; }
                                                this.valid = window.checkValid.acc_no(this.val);
                                                this.error = this.val.length > 0 && !this.valid ? 'Must be 9-18 digits' : '';
                                            }
                                        }" x-init="check();
                                        $watch('val', () => check())">
                                            <input type="text" x-model="val" maxlength="18"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                                    Valid</span>
                                                <span x-show="error" x-text="error"
                                                    class="text-red-500 text-xs font-semibold"></span>
                                            </div>
                                        </div>
                                        @error('formData.hof_acc_no')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- HOF EPIC / Voter Details --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">A4</span>
                                        HOF EPIC/Voter Card Details | ভোটার কার্ড বিবরণী
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">HOF EPIC/Voter
                                            No. <br><span class="text-xs text-gray-500 font-normal">ভোটার কার্ড
                                                নম্বর</span></label>
                                        <div x-data="{
                                            val: @entangle('formData.hof_epic_no'),
                                            error: '',
                                            valid: false,
                                            check() {
                                                if (!this.val) {
                                                    this.valid = false;
                                                    this.error = '';
                                                    return;
                                                }
                                                let cleaned = window.cleanInput.alphaNumericUpper(this.val, 10);
                                                if (this.val !== cleaned) { this.val = cleaned; }
                                                this.valid = window.checkValid.epic(this.val);
                                                this.error = this.val.length > 0 && !this.valid ? 'Format: AAA1234567' : '';
                                            }
                                        }" x-init="check();
                                        $watch('val', () => check())">
                                            <input type="text" x-model="val" maxlength="10"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono">
                                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                                    Valid EPIC</span>
                                                <span x-show="error" x-text="error"
                                                    class="text-red-500 text-xs font-semibold"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">HOF AC & Part No.
                                            of Electoral Roll <br><span
                                                class="text-xs text-gray-500 font-normal">বিধানসভা ও পার্ট
                                                নং</span></label>
                                        <input type="text" wire:model="formData.hof_ac_part_no"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Member Basic Identity --}}
                            @php
                                $index = $activeMemberIndex - 1;
                            @endphp
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
                                wire:key="member-identity-{{ $index }}">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">M1</span>
                                        Member #{{ $activeMemberIndex }} Basic Identity | সদস্যের পরিচয় ও সম্পর্ক
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4 pb-4 border-b border-gray-200">
                                    <div class="md:col-span-4">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Member Category *
                                            <br><span class="text-xs text-gray-500 font-normal">সদস্যের
                                                বিভাগ</span></label>
                                        <div class="flex items-center gap-6">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio"
                                                    wire:model.live="members.{{ $index }}.member_type"
                                                    value="adult"
                                                    class="h-4 w-4 text-amber-700 border-gray-300 focus:ring-amber-500">
                                                <span class="ml-2 text-sm font-medium text-gray-900">Adult /
                                                    প্রাপ্তবয়স্ক</span>
                                            </label>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio"
                                                    wire:model.live="members.{{ $index }}.member_type"
                                                    value="child"
                                                    class="h-4 w-4 text-amber-700 border-gray-300 focus:ring-amber-500">
                                                <span class="ml-2 text-sm font-medium text-gray-900">Child /
                                                    শিশু</span>
                                            </label>
                                        </div>
                                        @error("members.{$index}.member_type")
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name *
                                            <br><span class="text-xs text-gray-500 font-normal">সদস্যের
                                                নাম</span></label>
                                        <div x-data="{
                                            val: @entangle('members.' . $index . '.name'),
                                            error: '',
                                            valid: false,
                                            check() {
                                                if (!this.val) {
                                                    this.valid = false;
                                                    this.error = '';
                                                    return;
                                                }
                                                let cleaned = window.cleanInput.lettersOnly(this.val);
                                                if (this.val !== cleaned) { this.val = cleaned; }
                                                this.valid = window.checkValid.name(this.val);
                                                this.error = this.val.length > 0 && !this.valid ? 'Letters/dots/spaces' : '';
                                            }
                                        }" x-init="check();
                                        $watch('val', () => check())">
                                            <input type="text" x-model="val"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                                    Valid</span>
                                                <span x-show="error" x-text="error"
                                                    class="text-red-500 text-xs font-semibold"></span>
                                            </div>
                                        </div>
                                        @error("members.{$index}.name")
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Birth *
                                            <br><span class="text-xs text-gray-500 font-normal">জন্ম
                                                তারিখ</span></label>
                                        <input type="date" wire:model="members.{{ $index }}.dob"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error("members.{$index}.dob")
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Gender *
                                            <br><span class="text-xs text-gray-500 font-normal">লিঙ্গ</span></label>
                                        <select wire:model="members.{{ $index }}.gender"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            @foreach ($genders as $gender)
                                                <option value="{{ $gender }}">
                                                    {{ $gender }}
                                                    @if ($gender === 'Male')
                                                        / পুরুষ
                                                    @elseif($gender === 'Female')
                                                        / মহিলা
                                                    @elseif($gender === 'Other')
                                                        / অন্যান্য
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("members.{$index}.gender")
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Relation to HOF *
                                            <br><span class="text-xs text-gray-500 font-normal">পরিবার প্রধানের সাথে
                                                সম্পর্ক</span></label>
                                        <input type="text" wire:model="members.{{ $index }}.relation"
                                            placeholder="e.g. Spouse, Son"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error("members.{$index}.relation")
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Aadhaar Number
                                            (Optional for child &lt;5 years)<br><span
                                                class="text-xs text-gray-500 font-normal">আধার নম্বর</span></label>
                                        <div x-data="{
                                            val: @entangle('members.' . $index . '.aadhaar'),
                                            error: '',
                                            valid: false,
                                            check() {
                                                if (!this.val) {
                                                    this.valid = false;
                                                    this.error = '';
                                                    return;
                                                }
                                                let cleaned = window.cleanInput.numeric(this.val, 12);
                                                if (this.val !== cleaned) { this.val = cleaned; }
                                                this.valid = window.checkValid.aadhaar(this.val);
                                                this.error = this.val.length > 0 && !this.valid ? 'Invalid checksum' : '';
                                            }
                                        }" x-init="check();
                                        $watch('val', () => check())">
                                            <input type="text" x-model="val" maxlength="12"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                                    Valid Aadhaar</span>
                                                <span x-show="error" x-text="error"
                                                    class="text-red-500 text-xs font-semibold"></span>
                                            </div>
                                        </div>
                                        @error("members.{$index}.aadhaar")
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                @if (($members[$index]['member_type'] ?? 'adult') === 'adult')
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4 pt-4 border-t border-gray-200">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Applying for
                                                Annapurna Yojana? <br><span
                                                    class="text-xs text-gray-500 font-normal">অন্নপূর্ণা যোজনার জন্য
                                                    আবেদন করছেন কি?</span></label>
                                            <select wire:model="members.{{ $index }}.applying_for_ay"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="No">No / না</option>
                                                <option value="Yes">Yes / হ্যাঁ</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Member
                                                EPIC/Voter No. <br><span
                                                    class="text-xs text-gray-500 font-normal">ভোটার কার্ড
                                                    নম্বর</span></label>
                                            <div x-data="{
                                                val: @entangle('members.' . $index . '.epic_no'),
                                                error: '',
                                                valid: false,
                                                check() {
                                                    if (!this.val) {
                                                        this.valid = false;
                                                        this.error = '';
                                                        return;
                                                    }
                                                    let cleaned = window.cleanInput.alphaNumericUpper(this.val, 10);
                                                    if (this.val !== cleaned) { this.val = cleaned; }
                                                    this.valid = window.checkValid.epic(this.val);
                                                    this.error = this.val.length > 0 && !this.valid ? 'Format: AAA1234567' : '';
                                                }
                                            }" x-init="check();
                                            $watch('val', () => check())">
                                                <input type="text" x-model="val" maxlength="10"
                                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono">
                                                <div class="flex items-center gap-2 mt-0.5"
                                                    style="min-height: 1.25rem;">
                                                    <span x-show="valid"
                                                        class="text-xs text-green-600 font-semibold">✓ Valid
                                                        EPIC</span>
                                                    <span x-show="error" x-text="error"
                                                        class="text-red-500 text-xs font-semibold"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Member AC &
                                                Part No. <br><span class="text-xs text-gray-500 font-normal">বিধানসভা ও
                                                    পার্ট নং</span></label>
                                            <input type="text" wire:model="members.{{ $index }}.ac_part_no"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if (($members[$index]['member_type'] ?? 'adult') === 'adult')
                                {{-- Member Bank details --}}
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mt-4"
                                    wire:key="member-bank-{{ $index }}">
                                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                            <span
                                                class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                                style="background-color: #78350f;">M2</span>
                                            Member Bank Details (For Cash Transfer) | সদস্যের ব্যাংক বিবরণী
                                        </h3>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">IFSC Code *
                                                <br><span class="text-xs text-gray-500 font-normal">আইএফএসসি
                                                    কোড</span></label>
                                            <div x-data="{
                                                val: @entangle('members.' . $index . '.ifsc'),
                                                index: {{ $index }},
                                                error: '',
                                                valid: false,
                                                async check() {
                                                    if (!this.val) {
                                                        this.valid = false;
                                                        this.error = '';
                                                        return;
                                                    }
                                                    let cleaned = window.cleanInput.alphaNumericUpper(this.val, 11);
                                                    if (this.val !== cleaned) { this.val = cleaned; }
                                                    this.valid = window.checkValid.ifsc(this.val);
                                                    this.error = this.val.length > 0 && !this.valid ? 'Format: ABCD0123456' : '';
                                            
                                                    if (this.valid) {
                                                        try {
                                                            let response = await fetch('/js/bank-ifsc-master.json');
                                                            if (response.ok) {
                                                                let banks = await response.json();
                                                                let found = banks.find(b => b.ifsc.toUpperCase() === this.val.toUpperCase());
                                                                if (found) {
                                                                    $wire.set('members.' + this.index + '.bank_name', found.bankName);
                                                                }
                                                            }
                                                        } catch (e) {
                                                            console.error('Error looking up IFSC:', e);
                                                        }
                                                    }
                                                }
                                            }" x-init="check();
                                            $watch('val', () => check())">
                                                <input type="text" x-model="val" maxlength="11"
                                                    placeholder="e.g. SBIN0001234"
                                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono">
                                                <div class="flex items-center gap-2 mt-0.5"
                                                    style="min-height: 1.25rem;">
                                                    <span x-show="valid"
                                                        class="text-xs text-green-600 font-semibold">✓ Valid</span>
                                                    <span x-show="error" x-text="error"
                                                        class="text-red-500 text-xs font-semibold"></span>
                                                </div>
                                            </div>
                                            @error("members.{$index}.ifsc")
                                                <span class="text-red-600 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Bank Name *
                                                <br><span class="text-xs text-gray-500 font-normal">ব্যাংকের
                                                    নাম</span></label>
                                            <div x-data="{
                                                val: @entangle('members.' . $index . '.bank_name'),
                                                error: '',
                                                valid: false,
                                                check() {
                                                    if (!this.val) {
                                                        this.valid = false;
                                                        this.error = '';
                                                        return;
                                                    }
                                                    let cleaned = window.cleanInput.lettersOnly(this.val);
                                                    if (this.val !== cleaned) { this.val = cleaned; }
                                                    this.valid = window.checkValid.name(this.val);
                                                    this.error = this.val.length > 0 && !this.valid ? 'Letters only' : '';
                                                }
                                            }" x-init="check();
                                            $watch('val', () => check())">
                                                <input type="text" x-model="val"
                                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <div class="flex items-center gap-2 mt-0.5"
                                                    style="min-height: 1.25rem;">
                                                    <span x-show="valid"
                                                        class="text-xs text-green-600 font-semibold">✓ Valid</span>
                                                    <span x-show="error" x-text="error"
                                                        class="text-red-500 text-xs font-semibold"></span>
                                                </div>
                                            </div>
                                            @error("members.{$index}.bank_name")
                                                <span class="text-red-600 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Account
                                                Number * <br><span class="text-xs text-gray-500 font-normal">অ্যাকাউন্ট
                                                    নম্বর</span></label>
                                            <div x-data="{
                                                val: @entangle('members.' . $index . '.acc_no'),
                                                error: '',
                                                valid: false,
                                                check() {
                                                    if (!this.val) {
                                                        this.valid = false;
                                                        this.error = '';
                                                        return;
                                                    }
                                                    let cleaned = window.cleanInput.numeric(this.val, 18);
                                                    if (this.val !== cleaned) { this.val = cleaned; }
                                                    this.valid = window.checkValid.acc_no(this.val);
                                                    this.error = this.val.length > 0 && !this.valid ? 'Must be 9-18 digits' : '';
                                                }
                                            }" x-init="check();
                                            $watch('val', () => check())">
                                                <input type="text" x-model="val" maxlength="18"
                                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                                                <div class="flex items-center gap-2 mt-0.5"
                                                    style="min-height: 1.25rem;">
                                                    <span x-show="valid"
                                                        class="text-xs text-green-600 font-semibold">✓ Valid</span>
                                                    <span x-show="error" x-text="error"
                                                        class="text-red-500 text-xs font-semibold"></span>
                                                </div>
                                            </div>
                                            @error("members.{$index}.acc_no")
                                                <span class="text-red-600 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="bg-amber-50 border border-amber-200 rounded-lg p-5 mt-4 text-center text-amber-900 shadow-sm">
                                    <p class="text-xs font-semibold">Note: Bank account and Voter details are not
                                        required for child members.</p>
                                    <p class="text-[10px] text-amber-700 mt-1">শিশু সদস্যদের জন্য ব্যাংক অ্যাকাউন্ট এবং
                                        ভোটার বিবরণীর প্রয়োজন নেই।</p>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif

                {{-- SECTION B: RATION CARD / FOOD SUBSIDY --}}
                @if ($activeSection === 'ration_subsidy')
                    <div class="space-y-6">
                        @if ($activeMemberIndex === 0)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">B</span>
                                        Ration Card & Food Subsidy | রেশন কার্ড ও খাদ্য ভর্তুকি বিবরণী
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Do you have a
                                            Digital Ration Card? * <br><span
                                                class="text-xs text-gray-500 font-normal">রেশন কার্ড আছে
                                                কি?</span></label>
                                        <select wire:model="formData.has_digital_ration_card"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                            <option value="No">No / না</option>
                                        </select>
                                        @error('formData.has_digital_ration_card')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">If Yes, Card Type
                                            <br><span class="text-xs text-gray-500 font-normal">রেশন কার্ডের
                                                ধরন</span></label>
                                        <select wire:model="formData.ration_card_type"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            @foreach ($rcTypes as $type)
                                                @php
                                                    $val = $type;
                                                    if ($type === 'RKSY-I') {
                                                        $val = 'RKSY1';
                                                    } elseif ($type === 'RKSY-II') {
                                                        $val = 'RKSY2';
                                                    } elseif ($type === 'Non-Subsidized') {
                                                        $val = 'Non-subsidized';
                                                    }
                                                @endphp
                                                <option value="{{ $val }}">
                                                    {{ $type }}
                                                    @if ($type === 'AAY')
                                                        (Antyodaya Anna Yojana)
                                                    @elseif($type === 'PHH')
                                                        (Priority Household)
                                                    @elseif($type === 'SPHH')
                                                        (Special Priority Household)
                                                    @elseif($type === 'Non-Subsidized')
                                                        / ভর্তুকিহীন
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Lifting Monthly
                                            Ration? * <br><span class="text-xs text-gray-500 font-normal">রেশন পাচ্ছেন
                                                কি?</span></label>
                                        <select wire:model="formData.is_lifting_ration"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                            <option value="No">No / না</option>
                                        </select>
                                        @error('formData.is_lifting_ration')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Member Section B --}}
                            @php
                                $index = $activeMemberIndex - 1;
                            @endphp
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
                                wire:key="member-ration-{{ $index }}">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">B</span>
                                        Ration Card (Member #{{ $activeMemberIndex }}) | সদস্যের রেশন বিবরণী
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Do they have a
                                            Digital Ration Card? <br><span
                                                class="text-xs text-gray-500 font-normal">রেশন কার্ড আছে
                                                কি?</span></label>
                                        <select wire:model="members.{{ $index }}.has_digital_ration_card"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                            <option value="No">No / না</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ration Card
                                            Number <br><span class="text-xs text-gray-500 font-normal">রেশন কার্ড
                                                নম্বর</span></label>
                                        <input type="text" wire:model="members.{{ $index }}.ration_card_no"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ration Card Type
                                            <br><span class="text-xs text-gray-500 font-normal">রেশন কার্ডের
                                                ধরন</span></label>
                                        <select wire:model="members.{{ $index }}.ration_card_type"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            @foreach ($rcTypes as $type)
                                                @php
                                                    $val = $type;
                                                    if ($type === 'RKSY-I') {
                                                        $val = 'RKSY1';
                                                    } elseif ($type === 'RKSY-II') {
                                                        $val = 'RKSY2';
                                                    } elseif ($type === 'Non-Subsidized') {
                                                        $val = 'Non-subsidized';
                                                    }
                                                @endphp
                                                <option value="{{ $val }}">
                                                    {{ $type }}
                                                    @if ($type === 'Non-Subsidized')
                                                        / ভর্তুকিহীন
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif


                {{-- SECTION C: ASSETS --}}
                @if ($activeSection === 'assets')
                    <div class="space-y-6">
                        @if ($activeMemberIndex === 0)
                            {{-- Family Assets for HOF --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">C1</span>
                                        Family Assets | পারিবারিক সম্পদের বিবরণ
                                    </h3>
                                </div>

                                {{-- Land & house --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">House size: &ge;
                                            3 Pucca Rooms? * <br><span class="text-xs text-gray-500 font-normal">৩ বা
                                                তার বেশি পাকা ঘর আছে কি?</span></label>
                                        <select wire:model="formData.has_pucca_rooms"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                            <option value="No">No / না</option>
                                        </select>
                                        @error('formData.has_pucca_rooms')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Owns land? *
                                            <br><span class="text-xs text-gray-500 font-normal">জমি আছে
                                                কি?</span></label>
                                        <select wire:model="formData.owns_land"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                            <option value="No">No / না</option>
                                        </select>
                                        @error('formData.owns_land')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Size of Land (in
                                            Decimals) <br><span class="text-xs text-gray-500 font-normal">জমির মোট
                                                পরিমাণ (ডেসিমেলে)</span></label>
                                        <input type="number" step="0.01" wire:model="formData.land_size_decimals"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>

                                {{-- 4-Wheeler + Vehicle Count --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Owns 4-Wheeler? *
                                            <br><span class="text-xs text-gray-500 font-normal">৪-চাকার গাড়ি আছে
                                                কি?</span></label>
                                        <select wire:model="formData.owns_4_wheeler"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                            <option value="No">No / না</option>
                                        </select>
                                        @error('formData.owns_4_wheeler')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Vehicles
                                            <br><span class="text-xs text-gray-500 font-normal">গাড়ির
                                                সংখ্যা</span></label>
                                        <input type="number" min="0" wire:model.live="formData.num_vehicles"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>

                                {{-- Dynamic vehicle detail rows — one row per vehicle --}}
                                @if (!empty($formData['vehicles']) && count($formData['vehicles']) > 0)
                                    <div class="mt-4 space-y-2">
                                        <p class="text-sm font-semibold text-gray-600 mb-2">Vehicle Details / গাড়ির
                                            বিবরণ</p>
                                        @foreach ($formData['vehicles'] as $vi => $vehicle)
                                            <div wire:key="hof-vehicle-{{ $vi }}"
                                                class="grid grid-cols-1 md:grid-cols-3 gap-4 p-3 border border-amber-200 rounded-lg bg-amber-50 items-center">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-700 text-white text-xs font-bold">{{ $vi + 1 }}</span>
                                                    <span class="text-sm text-gray-700 font-medium">Vehicle
                                                        {{ $vi + 1 }}</span>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-semibold text-gray-600 mb-1">Registration
                                                        No. / রেজিস্ট্রেশন নম্বর</label>
                                                    <div x-data="{
                                                        val: @entangle('formData.vehicles.' . $vi . '.reg_no'),
                                                        error: '',
                                                        valid: false,
                                                        check() {
                                                            if (!this.val) {
                                                                this.valid = false;
                                                                this.error = '';
                                                                return;
                                                            }
                                                            let cleaned = window.cleanInput.alphaNumericUpper(this.val, 10);
                                                            if (this.val !== cleaned) { this.val = cleaned; }
                                                            this.valid = window.checkValid.vehicle(this.val);
                                                            this.error = this.val.length > 0 && !this.valid ? 'Format: WB01AB1234' : '';
                                                        }
                                                    }" x-init="check();
                                                    $watch('val', () => check())">
                                                        <input type="text" x-model="val" maxlength="10"
                                                            placeholder="e.g. WB-01-AB-1234"
                                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-amber-500 focus:border-amber-500 uppercase font-mono">
                                                        <div class="flex items-center gap-2 mt-0.5"
                                                            style="min-height: 1.25rem;">
                                                            <span x-show="valid"
                                                                class="text-xs text-green-600 font-semibold">✓
                                                                Valid</span>
                                                            <span x-show="error" x-text="error"
                                                                class="text-red-500 text-xs font-semibold"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-semibold text-gray-600 mb-1">Vehicle
                                                        Model / মডেল নাম</label>
                                                    <input type="text"
                                                        wire:model="formData.vehicles.{{ $vi }}.model"
                                                        placeholder="e.g. Maruti Swift"
                                                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-amber-500 focus:border-amber-500">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                            </div>

                            {{-- Health Insurance for HOF --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">C2</span>
                                        Health Insurance Coverage (HOF) | পরিবার প্রধানের স্বাস্থ্য বীমা বিবরণী
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Health Insurance
                                            Type <br><span class="text-xs text-gray-500 font-normal">বীমার
                                                প্রকার</span></label>
                                        <select wire:model="formData.health_insurance_type"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="None">None / নেই</option>
                                            <option value="Government">Government / সরকারি (যেমন স্বাস্থ্যসাথী)
                                            </option>
                                            <option value="Private">Private / ব্যক্তিগত</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Annual Premium
                                            (INR) <br><span class="text-xs text-gray-500 font-normal">বার্ষিক
                                                প্রিমিয়াম</span></label>
                                        <input type="number" wire:model="formData.health_insurance_premium"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Sum Assured (INR)
                                            <br><span class="text-xs text-gray-500 font-normal">বীমাকৃত
                                                রাশি</span></label>
                                        <input type="number" wire:model="formData.health_insurance_sum_assured"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Health Insurance for Adult Member --}}
                            @php
                                $index = $activeMemberIndex - 1;
                            @endphp
                            @if (($members[$index]['member_type'] ?? 'adult') === 'adult')
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
                                    wire:key="member-insurance-{{ $index }}">
                                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                            <span
                                                class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                                style="background-color: #78350f;">C</span>
                                            Health Insurance Coverage (Member #{{ $activeMemberIndex }}) | সদস্যের
                                            স্বাস্থ্য বীমা বিবরণী
                                        </h3>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Health
                                                Insurance Type <br><span
                                                    class="text-xs text-gray-500 font-normal">বীমার
                                                    প্রকার</span></label>
                                            <select wire:model="members.{{ $index }}.health_insurance_type"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="No">None / নেই</option>
                                                <option value="Government">Government / সরকারি (যেমন স্বাস্থ্যসাথী)
                                                </option>
                                                <option value="Private">Private / ব্যক্তিগত</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Annual
                                                Premium (INR) <br><span
                                                    class="text-xs text-gray-500 font-normal">বার্ষিক
                                                    প্রিমিয়াম</span></label>
                                            <input type="number"
                                                wire:model="members.{{ $index }}.health_insurance_premium"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Sum Assured
                                                (INR) <br><span class="text-xs text-gray-500 font-normal">বীমাকৃত
                                                    রাশি</span></label>
                                            <input type="number"
                                                wire:model="members.{{ $index }}.health_insurance_sum_assured"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif


                {{-- SECTION D: INCOME & PROFESSION --}}
                @if ($activeSection === 'income_profession')
                    <div class="space-y-6">
                        @if ($activeMemberIndex === 0)
                            {{-- Income / Profession for HOF --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">D1</span>
                                        Income & Profession | আয় ও পেশা
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Pays Income /
                                            Professional Tax? * <br><span class="text-xs text-gray-500 font-normal">কর
                                                প্রদান করেন কি?</span></label>
                                        <select wire:model="formData.pays_tax"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                            <option value="No">No / না</option>
                                        </select>
                                        @error('formData.pays_tax')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">PAN Card No.
                                            (HOF) <br><span class="text-xs text-gray-500 font-normal">প্যান কার্ড
                                                নং</span></label>
                                        <div x-data="{
                                            val: @entangle('formData.hof_pan_no'),
                                            error: '',
                                            valid: false,
                                            check() {
                                                if (!this.val) {
                                                    this.valid = false;
                                                    this.error = '';
                                                    return;
                                                }
                                                let cleaned = window.cleanInput.alphaNumericUpper(this.val, 10);
                                                if (this.val !== cleaned) { this.val = cleaned; }
                                                this.valid = window.checkValid.pan(this.val);
                                                this.error = this.val.length > 0 && !this.valid ? 'Format: AAAAA1111A' : '';
                                            }
                                        }" x-init="check();
                                        $watch('val', () => check())">
                                            <input type="text" x-model="val" maxlength="10"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono">
                                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                                    Valid PAN</span>
                                                <span x-show="error" x-text="error"
                                                    class="text-red-500 text-xs font-semibold"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Employment of HOF
                                            <br><span class="text-xs text-gray-500 font-normal">প্রধানের
                                                কর্মসংস্থান</span></label>
                                        <select wire:model="formData.hof_employment_nature"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            @foreach ($employmentNatures as $nature)
                                                @php
                                                    $val = $nature;
                                                    if ($nature === 'Salaried, in Private Sector') {
                                                        $val = 'Salaried in Private';
                                                    } elseif (
                                                        $nature ===
                                                        'Formal Sector Self-Employed (Entrepreneur/Business/Proprietor/etc.)'
                                                    ) {
                                                        $val = 'Formal Sector Self-Employed';
                                                    } elseif (
                                                        $nature ===
                                                        'Informal Sector Self-Employed (Artisan/Craftsman/Farmer/etc.)'
                                                    ) {
                                                        $val = 'Informal Sector Self-Employed';
                                                    }
                                                @endphp
                                                <option value="{{ $val }}">
                                                    {{ $nature }}
                                                    @if ($nature === 'Government Sector')
                                                        / সরকারি ক্ষেত্র
                                                    @elseif($nature === 'Salaried, in Private Sector')
                                                        / বেসরকারি ক্ষেত্র
                                                    @elseif($nature === 'Formal Sector Self-Employed (Entrepreneur/Business/Proprietor/etc.)')
                                                        / স্ব-নিযুক্ত (আনুষ্ঠানিক)
                                                    @elseif($nature === 'Part-time job')
                                                        / খণ্ডকালীন কাজ
                                                    @elseif($nature === 'Informal Sector Self-Employed (Artisan/Craftsman/Farmer/etc.)')
                                                        / স্ব-নিযুক্ত (অনানুষ্ঠানিক)
                                                    @elseif($nature === 'Housewife')
                                                        / গৃহিণী
                                                    @elseif($nature === 'Unemployed')
                                                        / বেকার
                                                    @elseif($nature === 'Others')
                                                        / অন্যান্য
                                                    @endif
                                                </option>
                                            @endforeach
                                            @if (!in_array('Migrant Labourer', $employmentNatures))
                                                <option value="Migrant Labourer">Migrant Labourer / পরিযায়ী শ্রমিক
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-6 mt-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Total Annual
                                            Family Income (INR) * <br><span
                                                class="text-xs text-gray-500 font-normal">বার্ষিক মোট পারিবারিক
                                                আয়</span></label>
                                        <input type="number" wire:model="formData.total_annual_income"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('formData.total_annual_income')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Literacy & Education for HOF --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">D2</span>
                                        Education & Literacy (HOF) | পরিবার প্রধানের শিক্ষা ও সাক্ষরতা
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Literacy Status
                                            (HOF) <br><span class="text-xs text-gray-500 font-normal">স্বাক্ষরতা
                                                স্থিতি</span></label>
                                        <select wire:model="formData.hof_literate_status"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            <option value="Literate">Literate / সাক্ষর</option>
                                            <option value="Illiterate">Illiterate / নিরক্ষর</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Highest
                                            Educational Qualification <br><span
                                                class="text-xs text-gray-500 font-normal">সর্বোচ্চ শিক্ষাগত
                                                যোগ্যতা</span></label>
                                        <input type="text" wire:model="formData.hof_highest_qualification"
                                            placeholder="e.g. Graduate, Higher Secondary"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 border-t border-gray-200 pt-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Literate
                                            Adults in Family <br><span class="text-xs text-gray-500 font-normal">সাক্ষর
                                                প্রাপ্তবয়স্ক সংখ্যা</span></label>
                                        <input type="number" wire:model="formData.num_literate_adults"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Illiterate
                                            Adults in Family <br><span
                                                class="text-xs text-gray-500 font-normal">নিরক্ষর প্রাপ্তবয়স্ক
                                                সংখ্যা</span></label>
                                        <input type="number" wire:model="formData.num_illiterate_adults"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>

                            {{-- Other Income Filters --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">D3</span>
                                        Other Income Filters | অন্যান্য আয়ের বিবরণ
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Holding
                                            Constitutional Post? <br><span
                                                class="text-xs text-gray-500 font-normal">সাংবিধানিক পদে আছেন
                                                কি?</span></label>
                                        <select wire:model="formData.has_constitutional_post"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                            <option value="No">No / না</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Constitutional
                                            Post Details <br><span class="text-xs text-gray-500 font-normal">সাংবিধানিক
                                                পদের বিবরণ</span></label>
                                        <input type="text" wire:model="formData.constitutional_post_details"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="Specify post details">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Registered under
                                            GST? <br><span class="text-xs text-gray-500 font-normal">জিএসটি নথিভুক্ত
                                                কি?</span></label>
                                        <select wire:model="formData.has_gst_reg"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                            <option value="No">No / না</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">GSTIN <br><span
                                                class="text-xs text-gray-500 font-normal">জিএসটিআইএন
                                                নম্বর</span></label>
                                        <input type="text" wire:model="formData.gstin" placeholder="GST Number"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Is Government
                                            Pensioner? <br><span class="text-xs text-gray-500 font-normal">সরকারি
                                                পেনশনভোগী কি?</span></label>
                                        <select wire:model="formData.has_pensioner"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                            <option value="No">No / না</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 gap-6 mt-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Government
                                            Pensioner Details <br><span
                                                class="text-xs text-gray-500 font-normal">পেনশনভোগীর
                                                বিবরণ</span></label>
                                        <input type="text" wire:model="formData.pensioner_details"
                                            placeholder="Specify pensioner details"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Member Specific Income/Employment/Literacy --}}
                            @php
                                $index = $activeMemberIndex - 1;
                            @endphp
                            @if (($members[$index]['member_type'] ?? 'adult') === 'adult')
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
                                    wire:key="member-income-{{ $index }}">
                                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                            <span
                                                class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                                style="background-color: #78350f;">D</span>
                                            Member #{{ $activeMemberIndex }} Income & Literacy | সদস্যের পেশা ও
                                            সাক্ষরতা
                                        </h3>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">PAN Card
                                                Number (If available) <br><span
                                                    class="text-xs text-gray-500 font-normal">প্যান কার্ড
                                                    নম্বর</span></label>
                                            <div x-data="{
                                                val: @entangle('members.' . $index . '.pan_no'),
                                                error: '',
                                                valid: false,
                                                check() {
                                                    if (!this.val) {
                                                        this.valid = false;
                                                        this.error = '';
                                                        return;
                                                    }
                                                    let cleaned = window.cleanInput.alphaNumericUpper(this.val, 10);
                                                    if (this.val !== cleaned) { this.val = cleaned; }
                                                    this.valid = window.checkValid.pan(this.val);
                                                    this.error = this.val.length > 0 && !this.valid ? 'Format: AAAAA1111A' : '';
                                                }
                                            }" x-init="check();
                                            $watch('val', () => check())">
                                                <input type="text" x-model="val" maxlength="10"
                                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono">
                                                <div class="flex items-center gap-2 mt-0.5"
                                                    style="min-height: 1.25rem;">
                                                    <span x-show="valid"
                                                        class="text-xs text-green-600 font-semibold">✓ Valid PAN</span>
                                                    <span x-show="error" x-text="error"
                                                        class="text-red-500 text-xs font-semibold"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nature of
                                                Employment <br><span
                                                    class="text-xs text-gray-500 font-normal">কর্মসংস্থানের
                                                    বিবরণ</span></label>
                                            <select wire:model="members.{{ $index }}.employment_nature"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">-- Select --</option>
                                                @foreach ($employmentNatures as $nature)
                                                    @php
                                                        $val = $nature;
                                                        if ($nature === 'Salaried, in Private Sector') {
                                                            $val = 'Salaried in Private';
                                                        } elseif (
                                                            $nature ===
                                                            'Formal Sector Self-Employed (Entrepreneur/Business/Proprietor/etc.)'
                                                        ) {
                                                            $val = 'Formal Sector Self-Employed';
                                                        } elseif (
                                                            $nature ===
                                                            'Informal Sector Self-Employed (Artisan/Craftsman/Farmer/etc.)'
                                                        ) {
                                                            $val = 'Informal Sector Self-Employed';
                                                        }
                                                    @endphp
                                                    <option value="{{ $val }}">
                                                        {{ $nature }}
                                                        @if ($nature === 'Government Sector')
                                                            / সরকারি চাকরি
                                                        @elseif($nature === 'Salaried, in Private Sector')
                                                            / বেসরকারি চাকরি
                                                        @elseif($nature === 'Formal Sector Self-Employed (Entrepreneur/Business/Proprietor/etc.)')
                                                            / স্ব-নিযুক্ত (আনুষ্ঠানিক)
                                                        @elseif($nature === 'Part-time job')
                                                            / খণ্ডকালীন কাজ
                                                        @elseif($nature === 'Informal Sector Self-Employed (Artisan/Craftsman/Farmer/etc.)')
                                                            / স্ব-নিযুক্ত (অনানুষ্ঠানিক)
                                                        @elseif($nature === 'Housewife')
                                                            / গৃহিণী
                                                        @elseif($nature === 'Unemployed')
                                                            / বেকার
                                                        @elseif($nature === 'Others')
                                                            / অন্যান্য
                                                        @endif
                                                    </option>
                                                @endforeach
                                                @if (!in_array('Migrant Labourer', $employmentNatures))
                                                    <option value="Migrant Labourer">Migrant Labourer / পরিযায়ী শ্রমিক
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Literacy
                                                Status (Member) <br><span
                                                    class="text-xs text-gray-500 font-normal">সদস্যের সাক্ষরতা
                                                    স্থিতি</span></label>
                                            <select wire:model="members.{{ $index }}.literate_status"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">-- Select --</option>
                                                <option value="Literate">Literate / সাক্ষর</option>
                                                <option value="Illiterate">Illiterate / নিরক্ষর</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Highest
                                                Educational Qualification <br><span
                                                    class="text-xs text-gray-500 font-normal">সর্বোচ্চ শিক্ষাগত
                                                    যোগ্যতা</span></label>
                                            <input type="text"
                                                wire:model="members.{{ $index }}.highest_qualification"
                                                placeholder="e.g. Graduate, Class X"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif


                {{-- SECTION E: OTHER IDENTITY DOCUMENTS --}}
                @if ($activeSection === 'other_docs')
                    <div class="space-y-6">
                        @if ($activeMemberIndex === 0)
                            {{-- Other Docs for HOF --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">E1</span>
                                        CAA (Citizenship Amendment Act) Status | নাগরিকত্ব সংশোধনী আইন স্থিতি
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">CAA Application
                                            Status <br><span class="text-xs text-gray-500 font-normal">সিএএ
                                                স্থিতি</span></label>
                                        <select wire:model.live="formData.hof_caa_status"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="Not Applicable">Not Applicable / প্রযোজ্য নয়</option>
                                            <option value="Applied">Applied / আবেদন করা হয়েছে</option>
                                            <option value="Issued">Issued / সংশাপত্র প্রদান করা হয়েছে</option>
                                        </select>
                                    </div>
                                    @if ($formData['hof_caa_status'] === 'Applied')
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">CAA
                                                Application Number * <br><span
                                                    class="text-xs text-gray-500 font-normal">সিএএ আবেদন
                                                    নম্বর</span></label>
                                            <input type="text" wire:model="formData.hof_caa_app_no"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    @elseif ($formData['hof_caa_status'] === 'Issued')
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">CAA
                                                Certificate Number * <br><span
                                                    class="text-xs text-gray-500 font-normal">সিএএ সংশাপত্র
                                                    নম্বর</span></label>
                                            <input type="text" wire:model="formData.hof_caa_cert_no"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Other Credit/Artisan Cards for HOF --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">E2</span>
                                        Other Credit / Artisan Cards | ক্রেডিট এবং কারিগর কার্ডের বিবরণ
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Card Type
                                            <br><span class="text-xs text-gray-500 font-normal">কার্ডের
                                                প্রকার</span></label>
                                        <select wire:model.live="formData.hof_kcc_type"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">None / কোনোটিই নয়</option>
                                            @foreach ($documentTypes as $docType)
                                                <option value="{{ $docType['value'] }}">
                                                    {{ $docType['label'] }}
                                                    @if ($docType['value'] === 'Artisan Credit Card')
                                                        / কারিগর ক্রেডিট কার্ড
                                                    @elseif($docType['value'] === 'Student CC')
                                                        / স্টুডেন্ট ক্রেডিট কার্ড
                                                    @elseif($docType['value'] === 'Others')
                                                        / অন্যান্য
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if (!empty($formData['hof_kcc_type']))
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Card ID
                                                Number * <br><span class="text-xs text-gray-500 font-normal">কার্ড আইডি
                                                    নম্বর</span></label>
                                            <input type="text" wire:model="formData.hof_kcc_id_no"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Issue Date
                                                <br><span class="text-xs text-gray-500 font-normal">প্রদানের
                                                    তারিখ</span></label>
                                            <input type="date" wire:model="formData.hof_kcc_date"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Issuing
                                                Authority <br><span
                                                    class="text-xs text-gray-500 font-normal">প্রদানকারী
                                                    কর্তৃপক্ষ</span></label>
                                            <input type="text" wire:model="formData.hof_kcc_issuing_authority"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- HOF SIR Status --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">E3</span>
                                        SIR (Survey of India Records) Tribunal pending Status | এসআইআর ট্রাইব্যুনাল
                                        স্থিতি
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">SIR Tribunal
                                            Status <br><span class="text-xs text-gray-500 font-normal">ট্রাইব্যুনাল
                                                স্থিতি</span></label>
                                        <select wire:model.live="formData.hof_sir_status"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="Not Applicable">Not Applicable / প্রযোজ্য নয়</option>
                                            <option value="No">No Pending Case / কোনো মামলা নেই</option>
                                            <option value="Yes">Yes, Case Pending / মামলা বিচারাধীন আছে</option>
                                        </select>
                                    </div>
                                    @if ($formData['hof_sir_status'] === 'Yes')
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">SIR Case
                                                Details * <br><span class="text-xs text-gray-500 font-normal">মামলার
                                                    বিবরণ</span></label>
                                            <input type="text" wire:model="formData.hof_sir_case_details"
                                                placeholder="Enter case details"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- Member Specific Other Docs --}}
                            @php
                                $index = $activeMemberIndex - 1;
                            @endphp
                            @if (($members[$index]['member_type'] ?? 'adult') === 'adult')
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
                                    wire:key="member-otherdocs-{{ $index }}">
                                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                            <span
                                                class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                                style="background-color: #78350f;">E</span>
                                            Member #{{ $activeMemberIndex }} CAA, Credit Card & SIR Status | সদস্যের
                                            পরিচয়পত্র স্থিতি
                                        </h3>
                                    </div>

                                    {{-- CAA status --}}
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-4 border-b border-gray-200">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">CAA
                                                Application Status <br><span
                                                    class="text-xs text-gray-500 font-normal">সিএএ
                                                    স্থিতি</span></label>
                                            <select wire:model.live="members.{{ $index }}.caa_status"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="Not Applicable">Not Applicable / প্রযোজ্য নয়</option>
                                                <option value="Applied">Applied / আবেদন করা হয়েছে</option>
                                                <option value="Issued">Issued / সংশাপত্র প্রদান করা হয়েছে</option>
                                            </select>
                                        </div>
                                        @if (($members[$index]['caa_status'] ?? '') === 'Applied')
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">CAA
                                                    Application Number * <br><span
                                                        class="text-xs text-gray-500 font-normal">সিএএ আবেদন
                                                        নম্বর</span></label>
                                                <input type="text"
                                                    wire:model="members.{{ $index }}.caa_app_no"
                                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                        @elseif (($members[$index]['caa_status'] ?? '') === 'Issued')
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">CAA
                                                    Certificate Number * <br><span
                                                        class="text-xs text-gray-500 font-normal">সিএএ সংশাপত্র
                                                        নম্বর</span></label>
                                                <input type="text"
                                                    wire:model="members.{{ $index }}.caa_cert_no"
                                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Credit card --}}
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 py-4 border-b border-gray-200">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Card Type
                                                <br><span class="text-xs text-gray-500 font-normal">কার্ডের
                                                    প্রকার</span></label>
                                            <select wire:model.live="members.{{ $index }}.kcc_type"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">None / কোনোটিই নয়</option>
                                                @foreach ($documentTypes as $docType)
                                                    <option value="{{ $docType['value'] }}">
                                                        {{ $docType['label'] }}
                                                        @if ($docType['value'] === 'Artisan Credit Card')
                                                            / কারিগর ক্রেডিট কার্ড
                                                        @elseif($docType['value'] === 'Student CC')
                                                            / স্টুডেন্ট ক্রেডিট কার্ড
                                                        @elseif($docType['value'] === 'Others')
                                                            / অন্যান্য
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if (!empty($members[$index]['kcc_type']))
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">Card ID
                                                    Number * <br><span class="text-xs text-gray-500 font-normal">কার্ড
                                                        আইডি নম্বর</span></label>
                                                <input type="text"
                                                    wire:model="members.{{ $index }}.kcc_id_no"
                                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">Issue
                                                    Date <br><span class="text-xs text-gray-500 font-normal">প্রদানের
                                                        তারিখ</span></label>
                                                <input type="date"
                                                    wire:model="members.{{ $index }}.kcc_date"
                                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">Issuing
                                                    Authority <br><span
                                                        class="text-xs text-gray-500 font-normal">প্রদানকারী
                                                        কর্তৃপক্ষ</span></label>
                                                <input type="text"
                                                    wire:model="members.{{ $index }}.kcc_issuing_authority"
                                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                        @endif
                                    </div>

                                    {{-- SIR Status --}}
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">SIR Tribunal
                                                Status <br><span
                                                    class="text-xs text-gray-500 font-normal">ট্রাইব্যুনাল
                                                    স্থিতি</span></label>
                                            <select wire:model.live="members.{{ $index }}.sir_status"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="Not Applicable">Not Applicable / প্রযোজ্য নয়</option>
                                                <option value="No">No Pending Case / কোনো মামলা নেই</option>
                                                <option value="Yes">Yes, Case Pending / মামলা বিচারাধীন আছে
                                                </option>
                                            </select>
                                        </div>
                                        @if (($members[$index]['sir_status'] ?? '') === 'Yes')
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">SIR Case
                                                    Details * <br><span
                                                        class="text-xs text-gray-500 font-normal">মামলার
                                                        বিবরণ</span></label>
                                                <input type="text"
                                                    wire:model="members.{{ $index }}.sir_case_details"
                                                    placeholder="Enter case details"
                                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif

                {{-- SECTION 5: DECLARATION --}}
                {{-- SECTION F: SOCIAL STATUS & DEPENDENTS --}}
                @if ($activeSection === 'social_dependents')
                    <div class="space-y-6">
                        @php
                            $index = $activeMemberIndex - 1;
                        @endphp
                        @if ($activeMemberIndex > 0 && ($members[$index]['member_type'] ?? 'adult') === 'child')
                            {{-- Child School details --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
                                wire:key="member-school-{{ $index }}">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">F1</span>
                                        School Attendance | বিদ্যালয়ে অধ্যয়নরত বিবরণ
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Class / Grade
                                            <br><span class="text-xs text-gray-500 font-normal">শ্রেণী</span></label>
                                        <input type="text"
                                            wire:model="members.{{ $index }}.school_grade"
                                            placeholder="e.g. Class IV"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">School Name
                                            <br><span class="text-xs text-gray-500 font-normal">বিদ্যালয়ের
                                                নাম</span></label>
                                        <input type="text" wire:model="members.{{ $index }}.school_name"
                                            placeholder="Enter School Name"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">School Type
                                            <br><span class="text-xs text-gray-500 font-normal">বিদ্যালয়ের
                                                প্রকার</span></label>
                                        <select wire:model.live="members.{{ $index }}.school_type"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            @foreach ($schoolTypes as $type)
                                                <option value="{{ $type }}">
                                                    {{ $type }}
                                                    @if ($type === 'Govt School')
                                                        / সরকারি বিদ্যালয়
                                                    @elseif($type === 'Govt Aided School')
                                                        / সরকারি সাহায্যপ্রাপ্ত বিদ্যালয়
                                                    @elseif($type === 'Govt Sponsored School')
                                                        / সরকারি স্পন্সরড বিদ্যালয়
                                                    @elseif($type === 'Madrasah')
                                                        / মাদ্রাসা
                                                    @elseif($type === 'Recognized Private School')
                                                        / স্বীকৃত বেসরকারি বিদ্যালয়
                                                    @elseif($type === 'Unrecognized Private School')
                                                        / অস্বীকৃত বেসরকারি বিদ্যালয়
                                                    @elseif($type === 'Others')
                                                        / অন্যান্য
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @if (($members[$index]['school_type'] ?? '') === 'Others')
                                    <div class="grid grid-cols-1 gap-6 mt-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Other School
                                                Type Details <br><span
                                                    class="text-xs text-gray-500 font-normal">অন্যান্য বিদ্যালয়
                                                    বিবরণ</span></label>
                                            <input type="text"
                                                wire:model="members.{{ $index }}.school_type_other"
                                                placeholder="Specify School Type"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Child Vaccination details --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
                                wire:key="member-vaccination-{{ $index }}">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">F2</span>
                                        Vaccination Status | শিশুর টিকাকরণ স্থিতি
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Vaccinated?
                                            <br><span class="text-xs text-gray-500 font-normal">টিকাকরণ করা
                                                হয়েছে?</span></label>
                                        <select wire:model="members.{{ $index }}.vaccination_status"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                            <option value="No">No / না</option>
                                            <option value="Partial">Partial / আংশিক</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Vaccination Card
                                            ID <br><span class="text-xs text-gray-500 font-normal">টিকা কার্ড
                                                আইডি</span></label>
                                        <input type="text"
                                            wire:model="members.{{ $index }}.vaccination_card_id"
                                            placeholder="Enter Card ID"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Last Date or
                                            Reason Skipped <br><span class="text-xs text-gray-500 font-normal">সর্বশেষ
                                                তারিখ বা বাদ দেওয়ার কারণ</span></label>
                                        <input type="text"
                                            wire:model="members.{{ $index }}.vaccination_skip_reason_or_date"
                                            placeholder="Date or skip reason"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        @else
                            <div
                                class="bg-amber-50 border border-amber-200 rounded-lg p-5 text-center text-amber-900">
                                <p class="text-xs font-semibold">Social Status & Dependents is only applicable for
                                    child members.</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- SECTION G: BENEFITS UNDER GOVERNMENT SCHEMES --}}
                @if ($activeSection === 'gov_benefits')
                    <div class="space-y-6">
                        @if ($activeMemberIndex === 0)
                            {{-- HOF Benefits --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                        <span
                                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                            style="background-color: #78350f;">G</span>
                                        Benefits under Government Schemes (HOF) | অন্যান্য সরকারি সুবিধা
                                    </h3>
                                </div>
                                <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                                    Select which schemes the Head of Family is currently receiving DBT benefits from.
                                    You can check the <strong>Opt Out</strong> box if they wish to voluntarily surrender
                                    the DBT benefit.
                                    <br><span class="text-[10px] text-gray-500">পরিবার প্রধান বর্তমানে কোন কোন সরকারি
                                        প্রকল্পে সুবিধা পাচ্ছেন তা চিহ্নিত করুন। সুবিধা প্রত্যাহার করতে চাইলে 'Opt Out'
                                        সিলেক্ট করুন।</span>
                                </p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Receiving DBT
                                            Benefits? <br><span class="text-[10px] text-gray-500">ডিবিটি সুবিধা পান
                                                কি?</span></label>
                                        <select wire:model.live="formData.hof_has_dbt_benefits"
                                            class="w-full border border-gray-300 rounded p-2 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="No">No / না</option>
                                            <option value="Yes">Yes / হ্যাঁ</option>
                                        </select>
                                    </div>
                                </div>

                                @if ($formData['hof_has_dbt_benefits'] === 'Yes')
                                    <div class="space-y-3">
                                        @for ($i = 0; $i < 5; $i++)
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4 p-2 bg-gray-50 rounded border border-gray-200">
                                                <div>
                                                    <select
                                                        wire:model="formData.hof_dbt_benefits.{{ $i }}.scheme_name"
                                                        class="w-full border border-gray-300 rounded p-1.5 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                                        <option value="">-- Select Scheme {{ $i + 1 }}
                                                            --</option>
                                                        @foreach ($benefitSchemes as $scheme)
                                                            <option value="{{ $scheme }}">
                                                                {{ $scheme }}
                                                                @if ($scheme === 'Others')
                                                                    / অন্যান্য
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                        @if (!in_array('Student Credit Card', $benefitSchemes))
                                                            <option value="Student Credit Card">Student Credit Card
                                                            </option>
                                                        @endif
                                                        @if (!in_array('Yuvashree', $benefitSchemes))
                                                            <option value="Yuvashree">Yuvashree</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox"
                                                        wire:model="formData.hof_dbt_benefits.{{ $i }}.opt_out"
                                                        id="hof_dbt_opt_out_{{ $i }}"
                                                        class="h-4 w-4 text-indigo-900 border-gray-300 rounded focus:ring-indigo-500">
                                                    <label for="hof_dbt_opt_out_{{ $i }}"
                                                        class="text-xs text-gray-700 font-medium">Voluntarily Opt Out?
                                                        / স্বেচ্ছায় সুবিধা ত্যাগ করতে চান</label>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        @else
                            {{-- Member Benefits --}}
                            @php
                                $index = $activeMemberIndex - 1;
                            @endphp
                            @if (($members[$index]['member_type'] ?? 'adult') === 'adult')
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
                                    wire:key="member-benefits-{{ $index }}">
                                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                            <span
                                                class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                                style="background-color: #78350f;">G</span>
                                            Benefits under Government Schemes (Member #{{ $activeMemberIndex }}) |
                                            অন্যান্য সরকারি সুবিধা
                                        </h3>
                                    </div>
                                    <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                                        Select which schemes this member is currently receiving DBT benefits from. You
                                        can check the <strong>Opt Out</strong> box if they wish to voluntarily surrender
                                        the DBT benefit.
                                    </p>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Receiving
                                                DBT Benefits? <br><span class="text-[10px] text-gray-500">ডিবিটি
                                                    সুবিধা পান কি?</span></label>
                                            <select wire:model.live="members.{{ $index }}.has_dbt_benefits"
                                                class="w-full border border-gray-300 rounded p-2 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="No">No / না</option>
                                                <option value="Yes">Yes / হ্যাঁ</option>
                                            </select>
                                        </div>
                                    </div>

                                    @if (($members[$index]['has_dbt_benefits'] ?? 'No') === 'Yes')
                                        <div class="space-y-3">
                                            @for ($i = 0; $i < 5; $i++)
                                                <div
                                                    class="grid grid-cols-1 md:grid-cols-2 gap-4 p-2 bg-gray-50 rounded border border-gray-200">
                                                    <div>
                                                        <select
                                                            wire:model="members.{{ $index }}.dbt_benefits.{{ $i }}.scheme_name"
                                                            class="w-full border border-gray-300 rounded p-1.5 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                                            <option value="">-- Select Scheme
                                                                {{ $i + 1 }} --</option>
                                                            @foreach ($benefitSchemes as $scheme)
                                                                <option value="{{ $scheme }}">
                                                                    {{ $scheme }}
                                                                    @if ($scheme === 'Others')
                                                                        / অন্যান্য
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                            @if (!in_array('Student Credit Card', $benefitSchemes))
                                                                <option value="Student Credit Card">Student Credit
                                                                    Card</option>
                                                            @endif
                                                            @if (!in_array('Yuvashree', $benefitSchemes))
                                                                <option value="Yuvashree">Yuvashree</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <input type="checkbox"
                                                            wire:model="members.{{ $index }}.dbt_benefits.{{ $i }}.opt_out"
                                                            id="m_{{ $index }}_dbt_opt_out_{{ $i }}"
                                                            class="h-4 w-4 text-indigo-900 border-gray-300 rounded focus:ring-indigo-500">
                                                        <label
                                                            for="m_{{ $index }}_dbt_opt_out_{{ $i }}"
                                                            class="text-xs text-gray-700 font-medium">Voluntarily Opt
                                                            Out? / সুবিধা ত্যাগ করতে চান</label>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                @endif

                {{-- SECTION H: DECLARATION & CONSENT --}}
                @if ($activeSection === 'declaration')
                    <div class="space-y-6">
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-5">
                            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                                    <span
                                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                                        style="background-color: #78350f;">H</span>
                                    Declaration & Consent | ঘোষণা এবং সম্মতি
                                </h3>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" wire:model="formData.agree_consent" id="agree_consent"
                                        class="mt-1 h-4 w-4 text-indigo-900 border-gray-300 rounded focus:ring-indigo-500">
                                    <label for="agree_consent"
                                        class="text-xs md:text-sm text-gray-700 font-medium leading-relaxed">
                                        I hereby declare that the above information is true to the best of my knowledge
                                        and I have provided all the supporting documents where applicable and HAVE NOT
                                        missed any criteria as mentioned above. I understand that my social protection
                                        benefits will be stopped if any information provided by me turns out to be
                                        false.
                                        <br>
                                        <span class="text-xs text-gray-500 font-normal italic">
                                            আমি ঘোষণা করছি যে আমার জ্ঞানত উপরোক্ত তথ্যগুলি সত্য এবং আমি প্রযোজ্য সমস্ত
                                            সহায়ক নথি প্রদান করেছি। আমি বুঝতে পারছি যে আমার দেওয়া কোনো তথ্য ভুল প্রমানিত
                                            হলে আমার সামাজিক সুরক্ষা সুবিধা বন্ধ করে দেওয়া হবে।
                                        </span>
                                    </label>
                                </div>
                                @error('formData.agree_consent')
                                    <div class="text-red-600 text-xs pl-7 font-semibold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
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
                                Next / এগিয়ে চলুন
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
                            Next / এগিয়ে চলুন
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

</form>
