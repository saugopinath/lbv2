@php
    $pageItems = $page ?? collect();
    $personal = $applicantInfo ?? $pageItems->first()?->personaldetails;
    $revertInfo = $pageItems->first()?->acceptRejectInfo;
    $schemeId = $schemeId ?? ($pageItems->first()?->scheme_id ?? null);
    $schemeName = $personal?->scheme?->name 
        ?? \App\Models\Scheme::where('id', $schemeId)->value('name') 
        ?? 'Scheme #' . $schemeId;
    $contact = $personal?->contact ?? \App\Models\BeneficiaryContactDetail::where('application_id', $id)->first();
    $districtName = !empty($contact?->district_id) ? (\App\Models\District::where('lgd_code', $contact->district_id)->orWhere('id', $contact->district_id)->value('name') ?? 'District ' . $contact->district_id) : null;
    $blockName = !empty($contact?->blockurban) ? (\App\Models\Block::where('lgd_code', $contact->blockurban)->orWhere('id', $contact->blockurban)->value('name') ?? 'Block ' . $contact->blockurban) : null;
    $gpName = !empty($contact?->gpward) ? (\App\Models\Panchayat::where('lgd_code', $contact->gpward)->orWhere('id', $contact->gpward)->value('name') ?? 'GP ' . $contact->gpward) : null;
@endphp

<div class="w-full max-w-full min-w-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-2xl overflow-hidden mb-8">
    {{-- Page Header --}}
    <div class="p-4 sm:p-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/40 flex flex-wrap items-center justify-between gap-4 w-full max-w-full min-w-0">
        <div>
            <div class="flex items-center gap-2.5">
                @if ($stage === 'verifier')
                    <span class="p-2 rounded-xl bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Update Incomplete Details</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Verify and update applicant information and upload mandatory verification enclosures
                        </p>
                    </div>
                @elseif ($stage === 'revert')
                    <span class="p-2 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Revert Incomplete Details</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Review approver remarks, rectify details, re-upload documents, and resubmit to approver
                        </p>
                    </div>
                @elseif ($stage === 'approver')
                    <span class="p-2 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Approver Review &amp; Decision</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Review verifier submissions against old records and approve or revert with remarks
                        </p>
                    </div>
                @else
                    <span class="p-2 rounded-xl bg-violet-100 text-violet-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Incomplete Details</h1>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Controls: Stage Badge & Back to Queue Button --}}
        <div class="flex flex-wrap items-center gap-2.5">
            @if ($stage === 'revert')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-700">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Revert Queue
                </span>
            @elseif ($stage === 'approver')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-700">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    Approver Queue
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-violet-50 dark:bg-violet-900/30 text-violet-800 dark:text-violet-300 border border-violet-200 dark:border-violet-700">
                    <span class="w-2 h-2 rounded-full bg-violet-600"></span>
                    Verifier Queue
                </span>
            @endif

            <a href="{{ route('incomplete.types', $stage === 'revert' ? 'verifier' : $stage) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-xs">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to Queue</span>
            </a>
        </div>
    </div>

    {{-- Main Container Body --}}
    <div class="p-4 sm:p-6 space-y-6 w-full max-w-full min-w-0">

        {{-- Alerts --}}
        @if ($errors->has('duplicate_check'))
            <div class="p-4 rounded-xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/30 text-rose-800 dark:text-rose-300 shadow-xs flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider mb-1">Duplicate Record Detected</h4>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach ($errors->get('duplicate_check') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/30 text-rose-800 dark:text-rose-300 shadow-xs flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <strong class="font-bold">Error:</strong> {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- Revert Details Notice Banner (When in Revert Queue) --}}
        @if ($stage === 'revert' || !empty($revertInfo))
            <div class="p-4 sm:p-5 rounded-xl border border-amber-300 dark:border-amber-700 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-950/40 dark:to-orange-950/20 text-amber-900 dark:text-amber-200 shadow-xs">
                <div class="flex items-start gap-3.5">
                    <div class="p-2 rounded-xl bg-amber-500 text-white shadow-xs flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                            <h3 class="text-sm font-bold text-amber-900 dark:text-amber-100 uppercase tracking-wider">
                                Application Returned by Approver for Correction
                            </h3>
                            @if(!empty($revertInfo?->created_at))
                                <span class="text-xs text-amber-700 dark:text-amber-300">
                                    Reverted on {{ $revertInfo->created_at->format('d M Y, h:i A') }}
                                </span>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2 text-xs">
                            <div class="p-3 bg-white/80 dark:bg-gray-800/80 rounded-lg border border-amber-200 dark:border-amber-800">
                                <span class="font-bold text-amber-800 dark:text-amber-400 block mb-0.5 uppercase tracking-wider">Revert Reason Cause:</span>
                                <span class="text-gray-800 dark:text-gray-200 font-medium">
                                    {{ $revertInfo?->revertReason?->name ?? 'Specific issue flagged by Approver' }}
                                </span>
                            </div>
                            <div class="p-3 bg-white/80 dark:bg-gray-800/80 rounded-lg border border-amber-200 dark:border-amber-800">
                                <span class="font-bold text-amber-800 dark:text-amber-400 block mb-0.5 uppercase tracking-wider">Approver Remarks:</span>
                                <span class="text-gray-800 dark:text-gray-200 italic">
                                    "{{ $revertInfo?->revert_reason_remarks ?? 'Please correct the indicated incomplete fields and upload fresh verification enclosures.' }}"
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Beneficiary Information Summary Card --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-5 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </span>
                    <h2 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                        Beneficiary Profile
                    </h2>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-semibold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        Application ID: #{{ $id }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold bg-violet-50 dark:bg-violet-900/40 text-violet-800 dark:text-violet-200 border border-violet-200 dark:border-violet-700">
                        <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span>Scheme: {{ $schemeName }}</span>
                    </span>
                </div>
            </div>

            {{-- Beneficiary Complete Details Grid (Personal & Location) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                {{-- Beneficiary Name --}}
                <div class="p-3 rounded-lg bg-gray-50/80 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500 dark:text-gray-400 font-medium block mb-1">Beneficiary Name</span>
                    <span class="text-sm font-bold text-gray-800 dark:text-white">
                        {{ $personal?->beneficiary_name ?? 'N/A' }}
                    </span>
                </div>

                {{-- Father / Spouse Name --}}
                <div class="p-3 rounded-lg bg-gray-50/80 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500 dark:text-gray-400 font-medium block mb-1">Father / Spouse Name</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white">
                        {{ $personal?->ben_father_name ?? ($personal?->ben_spouse_name ?? 'N/A') }}
                    </span>
                </div>

                {{-- Gender & Caste --}}
                <div class="p-3 rounded-lg bg-gray-50/80 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500 dark:text-gray-400 font-medium block mb-1">Gender &amp; Category</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white">
                        {{ $personal?->gender ?? 'Not Specified' }} • Caste: {{ $personal?->caste ?? 'General' }}
                    </span>
                </div>

                {{-- Date of Birth / Age --}}
                <div class="p-3 rounded-lg bg-gray-50/80 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500 dark:text-gray-400 font-medium block mb-1">Date of Birth / Age</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white">
                        {{ !empty($personal?->dob) ? \Carbon\Carbon::parse($personal->dob)->format('d-m-Y') : 'N/A' }}
                        @if(!empty($personal?->age))
                            <span class="text-gray-500 dark:text-gray-400 text-xs">({{ $personal->age }} yrs)</span>
                        @endif
                    </span>
                </div>

                {{-- Registered Mobile --}}
                <div class="p-3 rounded-lg bg-gray-50/80 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500 dark:text-gray-400 font-medium block mb-1">Contact Mobile Number</span>
                    <span class="text-sm font-semibold text-gray-800 dark:text-white font-mono">
                        {{ $personal?->contactdetails?->mobile_no ?? ($contact?->mobile_no ?? ($personal?->other_details['mobile_no'] ?? 'N/A')) }}
                    </span>
                </div>

                {{-- Village / City / Premises --}}
                <div class="p-3 rounded-lg bg-gray-50/80 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500 dark:text-gray-400 font-medium block mb-1">Village / City / Premises</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white truncate block">
                        {{ $contact?->villtowncity ?? 'N/A' }}
                        @if(!empty($contact?->housepremiseno))
                            <span class="text-gray-500 text-xs">(#{{ $contact->housepremiseno }})</span>
                        @endif
                    </span>
                </div>

                {{-- Post Office & PIN --}}
                <div class="p-3 rounded-lg bg-gray-50/80 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500 dark:text-gray-400 font-medium block mb-1">Post Office &amp; PIN Code</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white">
                        {{ $contact?->postoffice ?? 'N/A' }}
                        @if(!empty($contact?->pincode))
                            <span class="font-mono text-gray-600 dark:text-gray-300">- {{ $contact->pincode }}</span>
                        @endif
                    </span>
                </div>

                {{-- District / Block / GP --}}
                <div class="p-3 rounded-lg bg-gray-50/80 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500 dark:text-gray-400 font-medium block mb-1">District / Block / GP</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white truncate block">
                        {{ $districtName ?? 'N/A' }}{{ !empty($blockName) ? ' • ' . $blockName : '' }}{{ !empty($gpName) ? ' • ' . $gpName : '' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Issues Requiring Attention Strip --}}
        <div class="p-4 rounded-xl border border-amber-200 dark:border-amber-800/60 bg-amber-50/40 dark:bg-amber-950/20">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-2.5">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h3 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">
                        Issues Requiring Correction ({{ count($pageItems) }})
                    </h3>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Provide verified details and required supporting enclosures for each section below
                </span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach ($pageItems as $issue)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white dark:bg-gray-800 text-amber-900 dark:text-amber-200 border border-amber-300 dark:border-amber-700 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>{{ strtoupper($issue->incompleteType?->name ?? 'Pending Issue') }}</span>
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Main Form / Input Sections --}}
        <form method="POST"
              action="{{ $stage === 'revert' ? route('incomplete-revert-update', ['id' => encrypt($id), 'schemeId' => encrypt($schemeId)]) : route('incomplete-full-details-update', ['id' => encrypt($id), 'schemeId' => encrypt($schemeId)]) }}"
              class="space-y-6">
            @csrf

            {{-- Aadhaar Modification Section --}}
            @if (!empty($aadhaarIssues))
                <x-incomplete.aadhaar-modification :aadhaar-issues="$aadhaarIssues" :schemeId="$schemeId" :stage="$stage" />
            @endif

            {{-- Mobile Modification Section --}}
            @if (!empty($mobileIssues))
                <x-incomplete.mobile-issues :mobile-issues="$mobileIssues" :stage="$stage" />
            @endif

            {{-- Bank & Payment Failure Modification Sections --}}
            @if (!empty($sortedBankIssues))
                @foreach ($sortedBankIssues as $item)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-xs">
                        <div class="flex items-center gap-2.5 pb-3 mb-4 border-b border-gray-100 dark:border-gray-700">
                            <span class="p-1.5 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                                    {{ $item->incompleteType?->name ?? 'Bank / Payment Related Issue' }}
                                </h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    Bank Account Verification &amp; Passbook Enclosure
                                </span>
                            </div>
                        </div>

                        @if ($item->incomplete_type == '1411')
                            <livewire:incomplete.dup-bank :item="$item" :schemeId="$schemeId" :dupAction="$item->dupAction" :stage="$stage" :wire:key="'dup-'.$item->id" />
                        @elseif ($item->incomplete_type == '145')
                            <livewire:incomplete.bank-name-fail :item="$item" :schemeId="$schemeId" :dupAction="$item->dupAction" :stage="$stage" :wire:key="'name-'.$item->id" />
                        @elseif ($item->incomplete_type == '146')
                            <livewire:incomplete.bank-account-fail :item="$item" :schemeId="$schemeId" :dupAction="$item->dupAction" :stage="$stage" :wire:key="'account-'.$item->id" />
                        @elseif ($item->incomplete_type == '1412')
                            <livewire:incomplete.mismatch-low :item="$item" :schemeId="$schemeId" :dupAction="$item->dupAction" :stage="$stage" :wire:key="'mismatch-low-'.$item->id" />
                        @elseif ($item->incomplete_type == '1413')
                            <livewire:incomplete.mismatch-high :item="$item" :schemeId="$schemeId" :dupAction="$item->dupAction" :stage="$stage" :wire:key="'mismatch-high-'.$item->id" />
                        @endif
                    </div>
                @endforeach
            @endif

            {{-- Bottom Action Footer Bar --}}
            <div class="mt-6 pt-5 border-t border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                <a href="{{ route('incomplete.types', $stage === 'revert' ? 'verifier' : $stage) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-xs">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Cancel &amp; Return to List
                </a>

                <div class="flex flex-wrap items-center gap-3">
                    @if ($stage === 'verifier')
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 text-xs font-bold rounded-lg bg-violet-700 hover:bg-violet-800 text-white shadow-sm transition cursor-pointer"
                                onclick="return confirm('Are you sure you want to submit the updated details to the Approver for approval?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <span>Submit Verification to Approver</span>
                        </button>
                    @elseif ($stage === 'revert')
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 text-xs font-bold rounded-lg bg-amber-600 hover:bg-amber-700 text-white shadow-sm transition cursor-pointer"
                                onclick="return confirm('Are you sure you want to resubmit the corrected application to the Approver?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Resubmit Corrected Details to Approver</span>
                        </button>
                    @elseif ($stage === 'approver')
                        <div class="flex items-center gap-3">
                            <button type="button"
                                    x-on:click="$dispatch('open-revert-modal')"
                                    class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                <span>Revert</span>
                            </button>

                            <button type="button"
                                    x-on:click="if(confirm('Are you sure you want to approve this request?')) { $wire.approve(); }"
                                    class="inline-flex items-center gap-1.5 px-6 py-2.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Approve</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </form>

        {{-- Approver Revert Modal --}}
        @if ($stage === 'approver')
            <div x-data="{ open: false }"
                 x-on:open-revert-modal.window="open = true"
                 x-show="open"
                 x-cloak
                 class="fixed inset-0 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs z-50 p-4"
                 style="display:none">

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-md border border-gray-200 dark:border-gray-700"
                     @click.outside="open = false">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-rose-100 dark:bg-rose-900/40 text-rose-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </span>
                            <h2 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                                Revert Application
                            </h2>
                        </div>
                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        Please select a reason and provide remarks for reverting this application back to the verifier queue.
                    </p>

                    {{-- Revert Reason Dropdown --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Revert Reason <span class="text-rose-500">*</span>
                        </label>
                        <select name="revert_reason_cause_id"
                                id="revert_reason_cause_id"
                                wire:model.live="revert_reason_cause_id"
                                class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-xs focus:ring-2 focus:ring-rose-500 py-2 px-3">
                            <option value="">-- Select Reason --</option>
                            @foreach ($revertReasons as $reason)
                                <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                            @endforeach
                        </select>
                        @error('revert_reason_cause_id')
                            <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Remarks Textarea --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Remarks <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="revert_reason_remarks"
                                  name="revert_reason_remarks"
                                  wire:model="revert_reason_remarks"
                                  rows="3"
                                  placeholder="Provide instructions on what needs correction..."
                                  class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-xs focus:ring-2 focus:ring-rose-500 py-2 px-3"></textarea>
                        @error('revert_reason_remarks')
                            <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Modal Buttons --}}
                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <button type="button"
                                @click="open = false"
                                class="px-4 py-2 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 transition cursor-pointer">
                            Cancel
                        </button>

                        <button type="button"
                                wire:click="validateRevert"
                                class="px-5 py-2 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition cursor-pointer">
                            Submit
                        </button>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('livewire:init', () => {
                    Livewire.on('confirm-revert', () => {
                        if (confirm('Are you sure you want to revert this request?')) {
                            Livewire.dispatch('do-revert');
                        }
                    });
                });
            </script>
        @endif
    </div>
</div>
