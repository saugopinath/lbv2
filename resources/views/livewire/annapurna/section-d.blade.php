<div class="space-y-6">
    @if ($activeMemberIndex === 0)
        {{-- Income / Profession for HOF --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">D1</span>
                    Income & Profession | আয় ও পেশা
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pays Income /
                        Professional Tax? * <br><span class="text-xs text-gray-500 font-normal">কর
                            প্রদান করেন কি?</span></label>
                    <select wire:model="formData.pays_tax"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Yes">Yes / হ্যাঁ</option>
                        <option value="No">No / না</option>
                    </select>
                    @error('formData.pays_tax')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">PAN Card No.
                        (HOF) <br><span class="text-xs text-gray-500 font-normal">প্যান কার্ড
                            নং</span></label>
                    <div x-data="{
                        val: @entangle('formData.hof_pan_no'),
                        error: '',
                        valid: false,
                        check() {
                            if (!this.val) {
                                this.valid = false;
                                this.error = '';
                                return;
                            }
                            let cleaned = window.cleanInput.alphaNumericUpper(this.val, 10);
                            if (this.val !== cleaned) { this.val = cleaned; }
                            this.valid = window.checkValid.pan(this.val);
                            this.error = this.val.length > 0 && !this.valid ? 'Format: AAAAA1111A' : '';
                        }
                    }" x-init="check();
                    $watch('val', () => check())">
                        <input type="text" x-model="val" maxlength="10"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono">
                        <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                            <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                Valid PAN</span>
                            <span x-show="error" x-text="error"
                                class="text-red-500 text-xs font-semibold"></span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Employment of HOF
                        <br><span class="text-xs text-gray-500 font-normal">প্রধানের
                            কর্মসংস্থান</span></label>
                    <select wire:model="formData.hof_employment_nature"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        @foreach ($employmentNatures as $nature)
                            @php
                                $val = $nature;
                                if ($nature === 'Salaried, in Private Sector') {
                                    $val = 'Salaried in Private';
                                } elseif (
                                    $nature ===
                                    'Formal Sector Self-Employed (Entrepreneur/Business/Proprietor/etc.)'
                                ) {
                                    $val = 'Formal Sector Self-Employed';
                                } elseif (
                                    $nature ===
                                    'Informal Sector Self-Employed (Artisan/Craftsman/Farmer/etc.)'
                                ) {
                                    $val = 'Informal Sector Self-Employed';
                                }
                            @endphp
                            <option value="{{ $val }}">
                                {{ $nature }}
                                @if ($nature === 'Government Sector')
                                    / সরকারি ক্ষেত্র
                                @elseif($nature === 'Salaried, in Private Sector')
                                    / বেসরকারি ক্ষেত্র
                                @elseif($nature === 'Formal Sector Self-Employed (Entrepreneur/Business/Proprietor/etc.)')
                                    / স্ব-নিযুক্ত (আনুষ্ঠানিক)
                                @elseif($nature === 'Part-time job')
                                    / খণ্ডকালীন কাজ
                                @elseif($nature === 'Informal Sector Self-Employed (Artisan/Craftsman/Farmer/etc.)')
                                    / স্ব-নিযুক্ত (অনানুষ্ঠানিক)
                                @elseif($nature === 'Housewife')
                                    / গৃহিণী
                                @elseif($nature === 'Unemployed')
                                    / বেকার
                                @elseif($nature === 'Others')
                                    / অন্যান্য
                                @endif
                            </option>
                        @endforeach
                        @if (!in_array('Migrant Labourer', $employmentNatures))
                            <option value="Migrant Labourer">Migrant Labourer / পরিযায়ী শ্রমিক
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Total Annual
                        Family Income (INR) * <br><span
                            class="text-xs text-gray-500 font-normal">বার্ষিক মোট পারিবারিক
                            আয়</span></label>
                    <input type="number" wire:model="formData.total_annual_income"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('formData.total_annual_income')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Literacy & Education for HOF --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">D2</span>
                    Education & Literacy (HOF) | পরিবার প্রধানের শিক্ষা ও সাক্ষরতা
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Literacy Status
                        (HOF) <br><span class="text-xs text-gray-500 font-normal">স্বাক্ষরতা
                            স্থিতি</span></label>
                    <select wire:model="formData.hof_literate_status"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Literate">Literate / সাক্ষর</option>
                        <option value="Illiterate">Illiterate / নিরক্ষর</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Highest
                        Educational Qualification <br><span
                            class="text-xs text-gray-500 font-normal">সর্বোচ্চ শিক্ষাগত
                            যোগ্যতা</span></label>
                    <input type="text" wire:model="formData.hof_highest_qualification"
                        placeholder="e.g. Graduate, Higher Secondary"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 border-t border-gray-200 pt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Literate
                        Adults in Family <br><span class="text-xs text-gray-500 font-normal">সাক্ষর
                            প্রাপ্তবয়স্ক সংখ্যা</span></label>
                    <input type="number" wire:model="formData.num_literate_adults"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Illiterate
                        Adults in Family <br><span
                            class="text-xs text-gray-500 font-normal">নিরক্ষর প্রাপ্তবয়স্ক
                            সংখ্যা</span></label>
                    <input type="number" wire:model="formData.num_illiterate_adults"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        {{-- Other Income Filters --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">D3</span>
                    Other Income Filters | অন্যান্য আয়ের বিবরণ
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Holding
                        Constitutional Post? <br><span
                            class="text-xs text-gray-500 font-normal">সাংবিধানিক পদে আছেন
                            কি?</span></label>
                    <select wire:model="formData.has_constitutional_post"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Yes">Yes / হ্যাঁ</option>
                        <option value="No">No / না</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Constitutional
                        Post Details <br><span class="text-xs text-gray-500 font-normal">সাংবিধানিক
                            পদের বিবরণ</span></label>
                    <input type="text" wire:model="formData.constitutional_post_details"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Specify post details">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Registered under
                        GST? <br><span class="text-xs text-gray-500 font-normal">জিএসটি নথিভুক্ত
                            কি?</span></label>
                    <select wire:model="formData.has_gst_reg"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Yes">Yes / হ্যাঁ</option>
                        <option value="No">No / না</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">GSTIN <br><span
                            class="text-xs text-gray-500 font-normal">জিএসটিআইএন
                            নম্বর</span></label>
                    <input type="text" wire:model="formData.gstin" placeholder="GST Number"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Is Government
                        Pensioner? <br><span class="text-xs text-gray-500 font-normal">সরকারি
                            পেনশনভোগী কি?</span></label>
                    <select wire:model="formData.has_pensioner"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Yes">Yes / হ্যাঁ</option>
                        <option value="No">No / না</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Government
                        Pensioner Details <br><span
                            class="text-xs text-gray-500 font-normal">পেনশনভোগীর
                            বিবরণ</span></label>
                    <input type="text" wire:model="formData.pensioner_details"
                        placeholder="Specify pensioner details"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>
    @else
        {{-- Member Specific Income/Employment/Literacy --}}
        @php
            $index = $activeMemberIndex - 1;
        @endphp
        @if (($members[$index]['member_type'] ?? 'adult') === 'adult')
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
                wire:key="member-income-{{ $index }}">
                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                        <span
                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                            style="background-color: #78350f;">D</span>
                        Member #{{ $activeMemberIndex }} Income & Literacy | সদস্যের পেশা ও
                        সাক্ষরতা
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">PAN Card
                            Number (If available) <br><span
                                class="text-xs text-gray-500 font-normal">প্যান কার্ড
                                নম্বর</span></label>
                        <div x-data="{
                            val: @entangle('members.' . $index . '.pan_no'),
                            error: '',
                            valid: false,
                            check() {
                                if (!this.val) {
                                    this.valid = false;
                                    this.error = '';
                                    return;
                                }
                                let cleaned = window.cleanInput.alphaNumericUpper(this.val, 10);
                                if (this.val !== cleaned) { this.val = cleaned; }
                                this.valid = window.checkValid.pan(this.val);
                                this.error = this.val.length > 0 && !this.valid ? 'Format: AAAAA1111A' : '';
                            }
                        }" x-init="check();
                        $watch('val', () => check())">
                            <input type="text" x-model="val" maxlength="10"
                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono">
                            <div class="flex items-center gap-2 mt-0.5"
                                style="min-height: 1.25rem;">
                                <span x-show="valid"
                                    class="text-xs text-green-600 font-semibold">✓ Valid PAN</span>
                                <span x-show="error" x-text="error"
                                    class="text-red-500 text-xs font-semibold"></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nature of
                            Employment <br><span
                                class="text-xs text-gray-500 font-normal">কর্মসংস্থানের
                                বিবরণ</span></label>
                        <select wire:model="members.{{ $index }}.employment_nature"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select --</option>
                            @foreach ($employmentNatures as $nature)
                                @php
                                    $val = $nature;
                                    if ($nature === 'Salaried, in Private Sector') {
                                        $val = 'Salaried in Private';
                                    } elseif (
                                        $nature ===
                                        'Formal Sector Self-Employed (Entrepreneur/Business/Proprietor/etc.)'
                                    ) {
                                        $val = 'Formal Sector Self-Employed';
                                    } elseif (
                                        $nature ===
                                        'Informal Sector Self-Employed (Artisan/Craftsman/Farmer/etc.)'
                                    ) {
                                        $val = 'Informal Sector Self-Employed';
                                    }
                                @endphp
                                <option value="{{ $val }}">
                                    {{ $nature }}
                                    @if ($nature === 'Government Sector')
                                        / সরকারি চাকরি
                                    @elseif($nature === 'Salaried, in Private Sector')
                                        / বেসরকারি চাকরি
                                    @elseif($nature === 'Formal Sector Self-Employed (Entrepreneur/Business/Proprietor/etc.)')
                                        / স্ব-নিযুক্ত (আনুষ্ঠানিক)
                                    @elseif($nature === 'Part-time job')
                                        / খণ্ডকালীন কাজ
                                    @elseif($nature === 'Informal Sector Self-Employed (Artisan/Craftsman/Farmer/etc.)')
                                        / স্ব-নিযুক্ত (অনানুষ্ঠানিক)
                                    @elseif($nature === 'Housewife')
                                        / গৃহিণী
                                    @elseif($nature === 'Unemployed')
                                        / বেকার
                                    @elseif($nature === 'Others')
                                        / অন্যান্য
                                    @endif
                                </option>
                            @endforeach
                            @if (!in_array('Migrant Labourer', $employmentNatures))
                                <option value="Migrant Labourer">Migrant Labourer / পরিযায়ী শ্রমিক
                                </option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Literacy
                            Status (Member) <br><span
                                class="text-xs text-gray-500 font-normal">সদস্যের সাক্ষরতা
                                স্থিতি</span></label>
                        <select wire:model="members.{{ $index }}.literate_status"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select --</option>
                            <option value="Literate">Literate / সাক্ষর</option>
                            <option value="Illiterate">Illiterate / নিরক্ষর</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Highest
                            Educational Qualification <br><span
                                class="text-xs text-gray-500 font-normal">সর্বোচ্চ শিক্ষাগত
                                যোগ্যতা</span></label>
                        <input type="text"
                            wire:model="members.{{ $index }}.highest_qualification"
                            placeholder="e.g. Graduate, Class X"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
