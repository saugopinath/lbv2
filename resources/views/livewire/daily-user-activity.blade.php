<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">

    <!-- Header -->
    <div class="p-8 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-50/50 to-transparent dark:from-gray-800/50">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-2xl shadow-inner">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400 tracking-tight">Daily User Activity</h3>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="p-8 bg-gray-50/50 dark:bg-gray-800/50">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <!-- Date Range -->
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 dark:text-gray-300 ml-1 uppercase tracking-widest">Date Time Range</label>
                <div class="relative group">
                    <input wire:model.live="dateRange" type="date" placeholder="Select Date Range"
                        class="w-full pl-4 pr-10 py-3 bg-white dark:bg-gray-700 border-2 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all duration-200 text-gray-700 dark:text-gray-200">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 group-hover:text-blue-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Name Filter -->
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 dark:text-gray-300 ml-1 uppercase tracking-widest">Name</label>
                <div class="relative group">
                    <input wire:model.live.debounce.300ms="name" type="text" placeholder="Search by name..."
                        class="w-full pl-4 pr-10 py-3 bg-white dark:bg-gray-700 border-2 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all duration-200 text-gray-700 dark:text-gray-200">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 group-hover:text-blue-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Username Filter -->
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 dark:text-gray-300 ml-1 uppercase tracking-widest">Username</label>
                <div class="relative group">
                    <input wire:model.live.debounce.300ms="username" type="text" placeholder="Search by username..."
                        class="w-full pl-4 pr-10 py-3 bg-white dark:bg-gray-700 border-2 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all duration-200 text-gray-700 dark:text-gray-200">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 group-hover:text-blue-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Search Button (Visual Only for now as inputs are live) -->
            <div class="flex justify-start">
                <button class="p-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl shadow-lg shadow-blue-500/30 transform hover:-translate-y-1 transition-all duration-200 group">
                    <svg class="w-6 h-6 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto p-2">
        <table class="w-full text-left min-w-[1000px] lg:min-w-full">
            <thead class="bg-gray-50/50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-xs font-black uppercase tracking-[0.2em]">
                <tr>
                    <th class="px-4 lg:px-8 py-5 rounded-tl-2xl">User</th>
                    <th class="px-4 lg:px-8 py-5 text-center">Login Time</th>
                    <th class="px-4 lg:px-8 py-5 text-center">Logout Time</th>
                    <th class="px-4 lg:px-8 py-5 text-center rounded-tr-2xl">Applications</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($activities as $activity)
                <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-all duration-200 group">
                    <td class="px-4 lg:px-8 py-4 lg:py-6">
                        <div class="flex items-center space-x-3 lg:space-x-4">
                            <div class="relative cursor-pointer flex-shrink-0" wire:click="openAuditModal('{{ $activity->session_id }}')">
                                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-base lg:text-lg shadow-md group-hover:rotate-6 transition-transform">
                                    {{ substr($activity->properties['user_name'] ?? 'U', 0, 1) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-3 h-3 lg:w-4 lg:h-4 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></div>
                            </div>
                            <div class="flex flex-col cursor-pointer min-w-0" wire:click="openAuditModal('{{ $activity->session_id }}')">
                                <span class="font-bold text-gray-900 dark:text-white text-sm lg:text-lg leading-tight hover:text-blue-600 transition-colors truncate max-w-[150px] lg:max-w-none" title="{{ $activity->properties['user_name'] ?? 'Unknown User' }}">{{ $activity->properties['user_name'] ?? 'Unknown User' }}</span>
                                <span class="text-xs lg:text-sm font-medium text-gray-400 dark:text-gray-500">@ {{ $activity->properties['user_mobile'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 lg:px-8 py-4 lg:py-6 text-center">
                        <div class="inline-flex flex-col items-center">
                            <span class="text-gray-900 dark:text-white font-semibold text-xs lg:text-sm">{{ $activity->created_at->format('M d, Y') }}</span>
                            <span class="text-xs lg:text-sm text-blue-500 dark:text-blue-400 font-bold tabular-nums">{{ $activity->created_at->format('h:i:s A') }}</span>
                        </div>
                    </td>
                    <td class="px-4 lg:px-8 py-4 lg:py-6 text-center">
                        @if($activity->logout_time)
                        <div class="inline-flex flex-col items-center">
                            <span class="text-gray-900 dark:text-white font-semibold text-xs lg:text-sm">{{ \Carbon\Carbon::parse($activity->logout_time)->format('M d, Y') }}</span>
                            <span class="text-xs lg:text-sm text-indigo-500 dark:text-indigo-400 font-bold tabular-nums">{{ \Carbon\Carbon::parse($activity->logout_time)->format('h:i:s A') }}</span>
                        </div>
                        @elseif($activity->last_activity)
                        <span class="px-3 lg:px-4 py-1.5 lg:py-2 bg-emerald-100 text-emerald-600 text-[10px] lg:text-xs font-bold rounded-full animate-pulse whitespace-nowrap">
                            Active Now
                        </span>
                        @else
                        <span class="px-3 lg:px-4 py-1.5 lg:py-2 bg-red-100 text-red-600 text-[10px] lg:text-xs font-bold rounded-full whitespace-nowrap">
                            Session Timeout
                        </span>
                        @endif
                    </td>
                    <td class="px-4 lg:px-8 py-4 lg:py-6 text-center max-w-[300px] lg:max-w-[400px]">
                        @if(isset($pageLogs[$activity->session_id]))
                        @php
                        $pages = $pageLogs[$activity->session_id];
                        $totalPages = count($pages);
                        $hiddenCount = $totalPages - 3;
                        @endphp

                        <div
                            x-data="{ 
                            showAll: false,
                            totalPages: {{ $totalPages }},
                            hiddenCount: {{ $hiddenCount }}
                        }"
                            class="flex flex-col items-center gap-2 w-full">

                            <!-- Grid container for badges - Fixed width and better grid -->
                            <div class="grid grid-cols-2 gap-3 mx-auto">
                                @foreach($pages as $index => $page)
                                <div class="flex items-center gap-2 lg:gap-3 px-1.5 lg:px-3 py-1 bg-gradient-to-r from-cyan-400/10 to-emerald-400/10 
                                        text-cyan-700 dark:text-cyan-300 text-[8px] lg:text-[10px] font-black uppercase 
                                        tracking-widest rounded-lg border border-cyan-100 
                                        dark:border-cyan-800/50 shadow-sm
                                        transition-all duration-300 cursor-pointer hover:from-cyan-400/20 hover:to-emerald-400/20 hover:scale-105
                                        min-w-0 w-full"
                                    :class="{ 
                                        'hidden': !showAll && {{ $index }} >= 3,
                                        'col-span-1': true
                                    }">

                                    <span
                                        wire:click="openActionModal('{{ $activity->session_id }}', '{{ addslashes($page['url']) }}')"
                                        class="truncate flex-1 min-w-0 text-center"
                                        title="{{ $page['name'] }}">
                                        {{ $page['name'] }}
                                    </span>

                                    <button
                                        type="button"
                                        wire:click.stop="openAuditModal('{{ $activity->session_id }}', '{{ addslashes($page['url']) }}')"
                                        class="flex-shrink-0 p-0.5 lg:p-1 rounded-lg bg-white/50 dark:bg-gray-900/40 hover:bg-white dark:hover:bg-gray-800 text-cyan-700 dark:text-cyan-200">
                                        <svg class="w-2.5 h-2.5 lg:w-3.5 lg:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z" />
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>

                            @if($totalPages > 3)
                            <button
                                @click="showAll = !showAll"
                                class="text-[10px] lg:text-xs text-blue-600 font-semibold hover:text-blue-800 
                                    transition-all duration-200 hover:scale-105 mt-1
                                    px-2 lg:px-3 py-1 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20
                                    whitespace-nowrap"
                                x-text="showAll ? 'Show Less' : 'View More (' + hiddenCount + ' more)'">
                            </button>
                            @endif
                        </div>

                        @else
                        <div class="text-center">
                            <span class="text-gray-400 text-[8px] lg:text-[10px] italic">No page activity</span>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 lg:px-8 py-10 lg:py-20 text-center">
                        <div class="flex flex-col items-center space-y-3 lg:space-y-4">
                            <div class="p-4 lg:p-6 bg-gray-50 dark:bg-gray-800 rounded-full">
                                <svg class="w-12 h-12 lg:w-16 lg:h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <p class="text-base lg:text-xl font-bold text-gray-400 dark:text-gray-500">No activity logs found for this criteria recorded</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700">
        {{ $activities->links() }}
    </div>

    <!-- Audit Details Modal -->
    @teleport('body')
    <div x-data="{ show: @entangle('showAuditModal') }" x-show="show"
        class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" @click="show = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100 dark:border-gray-700 z-[110]">

                <div class="p-8 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-blue-50 to-transparent dark:from-gray-800/50">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-2xl">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2v-1M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Audit Logs</h3>
                            @if($selectedUrl)
                            <p class="text-xs text-blue-500 font-bold uppercase tracking-widest mt-1 flex items-center gap-1 flex-wrap">
                                <span class="flex-shrink-0">Filtered by:</span>
                                <span class="font-black truncate max-w-[150px] md:max-w-[200px] lg:max-w-[300px]"
                                    title="{{ $this->getDisplayNameFromUrl($selectedUrl) }}">
                                    {{ $this->getDisplayNameFromUrl($selectedUrl) }}
                                </span>
                            </p>
                            @else
                            <p class="text-xs text-gray-400 font-medium mt-1">Full Session Activity</p>
                            @endif
                        </div>
                    </div>
                    <button @click="show = false" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="max-h-[60vh] overflow-y-auto p-8 bg-gray-50/30 dark:bg-gray-800/30">
                    @if($this->audits->isEmpty())
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center p-4 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 15.658c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">No audit logs recorded for this {{ $selectedUrl ? 'page' : 'session' }}.</p>
                    </div>
                    @else
                    <div class="space-y-6">
                        @foreach($this->audits as $audit)
                        <div class="bg-white dark:bg-gray-700/50 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-600/50 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center space-x-3">
                                    <span @class([ 'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest' , 'bg-green-100 text-green-700'=> $audit->event === 'created',
                                        'bg-blue-100 text-blue-700' => $audit->event === 'updated',
                                        'bg-red-100 text-red-700' => $audit->event === 'deleted',
                                        'bg-gray-100 text-gray-700' => !in_array($audit->event, ['created', 'updated', 'deleted'])
                                        ])>
                                        {{ $audit->event }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">
                                        {{ class_basename($audit->auditable_type) }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-400 font-medium">
                                    {{ $audit->created_at->format('M d, h:i:s A') }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($audit->old_values)
                                <div class="space-y-2">
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Old Values</span>
                                    <div class="bg-red-50/50 dark:bg-red-900/10 p-3 rounded-xl border border-red-100 dark:border-red-900/20">
                                        <pre class="text-[11px] text-red-600 dark:text-red-400 font-mono whitespace-pre-wrap">{{ json_encode($audit->old_values, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                                @endif
                                @if($audit->new_values)
                                <div class="space-y-2">
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">New Values</span>
                                    <div class="bg-emerald-50/50 dark:bg-emerald-900/10 p-3 rounded-xl border border-emerald-100 dark:border-emerald-900/20">
                                        <pre class="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono whitespace-pre-wrap">{{ json_encode($audit->new_values, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                                @endif
                            </div>

                            @php $other = is_string($audit->other_details) ? json_decode($audit->other_details, true) : $audit->other_details; @endphp
                            @if($other)
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-600/50">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 block mb-2">Context</span>
                                <div class="flex flex-col gap-2">
                                    @if(isset($other['url']))
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-600 rounded text-[9px] text-gray-500 dark:text-gray-400 font-bold uppercase">URL</span>
                                        <span class="text-[10px] text-gray-600 dark:text-gray-300 font-mono break-all">{{ $other['url'] }}</span>
                                    </div>
                                    @endif
                                    <div class="flex gap-2">
                                        @if(isset($other['method']))
                                        <span class="px-2 py-1 bg-blue-50 dark:bg-blue-900/20 rounded text-[9px] text-blue-600 dark:text-blue-400 font-bold">METHOD: {{ $other['method'] }}</span>
                                        @endif
                                        @if(isset($other['updated_by_role']))
                                        <span class="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/20 rounded text-[9px] text-indigo-600 dark:text-indigo-400 font-bold">ROLE ID: {{ $other['updated_by_role'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="p-8 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-end">
                    <button @click="show = false" class="px-8 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endteleport
    <!-- ═══ Livewire Actions Modal ═══════════════════════════════════════════ -->
    @teleport('body')
    <div x-data="{ show: @entangle('showActionModal') }" x-show="show"
        class="fixed inset-0 z-[110] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="show"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="show = false"></div>

            <div x-show="show"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl border border-gray-100 dark:border-gray-700 z-[120]">

                <!-- Header - Cleaner gradient -->
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-indigo-50/50 to-transparent dark:from-indigo-900/10">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-indigo-100 dark:bg-indigo-900/40 rounded-xl shadow-sm">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Action Logs</h3>
                            @if($actionModalUrl)
                            <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium mt-0.5 flex items-center gap-1.5">
                                <span class="flex-shrink-0">Filtered by:</span>
                                <span class="font-semibold truncate max-w-[200px] md:max-w-[300px] bg-indigo-50 dark:bg-indigo-900/20 px-2 py-0.5 rounded-md"
                                    title="{{ $this->getDisplayNameFromUrl($actionModalUrl) }}">
                                    {{ $this->getDisplayNameFromUrl($actionModalUrl) }}
                                </span>
                            </p>
                            @endif
                        </div>
                    </div>
                    <button wire:click="closeActionModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Action List - Improved spacing and cards -->
                <div class="max-h-[60vh] overflow-y-auto p-5 space-y-2.5 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600">
                    @if($this->actionLogs->isEmpty())
                    <div class="text-center py-16 px-4">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700/50 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">No action logs recorded</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Actions performed on this page will appear here</p>
                    </div>
                    @else
                    @foreach($this->actionLogs as $actionLog)
                    <div class="group relative bg-white dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-600/50 hover:border-indigo-200 dark:hover:border-indigo-700 hover:shadow-md transition-all duration-200">
                        <!-- Mobile-friendly flex column on small screens -->
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex-1 min-w-0 space-y-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs font-medium rounded-md border border-indigo-100 dark:border-indigo-800">
                                        {{ $actionLog->component_name }}
                                    </span>
                                    <span class="font-mono text-sm font-medium text-gray-700 dark:text-gray-200 truncate">
                                        {{ $actionLog->method_name }}()
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap">
                                    @if(isset($actionLog->response_payload['_action']) && $actionLog->response_payload['_action'] === 'redirect')
                                    <span class="inline-flex items-center px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-[10px] font-medium rounded-full border border-blue-200 dark:border-blue-800">
                                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1.5"></span>
                                        Redirect
                                    </span>
                                    @elseif(isset($actionLog->response_payload['returns']))
                                    <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-[10px] font-medium rounded-full border border-emerald-200 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>
                                        Success
                                    </span>
                                    @endif
                                    <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">
                                        {{ $actionLog->created_at->format('H:i:s') }}
                                    </span>
                                    <span class="text-xs text-gray-300 dark:text-gray-600">•</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $actionLog->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 sm:flex-shrink-0">
                                <button wire:click="openActionRequestModal({{ $actionLog->id }})"
                                    class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-500 text-gray-700 dark:text-gray-200 text-xs font-medium rounded-lg shadow-sm hover:shadow transition-all duration-200 group-hover:border-blue-200 dark:group-hover:border-blue-700">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Details
                                </button>
                                <button wire:click="openActionAuditLog({{ $actionLog->id }})"
                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 text-xs font-medium rounded-lg border border-indigo-200 dark:border-indigo-800 transition-all duration-200 group-hover:border-indigo-300 dark:group-hover:border-indigo-700">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Audit
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>

                <!-- Footer - Cleaner design -->
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-end rounded-b-2xl">
                    <button wire:click="closeActionModal"
                        class="px-5 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:border-gray-300 dark:hover:border-gray-500 transition-all text-sm shadow-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endteleport

    <!-- ═══ Action Audit Detail Modal ════════════════════════════════════════ -->
    @teleport('body')
    <div x-data="{ show: @entangle('showActionAuditModal') }" x-show="show"
        class="fixed inset-0 z-[130] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="show"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" @click="show = false"></div>

            <div x-show="show"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-3xl border border-gray-100 dark:border-gray-700 z-[140]">

                @if($selectedActionLog)
                <!-- Header -->
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-emerald-50 to-transparent dark:from-gray-800/50">
                    <div class="flex items-center space-x-3">
                        <div class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                <span class="text-indigo-600">{{ $selectedActionLog['component_name'] }}</span>
                                &rarr;
                                <span class="font-mono">{{ $selectedActionLog['method_name'] }}()</span>
                            </h3>
                            <p class="text-[10px] text-gray-400 font-medium mt-0.5">{{ \Carbon\Carbon::parse($selectedActionLog['created_at'])->format('M d, Y h:i:s A') }}</p>
                        </div>
                    </div>
                    <button wire:click="closeActionAuditLog" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-5 max-h-[65vh] overflow-y-auto">

                    <!-- Request Summary (compact) -->
                    <div class="bg-blue-50/60 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-900/20 p-4">
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] text-blue-400 block mb-2">📤 Request Sent</span>
                        @php $req = $selectedActionLog['request_payload'] ?? []; @endphp
                        @if(!empty($req['updates']))
                        <div class="mb-2">
                            <span class="text-[9px] font-bold uppercase text-blue-400 block mb-1">Form Values (wire:model)</span>
                            <pre class="text-[11px] text-blue-700 dark:text-blue-300 font-mono whitespace-pre-wrap">{{ json_encode($req['updates'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                        @endif
                        @if(!empty($req['params']))
                        <div>
                            <span class="text-[9px] font-bold uppercase text-blue-400 block mb-1">Method Params</span>
                            <pre class="text-[11px] text-blue-700 dark:text-blue-300 font-mono whitespace-pre-wrap">{{ json_encode($req['params'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                        @endif
                        @if(empty($req['updates']) && empty($req['params']))
                        <p class="text-[11px] text-gray-400 italic">No parameters sent.</p>
                        @endif
                    </div>

                    <!-- Laravel Audit Records -->
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 block mb-3">� Database Changes (Audit Log)</span>

                        @if(empty($actionAudits))
                        <div class="text-center py-8 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-600/50">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm text-gray-400 font-medium">No audit records found for this action's timeframe.</p>
                            <p class="text-[10px] text-gray-400 mt-1">This action may not have caused any DB changes, or the model is not auditable.</p>
                        </div>
                        @else
                        <div class="space-y-4">
                            @foreach($actionAudits as $audit)
                            @php
                            $oldVals = is_array($audit['old_values']) ? $audit['old_values'] : json_decode($audit['old_values'] ?? '{}', true);
                            $newVals = is_array($audit['new_values']) ? $audit['new_values'] : json_decode($audit['new_values'] ?? '{}', true);
                            $modelName = class_basename($audit['auditable_type'] ?? '');
                            @endphp
                            <div class="bg-white dark:bg-gray-700/50 rounded-2xl border border-gray-100 dark:border-gray-600/50 overflow-hidden">
                                <div class="flex items-center justify-between px-5 py-3 bg-gray-50/80 dark:bg-gray-700/80 border-b border-gray-100 dark:border-gray-600/50">
                                    <div class="flex items-center gap-3">
                                        <span @class([ 'px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest' , 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'=> ($audit['event'] ?? '') === 'created',
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' => ($audit['event'] ?? '') === 'updated',
                                            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' => ($audit['event'] ?? '') === 'deleted',
                                            'bg-gray-100 text-gray-700' => !in_array($audit['event'] ?? '', ['created','updated','deleted']),
                                            ])>{{ $audit['event'] ?? 'unknown' }}</span>
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-100">{{ $modelName }}</span>
                                        @if($audit['auditable_id'] ?? null)
                                        <span class="text-[10px] text-gray-400 font-mono">#{{ $audit['auditable_id'] }}</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-gray-400 tabular-nums">
                                        {{ \Carbon\Carbon::parse($audit['created_at'])->format('h:i:s A') }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-600/50">
                                    @if(!empty($oldVals))
                                    <div class="p-4">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-red-400 block mb-2">Before (Old Values)</span>
                                        <div class="bg-red-50/50 dark:bg-red-900/10 rounded-lg p-3 border border-red-100 dark:border-red-900/20">
                                            <pre class="text-[11px] text-red-600 dark:text-red-400 font-mono whitespace-pre-wrap">{{ json_encode($oldVals, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    </div>
                                    @endif
                                    @if(!empty($newVals))
                                    <div class="p-4">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-emerald-400 block mb-2">After (New Values)</span>
                                        <div class="bg-emerald-50/50 dark:bg-emerald-900/10 rounded-lg p-3 border border-emerald-100 dark:border-emerald-900/20">
                                            <pre class="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono whitespace-pre-wrap">{{ json_encode($newVals, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    </div>
                                    @endif
                                    @if(empty($oldVals) && empty($newVals))
                                    <div class="p-4 col-span-2">
                                        <p class="text-[11px] text-gray-400 italic text-center">No value changes recorded.</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <div class="p-5 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center rounded-b-3xl">
                    <button wire:click="closeActionAuditLog" class="text-sm text-gray-500 hover:text-gray-700 font-semibold transition-colors flex items-center gap-1">
                        &larr; Back to Actions
                    </button>
                    <button wire:click="closeActionModal" class="px-6 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all text-sm">
                        Close All
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endteleport

    @teleport('body')
    <div x-data="{ show: @entangle('showActionRequestModal') }" x-show="show"
        class="fixed inset-0 z-[140] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="show"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" @click="show = false"></div>

            <div x-show="show"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-3xl border border-gray-100 dark:border-gray-700 z-[150]">

                @if($selectedActionRequestLog)
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-sky-50 to-transparent dark:from-gray-800/50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            <span class="text-sky-600">{{ $selectedActionRequestLog['component_name'] ?? '' }}</span>
                            &rarr;
                            <span class="font-mono">{{ $selectedActionRequestLog['method_name'] ?? '' }}()</span>
                        </h3>
                        <p class="text-[10px] text-gray-400 font-medium mt-0.5">
                            {{ \Carbon\Carbon::parse($selectedActionRequestLog['created_at'] ?? now())->format('M d, Y h:i:s A') }}
                        </p>
                    </div>
                    <button wire:click="closeActionRequestModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-5 max-h-[65vh] overflow-y-auto">
                    <div class="bg-blue-50/60 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-900/20 p-4">
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] text-blue-400 block mb-2">📤 Request</span>
                        @php $req = $selectedActionRequestLog['request_payload'] ?? []; @endphp
                        @if(!empty($req['updates']))
                        <div class="mb-2">
                            <span class="text-[9px] font-bold uppercase text-blue-400 block mb-1">Form Values (wire:model)</span>
                            <pre class="text-[11px] text-blue-700 dark:text-blue-300 font-mono whitespace-pre-wrap">{{ json_encode($req['updates'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                        @endif
                        @if(!empty($req['params']))
                        <div>
                            <span class="text-[9px] font-bold uppercase text-blue-400 block mb-1">Method Params</span>
                            <pre class="text-[11px] text-blue-700 dark:text-blue-300 font-mono whitespace-pre-wrap">{{ json_encode($req['params'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                        @endif
                        @if(empty($req['updates']) && empty($req['params']))
                        <p class="text-[11px] text-gray-400 italic">No parameters sent.</p>
                        @endif
                    </div>

                    <div class="bg-emerald-50/60 dark:bg-emerald-900/10 rounded-xl border border-emerald-100 dark:border-emerald-900/20 p-4">
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-400 block mb-2">📥 Response</span>
                        @php $res = $selectedActionRequestLog['response_payload'] ?? []; @endphp
                        @if(!empty($res))
                        <pre class="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono whitespace-pre-wrap">{{ json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                        <p class="text-[11px] text-gray-400 italic">No response payload recorded.</p>
                        @endif
                    </div>
                </div>

                <div class="p-5 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-end rounded-b-3xl">
                    <button wire:click="closeActionRequestModal" class="px-6 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all text-sm">
                        Close
                    </button>
                </div>
                @else
                <div class="p-12 text-center">
                    <p class="font-semibold text-gray-500">No action details available.</p>
                    <button wire:click="closeActionRequestModal" class="mt-4 px-6 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all text-sm">
                        Close
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endteleport

</div>