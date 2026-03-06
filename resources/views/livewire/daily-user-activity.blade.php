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
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-xs font-black uppercase tracking-[0.2em]">
                <tr>
                    <th class="px-8 py-5 rounded-tl-2xl">User</th>
                    <th class="px-8 py-5 text-center">Login Time</th>
                    <th class="px-8 py-5 text-center">Logout Time</th>
                    <th class="px-8 py-5 text-center rounded-tr-2xl">Applications</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($activities as $activity)
                <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-all duration-200 group">
                    <td class="px-8 py-6">
                        <div class="flex items-center space-x-4">
                            <div class="relative cursor-pointer" wire:click="openAuditModal('{{ $activity->session_id }}')">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md group-hover:rotate-6 transition-transform">
                                    {{ substr($activity->properties['user_name'] ?? 'U', 0, 1) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></div>
                            </div>
                            <div class="flex flex-col cursor-pointer" wire:click="openAuditModal('{{ $activity->session_id }}')">
                                <span class="font-bold text-gray-900 dark:text-white text-lg leading-tight hover:text-blue-600 transition-colors">{{ $activity->properties['user_name'] ?? 'Unknown User' }}</span>
                                <span class="text-sm font-medium text-gray-400 dark:text-gray-500">@ {{ $activity->properties['user_mobile'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <div class="inline-flex flex-col items-center">
                            <span class="text-gray-900 dark:text-white font-semibold">{{ $activity->created_at->format('M d, Y') }}</span>
                            <span class="text-sm text-blue-500 dark:text-blue-400 font-bold tabular-nums">{{ $activity->created_at->format('h:i:s A') }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">

                        @if($activity->logout_time)

                        <div class="inline-flex flex-col items-center">
                            <span class="text-gray-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($activity->logout_time)->format('M d, Y') }}</span>
                            <span class="text-sm text-indigo-500 dark:text-indigo-400 font-bold tabular-nums">{{ \Carbon\Carbon::parse($activity->logout_time)->format('h:i:s A') }}</span>
                        </div>

                        @elseif($activity->last_activity)

                        <span class="px-4 py-2 bg-emerald-100 text-emerald-600 text-xs font-bold rounded-full animate-pulse">
                            Active Now
                        </span>
                        @else
                        <span class="px-4 py-2 bg-red-100 text-red-600 text-xs font-bold rounded-full">
                            Session Timeout
                        </span>
                        @endif
                    </td>
                    <td class="px-8 py-6 text-center">
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
                            class="flex flex-col items-center gap-2">
                            <!-- Grid container for badges -->
                            <div class="grid grid-cols-3 gap-2 w-full max-w-auto">
                                @foreach($pages as $index => $page)
                                <span
                                    wire:click="openAuditModal('{{ $activity->session_id }}', '{{ addslashes($page['url']) }}')"
                                    class="px-3 py-1 bg-gradient-to-r from-cyan-400/10 to-emerald-400/10 
                            text-cyan-700 dark:text-cyan-300 text-[10px] font-black uppercase 
                            tracking-widest rounded-lg border border-cyan-100 
                            dark:border-cyan-800/50 shadow-sm whitespace-nowrap text-center
                            transition-all duration-300 cursor-pointer hover:from-cyan-400/20 hover:to-emerald-400/20 hover:scale-105"
                                    :class="{ 
                            'hidden': !showAll && {{ $index }} >= 3,
                            'col-span-1': true
                        }">
                                    {{ $page['name'] }}
                                </span>
                                @endforeach


                            </div>

                            @if($totalPages > 3)
                            <button
                                @click="showAll = !showAll"
                                class="text-xs text-blue-600 font-semibold hover:text-blue-800 
                        transition-all duration-200 hover:scale-105 mt-1
                        px-3 py-1 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                x-text="showAll ? 'Show Less' : 'View More (' + hiddenCount + ' more)'">
                            </button>
                            @endif
                        </div>

                        @else
                        <div class="text-center">
                            <span class="text-gray-400 text-[10px] italic">No page activity</span>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-full">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <p class="text-xl font-bold text-gray-400 dark:text-gray-500">No activity logs found for this criteria recorded</p>
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
                            <p class="text-xs text-blue-500 font-bold uppercase tracking-widest mt-1">
                                Filtered by: {{ Str::title(str_replace(['-', '_'], ' ', Str::afterLast($selectedUrl, '/'))) ?: 'Home' }}
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

</div>