<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Backdrop with blur effect -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col transform transition-all duration-300 scale-100 opacity-100">

                <!-- Modal Header - Simplified with better contrast -->
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-5 flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-tight">Request Processing</h2>
                            <p class="text-indigo-200 text-xs">Scheme Name: {{ $SchemeName }}</p>
                            <p class="text-indigo-200 text-xs">Application ID: {{ $selectedRequest->ref_id }}</p>
                        </div>
                    </div>
                    <button
                        wire:click="closeModal"
                        class="rounded-lg p-2 text-white/80 hover:bg-white/10 hover:text-white transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <!-- Info Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Module Name Card -->
                        <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 4 4 4h8c2.5 0 4-2 4-4V7c0-2-1.5-4-4-4H8c-2.5 0-4 2-4 4z"></path>
                                </svg>
                                <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">Application Date</span>
                            </div>
                            <p class="text-sm font-medium text-gray-900">{{ $selectedRequest->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        <!-- Current Step Card -->
                        <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span class="text-xs font-semibold text-amber-700 uppercase tracking-wide">Current Step</span>
                            </div>
                            <span class="inline-flex rounded-full bg-amber-200 px-3 py-1 text-xs font-semibold text-amber-800">
                                {{ $selectedRequest->step->label->label_name ?? 'Processing' }}
                            </span>
                        </div>
                    </div>

                    <!-- Data Changes Section -->
                    <div>
                        <h4 class="text-base font-bold text-gray-900 border-l-4 border-indigo-600 pl-3 mb-6">Proposed Data Changes</h4>

                        <div class="space-y-8">
                            @foreach($groupedChanges as $groupName => $changes)
                            <div class="relative">
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="text-xs font-black text-indigo-600 uppercase tracking-[0.2em] whitespace-nowrap">{{ $groupName }}</span>
                                    <div class="h-px w-full bg-gradient-to-r from-indigo-100 to-transparent"></div>
                                </div>

                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($changes as $change)
                                    <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition-all duration-300">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="inline-flex rounded-lg bg-indigo-50 px-2.5 py-1 text-[10px] font-bold text-indigo-700 uppercase tracking-wider">
                                                {{ $change['label'] }}
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-6">
                                            <div class="space-y-1">
                                                <span class="block text-[10px] font-medium text-gray-400 uppercase">Current Data</span>
                                                <p class="text-sm text-gray-400 font-medium">
                                                    {{ $change['old'] }}
                                                </p>
                                            </div>
                                            <div class="space-y-1 border-l border-gray-100 pl-6">
                                                <span class="block text-[10px] font-bold text-emerald-600 uppercase">Requested Data</span>
                                                <p class="text-sm font-bold text-emerald-700">
                                                    {{ $change['new'] ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Remark Section -->
                    <div class="pt-4 border-t border-gray-200">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Process Remark
                        </label>
                        <textarea
                            wire:model="remark"
                            rows="2"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:bg-white transition-all duration-200 outline-none resize-none"
                            placeholder="Add your remarks for approval or rejection..."></textarea>
                        @error('remark')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="border-t border-white/20 bg-gradient-to-r from-gray-50/90 to-white/90 backdrop-blur-sm px-6 py-5 shrink-0">
                    <div class="flex gap-4">
                        <button
                            wire:click="processAction('approve')"
                            class="flex-1 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0">
                            <svg class="inline h-4 w-4 mr-2 -mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $selectedRequest->step->label->label_name ?? 'Processing' }}
                        </button>

                        <button
                            wire:click="processAction('reject')"
                            class="flex-1 rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0">
                            <svg class="inline h-4 w-4 mr-2 -mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Reject
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif
</div>