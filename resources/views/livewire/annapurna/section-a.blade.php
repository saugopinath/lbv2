<div class="space-y-6">
    @if ($activeMemberIndex === 0)
        {{-- HOF Basic Identity --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">A1</span>
                    Family Head Identity | পরিবার প্রধানের পরিচয়
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Name of Head of
                        Family (HOF) * <br><span class="text-xs text-gray-500 font-normal">পরিবার
                            প্রধানের নাম</span></label>
                    <div x-data="{
                        val: @entangle('formData.hof_name'),
                        error: '',
                        valid: false,
                        check() {
                            if (!this.val) {
                                this.valid = false;
                                this.error = '';
                                return;
                            }
                            let cleaned = window.cleanInput.lettersOnly(this.val);
                            if (this.val !== cleaned) { this.val = cleaned; }
                            this.valid = window.checkValid.name(this.val);
                            this.error = this.val.length > 0 && !this.valid ? 'Letters/dots/spaces only' : '';
                        }
                    }" x-init="check();
                    $watch('val', () => check())">
                        <input type="text" x-model="val"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                            <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                Valid</span>
                            <span x-show="error" x-text="error" class="text-red-500 text-xs font-semibold"></span>
                        </div>
                    </div>
                    @error('formData.hof_name')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Birth of
                        HOF * <br><span class="text-xs text-gray-500 font-normal">জন্ম
                            তারিখ</span></label>
                    <input type="date" wire:model.live="formData.hof_dob"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('formData.hof_dob')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Gender of HOF *
                        <br><span class="text-xs text-gray-500 font-normal">লিঙ্গ</span></label>
                    <select wire:model.live="formData.hof_gender"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        @foreach ($genders as $gender)
                            <option value="{{ $gender }}">
                                {{ $gender }}
                                @if ($gender === 'Male')
                                    / পুরুষ
                                @elseif($gender === 'Female')
                                    / মহিলা
                                @elseif($gender === 'Other')
                                    / অন্যান্য
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('formData.hof_gender')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                @if ($this->isHofFemale25to60())
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Applying for
                            Annapurna Yojana? <br><span class="text-xs text-gray-500 font-normal">অন্নপূর্ণা যোজনার জন্য
                                আবেদন
                                করছেন?</span></label>
                        <select wire:model="formData.hof_applying_for_ay"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Yes">Yes / হ্যাঁ</option>
                            <option value="No">No / না</option>
                        </select>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Aadhaar of HOF *
                        <br><span class="text-xs text-gray-500 font-normal">আধার
                            নম্বর</span></label>
                    <div x-data="{
                        val: @entangle('formData.hof_aadhaar'),
                        error: '',
                        valid: false,
                        check() {
                            if (!this.val) {
                                this.valid = false;
                                this.error = '';
                                return;
                            }
                            let cleaned = window.cleanInput.numeric(this.val, 12);
                            if (this.val !== cleaned) { this.val = cleaned; }
                            this.valid = window.checkValid.aadhaar(this.val);
                            this.error = this.val.length > 0 && !this.valid ? 'Invalid checksum' : '';
                        }
                    }" x-init="check();
                    $watch('val', () => check())">
                        <input type="text" x-model="val" maxlength="12"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                        <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                            <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                Valid Aadhaar</span>
                            <span x-show="error" x-text="error" class="text-red-500 text-xs font-semibold"></span>
                        </div>
                    </div>
                    @error('formData.hof_aadhaar')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror

                    <x-upload-button doc-id="101" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Household ID of
                        Digital Ration Card, if any <br><span class="text-xs text-gray-500 font-normal">রেশন কার্ডের
                            গৃহস্থালি
                            আইডি</span></label>
                    <input type="text" wire:model="formData.hof_ration_card_id"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Family
                        Members (number only)<br><span class="text-xs text-gray-500 font-normal">পরিবারের মোট সদস্য
                            সংখ্যা</span></label>
                    <input type="number" min="1" wire:model.live="formData.num_family_members" readonly
                        class="w-full border border-gray-300 rounded p-2 text-sm font-semibold text-gray-700 bg-gray-100 cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Contact No *
                        <br><span class="text-xs text-gray-500 font-normal">যোগাযোগ নম্বর
                            (মোবাইল)</span></label>
                    <div x-data="{
                        val: @entangle('formData.contact_no'),
                        error: '',
                        valid: false,
                        check() {
                            if (!this.val) {
                                this.valid = false;
                                this.error = '';
                                return;
                            }
                            let cleaned = window.cleanInput.numeric(this.val, 10);
                            if (this.val !== cleaned) { this.val = cleaned; }
                            this.valid = window.checkValid.contact_no(this.val);
                            this.error = this.val.length > 0 && !this.valid ? 'Must be 10 digits' : '';
                        }
                    }" x-init="check();
                    $watch('val', () => check())">
                        <input type="text" x-model="val" maxlength="10"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                            <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                Valid</span>
                            <span x-show="error" x-text="error" class="text-red-500 text-xs font-semibold"></span>
                        </div>
                    </div>
                    @error('formData.contact_no')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category *
                        <br><span class="text-xs text-gray-500 font-normal">শ্রেণী</span></label>
                    <select wire:model.live="formData.category"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        @foreach ($categories as $cat)
                            @php
                                $val = $cat === 'EWS' ? 'UR-EWS' : $cat;
                            @endphp
                            <option value="{{ $val }}">
                                {{ $val }}
                                @if ($val === 'UR')
                                    / সাধারণ
                                @elseif($val === 'UR-EWS')
                                    / সাধারণ (অর্থনৈতিকভাবে অনগ্রসর)
                                @elseif($val === 'SC')
                                    / তফসিলি জাতি
                                @elseif($val === 'ST')
                                    / তফসিলি উপজাতি
                                @elseif($val === 'OBC')
                                    / অন্যান্য অনগ্রসর শ্রেণী
                                @endif
                            </option>
                        @endforeach
                        @if (!in_array('PVTG', $categories))
                            <option value="PVTG">PVTG / বিশেষ দুর্বল উপজাতি শ্রেণী</option>
                        @endif
                    </select>
                    @error('formData.category')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                @if (in_array($formData['category'] ?? '', ['SC', 'ST', 'OBC']))
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Caste
                            Certificate No. * <br><span class="text-xs text-gray-500 font-normal">জাতিগত সংশাপত্র
                                নং</span></label>
                        <input type="text" wire:model="formData.caste_certificate_no"
                            placeholder="Enter Caste Certificate Number"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('formData.caste_certificate_no')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                        <x-upload-button doc-id="106" />
                    </div>
                @elseif (($formData['category'] ?? '') == 'UR-EWS')
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">EWS
                            Certificate No. * <br><span class="text-xs text-gray-500 font-normal">ইডব্লিউএস সংশাপত্র
                                নং</span></label>
                        <input type="text" wire:model="formData.ews_certificate_no"
                            placeholder="Enter EWS Certificate Number"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('formData.ews_certificate_no')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                        <x-upload-button doc-id="106" />
                    </div>
                @elseif (($formData['category'] ?? '') == 'PVTG')
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">PVTG
                            Certificate/Declaration No. * <br><span class="text-xs text-gray-500 font-normal">পিভিটিজি
                                সংশাপত্র
                                নং</span></label>
                        <input type="text" wire:model="formData.pvtg_certificate_no"
                            placeholder="Enter PVTG ID/Declaration No"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('formData.pvtg_certificate_no')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                        <x-upload-button doc-id="114" />
                    </div>
                @endif
            </div>
        </div>

        {{-- HOF Address --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">A2</span>
                    Address (Permanent Address) | স্থায়ী ঠিকানা
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">State * <br><span
                            class="text-xs text-gray-500 font-normal">রাজ্য</span></label>
                    <input type="text" wire:model="formData.state" readonly
                        class="w-full bg-gray-100 border border-gray-300 rounded p-2 text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">District *
                        <br><span class="text-xs text-gray-500 font-normal">জেলা</span></label>
                    <select wire:model.live="formData.district_id"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select District --</option>
                        @foreach ($districts as $d)
                            <option value="{{ $d->id }}">{{ strtoupper($d->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('formData.district_id')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Rural / Urban *
                        <br><span class="text-xs text-gray-500 font-normal">গ্রামীণ / শহর
                            এলাকা</span></label>
                    <select wire:model.live="formData.rural_urban"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select Rural/Urban --</option>
                        <option value="2">Rural (Block) / গ্রামীণ</option>
                        <option value="1">Urban (Municipality/Corporation) / শহর</option>
                    </select>
                    @error('formData.rural_urban')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Block /
                        Municipality * <br><span class="text-xs text-gray-500 font-normal">ব্লক /
                            পৌরসভা</span></label>
                    <select wire:model.live="formData.blockurban"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        {{ empty($blocks) ? 'disabled' : '' }}>
                        <option value="">-- Select --</option>
                        @foreach ($blocks as $b)
                            <option value="{{ $b->id }}">{{ strtoupper($b->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('formData.blockurban')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">GP / Ward *
                        <br><span class="text-xs text-gray-500 font-normal">গ্রাম পঞ্চায়েত /
                            ওয়ার্ড</span></label>
                    <select wire:model="formData.gpward"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        {{ empty($gps) ? 'disabled' : '' }}>
                        <option value="">-- Select --</option>
                        @foreach ($gps as $g)
                            <option value="{{ $g->id }}">{{ strtoupper($g->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('formData.gpward')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Village / Town /
                        City * <br><span class="text-xs text-gray-500 font-normal">গ্রাম /
                            শহর</span></label>
                    <input type="text" wire:model="formData.village_town"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('formData.village_town')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">House / Premise
                        No <br><span class="text-xs text-gray-500 font-normal">বাড়ির
                            নম্বর</span></label>
                    <input type="text" wire:model="formData.house_no"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Police Station *
                        <br><span class="text-xs text-gray-500 font-normal">থানা</span></label>
                    <input type="text" wire:model="formData.police_station"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('formData.police_station')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Post Office *
                        <br><span class="text-xs text-gray-500 font-normal">ডাকঘর</span></label>
                    <input type="text" wire:model="formData.post_office"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('formData.post_office')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pincode *
                        <br><span class="text-xs text-gray-500 font-normal">পিন কোড</span></label>
                    <div x-data="{
                        val: @entangle('formData.pincode'),
                        error: '',
                        valid: false,
                        check() {
                            if (!this.val) {
                                this.valid = false;
                                this.error = '';
                                return;
                            }
                            let cleaned = window.cleanInput.numeric(this.val, 6);
                            if (this.val !== cleaned) { this.val = cleaned; }
                            this.valid = window.checkValid.pincode(this.val);
                            this.error = this.val.length > 0 && !this.valid ? 'Must be 6 digits' : '';
                        }
                    }" x-init="check();
                    $watch('val', () => check())">
                        <input type="text" x-model="val" maxlength="6"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                        <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                            <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                Valid</span>
                            <span x-show="error" x-text="error" class="text-red-500 text-xs font-semibold"></span>
                        </div>
                    </div>
                    @error('formData.pincode')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- HOF Bank Details --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">A3</span>
                    HOF Bank Details (For Cash Transfer) | পরিবার প্রধানের ব্যাংক বিবরণী
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">HOF IFSC Code *
                        <br><span class="text-xs text-gray-500 font-normal">আইএফএসসি
                            কোড</span></label>
                    <div class="relative" x-data="{
                        val: @entangle('formData.hof_ifsc'),
                        error: '',
                        valid: false,
                        banksList: [],
                        suggestions: [],
                        async loadBanks() {
                            if (this.banksList.length > 0) return;
                            try {
                                let response = await fetch('/js/bank-ifsc-master.json');
                                if (response.ok) {
                                    this.banksList = await response.json();
                                }
                            } catch (e) {
                                console.error('Error loading banks:', e);
                            }
                        },
                        async check() {
                            if (!this.val) {
                                this.valid = false;
                                this.error = '';
                                this.suggestions = [];
                                return;
                            }
                            let cleaned = window.cleanInput.alphaNumericUpper(this.val, 11);
                            if (this.val !== cleaned) { this.val = cleaned; }
                            this.valid = window.checkValid.ifsc(this.val);
                            this.error = this.val.length > 0 && !this.valid ? 'Format: ABCD0123456' : '';
                    
                            if (this.val.length >= 4) {
                                await this.loadBanks();
                                let searchVal = this.val.toUpperCase();
                                this.suggestions = this.banksList
                                    .filter(b => b.ifsc.toUpperCase().startsWith(searchVal))
                                    .slice(0, 8);
                            } else {
                                this.suggestions = [];
                            }
                    
                            if (this.valid) {
                                await this.loadBanks();
                                let found = this.banksList.find(b => b.ifsc.toUpperCase() === this.val.toUpperCase());
                                if (found) {
                                    $wire.set('formData.hof_bank_name', found.bankName);
                                }
                                this.suggestions = [];
                            }
                        },
                        selectSuggestion(item) {
                            this.val = item.ifsc;
                            $wire.set('formData.hof_bank_name', item.bankName);
                            this.suggestions = [];
                            this.valid = true;
                            this.error = '';
                        }
                    }" x-init="check();
                    $watch('val', () => check())"
                        @click.outside="suggestions = []">
                        <input type="text" x-model="val" maxlength="11" placeholder="e.g. SBIN0001234"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono">

                        <!-- Suggestions Dropdown -->
                        <ul x-show="suggestions.length > 0"
                            class="absolute z-50 w-full bg-white border border-gray-300 rounded mt-1 max-h-60 overflow-y-auto shadow-lg text-sm"
                            style="display: none;">
                            <template x-for="item in suggestions" :key="item.ifsc">
                                <li @click="selectSuggestion(item)"
                                    class="p-2 hover:bg-orange-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                    <div class="font-bold text-amber-800" x-text="item.ifsc"></div>
                                    <div class="text-[11px] text-gray-600" x-text="item.bankName"></div>
                                    <div class="text-[10px] text-gray-400 italic" x-text="item.branchName"></div>
                                </li>
                            </template>
                        </ul>

                        <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                            <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                Valid</span>
                            <span x-show="error" x-text="error" class="text-red-500 text-xs font-semibold"></span>
                        </div>
                    </div>
                    @error('formData.hof_ifsc')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">HOF Bank Name *
                        <br><span class="text-xs text-gray-500 font-normal">ব্যাংকের
                            নাম</span></label>
                    <div x-data="{
                        val: @entangle('formData.hof_bank_name'),
                        error: '',
                        valid: false,
                        check() {
                            if (!this.val) {
                                this.valid = false;
                                this.error = '';
                                return;
                            }
                            let cleaned = window.cleanInput.lettersOnly(this.val);
                            if (this.val !== cleaned) { this.val = cleaned; }
                            this.valid = window.checkValid.name(this.val);
                            this.error = this.val.length > 0 && !this.valid ? 'Letters only' : '';
                        }
                    }" x-init="check();
                    $watch('val', () => check())">
                        <input type="text" x-model="val"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                            <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                Valid</span>
                            <span x-show="error" x-text="error" class="text-red-500 text-xs font-semibold"></span>
                        </div>
                    </div>
                    @error('formData.hof_bank_name')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">HOF Account
                        Number * <br><span class="text-xs text-gray-500 font-normal">অ্যাকাউন্ট
                            নম্বর</span></label>
                    <div x-data="{
                        val: @entangle('formData.hof_acc_no'),
                        error: '',
                        valid: false,
                        check() {
                            if (!this.val) {
                                this.valid = false;
                                this.error = '';
                                return;
                            }
                            let cleaned = window.cleanInput.numeric(this.val, 18);
                            if (this.val !== cleaned) { this.val = cleaned; }
                            this.valid = window.checkValid.acc_no(this.val);
                            this.error = this.val.length > 0 && !this.valid ? 'Must be 9-18 digits' : '';
                        }
                    }" x-init="check();
                    $watch('val', () => check())">
                        <input type="text" x-model="val" maxlength="18"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                        <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                            <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                Valid</span>
                            <span x-show="error" x-text="error" class="text-red-500 text-xs font-semibold"></span>
                        </div>
                    </div>
                    @error('formData.hof_acc_no')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                    <x-upload-button doc-id="104" />
                </div>
            </div>
        </div>

        {{-- HOF EPIC / Voter Details --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">A4</span>
                    HOF EPIC/Voter Card Details | ভোটার কার্ড বিবরণী
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">HOF EPIC/Voter
                        No. <br><span class="text-xs text-gray-500 font-normal">ভোটার কার্ড
                            নম্বর</span></label>
                    <div x-data="{
                        val: @entangle('formData.hof_epic_no'),
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
                            this.valid = window.checkValid.epic(this.val);
                            this.error = this.val.length > 0 && !this.valid ? 'Format: AAA1234567' : '';
                        }
                    }" x-init="check();
                    $watch('val', () => check())">
                        <input type="text" x-model="val" maxlength="10"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono">
                        <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                            <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                Valid EPIC</span>
                            <span x-show="error" x-text="error" class="text-red-500 text-xs font-semibold"></span>
                        </div>
                    </div>
                    @error('formData.hof_epic_no')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                    <x-upload-button doc-id="102" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Assembly Constituency No. <br><span
                            class="text-xs text-gray-500 font-normal">বিধানসভা নং</span></label>
                    <select wire:model.live="formData.hof_assembly_constituency"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select AC --</option>
                        @foreach ($this->getFilteredAssemblies() as $assembly)
                            <option value="{{ $assembly['id'] }}">{{ $assembly['text'] }}</option>
                        @endforeach
                    </select>
                    @error('formData.hof_assembly_constituency')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Part No. of Electoral Roll <br><span
                            class="text-xs text-gray-500 font-normal">পার্ট নং</span></label>
                    <input type="text" wire:model.live="formData.hof_part_no"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('formData.hof_part_no')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    @else
        {{-- Member Basic Identity --}}
        @php
            $index = $activeMemberIndex - 1;
        @endphp
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5" wire:key="member-identity-{{ $index }}">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">M1</span>
                    Member #{{ $activeMemberIndex }} Basic Identity | সদস্যের পরিচয় ও সম্পর্ক
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4 pb-4 border-b border-gray-200">
                <div class="md:col-span-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Member Category *
                        <br><span class="text-xs text-gray-500 font-normal">সদস্যের
                            বিভাগ</span></label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" wire:model.live="members.{{ $index }}.member_type"
                                value="adult" class="h-4 w-4 text-amber-700 border-gray-300 focus:ring-amber-500">
                            <span class="ml-2 text-sm font-medium text-gray-900">Adult /
                                প্রাপ্তবয়স্ক</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" wire:model.live="members.{{ $index }}.member_type"
                                value="child" class="h-4 w-4 text-amber-700 border-gray-300 focus:ring-amber-500">
                            <span class="ml-2 text-sm font-medium text-gray-900">Child /
                                শিশু</span>
                        </label>
                    </div>
                    @error("members.{$index}.member_type")
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name *
                        <br><span class="text-xs text-gray-500 font-normal">সদস্যের
                            নাম</span></label>
                    <div x-data="{
                        val: @entangle('members.' . $index . '.name'),
                        error: '',
                        valid: false,
                        check() {
                            if (!this.val) {
                                this.valid = false;
                                this.error = '';
                                return;
                            }
                            let cleaned = window.cleanInput.lettersOnly(this.val);
                            if (this.val !== cleaned) { this.val = cleaned; }
                            this.valid = window.checkValid.name(this.val);
                            this.error = this.val.length > 0 && !this.valid ? 'Letters/dots/spaces' : '';
                        }
                    }" x-init="check();
                    $watch('val', () => check())">
                        <input type="text" x-model="val"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                            <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                Valid</span>
                            <span x-show="error" x-text="error" class="text-red-500 text-xs font-semibold"></span>
                        </div>
                    </div>
                    @error("members.{$index}.name")
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Birth *
                        <br><span class="text-xs text-gray-500 font-normal">জন্ম
                            তারিখ</span></label>
                    <input type="date" wire:model="members.{{ $index }}.dob"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error("members.{$index}.dob")
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Gender *
                        <br><span class="text-xs text-gray-500 font-normal">লিঙ্গ</span></label>
                    <select wire:model="members.{{ $index }}.gender"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        @foreach ($genders as $gender)
                            <option value="{{ $gender }}">
                                {{ $gender }}
                                @if ($gender === 'Male')
                                    / পুরুষ
                                @elseif($gender === 'Female')
                                    / মহিলা
                                @elseif($gender === 'Other')
                                    / অন্যান্য
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error("members.{$index}.gender")
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Relation to HOF *
                        <br><span class="text-xs text-gray-500 font-normal">পরিবার প্রধানের সাথে
                            সম্পর্ক</span></label>
                    <div x-data="{
                        realVal: @entangle('members.' . $index . '.relation'),
                        selectVal: '',
                        customVal: '',
                        standards: {{ json_encode(array_values(array_filter($relations, fn($r) => $r !== 'Others'))) }},
                    
                        init() {
                            this.updateFromReal();
                            this.$watch('realVal', value => {
                                this.updateFromReal();
                            });
                        },
                        updateFromReal() {
                            if (!this.realVal) {
                                this.selectVal = '';
                                this.customVal = '';
                            } else if (this.standards.includes(this.realVal)) {
                                this.selectVal = this.realVal;
                                this.customVal = '';
                            } else {
                                this.selectVal = 'Others';
                                this.customVal = this.realVal;
                            }
                        },
                        onSelectChange() {
                            if (this.selectVal === 'Others') {
                                this.realVal = this.customVal || 'Others';
                            } else {
                                this.realVal = this.selectVal;
                            }
                        },
                        onCustomChange() {
                            this.realVal = this.customVal || 'Others';
                        }
                    }" x-init="init()">
                        <select x-model="selectVal" @change="onSelectChange()"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select --</option>
                            @foreach ($relations as $rel)
                                <option value="{{ $rel }}">
                                    {{ $rel }}
                                    @if ($rel === 'Spouse')
                                        / স্ত্রী/স্বামী
                                    @elseif($rel === 'Son')
                                        / পুত্র
                                    @elseif($rel === 'Daughter')
                                        / কন্যা
                                    @elseif($rel === 'Father')
                                        / পিতা
                                    @elseif($rel === 'Mother')
                                        / মাতা
                                    @elseif($rel === 'Brother')
                                        / ভাই
                                    @elseif($rel === 'Sister')
                                        / বোন
                                    @elseif($rel === 'Father-in-law')
                                        / শ্বশুর
                                    @elseif($rel === 'Mother-in-law')
                                        / শাশুড়ি
                                    @elseif($rel === 'Son-in-law')
                                        / জামাতা
                                    @elseif($rel === 'Daughter-in-law')
                                        / পুত্রবধূ
                                    @elseif($rel === 'Grandson')
                                        / নাতি
                                    @elseif($rel === 'Granddaughter')
                                        / নাতনি
                                    @elseif($rel === 'Grandfather')
                                        / ঠাকুরদা/দাদু
                                    @elseif($rel === 'Grandmother')
                                        / ঠাকুমা/দিদিমা
                                    @elseif($rel === 'Uncle')
                                        / কাকা/জ্যাঠা/মামা
                                    @elseif($rel === 'Aunt')
                                        / কাকিমা/জেঠিমা/মাসি/পিসি
                                    @elseif($rel === 'Nephew')
                                        / ভাইপো/ভাগ্নে
                                    @elseif($rel === 'Niece')
                                        / ভাইঝি/ভাগ্নি
                                    @elseif($rel === 'Others')
                                        / অন্যান্য
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <div x-show="selectVal === 'Others'" class="mt-2" style="display: none;">
                            <input type="text" x-model="customVal" @input="onCustomChange()"
                                placeholder="Specify Relationship / সম্পর্ক উল্লেখ করুন"
                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    @error("members.{$index}.relation")
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Aadhaar Number
                        (Optional for child &lt;5 years)<br><span class="text-xs text-gray-500 font-normal">আধার
                            নম্বর</span></label>
                    <div x-data="{
                        val: @entangle('members.' . $index . '.aadhaar'),
                        error: '',
                        valid: false,
                        check() {
                            if (!this.val) {
                                this.valid = false;
                                this.error = '';
                                return;
                            }
                            let cleaned = window.cleanInput.numeric(this.val, 12);
                            if (this.val !== cleaned) { this.val = cleaned; }
                            this.valid = window.checkValid.aadhaar(this.val);
                            this.error = this.val.length > 0 && !this.valid ? 'Invalid checksum' : '';
                        }
                    }" x-init="check();
                    $watch('val', () => check())">
                        <input type="text" x-model="val" maxlength="12"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                        <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                            <span x-show="valid" class="text-xs text-green-600 font-semibold">✓
                                Valid Aadhaar</span>
                            <span x-show="error" x-text="error" class="text-red-500 text-xs font-semibold"></span>
                        </div>
                    </div>
                    @error("members.{$index}.aadhaar")
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @if (($members[$index]['member_type'] ?? 'adult') === 'adult')
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4 pt-4 border-t border-gray-200">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Applying for
                            Annapurna Yojana? <br><span class="text-xs text-gray-500 font-normal">অন্নপূর্ণা যোজনার
                                জন্য
                                আবেদন করছেন কি?</span></label>
                        <select wire:model="members.{{ $index }}.applying_for_ay"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="No">No / না</option>
                            <option value="Yes">Yes / হ্যাঁ</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Member
                            EPIC/Voter No. <br><span class="text-xs text-gray-500 font-normal">ভোটার কার্ড
                                নম্বর</span></label>
                        <div x-data="{
                            val: @entangle('members.' . $index . '.epic_no'),
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
                                this.valid = window.checkValid.epic(this.val);
                                this.error = this.val.length > 0 && !this.valid ? 'Format: AAA1234567' : '';
                            }
                        }" x-init="check();
                        $watch('val', () => check())">
                            <input type="text" x-model="val" maxlength="10"
                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono">
                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓ Valid
                                    EPIC</span>
                                <span x-show="error" x-text="error"
                                    class="text-red-500 text-xs font-semibold"></span>
                            </div>
                        </div>
                        @error("members.{$index}.epic_no")
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Assembly Constituency No.
                            <br><span class="text-xs text-gray-500 font-normal">বিধানসভা নং</span></label>
                        <select wire:model.live="members.{{ $index }}.assembly_constituency"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select AC --</option>
                            @foreach ($this->getFilteredAssemblies() as $assembly)
                                <option value="{{ $assembly['id'] }}">{{ $assembly['text'] }}</option>
                            @endforeach
                        </select>
                        @error("members.{$index}.assembly_constituency")
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Part No. of Electoral Roll
                            <br><span class="text-xs text-gray-500 font-normal">পার্ট নং</span></label>
                        <input type="text" wire:model.live="members.{{ $index }}.part_no"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error("members.{$index}.part_no")
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @endif
        </div>

        @if (($members[$index]['member_type'] ?? 'adult') === 'adult')
            {{-- Member Bank details --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mt-4"
                wire:key="member-bank-{{ $index }}">
                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                        <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                            style="background-color: #78350f;">M2</span>
                        Member Bank Details (For Cash Transfer) | সদস্যের ব্যাংক বিবরণী
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">IFSC Code *
                            <br><span class="text-xs text-gray-500 font-normal">আইএফএসসি
                                কোড</span></label>
                        <div class="relative" x-data="{
                            val: @entangle('members.' . $index . '.ifsc'),
                            index: {{ $index }},
                            error: '',
                            valid: false,
                            banksList: [],
                            suggestions: [],
                            async loadBanks() {
                                if (this.banksList.length > 0) return;
                                try {
                                    let response = await fetch('/js/bank-ifsc-master.json');
                                    if (response.ok) {
                                        this.banksList = await response.json();
                                    }
                                } catch (e) {
                                    console.error('Error loading banks:', e);
                                }
                            },
                            async check() {
                                if (!this.val) {
                                    this.valid = false;
                                    this.error = '';
                                    this.suggestions = [];
                                    return;
                                }
                                let cleaned = window.cleanInput.alphaNumericUpper(this.val, 11);
                                if (this.val !== cleaned) { this.val = cleaned; }
                                this.valid = window.checkValid.ifsc(this.val);
                                this.error = this.val.length > 0 && !this.valid ? 'Format: ABCD0123456' : '';
                        
                                if (this.val.length >= 4) {
                                    await this.loadBanks();
                                    let searchVal = this.val.toUpperCase();
                                    this.suggestions = this.banksList
                                        .filter(b => b.ifsc.toUpperCase().startsWith(searchVal))
                                        .slice(0, 8);
                                } else {
                                    this.suggestions = [];
                                }
                        
                                if (this.valid) {
                                    await this.loadBanks();
                                    let found = this.banksList.find(b => b.ifsc.toUpperCase() === this.val.toUpperCase());
                                    if (found) {
                                        $wire.set('members.' + this.index + '.bank_name', found.bankName);
                                    }
                                    this.suggestions = [];
                                }
                            },
                            selectSuggestion(item) {
                                this.val = item.ifsc;
                                $wire.set('members.' + this.index + '.bank_name', item.bankName);
                                this.suggestions = [];
                                this.valid = true;
                                this.error = '';
                            }
                        }" x-init="check();
                        $watch('val', () => check())"
                            @click.outside="suggestions = []">
                            <input type="text" x-model="val" maxlength="11" placeholder="e.g. SBIN0001234"
                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono">

                            <!-- Suggestions Dropdown -->
                            <ul x-show="suggestions.length > 0"
                                class="absolute z-50 w-full bg-white border border-gray-300 rounded mt-1 max-h-60 overflow-y-auto shadow-lg text-sm"
                                style="display: none;">
                                <template x-for="item in suggestions" :key="item.ifsc">
                                    <li @click="selectSuggestion(item)"
                                        class="p-2 hover:bg-orange-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                        <div class="font-bold text-amber-800" x-text="item.ifsc"></div>
                                        <div class="text-[11px] text-gray-600" x-text="item.bankName"></div>
                                        <div class="text-[10px] text-gray-400 italic" x-text="item.branchName"></div>
                                    </li>
                                </template>
                            </ul>

                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓ Valid</span>
                                <span x-show="error" x-text="error"
                                    class="text-red-500 text-xs font-semibold"></span>
                            </div>
                        </div>
                        @error("members.{$index}.ifsc")
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bank Name *
                            <br><span class="text-xs text-gray-500 font-normal">ব্যাংকের
                                নাম</span></label>
                        <div x-data="{
                            val: @entangle('members.' . $index . '.bank_name'),
                            error: '',
                            valid: false,
                            check() {
                                if (!this.val) {
                                    this.valid = false;
                                    this.error = '';
                                    return;
                                }
                                let cleaned = window.cleanInput.lettersOnly(this.val);
                                if (this.val !== cleaned) { this.val = cleaned; }
                                this.valid = window.checkValid.name(this.val);
                                this.error = this.val.length > 0 && !this.valid ? 'Letters only' : '';
                            }
                        }" x-init="check();
                        $watch('val', () => check())">
                            <input type="text" x-model="val"
                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓ Valid</span>
                                <span x-show="error" x-text="error"
                                    class="text-red-500 text-xs font-semibold"></span>
                            </div>
                        </div>
                        @error("members.{$index}.bank_name")
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Account
                            Number * <br><span class="text-xs text-gray-500 font-normal">অ্যাকাউন্ট
                                নম্বর</span></label>
                        <div x-data="{
                            val: @entangle('members.' . $index . '.acc_no'),
                            error: '',
                            valid: false,
                            check() {
                                if (!this.val) {
                                    this.valid = false;
                                    this.error = '';
                                    return;
                                }
                                let cleaned = window.cleanInput.numeric(this.val, 18);
                                if (this.val !== cleaned) { this.val = cleaned; }
                                this.valid = window.checkValid.acc_no(this.val);
                                this.error = this.val.length > 0 && !this.valid ? 'Must be 9-18 digits' : '';
                            }
                        }" x-init="check();
                        $watch('val', () => check())">
                            <input type="text" x-model="val" maxlength="18"
                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                            <div class="flex items-center gap-2 mt-0.5" style="min-height: 1.25rem;">
                                <span x-show="valid" class="text-xs text-green-600 font-semibold">✓ Valid</span>
                                <span x-show="error" x-text="error"
                                    class="text-red-500 text-xs font-semibold"></span>
                            </div>
                        </div>
                        @error("members.{$index}.acc_no")
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-5 mt-4 text-center text-amber-900 shadow-sm">
                <p class="text-xs font-semibold">Note: Bank account and Voter details are not
                    required for child members.</p>
                <p class="text-[10px] text-amber-700 mt-1"> hishu sadoshoder jonyo bank account abong voter biboronir
                    proyojon nei.</p>
            </div>
        @endif
    @endif
</div>
