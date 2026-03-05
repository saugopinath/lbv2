<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden transform transition-all duration-300">
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
                            <div class="relative">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md group-hover:rotate-6 transition-transform">
                                    {{ substr($activity->properties['user_name'] ?? 'U', 0, 1) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></div>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 dark:text-white text-lg leading-tight">{{ $activity->properties['user_name'] ?? 'Unknown User' }}</span>
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
                        @else
                        <span class="px-4 py-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-xs font-black uppercase tracking-widest rounded-full border border-emerald-100 dark:border-emerald-800 animate-pulse">
                            Active Now
                        </span>
                        @endif
                    </td>
                    <td class="px-8 py-6 text-center">
                        <div class="flex flex-wrap justify-center gap-2">
                            @if(isset($pageLogs[$activity->session_id]))
                            @foreach($pageLogs[$activity->session_id] as $page)
                            <span class="px-3 py-1 bg-gradient-to-r from-cyan-400/10 to-emerald-400/10 text-cyan-700 dark:text-cyan-300 text-[10px] font-black uppercase tracking-widest rounded-lg border border-cyan-100 dark:border-cyan-800/50 shadow-sm whitespace-nowrap">
                                {{ $page }}
                            </span>
                            @endforeach
                            @else
                            <span class="text-gray-400 text-[10px] italic">No page activity</span>
                            @endif
                        </div>
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
</div>