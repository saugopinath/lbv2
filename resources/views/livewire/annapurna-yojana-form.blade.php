<form wire:submit.prevent="save" class="max-w-6xl mx-auto my-8 bg-white border-2 rounded-lg shadow-xl overflow-hidden" style="border-color: #b45309;">
    {{-- Custom Theme Color Overrides for Government brand style --}}
    <style>
        .border-indigo-900 {
            border-color: #b45309 !important;
        }
        .text-indigo-950 {
            color: #78350f !important;
        }
        .bg-indigo-50 {
            background-color: #fff7ed !important;
        }
        .border-indigo-200 {
            border-color: #ffedd5 !important;
        }
        .text-indigo-900 {
            color: #b45309 !important;
        }
        [style*="background-color: #1e1b4b"] {
            background-color: #b45309 !important;
        }
        [style*="background-color:#1e1b4b"] {
            background-color: #b45309 !important;
        }
        input[type="checkbox"]:checked {
            background-color: #b45309 !important;
            border-color: #b45309 !important;
        }
        input[type="text"]:focus, select:focus, input[type="number"]:focus, input[type="date"]:focus {
            border-color: #b45309 !important;
            --tw-ring-color: #b45309 !important;
            outline: 2px solid transparent !important;
            outline-offset: 2px !important;
        }
    </style>

    {{-- Government Style Header --}}
    <div class="p-6 border-b-4 border-amber-500" style="background-color: #9a3412; color: #ffffff;">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-inner">
                    <span class="font-bold text-3xl font-serif" style="color: #9a3412;">AY</span>
                </div>
                <div>
                    <h2 class="text-xs md:text-sm font-semibold uppercase tracking-wider" style="color: #fed7aa;">Government of West Bengal</h2>
                    <h1 class="text-xl md:text-2xl font-bold font-serif text-amber-400">ANNAPURNA YOJANA</h1>
                    <p class="text-xs" style="color: #ffedd5;">Department of Food & Supplies | খাদ্য ও সরবরাহ দপ্তর</p>
                </div>
            </div>
            <div class="text-center md:text-right">
                <span class="font-bold text-xs uppercase px-3 py-1 rounded shadow" style="background-color: #f59e0b; color: #78350f;">
                    Family Level Data Collection Form
                </span>
                <p class="text-xs mt-2" style="color: #ffedd5;">পারিবারিক স্তরের তথ্য সংগ্রহপত্র</p>
            </div>
        </div>
    </div>

    {{-- Step Progress Bar --}}
    <div class="bg-gray-100 border-b border-gray-200 px-6 py-4">
        <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-2">
            <div class="flex items-center gap-2">
                <span class="text-xs uppercase font-bold text-gray-500">Progress:</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded" style="background-color: #b45309; color: #ffffff;">Step {{ $currentStep }} of {{ $totalSteps }}</span>
            </div>
            
            <div class="grid grid-cols-4 gap-2 w-full md:w-auto text-center text-xs font-bold">
                <div class="py-2 px-3 rounded" style="{{ $currentStep >= 1 ? 'background-color: #b45309; color: #ffffff;' : 'background-color: #e2e8f0; color: #475569;' }}">
                    1. HOF & Family Identity
                </div>
                <div class="py-2 px-3 rounded" style="{{ $currentStep >= 2 ? 'background-color: #b45309; color: #ffffff;' : 'background-color: #e2e8f0; color: #475569;' }}">
                    2. Ration, Assets & Income
                </div>
                <div class="py-2 px-3 rounded" style="{{ $currentStep >= 3 ? 'background-color: #b45309; color: #ffffff;' : 'background-color: #e2e8f0; color: #475569;' }}">
                    3. Other ID & Status
                </div>
                <div class="py-2 px-3 rounded" style="{{ $currentStep >= 4 ? 'background-color: #b45309; color: #ffffff;' : 'background-color: #e2e8f0; color: #475569;' }}">
                    4. Submit Application
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    <div class="p-6 pb-0">
        @if ($successMessage)
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ $successMessage }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errorMessage)
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ $errorMessage }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('member_limit'))
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded shadow-sm mb-4">
                <p class="text-sm font-medium text-amber-800">{{ session('member_limit') }}</p>
            </div>
        @endif
    </div>

    {{-- Form Body --}}
    <div class="p-6">
        
        {{-- STEP 1: FAMILY IDENTITY & ADDRESS --}}
        @if ($currentStep == 1)
            <div class="space-y-8">
                {{-- A. Family Head Identity --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                            <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" style="background-color: #1e1b4b;">A</span>
                            Family Head Identity | পরিবার প্রধানের পরিচয়
                        </h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Name of Head of Family (HOF) * <br><span class="text-xs text-gray-500 font-normal">পরিবার প্রধানের নাম</span></label>
                            <input type="text" wire:model="formData.hof_name" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('formData.hof_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Birth of HOF * <br><span class="text-xs text-gray-500 font-normal">জন্ম তারিখ</span></label>
                            <input type="date" wire:model="formData.hof_dob" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('formData.hof_dob') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Gender of HOF * <br><span class="text-xs text-gray-500 font-normal">লিঙ্গ</span></label>
                            <select wire:model="formData.hof_gender" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Select --</option>
                                <option value="Male">Male / পুরুষ</option>
                                <option value="Female">Female / মহিলা</option>
                                <option value="Other">Other / অন্যান্য</option>
                            </select>
                            @error('formData.hof_gender') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Aadhaar of HOF * <br><span class="text-xs text-gray-500 font-normal">আধার নম্বর</span></label>
                            <input type="text" wire:model="formData.hof_aadhaar" maxlength="12" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('formData.hof_aadhaar') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Household ID of Digital Ration Card, if any <br><span class="text-xs text-gray-500 font-normal">রেশন কার্ডের গৃহস্থালি আইডি</span></label>
                            <input type="text" wire:model="formData.hof_ration_card_id" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Contact No * <br><span class="text-xs text-gray-500 font-normal">যোগাযোগ নম্বর (মোবাইল)</span></label>
                            <input type="text" wire:model="formData.contact_no" maxlength="10" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('formData.contact_no') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Category * <br><span class="text-xs text-gray-500 font-normal">শ্রেণী</span></label>
                            <select wire:model.live="formData.category" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Select --</option>
                                <option value="UR">UR / সাধারণ</option>
                                <option value="UR-EWS">UR-EWS / সাধারণ (অর্থনৈতিকভাবে অনগ্রসর)</option>
                                <option value="SC">SC / তফসিলি জাতি</option>
                                <option value="ST">ST / তফসিলি উপজাতি</option>
                                <option value="OBC">OBC / অন্যান্য অনগ্রসর শ্রেণী</option>
                                <option value="PVTG">PVTG / বিশেষ দুর্বল উপজাতি শ্রেণী</option>
                            </select>
                            @error('formData.category') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Conditional Certificate Input Fields --}}
                        @if (in_array($formData['category'], ['SC', 'ST', 'OBC']))
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Caste Certificate No. * <br><span class="text-xs text-gray-500 font-normal">জাতিগত সংশাপত্র নং</span></label>
                                <input type="text" wire:model="formData.caste_certificate_no" placeholder="Enter Caste Certificate Number" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('formData.caste_certificate_no') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @elseif ($formData['category'] == 'UR-EWS')
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">EWS Certificate No. * <br><span class="text-xs text-gray-500 font-normal">ইডব্লিউএস সংশাপত্র নং</span></label>
                                <input type="text" wire:model="formData.ews_certificate_no" placeholder="Enter EWS Certificate Number" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('formData.ews_certificate_no') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @elseif ($formData['category'] == 'PVTG')
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">PVTG Certificate/Declaration No. * <br><span class="text-xs text-gray-500 font-normal">পিভিটিজি সংশাপত্র নং</span></label>
                                <input type="text" wire:model="formData.pvtg_certificate_no" placeholder="Enter PVTG ID/Declaration No" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('formData.pvtg_certificate_no') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Family Members (number only)<br><span class="text-xs text-gray-500 font-normal">পরিবারের মোট সদস্য সংখ্যা</span></label>
                            <input type="text" wire:model="formData.num_family_members" readonly class="w-full bg-gray-200 border border-gray-300 rounded p-2 text-sm">
                        </div>
                    </div>
                </div>

                {{-- B. Permanent Address --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                            <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" style="background-color: #1e1b4b;">B</span>
                            Address (Permanent Address) | স্থায়ী ঠিকানা
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">State * <br><span class="text-xs text-gray-500 font-normal">রাজ্য</span></label>
                            <input type="text" wire:model="formData.state" readonly class="w-full bg-gray-200 border border-gray-300 rounded p-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">District * <br><span class="text-xs text-gray-500 font-normal">জেলা</span></label>
                            <select wire:model.live="formData.district_id" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Select District --</option>
                                @foreach ($districts as $d)
                                    <option value="{{ $d->id }}">{{ strtoupper($d->name) }}</option>
                                @endforeach
                            </select>
                            @error('formData.district_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Rural / Urban * <br><span class="text-xs text-gray-500 font-normal">গ্রামীণ / শহর এলাকা</span></label>
                            <select wire:model.live="formData.rural_urban" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Select Rural/Urban --</option>
                                <option value="2">Rural (Block) / গ্রামীণ</option>
                                <option value="1">Urban (Municipality/Corporation) / শহর</option>
                            </select>
                            @error('formData.rural_urban') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Block / Municipality * <br><span class="text-xs text-gray-500 font-normal">ব্লক / পৌরসভা</span></label>
                            <select wire:model.live="formData.blockurban" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" {{ empty($blocks) ? 'disabled' : '' }}>
                                <option value="">-- Select --</option>
                                @foreach ($blocks as $b)
                                    <option value="{{ $b->id }}">{{ strtoupper($b->name) }}</option>
                                @endforeach
                            </select>
                            @error('formData.blockurban') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">GP / Ward * <br><span class="text-xs text-gray-500 font-normal">গ্রাম পঞ্চায়েত / ওয়ার্ড</span></label>
                            <select wire:model="formData.gpward" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" {{ empty($gps) ? 'disabled' : '' }}>
                                <option value="">-- Select --</option>
                                @foreach ($gps as $g)
                                    <option value="{{ $g->id }}">{{ strtoupper($g->name) }}</option>
                                @endforeach
                            </select>
                            @error('formData.gpward') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Village / Town / City * <br><span class="text-xs text-gray-500 font-normal">গ্রাম / শহর</span></label>
                            <input type="text" wire:model="formData.village_town" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('formData.village_town') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">House / Premise No <br><span class="text-xs text-gray-500 font-normal">বাড়ির নম্বর</span></label>
                            <input type="text" wire:model="formData.house_no" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Police Station * <br><span class="text-xs text-gray-500 font-normal">থানা</span></label>
                            <input type="text" wire:model="formData.police_station" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('formData.police_station') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Post Office * <br><span class="text-xs text-gray-500 font-normal">ডাকঘর</span></label>
                            <input type="text" wire:model="formData.post_office" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('formData.post_office') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pincode * <br><span class="text-xs text-gray-500 font-normal">পিন কোড</span></label>
                            <input type="text" wire:model="formData.pincode" maxlength="6" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('formData.pincode') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- C. HOF Bank & EPIC Details --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                            <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" style="background-color: #1e1b4b;">C</span>
                            HOF Bank & EPIC Details | পরিবার প্রধানের ব্যাংক ও ভোটার কার্ড বিবরণ
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">HOF Bank Name * <br><span class="text-xs text-gray-500 font-normal">ব্যাংকের নাম</span></label>
                            <input type="text" wire:model="formData.hof_bank_name" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('formData.hof_bank_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">HOF Account Number * <br><span class="text-xs text-gray-500 font-normal">অ্যাকাউন্ট নম্বর</span></label>
                            <input type="text" wire:model="formData.hof_acc_no" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('formData.hof_acc_no') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">HOF IFSC Code * <br><span class="text-xs text-gray-500 font-normal">আইএফএসসি কোড</span></label>
                            <input type="text" wire:model="formData.hof_ifsc" maxlength="11" placeholder="e.g. SBIN0001234" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('formData.hof_ifsc') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">HOF EPIC No. <br><span class="text-xs text-gray-500 font-normal">ভোটার কার্ড নম্বর</span></label>
                            <input type="text" wire:model="formData.hof_epic_no" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">HOF AC & Part No. <br><span class="text-xs text-gray-500 font-normal">বিধানসভা ও পার্ট নং</span></label>
                            <input type="text" wire:model="formData.hof_ac_part_no" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- D. Family Members Details --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <div class="border-b-2 border-indigo-900 pb-2 mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                            <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" style="background-color: #1e1b4b;">D</span>
                            Family Members Details (Max 5) | পরিবারের অন্যান্য সদস্যদের বিবরণ
                        </h3>
                        <button type="button" wire:click="addMember" class="hover:bg-opacity-90 text-white text-xs font-semibold px-4 py-2 rounded shadow transition flex items-center gap-1" style="background-color: #1e1b4b;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Member / সদস্য যোগ করুন
                        </button>
                    </div>

                    @if (empty($members))
                        <div class="text-center py-6 text-gray-500 bg-white border border-dashed border-gray-300 rounded">
                            No other family members added yet. Click the button above to add members.
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach ($members as $index => $member)
                                <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm relative">
                                    <button type="button" wire:click="removeMember({{ $index }})" class="absolute top-2 right-2 text-red-500 hover:text-red-700 transition" title="Remove Member">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    <span class="inline-block bg-indigo-100 text-indigo-900 text-xs font-bold px-2.5 py-1 rounded mb-3">
                                        Member #{{ $index + 1 }}
                                    </span>
                                    
                                    {{-- Primary Member Info --}}
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Full Name *</label>
                                            <input type="text" wire:model="members.{{ $index }}.name" class="w-full border border-gray-300 rounded p-1 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @error("members.{$index}.name") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Date of Birth *</label>
                                            <input type="date" wire:model="members.{{ $index }}.dob" class="w-full border border-gray-300 rounded p-1 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @error("members.{$index}.dob") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Gender *</label>
                                            <select wire:model="members.{{ $index }}.gender" class="w-full border border-gray-300 rounded p-1 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">-- Select --</option>
                                                <option value="Male">Male / পুরুষ</option>
                                                <option value="Female">Female / মহিলা</option>
                                                <option value="Other">Other / অন্যান্য</option>
                                            </select>
                                            @error("members.{$index}.gender") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Relation to Head *</label>
                                            <input type="text" wire:model="members.{{ $index }}.relation" placeholder="e.g. Spouse, Son" class="w-full border border-gray-300 rounded p-1 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @error("members.{$index}.relation") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Aadhaar Number (Optional for &lt;5 years)</label>
                                            <input type="text" wire:model="members.{{ $index }}.aadhaar" maxlength="12" class="w-full border border-gray-300 rounded p-1 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @error("members.{$index}.aadhaar") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Applying for Annapurna Yojana?</label>
                                            <select wire:model="members.{{ $index }}.applying_for_ay" class="w-full border border-gray-300 rounded p-1 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="No">No / না</option>
                                                <option value="Yes">Yes / হ্যাঁ</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">EPIC No.</label>
                                            <input type="text" wire:model="members.{{ $index }}.epic_no" class="w-full border border-gray-300 rounded p-1 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">AC & Part No.</label>
                                            <input type="text" wire:model="members.{{ $index }}.ac_part_no" class="w-full border border-gray-300 rounded p-1 text-sm">
                                        </div>
                                    </div>

                                    {{-- Member Bank Info --}}
                                    <div class="border-t border-gray-200 mt-4 pt-3">
                                        <h4 class="text-xs font-bold text-indigo-900 mb-2">Member Bank Details (for cash transfer)</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1">Bank Name *</label>
                                                <input type="text" wire:model="members.{{ $index }}.bank_name" class="w-full border border-gray-300 rounded p-1 text-sm">
                                                @error("members.{$index}.bank_name") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1">Account Number *</label>
                                                <input type="text" wire:model="members.{{ $index }}.acc_no" class="w-full border border-gray-300 rounded p-1 text-sm">
                                                @error("members.{$index}.acc_no") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1">IFSC Code *</label>
                                                <input type="text" wire:model="members.{{ $index }}.ifsc" maxlength="11" class="w-full border border-gray-300 rounded p-1 text-sm">
                                                @error("members.{$index}.ifsc") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif


        {{-- STEP 2: RATION, ASSETS & INCOME DETAILS --}}
        @if ($currentStep == 2)
            <div class="space-y-8">
                {{-- B. Ration Card / Food Subsidy --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                            <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" style="background-color: #1e1b4b;">A</span>
                            Ration Card / Food Subsidy | রেশন কার্ড বিবরণ
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Do you have a Digital Ration Card? * <br><span class="text-xs text-gray-500 font-normal">রেশন কার্ড আছে কি?</span></label>
                            <select wire:model="formData.has_digital_ration_card" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes / হ্যাঁ</option>
                                <option value="No">No / না</option>
                            </select>
                            @error('formData.has_digital_ration_card') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">If Yes, Card Type <br><span class="text-xs text-gray-500 font-normal">রেশন কার্ডের ধরন</span></label>
                            <select wire:model="formData.ration_card_type" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="AAY">AAY (Antyodaya Anna Yojana)</option>
                                <option value="PHH">PHH (Priority Household)</option>
                                <option value="SPHH">SPHH (Special Priority Household)</option>
                                <option value="RKSY1">RKSY-I</option>
                                <option value="RKSY2">RKSY-II</option>
                                <option value="Non-subsidized">Non-Subsidized / ভর্তুকিহীন</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Lifting Monthly Ration? * <br><span class="text-xs text-gray-500 font-normal">রেশন দোকান থেকে রেশন পাচ্ছেন কি?</span></label>
                            <select wire:model="formData.is_lifting_ration" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes / হ্যাঁ</option>
                                <option value="No">No / না</option>
                            </select>
                            @error('formData.is_lifting_ration') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- C. Assets --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                            <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" style="background-color: #1e1b4b;">B</span>
                            Assets details | সম্পদের বিবরণ
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">House size: &ge; 3 Pucca Rooms? <br><span class="text-xs text-gray-500 font-normal">৩ বা তার বেশি পাকা ঘর আছে কি?</span></label>
                            <select wire:model="formData.has_pucca_rooms" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes / হ্যাঁ</option>
                                <option value="No">No / না</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Owns land? <br><span class="text-xs text-gray-500 font-normal">জমি আছে কি?</span></label>
                            <select wire:model="formData.owns_land" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes / হ্যাঁ</option>
                                <option value="No">No / না</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Size of Land (in Decimals) <br><span class="text-xs text-gray-500 font-normal">জমির মোট পরিমাণ (ডেসিমেলে)</span></label>
                            <input type="number" step="0.01" wire:model="formData.land_size_decimals" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Owns 4-Wheeler? * <br><span class="text-xs text-gray-500 font-normal">৪-চাকার গাড়ি আছে কি?</span></label>
                            <select wire:model="formData.owns_4_wheeler" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes / হ্যাঁ</option>
                                <option value="No">No / না</option>
                            </select>
                            @error('formData.owns_4_wheeler') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Vehicles <br><span class="text-xs text-gray-500 font-normal">গাড়ির সংখ্যা</span></label>
                            <input type="number" wire:model="formData.num_vehicles" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Vehicle Registration No. <br><span class="text-xs text-gray-500 font-normal">রেজিস্ট্রেশন নম্বর</span></label>
                            <input type="text" wire:model="formData.vehicle_reg_no" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Vehicle Model <br><span class="text-xs text-gray-500 font-normal">মডেল নাম</span></label>
                            <input type="text" wire:model="formData.vehicle_model" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                    </div>
                </div>

                {{-- D. Income / Profession --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                            <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" style="background-color: #1e1b4b;">C</span>
                            Income / Profession | আয় ও পেশা
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pays Income / Professional Tax? <br><span class="text-xs text-gray-500 font-normal">কর প্রদান করেন কি?</span></label>
                            <select wire:model="formData.pays_tax" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes / হ্যাঁ</option>
                                <option value="No">No / না</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">PAN Card No. (HOF) <br><span class="text-xs text-gray-500 font-normal">প্যান কার্ড নং</span></label>
                            <input type="text" wire:model="formData.hof_pan_no" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Employment of HOF <br><span class="text-xs text-gray-500 font-normal">প্রধানের কর্মসংস্থান</span></label>
                            <select wire:model="formData.hof_employment_nature" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="Government Sector">Government Sector</option>
                                <option value="Salaried in Private">Salaried in Private</option>
                                <option value="Formal Sector Self-Employed">Formal Sector Self-Employed</option>
                                <option value="Part-time job">Part-time job</option>
                                <option value="Informal Sector Self-Employed">Informal Sector Self-Employed</option>
                                <option value="Migrant Labourer">Migrant Labourer</option>
                                <option value="Unemployed">Unemployed</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Literate Adults <br><span class="text-xs text-gray-500 font-normal">সাক্ষর প্রাপ্তবয়স্ক সংখ্যা</span></label>
                            <input type="number" wire:model="formData.num_literate_adults" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Illiterate Adults <br><span class="text-xs text-gray-500 font-normal">নিরক্ষর প্রাপ্তবয়স্ক সংখ্যা</span></label>
                            <input type="number" wire:model="formData.num_illiterate_adults" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Total Annual Family Income (INR) * <br><span class="text-xs text-gray-500 font-normal">বার্ষিক মোট আয়</span></label>
                            <input type="number" wire:model="formData.total_annual_income" class="w-full border border-gray-300 rounded p-2 text-sm">
                            @error('formData.total_annual_income') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Holding Constitutional Post? <br><span class="text-xs text-gray-500 font-normal">সাংবিধানিক পদে আছেন কি?</span></label>
                            <select wire:model="formData.has_constitutional_post" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes / হ্যাঁ</option>
                                <option value="No">No / না</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Constitutional Post Details <br><span class="text-xs text-gray-500 font-normal">সাংবিধানিক পদের বিবরণ</span></label>
                            <input type="text" wire:model="formData.constitutional_post_details" class="w-full border border-gray-300 rounded p-2 text-sm" placeholder="Specify member name and post name">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Registered under GST? <br><span class="text-xs text-gray-500 font-normal">জিএসটি নথিভুক্ত কি?</span></label>
                            <select wire:model="formData.has_gst_reg" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes / হ্যাঁ</option>
                                <option value="No">No / না</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">GSTIN <br><span class="text-xs text-gray-500 font-normal">জিএসটিআইএন নম্বর</span></label>
                            <input type="text" wire:model="formData.gstin" placeholder="GST Number" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Is Government Pensioner? <br><span class="text-xs text-gray-500 font-normal">সরকারি পেনশনভোগী কি?</span></label>
                            <select wire:model="formData.has_pensioner" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes / হ্যাঁ</option>
                                <option value="No">No / না</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        @endif


        {{-- STEP 3: OTHER IDENTITY & STATUS --}}
        @if ($currentStep == 3)
            <div class="space-y-8">
                {{-- E. Other Identity Documents --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                            <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" style="background-color: #1e1b4b;">A</span>
                            Other Identity Documents | অন্যান্য নথিপত্র
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">CAA Application Status <br><span class="text-xs text-gray-500 font-normal">সিএএ আবেদন স্থিতি</span></label>
                            <select wire:model="formData.hof_caa_status" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="Not Applicable">Not Applicable / প্রযোজ্য নয়</option>
                                <option value="Applied">Applied / আবেদন করেছেন</option>
                                <option value="Issued">Issued / সংশাপত্র পেয়েছেন</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">CAA Cert/App No. <br><span class="text-xs text-gray-500 font-normal">সিএএ সংশাপত্র/আবেদন নং</span></label>
                            <input type="text" wire:model="formData.hof_caa_no" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Deleted in SIR, Case Pending Tribunal? <br><span class="text-xs text-gray-500 font-normal">এসআইআর থেকে বাদ, ট্রাইব্যুনালে বিচারাধীন?</span></label>
                            <select wire:model="formData.hof_sir_tribunal_pending" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="Not Applicable">Not Applicable</option>
                                <option value="NO">NO / না</option>
                                <option value="Yes">Yes / হ্যাঁ</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Credit/Artisan Card Status <br><span class="text-xs text-gray-500 font-normal">ক্রেডিট/কারিগর কার্ড স্থিতি</span></label>
                            <select wire:model="formData.hof_kcc_status" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="KCC">KCC (Kishan Credit Card)</option>
                                <option value="KCC ARD">KCC ARD</option>
                                <option value="Artisan Credit Card">Artisan Credit Card</option>
                                <option value="MJCC">MJCC</option>
                                <option value="Student CC">Student Credit Card</option>
                                <option value="None">None / কোনোটিই নয়</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Card ID Number <br><span class="text-xs text-gray-500 font-normal">কার্ড আইডি নম্বর</span></label>
                            <input type="text" wire:model="formData.hof_kcc_no" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Issue <br><span class="text-xs text-gray-500 font-normal">ইস্যু করার তারিখ</span></label>
                            <input type="date" wire:model="formData.hof_kcc_date" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                    </div>
                </div>

                {{-- F. Health Insurance Coverage --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                            <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" style="background-color: #1e1b4b;">B</span>
                            Health Insurance details (HOF) | স্বাস্থ্য বীমা বিবরণ
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Insurance Type <br><span class="text-xs text-gray-500 font-normal">বীমার প্রকার</span></label>
                            <select wire:model="formData.health_insurance_type" class="w-full border border-gray-300 rounded p-2 text-sm">
                                <option value="">-- Select --</option>
                                <option value="Government">Government / সরকারি (যেমন স্বাস্থ্যসাথী)</option>
                                <option value="Private">Private / ব্যক্তিগত</option>
                                <option value="None">None / নেই</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Annual Premium (INR) <br><span class="text-xs text-gray-500 font-normal">বার্ষিক প্রিমিয়াম</span></label>
                            <input type="number" wire:model="formData.health_insurance_premium" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Sum Assured (INR) <br><span class="text-xs text-gray-500 font-normal">বীমাকৃত রাশি</span></label>
                            <input type="number" wire:model="formData.health_insurance_sum_assured" class="w-full border border-gray-300 rounded p-2 text-sm">
                        </div>
                    </div>
                </div>
            </div>
        @endif


        {{-- STEP 4: DECLARATION & SUBMIT --}}
        @if ($currentStep == 4)
            <div class="space-y-8">
                {{-- Social Benefits --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                            <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" style="background-color: #1e1b4b;">A</span>
                            Benefits under Government Schemes | অন্যান্য সরকারি সুবিধা
                        </h3>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        Are you receiving any DBT benefits under Government Schemes? If yes, please declare. You may also opt-out from DBT if you voluntarily wish.
                    </p>
                </div>

                {{-- H. Declaration & Consent --}}
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-5">
                    <div class="border-b-2 border-indigo-900 pb-2 mb-4">
                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                            <span class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" style="background-color: #1e1b4b;">B</span>
                            Declaration & Consent | ঘোষণা এবং সম্মতি
                        </h3>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" wire:model="formData.agree_consent" id="agree_consent" class="mt-1 h-4 w-4 text-indigo-900 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="agree_consent" class="text-xs md:text-sm text-gray-700 font-medium leading-relaxed">
                                I hereby declare that the above information is true to the best of my knowledge and I have provided all the supporting documents where applicable and HAVE NOT missed any criteria as mentioned above. I understand that my social protection benefits will be stopped if any information provided by me turns out to be false.
                                <br>
                                <span class="text-xs text-gray-500 font-normal italic">
                                    আমি ঘোষণা করছি যে আমার জ্ঞানত উপরোক্ত তথ্যগুলি সত্য এবং আমি প্রযোজ্য সমস্ত সহায়ক নথি প্রদান করেছি। আমি বুঝতে পারছি যে আমার দেওয়া কোনো তথ্য ভুল প্রমানিত হলে আমার সামাজিক সুরক্ষা সুবিধা বন্ধ করে দেওয়া হবে।
                                </span>
                            </label>
                        </div>
                        @error('formData.agree_consent') <div class="text-red-600 text-xs pl-7 font-semibold">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        @endif

        {{-- Navigation Controls --}}
        <div class="flex justify-between items-center mt-8 pt-4 border-t border-gray-200">
            <div>
                @if ($currentStep > 1)
                    <button type="button" wire:click="previousStep" class="hover:bg-gray-300 text-gray-800 font-bold px-6 py-2.5 rounded shadow transition text-sm flex items-center gap-1 uppercase tracking-wider" style="background-color: #e2e8f0;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back / পিছনে
                    </button>
                @endif
            </div>

            <div>
                @if ($currentStep < $totalSteps)
                    <button type="button" wire:click="nextStep" class="hover:bg-opacity-90 text-white font-bold px-6 py-2.5 rounded shadow transition text-sm flex items-center gap-1 uppercase tracking-wider" style="background-color: #1e1b4b;">
                        Next / এগিয়ে চলুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                @else
                    <button type="submit" class="hover:bg-opacity-90 text-white font-bold px-8 py-3 rounded-lg shadow-md hover:shadow-lg transition flex items-center gap-2 text-sm uppercase tracking-wider" style="background-color: #1e1b4b;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Submit Application / আবেদন জমা দিন
                    </button>
                @endif
            </div>
        </div>

    </div>
</form>
