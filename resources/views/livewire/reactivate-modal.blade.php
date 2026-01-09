<div x-data="{ open: false }" x-show="open" x-cloak x-init="$watch('open', v => { if (!v) $wire.resetForm(); })"
    x-on:show-modal.window="open = true" x-on:hide-modal.window="open = false" @keydown.escape.window="open = false"
    wire:ignore.self class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300 overflow-y-auto py-8">

    <!-- Modal Container with Scale Animation -->
    <div x-show="open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" class="w-full max-w-4xl mx-4 my-auto">

        <form x-on:submit.prevent="$wire.saveDsMark()" class="bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

            <!-- HEADER with Gradient Background (fixed) -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6 flex-shrink-0">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-white">
                            Beneficiary Details
                        </h2>
                    </div>

                    <button @click="open = false"
                        class="text-white hover:bg-white/20 p-2 rounded-full transition-colors duration-200"
                        aria-label="Close modal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- SCROLLABLE CONTENT AREA -->
            <div class="flex-1 overflow-y-auto">
                <div class="p-8">

                    <!-- BENEFICIARY INFO SECTION -->
                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
                            <h3 class="text-lg font-semibold text-gray-800">Personal Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Row 1 -->
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600">Beneficiary ID</span>
                                </div>
                                <p class="ml-6 text-gray-800 font-semibold bg-blue-50 px-3 py-2 rounded-lg">{{ $beneficiary_id }}</p>
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600">JNMP Portal Name</span>
                                </div>
                                <p class="ml-6 text-gray-800 px-3 py-2 rounded-lg border border-gray-100">{{ $jnmp_name ?? '—' }}</p>
                            </div>

                            <!-- Row 2 -->
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600">Date of Death</span>
                                </div>
                                <p class="ml-6 text-gray-800 px-3 py-2 rounded-lg border border-gray-100">{{ $dob ?? '—' }}</p>
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600">Full Name</span>
                                </div>
                                <p class="ml-6 text-gray-800 px-3 py-2 rounded-lg border border-gray-100">{{ $name ?? '—' }}</p>
                            </div>

                            <!-- Row 3 -->
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600">Gender</span>
                                </div>
                                <p class="ml-6 text-gray-800 px-3 py-2 rounded-lg border border-gray-100">{{ $gender ?? '—' }}</p>
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600">Mobile Number</span>
                                </div>
                                <p class="ml-6 text-gray-800 px-3 py-2 rounded-lg border border-gray-100">{{ $mobile ?? '—' }}</p>
                            </div>

                            <!-- Row 4 -->
                            <div class="md:col-span-2 space-y-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600">Father's Name</span>
                                </div>
                                <p class="ml-6 text-gray-800 px-3 py-2 rounded-lg border border-gray-100">{{ $father_name ?? '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-8"></div>

                    <!-- DOCUMENT UPLOAD SECTION -->
                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
                            <h3 class="text-lg font-semibold text-gray-800 mt-4">Document Upload</h3>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 mb-4">

                            <livewire:enclosure-list :application_id="$applicantId" :doc_type_id_array_list="[169]"
                                wire:key="enclosure-{{ $applicantId }}" />

                            @if ($errors->has('document_upload'))
                                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <p class="text-red-600 text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $errors->first('document_upload') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-8"></div>

                    <!-- REACTIVATION DETAILS SECTION -->
                    <div class="mb-8">
                        <!-- REASON SELECTION -->
                        <div class="mb-6">
                            <x-form.select name="revert_reason_cause_id" id="revert_reason_cause_id"
                                label="Reactivation Reason" wire:model.live="revert_reason_cause_id" required
                                class="!border-gray-300 !focus:border-blue-500 !focus:ring-blue-500">
                                <option value="">-- Select Reactivation Reason --</option>
                                @foreach ($reactive_reason as $reason)
                                    <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                @endforeach
                            </x-form.select>
                        </div>

                        <!-- REMARKS -->
                        <div class="mb-6">
                            <x-form.textarea id="revert_reason_remarks" name="revert_reason_remarks" label="Remarks"
                                wire:model="revert_reason_remarks" required rows="4"
                                placeholder="Provide detailed remarks for reactivation..."
                                class="!border-gray-300 !focus:border-blue-500 !focus:ring-blue-500" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- FIXED FOOTER (always visible at bottom) -->
            <div class="border-t border-gray-200 bg-white px-8 py-6 flex-shrink-0">
                <div class="flex items-center justify-between mb-2">
                    <x-button.red @click="open = false" type="button"
                        class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium rounded-lg hover:bg-gray-100 transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </x-button.red>

                    <x-button.primary type="submit"
                        class="px-8 py-3 text-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2 group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Save as Alive
                    </x-button.primary>
                </div>
            </div>

        </form>
    </div>
</div>
