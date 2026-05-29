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

            @if ($formData['hof_has_dbt_benefits'] === 'Yes')
                <div class="space-y-3">
                    @for ($i = 0; $i < 5; $i++)
                        <div wire:key="hof-benefit-row-{{ $i }}"
                            class="grid grid-cols-1 md:grid-cols-2 gap-4 p-2 bg-gray-50 rounded border border-gray-200">
                            <div>
                                <select
                                    wire:model="formData.hof_dbt_benefits.{{ $i }}.scheme_name"
                                    class="w-full border border-gray-300 rounded p-1.5 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Select Scheme {{ $i + 1 }}
                                        --</option>
                                    @foreach ($benefitSchemes as $scheme)
                                        <option value="{{ $scheme }}">
                                            {{ $scheme }}
                                            @if ($scheme === 'Others')
                                                / অন্যান্য
                                            @endif
                                        </option>
                                    @endforeach
                                    @if (!in_array('Student Credit Card', $benefitSchemes))
                                        <option value="Student Credit Card">Student Credit Card
                                        </option>
                                    @endif
                                    @if (!in_array('Yuvashree', $benefitSchemes))
                                        <option value="Yuvashree">Yuvashree</option>
                                    @endif
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox"
                                    wire:model="formData.hof_dbt_benefits.{{ $i }}.opt_out"
                                    id="hof_dbt_opt_out_{{ $i }}"
                                    class="h-4 w-4 text-indigo-900 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="hof_dbt_opt_out_{{ $i }}"
                                    class="text-xs text-gray-700 font-medium">Voluntarily Opt Out?
                                    / স্বেচ্ছায় সুবিধা ত্যাগ করতে চান</label>
                            </div>
                        </div>
                    @endfor
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
                        @for ($i = 0; $i < 5; $i++)
                            <div wire:key="member-{{ $index }}-benefit-row-{{ $i }}"
                                class="grid grid-cols-1 md:grid-cols-2 gap-4 p-2 bg-gray-50 rounded border border-gray-200">
                                <div>
                                    <select
                                        wire:model="members.{{ $index }}.dbt_benefits.{{ $i }}.scheme_name"
                                        class="w-full border border-gray-300 rounded p-1.5 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Select Scheme
                                            {{ $i + 1 }} --</option>
                                        @foreach ($benefitSchemes as $scheme)
                                            <option value="{{ $scheme }}">
                                                {{ $scheme }}
                                                @if ($scheme === 'Others')
                                                    / অন্যান্য
                                                @endif
                                            </option>
                                        @endforeach
                                        @if (!in_array('Student Credit Card', $benefitSchemes))
                                            <option value="Student Credit Card">Student Credit
                                                Card</option>
                                        @endif
                                        @if (!in_array('Yuvashree', $benefitSchemes))
                                            <option value="Yuvashree">Yuvashree</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="checkbox"
                                        wire:model="members.{{ $index }}.dbt_benefits.{{ $i }}.opt_out"
                                        id="m_{{ $index }}_dbt_opt_out_{{ $i }}"
                                        class="h-4 w-4 text-indigo-900 border-gray-300 rounded focus:ring-indigo-500">
                                    <label
                                        for="m_{{ $index }}_dbt_opt_out_{{ $i }}"
                                        class="text-xs text-gray-700 font-medium">Voluntarily Opt
                                        Out? / সুবিধা ত্যাগ করতে চান</label>
                                </div>
                            </div>
                        @endfor
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>
