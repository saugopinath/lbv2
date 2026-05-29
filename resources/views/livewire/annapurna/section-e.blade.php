<div class="space-y-6">
    @if ($activeMemberIndex === 0)
        {{-- Other Docs for HOF --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">E1</span>
                    CAA (Citizenship Amendment Act) Status | নাগরিকত্ব সংশোধনী আইন স্থিতি
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">CAA Application
                        Status <br><span class="text-xs text-gray-500 font-normal">সিএএ
                            স্থিতি</span></label>
                    <select wire:model.live="formData.hof_caa_status"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Not Applicable">Not Applicable / প্রযোজ্য নয়</option>
                        <option value="Applied">Applied / আবেদন করা হয়েছে</option>
                        <option value="Issued">Issued / সংশাপত্র প্রদান করা হয়েছে</option>
                    </select>
                    @error('formData.hof_caa_status')
                        <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                @if ($formData['hof_caa_status'] === 'Applied')
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">CAA
                            Application Number * <br><span
                                class="text-xs text-gray-500 font-normal">সিএএ আবেদন
                                নম্বর</span></label>
                        <input type="text" wire:model="formData.hof_caa_app_no"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('formData.hof_caa_app_no')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                        <x-upload-button doc-id="111" />
                    </div>
                @elseif ($formData['hof_caa_status'] === 'Issued')
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">CAA
                            Certificate Number * <br><span
                                class="text-xs text-gray-500 font-normal">সিএএ সংশাপত্র
                                নম্বর</span></label>
                        <input type="text" wire:model="formData.hof_caa_cert_no"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('formData.hof_caa_cert_no')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                        <x-upload-button doc-id="111" />
                    </div>
                @endif
            </div>
        </div>

        {{-- Other Credit/Artisan Cards for HOF --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">E2</span>
                    Other Credit / Artisan Cards | ক্রেডিট এবং কারিগর কার্ডের বিবরণ
                </h3>
                <button type="button" wire:click="addHofKccCard"
                    class="bg-indigo-900 text-white hover:bg-indigo-800 px-3 py-1.5 rounded text-sm font-semibold flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Card / কার্ড যোগ করুন
                </button>
            </div>

            <div class="space-y-4">
                @foreach ($formData['hof_kcc_cards'] ?? [] as $ki => $card)
                    <div class="p-4 bg-white border border-gray-200 rounded-lg relative" wire:key="hof-card-{{ $ki }}">
                        @if (count($formData['hof_kcc_cards'] ?? []) > 1)
                            <button type="button" wire:click="removeHofKccCard({{ $ki }})"
                                class="absolute top-2 right-2 text-red-600 hover:text-red-800 transition-colors p-1"
                                title="Remove Card">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pr-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Card Type
                                    <br><span class="text-xs text-gray-500 font-normal">কার্ডের প্রকার</span></label>
                                <select wire:model.live="formData.hof_kcc_cards.{{ $ki }}.type"
                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Select Card Type --</option>
                                    <option value="None">None / কোনোটিই নয়</option>
                                    @foreach ($documentTypes as $docType)
                                        <option value="{{ $docType['value'] }}">
                                            {{ $docType['label'] }}
                                            @if ($docType['value'] === 'Artisan Credit Card')
                                                / কারিগর ক্রেডিট কার্ড
                                            @elseif($docType['value'] === 'Student CC')
                                                / স্টুডেন্ট ক্রেডিট কার্ড
                                            @elseif($docType['value'] === 'Others')
                                                / অন্যান্য
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error("formData.hof_kcc_cards.{$ki}.type")
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            @if (!empty($card['type']) && $card['type'] !== 'None')
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Card ID Number *
                                        <br><span class="text-xs text-gray-500 font-normal">কার্ড আইডি নম্বর</span></label>
                                    <input type="text" wire:model="formData.hof_kcc_cards.{{ $ki }}.id_no"
                                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @error("formData.hof_kcc_cards.{$ki}.id_no")
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Issue Date
                                        <br><span class="text-xs text-gray-500 font-normal">প্রদানের তারিখ</span></label>
                                    <input type="date" wire:model="formData.hof_kcc_cards.{{ $ki }}.date"
                                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @error("formData.hof_kcc_cards.{$ki}.date")
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Issuing Authority
                                        <br><span class="text-xs text-gray-500 font-normal">প্রদানকারী কর্তৃপক্ষ</span></label>
                                    <input type="text" wire:model="formData.hof_kcc_cards.{{ $ki }}.issuing_authority"
                                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-600 mb-1">Upload KCC/Artisan Card Document (if any):</p>
                <x-upload-button doc-id="114" />
            </div>
        </div>

        {{-- HOF SIR Status --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">E3</span>
                    SIR (Survey of India Records) Tribunal pending Status | এসআইআর ট্রাইব্যুনাল
                    স্থিতি
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">SIR Tribunal
                        Status <br><span class="text-xs text-gray-500 font-normal">ট্রাইব্যুনাল
                            স্থিতি</span></label>
                    <select wire:model.live="formData.hof_sir_status"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Not Applicable">Not Applicable / প্রযোজ্য নয়</option>
                        <option value="No">No Pending Case / কোনো মামলা নেই</option>
                        <option value="Yes">Yes, Case Pending / মামলা বিচারাধীন আছে</option>
                    </select>
                    @error('formData.hof_sir_status')
                        <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                @if ($formData['hof_sir_status'] === 'Yes')
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">SIR Case
                            Details * <br><span class="text-xs text-gray-500 font-normal">মামলার
                                বিবরণ</span></label>
                        <input type="text" wire:model="formData.hof_sir_case_details"
                            placeholder="Enter case details"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('formData.hof_sir_case_details')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                        <x-upload-button doc-id="110" />
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- Member Specific Other Docs --}}
        @php
            $index = $activeMemberIndex - 1;
        @endphp
        @if (($members[$index]['member_type'] ?? 'adult') === 'adult')
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
                wire:key="member-otherdocs-{{ $index }}">
                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                        <span
                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                            style="background-color: #78350f;">E</span>
                        Member #{{ $activeMemberIndex }} CAA, Credit Card & SIR Status | সদস্যের
                        পরিচয়পত্র স্থিতি
                    </h3>
                </div>

                {{-- CAA status --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-4 border-b border-gray-200">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">CAA
                            Application Status <br><span
                                class="text-xs text-gray-500 font-normal">সিএএ
                                স্থিতি</span></label>
                        <select wire:model.live="members.{{ $index }}.caa_status"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select --</option>
                            <option value="Not Applicable">Not Applicable / প্রযোজ্য নয়</option>
                            <option value="Applied">Applied / আবেদন করা হয়েছে</option>
                            <option value="Issued">Issued / সংশাপত্র প্রদান করা হয়েছে</option>
                        </select>
                        @error("members.{$index}.caa_status")
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    @if (($members[$index]['caa_status'] ?? '') === 'Applied')
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">CAA
                                Application Number * <br><span
                                    class="text-xs text-gray-500 font-normal">সিএএ আবেদন
                                    নম্বর</span></label>
                            <input type="text"
                                wire:model="members.{{ $index }}.caa_app_no"
                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error("members.{$index}.caa_app_no")
                                <span class="text-red-600 text-xs">{{ $message }}</span>
                            @enderror
                            <x-upload-button doc-id="111" />
                        </div>
                    @elseif (($members[$index]['caa_status'] ?? '') === 'Issued')
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">CAA
                                Certificate Number * <br><span
                                    class="text-xs text-gray-500 font-normal">সিএএ সংশাপত্র
                                    নম্বর</span></label>
                            <input type="text"
                                wire:model="members.{{ $index }}.caa_cert_no"
                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error("members.{$index}.caa_cert_no")
                                <span class="text-red-600 text-xs">{{ $message }}</span>
                            @enderror
                            <x-upload-button doc-id="111" />
                        </div>
                    @endif
                </div>

                {{-- Credit card --}}
                <div class="py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-sm font-bold text-indigo-900">
                            Other Credit / Artisan Cards | ক্রেডিট এবং কারিগর কার্ডের বিবরণ
                        </label>
                        <button type="button" wire:click="addMemberKccCard({{ $index }})"
                            class="bg-indigo-900 text-white hover:bg-indigo-800 px-3 py-1 rounded text-xs font-semibold flex items-center gap-1 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Card / কার্ড যোগ করুন
                        </button>
                    </div>

                    <div class="space-y-4">
                        @foreach ($members[$index]['kcc_cards'] ?? [] as $ci => $card)
                            <div class="p-4 bg-white border border-gray-200 rounded-lg relative" wire:key="member-{{ $index }}-card-{{ $ci }}">
                                @if (count($members[$index]['kcc_cards'] ?? []) > 1)
                                    <button type="button" wire:click="removeMemberKccCard({{ $index }}, {{ $ci }})"
                                        class="absolute top-2 right-2 text-red-600 hover:text-red-800 transition-colors p-1"
                                        title="Remove Card">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pr-8">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Card Type
                                            <br><span class="text-xs text-gray-500 font-normal">কার্ডের প্রকার</span></label>
                                        <select wire:model.live="members.{{ $index }}.kcc_cards.{{ $ci }}.type"
                                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Select Card Type --</option>
                                            <option value="None">None / কোনোটিই নয়</option>
                                            @foreach ($documentTypes as $docType)
                                                <option value="{{ $docType['value'] }}">
                                                    {{ $docType['label'] }}
                                                    @if ($docType['value'] === 'Artisan Credit Card')
                                                        / কারিগর ক্রেডিট কার্ড
                                                    @elseif($docType['value'] === 'Student CC')
                                                        / স্টুডেন্ট ক্রেডিট কার্ড
                                                    @elseif($docType['value'] === 'Others')
                                                        / অন্যান্য
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("members.{$index}.kcc_cards.{$ci}.type")
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    @if (!empty($card['type']) && $card['type'] !== 'None')
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Card ID Number *
                                                <br><span class="text-xs text-gray-500 font-normal">কার্ড আইডি নম্বর</span></label>
                                            <input type="text" wire:model="members.{{ $index }}.kcc_cards.{{ $ci }}.id_no"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @error("members.{$index}.kcc_cards.{$ci}.id_no")
                                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Issue Date
                                                <br><span class="text-xs text-gray-500 font-normal">প্রদানের তারিখ</span></label>
                                            <input type="date" wire:model="members.{{ $index }}.kcc_cards.{{ $ci }}.date"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @error("members.{$index}.kcc_cards.{$ci}.date")
                                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Issuing Authority
                                                <br><span class="text-xs text-gray-500 font-normal">প্রদানকারী কর্তৃপক্ষ</span></label>
                                            <input type="text" wire:model="members.{{ $index }}.kcc_cards.{{ $ci }}.issuing_authority"
                                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-600 mb-1">Upload KCC/Artisan Card Document (if any):</p>
                        <x-upload-button doc-id="114" />
                    </div>
                </div>

                {{-- SIR Status --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">SIR Tribunal
                            Status <br><span
                                class="text-xs text-gray-500 font-normal">ট্রাইব্যুনাল
                                স্থিতি</span></label>
                        <select wire:model.live="members.{{ $index }}.sir_status"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select --</option>
                            <option value="Not Applicable">Not Applicable / প্রযোজ্য নয়</option>
                            <option value="No">No Pending Case / কোনো মামলা নেই</option>
                            <option value="Yes">Yes, Case Pending / মামলা বিচারাধীন আছে</option>
                        </select>
                        @error("members.{$index}.sir_status")
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    @if (($members[$index]['sir_status'] ?? '') === 'Yes')
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">SIR Case
                                Details * <br><span
                                    class="text-xs text-gray-500 font-normal">মামলার
                                    বিবরণ</span></label>
                            <input type="text"
                                wire:model="members.{{ $index }}.sir_case_details"
                                placeholder="Enter case details"
                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error("members.{$index}.sir_case_details")
                                <span class="text-red-600 text-xs">{{ $message }}</span>
                            @enderror
                            <x-upload-button doc-id="110" />
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>
