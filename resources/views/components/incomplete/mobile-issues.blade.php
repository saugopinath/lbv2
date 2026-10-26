@props(['mobileIssues' => [], 'stage' => 'verifier'])

<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-xs">
    {{-- Card Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 mb-4 border-b border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-2.5">
            <span class="p-1.5 rounded-lg bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </span>
            <div>
                <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                    Mobile Number Verification &amp; Modification
                </h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Update Contact Mobile Number
                </span>
            </div>
        </div>

        {{-- Issue Badges --}}
        <div class="flex flex-wrap gap-1.5">
            @foreach ($mobileIssues as $issueItem)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                    {{ $issueItem->incompleteType?->name ?? 'Mobile Issue' }}
                </span>
            @endforeach
        </div>
    </div>

    @php
        $firstIssue = $mobileIssues[0] ?? null;
        $mobileKey = $firstIssue?->application_id;
        $oldMobile = $firstIssue?->old_value['mobile_no'] ?? null;
        $newMobile = $firstIssue?->new_value['mobile_no'] ?? null;
        $formMobileValue = old("dup_mobile.{$mobileKey}") ?? ($formData['dup_mobile'][$mobileKey] ?? '');
    @endphp

    {{-- Fields Section --}}
    @if (!empty($stage) && in_array($stage, ['verifier', 'revert']))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Existing Mobile --}}
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 block mb-1">
                    Existing Mobile on File
                </span>
                <span class="text-sm font-mono font-bold text-gray-800 dark:text-white">
                    {{ !empty($oldMobile) ? $oldMobile : 'Not Available / Blank' }}
                </span>
            </div>

            {{-- New Mobile Input --}}
            <div class="lg:col-span-2">
                <label for="dup_mobile_{{ $mobileKey }}" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                    New 10-Digit Mobile Number <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 dark:text-gray-400 text-xs font-mono">
                        +91
                    </div>
                    <input type="text"
                           id="dup_mobile_{{ $mobileKey }}"
                           name="dup_mobile[{{ $mobileKey }}]"
                           required
                           maxlength="10"
                           placeholder="Enter 10-digit mobile number"
                           wire:model.defer="formData.dup_mobile.{{ $mobileKey }}"
                           value="{{ $formMobileValue }}"
                           x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 10)"
                           class="w-full text-sm font-mono rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500 pl-11 pr-3 py-2">
                </div>
                <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block">
                    Must be a valid 10-digit Indian mobile number
                </span>
                @error('mobile')
                    <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>
    @elseif (!empty($stage) && $stage === 'approver')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-3.5 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 block mb-1">
                    Original Mobile Number
                </span>
                <span class="text-sm font-mono font-bold text-gray-700 dark:text-gray-300">
                    {{ !empty($oldMobile) ? '+91 ' . $oldMobile : 'N/A' }}
                </span>
            </div>
            <div class="p-3.5 rounded-lg bg-teal-50/60 dark:bg-teal-950/30 border border-teal-200 dark:border-teal-800">
                <span class="text-xs font-semibold uppercase tracking-wider text-teal-700 dark:text-teal-400 block mb-1">
                    Updated Mobile Submitted
                </span>
                <span class="text-base font-mono font-bold text-teal-900 dark:text-teal-200">
                    {{ !empty($newMobile) ? '+91 ' . $newMobile : 'Not Provided' }}
                </span>
            </div>
        </div>
    @endif
</div>
