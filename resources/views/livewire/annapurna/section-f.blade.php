<div class="space-y-6">
    @php
        $index = $activeMemberIndex - 1;
    @endphp
    @if ($activeMemberIndex > 0 && ($members[$index]['member_type'] ?? 'adult') === 'child')
        {{-- Child School details --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
            wire:key="member-school-{{ $index }}">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">F1</span>
                    School Attendance | বিদ্যালয়ে অধ্যয়নরত বিবরণ
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Class / Grade
                        <br><span class="text-xs text-gray-500 font-normal">শ্রেণী</span></label>
                    <input type="text"
                        wire:model="members.{{ $index }}.school_grade"
                        placeholder="e.g. Class IV"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error("members.{$index}.school_grade")
                        <span class="text-red-600 text-xs block mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">School Name
                        <br><span class="text-xs text-gray-500 font-normal">বিদ্যালয়ের
                            নাম</span></label>
                    <input type="text" wire:model="members.{{ $index }}.school_name"
                        placeholder="Enter School Name"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error("members.{$index}.school_name")
                        <span class="text-red-600 text-xs block mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">School Type
                        <br><span class="text-xs text-gray-500 font-normal">বিদ্যালয়ের
                            প্রকার</span></label>
                    <select wire:model.live="members.{{ $index }}.school_type"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        @foreach ($schoolTypes as $type)
                            <option value="{{ $type }}">
                                {{ $type }}
                                @if ($type === 'Govt School')
                                    / সরকারি বিদ্যালয়
                                @elseif($type === 'Govt Aided School')
                                    / সরকারি সাহায্যপ্রাপ্ত বিদ্যালয়
                                @elseif($type === 'Govt Sponsored School')
                                    / সরকারি স্পন্সরড বিদ্যালয়
                                @elseif($type === 'Madrasah')
                                    / মাদ্রাসা
                                @elseif($type === 'Recognized Private School')
                                    / স্বীকৃত বেসরকারি বিদ্যালয়
                                @elseif($type === 'Unrecognized Private School')
                                    / অস্বীকৃত বেসরকারি বিদ্যালয়
                                @elseif($type === 'Others')
                                    / অন্যান্য
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error("members.{$index}.school_type")
                        <span class="text-red-600 text-xs block mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @if (($members[$index]['school_type'] ?? '') === 'Others')
                <div class="grid grid-cols-1 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Other School
                            Type Details <br><span
                                class="text-xs text-gray-500 font-normal">অন্যান্য বিদ্যালয়
                                বিবরণ</span></label>
                        <input type="text"
                            wire:model="members.{{ $index }}.school_type_other"
                            placeholder="Specify School Type"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error("members.{$index}.school_type_other")
                            <span class="text-red-600 text-xs block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @endif
        </div>

        {{-- Child Vaccination details --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
            wire:key="member-vaccination-{{ $index }}">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">F2</span>
                    Vaccinated? | শিশুর টিকাকরণ স্থিতি
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Vaccinated?
                        <br><span class="text-xs text-gray-500 font-normal">টিকাকরণ করা
                            হয়েছে?</span></label>
                    <select wire:model.live="members.{{ $index }}.vaccination_status"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Yes">Yes / হ্যাঁ</option>
                        <option value="No">No / না</option>                        
                    </select>
                    @error("members.{$index}.vaccination_status")
                        <span class="text-red-600 text-xs block mt-1">{{ $message }}</span>
                    @enderror
                </div>
                @if (($members[$index]['vaccination_status'] ?? '') === 'Yes' || ($members[$index]['vaccination_status'] ?? '') === 'Partial')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Vaccination Card
                        ID <br><span class="text-xs text-gray-500 font-normal">টিকা কার্ড
                            আইডি</span></label>
                    <input type="text"
                        wire:model="members.{{ $index }}.vaccination_card_id"
                        placeholder="Enter Card ID"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error("members.{$index}.vaccination_card_id")
                        <span class="text-red-600 text-xs block mt-1">{{ $message }}</span>
                    @enderror
                </div>
                @endif
                @if (($members[$index]['vaccination_status'] ?? '') === 'No' || ($members[$index]['vaccination_status'] ?? '') === 'Partial')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Last Date or
                        Reason Skipped <br><span class="text-xs text-gray-500 font-normal">সর্বশেষ
                            তারিখ বা বাদ দেওয়ার কারণ</span></label>
                    <input type="text"
                        wire:model="members.{{ $index }}.vaccination_skip_reason_or_date"
                        placeholder="Date or skip reason"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error("members.{$index}.vaccination_skip_reason_or_date")
                        <span class="text-red-600 text-xs block mt-1">{{ $message }}</span>
                    @enderror
                </div>
                @endif
            </div>
        </div>
    @else
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-5 text-center text-amber-900">
            <p class="text-xs font-semibold">Social Status & Dependents is only applicable for
                child members.</p>
            <p class="text-[10px] text-amber-700 mt-1">সামাজিক অবস্থা ও নির্ভরশীলতা বিবরণটি শুধুমাত্র শিশু সদস্যদের জন্য প্রযোজ্য।</p>
        </div>
    @endif
</div>
