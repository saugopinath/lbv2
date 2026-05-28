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
        'verified'  => 'bg-blue-100    text-blue-700    border-blue-300',
        'approved'  => 'bg-emerald-100 text-emerald-700 border-emerald-300',
        'rejected'  => 'bg-red-100     text-red-700     border-red-300',
        'reverted'  => 'bg-orange-100  text-orange-700  border-orange-300',
        default     => 'bg-gray-100    text-gray-600    border-gray-300',
    };

    /** Row-group palette — alternates per family */
    $groupColors = [
        'bg-white',
        'bg-violet-50/40',
    ];
@endphp

<div class="w-full space-y-4" wire:loading.class="opacity-60">

    {{-- ── Stats Cards ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach ([
            ['label' => 'Total Applications', 'value' => $stats->total_families ?? 0,  'color' => 'from-violet-500 to-indigo-600',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Total Members',      'value' => $stats->total_members  ?? 0,  'color' => 'from-sky-500 to-cyan-600',         'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'],
            ['label' => 'Pending',            'value' => $stats->pending        ?? 0,  'color' => 'from-amber-400 to-orange-500',     'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Verified',           'value' => $stats->verified       ?? 0,  'color' => 'from-emerald-500 to-teal-600',     'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as $card)
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br {{ $card['color'] }} p-4 text-white shadow-md">
                <p class="text-xs font-medium opacity-80">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-bold">{{ number_format($card['value']) }}</p>
                <svg class="absolute -right-2 -bottom-2 w-14 h-14 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        @endforeach
    </div>

    {{-- ── Filters ───────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 space-y-3">

        {{-- LGD Location Filter (District / Rural-Urban / Block-ULB / GP-Ward) --}}
        <livewire:filter-lgd-master :button_show="1" />

        {{-- Secondary filters: Gender + Free-text Search --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">

            {{-- Gender --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">
                    Gender
                </label>
                <select wire:model.live="gender"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg
                           focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                           dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Genders</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            {{-- Search --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">
                    Search
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </span>
                    <input wire:model.live.debounce.500ms="search"
                        type="text"
                        placeholder="App ID / Member Name / Aadhaar / Mobile"
                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg
                               focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                               dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                </div>
            </div>

        </div>

        {{-- Reset all --}}
        @if ($search || $gender)
            <div class="flex justify-end">
                <button wire:click="resetFilters"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium
                           text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear All Filters
                </button>
            </div>
        @endif
    </div>

    {{-- ── Table ─────────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

        {{-- Loading bar --}}
        <div wire:loading.delay
            class="h-1 w-full bg-gradient-to-r from-violet-500 via-indigo-500 to-violet-500
                   animate-pulse rounded-t-2xl">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-violet-800 text-white text-xs uppercase tracking-wider">
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

                        {{-- Top border between families --}}
                        @if (!$loop->first)
                            <tr><td colspan="9" class="h-px bg-gray-200 dark:bg-gray-600 p-0"></td></tr>
                        @endif

                        @foreach ($members as $mIdx => $member)
                            <tr class="{{ $colorClass }} hover:bg-violet-50/60 dark:hover:bg-gray-700 transition-colors duration-100">

                                {{-- Application ID — shown only for first member of group --}}
                                <td class="px-4 py-2.5 align-top whitespace-nowrap">
                                    @if ($mIdx === 0)
                                        <div class="flex flex-col gap-1">
                                            <span class="font-mono text-xs font-semibold text-indigo-700 dark:text-indigo-300
                                                         bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded border border-indigo-100">
                                                #{{ substr($first->application_id, 0, 8) }}…
                                            </span>
                                            <span class="text-[10px] text-gray-400">
                                                {{ $memberCount }} member{{ $memberCount > 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Member Name + HoF badge --}}
                                <td class="px-4 py-2.5 align-middle">
                                    <div class="flex items-center gap-2">
                                        {{-- Avatar initial --}}
                                        <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                            {{ $member->is_hof
                                                ? 'bg-violet-600 text-white'
                                                : 'bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-300' }}">
                                            {{ strtoupper(substr($member->member_name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-100 leading-tight">
                                                {{ $member->member_name ?? '—' }}
                                            </p>
                                            @if ($member->is_hof)
                                                <span class="text-[10px] font-semibold text-violet-600 dark:text-violet-400">
                                                    ★ Head of Family
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Mobile (shown only for HoF) --}}
                                <td class="px-4 py-2.5 align-middle whitespace-nowrap text-gray-600 dark:text-gray-300">
                                    @if ($member->is_hof && $member->mobile_no)
                                        <span class="font-mono text-xs">{{ $member->mobile_no }}</span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                    @endif
                                </td>

                                {{-- Aadhaar (masked) --}}
                                <td class="px-4 py-2.5 align-middle whitespace-nowrap">
                                    <span class="font-mono text-xs tracking-wider text-gray-700 dark:text-gray-300">
                                        {{ $maskAadhaar($member->aadhaar_no) }}
                                    </span>
                                </td>

                                {{-- Age --}}
                                <td class="px-4 py-2.5 align-middle text-center">
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        {{ $calcAge($member->date_of_birth) }}
                                    </span>
                                </td>

                                {{-- Gender --}}
                                <td class="px-4 py-2.5 align-middle text-center">
                                    @if ($member->gender)
                                        <span class="px-2 py-0.5 text-xs rounded-full font-medium
                                            {{ strtolower($member->gender) === 'male'
                                                ? 'bg-sky-100 text-sky-700'
                                                : (strtolower($member->gender) === 'female'
                                                    ? 'bg-pink-100 text-pink-700'
                                                    : 'bg-gray-100 text-gray-600') }}">
                                            {{ $member->gender }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- Address (shown only for first member) --}}
                                <td class="px-4 py-2.5 align-top text-xs text-gray-600 dark:text-gray-400 max-w-[200px]">
                                    @if ($mIdx === 0)
                                        <div class="leading-snug">
                                            @if ($first->area_type)
                                                <span class="inline-block px-1.5 py-0.5 text-[10px] rounded
                                                    {{ $first->area_type === 'Rural'
                                                        ? 'bg-emerald-100 text-emerald-700'
                                                        : 'bg-sky-100 text-sky-700' }} mb-1">
                                                    {{ $first->area_type }}
                                                </span>
                                            @endif
                                            <br>
                                            {{ $buildAddress($first) }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Status (shown only for first member) --}}
                                <td class="px-4 py-2.5 align-top text-center whitespace-nowrap">
                                    @if ($mIdx === 0)
                                        <span class="px-2 py-1 text-xs font-semibold border rounded-md
                                                     {{ $statusColor($first->status) }}">
                                            {{ $first->status ?? 'N/A' }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions (shown for each member) --}}
                                <td class="px-4 py-2.5 align-middle text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <a href="{{ route('annapurna-yojana-verification.details', ['family_id' => $member->family_id]) }}"
                                            title="View Form Details"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium
                                                   rounded-md border border-indigo-300 text-indigo-600
                                                   hover:bg-indigo-50 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                                        -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View Form
                                        </a>
                                        <button
                                            title="Verify Application"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium
                                                   rounded-md border border-emerald-300 text-emerald-700
                                                   hover:bg-emerald-50 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Verify
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @endforeach

                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <svg class="w-12 h-12 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                               M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm font-semibold text-gray-500">No applications found</p>
                                    <p class="text-xs text-gray-400">Try adjusting your search or filter criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- ── Footer: per-page + pagination ──────────────────────────────── --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3
                    border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">

            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <label for="perPage" class="font-medium">Rows per page:</label>
                <select id="perPage" wire:model.live="perPage"
                    class="border border-gray-200 rounded-md px-2 py-1 text-xs
                           dark:bg-gray-700 dark:border-gray-600 dark:text-white
                           focus:ring-2 focus:ring-violet-500">
                    @foreach ([10, 15, 25, 50, 100] as $n)
                        <option value="{{ $n }}">{{ $n }}</option>
                    @endforeach
                </select>
                <span>
                    Showing
                    <strong>{{ $paginator->firstItem() ?? 0 }}</strong>–<strong>{{ $paginator->lastItem() ?? 0 }}</strong>
                    of <strong>{{ number_format($paginator->total()) }}</strong> families
                </span>
            </div>

            <div class="text-xs">
                {{ $paginator->links() }}
            </div>
        </div>
    </div>

</div>
