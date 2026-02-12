@extends('frontend.layouts.app-template')
@push('styles')
    <style>
        .input {
            @apply p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 transition-all duration-200;
        }

        .area-btn {
            @apply border rounded-xl py-2 text-center transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer;
        }

        .area-btn.active {
            @apply bg-indigo-100 border-indigo-600 text-indigo-700 font-medium;
        }

        .beneficiary-card {
            @apply bg-gradient-to-br from-gray-900 to-black text-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1;
        }

        .status-active {
            @apply px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-300 border border-green-500/30;
        }

        .status-pending {
            @apply px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30;
        }

        .status-inactive {
            @apply px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-300 border border-red-500/30;
        }

        .filter-sidebar {
            transition: all 0.3s ease-in-out;
        }

        .main-content {
            transition: all 0.3s ease-in-out;
        }

        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4f46e5;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
@section('content')
    @include('frontend.components.top-header')
    @include('frontend.components.header')

    <div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- ================= COLLAPSIBLE FILTER SIDEBAR ================= -->
        <aside id="filterSidebar" class="filter-sidebar bg-white border-r shadow-xl transition-all duration-300"
            style="width: 320px;">

            <div class="flex items-center justify-between p-5 border-b bg-gradient-to-r from-indigo-50 to-white">
                <h2 class="font-bold text-xl text-gray-800 flex items-center">
                    <i class="fas fa-sliders-h mr-3 text-indigo-600"></i> Filters
                </h2>
                <button id="toggleSidebar" class="p-2 rounded-lg hover:bg-indigo-100 transition-colors duration-200"
                    title="Collapse sidebar">
                    <i class="fas fa-chevron-left text-indigo-600"></i>
                </button>
            </div>

            <div class="p-5 space-y-6 overflow-y-auto h-[calc(100vh-80px)]">
                <!-- Scheme -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-project-diagram mr-2 text-indigo-500"></i> Scheme
                    </label>
                    <select id="scheme" class="mt-1 w-full input">
                        <option value="">All Schemes</option>
                        @foreach($schemes as $scheme)
                            <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- District -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-indigo-500"></i> District
                    </label>
                    <select id="district" class="mt-1 w-full input">
                        <option value="">All Districts</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->district_code }}">{{ $dist->district_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Area -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-city mr-2 text-indigo-500"></i> Area Type
                    </label>
                    <div class="grid grid-cols-3 gap-2 mt-2">
                        <button onclick="setArea('')" class="area-btn active" id="areaAll">
                            <i class="fas fa-globe mr-1"></i> All
                        </button>
                        <button onclick="setArea('2')" class="area-btn" id="areaRural">
                            <i class="fas fa-tree mr-1"></i> Rural
                        </button>
                        <button onclick="setArea('1')" class="area-btn" id="areaUrban">
                            <i class="fas fa-building mr-1"></i> Urban
                        </button>
                    </div>
                    <input type="hidden" id="urban_code">
                </div>

                <!-- Block / Subdivision -->
                <div id="blk_sub_div" style="display: none;">
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



                <div class="pt-4 space-y-3">
                    <button onclick="applyFilters()"
                        class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white py-3 rounded-xl font-semibold transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>

                    <button onclick="resetFilters()"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-medium transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i> Reset Filters
                    </button>
                </div>

                <!-- Results Info -->
                <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="flex items-center text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm font-medium">Showing <span
                                id="resultCount">{{ $results }}</span>beneficiaries</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ================= MAIN CONTENT ================= -->
        <main class="main-content flex-1 p-6 transition-all duration-300" style="margin-left: 0;">

            <!-- HEADER WITH SEARCH AND COLLAPSE BUTTON -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Beneficiary Search</h1>
                    <p class="text-gray-600 mt-1">Search Beneficiary records</p>
                </div>

                <div class="flex gap-3">
                    <!-- Sidebar toggle button (visible when sidebar is collapsed) -->
                    <button id="showSidebar" class="hidden bg-indigo-600 text-white px-4 py-2 rounded-xl flex items-center">
                        <i class="fas fa-filter mr-2"></i> Show Filters
                    </button>

                    <!-- Mobile filter toggle -->
                    <button id="mobileToggleSidebar"
                        class="md:hidden bg-indigo-600 text-white px-4 py-2 rounded-xl flex items-center">
                        <i class="fas fa-filter mr-2"></i> Filters
                    </button>
                </div>
            </div>

            <!-- SEARCH BAR -->
            <div class="max-w-4xl mx-auto mb-10">
                <div class="relative">
                    <i class="fas fa-search absolute left-5 top-4 text-gray-400 text-lg"></i>
                    <input id="searchText" type="text"
                        placeholder="Search by Beneficiary ID, Name, Mobile Number or Address"
                        class="w-full pl-14 pr-36 py-4 rounded-2xl border border-gray-300 focus:ring-3 focus:ring-indigo-300 focus:border-indigo-500 shadow-sm transition-all duration-300">

                    <button onclick="searchBeneficiary()"
                        class="absolute right-2 top-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-md hover:shadow-lg">
                        <i class="fas fa-search mr-2"></i> Search
                    </button>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="hidden flex justify-center items-center py-12">
                <div class="loader"></div>
                <span class="ml-3 text-gray-600">Loading beneficiaries...</span>
            </div>

            <!-- ================= BENEFICIARY CARDS GRID ================= -->
            <div id="resultArea" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
                <!-- Dynamic cards will be loaded here -->
            </div>

            <div class="text-center mt-10">
                <button id="viewMoreBtn" onclick="loadMore()"
                    class="hidden bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-semibold">
                    View More
                </button>
            </div>




        </main>
    </div>

    @include('frontend.layouts.footer')
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Global variables for state
        let offset = 0;
        let limit = 100;
        let totalRecords = 0;
        let searchTimer;
        let sidebarCollapsed = false;

        $(document).ready(function () {
            // Initial Load
            loadBeneficiaries(true);

            // Search with Debounce
            $('#searchText').on('keyup', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    resetAndLoad();
                }, 500);
            });

            // Filter Change Events
            $('#scheme, #district').on('change', resetAndLoad);

            // Initialize area selection
            setArea('');

            // Sidebar Toggle Events
            $('#toggleSidebar').click(function () {
                toggleSidebar();
            });

            $('#showSidebar').click(function () {
                expandSidebar();
            });

            $('#mobileToggleSidebar').click(function () {
                toggleMobileSidebar();
            });

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
                    $("#blk_sub_div").show();
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




        });

        // ================= DATA LOADING =================

        function resetAndLoad() {
            offset = 0;
            $('#resultArea').html('');
            loadBeneficiaries(true);
        }

        function loadBeneficiaries(reset = false) {
            const spinner = $('#loadingSpinner');
            spinner.removeClass('hidden');

            // Gather Data
            const data = {
                offset: offset,
                search: $('#searchText').val(),
                scheme: $('#scheme').val(),
                district: $('#district').val(),
                urban_code: $('#urban_code').val(),
                block: $('#block').val(),
                muncid: $('#muncid').val(),
                gp_ward: $('#gp_ward').val(),
                status: $('#statusFilter').val()
            };

            $.ajax({
                url: "{{ route('beneficiaries.search') }}",
                data: data,
                success: function (res) {
                    totalRecords = res.total;
                    offset = res.loaded;

                    if (reset) {
                        $('#resultArea').html(res.html);
                    } else {
                        $('#resultArea').append(res.html);
                    }

                    $('#resultCount').text(totalRecords);
                    spinner.addClass('hidden');

                    // Handle View More Button
                    if (offset < totalRecords) {
                        $('#viewMoreBtn').removeClass('hidden');
                    } else {
                        $('#viewMoreBtn').addClass('hidden');
                    }

                    if (totalRecords === 0) {
                        // Optional: Show no results message
                        $('#resultArea').html('<div class="col-span-full text-center text-gray-500 py-8">No beneficiaries found.</div>');
                    }
                },
                error: function (xhr) {
                    console.error('Error:', xhr);
                    spinner.addClass('hidden');
                    showNotification('Error loading beneficiaries', 'error');
                }
            });
        }

        // ================= GLOBAL ACTIONS =================

        function loadMore() {
            loadBeneficiaries(false);
        }

        function searchBeneficiary() {
            resetAndLoad();
        }

        function applyFilters() {
            resetAndLoad();
            // Mobile handling
            if (window.innerWidth < 768) {
                const sidebar = $('#filterSidebar');
                if (sidebar.css('position') === 'fixed') {
                    toggleMobileSidebar();
                }
            }
        }

        function resetFilters() {
            $('#scheme').val('');
            $('#district').val('');
            $('#urban_code').val('');
            $('#statusFilter').val('');
            $('#searchText').val('');
            setArea('');
            resetAndLoad();
        }

        function setArea(type) {
            $('#urban_code').val(type).trigger('change');

            $('.area-btn').removeClass('active');
            if (type === '') $('#areaAll').addClass('active');
            if (type === '2') $('#areaRural').addClass('active');
            if (type === '1') $('#areaUrban').addClass('active');

            // Note: We don't auto-reload here to match original 'Apply' logic,
            // but user can click Apply. 
        }

        // ================= UI HELPERS =================

        function toggleSidebar() {
            const sidebar = $('#filterSidebar');
            const toggleBtn = $('#toggleSidebar');
            const showSidebarBtn = $('#showSidebar');

            sidebarCollapsed = !sidebarCollapsed;

            if (sidebarCollapsed) {
                sidebar.css({
                    'width': '0',
                    'overflow': 'hidden',
                    'opacity': '0'
                });
                toggleBtn.find('i').removeClass('fa-chevron-left').addClass('fa-chevron-right');
                toggleBtn.attr('title', 'Expand sidebar');
                showSidebarBtn.removeClass('hidden');
            } else {
                sidebar.css({
                    'width': '320px',
                    'opacity': '1'
                });
                toggleBtn.find('i').removeClass('fa-chevron-right').addClass('fa-chevron-left');
                toggleBtn.attr('title', 'Collapse sidebar');
                showSidebarBtn.addClass('hidden');
            }
        }

        function expandSidebar() {
            const sidebar = $('#filterSidebar');
            const toggleBtn = $('#toggleSidebar');
            const showSidebarBtn = $('#showSidebar');

            sidebar.css({
                'width': '320px',
                'opacity': '1'
            });
            toggleBtn.find('i').removeClass('fa-chevron-right').addClass('fa-chevron-left');
            toggleBtn.attr('title', 'Collapse sidebar');
            showSidebarBtn.addClass('hidden');
            sidebarCollapsed = false;
        }

        function toggleMobileSidebar() {
            const sidebar = $('#filterSidebar');

            if (sidebar.hasClass('hidden') || sidebar.width() === 0) {
                sidebar.removeClass('hidden');
                sidebar.css({
                    'width': '100%',
                    'opacity': '1',
                    'z-index': '50',
                    'position': 'fixed',
                    'height': '100vh',
                    'top': '0',
                    'left': '0'
                });
            } else {
                sidebar.css('opacity', '0');
                setTimeout(() => {
                    sidebar.addClass('hidden');
                    sidebar.removeAttr('style');
                }, 300);
            }
        }

        function showNotification(message, type = 'info') {
            $('.custom-notification').remove();

            let bgClass = 'bg-indigo-600';
            if (type === 'warning') bgClass = 'bg-yellow-500';
            if (type === 'error') bgClass = 'bg-red-500';

            const notification = $(`
                                                                        <div class="custom-notification fixed top-6 right-6 px-6 py-4 rounded-xl shadow-lg z-50 transform transition-all duration-300 ${bgClass} text-white">
                                                                            ${message}
                                                                            <button class="absolute top-2 right-3 text-white hover:text-gray-200">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    `);

            notification.find('button').click(function () {
                notification.remove();
            });

            $('body').append(notification);

            setTimeout(function () {
                if (notification.length) {
                    notification.css({
                        'opacity': '0',
                        'transform': 'translateX(100px)'
                    });
                    setTimeout(() => notification.remove(), 300);
                }
            }, 4000);
        }

        function viewDetails(beneficiaryId) {
            showNotification(`Opening details for beneficiary ${beneficiaryId}`);
            console.log('View details for:', beneficiaryId);
        }

        function viewPayments(beneficiaryId) {
            showNotification(`Opening payment history for beneficiary ${beneficiaryId}`, 'info');
            console.log('View payments for:', beneficiaryId);
        }


    </script>



@endpush