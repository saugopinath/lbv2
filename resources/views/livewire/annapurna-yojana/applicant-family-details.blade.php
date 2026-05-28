@php
    /** Mask Aadhaar — show only last 4 digits */
    $maskAadhaar = fn(?string $no): string =>
        $no ? str_repeat('×', max(0, strlen(trim($no)) - 4)) . substr(trim($no), -4) : '—';

    /** Calculate age from a date string */
    $calcAge = function (?string $dob): string {
        if (!$dob) return '—';
        try {
            return (string) \Carbon\Carbon::parse($dob)->age;
        } catch (\Throwable) {
            return '—';
        }
    };

    /** Status colour map */
    $statusColor = fn(?string $s): string => match (strtolower((string)$s)) {
        'submitted' => 'bg-amber-50 text-amber-700 border-amber-200',
        'pending'   => 'bg-amber-50 text-amber-700 border-amber-200',
        'verified'  => 'bg-blue-50 text-blue-700 border-blue-200',
        'approved'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'rejected'  => 'bg-red-50 text-red-700 border-red-200',
        'reverted'  => 'bg-orange-50 text-orange-700 border-orange-200',
        default     => 'bg-gray-50 text-gray-600 border-gray-200',
    };

    $roleId = isset($family->next_level_role_id) ? (int)$family->next_level_role_id : 0;
@endphp

<div class="w-full space-y-6 pb-12" x-data="{ activeMemberTab: {} }">
    {{-- Toast messages --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl shadow-sm flex items-center gap-3 animate-pulse">
            <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl shadow-sm flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-rose-500 text-lg"></i>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- ── WORKFLOW STEPPER TIMELINE ── --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-2xl p-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="w-full md:w-auto">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Application Workflow Status</h3>
                <div class="flex items-center gap-3">
                    <span class="text-2xl font-black text-gray-800 dark:text-white">Family #{{ $familyId }}</span>
                    <span class="px-3 py-1 text-xs font-bold border rounded-full {{ $statusColor($verificationStatus) }}">
                        {{ $verificationStatus }}
                    </span>
                </div>
            </div>
            
            <div class="flex items-center justify-center gap-4 w-full md:w-auto overflow-x-auto py-2">
                {{-- STEP 1: Submitted --}}
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold {{ $roleId >= 0 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="text-xs">
                        <p class="font-bold text-gray-800 dark:text-gray-200">Submitted</p>
                        <p class="text-[10px] text-gray-400">By Operator</p>
                    </div>
                </div>
                <div class="w-12 h-0.5 {{ $roleId >= 50 ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>

                {{-- STEP 2: Verified --}}
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold {{ $roleId >= 50 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="text-xs">
                        <p class="font-bold {{ $roleId >= 50 ? 'text-blue-600' : 'text-gray-400' }}">Verified</p>
                        <p class="text-[10px] text-gray-400">By Verifier</p>
                    </div>
                </div>
                <div class="w-12 h-0.5 {{ $roleId >= 100 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>

                {{-- STEP 3: Approved --}}
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold {{ $roleId >= 100 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="text-xs">
                        <p class="font-bold {{ $roleId >= 100 ? 'text-emerald-600' : 'text-gray-400' }}">Approved</p>
                        <p class="text-[10px] text-gray-400">By Approver</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Action & Header Bar --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-gray-100 dark:border-gray-700">
        <div class="space-y-1">
            <div class="flex items-center gap-2 text-xs font-semibold {{ $isApprover ? 'text-emerald-600 dark:text-emerald-400' : 'text-violet-600 dark:text-violet-400' }} uppercase tracking-wider">
                @if($isApprover)
                    <a href="{{ route('annapurna-yojana-approval') }}" class="hover:underline">Approval List</a>
                @else
                    <a href="{{ route('annapurna-yojana-verification') }}" class="hover:underline">Verification List</a>
                @endif
                <span>/</span>
                <span class="text-gray-400">Application Details</span>
            </div>
            <p class="text-xs text-gray-400 font-mono">
                App UUID: {{ $family->application_id ?? 'N/A' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($isApprover)
                <a href="{{ route('annapurna-yojana-approval') }}" 
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    Back to List
                </a>
            @else
                <a href="{{ route('annapurna-yojana-verification') }}" 
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    Back to List
                </a>
            @endif
        </div>
    </div>

    {{-- Main details panels grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Location & Area --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-2xl p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700 pb-3">
                <div class="p-2 bg-violet-50 dark:bg-violet-900/30 text-violet-600 rounded-lg">
                    <i class="fas fa-map-marked-alt text-lg"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                    Location Mapping
                </h2>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-gray-400 block font-medium uppercase mb-0.5">Area Type</span>
                    <span class="px-2 py-0.5 font-bold rounded {{ $family->area_type === 'Rural' ? 'bg-emerald-50 text-emerald-700' : 'bg-sky-50 text-sky-700' }}">
                        {{ $family->area_type ?? '—' }}
                    </span>
                </div>
                <div>
                    <span class="text-gray-400 block font-medium uppercase mb-0.5">District</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $family->district ?? '—' }}</span>
                </div>
                @if($family->area_type === 'Rural')
                    <div>
                        <span class="text-gray-400 block font-medium uppercase mb-0.5">Block</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $family->block ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-medium uppercase mb-0.5">Gram Panchayat</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $family->gp ?? '—' }}</span>
                    </div>
                @else
                    <div>
                        <span class="text-gray-400 block font-medium uppercase mb-0.5">Municipality / ULB</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $family->ulb ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-medium uppercase mb-0.5">Ward</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $family->ward ?? '—' }}</span>
                    </div>
                @endif

                <div class="col-span-2 grid grid-cols-3 gap-2 pt-2 border-t border-gray-50 dark:border-gray-700">
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-medium">Dist LGD Code</span>
                        <span class="font-mono font-bold text-gray-700 dark:text-gray-300">{{ $family->lgd_district_code ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-medium">Block LGD Code</span>
                        <span class="font-mono font-bold text-gray-700 dark:text-gray-300">{{ $family->lgd_block_mc_code ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-medium">GP LGD Code</span>
                        <span class="font-mono font-bold text-gray-700 dark:text-gray-300">{{ $family->lgd_gp_ward_code ?? '—' }}</span>
                    </div>
                </div>

                <div class="col-span-2">
                    <span class="text-gray-400 block font-medium uppercase mb-0.5">Full Physical Address</span>
                    <span class="font-medium text-gray-700 dark:text-gray-300 block bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg border border-gray-100 dark:border-gray-700">
                        {{ $family->address ?? 'No physical address listed.' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Ration Card Information --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-2xl p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700 pb-3">
                <div class="p-2 bg-sky-50 dark:bg-sky-900/30 text-sky-600 rounded-lg">
                    <i class="fas fa-id-card text-lg"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                    Ration Information
                </h2>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div class="col-span-2">
                    <span class="text-gray-400 block font-medium uppercase mb-0.5">Has Digital Ration Card?</span>
                    <span class="inline-flex items-center gap-1.5 font-bold">
                        @if($family->has_digital_ration_card)
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-emerald-700">YES</span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span class="text-red-700">NO</span>
                        @endif
                    </span>
                </div>
                <div class="col-span-2">
                    <span class="text-gray-400 block font-medium uppercase mb-0.5">Ration Card Household ID</span>
                    <span class="font-mono text-sm font-bold text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-900 px-3 py-1 rounded border border-gray-100 dark:border-gray-700 block w-full">
                        {{ $family->ration_card_household_id ?? '—' }}
                    </span>
                </div>
                <div class="col-span-2">
                    <span class="text-gray-400 block font-medium uppercase mb-0.5">Lifting Monthly Ration?</span>
                    <span class="inline-flex items-center gap-1.5 font-bold">
                        @if($family->lifting_monthly_ration)
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-emerald-700">YES</span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span class="text-red-700">NO</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Electricity & Utilities --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-2xl p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700 pb-3">
                <div class="p-2 bg-amber-50 dark:bg-amber-900/30 text-amber-600 rounded-lg">
                    <i class="fas fa-bolt text-lg"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                    Electricity & Utilities
                </h2>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div class="col-span-2">
                    <span class="text-gray-400 block font-medium uppercase mb-0.5">Has Electricity Connection?</span>
                    <span class="inline-flex items-center gap-1.5 font-bold">
                        @if($family->has_electricity_connection)
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-emerald-700">YES</span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span class="text-red-700">NO</span>
                        @endif
                    </span>
                </div>
                @if($family->has_electricity_connection)
                    <div class="col-span-2">
                        <span class="text-gray-400 block font-medium uppercase mb-0.5">Electricity Consumer ID</span>
                        <span class="font-mono text-sm font-bold text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-900 px-3 py-1 rounded border border-gray-100 dark:border-gray-700 block w-full">
                            {{ $family->electricity_consumer_id ?? '—' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-medium uppercase mb-0.5">Monthly Power Consumed</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200 text-sm">
                            {{ $family->power_units_consumed ?? 0 }} <span class="text-[10px] text-gray-400 uppercase">Units</span>
                        </span>
                    </div>
                @endif
                <div>
                    <span class="text-gray-400 block font-medium uppercase mb-0.5">Is Agreed?</span>
                    <span class="inline-flex items-center gap-1 font-bold text-emerald-700">
                        <i class="fas fa-check-circle text-emerald-500"></i>
                        Agreed
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- Family demographics & Income details --}}
    <div class="rounded-2xl p-6 text-white grid grid-cols-2 md:grid-cols-4 gap-4 text-center shadow-md"
         style="background: {{ $isApprover 
             ? 'linear-gradient(135deg, #022c22 0%, #064e3b 50%, #0f766e 100%)' 
             : 'linear-gradient(135deg, #1e1b4b 0%, #2e1065 50%, #4c1d95 100%)' }}; border: 1px solid rgba(255,255,255,0.08);">
        <div>
            <span class="{{ $isApprover ? 'text-teal-100' : 'text-violet-200' }} text-xs font-semibold uppercase block">Total Family Members</span>
            <span class="text-2xl font-extrabold">{{ $family->total_family_members ?? count($members) }}</span>
        </div>
        <div class="border-l {{ $isApprover ? 'border-teal-700/50' : 'border-violet-700/50' }}">
            <span class="{{ $isApprover ? 'text-teal-100' : 'text-violet-200' }} text-xs font-semibold uppercase block">Literate Adults</span>
            <span class="text-2xl font-extrabold">{{ $family->no_of_literate_adults ?? 0 }}</span>
        </div>
        <div class="border-l {{ $isApprover ? 'border-teal-700/50' : 'border-violet-700/50' }}">
            <span class="{{ $isApprover ? 'text-teal-100' : 'text-violet-200' }} text-xs font-semibold uppercase block">Illiterate Adults</span>
            <span class="text-2xl font-extrabold">{{ $family->no_of_illiterate_adults ?? 0 }}</span>
        </div>
        <div class="border-l {{ $isApprover ? 'border-teal-700/50' : 'border-violet-700/50' }}">
            <span class="{{ $isApprover ? 'text-teal-100' : 'text-violet-200' }} text-xs font-semibold uppercase block font-bold">Annual Family Income</span>
            <span class="text-2xl font-extrabold text-emerald-300">₹{{ number_format($family->total_annual_family_income ?? 0) }}</span>
        </div>
    </div>

    {{-- Family Members List --}}
    <div class="space-y-4">
        <h3 class="text-md font-extrabold text-gray-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-users text-violet-600"></i>
            Family Members Profile & Verification Tabs
        </h3>

        @foreach($members as $index => $m)
            @php
                $memberIdKey = "m_" . $m->id;
            @endphp
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden" 
                 x-init="activeMemberTab['{{ $memberIdKey }}'] = 'kyc'">
                
                {{-- Profile Summary Header --}}
                <div class="p-5 flex flex-wrap items-center justify-between gap-4 bg-gray-50/50 dark:bg-gray-900/10 border-b border-gray-50 dark:border-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-violet-600 text-white flex items-center justify-center font-bold text-sm shadow-sm shadow-violet-200">
                            {{ strtoupper(substr($m->member_name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="font-bold text-gray-800 dark:text-white text-base leading-tight">
                                    {{ $m->member_name ?? '—' }}
                                </h4>
                                @if($m->is_hof)
                                    <span class="px-2 py-0.5 text-[9px] font-extrabold uppercase rounded bg-violet-100 text-violet-700 border border-violet-200">
                                        ★ Head of Family
                                    </span>
                                @endif
                                @if($m->applying_for_annapurna_bhandar)
                                    <span class="px-2 py-0.5 text-[9px] font-extrabold uppercase rounded bg-pink-100 text-pink-700 border border-pink-200 animate-pulse">
                                        Annapurna Bhandar Applicant
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-400 mt-1 font-mono">
                                <span>Relation: <strong class="text-gray-700 dark:text-gray-300">{{ $m->relation_with_head_of_family ?? ($m->is_hof ? 'Self' : '—') }}</strong></span>
                                <span>•</span>
                                <span>Aadhaar: <strong class="text-gray-700 dark:text-gray-300">{{ $maskAadhaar($m->aadhaar_no) }}</strong></span>
                                <span>•</span>
                                <span>Mobile: <strong class="text-gray-700 dark:text-gray-300">{{ $m->mobile_no ?? '—' }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-xs font-semibold">
                        <div class="text-right">
                            <span class="text-gray-400 block text-[9px] uppercase font-medium">Gender</span>
                            <span class="px-2 py-0.5 rounded font-bold
                                {{ strtolower($m->gender) === 'male' ? 'bg-sky-50 text-sky-700' : (strtolower($m->gender) === 'female' ? 'bg-pink-50 text-pink-700' : 'bg-gray-50 text-gray-600') }}">
                                {{ $m->gender ?? '—' }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-400 block text-[9px] uppercase font-medium">Age</span>
                            <span class="text-gray-800 dark:text-gray-200 font-bold">{{ $calcAge($m->date_of_birth) }} Yrs</span>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-400 block text-[9px] uppercase font-medium">Social Class</span>
                            <span class="text-gray-800 dark:text-gray-200 font-bold px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700">{{ $m->social_category ?? 'General' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Interactive Tabs Selector --}}
                <div class="flex flex-wrap border-b border-gray-100 dark:border-gray-700 bg-gray-50/20 px-4 text-xs font-medium text-gray-500">
                    @foreach([
                        'kyc'        => 'KYC & ID Documents',
                        'bank'       => 'Bank Details',
                        'profession' => 'Employment & Tax',
                        'housing'    => 'Housing & Vehicles',
                        'posts'      => 'Posts & Pensions',
                        'health'     => 'Health & Disability',
                    ] as $tabKey => $tabLabel)
                        <button @click="activeMemberTab['{{ $memberIdKey }}'] = '{{ $tabKey }}'"
                                :class="activeMemberTab['{{ $memberIdKey }}'] === '{{ $tabKey }}' 
                                    ? 'border-violet-600 text-violet-600 font-bold border-b-2' 
                                    : 'border-transparent hover:text-gray-700 hover:border-gray-300'"
                                class="px-4 py-3 transition duration-150">
                            {{ $tabLabel }}
                        </button>
                    @endforeach
                </div>

                {{-- Tab Contents --}}
                <div class="p-6">
                    
                    {{-- 1. KYC & ID Documents --}}
                    <div x-show="activeMemberTab['{{ $memberIdKey }}'] === 'kyc'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Aadhaar Profile</h5>
                            <div class="text-xs space-y-1">
                                <span class="text-gray-400 block font-medium">Aadhaar Number (Masked)</span>
                                <span class="font-mono text-sm font-bold text-gray-800 dark:text-gray-200">{{ $maskAadhaar($m->aadhaar_no) }}</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Electoral & Voter Identity</h5>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-gray-400 block mb-0.5">EPIC (Voter Card) No</span>
                                    <span class="font-mono font-bold text-gray-800 dark:text-gray-200 block bg-gray-50 dark:bg-gray-900 px-2 py-0.5 rounded">{{ $m->epic_no ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block mb-0.5">Assembly Constituency</span>
                                    <span class="font-bold text-gray-700 dark:text-gray-300">{{ $m->assembly_constituency_no ?? '—' }}</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-gray-400 block mb-0.5">Part Number</span>
                                    <span class="font-bold text-gray-700 dark:text-gray-300 font-mono">{{ $m->part_no ?? '—' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Other Identity Proofs</h5>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="col-span-2">
                                    <span class="text-gray-400 block mb-0.5">PAN Card Number</span>
                                    <span class="font-mono font-bold text-gray-800 dark:text-gray-200 block uppercase font-bold text-indigo-600">
                                        {{ $m->pan_no ?? '—' }}
                                    </span>
                                    @if(isset($m->pan_name) && $m->pan_name)
                                        <span class="text-[10px] text-gray-400 block mt-0.5">Name on Card: {{ $m->pan_name }}</span>
                                    @endif
                                </div>
                                <div class="col-span-2 border-t pt-2 mt-1">
                                    <span class="text-gray-400 block mb-0.5">Other ID Details (No / Authority / Date)</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200 block">
                                        {{ $m->other_id_no ?? '—' }}
                                    </span>
                                    @if(isset($m->other_id_issuing_authority) || isset($m->other_id_issued_date))
                                        <span class="text-[10px] text-gray-400 block">
                                            Issued by: {{ $m->other_id_issuing_authority ?? '—' }} on {{ $m->other_id_issued_date ?? '—' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Bank Details --}}
                    <div x-show="activeMemberTab['{{ $memberIdKey }}'] === 'bank'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="col-span-1 bg-violet-50/30 dark:bg-violet-900/10 p-5 rounded-2xl border border-violet-100/50 flex items-center justify-center">
                            <div class="text-center space-y-2">
                                <div class="w-12 h-12 rounded-full bg-violet-100 dark:bg-violet-900 flex items-center justify-center text-violet-600 mx-auto">
                                    <i class="fas fa-university text-xl"></i>
                                </div>
                                <h6 class="font-bold text-sm text-gray-800 dark:text-white">Direct Benefit Transfer</h6>
                                <p class="text-[10px] text-gray-400">Account verified for government incentive transfers.</p>
                            </div>
                        </div>
                        <div class="col-span-2 grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-gray-400 block font-medium uppercase mb-0.5">Bank Name</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200 text-sm block">
                                    {{ $m->bank_name ?? '—' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-400 block font-medium uppercase mb-0.5">Bank Account Number</span>
                                <span class="font-mono text-sm font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-3 py-1 rounded border border-indigo-100 w-full block">
                                    {{ $m->bank_account_no ?? '—' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-400 block font-medium uppercase mb-0.5">IFSC Code</span>
                                <span class="font-mono text-sm font-bold text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-900 px-3 py-1 rounded border border-gray-100 block">
                                    {{ $m->ifsc_code ?? '—' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Employment & Tax --}}
                    <div x-show="activeMemberTab['{{ $memberIdKey }}'] === 'profession'" class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Employment & Income</h5>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Govt Employment Type</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200 block">{{ $m->govt_employment_type ?? 'None / Unemployed' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Gross Annual Income (Individual)</span>
                                <span class="font-extrabold text-sm text-emerald-600 dark:text-emerald-400">₹{{ number_format($m->gross_annual_income ?? 0) }}</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Professional Tax Status</h5>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Pays Professional or Income Tax?</span>
                                <span class="inline-flex items-center gap-1 font-bold {{ $m->pays_income_or_professional_tax ? 'text-amber-600' : 'text-gray-500' }}">
                                    @if($m->pays_income_or_professional_tax)
                                        <i class="fas fa-check-circle text-amber-500"></i>
                                        Yes, Tax Payer
                                    @else
                                        No Tax Liability
                                    @endif
                                </span>
                            </div>
                            @if(isset($m->professional_tax_profession) && $m->professional_tax_profession)
                                <div>
                                    <span class="text-gray-400 block mb-0.5">Designated Profession / Trade</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $m->professional_tax_profession }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Business & GSTIN</h5>
                            <div>
                                <span class="text-gray-400 block mb-0.5">GST Registered?</span>
                                <span class="inline-flex items-center gap-1 font-bold {{ $m->is_registered_gst ? 'text-amber-600' : 'text-gray-500' }}">
                                    @if($m->is_registered_gst)
                                        <i class="fas fa-check-circle text-amber-500"></i>
                                        Registered
                                    @else
                                        Not Registered
                                    @endif
                                </span>
                            </div>
                            @if($m->is_registered_gst && isset($m->gstin) && $m->gstin)
                                <div>
                                    <span class="text-gray-400 block mb-0.5">GSTIN Number</span>
                                    <span class="font-mono font-bold text-gray-800 dark:text-gray-200 block bg-gray-50 dark:bg-gray-900 px-2 py-0.5 rounded">{{ $m->gstin }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 4. Housing & Vehicles --}}
                    <div x-show="activeMemberTab['{{ $memberIdKey }}'] === 'housing'" class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Real Estate / Pucca Rooms</h5>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Has 3 or More Pucca Rooms?</span>
                                <span class="inline-flex items-center gap-1.5 font-bold">
                                    @if($m->has_three_pucca_rooms)
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                        <span class="text-amber-700">YES (Exclusion Trigger)</span>
                                    @else
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-emerald-700">NO (Eligible)</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Land & Agriculture</h5>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Owns Agricultural / Residential Land?</span>
                                <span class="inline-flex items-center gap-1.5 font-bold">
                                    @if($m->owns_land)
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                        <span class="text-amber-700">YES</span>
                                    @else
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-emerald-700">NO</span>
                                    @endif
                                </span>
                            </div>
                            @if($m->owns_land && isset($m->landholding_size_decimals) && $m->landholding_size_decimals)
                                <div>
                                    <span class="text-gray-400 block mb-0.5">Landholding Size (in Decimals)</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $m->landholding_size_decimals }} Decimals</span>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Automobile Ownership</h5>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Owns a 4-Wheeler Vehicle?</span>
                                <span class="inline-flex items-center gap-1.5 font-bold">
                                    @if($m->has_four_wheeler)
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                        <span class="text-amber-700">YES (Exclusion)</span>
                                    @else
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-emerald-700">NO</span>
                                    @endif
                                </span>
                            </div>
                            @if($m->has_four_wheeler)
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Vehicle Count</span>
                                        <span class="font-bold text-gray-800 dark:text-gray-200">{{ $m->vehicle_count ?? 1 }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Vehicle Model</span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $m->vehicle_model ?? '—' }}</span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="text-gray-400 block mb-0.5">Registration Number</span>
                                        <span class="font-mono font-bold text-gray-800 dark:text-gray-200 uppercase bg-gray-50 px-2 py-0.5 rounded">{{ $m->vehicle_registration_no ?? '—' }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 5. Posts & Pensions --}}
                    <div x-show="activeMemberTab['{{ $memberIdKey }}'] === 'posts'" class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Constitutional Post</h5>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Holds Constitutional Post?</span>
                                <span class="inline-flex items-center gap-1.5 font-bold">
                                    @if($m->holds_constitutional_post)
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                                        <span class="text-amber-700 font-extrabold">YES (Exclusion)</span>
                                    @else
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-emerald-700">NO</span>
                                    @endif
                                </span>
                            </div>
                            @if($m->holds_constitutional_post && isset($m->constitutional_post_member_no) && $m->constitutional_post_member_no)
                                <div>
                                    <span class="text-gray-400 block mb-0.5">Constitutional Member Reference ID</span>
                                    <span class="font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $m->constitutional_post_member_no }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-4 col-span-2">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Government Pension Status</h5>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Is a Government Pensioner?</span>
                                <span class="inline-flex items-center gap-1.5 font-bold">
                                    @if($m->is_govt_pensioner)
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                        <span class="text-amber-700 font-extrabold">YES (Exclusion)</span>
                                    @else
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-emerald-700">NO</span>
                                    @endif
                                </span>
                            </div>
                            @if($m->is_govt_pensioner && isset($m->govt_pensioner_member_no) && $m->govt_pensioner_member_no)
                                <div>
                                    <span class="text-gray-400 block mb-0.5">Pension Scheme / PPO Card Number</span>
                                    <span class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded block w-max">{{ $m->govt_pensioner_member_no }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 6. Health & Disability --}}
                    <div x-show="activeMemberTab['{{ $memberIdKey }}'] === 'health'" class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Health Insurance</h5>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Has Health Insurance cover?</span>
                                <span class="inline-flex items-center gap-1.5 font-bold">
                                    @if($m->has_health_insurance)
                                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                                        <span class="text-emerald-700 font-bold">Covered</span>
                                    @else
                                        <span class="w-2.5 h-2.5 bg-gray-300 rounded-full"></span>
                                        <span class="text-gray-500 font-medium">Not Covered</span>
                                    @endif
                                </span>
                            </div>
                            @if($m->has_health_insurance)
                                <div class="grid grid-cols-2 gap-2 border-t pt-2">
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Insurance Type / Scheme</span>
                                        <span class="font-bold text-gray-800 dark:text-gray-200">{{ $m->health_insurance_type ?? '—' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Sum Assured</span>
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400">₹{{ number_format($m->health_insurance_sum_assured ?? 0) }}</span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="text-gray-400 block mb-0.5">Annual Premium Paid</span>
                                        <span class="font-bold text-gray-700 dark:text-gray-300">₹{{ number_format($m->health_insurance_annual_premium ?? 0) }} / year</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-4 font-xs">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Physically Challenged / Disability Profile</h5>
                            @if(isset($m->disability_id_no) && $m->disability_id_no)
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Disability Card ID</span>
                                        <span class="font-mono font-bold text-gray-800 dark:text-gray-200 block bg-gray-50 px-2 py-0.5 rounded">{{ $m->disability_id_no }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Disability Level (%)</span>
                                        <span class="font-bold text-rose-600 text-sm block">{{ $m->disability_percentage ?? 0 }}% Challenged</span>
                                    </div>
                                    @if(isset($m->disability_category) && $m->disability_category)
                                        <div class="col-span-2">
                                            <span class="text-gray-400 block mb-0.5">Designated Disability Category</span>
                                            <span class="font-bold text-gray-700 dark:text-gray-300">{{ $m->disability_category }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="p-3 bg-gray-50/50 rounded-xl text-center border text-gray-400">
                                    No physical disability recorded.
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        @endforeach
    </div>

</div>
