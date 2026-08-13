@extends('frontend.layouts.app-template')

@section('content')
    @php
        $colorMap = [
            'blue' => ['100' => '#dbeafe', '400' => '#60a5fa', '600' => '#2563eb'],
            'pink' => ['100' => '#fce7f3', '400' => '#f472b6', '600' => '#db2777'],
            'yellow' => ['100' => '#fef9c3', '400' => '#facc15', '600' => '#ca8a04'],
            'indigo' => ['100' => '#e0e7ff', '400' => '#818cf8', '600' => '#4f46e5'],
            'purple' => ['100' => '#f3e8ff', '400' => '#c084fc', '600' => '#9333ea'],
            'red' => ['100' => '#fee2e2', '400' => '#f87171', '600' => '#dc2626'],
            'green' => ['100' => '#dcfce7', '400' => '#4ade80', '600' => '#16a34a'],
            'emerald' => ['100' => '#d1fae5', '400' => '#34d399', '600' => '#059669'],
            'orange' => ['100' => '#ffedd5', '400' => '#fb923c', '600' => '#ea580c'],
            'teal' => ['100' => '#ccfbf1', '400' => '#2dd4bf', '600' => '#0d9488'],
            'gray' => ['100' => '#f3f4f6', '400' => '#9ca3af', '600' => '#4b5563'],
            'amber' => ['100' => '#fef3c7', '400' => '#fbbf24', '600' => '#d97706'],
        ];
    @endphp

    @include('frontend.components.top-header')
    @include('frontend.components.header')

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Enhanced Header -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl shadow-xl p-6 mb-8 border border-blue-100">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div class="flex-1">
                    <div class="flex items-center mb-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-white shadow-md border border-blue-200">
                            <i class="fa-solid fa-user-circle text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">
                                Beneficiary Details
                            </h1>
                            <p class="text-gray-600 mt-1">Complete profile information</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-red-500 to-red-600 text-white shadow-sm">
                            <i class="fa-solid fa-id-card mr-2"></i>
                            ID: {{$row->id}}
                        </span>

                        @if($row->wt_special == 1)
                            <span
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-sm">
                                <i class="fa-solid fa-star mr-2"></i>
                                Special Quota
                            </span>
                        @endif

                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-green-500 to-green-600 text-white shadow-sm">
                            <i class="fa-solid fa-user mr-2"></i>
                            {{ ($row->gender == 'Other') ? "Other" : $row->gender }}
                        </span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button
                        class="hs-tooltip group inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 hover:shadow-md px-5 py-3 transition-all duration-200"
                        onclick="window.print()">
                        <div
                            class="w-8 h-8 flex items-center justify-center rounded-md bg-blue-50 group-hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-print text-blue-600"></i>
                        </div>
                        <span>Print</span>
                    </button>
                    <button
                        class="hs-tooltip group inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl px-5 py-3 transition-all duration-200">
                        <div class="w-8 h-8 flex items-center justify-center rounded-md bg-white/20">
                            <i class="fa-solid fa-download"></i>
                        </div>
                        <span>Export PDF</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Application Under Section Accordion -->
        @if($is_state_login)
            <div
                class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8 overflow-hidden transition-all duration-300 hover:shadow-xl">
                <button class="accordion-toggle w-full text-left group" id="application-accordion-heading"
                    aria-controls="application-accordion-collapse">
                    <div
                        class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200 rounded-t-2xl">
                        <div class="flex items-center">
                            <div
                                class="flex-shrink-0 w-14 h-14 flex items-center justify-center rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700 border border-blue-200 group-hover:from-blue-200 group-hover:to-blue-300 transition-all">
                                <i class="fa-solid fa-map-marker-alt text-xl"></i>
                            </div>
                            <div class="ml-5">
                                <h2 class="text-xl font-bold text-gray-900">Application Under</h2>
                                <p class="text-gray-600 mt-1">Geographical jurisdiction details</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="mr-4 text-right">
                                <div class="text-sm font-medium text-gray-500">Region</div>
                                <div class="text-lg font-bold text-blue-700">{{$district_state_name}}</div>
                            </div>
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 group-hover:bg-gray-200 transition-colors">
                                <i
                                    class="accordion-active:rotate-180 accordion-active:text-blue-600 text-gray-600 text-lg fa-solid fa-chevron-down transition-all duration-300"></i>
                            </div>
                        </div>
                    </div>
                </button>
                <div id="application-accordion-collapse" class="accordion-content hidden border-t border-gray-100"
                    aria-labelledby="application-accordion-heading">
                    <div class="p-8 bg-gradient-to-br from-gray-50 to-white">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                            <i class="fa-solid fa-landmark"></i>
                                        </div>
                                        <h3 class="ml-3 text-lg font-semibold text-gray-800">District Information</h3>
                                    </div>
                                    <p class="text-2xl font-bold text-gray-900">{{$district_state_name}}</p>
                                    <p class="text-gray-600 mt-2">Primary administrative region</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-green-100 text-green-600">
                                            <i class="fa-solid fa-layer-group"></i>
                                        </div>
                                        <h3 class="ml-3 text-lg font-semibold text-gray-800">Block/SubDivision</h3>
                                    </div>
                                    <p class="text-2xl font-bold text-gray-900">{{$block_subdiv_state_name}}</p>
                                    <p class="text-gray-600 mt-2">Secondary administrative division</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Accordions Container -->
        <div class="space-y-6">
            <!-- Personal Details Accordion -->
            <div
                class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl">
                <button class="accordion-toggle w-full text-left group" id="personal-accordion-heading"
                    aria-controls="personal-accordion-collapse">
                    <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div
                                class="flex-shrink-0 w-14 h-14 flex items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md">
                                <i class="fa-solid fa-user text-xl"></i>
                            </div>
                            <div class="ml-5">
                                <h2 class="text-xl font-bold text-gray-900">Personal Details</h2>
                                <p class="text-gray-600 mt-1">{{$row->ben_fname}} {{$row->ben_lname}}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 group-hover:bg-gray-200 transition-colors">
                                <i
                                    class="accordion-active:rotate-180 accordion-active:text-blue-600 text-gray-600 text-lg fa-solid fa-chevron-down transition-all duration-300"></i>
                            </div>
                        </div>
                    </div>
                </button>
                <div id="personal-accordion-collapse" class="accordion-content hidden border-t border-gray-100"
                    aria-labelledby="personal-accordion-heading">
                    <div class="p-8 bg-gradient-to-br from-blue-50/30 to-white">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @php
                                $personalFields = [
                                    ['icon' => 'fa-signature', 'label' => 'Full Name', 'value' => $row->ben_fname . ' ' . $row->ben_mname . ' ' . $row->ben_lname, 'color' => 'blue'],
                                    ['icon' => 'fa-venus-mars', 'label' => 'Gender', 'value' => ($row->gender == 'Other') ? "Transgender" : $row->gender, 'color' => 'pink'],
                                    ['icon' => 'fa-birthday-cake', 'label' => 'Date of Birth', 'value' => $row->dob ? date('d/m/Y', strtotime($row->dob)) : 'N/A', 'color' => 'yellow'],
                                    ['icon' => 'fa-male', 'label' => "Father's Name", 'value' => $row->father_fname . ' ' . $row->father_mname . ' ' . $row->father_lname, 'color' => 'indigo'],
                                    ['icon' => 'fa-female', 'label' => "Mother's Name", 'value' => $row->mother_fname . ' ' . $row->mother_mname . ' ' . $row->mother_lname, 'color' => 'pink'],
                                    ['icon' => 'fa-users', 'label' => 'Caste', 'value' => $row->caste, 'color' => 'purple'],
                                    ['icon' => 'fa-ring', 'label' => 'Marital Status', 'value' => $row->marital_status, 'color' => 'red'],
                                    ['icon' => 'fa-user-friends', 'label' => 'Spouse Name', 'value' => $row->spouse_fname . ' ' . $row->spouse_mname . ' ' . $row->spouse_lname, 'color' => 'green'],
                                    ['icon' => 'fa-money-bill-wave', 'label' => 'Monthly Family Income', 'value' => '₹ ' . $row->mothly_income, 'color' => 'emerald'],
                                ];
                            @endphp

                            @foreach($personalFields as $field)
                                @php
                                    $pColor = $colorMap[$field['color']] ?? $colorMap['gray'];
                                @endphp
                                <div
                                    class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-12 h-12 flex items-center justify-center rounded-lg"
                                                style="background-color: {{$pColor['100']}}; color: {{$pColor['600']}}">
                                                <i class="fa-solid {{$field['icon']}}"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">{{$field['label']}}</h3>
                                            <p class="text-lg font-semibold text-gray-900">{{$field['value']}}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Details Accordion -->
            <div
                class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl">
                <button class="accordion-toggle w-full text-left group" id="contact-accordion-heading"
                    aria-controls="contact-accordion-collapse">
                    <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div
                                class="flex-shrink-0 w-14 h-14 flex items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white shadow-md">
                                <i class="fa-solid fa-address-book text-xl"></i>
                            </div>
                            <div class="ml-5">
                                <h2 class="text-xl font-bold text-gray-900">Contact Details</h2>
                                <p class="text-gray-600 mt-1">{{$row->mobile_no}} • {{$row->email ?: 'No Email'}}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="mr-4 text-right">
                                <div class="text-sm font-medium text-gray-500">District</div>
                                <div class="text-lg font-bold text-green-700">{{$district_name}}</div>
                            </div>
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 group-hover:bg-gray-200 transition-colors">
                                <i
                                    class="accordion-active:rotate-180 accordion-active:text-blue-600 text-gray-600 text-lg fa-solid fa-chevron-down transition-all duration-300"></i>
                            </div>
                        </div>
                    </div>
                </button>
                <div id="contact-accordion-collapse" class="accordion-content hidden border-t border-gray-100"
                    aria-labelledby="contact-accordion-heading">
                    <div class="p-8 bg-gradient-to-br from-green-50/30 to-white">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @php
                                $contactFields = [
                                    ['icon' => 'fa-map', 'label' => 'State', 'value' => 'West Bengal', 'color' => 'blue'],
                                    ['icon' => 'fa-landmark', 'label' => 'Assembly Constitution', 'value' => $row->assembly_name, 'color' => 'purple'],
                                    ['icon' => 'fa-map-pin', 'label' => 'District', 'value' => $district_name, 'color' => 'green'],
                                    ['icon' => 'fa-city', 'label' => 'Block/Municipality/Corp', 'value' => $row->block_name, 'color' => 'indigo'],
                                    ['icon' => 'fa-home', 'label' => 'GP/Ward No.', 'value' => $gp_name, 'color' => 'orange'],
                                    ['icon' => 'fa-building', 'label' => 'Village/Town/City', 'value' => $row->village_town_city, 'color' => 'teal'],
                                    ['icon' => 'fa-house-user', 'label' => 'House/Premise No.', 'value' => $row->house_premise_no, 'color' => 'red'],
                                    ['icon' => 'fa-mail-bulk', 'label' => 'Post Office', 'value' => $row->post_office, 'color' => 'yellow'],
                                    ['icon' => 'fa-map-marked-alt', 'label' => 'Pin Code', 'value' => $row->pincode, 'color' => 'pink'],
                                    ['icon' => 'fa-shield-alt', 'label' => 'Police Station', 'value' => $row->police_station, 'color' => 'gray'],
                                    ['icon' => 'fa-mobile-alt', 'label' => 'Mobile Number', 'value' => $row->mobile_no, 'color' => 'blue'],
                                    ['icon' => 'fa-envelope', 'label' => 'Email ID', 'value' => $row->email ?: 'Not provided', 'color' => 'indigo'],
                                ];
                            @endphp

                            @foreach($contactFields as $field)
                                @php
                                    $cColor = $colorMap[$field['color']] ?? $colorMap['gray'];
                                @endphp
                                <div
                                    class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-12 h-12 flex items-center justify-center rounded-lg"
                                                style="background-color: {{$cColor['100']}}; color: {{$cColor['600']}}">
                                                <i class="fa-solid {{$field['icon']}}"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">{{$field['label']}}</h3>
                                            <p class="text-lg font-semibold text-gray-900 truncate">{{$field['value']}}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identification Numbers Accordion -->
            <div
                class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl">
                <button class="accordion-toggle w-full text-left group" id="identification-accordion-heading"
                    aria-controls="identification-accordion-collapse">
                    <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div
                                class="flex-shrink-0 w-14 h-14 flex items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white shadow-md">
                                <i class="fa-solid fa-fingerprint text-xl"></i>
                            </div>
                            <div class="ml-5">
                                <h2 class="text-xl font-bold text-gray-900">Identification Numbers</h2>
                                <p class="text-gray-600 mt-1">
                                    {{$row->aadhaar_no ? 'Aadhaar: ' . $row->aadhaar_no : 'No Aadhaar'}}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="mr-4">
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border border-purple-200">
                                    <i class="fa-solid fa-shield-check mr-2"></i>
                                    {{$row->aadhaar_no ? 'Verified' : 'Pending'}}
                                </span>
                            </div>
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 group-hover:bg-gray-200 transition-colors">
                                <i
                                    class="accordion-active:rotate-180 accordion-active:text-blue-600 text-gray-600 text-lg fa-solid fa-chevron-down transition-all duration-300"></i>
                            </div>
                        </div>
                    </div>
                </button>
                <div id="identification-accordion-collapse" class="accordion-content hidden border-t border-gray-100"
                    aria-labelledby="identification-accordion-heading">
                    <div class="p-8 bg-gradient-to-br from-purple-50/30 to-white">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @php
                                $idFields = [
                                    ['icon' => 'fa-credit-card', 'label' => 'Digital Ration Card No.', 'value' => $row->ration_card_no, 'color' => 'orange'],
                                    ['icon' => 'fa-id-card-alt', 'label' => 'AHL TIN', 'value' => $row->ahl_tin, 'color' => 'blue'],
                                    ['icon' => 'fa-address-card', 'label' => 'Aadhaar No.', 'value' => $row->aadhaar_no, 'color' => 'green'],
                                    ['icon' => 'fa-vote-yea', 'label' => 'EPIC/Voter ID No.', 'value' => $row->epic_voter_id, 'color' => 'red'],
                                    ['icon' => 'fa-file-invoice', 'label' => 'PAN', 'value' => $row->pan_no, 'color' => 'purple'],
                                    ['icon' => 'fa-list-ol', 'label' => 'BPL Seq No.', 'value' => $row->bpl_seq_no, 'color' => 'teal'],
                                    ['icon' => 'fa-id-badge', 'label' => 'BPL ID No.', 'value' => $row->bpl_id_no, 'color' => 'yellow'],
                                    ['icon' => 'fa-chart-line', 'label' => 'BPL Total Score', 'value' => $row->bpl_total_score, 'color' => 'indigo'],
                                ];
                            @endphp

                            @foreach($idFields as $field)
                                @php
                                    $iColor = $colorMap[$field['color']] ?? $colorMap['gray'];
                                @endphp
                                <div
                                    class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex flex-col h-full">
                                        <div class="flex items-center mb-4">
                                            <div
                                                class="w-10 h-10 flex items-center justify-center rounded-lg"
                                                style="background-color: {{$iColor['100']}}; color: {{$iColor['600']}}">
                                                <i class="fa-solid {{$field['icon']}}"></i>
                                            </div>
                                            <h3 class="ml-3 text-sm font-medium text-gray-700">{{$field['label']}}</h3>
                                        </div>
                                        <div class="mt-auto">
                                            <p class="text-xl font-bold text-gray-900 font-mono truncate">
                                                {{ $field['value'] ?: 'Not Provided' }}
                                            </p>
                                            <div
                                                class="h-1 w-12 rounded-full mt-3"
                                                style="background: linear-gradient(to right, {{$iColor['400']}}, {{$iColor['600']}})">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Details Accordion -->
            <div
                class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl">
                <button class="accordion-toggle w-full text-left group" id="bank-accordion-heading"
                    aria-controls="bank-accordion-collapse">
                    <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div
                                class="flex-shrink-0 w-14 h-14 flex items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-md">
                                <i class="fa-solid fa-university text-xl"></i>
                            </div>
                            <div class="ml-5">
                                <h2 class="text-xl font-bold text-gray-900">Bank Details</h2>
                                <p class="text-gray-600 mt-1">{{$row->bank_name ?: 'No Bank Details'}}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            @if($row->bank_ifsc)
                                <div class="mr-4">
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-amber-100 to-amber-200 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-hashtag mr-2"></i>
                                        IFSC: {{$row->bank_ifsc}}
                                    </span>
                                </div>
                            @endif
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 group-hover:bg-gray-200 transition-colors">
                                <i
                                    class="accordion-active:rotate-180 accordion-active:text-blue-600 text-gray-600 text-lg fa-solid fa-chevron-down transition-all duration-300"></i>
                            </div>
                        </div>
                    </div>
                </button>
                <div id="bank-accordion-collapse" class="accordion-content hidden border-t border-gray-100"
                    aria-labelledby="bank-accordion-heading">
                    <div class="p-8 bg-gradient-to-br from-amber-50/30 to-white">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @php
                                $bankFields = [
                                    ['icon' => 'fa-landmark', 'label' => 'Bank Name', 'value' => $row->bank_name, 'description' => 'Primary banking institution'],
                                    ['icon' => 'fa-code-branch', 'label' => 'Branch Name', 'value' => $row->branch_name, 'description' => 'Branch location'],
                                    ['icon' => 'fa-wallet', 'label' => 'Account No.', 'value' => $row->bank_code, 'description' => 'Account number'],
                                    ['icon' => 'fa-hashtag', 'label' => 'IFSC Code', 'value' => $row->bank_ifsc, 'description' => 'Bank identification code'],
                                ];
                            @endphp

                            @foreach($bankFields as $field)
                                <div
                                    class="bg-white p-6 rounded-xl shadow-md border border-amber-100 hover:shadow-lg transition-all duration-300">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-14 h-14 flex items-center justify-center rounded-xl bg-gradient-to-br from-amber-100 to-amber-200 text-amber-600 border border-amber-200">
                                                <i class="fa-solid {{$field['icon']}} text-xl"></i>
                                            </div>
                                        </div>
                                        <div class="ml-5 flex-1">
                                            <h3 class="text-lg font-bold text-gray-900 mb-1">{{$field['label']}}</h3>
                                            <p class="text-2xl font-bold text-gray-900 font-mono mb-2">
                                                {{$field['value'] ?: 'N/A'}}</p>
                                            <p class="text-sm text-gray-500">{{$field['description']}}</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fa-solid fa-check-circle text-green-500 mr-2"></i>
                                            <span>{{$field['value'] ? 'Verified and active' : 'Information pending'}}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachments Accordion -->
            @if(count($docs) > 0)
                <div
                    class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl">
                    <button class="accordion-toggle w-full text-left group" id="attachments-accordion-heading"
                        aria-controls="attachments-accordion-collapse">
                        <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 w-14 h-14 flex items-center justify-center rounded-xl bg-gradient-to-br from-gray-600 to-gray-700 text-white shadow-md">
                                    <i class="fa-solid fa-paperclip text-xl"></i>
                                </div>
                                <div class="ml-5">
                                    <h2 class="text-xl font-bold text-gray-900">Attached Documents</h2>
                                    <p class="text-gray-600 mt-1">{{ count($docs) }} documents attached</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="mr-4">
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-gray-200 to-gray-300 text-gray-800">
                                        <i class="fa-solid fa-folder-open mr-2"></i>
                                        {{ count($docs) }} files
                                    </span>
                                </div>
                                <div
                                    class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 group-hover:bg-gray-200 transition-colors">
                                    <i
                                        class="accordion-active:rotate-180 accordion-active:text-blue-600 text-gray-600 text-lg fa-solid fa-chevron-down transition-all duration-300"></i>
                                </div>
                            </div>
                        </div>
                    </button>
                    <div id="attachments-accordion-collapse" class="accordion-content hidden border-t border-gray-100"
                        aria-labelledby="attachments-accordion-heading">
                        <div class="p-8 bg-gradient-to-br from-gray-50 to-white">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($docs as $doc)
                                    @if($doc->attched_document != "")
                                        @php
                                            $image_extension = '';
                                            if ($doc->document_mime_type == 'image/jpeg') {
                                                $image_extension = 'jpg';
                                            } else if ($doc->document_mime_type == 'image/png') {
                                                $image_extension = 'png';
                                            } else if ($doc->document_mime_type == 'application/pdf') {
                                                $image_extension = 'pdf';
                                            }

                                            $bgColor = $image_extension == 'pdf' ? 'bg-red-50 border-red-100' :
                                                (in_array($image_extension, ['jpg', 'png']) ? 'bg-green-50 border-green-100' : 'bg-gray-50 border-gray-100');

                                            $textColor = $image_extension == 'pdf' ? 'text-red-600' :
                                                (in_array($image_extension, ['jpg', 'png']) ? 'text-green-600' : 'text-gray-600');
                                        @endphp

                                        <div
                                            class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                                            <div class="p-5">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 text-lg">{{$doc->doc_type_name}}</h4>
                                                        <p class="text-sm text-gray-500 mt-1">Document ID: {{$doc->id}}</p>
                                                    </div>
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{$bgColor}} {{$textColor}} border">
                                                        {{ strtoupper($image_extension) }}
                                                    </span>
                                                </div>

                                                @if(in_array($image_extension, ['jpg', 'png']))
                                                    <?php                $row_image = "data:image/" . $image_extension . ";base64," . $doc->attched_document; ?>
                                                    <div class="mb-4">
                                                        <div class="relative overflow-hidden rounded-lg border border-gray-200">
                                                            <a href="{{$row_image}}" target="_blank" class="block">
                                                                <img src="{{$row_image}}" alt="{{$doc->doc_type_name}}"
                                                                    class="w-full h-48 object-cover hover:scale-105 transition-transform duration-300">
                                                            </a>
                                                            <div class="absolute top-3 right-3">
                                                                <span class="bg-black/50 text-white text-xs px-2 py-1 rounded">
                                                                    {{$image_extension == 'jpg' ? 'JPEG' : 'PNG'}}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($image_extension == 'pdf')
                                                    <div class="mb-4">
                                                        <div
                                                            class="relative bg-gradient-to-br from-red-50 to-white border border-red-100 rounded-xl p-6 text-center">
                                                            <div
                                                                class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-red-100 rounded-full">
                                                                <i class="fa-solid fa-file-pdf text-3xl text-red-500"></i>
                                                            </div>
                                                            <p class="text-sm text-gray-600">PDF Document</p>
                                                            <div class="mt-3 text-xs text-gray-500">
                                                                <i class="fa-solid fa-file-pdf mr-1"></i>
                                                                Portable Document Format
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="flex gap-3">
                                                    <a href="{{route('jbDownload', ['scheme_id' => $doc->scheme_id, 'created_by_dist_code' => $doc->created_by_dist_code, 'beneficiary_id' => $doc->beneficiary_id, 'document_type' => $doc->document_type])}}"
                                                        class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-medium shadow-sm hover:shadow-md">
                                                        <i class="fa-solid fa-download mr-2"></i> Download
                                                    </a>
                                                    @if(in_array($image_extension, ['jpg', 'png']))
                                                        <a href="{{$row_image}}" target="_blank"
                                                            class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-medium shadow-sm hover:shadow-md">
                                                            <i class="fa-solid fa-eye mr-2"></i> Preview
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    @include('frontend.layouts.footer')

@endsection

@push('styles')
    <style>
        .accordion-toggle {
            transition: all 0.3s ease;
        }

        .accordion-content {
            transition: max-height 0.3s ease-out, opacity 0.2s ease-out;
        }

        .accordion-content:not(.hidden) {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .shadow-soft {
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.08);
        }

        .hover-lift:hover {
            transform: translateY(-4px);
            transition: transform 0.2s ease;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const accordions = document.querySelectorAll('.accordion-toggle');

            accordions.forEach(toggle => {
                const contentId = toggle.getAttribute('aria-controls');
                const content = document.getElementById(contentId);

                if (!content) return;

                toggle.addEventListener('click', () => {
                    const isOpen = !content.classList.contains('hidden');

                    // Close all other accordions
                    accordions.forEach(otherToggle => {
                        if (otherToggle !== toggle) {
                            const otherContentId = otherToggle.getAttribute('aria-controls');
                            const otherContent = document.getElementById(otherContentId);
                            if (otherContent) {
                                otherContent.classList.add('hidden');
                            }
                        }
                    });

                    // Toggle current accordion
                    if (isOpen) {
                        content.classList.add('hidden');
                    } else {
                        content.classList.remove('hidden');
                        content.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            });

            // Auto-open first accordion
            const firstAccordion = document.querySelector('.accordion-toggle');
            if (firstAccordion && !firstAccordion.closest('#application-accordion-heading')) {
                firstAccordion.click();
            }
        });
    </script>
@endpush