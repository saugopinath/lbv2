<div class="space-y-6">
    @if ($activeMemberIndex === 0)
        {{-- HOF Benefits --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">G</span>
                    Benefits under Government Schemes (HOF) | অন্যান্য সরকারি সুবিধা
                </h3>
            </div>
            <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                Select which schemes the Head of Family is currently receiving DBT benefits from.
                You can check the <strong>Opt Out</strong> box if they wish to voluntarily surrender
                the DBT benefit.
                <br><span class="text-[10px] text-gray-500">পরিবার প্রধান বর্তমানে কোন কোন সরকারি
                    প্রকল্পে সুবিধা পাচ্ছেন তা চিহ্নিত করুন। সুবিধা প্রত্যাহার করতে চাইলে 'Opt Out'
                    সিলেক্ট করুন।</span>
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Receiving DBT
                        Benefits? <br><span class="text-[10px] text-gray-500">ডিবিটি সুবিধা পান
                            কি?</span></label>
                    <select wire:model.live="formData.hof_has_dbt_benefits"
                        class="w-full border border-gray-300 rounded p-2 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="No">No / না</option>
                        <option value="Yes">Yes / হ্যাঁ</option>
                    </select>
                </div>
            </div>

            @if (($formData['hof_has_dbt_benefits'] ?? 'No') === 'Yes')
                <div class="space-y-3">
                    @foreach ($formData['hof_dbt_benefits'] ?? [] as $i => $benefit)
                        <div wire:key="hof-benefit-row-{{ $i }}"
                            class="flex flex-col md:flex-row gap-4 p-3 bg-white rounded border border-gray-200 items-center justify-between">
                            <div class="w-full md:w-5/12">
                                <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Scheme / প্রকল্প</label>
                                <select
                                    wire:model="formData.hof_dbt_benefits.{{ $i }}.scheme_name"
                                    class="w-full border border-gray-300 rounded p-1.5 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Select Scheme --</option>
                                    @foreach ($benefitSchemes as $scheme)
                                        <option value="{{ $scheme }}">
                                            {{ $scheme }}
                                            @if ($scheme === 'Others')
                                                / অন্যান্য
                                            @endif
                                        </option>
                                    @endforeach
                                    @if (!in_array('Student Credit Card', $benefitSchemes))
                                        <option value="Student Credit Card">Student Credit Card</option>
                                    @endif
                                    @if (!in_array('Yuvashree', $benefitSchemes))
                                        <option value="Yuvashree">Yuvashree</option>
                                    @endif
                                </select>
                            </div>
                            <div class="w-full md:w-5/12 flex items-center gap-2 mt-4 md:mt-5">
                                <input type="checkbox"
                                    wire:model="formData.hof_dbt_benefits.{{ $i }}.opt_out"
                                    id="hof_dbt_opt_out_{{ $i }}"
                                    class="h-4 w-4 text-indigo-900 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="hof_dbt_opt_out_{{ $i }}"
                                    class="text-xs text-gray-700 font-medium">Voluntarily Opt Out? / স্বেচ্ছায় সুবিধা ত্যাগ করতে চান</label>
                            </div>
                            <div class="w-full md:w-1/12 flex justify-end mt-4 md:mt-5">
                                @if (count($formData['hof_dbt_benefits']) > 1)
                                    <button type="button" wire:click="removeHofDbtBenefit({{ $i }})"
                                        class="text-red-600 hover:text-red-800 text-xs font-semibold flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Remove
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="flex justify-start mt-2">
                        <button type="button" wire:click="addHofDbtBenefit"
                            class="bg-indigo-900 hover:bg-indigo-950 text-white font-bold py-1.5 px-3 rounded text-xs flex items-center gap-1 shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Scheme / প্রকল্প যোগ করুন
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @else
        {{-- Member Benefits --}}
        @php
            $index = $activeMemberIndex - 1;
        @endphp
        @if (($members[$index]['member_type'] ?? 'adult') === 'adult')
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
                wire:key="member-benefits-{{ $index }}">
                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                        <span
                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                            style="background-color: #78350f;">G</span>
                        Benefits under Government Schemes (Member #{{ $activeMemberIndex }}) |
                        অন্যান্য সরকারি সুবিধা
                    </h3>
                </div>
                <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                    Select which schemes this member is currently receiving DBT benefits from. You
                    can check the <strong>Opt Out</strong> box if they wish to voluntarily surrender
                    the DBT benefit.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Receiving
                            DBT Benefits? <br><span class="text-[10px] text-gray-500">ডিবিটি
                                সুবিধা পান কি?</span></label>
                        <select wire:model.live="members.{{ $index }}.has_dbt_benefits"
                            class="w-full border border-gray-300 rounded p-2 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="No">No / না</option>
                            <option value="Yes">Yes / হ্যাঁ</option>
                        </select>
                    </div>
                </div>

                @if (($members[$index]['has_dbt_benefits'] ?? 'No') === 'Yes')
                    <div class="space-y-3">
                        @foreach ($members[$index]['dbt_benefits'] ?? [] as $i => $benefit)
                            <div wire:key="member-{{ $index }}-benefit-row-{{ $i }}"
                                class="flex flex-col md:flex-row gap-4 p-3 bg-white rounded border border-gray-200 items-center justify-between">
                                <div class="w-full md:w-5/12">
                                    <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Scheme / প্রকল্প</label>
                                    <select
                                        wire:model="members.{{ $index }}.dbt_benefits.{{ $i }}.scheme_name"
                                        class="w-full border border-gray-300 rounded p-1.5 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Select Scheme --</option>
                                        @foreach ($benefitSchemes as $scheme)
                                            <option value="{{ $scheme }}">
                                                {{ $scheme }}
                                                @if ($scheme === 'Others')
                                                    / অন্যান্য
                                                @endif
                                            </option>
                                        @endforeach
                                        @if (!in_array('Student Credit Card', $benefitSchemes))
                                            <option value="Student Credit Card">Student Credit Card</option>
                                        @endif
                                        @if (!in_array('Yuvashree', $benefitSchemes))
                                            <option value="Yuvashree">Yuvashree</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="w-full md:w-5/12 flex items-center gap-2 mt-4 md:mt-5">
                                    <input type="checkbox"
                                        wire:model="members.{{ $index }}.dbt_benefits.{{ $i }}.opt_out"
                                        id="m_{{ $index }}_dbt_opt_out_{{ $i }}"
                                        class="h-4 w-4 text-indigo-900 border-gray-300 rounded focus:ring-indigo-500">
                                    <label
                                        for="m_{{ $index }}_dbt_opt_out_{{ $i }}"
                                        class="text-xs text-gray-700 font-medium">Voluntarily Opt Out? / সুবিধা ত্যাগ করতে চান</label>
                                </div>
                                <div class="w-full md:w-1/12 flex justify-end mt-4 md:mt-5">
                                    @if (count($members[$index]['dbt_benefits']) > 1)
                                        <button type="button" wire:click="removeMemberDbtBenefit({{ $index }}, {{ $i }})"
                                            class="text-red-600 hover:text-red-800 text-xs font-semibold flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Remove
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="flex justify-start mt-2">
                            <button type="button" wire:click="addMemberDbtBenefit({{ $index }})"
                                class="bg-indigo-900 hover:bg-indigo-950 text-white font-bold py-1.5 px-3 rounded text-xs flex items-center gap-1 shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Scheme / প্রকল্প যোগ করুন
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>
