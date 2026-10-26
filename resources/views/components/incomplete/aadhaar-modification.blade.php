@props(['aadhaarIssues' => [], 'schemeId' => null, 'stage' => 'verifier'])

<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-xs">
    {{-- Card Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 mb-4 border-b border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-2.5">
            <span class="p-1.5 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                </svg>
            </span>
            <div>
                <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                    Aadhaar Verification &amp; Modification
                </h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Aadhaar Number &amp; Mandatory Identity Enclosure
                </span>
            </div>
        </div>

        {{-- Issue Badges --}}
        <div class="flex flex-wrap gap-1.5">
            @foreach ($aadhaarIssues as $issueItem)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                    {{ $issueItem->incompleteType?->name ?? 'Aadhaar Issue' }}
                </span>
            @endforeach
        </div>
    </div>

    @php
        $firstIssue = $aadhaarIssues[0] ?? null;
        $appId = $firstIssue?->application_id;
        $oldAadhaar = $firstIssue?->old_value['aadhaar_no'] ?? null;
        $newAadhaar = $firstIssue?->new_value['aadhaar_no'] ?? null;
    @endphp

    {{-- Data Fields Section --}}
    <div class="mb-5">
        @if (!empty($stage) && in_array($stage, ['verifier', 'revert']))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Existing Aadhaar (Read-Only) --}}
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 block mb-1">
                        Existing Aadhaar on File
                    </span>
                    <span class="text-sm font-mono font-bold text-gray-800 dark:text-white">
                        {{ !empty($oldAadhaar) ? chunk_split($oldAadhaar, 4, ' ') : 'Not Available / Blank' }}
                    </span>
                </div>

                {{-- New Aadhaar Input --}}
                <div class="lg:col-span-2">
                    <label for="aadhaar_modification_{{ $appId }}" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                        New Aadhaar Number <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                               id="aadhaar_modification_{{ $appId }}"
                               name="aadhaar_modification"
                               required
                               maxlength="12"
                               placeholder="Enter 12-digit Aadhaar number"
                               wire:model.defer="formData.aadhaar_modification.{{ $appId }}"
                               x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 12)"
                               class="w-full text-sm font-mono rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2">
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block">
                        Must be a valid 12-digit Aadhaar number satisfying Verhoeff validation
                    </span>
                    @error('aadhaar')
                        <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        @elseif (!empty($stage) && $stage === 'approver')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-3.5 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 block mb-1">
                        Original Aadhaar Number
                    </span>
                    <span class="text-sm font-mono font-bold text-gray-700 dark:text-gray-300">
                        {{ !empty($oldAadhaar) ? chunk_split($oldAadhaar, 4, ' ') : 'N/A' }}
                    </span>
                </div>
                <div class="p-3.5 rounded-lg bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800">
                    <span class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-400 block mb-1">
                        Updated Aadhaar Submitted
                    </span>
                    <span class="text-base font-mono font-bold text-indigo-900 dark:text-indigo-200">
                        {{ !empty($newAadhaar) ? chunk_split($newAadhaar, 4, ' ') : 'Not Provided' }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- Enclosures Side-by-Side Grid --}}
    @if ($appId)
        <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-3 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                <span>Aadhaar Document Enclosures</span>
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Previous Approved Document --}}
                <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/20">
                    <div class="flex items-center justify-between pb-2 mb-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Previous Approved Aadhaar Copy
                        </span>
                        <span class="text-xs px-2.5 py-0.5 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-medium">
                            On File
                        </span>
                    </div>
                    <livewire:enclosure-list
                        :application_id="$appId"
                        :scheme_id="$schemeId"
                        :doc_type_id_array_list="[107]"
                        :is_page="1"
                        :key="'previous-' . $appId" />
                </div>

                {{-- Newly Uploaded Document --}}
                <div class="p-4 rounded-xl border border-indigo-200 dark:border-indigo-800 bg-indigo-50/30 dark:bg-indigo-950/10">
                    <div class="flex items-center justify-between pb-2 mb-3 border-b border-indigo-100 dark:border-indigo-800/60">
                        <span class="text-xs font-bold text-indigo-900 dark:text-indigo-200 uppercase tracking-wider">
                            New Uploaded Aadhaar Copy
                        </span>
                        <span class="text-xs px-2.5 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300 font-medium">
                            Verification Copy
                        </span>
                    </div>
                    <livewire:enclosure-list
                        :application_id="$appId"
                        :scheme_id="$schemeId"
                        :doc_type_id_array_list="[107]"
                        :enclosureSource="5"
                        :key="'new-' . $appId" />

                    @error('document_upload')
                        <span class="text-rose-600 text-xs mt-2 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    @endif
</div>