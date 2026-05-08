@extends('frontend.layouts.app-template')

@push('styles')
    <style>
        .sidebar {
            min-width: 290px;
        }

        /* Smooth scroll and focus styles */
        * {
            scroll-behavior: smooth;
        }

        button:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Card hover animation */
        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
        }
    </style>


    <style>
        /* Smooth animations */
        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        /* Custom focus styles */
        select:focus,
        input:focus {
            outline: none;
        }

        /* Remove default number spinner */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Smooth transitions */
        * {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Pulse animation */
        .animate-pulse {
            animation: pulse 0.3s ease-in-out;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(0.98);
            }
        }
    </style>
@endpush

@section('content')

    @include('frontend.components.top-header')
    @include('frontend.components.header')

    <div class="flex bg-gray-100 min-h-screen">

        <!-- ================= LEFT SIDEBAR ================= -->
        <div class="sidebar bg-white shadow-lg border-r border-gray-200 p-5">

            <!-- FILTERS -->
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Filters</h2>

            <div class="space-y-4">
                <!-- District -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Scheme</label>
                    <select id="scheme"
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition scheme">
                        <option value="">Select Scheme </option>
                        @foreach ($schemes as $scheme)
                            <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- District -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                    <select id="district"
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition lgd-filter">
                        <option value="">Select District</option>
                        @foreach ($districts as $dist)
                            <option value="{{ $dist->district_code }}">{{ $dist->district_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Rural/Urban -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rural / Urban</label>
                    <select id="urban_code"
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition lgd-filter">
                        <option value="">Select Rural/Urban</option>
                        <option value="2">Rural</option>
                        <option value="1">Urban</option>
                    </select>
                </div>

                <!-- Block / Subdivision -->
                <div>
                    <label id="blk_sub_txt" class="block text-sm font-medium text-gray-700 mb-1">Block / Subdivision</label>
                    <select id="block"
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition lgd-filter">
                        <option value="">--All--</option>
                    </select>
                </div>

                <!-- Municipality -->
                <div id="municipality_div" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Municipality</label>
                    <select id="muncid"
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition lgd-filter">
                        <option value="">--All--</option>
                    </select>
                </div>

                <!-- GP / Ward -->
                <div id="gp_ward_div" style="display: none;">
                    <label id="gp_ward_txt" class="block text-sm font-medium text-gray-700 mb-1">GP/Ward</label>
                    <select id="gp_ward"
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition lgd-filter">
                        <option value="">--All--</option>
                    </select>
                </div>

            </div>

        </div>

        <!-- ================= MAIN CONTENT ================= -->
        <div class="flex-1 p-8">

            <h1 class="text-2xl font-bold text-gray-800 mb-6">Track Applicant</h1>

            <!-- ================= SEARCH CONTROLS (MOVED TO TOP) ================= -->
            <div
                class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-0">
                    <!-- Search Type - With icon indicator -->
                    <div class="w-full sm:w-48 flex-shrink-0">
                        <div class="relative group">
                            <select id="searchType"
                                class="w-full pl-10 pr-8 py-3 bg-white rounded-l-lg border border-gray-300 border-r-0 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200 appearance-none cursor-pointer text-gray-700 font-medium group-hover:border-gray-400">
                                <option value="">Search By</option>
                                <option value="ben_id">Beneficiary ID</option>
                                <option value="aadhar_no">Aadhaar Number</option>
                                <option value="bank_account">Bank Account</option>
                                <option value="mobile_no">Mobile Number</option>
                                <option value="name">Full Name</option>
                            </select>
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <i id="searchTypeIcon" class="fas fa-user text-blue-500 transition-colors duration-200"></i>
                            </div>
                            <div
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                            <!-- Type label -->
                            <span class="absolute -top-2 left-2 px-1.5 text-xs text-gray-500 bg-white">Search By</span>
                        </div>
                    </div>

                    <!-- Search Value - Main input -->
                    <div class="flex-1 relative">
                        <div class="relative group">
                            <input type="text" id="searchValue"
                                class="w-full pl-12 pr-10 py-3 border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200 placeholder-gray-400 group-hover:border-gray-400"
                                placeholder="Type your search here..." autocomplete="off">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <i
                                    class="fas fa-search text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"></i>
                            </div>
                            <!-- Clear button appears when there's text -->
                            <button id="clearBtn" onclick="clearSearch()"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-300 hover:text-red-400 transition-all duration-200 opacity-0 pointer-events-none">
                                <i class="fas fa-times-circle"></i>
                            </button>
                            <!-- Search hint -->
                            <div id="searchHint" class="absolute -bottom-5 left-0 text-xs text-gray-400 mt-1 hidden">
                                Press Enter to search
                            </div>
                        </div>
                    </div>

                    <!-- Search Button - With hover effects -->
                    <div class="w-full sm:w-auto">
                        <button id="searchBtn"
                            class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-r-lg font-medium flex items-center justify-center gap-3 transition-all duration-200 shadow-sm hover:shadow-md hover:scale-[1.02] active:scale-[0.98] group">
                            <i class="fas fa-search group-hover:scale-110 transition-transform duration-200"></i>
                            <span>Search</span>
                            <i
                                class="fas fa-arrow-right text-xs opacity-70 group-hover:translate-x-1 transition-transform duration-200"></i>
                        </button>
                        <!-- Loading indicator -->
                        <div id="loadingIndicator" class="hidden text-center mt-1">
                            <div class="inline-flex items-center gap-2 text-xs text-blue-600">
                                <i class="fas fa-spinner fa-spin"></i>
                                Searching...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions (Optional minimal) -->
                <div class="mt-3 flex flex-wrap gap-1.5">
                    <span class="text-xs text-gray-500 mr-2">Quick:</span>
                    <button onclick="quickFill('ben_id', 'Beneficiary ID')"
                        class="text-xs px-2.5 py-1 bg-purple-50 hover:bg-purple-100 text-purple-600 rounded border border-purple-100 transition-colors">
                        Beneficiary ID
                    </button>
                    <button onclick="quickFill('aadhar_no', 'Aadhaar')"
                        class="text-xs px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded border border-blue-100 transition-colors">
                        Aadhaar
                    </button>
                    <button onclick="quickFill('mobile_no', 'Mobile')"
                        class="text-xs px-2.5 py-1 bg-green-50 hover:bg-green-100 text-green-600 rounded border border-green-100 transition-colors">
                        Mobile
                    </button>
                    <button onclick="quickFill('bank_account', 'Bank Account')"
                        class="text-xs px-2.5 py-1 bg-pink-50 hover:bg-pink-100 text-pink-600 rounded border border-pink-100 transition-colors">
                        Bank Account No.
                    </button>
                </div>
            </div>


            <!-- Search Results Section -->
            <div id="noResults" class="text-center text-gray-500 py-10 hidden">
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                    <p class="text-lg mb-2">No results found</p>
                    <p class="text-sm text-gray-500">Try adjusting your search criteria or filters</p>
                </div>
            </div>

            <div id="resultsContainer" class="space-y-6 mt-6"></div>

        </div>

        <!-- Add this toast container for notifications -->
        <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

        <!-- Modal for showing sensitive data -->
        <div id="sensitiveDataModal"
            class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Sensitive Information</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="sensitiveDataContent" class="space-y-3"></div>
                <div class="mt-6 flex justify-end gap-3">
                    <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg">
                        Close
                    </button>
                </div>
            </div>
        </div>

    </div>

    @include('frontend.layouts.footer')

@endsection


@push('scripts')
    <script src="{{ asset('js/master-data-v2.js') }}"></script>
    <script>
        $(function () {
            // ---------- Cached selectors ----------
            const $searchType = $('#searchType');
            const $searchValue = $('#searchValue');
            const $clearBtn = $('#clearBtn');
            const $searchHint = $('#searchHint');
            const $searchBtn = $('#searchBtn');
            const $searchTypeIcon = $('#searchTypeIcon');
            const $loadingIndicator = $('#loadingIndicator');

            const $scheme = $('#scheme');
            const $district = $('#district');
            const $urban_code = $('#urban_code');
            const $block = $('#block');
            const $muncid = $('#muncid');
            const $gp_ward = $('#gp_ward');
            const $municipality_div = $('#municipality_div');
            const $gp_ward_div = $('#gp_ward_div');

            const $resultsContainer = $('#resultsContainer');
            const $noResults = $('#noResults');
            const $toastContainer = $('#toast-container');

            // icon mapping and placeholders
            const iconMap = {
                'ben_id': 'fa-user',
                'aadhar_no': 'fa-id-card',
                'bank_account': 'fa-credit-card',
                'mobile_no': 'fa-mobile-alt',
                'name': 'fa-user-tag'
            };

            const placeholderMap = {
                'ben_id': 'Enter Beneficiary ID...',
                'aadhar_no': 'Enter 12-digit Aadhaar...',
                'bank_account': 'Enter account number...',
                'mobile_no': 'Enter 10-digit mobile...',
                'name': 'Enter full name...'
            };

            // Ensure required elements exist
            function exists($el) { return $el && $el.length; }

            // ---------- Utilities ----------
            function html(id, content) {
                $('#' + id).html(content);
            }
            function val(id) {
                return $('#' + id).val();
            }

            // ---------- Icon + placeholder update ----------
            if (exists($searchType) && exists($searchTypeIcon)) {
                $searchType.on('change', function () {
                    const v = $(this).val();
                    const iconClass = iconMap[v] || 'fa-search';
                    $searchTypeIcon.attr('class', `fas ${iconClass} text-blue-500 transition-colors duration-200`);

                    $searchValue.attr('placeholder', placeholderMap[v] || 'Type your search here...');
                });

                // trigger initial change to set icon/placeholder
                $searchType.trigger('change');
            }

            // ---------- Clear button show/hide ----------
            if (exists($searchValue) && exists($clearBtn)) {
                $searchValue.on('input', function () {
                    const has = $(this).val().trim().length > 0;
                    $clearBtn.toggleClass('opacity-0 pointer-events-none', !has);
                    $clearBtn.toggleClass('opacity-100 pointer-events-auto', !!has);
                });
            }

            // ---------- Hint show/hide ----------
            if (exists($searchValue) && exists($searchHint)) {
                $searchValue.on('focus', () => $searchHint.removeClass('hidden'));
                $searchValue.on('blur', () => $searchHint.addClass('hidden'));
            }

            // ---------- Global helper functions (exposed on window as before) ----------
            window.clearSearch = function () {
                if (!exists($searchValue)) return;
                $searchValue.val('').focus();
                $clearBtn.removeClass('opacity-100 pointer-events-auto').addClass('opacity-0 pointer-events-none');
            };

            window.quickFill = function (type, label) {
                $searchType.val(type).trigger('change');
                $searchValue.focus().attr('placeholder', `Search by ${label}...`);
            };

            // ---------- ENTER key support ----------
            if (exists($searchValue)) {
                $searchValue.on('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        triggerSearch();
                    }
                });
            }

            // ---------- District / Urban / Block / Muncid / GP handlers ----------
            // NOTE: blocks, subDistricts, ulbs, gps, ulb_wards variables must be available globally (master-data-v2.js)
            $('#district').change(function () {
                var district = $(this).val();
                //alert(district);
                $('#urban_code').val('');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
            });

            $('#urban_code').change(function () {
                var urban_code = $(this).val();
                if (urban_code == '') {
                    $('#muncid').html('<option value="">--All --</option>');
                }
                $('#muncid').html('<option value="">--All --</option>');
                $('#block').html('<option value="">--All --</option>');
                $('#gp_ward').html('<option value="">--All --</option>');
                select_district_code = $('#district').val();
                if (select_district_code == '') {
                    alert('Please Select District First');
                    $("#district").focus();
                    $("#urban_code").val('');
                } else {
                    select_body_type = urban_code;
                    var htmlOption = '<option value="">--All--</option>';
                    $("#gp_ward_div").show();
                    if (select_body_type == 2) {
                        $("#blk_sub_txt").text('Block');
                        $("#gp_ward_txt").text('GP');
                        $("#municipality_div").hide();
                        $.each(blocks, function (key, value) {
                            if (value.district_code == select_district_code) {
                                htmlOption += '<option value="' + value.id + '">' + value.text +
                                    '</option>';
                            }
                        });
                    } else if (select_body_type == 1) {
                        $("#blk_sub_txt").text('Subdivision');
                        $("#gp_ward_txt").text('Ward');
                        $("#municipality_div").show();
                        $.each(subDistricts, function (key, value) {
                            if (value.district_code == select_district_code) {
                                htmlOption += '<option value="' + value.id + '">' + value.text +
                                    '</option>';
                            }
                        });
                    } else {
                        $("#blk_sub_txt").text('Block/Subdivision');
                    }
                    $('#block').html(htmlOption);
                }

            });

            $('#block').change(function () {
                var block = $(this).val();
                var district = $("#district").val();
                var urban_code = $("#urban_code").val();
                if (district == '') {
                    $('#urban_code').val('');
                    $('#block').html('<option value="">--All --</option>');
                    $('#muncid').html('<option value="">--All --</option>');
                    alert('Please Select District First');
                    $("#district").focus();

                }
                if (urban_code == '') {
                    alert('Please Select Rural/Urban First');
                    $('#block').html('<option value="">--All --</option>');
                    $('#muncid').html('<option value="">--All --</option>');
                    $("#urban_code").focus();
                }
                if (block != '') {
                    var rural_urbanid = $('#urban_code').val();
                    if (rural_urbanid == 1) {
                        var sub_district_code = $(this).val();
                        if (sub_district_code != '') {
                            $('#muncid').html('<option value="">--All --</option>');
                            select_district_code = $('#district').val();
                            var htmlOption = '<option value="">--All--</option>';
                            $.each(ulbs, function (key, value) {
                                if ((value.district_code == select_district_code) && (value
                                    .sub_district_code == sub_district_code)) {
                                    htmlOption += '<option value="' + value.id + '">' + value.text +
                                        '</option>';
                                }
                            });
                            $('#muncid').html(htmlOption);
                        } else {
                            $('#muncid').html('<option value="">--All --</option>');
                        }
                    } else if (rural_urbanid == 2) {
                        $('#muncid').html('<option value="">--All --</option>');
                        $("#municipality_div").hide();
                        var block_code = $(this).val();
                        select_district_code = $('#district').val();

                        var htmlOption = '<option value="">--All--</option>';
                        $.each(gps, function (key, value) {
                            if ((value.district_code == select_district_code) && (value
                                .block_code == block_code)) {
                                htmlOption += '<option value="' + value.id + '">' + value.text +
                                    '</option>';
                            }
                        });
                        $('#gp_ward').html(htmlOption);
                        $("#gp_ward_div").show();


                    } else {
                        $('#muncid').html('<option value="">--All --</option>');
                        $("#municipality_div").hide();
                    }
                } else {
                    $('#muncid').html('<option value="">--All --</option>');
                    $('#gp_ward').html('<option value="">--All --</option>');
                }

            });
            $('#muncid').change(function () {
                var muncid = $(this).val();
                var district = $("#district").val();
                var urban_code = $("#urban_code").val();
                if (district == '') {
                    $('#urban_code').val('');
                    $('#block').html('<option value="">--All --</option>');
                    $('#muncid').html('<option value="">--All --</option>');
                    alert('Please Select District First');
                    $("#district").focus();

                }
                if (urban_code == '') {
                    alert('Please Select Rural/Urban First');
                    $('#block').html('<option value="">--All --</option>');
                    $('#muncid').html('<option value="">--All --</option>');
                    $("#urban_code").focus();
                }
                if (muncid != '') {
                    var rural_urbanid = $('#urban_code').val();
                    if (rural_urbanid == 1) {
                        var municipality_code = $(this).val();
                        if (municipality_code != '') {
                            $('#gp_ward').html('<option value="">--All --</option>');
                            var htmlOption = '<option value="">--All--</option>';
                            $.each(ulb_wards, function (key, value) {
                                if (value.urban_body_code == municipality_code) {
                                    htmlOption += '<option value="' + value.id + '">' + value.text +
                                        '</option>';
                                }
                            });
                            $('#gp_ward').html(htmlOption);
                        } else {
                            $('#gp_ward').html('<option value="">--All --</option>');
                        }
                    } else {
                        $('#gp_ward').html('<option value="">--All --</option>');
                        $("#gp_ward_div").hide();
                    }
                } else {
                    $('#gp_ward').html('<option value="">--All --</option>');
                }

            });





            // ---------- Search functionality ----------
            function triggerSearch() {
                if (!$searchValue.val().trim()) {
                    $searchValue.addClass('animate-shake').focus();
                    setTimeout(() => $searchValue.removeClass('animate-shake'), 500);
                    return;
                }
                $searchBtn.addClass('animate-pulse');
                setTimeout(() => $searchBtn.removeClass('animate-pulse'), 300);
                performSearch();
            }

            function performSearch() {
                const searchTypeVal = $searchType.val();
                const searchValueVal = $searchValue.val().trim();
                if (searchTypeVal) {
                    if (!searchValueVal) {
                        showToast('Please enter a search value', 'error');
                        $searchValue.focus();
                        return;
                    }
                }

                const schemeVal = $scheme.val() || '';
                const districtVal = $district.val() || '';
                const urbanVal = $urban_code.val() || '';
                const blockVal = $block.val() || '';
                const muncidVal = $muncid.val() || '';
                const gpWardVal = $gp_ward.val() || '';

                // Show loading UI
                const $btn = $searchBtn;
                const originalHtml = $btn.html();
                $loadingIndicator.removeClass('hidden');
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Searching...');

                $.ajax({
                    url: '/search',
                    method: 'GET',
                    data: {
                        search_type: searchTypeVal,
                        search_value: searchValueVal,
                        scheme: schemeVal,
                        district: districtVal,
                        urbanRural: urbanVal,
                        block: blockVal,
                        muncid: muncidVal,
                        gp_ward: gpWardVal
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (typeof window.renderResults === 'function') {
                            window.renderResults(data);
                        } else {
                            // fallback
                            renderResults(data);
                        }
                    },
                    error: function (xhr, status, err) {
                        console.error('Search error:', err);
                        showToast('Search failed. Please try again.', 'error');
                    },
                    complete: function () {
                        $loadingIndicator.addClass('hidden');
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            }

            // bind search triggers
            $searchBtn.on('click', triggerSearch);

            $searchValue.on('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    triggerSearch();
                }
            });

            // ---------- Masking helpers ----------
            window.maskAadhaar = function (aadhaar) {
                if (!aadhaar) return 'Not Available';
                const clean = String(aadhaar).replace(/\D/g, '');
                if (clean.length < 4) return 'Invalid';
                return `XXXX-XXXX-${clean.slice(-4)}`;
            };

            window.maskMobile = function (mobile) {
                if (!mobile) return 'Not Available';
                const clean = String(mobile).replace(/\D/g, '');
                if (clean.length < 4) return 'Invalid';
                return `XXXXXX${clean.slice(-4)}`;
            };

            window.maskAccountNumber = function (account) {
                if (!account) return 'Not Available';
                const clean = String(account).replace(/\D/g, '');
                if (clean.length < 4) return 'Invalid';
                return `XXXX${clean.slice(-4)}`;
            };

            // ---------- Status helpers ----------
            function getStatusColor(status) {
                const colors = {
                    'Active': 'bg-blue-500',
                    'Verified': 'bg-yellow-500',
                    'Approved': 'bg-green-500',
                    'Verified': 'bg-teal-500',
                    'Rejected': 'bg-red-500',
                    'Completed': 'bg-purple-500',
                    'Inactive': 'bg-gray-500'
                };
                return colors[status] || 'bg-gray-400';
            }

            function getStatusIcon(status) {
                const icons = {
                    'Active': 'fa-check-circle',
                    'Pending': 'fa-clock',
                    'Approved': 'fa-thumbs-up',
                    'Verified': 'fa-shield-check',
                    'Rejected': 'fa-times-circle',
                    'Completed': 'fa-flag-checkered',
                    'Inactive': 'fa-pause-circle'
                };
                return icons[status] || 'fa-info-circle';
            }

            window.getStatusColor = getStatusColor;
            window.getStatusIcon = getStatusIcon;

            window.formatDate = function (dateString) {
                if (!dateString) return '';
                try {
                    const d = new Date(dateString);
                    return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
                } catch (e) {
                    return dateString;
                }
            };

            // ---------- Copy to clipboard ----------
            window.copyToClipboard = function (text) {
                console.log('copyToClipboard called with:', text);
                if (!text || text === 'Not Available' || text === 'Invalid') {
                    showToast('Nothing to copy', 'error');
                    return;
                }

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        showToast('Copied to clipboard!', 'success');
                    }).catch(err => {
                        console.error('Copy failed:', err);
                        showToast('Failed to copy', 'error');
                    });
                    return;
                }

                // fallback
                const $textarea = $('<textarea>').val(text).css({ position: 'fixed', opacity: 0 }).appendTo('body');
                $textarea.focus().select();
                try {
                    document.execCommand('copy');
                    showToast('Copied to clipboard!', 'success');
                } catch (err) {
                    console.error('Fallback copy failed:', err);
                    showToast('Failed to copy', 'error');
                } finally {
                    $textarea.remove();
                }
            };

            // ---------- Toast (global) ----------
            window.showToast = function (message, type = 'success') {
                // remove existing
                $('.toast-notification').remove();

                const bgColor = (type === 'success') ? 'bg-green-500' : (type === 'info' ? 'bg-blue-500' : 'bg-red-500');
                const icon = (type === 'success') ? 'fa-check' : (type === 'info' ? 'fa-info-circle' : 'fa-exclamation-triangle');

                const $toast = $(`
                                                                <div class="toast-notification fixed bottom-4 right-4 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2 animate-fade-in">
                                                                    <i class="fas ${icon}"></i>
                                                                    <span>${message}</span>
                                                                </div>
                                                            `);

                if (exists($toastContainer)) {
                    $toastContainer.append($toast);
                } else {
                    $('body').append($toast);
                }

                setTimeout(() => {
                    $toast.addClass('animate-fade-out');
                    setTimeout(() => $toast.remove(), 300);
                }, 3000);
            };

            // ---------- Modal helpers ----------
            window.viewTimeline = function (id) {
                console.log('View timeline for:', id);
                showToast('Timeline view coming soon!', 'info');
            };

            window.viewDetails = function (id) {
                console.log('View details for:', id);
                window.location.href = `ben-details/${id}`;
            };

            window.showSensitiveData = function (id) {
                console.log('Show sensitive data for:', id);
                showToast('Full details view requires authentication', 'info');
            };

            window.closeModal = function () {
                $('#sensitiveDataModal').addClass('hidden');
            };

            // ---------- Render search results ----------
            window.renderResults = function (data) {
                $resultsContainer.empty();
                if (!Array.isArray(data) || data.length === 0) {
                    $noResults.removeClass('hidden');
                    return;
                }
                $noResults.addClass('hidden');

                // Header
                const $resultCount = $(`
                                                                <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                                                    <div class="flex items-center justify-between">
                                                                        <div class="flex items-center gap-2">
                                                                            <i class="fas fa-chart-bar text-blue-600"></i>
                                                                            <span class="font-medium text-blue-800">${data.length} result${data.length !== 1 ? 's' : ''} found</span>
                                                                        </div>
                                                                        <button class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1 clear-results-btn">
                                                                            <i class="fas fa-times"></i> Clear Results
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            `);
                $resultsContainer.append($resultCount);

                $resultCount.find('.clear-results-btn').on('click', function () {
                    clearResults();
                });

                // Build result cards
                data.forEach(result => {
                    const b = result._source || {};
                    const safeId = (result._id || '').replace(/'/g, "\\'");
                    const safeAadhaar = (b.aadhar_no || '').replace(/'/g, "\\'");
                    const safeMobile = (b.mobile_no || '').replace(/'/g, "\\'");
                    const safeAccount = (b.bank_code || '').replace(/'/g, "\\'");
                    const safeIFSC = (b.bank_ifsc || '').replace(/'/g, "\\'");

                    const $card = $(`
                                                                    <div class="bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-xl transition-all duration-300 overflow-hidden group mb-4">
                                                                        <div class="p-6">
                                                                            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                                                                                <div class="flex-1">
                                                                                    <div class="flex flex-wrap items-center gap-3 mb-3">
                                                                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center group-hover:scale-105 transition-transform">
                                                                                            <i class="fas fa-user text-blue-600 text-lg"></i>
                                                                                        </div>
                                                                                        <div>
                                                                                            <h2 class="text-xl font-bold text-gray-800 group-hover:text-blue-700 transition-colors">
                                                                                                ${b.full_name || 'Not Available'}
                                                                                            </h2>
                                                                                            <div class="flex items-center gap-2 mt-1">
                                                                                                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full flex items-center gap-1">
                                                                                                    <i class="fas fa-id-card text-xs"></i>
                                                                                                    ${window.maskAadhaar(b.aadhar_no)}
                                                                                                </span>
                                                                                                ${b.aadhar_no ? `<button class="copy-aadhaar text-gray-400 hover:text-blue-600 transition-colors p-1" title="Copy Aadhaar"><i class="fas fa-copy text-xs"></i></button>` : ''}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="flex items-center gap-3 text-gray-600 bg-gray-50 px-3 py-2 rounded-lg">
                                                                                        <i class="fas fa-mobile-alt text-gray-400"></i>
                                                                                        <span class="font-mono font-medium">${window.maskMobile(b.mobile_no)}</span>
                                                                                        ${b.mobile_no ? `<button class="copy-mobile ml-auto text-gray-400 hover:text-blue-600 transition-colors p-1" title="Copy Mobile"><i class="fas fa-copy text-sm"></i></button>` : ''}
                                                                                    </div>
                                                                                </div>

                                                                                <div class="flex gap-3">
                                                                                    <button class="timeline-btn inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-medium transition-all duration-200 hover:shadow-lg active:scale-95">
                                                                                        <i class="fas fa-history"></i><span>Timeline</span>
                                                                                    </button>
                                                                                    <button class="details-btn inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 hover:border-gray-400 px-4 py-2.5 rounded-lg font-medium transition-all duration-200 hover:shadow-md active:scale-95">
                                                                                        <i class="fas fa-eye"></i><span>Details</span>
                                                                                    </button>

                                                                                    <button class="pdf-btn inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-medium transition-all duration-200 hover:shadow-lg active:scale-95"
                                                                                        data-benid="${b.ben_id}">
                                                                                        <i class="fas fa-file-pdf"></i><span>PDF</span>
                                                                                    </button>
                                                                                </div>
                                                                            </div>

                                                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                                                                <div class="space-y-4">
                                                                                    <div class="flex items-center gap-3 text-gray-700">
                                                                                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                                                                            <i class="fas fa-map-marker-alt text-blue-600"></i>
                                                                                        </div>
                                                                                        <div>
                                                                                            <h3 class="font-semibold text-gray-800">Location Details</h3>
                                                                                            <p class="text-sm text-gray-500">Address information</p>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="space-y-3 pl-13">
                                                                                        <div class="grid grid-cols-2 gap-3">
                                                                                            <div class="space-y-1">
                                                                                                <label class="text-xs text-gray-500 font-medium">District</label>
                                                                                                <p class="text-gray-800 font-medium flex items-center gap-2">
                                                                                                    <i class="fas fa-building text-gray-400 text-sm"></i>
                                                                                                    ${b.district_name || 'Not Available'}
                                                                                                </p>
                                                                                            </div>
                                                                                            <div class="space-y-1">
                                                                                                <label class="text-xs text-gray-500 font-medium">Block/ULB</label>
                                                                                                <p class="text-gray-800 font-medium flex items-center gap-2">
                                                                                                    <i class="fas fa-th-large text-gray-400 text-sm"></i>
                                                                                                    ${b.block_ulb_name || 'Not Available'}
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="grid grid-cols-2 gap-3">
                                                                                            <div class="space-y-1">
                                                                                                <label class="text-xs text-gray-500 font-medium">GP/Ward</label>
                                                                                                <p class="text-gray-800 font-medium flex items-center gap-2">
                                                                                                    <i class="fas fa-map-pin text-gray-400 text-sm"></i>
                                                                                                    ${b.gp_ward_name || 'Not Available'}
                                                                                                </p>
                                                                                            </div>
                                                                                            <div class="space-y-1">
                                                                                                <label class="text-xs text-gray-500 font-medium">Village/Town</label>
                                                                                                <p class="text-gray-800 font-medium flex items-center gap-2">
                                                                                                    <i class="fas fa-home text-gray-400 text-sm"></i>
                                                                                                    ${b.village_town_city || 'Not Available'}
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="space-y-1">
                                                                                            <label class="text-xs text-gray-500 font-medium">Pincode</label>
                                                                                            <p class="text-gray-800 font-medium flex items-center gap-2">
                                                                                                <i class="fas fa-mail-bulk text-gray-400 text-sm"></i>
                                                                                                ${b.pincode || 'Not Available'}
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="space-y-4">
                                                                                    <div class="flex items-center gap-3 text-gray-700">
                                                                                        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                                                                                            <i class="fas fa-university text-green-600"></i>
                                                                                        </div>
                                                                                        <div>
                                                                                            <h3 class="font-semibold text-gray-800">Bank Information</h3>
                                                                                            <p class="text-sm text-gray-500">Account & branch details</p>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="space-y-3 pl-13">
                                                                                        <div class="space-y-1">
                                                                                            <label class="text-xs text-gray-500 font-medium">Bank Name</label>
                                                                                            <p class="text-gray-800 font-medium flex items-center gap-2">
                                                                                                <i class="fas fa-landmark text-gray-400 text-sm"></i>
                                                                                                ${b.bank_name || 'Not Available'}
                                                                                            </p>
                                                                                        </div>

                                                                                        <div class="grid grid-cols-2 gap-3">
                                                                                            <div class="space-y-1">
                                                                                                <label class="text-xs text-gray-500 font-medium">IFSC Code</label>
                                                                                                <div class="flex items-center gap-2">
                                                                                                    <p class="text-gray-800 font-medium flex items-center gap-2">
                                                                                                        <i class="fas fa-hashtag text-gray-400 text-sm"></i>
                                                                                                        ${b.bank_ifsc || 'Not Available'}
                                                                                                    </p>
                                                                                                    ${b.bank_ifsc ? `<button class="copy-ifsc text-gray-400 hover:text-green-600 transition-colors p-1" title="Copy IFSC"><i class="fas fa-copy text-xs"></i></button>` : ''}
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="space-y-1">
                                                                                                <label class="text-xs text-gray-500 font-medium">Account No.</label>
                                                                                                <div class="flex items-center gap-2">
                                                                                                    <p class="text-gray-800 font-medium font-mono flex items-center gap-2">
                                                                                                        <i class="fas fa-credit-card text-gray-400 text-sm"></i>
                                                                                                        ${window.maskAccountNumber(b.bank_code)}
                                                                                                    </p>
                                                                                                    ${b.bank_code ? `<button class="copy-account text-gray-400 hover:text-green-600 transition-colors p-1" title="Copy Account"><i class="fas fa-copy text-xs"></i></button>` : ''}
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="space-y-1">
                                                                                            <label class="text-xs text-gray-500 font-medium">Branch Name</label>
                                                                                            <p class="text-gray-800 font-medium flex items-center gap-2">
                                                                                                <i class="fas fa-code-branch text-gray-400 text-sm"></i>
                                                                                                ${b.branch_name || 'Not Available'}
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            ${b.status ? `
                                                                            <div class="mt-6 pt-5 border-t border-gray-100">
                                                                                <div class="flex flex-wrap items-center justify-between gap-3">
                                                                                    <div class="flex items-center gap-3">
                                                                                        <span class="px-3 py-1.5 ${getStatusColor(b.status)} text-white text-sm font-medium rounded-full flex items-center gap-2">
                                                                                            <i class="fas ${getStatusIcon(b.status)}"></i>
                                                                                            ${b.status}
                                                                                        </span>
                                                                                        ${b.last_updated ? `<span class="text-sm text-gray-500 flex items-center gap-1"><i class="far fa-clock"></i> Updated: ${window.formatDate(b.last_updated)}</span>` : ''}
                                                                                    </div>

                                                                                    <button class="view-full-details text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1 transition-colors active:scale-95">
                                                                                        <i class="fas fa-eye"></i> View Full Details
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                            ` : ''}
                                                                        </div>
                                                                    </div>
                                                                `);

                    // Attach dataset-bound events for this card
                    $card.find('.timeline-btn').on('click', function () { window.viewTimeline(safeId); });
                    $card.find('.details-btn').on('click', function () { window.viewDetails(safeId); });
                    $card.find('.view-full-details').on('click', function () { window.showSensitiveData(safeId); });

                    if (b.aadhar_no) $card.find('.copy-aadhaar').on('click', function () { window.copyToClipboard(safeAadhaar); });
                    if (b.mobile_no) $card.find('.copy-mobile').on('click', function () { window.copyToClipboard(safeMobile); });
                    if (b.bank_ifsc) $card.find('.copy-ifsc').on('click', function () { window.copyToClipboard(safeIFSC); });
                    if (b.bank_code) $card.find('.copy-account').on('click', function () { window.copyToClipboard(safeAccount); });

                    $resultsContainer.append($card);
                });
            };

            // ---------- Clear results ----------
            window.clearResults = function () {
                $resultsContainer.empty();
                $noResults.addClass('hidden');
            };

            // ---------- Add animation CSS if not already present ----------
            if (!$('#animation-styles').length) {
                const css = `
                                                                @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
                                                                @keyframes fadeOut { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(10px); } }
                                                                .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
                                                                .animate-fade-out { animation: fadeOut 0.3s ease-out forwards; }
                                                                .group:hover .group-hover\\:scale-105 { transform: scale(1.05); }
                                                                .fa-spinner { animation: spin 1s linear infinite; }
                                                                @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                                                                .animate-shake { animation: shake 0.4s; }
                                                                @keyframes shake { 10% { transform: translateX(-5px); } 30% { transform: translateX(5px); } 50% { transform: translateX(-5px); } 70% { transform: translateX(5px); } 90% { transform: translateX(0); } }
                                                            `;
                $('<style id="animation-styles">').text(css).appendTo('head');
            }

            // ---------- End of jQuery block ----------


            $('.lgd-filter').on('change', function () {
                var scheme = $('#scheme').val();
                if (!scheme) {
                    showToast('Please select a scheme', 'error');
                }
                performSearch();
            })

            $('#scheme').on('change', function () {
                performSearch();
            })


        });

    </script>
@endpush