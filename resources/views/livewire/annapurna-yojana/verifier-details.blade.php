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
        'pending'   => 'bg-amber-50 text-amber-700 border-amber-200',
        'verified'  => 'bg-blue-50 text-blue-700 border-blue-200',
        'approved'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'rejected'  => 'bg-red-50 text-red-700 border-red-200',
        'reverted'  => 'bg-orange-50 text-orange-700 border-orange-200',
        default     => 'bg-gray-50 text-gray-600 border-gray-200',
    };
@endphp

<div class="w-full space-y-6 pb-12" x-data="{ activeMemberTab: {} }">
    {{-- Toast messages --}}
    @if (session()->has('success'))
        <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-xl shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Top Action & Header Bar --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-gray-100 dark:border-gray-700">
        <div class="space-y-1">
            <div class="flex items-center gap-2 text-xs font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider">
                <a href="{{ route('annapurna-yojana-verification') }}" class="hover:underline">Verification List</a>
                <span>/</span>
                <span class="text-gray-400">Application Details</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-xl font-bold text-gray-800 dark:text-white leading-tight">
                    Family ID: #{{ $familyId }}
                </h1>
                <span class="px-3 py-1 text-xs font-bold border rounded-full {{ $statusColor($verificationStatus) }}">
                    {{ $verificationStatus ?? 'Pending' }}
                </span>
            </div>
            <p class="text-xs text-gray-400 font-mono">
                App UUID: {{ $family->application_id ?? 'N/A' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('annapurna-yojana-verification') }}" 
                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </a>

            @if(strtolower($verificationStatus) === 'pending')
                <button wire:click="revertApplication"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-orange-700 bg-orange-50 border border-orange-200 rounded-xl hover:bg-orange-100 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.334 4z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z" />
                    </svg>
                    Revert
                </button>

                <button wire:click="rejectApplication"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Reject
                </button>

                <button wire:click="verifyApplication"
                    class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl hover:from-emerald-600 hover:to-teal-700 transition shadow-md shadow-emerald-500/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Verify Application
                </button>
            @endif
        </div>
    </div>

    {{-- Main details panels grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Location & Area --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-2xl p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700 pb-3">
                <div class="p-2 bg-violet-50 dark:bg-violet-900/30 text-violet-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
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
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Agreed
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- Family demographics & Income details --}}
    <div class="bg-gradient-to-r from-violet-900 to-indigo-800 rounded-2xl p-6 text-white grid grid-cols-2 md:grid-cols-4 gap-4 text-center shadow-md">
        <div>
            <span class="text-violet-200 text-xs font-semibold uppercase block">Total Family Members</span>
            <span class="text-2xl font-extrabold">{{ $family->total_family_members ?? count($members) }}</span>
        </div>
        <div class="border-l border-violet-700/50">
            <span class="text-violet-200 text-xs font-semibold uppercase block">Literate Adults</span>
            <span class="text-2xl font-extrabold">{{ $family->no_of_literate_adults ?? 0 }}</span>
        </div>
        <div class="border-l border-violet-700/50">
            <span class="text-violet-200 text-xs font-semibold uppercase block">Illiterate Adults</span>
            <span class="text-2xl font-extrabold">{{ $family->no_of_illiterate_adults ?? 0 }}</span>
        </div>
        <div class="border-l border-violet-700/50">
            <span class="text-violet-200 text-xs font-semibold uppercase block font-bold">Annual Family Income</span>
            <span class="text-2xl font-extrabold text-emerald-300">₹{{ number_format($family->total_annual_family_income ?? 0) }}</span>
        </div>
    </div>

    {{-- Family Members List --}}
    <div class="space-y-4">
        <h3 class="text-md font-extrabold text-gray-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
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
                        <div class="w-10 h-10 rounded-full bg-violet-600 text-white flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr($m->member_name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
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
                        'education'  => 'Education & Vaccine',
                        'citizens'   => 'CAA & Tribunal'
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
                                    <span class="font-mono font-bold text-gray-800 dark:text-gray-200 block uppercase">
                                        {{ $m->pan_no ?? '—' }}
                                    </span>
                                    @if($m->pan_name)
                                        <span class="text-[10px] text-gray-400 block mt-0.5">Name on Card: {{ $m->pan_name }}</span>
                                    @endif
                                </div>
                                <div class="col-span-2 border-t pt-2 mt-1">
                                    <span class="text-gray-400 block mb-0.5">Other ID Details (No / Authority / Date)</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200 block">
                                        {{ $m->other_id_no ?? '—' }}
                                    </span>
                                    @if($m->other_id_issuing_authority || $m->other_id_issued_date)
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
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
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
                                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        Yes, Tax Payer
                                    @else
                                        No Tax Liability
                                    @endif
                                </span>
                            </div>
                            @if($m->professional_tax_profession)
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
                                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        Registered
                                    @else
                                        Not Registered
                                    @endif
                                </span>
                            </div>
                            @if($m->is_registered_gst && $m->gstin)
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
                            @if($m->owns_land && $m->landholding_size_decimals)
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
                            @if($m->holds_constitutional_post && $m->constitutional_post_member_no)
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
                            @if($m->is_govt_pensioner && $m->govt_pensioner_member_no)
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
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                        <span class="text-emerald-700">Covered</span>
                                    @else
                                        <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                        <span class="text-gray-500">Not Covered</span>
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
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Physically Challenged / Disability Profile</h5>
                            @if($m->disability_id_no)
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Disability Card ID</span>
                                        <span class="font-mono font-bold text-gray-800 dark:text-gray-200 bg-gray-50 px-2 py-0.5 rounded">{{ $m->disability_id_no }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Disability Percentage</span>
                                        <span class="font-extrabold text-sm text-red-600">{{ $m->disability_percentage ?? 0 }}%</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Issued Date</span>
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $m->disability_issued_date ?? '—' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Issuing Authority</span>
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $m->disability_issuing_authority ?? '—' }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="text-gray-400 text-center py-6">
                                    No disability record registered for this member.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 7. Education & Vaccine --}}
                    <div x-show="activeMemberTab['{{ $memberIdKey }}'] === 'education'" class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Literacy & Adult Education</h5>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Literacy Status</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200">{{ $m->literacy_status ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Highest Qualification</span>
                                <span class="font-bold text-indigo-700 dark:text-indigo-300">{{ $m->highest_educational_qualifications ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Child Schooling details</h5>
                            @if($m->school_name)
                                <div class="space-y-2">
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">School Name</span>
                                        <span class="font-bold text-gray-800 dark:text-gray-200 block">{{ $m->school_name }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-1">
                                        <div>
                                            <span class="text-gray-400 block mb-0.5">Grade / Standard</span>
                                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $m->school_grade ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 block mb-0.5">School Type</span>
                                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $m->school_type === 'Other' ? $m->school_type_other : ($m->school_type ?? '—') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-gray-400 text-center py-6">
                                    Not applicable / No school details.
                                </div>
                            @endif
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Immunization / Vaccination</h5>
                            @if($m->vaccination_status)
                                <div class="space-y-2">
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Vaccination Status</span>
                                        <span class="px-2 py-0.5 font-bold rounded {{ strtolower($m->vaccination_status) === 'fully' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ $m->vaccination_status }}
                                        </span>
                                    </div>
                                    @if($m->vaccination_card_id)
                                        <div>
                                            <span class="text-gray-400 block mb-0.5">Vaccination Card ID</span>
                                            <span class="font-mono font-bold text-gray-800 dark:text-gray-200 block">{{ $m->vaccination_card_id }}</span>
                                        </div>
                                    @endif
                                    @if($m->vaccination_skip_reason_or_date)
                                        <div>
                                            <span class="text-gray-400 block mb-0.5">Skip Reason / Dose Date</span>
                                            <span class="font-semibold text-gray-700 dark:text-gray-300 block">{{ $m->vaccination_skip_reason_or_date }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-gray-400 text-center py-6">
                                    No vaccine information.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 8. CAA & Tribunal --}}
                    <div x-show="activeMemberTab['{{ $memberIdKey }}'] === 'citizens'" class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">Citizenship Amendment Act (CAA) Status</h5>
                            @if($m->caa_application_no || $m->caa_certificate_no)
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="col-span-2">
                                        <span class="text-gray-400 block mb-0.5">CAA Application Status</span>
                                        <span class="px-2 py-0.5 font-bold rounded bg-gray-50 border border-gray-100 text-gray-700 block w-max">{{ $m->caa_application_status ?? 'Under Process' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">CAA Application Reference No</span>
                                        <span class="font-mono font-bold text-gray-800 dark:text-gray-200 block bg-gray-50 px-2 py-1 rounded">{{ $m->caa_application_no ?? '—' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">CAA Certificate Registration No</span>
                                        <span class="font-mono font-bold text-gray-800 dark:text-gray-200 block bg-gray-50 px-2 py-1 rounded">{{ $m->caa_certificate_no ?? '—' }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="text-gray-400 text-center py-6 bg-gray-50/20 rounded-xl">
                                    No CAA registration details recorded.
                                </div>
                            @endif
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wide border-b pb-2">SIR 2026 / Judicial Tribunal Details</h5>
                            @if($m->sir2026tribunal_status || $m->sir2026case_details)
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Tribunal Status</span>
                                        <span class="px-2 py-0.5 font-bold rounded bg-red-50 border border-red-100 text-red-700 block w-max uppercase">{{ $m->sir2026tribunal_status }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block mb-0.5">Case / Tribunal Appeal Details</span>
                                        <span class="font-semibold text-gray-700 dark:text-gray-300 block bg-gray-50/50 p-2.5 rounded-lg border border-gray-100 leading-normal">{{ $m->sir2026case_details }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="text-gray-400 text-center py-6 bg-gray-50/20 rounded-xl">
                                    No Tribunal or active court disputes listed.
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
</div>
