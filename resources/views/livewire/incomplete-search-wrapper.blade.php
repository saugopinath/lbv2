<div class="w-full max-w-full min-w-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-2xl overflow-hidden">
    {{-- Main Container Header: Applicant Incomplete Details --}}
    <div class="p-4 sm:p-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/40 flex flex-wrap items-center justify-between gap-4 w-full max-w-full min-w-0">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </span>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">Applicant Incomplete Details</h1>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 pl-10">
                Filter and search applications with missing or incomplete information
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            @if($stage === 'revert' || $revert === 'yes')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-700">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Revert Queue
                </span>
            @elseif($stage === 'approver')
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
        </div>
    </div>

    {{-- Main Body: 2 Separate Divs Inside --}}
    <div class="p-4 sm:p-6 space-y-6 w-full max-w-full min-w-0">
        {{-- Div 1: Search & Filters --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xs rounded-xl p-4 sm:p-5 w-full max-w-full min-w-0">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <h2 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">Search & Filters</h2>
                </div>
                @if(!empty($activeFilters) && count($activeFilters) > 0)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300">
                        {{ count($activeFilters) }} Active Filter(s)
                    </span>
                @endif
            </div>

            {{-- LGD Location Filters --}}
            <livewire:filterlgdmasternew :buttonShow="false" />

            {{-- Incomplete Type Filter --}}
            <livewire:incomplete-type :button_show="0" :wire:key="'incomplete-type'" />

            {{-- Direct Applicant Search Fields --}}
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-3 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>Direct Search (Application ID / Name / Mobile)</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Application ID</label>
                        <div class="relative">
                            <input type="text"
                                   wire:model="filters.application_id"
                                   wire:keydown.enter="search"
                                   placeholder="e.g. 1000000001"
                                   class="w-full text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 pl-3 pr-8 py-2">
                            @if(!empty($filters['application_id']))
                                <button type="button" wire:click="clearSingleFilter('application_id')" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Applicant Name</label>
                        <div class="relative">
                            <input type="text"
                                   wire:model="filters.applicant_name"
                                   wire:keydown.enter="search"
                                   placeholder="e.g. Test User"
                                   class="w-full text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 pl-3 pr-8 py-2">
                            @if(!empty($filters['applicant_name']))
                                <button type="button" wire:click="clearSingleFilter('applicant_name')" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Mobile Number</label>
                        <div class="relative">
                            <input type="text"
                                   wire:model="filters.mobile_no"
                                   wire:keydown.enter="search"
                                   placeholder="e.g. 9876543210"
                                   class="w-full text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 pl-3 pr-8 py-2">
                            @if(!empty($filters['mobile_no']))
                                <button type="button" wire:click="clearSingleFilter('mobile_no')" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Workflow Queue & Action Buttons --}}
            <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                {{-- Queue Selection --}}
                @if($stage == 'verifier' || $stage == 'revert')
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Queue:</span>
                        <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 p-0.5 bg-gray-50 dark:bg-gray-900">
                            <label class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium cursor-pointer transition {{ $revert === 'no' ? 'bg-white dark:bg-gray-800 text-violet-700 dark:text-violet-400 shadow-sm font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}">
                                <input type="radio" wire:model.live="revert" value="no" class="sr-only">
                                Regular Incomplete
                            </label>
                            <label class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium cursor-pointer transition {{ $revert === 'yes' ? 'bg-amber-500 text-white shadow-sm font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}">
                                <input type="radio" wire:model.live="revert" value="yes" class="sr-only">
                                Reverted Applications
                            </label>
                        </div>
                    </div>
                @else
                    <div></div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-wrap items-center gap-2.5">
                    <button type="button"
                            wire:click="resetAll"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-sm cursor-pointer">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset
                    </button>

                    <button type="button"
                            x-data
                            x-on:click="Livewire.dispatch('showLoader'); $wire.search();"
                            class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-semibold rounded-lg bg-violet-700 hover:bg-violet-800 text-white shadow-sm transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search Applications
                    </button>
                </div>
            </div>

            {{-- Active Filter Pills --}}
            @if(!empty($activeFilters) && count($activeFilters) > 0)
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex flex-wrap items-center gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Applied Filters:</span>
                    @foreach($activeFilters as $key => $filter)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-violet-50 dark:bg-violet-900/30 text-violet-800 dark:text-violet-300 border border-violet-200 dark:border-violet-700">
                            <span class="text-violet-600 dark:text-violet-400 font-semibold">{{ $filter['label'] }}:</span>
                            <span>{{ $filter['value'] }}</span>
                            <button type="button" wire:click="clearSingleFilter('{{ $key }}')" class="text-violet-500 hover:text-violet-800 dark:hover:text-white cursor-pointer ml-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </span>
                    @endforeach
                    <button type="button" wire:click="resetAll" class="text-xs text-red-600 hover:underline ml-1 cursor-pointer">
                        Clear all
                    </button>
                </div>
            @endif
        </div>

        {{-- Div 2: The lists --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xs rounded-xl p-4 sm:p-5 w-full max-w-full min-w-0">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    <h2 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">The lists</h2>
                </div>
            </div>

            <livewire:incomplete-type-table :stage="$stage" :schemeId="$schemeId" :wire:key="'table-'.$stage" />
        </div>
    </div>
</div>
