<div class="space-y-6">
    @if ($activeMemberIndex === 0)
        {{-- Family Assets for HOF --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">C1</span>
                    Family Assets | পারিবারিক সম্পদের বিবরণ
                </h3>
            </div>

            {{-- Land & house --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">House size: &ge;
                        3 Pucca Rooms? * <br><span class="text-xs text-gray-500 font-normal">৩ বা
                            তার বেশি পাকা ঘর আছে কি?</span></label>
                    <select wire:model="formData.has_pucca_rooms"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Yes">Yes / হ্যাঁ</option>
                        <option value="No">No / না</option>
                    </select>
                    @error('formData.has_pucca_rooms')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Owns land? *
                        <br><span class="text-xs text-gray-500 font-normal">জমি আছে
                            কি?</span></label>
                    <select wire:model.live="formData.owns_land"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Yes">Yes / হ্যাঁ</option>
                        <option value="No">No / না</option>
                    </select>
                    @error('formData.owns_land')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                @if (($formData['owns_land'] ?? '') === 'Yes')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Size of Land (in
                        Decimals) <br><span class="text-xs text-gray-500 font-normal">জমির মোট
                            পরিমাণ (ডেসিমেলে)</span></label>
                    <input type="number" step="0.01" wire:model="formData.land_size_decimals"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                @endif
            </div>

            {{-- 4-Wheeler + Vehicle Count --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Owns 4-Wheeler? *
                        <br><span class="text-xs text-gray-500 font-normal">৪-চাকার গাড়ি আছে
                            কি?</span></label>
                    <select wire:model.live="formData.owns_4_wheeler"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select --</option>
                        <option value="Yes">Yes / হ্যাঁ</option>
                        <option value="No">No / না</option>
                    </select>
                    @error('formData.owns_4_wheeler')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                @if (($formData['owns_4_wheeler'] ?? '') === 'Yes')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Vehicles
                        <br><span class="text-xs text-gray-500 font-normal">গাড়ির
                            সংখ্যা</span></label>
                    <input type="number" min="0" wire:model.live="formData.num_vehicles"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                @endif
            </div>

            {{-- Dynamic vehicle detail rows — one row per vehicle --}}
            @if (!empty($formData['vehicles']) && count($formData['vehicles']) > 0)
                <div class="mt-4 space-y-2">
                    <p class="text-sm font-semibold text-gray-600 mb-2">Vehicle Details / গাড়ির
                        বিবরণ</p>
                    @foreach ($formData['vehicles'] as $vi => $vehicle)
                        <div wire:key="hof-vehicle-{{ $vi }}"
                            class="grid grid-cols-1 md:grid-cols-3 gap-4 p-3 border border-amber-200 rounded-lg bg-amber-50 items-center">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-700 text-white text-xs font-bold">{{ $vi + 1 }}</span>
                                <span class="text-sm text-gray-700 font-medium">Vehicle
                                    {{ $vi + 1 }}</span>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-600 mb-1">Registration
                                    No. / রেজিস্ট্রেশন নম্বর</label>
                                <div x-data="{
                                    val: @entangle('formData.vehicles.' . $vi . '.reg_no'),
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
                                        this.valid = window.checkValid.vehicle(this.val);
                                        this.error = this.val.length > 0 && !this.valid ? 'Format: WB01AB1234' : '';
                                    }
                                }" x-init="check();
                                $watch('val', () => check())">
                                    <input type="text" x-model="val" maxlength="10"
                                        placeholder="e.g. WB-01-AB-1234"
                                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-amber-500 focus:border-amber-500 uppercase font-mono">
                                    <div class="flex items-center gap-2 mt-0.5"
                                        style="min-height: 1.25rem;">
                                        <span x-show="valid"
                                            class="text-xs text-green-600 font-semibold">✓
                                            Valid</span>
                                        <span x-show="error" x-text="error"
                                            class="text-red-500 text-xs font-semibold"></span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-600 mb-1">Vehicle
                                    Model / মডেল নাম</label>
                                <input type="text"
                                    wire:model="formData.vehicles.{{ $vi }}.model"
                                    placeholder="e.g. Maruti Swift"
                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

        {{-- Health Insurance for HOF --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <span
                        class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                        style="background-color: #78350f;">C2</span>
                    Health Insurance Coverage (HOF) | পরিবার প্রধানের স্বাস্থ্য বীমা বিবরণী
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Health Insurance
                        Type <br><span class="text-xs text-gray-500 font-normal">বীমার
                            প্রকার</span></label>
                    <select wire:model="formData.health_insurance_type"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="None">None / নেই</option>
                        <option value="Government">Government / সরকারি (যেমন স্বাস্থ্যসাথী)
                        </option>
                        <option value="Private">Private / ব্যক্তিগত</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Annual Premium
                        (INR) <br><span class="text-xs text-gray-500 font-normal">বার্ষিক
                            প্রিমিয়াম</span></label>
                    <input type="number" wire:model="formData.health_insurance_premium"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sum Assured (INR)
                        <br><span class="text-xs text-gray-500 font-normal">বীমাকৃত
                            রাশি</span></label>
                    <input type="number" wire:model="formData.health_insurance_sum_assured"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>
    @else
        {{-- Health Insurance for Adult Member --}}
        @php
            $index = $activeMemberIndex - 1;
        @endphp
        @if (($members[$index]['member_type'] ?? 'adult') === 'adult')
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5"
                wire:key="member-insurance-{{ $index }}">
                <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                    <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                        <span
                            class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                            style="background-color: #78350f;">C</span>
                        Health Insurance Coverage (Member #{{ $activeMemberIndex }}) | সদস্যের
                        স্বাস্থ্য বীমা বিবরণী
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Health
                            Insurance Type <br><span
                                class="text-xs text-gray-500 font-normal">বীমার
                                প্রকার</span></label>
                        <select wire:model="members.{{ $index }}.health_insurance_type"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="No">None / নেই</option>
                            <option value="Government">Government / সরকারি (যেমন স্বাস্থ্যসাথী)
                            </option>
                            <option value="Private">Private / ব্যক্তিগত</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Annual
                            Premium (INR) <br><span
                                class="text-xs text-gray-500 font-normal">বার্ষিক
                                প্রিমিয়াম</span></label>
                        <input type="number"
                            wire:model="members.{{ $index }}.health_insurance_premium"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Sum Assured
                            (INR) <br><span class="text-xs text-gray-500 font-normal">বীমাকৃত
                                রাশি</span></label>
                        <input type="number"
                            wire:model="members.{{ $index }}.health_insurance_sum_assured"
                            class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
