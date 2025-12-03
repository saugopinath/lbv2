<div class="space-y-6">
    <!-- Top Bar with improved layout -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                MIS Report 
            </h1>
        </div>
    </div>

    <!-- MIS Report Card with enhanced design -->
    <div
        class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
        <!-- Header with accent color -->
        <div
            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                            Incomplete MIS Report Summary
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                            Showing data for all districts
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span
                        class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                        {{ count($rows) }} Districts
                    </span>
                </div>
            </div>
        </div>

        <!-- Table with improved design -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0 z-10">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            District Information
                        </th>
                        <th
                            class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Total Verifier Pending
                        </th>
                        <th
                            class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Total Verifier
                        </th>
                        <th
                            class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Approved
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @foreach($rows as $row)
                        <tr
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150 {{ !$row->active ? 'opacity-70' : '' }}">
                            <!-- District Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-lg flex items-center justify-center {{ $row->active ? 'bg-gradient-to-br from-blue-500 to-blue-600' : 'bg-gradient-to-br from-gray-400 to-gray-500' }}">
                                            <span class="text-white font-bold text-sm">
                                                {{ substr($row->district, 0, 1) }}
                                            </span>
                                        </div>
                                        @if($row->active)
                                            <div
                                                class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-white dark:border-gray-800">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div
                                            class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate max-w-[180px]">
                                            {{ $row->district }}
                                        </div>
                                        <div
                                            class="text-xs {{ $row->active ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }} flex items-center space-x-1 mt-0.5">
                                            @if($row->active)
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                <span>Active</span>
                                            @else
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                <span>Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Pending Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="text-2xl font-bold {{ $row->active ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $row->pending }}
                                    </div>
                                    <div
                                        class="text-xs mt-1 px-2 py-0.5 rounded-full {{ $row->active ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                        Pending
                                    </div>
                                    @if($row->pending > 0)
                                        <button wire:click="exportDistrictExcel('{{ $row->district }}','pending')"
                                            class="mt-2 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200"
                                            title="Export Pending Data">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>

                            <!-- Verifier Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="text-2xl font-bold {{ $row->active ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $row->verifier }}
                                    </div>
                                    <div
                                        class="text-xs mt-1 px-2 py-0.5 rounded-full {{ $row->active ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                        Verified
                                    </div>
                                    @if($row->verifier > 0)
                                        <button wire:click="exportDistrictExcel('{{ $row->district }}','verifier')"
                                            class="mt-2 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors duration-200"
                                            title="Export Verifier Data">
                                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>

                            <!-- Approve Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="text-2xl font-bold {{ $row->active ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $row->approve }}
                                    </div>
                                    <div
                                        class="text-xs mt-1 px-2 py-0.5 rounded-full {{ $row->active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                        Approved
                                    </div>
                                    @if($row->approve > 0)
                                        <button wire:click="exportDistrictExcel('{{ $row->district }}','approve')"
                                            class="mt-2 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors duration-200"
                                            title="Export Approved Data">
                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    <!-- Summary Row -->
                    <tr
                        class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 border-t-2 border-gray-300 dark:border-gray-600">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="flex-shrink-0 h-10 w-10 rounded-lg bg-gradient-to-br from-gray-600 to-gray-700 flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">∑</span>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        Total
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        All Districts Summary
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col items-center">
                                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                                    {{ $totals['grand'] ?? 0 }}
                                </div>
                                <div
                                    class="text-xs mt-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                    Total Pending
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col items-center">
                                <div class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">
                                    {{ $totals['verifier'] ?? 0 }}
                                </div>
                                <div
                                    class="text-xs mt-1 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                    Total Verifier
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col items-center">
                                <div class="text-2xl font-bold text-green-700 dark:text-green-300">
                                    {{ $totals['approve'] ?? 0 }}
                                </div>
                                <div
                                    class="text-xs mt-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                    Total Approved
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer with summary -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-sm">
                <div class="text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Last updated:</span> {{ now()->format('M d, Y h:i A') }}
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        <span class="text-gray-700 dark:text-gray-300">Active District</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                        <span class="text-gray-700 dark:text-gray-300">Pending</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-indigo-500"></div>
                        <span class="text-gray-700 dark:text-gray-300">Verifier</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span class="text-gray-700 dark:text-gray-300">Approved</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>