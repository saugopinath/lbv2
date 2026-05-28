@php
    /**
     * Helpers (available inside this blade only)
     */

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

    /** Build readable address from family row */
    $buildAddress = function ($row): string {
        $parts = [];
        if ($row->area_type === 'Rural') {
            if ($row->district) $parts[] = 'Dist: ' . $row->district;
            if ($row->block)    $parts[] = 'Block: ' . $row->block;
            if ($row->gp)       $parts[] = 'GP: ' . $row->gp;
            if ($row->ward)     $parts[] = 'Ward: ' . $row->ward;
        } else {
            if ($row->district) $parts[] = 'Dist: ' . $row->district;
            if ($row->ulb)      $parts[] = 'ULB: ' . $row->ulb;
            if ($row->ward)     $parts[] = 'Ward: ' . $row->ward;
        }
        return $parts ? implode(' / ', $parts) : '—';
    };

    /** Status colour map */
    $statusColor = fn(?string $s): string => match (strtolower((string)$s)) {
        'pending'   => 'bg-amber-100   text-amber-700   border-amber-300',
        'submitted' => 'bg-amber-100   text-amber-700   border-amber-300',
        'verified'  => 'bg-blue-100    text-blue-700    border-blue-300',
        'approved'  => 'bg-emerald-100 text-emerald-700 border-emerald-300',
        'rejected'  => 'bg-red-100     text-red-700     border-red-300',
        'reverted'  => 'bg-orange-100  text-orange-700  border-orange-300',
        default     => 'bg-gray-100    text-gray-600    border-gray-300',
    };

    /** Get dynamic workflow status based on next_level_role_id and status */
    $getWorkflowStatus = function ($row): string {
        $roleId = isset($row->next_level_role_id) ? (int)$row->next_level_role_id : 0;
        if ($roleId === 0) return 'Submitted';
        if ($roleId === 50) return 'Verified';
        if ($roleId === 100) return 'Approved';
        if ($roleId === -50 || strtolower($row->status ?? '') === 'reverted') return 'Reverted';
        if ($roleId < 0 || strtolower($row->status ?? '') === 'rejected') return 'Rejected';
        return $row->status ?? 'Submitted';
    };

    /** Row-group palette — alternates per family */
    $groupColors = [
        'bg-white',
        'bg-emerald-50/20',
    ];
@endphp

<div class="w-full space-y-4" wire:loading.class="opacity-60">

    {{-- Toast messages --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl shadow-sm flex items-center gap-3 animate-pulse">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- ── Stats Cards ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach ([
            ['label' => 'Total Applications',   'value' => $stats->total_families ?? 0,      'bgStyle' => 'linear-gradient(135deg, #0d9488 0%, #042f2e 100%)', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Total Family Members', 'value' => $stats->total_members  ?? 0,      'bgStyle' => 'linear-gradient(135deg, #0284c7 0%, #0c4a6e 100%)', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'],
            ['label' => 'Pending Approval',     'value' => $stats->pending_approval ?? 0,    'bgStyle' => 'linear-gradient(135deg, #d97706 0%, #7c2d12 100%)', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Approved Applications', 'value' => $stats->approved  ?? 0,          'bgStyle' => 'linear-gradient(135deg, #059669 0%, #064e3b 100%)', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as $card)
            <div class="relative overflow-hidden rounded-xl p-4 text-white shadow-md border border-white/5" style="background: {{ $card['bgStyle'] }};">
                <p class="text-xs font-medium opacity-80">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-bold">{{ number_format($card['value']) }}</p>
                <svg class="absolute -right-2 -bottom-2 w-14 h-14 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        @endforeach
    </div>

    {{-- ── Filters ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 space-y-3">
        <livewire:filter-lgd-master :button_show="1" />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
            {{-- Gender --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Gender</label>
                <select wire:model.live="gender"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Genders</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            {{-- Search --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Search</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </span>
                    <input wire:model.live.debounce.500ms="search"
                        type="text"
                        placeholder="App ID / Member Name / Aadhaar / Mobile"
                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                </div>
            </div>
        </div>

        @if ($search || $gender)
            <div class="flex justify-end">
                <button wire:click="resetFilters"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear All Filters
                </button>
            </div>
        @endif
    </div>

    {{-- ── Table ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div wire:loading.delay class="h-1 w-full bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-500 animate-pulse rounded-t-2xl"></div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-emerald-800 text-white text-xs uppercase tracking-wider">
                        <th class="px-4 py-3 text-left whitespace-nowrap">Temp. Application ID</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Family Members Name</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Mobile No</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Aadhaar No</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Age</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Gender</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Address</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y-0">
                    @forelse ($families as $familyId => $members)
                        @php
                            $first      = $members->first();
                            $colorClass = $groupColors[$loop->index % 2];
                            $memberCount = $members->count();
                        @endphp

                        @if (!$loop->first)
                            <tr><td colspan="9" class="h-px bg-gray-200 dark:bg-gray-600 p-0"></td></tr>
                        @endif

                        @foreach ($members as $mIdx => $member)
                            <tr class="{{ $colorClass }} hover:bg-emerald-50/40 dark:hover:bg-gray-700 transition-colors duration-100">
                                {{-- Application ID --}}
                                <td class="px-4 py-2.5 align-top whitespace-nowrap">
                                    @if ($mIdx === 0)
                                        <div class="flex flex-col gap-1">
                                            <span class="font-mono text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded border border-emerald-100">
                                                #{{ substr($first->application_id, 0, 8) }}…
                                            </span>
                                            <span class="text-[10px] text-gray-400">
                                                {{ $memberCount }} member{{ $memberCount > 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Member Name --}}
                                <td class="px-4 py-2.5 align-middle">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                            {{ $member->is_hof ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-300' }}">
                                            {{ strtoupper(substr($member->member_name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-100 leading-tight">{{ $member->member_name ?? '—' }}</p>
                                            @if ($member->is_hof)
                                                <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">★ Head of Family</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Mobile --}}
                                <td class="px-4 py-2.5 align-middle whitespace-nowrap text-gray-600 dark:text-gray-300 font-mono text-xs">
                                    {{ ($member->is_hof && $member->mobile_no) ? $member->mobile_no : '—' }}
                                </td>

                                {{-- Aadhaar --}}
                                <td class="px-4 py-2.5 align-middle whitespace-nowrap font-mono text-xs text-gray-700 dark:text-gray-300">
                                    {{ $maskAadhaar($member->aadhaar_no) }}
                                </td>

                                {{-- Age --}}
                                <td class="px-4 py-2.5 align-middle text-center font-semibold text-gray-700 dark:text-gray-300">
                                    {{ $calcAge($member->date_of_birth) }}
                                </td>

                                {{-- Gender --}}
                                <td class="px-4 py-2.5 align-middle text-center">
                                    @if ($member->gender)
                                        <span class="px-2 py-0.5 text-xs rounded-full font-medium
                                            {{ strtolower($member->gender) === 'male' ? 'bg-sky-100 text-sky-700' : (strtolower($member->gender) === 'female' ? 'bg-pink-100 text-pink-700' : 'bg-gray-100 text-gray-600') }}">
                                            {{ $member->gender }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- Address --}}
                                <td class="px-4 py-2.5 align-top text-xs text-gray-600 dark:text-gray-400 max-w-[200px]">
                                    @if ($mIdx === 0)
                                        <div class="leading-snug">
                                            @if ($first->area_type)
                                                <span class="inline-block px-1.5 py-0.5 text-[10px] rounded {{ $first->area_type === 'Rural' ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700' }} mb-1">
                                                    {{ $first->area_type }}
                                                </span>
                                            @endif
                                            <br>
                                            {{ $buildAddress($first) }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-2.5 align-top text-center whitespace-nowrap">
                                    @if ($mIdx === 0)
                                        @php
                                            $wfStatus = $getWorkflowStatus($first);
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold border rounded-md {{ $statusColor($wfStatus) }}">
                                            {{ $wfStatus }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-2.5 align-middle text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <a href="{{ route('annapurna-yojana-approval.details', ['family_id' => $member->family_id]) }}"
                                            title="View Form Details"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium
                                                   rounded-md border border-emerald-300 text-emerald-600 hover:bg-emerald-50 dark:border-emerald-700 dark:text-emerald-400 dark:hover:bg-emerald-950/20 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7 -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View Form
                                        </a>

                                        @php
                                            $roleId = isset($member->next_level_role_id) ? (int)$member->next_level_role_id : 0;
                                        @endphp

                                        @if($roleId === 50)
                                            <div class="flex flex-col gap-1 w-full">
                                                <button wire:click="openActionModal({{ $member->family_id }}, 'Approve')"
                                                    title="Approve Application"
                                                    class="inline-flex items-center justify-center gap-1 px-2.5 py-1 text-[11px] font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-md hover:from-emerald-600 hover:to-teal-700 shadow-sm transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Approve
                                                </button>
                                                <button wire:click="openActionModal({{ $member->family_id }}, 'Revert')"
                                                    title="Revert Application"
                                                    class="inline-flex items-center justify-center gap-1 px-2.5 py-1 text-[11px] font-medium rounded-md border border-orange-300 text-orange-700 hover:bg-orange-50 dark:border-orange-700 dark:text-orange-400 dark:hover:bg-orange-950/20 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                    </svg>
                                                    Revert
                                                </button>
                                            </div>
                                        @elseif($roleId === 100)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded">
                                                <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Approved
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <svg class="w-12 h-12 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm font-semibold text-gray-500">No verified applications pending approval</p>
                                    <p class="text-xs text-gray-400">Try adjusting your search or LGD filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <label for="perPage" class="font-medium">Rows per page:</label>
                <select id="perPage" wire:model.live="perPage"
                    class="border border-gray-200 rounded-md px-2 py-1 text-xs dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-emerald-500">
                    @foreach ([10, 15, 25, 50, 100] as $n)
                        <option value="{{ $n }}">{{ $n }}</option>
                    @endforeach
                </select>
                <span>
                    Showing <strong>{{ $paginator->firstItem() ?? 0 }}</strong>–<strong>{{ $paginator->lastItem() ?? 0 }}</strong> of <strong>{{ number_format($paginator->total()) }}</strong> families
                </span>
            </div>
            <div class="text-xs">
                {{ $paginator->links() }}
            </div>
        </div>
    </div>

    {{-- ── DECISION DIALOG MODAL (ALPINE + LIVEWIRE) ── --}}
    @if($showActionModal && $selectedFamilyId && isset($families[$selectedFamilyId]))
        @php
            $selectedMembers = $families[$selectedFamilyId];
            $selectedFamily = $selectedMembers->first();
            $hofMember = $selectedMembers->firstWhere('is_hof', true) ?? $selectedFamily;
            $hofName = $hofMember->member_name ?? '—';
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none bg-slate-900/60 backdrop-blur-sm">
            <div class="relative w-full max-w-lg mx-auto my-6 p-4">
                <div class="relative flex flex-col w-full bg-white dark:bg-gray-800 border-0 rounded-3xl shadow-2xl outline-none focus:outline-none">
                    
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-700 rounded-t-3xl bg-slate-50 dark:bg-gray-900/20">
                        <h3 class="text-lg font-black text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="fas fa-file-signature text-emerald-500"></i>
                            Approver Decision Panel
                        </h3>
                        <button type="button" wire:click="closeActionModal" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-full transition">
                            <i class="fas fa-times text-base"></i>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="relative p-6 flex-auto space-y-4 max-h-[70vh] overflow-y-auto">
                        
                        {{-- Application Summary --}}
                        <div class="bg-emerald-50/30 dark:bg-emerald-950/20 rounded-2xl p-4 border border-emerald-100/50 space-y-2">
                            <h4 class="text-xs font-bold text-emerald-600 uppercase tracking-wide">Application Summary</h4>
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div>
                                    <span class="text-gray-400 block font-medium">Family ID</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">#{{ $selectedFamilyId }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block font-medium">Applicant Head</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate block">{{ $hofName }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block font-medium">District</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $selectedFamily->district ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block font-medium">Total Members</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $selectedMembers->count() }} Members</span>
                                </div>
                            </div>
                        </div>

                        {{-- Operation Type --}}
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Operation Type</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center gap-2 p-3 rounded-2xl border cursor-pointer hover:bg-slate-50/50 transition {{ $modalOpType === 'Approve' ? 'bg-emerald-50/70 border-emerald-500 text-emerald-700 font-bold shadow-sm shadow-emerald-100' : 'border-gray-200 text-gray-400 dark:border-gray-700' }}">
                                    <input type="radio" wire:model.live="modalOpType" value="Approve" class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-xs">Approve</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 rounded-2xl border cursor-pointer hover:bg-slate-50/50 transition {{ $modalOpType === 'Revert' ? 'bg-orange-50/70 border-orange-500 text-orange-700 font-bold shadow-sm shadow-orange-100' : 'border-gray-200 text-gray-400 dark:border-gray-700' }}">
                                    <input type="radio" wire:model.live="modalOpType" value="Revert" class="text-orange-600 focus:ring-orange-500">
                                    <span class="text-xs">Revert</span>
                                </label>
                            </div>
                        </div>

                        {{-- Remarks --}}
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">
                                Operational Remarks 
                                @if($modalOpType === 'Revert')
                                    <span class="text-rose-500 font-bold lowercase">(required)</span>
                                @else
                                    <span class="text-gray-400 font-normal lowercase">(optional)</span>
                                @endif
                            </label>
                            <textarea wire:model.defer="modalRemarks" 
                                      rows="3" 
                                      placeholder="Provide descriptive remarks detailing your approval decision or reversion reasons..."
                                      class="w-full text-xs p-3.5 border rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 block shadow-sm"></textarea>
                            @error('modalRemarks')
                                <span class="text-[10px] font-bold text-rose-500 block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end p-5 border-t border-gray-100 dark:border-gray-700 rounded-b-3xl bg-slate-50/50">
                        <button type="button" wire:click="closeActionModal" class="px-5 py-2 mr-2 text-xs font-semibold text-gray-500 bg-white border rounded-xl hover:bg-gray-100 transition">
                            Cancel
                        </button>
                        <button type="button" wire:click="submitModalAction" wire:loading.attr="disabled" class="px-6 py-2 text-xs font-bold text-white rounded-xl shadow-md transition
                            {{ $modalOpType === 'Approve' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 shadow-emerald-500/10 hover:from-emerald-600 hover:to-teal-700' : 'bg-gradient-to-r from-orange-500 to-amber-600 shadow-orange-500/10 hover:from-orange-600 hover:to-amber-700' }}">
                            <span wire:loading.remove>Submit Action</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin mr-1"></i> Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
