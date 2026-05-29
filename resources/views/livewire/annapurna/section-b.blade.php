<div class="space-y-6">
    @if ($activeMemberIndex === 0)
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">B</span>
                    Ration Card & Food Subsidy | রেশন কার্ড ও খাদ্য ভর্তুকি বিবরণী
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Do you have a
                        Digital Ration Card? * <br><span
                            class="text-xs text-gray-500 font-normal">রেশন কার্ড আছে
                            কি?</span></label>
                    <select wire:model="formData.has_digital_ration_card"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Yes">Yes / হ্যাঁ</option>
                        <option value="No">No / না</option>
                    </select>
                    @error('formData.has_digital_ration_card')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">If Yes, Card Type
                        <br><span class="text-xs text-gray-500 font-normal">রেশন কার্ডের
                            ধরন</span></label>
                    <select wire:model="formData.ration_card_type"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        @foreach ($rcTypes as $type)
                            @php
                                $val = $type;
                                if ($type === 'RKSY-I') {
                                    $val = 'RKSY1';
                                } elseif ($type === 'RKSY-II') {
                                    $val = 'RKSY2';
                                } elseif ($type === 'Non-Subsidized') {
                                    $val = 'Non-subsidized';
                                }
                            @endphp
                            <option value="{{ $val }}">
                                {{ $type }}
                                @if ($type === 'AAY')
                                    (Antyodaya Anna Yojana)
                                @elseif($type === 'PHH')
                                    (Priority Household)
                                @elseif($type === 'SPHH')
                                    (Special Priority Household)
                                @elseif($type === 'Non-Subsidized')
                                    / ভর্তুকিহীন
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Lifting Monthly
                        Ration? * <br><span class="text-xs text-gray-500 font-normal">রেশন পাচ্ছেন
                            কি?</span></label>
                    <select wire:model="formData.is_lifting_ration"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Yes">Yes / হ্যাঁ</option>
                        <option value="No">No / না</option>
                    </select>
                    @error('formData.is_lifting_ration')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    @else
        {{-- Member Section B --}}
        @php
            $index = $activeMemberIndex - 1;
        @endphp
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
            wire:key="member-ration-{{ $index }}">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">B</span>
                    Ration Card (Member #{{ $activeMemberIndex }}) | সদস্যের রেশন বিবরণী
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Do they have a
                        Digital Ration Card? <br><span
                            class="text-xs text-gray-500 font-normal">রেশন কার্ড আছে
                            কি?</span></label>
                    <select wire:model="members.{{ $index }}.has_digital_ration_card"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Yes">Yes / হ্যাঁ</option>
                        <option value="No">No / না</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ration Card
                        Number <br><span class="text-xs text-gray-500 font-normal">রেশন কার্ড
                            নম্বর</span></label>
                    <input type="text" wire:model="members.{{ $index }}.ration_card_no"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ration Card Type
                        <br><span class="text-xs text-gray-500 font-normal">রেশন কার্ডের
                            ধরন</span></label>
                    <select wire:model="members.{{ $index }}.ration_card_type"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        @foreach ($rcTypes as $type)
                            @php
                                $val = $type;
                                if ($type === 'RKSY-I') {
                                    $val = 'RKSY1';
                                } elseif ($type === 'RKSY-II') {
                                    $val = 'RKSY2';
                                } elseif ($type === 'Non-Subsidized') {
                                    $val = 'Non-subsidized';
                                }
                            @endphp
                            <option value="{{ $val }}">
                                {{ $type }}
                                @if ($type === 'Non-Subsidized')
                                    / ভর্তুকিহীন
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
</div>
