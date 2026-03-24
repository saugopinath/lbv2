@extends('frontend.layouts.app-template')
@push('styles')
    <style>
        .input {
            @apply p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 transition-all duration-200 w-full;
        }

        .area-btn {
            @apply border rounded-xl py-2.5 px-3 text-center text-sm transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer;
        }

        .area-btn.active {
            @apply bg-indigo-600 border-indigo-600 text-white font-medium shadow-md;
        }

        .beneficiary-card {
            @apply bg-white rounded-2xl p-6 shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100;
        }

        .status-active {
            @apply px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200;
        }

        .status-pending {
            @apply px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200;
        }

        .status-inactive {
            @apply px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200;
        }

        .filter-sidebar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .loader {
            border: 3px solid #e5e7eb;
            border-top: 3px solid #4f46e5;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Custom scrollbar */
        .filter-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .filter-sidebar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .filter-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .filter-sidebar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Mobile sidebar overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            backdrop-filter: blur(2px);
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* Improved input focus states */
        .input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        /* Better button states */
        .btn-primary {
            @apply bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold transition-all duration-300 shadow-md hover:shadow-lg active:scale-95;
        }

        .btn-secondary {
            @apply bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-300 transition-all duration-300 active:scale-95;
        }

        /* Empty state */
        .empty-state {
            @apply flex flex-col items-center justify-center py-16 text-center;
        }

        /* Search highlight animation */
        @keyframes highlight {

            0%,
            100% {
                background-color: transparent;
            }

            50% {
                background-color: rgba(79, 70, 229, 0.1);
            }
        }

        .search-highlight {
            animation: highlight 1s ease-in-out;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .filter-sidebar.mobile-open {
                position: fixed;
                top: 0;
                left: 0;
                width: 85% !important;
                max-width: 320px;
                height: 100vh;
                z-index: 50;
                opacity: 1 !important;
            }
        }

        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s ease-in-out infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Filter highlight styling */
        .filter-field-highlight {
            border-color: #4f46e5 !important;
            background-color: #f5f3ff !important;
            box-shadow: 0 0 0 1px #4f46e5 !important;
            font-weight: 500 !important;
        }

        .filter-label-highlight {
            color: #4f46e5 !important;
            transform: scale(1.02);
            transition: all 0.2s ease;
        }
    </style>
@endpush

@section('content')
    @include('frontend.components.top-header')
    @include('frontend.components.header')

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay" onclick="closeMobileSidebar()"></div>

    <div class="flex min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
        <!-- ================= COLLAPSIBLE FILTER SIDEBAR ================= -->
        <aside id="filterSidebar"
            class="filter-sidebar bg-white border-r border-gray-200 shadow-lg transition-all duration-300"
            style="width: 320px;">

            <div
                class="flex items-center justify-between p-5 border-b bg-gradient-to-r from-indigo-50 via-indigo-25 to-white sticky top-0 z-10">
                <h2 class="font-bold text-xl text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Filters
                </h2>
                <button id="toggleSidebar" class="p-2 rounded-lg hover:bg-indigo-100 transition-colors duration-200"
                    title="Collapse sidebar">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <div class="p-5 space-y-5 overflow-y-auto h-[calc(100vh-80px)]">
                <!-- Scheme -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Scheme
                    </label>
                    <select id="scheme" class="mt-1 input">
                        <option value="">All Schemes</option>
                        @foreach($schemes as $scheme)
                            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- District -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        District
                    </label>
                    <select id="district" class="mt-1 input">
                        <option value="">All Districts</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}">{{ $dist->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Area Type -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Area Type
                    </label>
                    <div class="grid grid-cols-3 gap-2 mt-2">
                        <button type="button" onclick="setArea('')" class="area-btn active" id="areaAll">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            All
                        </button>
                        <button type="button" onclick="setArea('2')" class="area-btn" id="areaRural">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Rural
                        </button>
                        <button type="button" onclick="setArea('1')" class="area-btn" id="areaUrban">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Urban
                        </button>
                    </div>
                    <input type="hidden" id="urban_code">
                </div>

                <!-- Block / Subdivision -->
                <div id="blk_sub_div" style="display: none;">
                    <label id="blk_sub_txt" class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        Block / Subdivision
                    </label>
                    <select id="block" class="mt-1 input">
                        <option value="">All</option>
                    </select>
                </div>

                <!-- Municipality -->
                <div id="municipality_div" style="display: none;">
                    <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Municipality
                    </label>
                    <select id="muncid" class="mt-1 input">
                        <option value="">All</option>
                    </select>
                </div>

                <!-- GP / Ward -->
                <div id="gp_ward_div" style="display: none;">
                    <label id="gp_ward_txt" class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        GP / Ward
                    </label>
                    <select id="gp_ward" class="mt-1 input">
                        <option value="">All</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 space-y-3">
                    <button type="button" onclick="applyFilters()"
                        class="w-full btn-primary py-3 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Apply Filters
                    </button>

                    <button type="button" onclick="resetFilters()"
                        class="w-full btn-secondary py-3 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset Filters
                    </button>
                </div>

                <!-- Results Info -->
                <div class="mt-6 p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                    <div class="flex items-center text-blue-700">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium">
                            Showing <span id="resultCount" class="font-bold">{{ $results }}</span> beneficiaries
                        </span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ================= MAIN CONTENT ================= -->
        <main class="main-content flex-1 p-4 md:p-6 transition-all duration-300" style="margin-left: 0;">

            <!-- HEADER WITH SEARCH -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-1">Beneficiary Search</h1>
                    <p class="text-gray-600 text-sm md:text-base">Search and filter beneficiary records</p>
                </div>

                <div class="flex gap-3">
                    <!-- Sidebar toggle button (visible when sidebar is collapsed) -->
                    <button type="button" id="showSidebar"
                        class="hidden btn-primary px-4 py-2 rounded-xl flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filters
                    </button>

                    <!-- Mobile filter toggle -->
                    <button type="button" id="mobileToggleSidebar"
                        class="md:hidden btn-primary px-4 py-2 rounded-xl flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Filters
                    </button>
                </div>
            </div>

            <!-- SEARCH BAR -->
            <div class="max-w-4xl mx-auto mb-8 md:mb-10">
                <div class="relative">
                    <svg class="w-5 h-5 absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input id="searchText" type="text"
                        placeholder="Search by Beneficiary ID, Name, Mobile Number or Address"
                        class="w-full pl-14 pr-4 md:pr-36 py-3.5 md:py-4 rounded-2xl border border-gray-300 focus:ring-3 focus:ring-indigo-300 focus:border-indigo-500 shadow-sm transition-all duration-300 text-sm md:text-base">

                    <button type="button" onclick="searchBeneficiary()"
                        class="hidden md:flex absolute right-2 top-2 btn-primary px-6 md:px-8 py-2.5 md:py-3 rounded-xl items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </button>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="hidden flex justify-center items-center py-12">
                <div class="loader"></div>
                <span class="ml-3 text-gray-600 font-medium">Loading beneficiaries...</span>
            </div>

            <!-- ================= BENEFICIARY CARDS GRID ================= -->
            <div id="resultArea" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 max-w-7xl mx-auto">
                <!-- Dynamic cards will be loaded here -->
            </div>

            <!-- View More Button -->
            <div class="text-center mt-8 md:mt-10">
                <button type="button" id="viewMoreBtn" onclick="loadMore()"
                    class="hidden btn-primary px-8 py-3 rounded-xl font-semibold inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    Load More
                </button>
            </div>
        </main>
    </div>

    @include('frontend.layouts.footer')
@endsection

@push('scripts')
    <script>
        // Data injected from Controller
        const blocks = @json($blocks ?? []);
        const subDistricts = @json($subDistricts ?? []);
        const ulbs = @json($ulbs ?? []);
        const gps = @json($gps ?? []);
        const ulb_wards = @json($ulb_wards ?? []);

        // Global variables for state
        let offset = 0;
        let limit = 100;
        let totalRecords = 0;
        let searchTimer;
        let sidebarCollapsed = false;

        // Helper Functions
        const $ = (selector) => document.querySelector(selector);
        const $$ = (selector) => document.querySelectorAll(selector);

        // DOM Ready
        document.addEventListener('DOMContentLoaded', function () {
            // Initial Load
            loadBeneficiaries(true);

            // Search with Debounce
            const searchInput = $('#searchText');
            if (searchInput) {
                searchInput.addEventListener('keyup', function (e) {
                    clearTimeout(searchTimer);
                    // Search on Enter key
                    if (e.keyCode === 13) {
                        searchBeneficiary();
                        return;
                    }
                    searchTimer = setTimeout(function () {
                        resetAndLoad();
                    }, 800);
                });
            }

            // Initialize area selection
            setArea('');

            // Sidebar Toggle Events
            const toggleSidebarBtn = $('#toggleSidebar');
            const showSidebarBtn = $('#showSidebar');
            const mobileToggleBtn = $('#mobileToggleSidebar');

            if (toggleSidebarBtn) {
                toggleSidebarBtn.addEventListener('click', toggleSidebar);
            }
            if (showSidebarBtn) {
                showSidebarBtn.addEventListener('click', expandSidebar);
            }
            if (mobileToggleBtn) {
                mobileToggleBtn.addEventListener('click', toggleMobileSidebar);
            }

            // Close mobile sidebar on window resize
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) {
                    closeMobileSidebar();
                }
            });

            // ---------- District / Urban / Block / Muncid / GP handlers ----------
            const districtSelect = $('#district');
            if (districtSelect) {
                districtSelect.addEventListener('change', function () {
                    const urbanCode = $('#urban_code');
                    const blockSelect = $('#block');
                    const muncidSelect = $('#muncid');
                    const gpWardSelect = $('#gp_ward');

                    if (urbanCode) urbanCode.value = '';
                    if (blockSelect) blockSelect.innerHTML = '<option value="">All</option>';
                    if (muncidSelect) muncidSelect.innerHTML = '<option value="">All</option>';
                    if (gpWardSelect) gpWardSelect.innerHTML = '<option value="">All</option>';

                    updateSidebarHighlights();

                    hide('#blk_sub_div');
                    hide('#municipality_div');
                    hide('#gp_ward_div');
                });
            }

            const urbanCodeInput = $('#urban_code');
            if (urbanCodeInput) {
                urbanCodeInput.addEventListener('change', function () {
                    const urban_code = this.value;
                    const select_district_code = districtSelect ? districtSelect.value : '';

                    const muncidSelect = $('#muncid');
                    const blockSelect = $('#block');
                    const gpWardSelect = $('#gp_ward');

                    if (muncidSelect) muncidSelect.innerHTML = '<option value="">All</option>';
                    if (blockSelect) blockSelect.innerHTML = '<option value="">All</option>';
                    if (gpWardSelect) gpWardSelect.innerHTML = '<option value="">All</option>';

                    if (urban_code === '') {
                        hide('#blk_sub_div');
                        hide('#municipality_div');
                        hide('#gp_ward_div');
                        return;
                    }

                    if (!select_district_code) {
                        showNotification('Please select district first', 'warning');
                        if (districtSelect) districtSelect.focus();
                        this.value = '';
                        setArea('');
                        return;
                    }

                    show('#blk_sub_div');
                    let htmlOption = '<option value="">All</option>';

                    if (urban_code == '2') {
                        // Rural
                        setText('#blk_sub_txt', 'Block');
                        setText('#gp_ward_txt', 'GP');
                        hide('#municipality_div');
                        show('#gp_ward_div');

                        if (typeof blocks !== 'undefined') {
                            blocks.forEach(function (value) {
                                if (value.district_id == select_district_code) {
                                    htmlOption += '<option value="' + value.id + '">' + value.name + '</option>';
                                }
                            });
                        }
                    } else if (urban_code == '1') {
                        // Urban
                        setText('#blk_sub_txt', 'Subdivision');
                        setText('#gp_ward_txt', 'Ward');
                        show('#municipality_div');
                        show('#gp_ward_div');

                        if (typeof subDistricts !== 'undefined') {
                            subDistricts.forEach(function (value) {
                                if (value.district_id == select_district_code) {
                                    htmlOption += '<option value="' + value.id + '">' + value.name + '</option>';
                                }
                            });
                        }
                    }
                    if (blockSelect) blockSelect.innerHTML = htmlOption;
                    updateSidebarHighlights();
                });
            }

            const blockSelect = $('#block');
            if (blockSelect) {
                blockSelect.addEventListener('change', function () {
                    const block = this.value;
                    const district = districtSelect ? districtSelect.value : '';
                    const urban_code = urbanCodeInput ? urbanCodeInput.value : '';

                    const gpWardSelect = $('#gp_ward');
                    const muncidSelect = $('#muncid');

                    if (gpWardSelect) gpWardSelect.innerHTML = '<option value="">All</option>';
                    if (muncidSelect) muncidSelect.innerHTML = '<option value="">All</option>';

                    if (!district) {
                        showNotification('Please select district first', 'warning');
                        if (districtSelect) districtSelect.focus();
                        return;
                    }

                    if (!urban_code) {
                        showNotification('Please select area type first', 'warning');
                        return;
                    }

                    if (!block) {
                        return;
                    }

                    let htmlOption = '<option value="">All</option>';

                    if (urban_code == '1') {
                        // Urban - load municipalities
                        if (typeof ulbs !== 'undefined') {
                            ulbs.forEach(function (value) {
                                if (value.subdivision_id == block) {
                                    htmlOption += '<option value="' + value.id + '">' + value.name + '</option>';
                                }
                            });
                        }
                        if (muncidSelect) muncidSelect.innerHTML = htmlOption;
                    } else if (urban_code == '2') {
                        // Rural - load GPs
                        hide('#municipality_div');
                        if (typeof gps !== 'undefined') {
                            gps.forEach(function (value) {
                                if (value.block_id == block) {
                                    htmlOption += '<option value="' + value.id + '">' + value.name + '</option>';
                                }
                            });
                        }
                        if (gpWardSelect) gpWardSelect.innerHTML = htmlOption;
                    }
                });
            }

            const muncidSelect = $('#muncid');
            if (muncidSelect) {
                muncidSelect.addEventListener('change', function () {
                    const muncid = this.value;
                    const district = districtSelect ? districtSelect.value : '';
                    const urban_code = urbanCodeInput ? urbanCodeInput.value : '';

                    const gpWardSelect = $('#gp_ward');
                    if (gpWardSelect) gpWardSelect.innerHTML = '<option value="">All</option>';

                    if (!district) {
                        showNotification('Please select district first', 'warning');
                        if (districtSelect) districtSelect.focus();
                        return;
                    }

                    if (!urban_code) {
                        showNotification('Please select area type first', 'warning');
                        return;
                    }

                    if (!muncid) {
                        return;
                    }

                    if (urban_code == '1') {
                        // Load wards for municipality
                        let htmlOption = '<option value="">All</option>';
                        if (typeof ulb_wards !== 'undefined') {
                            ulb_wards.forEach(function (value) {
                                if (value.municipality_id == muncid) {
                                    htmlOption += '<option value="' + value.id + '">' + value.name + '</option>';
                                }
                            });
                        }
                        if (gpWardSelect) gpWardSelect.innerHTML = htmlOption;
                    }
                    updateSidebarHighlights();
                });
            }

            // Add change listeners to all sidebar selects for automatic highlighting
            $$('.filter-sidebar select').forEach(select => {
                select.addEventListener('change', updateSidebarHighlights);
            });

            // Initialization of highlights
            updateSidebarHighlights();
        });

        // Function to update highlights on sidebar filters
        function updateSidebarHighlights() {
            const selects = ['#scheme', '#district', '#block', '#muncid', '#gp_ward',];
            selects.forEach(id => {
                const el = $(id);
                if (!el) return;

                // Look for the label inside the parent div
                const parent = el.closest('div');
                const label = parent ? parent.querySelector('label') : null;

                if (el.value !== '') {
                    el.classList.add('filter-field-highlight');
                    if (label) label.classList.add('filter-label-highlight');
                } else {
                    el.classList.remove('filter-field-highlight');
                    if (label) label.classList.remove('filter-label-highlight');
                }
            });
        }

        // Utility Functions
        function show(selector) {
            const element = $(selector);
            if (element) element.style.display = 'block';
        }

        function hide(selector) {
            const element = $(selector);
            if (element) element.style.display = 'none';
        }

        function setText(selector, text) {
            const element = $(selector);
            if (element) element.textContent = text;
        }

        function addClass(selector, className) {
            const element = $(selector);
            if (element) element.classList.add(className);
        }

        function removeClass(selector, className) {
            const element = $(selector);
            if (element) element.classList.remove(className);
        }

        function hasClass(selector, className) {
            const element = $(selector);
            return element ? element.classList.contains(className) : false;
        }

        // ================= DATA LOADING =================

        function resetAndLoad() {
            offset = 0;
            const resultArea = $('#resultArea');
            if (resultArea) resultArea.innerHTML = '';
            loadBeneficiaries(true);
        }

        function loadBeneficiaries(reset = false) {
            const spinner = $('#loadingSpinner');
            const resultArea = $('#resultArea');

            if (spinner) removeClass('#loadingSpinner', 'hidden');

            // Gather Data
            const data = {
                offset: offset,
                search: $('#searchText')?.value || '',
                scheme: $('#scheme')?.value || '',
                district: $('#district')?.value || '',
                urban_code: $('#urban_code')?.value || '',
                block: $('#block')?.value || '',
                muncid: $('#muncid')?.value || '',
                gp_ward: $('#gp_ward')?.value || '',
                status: $('#statusFilter')?.value || ''
            };

            // Convert data to URL params
            const params = new URLSearchParams(data).toString();

            fetch("{{ route('beneficiaries.search') }}?" + params, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(res => {
                    totalRecords = res.total;
                    offset = res.loaded;

                    if (reset) {
                        if (resultArea) resultArea.innerHTML = res.html;
                    } else {
                        if (resultArea) resultArea.innerHTML += res.html;
                    }

                    setText('#resultCount', totalRecords);
                    if (spinner) addClass('#loadingSpinner', 'hidden');

                    // Handle View More Button
                    const viewMoreBtn = $('#viewMoreBtn');
                    if (viewMoreBtn) {
                        if (offset < totalRecords) {
                            removeClass('#viewMoreBtn', 'hidden');
                        } else {
                            addClass('#viewMoreBtn', 'hidden');
                        }
                    }

                    if (totalRecords === 0 && resultArea) {
                        resultArea.innerHTML = `
                                                                                                                                                                    <div class="col-span-full empty-state">
                                                                                                                                                                        <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                                                                                                                        </svg>
                                                                                                                                                                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No beneficiaries found</h3>
                                                                                                                                                                        <p class="text-gray-500 mb-4">Try adjusting your search or filters</p>
                                                                                                                                                                        <button onclick="resetFilters()" class="btn-primary px-6 py-2 rounded-lg">
                                                                                                                                                                            Reset Filters
                                                                                                                                                                        </button>
                                                                                                                                                                    </div>
                                                                                                                                                                `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (spinner) addClass('#loadingSpinner', 'hidden');
                    showNotification('Error loading beneficiaries. Please try again.', 'error');

                    if (resultArea) {
                        resultArea.innerHTML = `
                                                                                                                                                                    <div class="col-span-full empty-state">
                                                                                                                                                                        <svg class="w-20 h-20 text-red-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                                                                                                                        </svg>
                                                                                                                                                                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Something went wrong</h3>
                                                                                                                                                                        <p class="text-gray-500 mb-4">Unable to load beneficiaries</p>
                                                                                                                                                                        <button onclick="resetAndLoad()" class="btn-primary px-6 py-2 rounded-lg">
                                                                                                                                                                            Try Again
                                                                                                                                                                        </button>
                                                                                                                                                                    </div>
                                                                                                                                                                `;
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
            // Close mobile sidebar after applying
            closeMobileSidebar();
        }

        function resetFilters() {
            const schemeSelect = $('#scheme');
            const districtSelect = $('#district');
            const urbanCodeInput = $('#urban_code');
            const blockSelect = $('#block');
            const muncidSelect = $('#muncid');
            const gpWardSelect = $('#gp_ward');
            const statusFilter = $('#statusFilter');
            const searchText = $('#searchText');

            if (schemeSelect) schemeSelect.value = '';
            if (districtSelect) districtSelect.value = '';
            if (urbanCodeInput) urbanCodeInput.value = '';
            if (blockSelect) blockSelect.innerHTML = '<option value="">All</option>';
            if (muncidSelect) muncidSelect.innerHTML = '<option value="">All</option>';
            if (gpWardSelect) gpWardSelect.innerHTML = '<option value="">All</option>';
            if (statusFilter) statusFilter.value = '';
            if (searchText) searchText.value = '';

            updateSidebarHighlights();

            // Hide conditional divs
            hide('#blk_sub_div');
            hide('#municipality_div');
            hide('#gp_ward_div');

            setArea('');
            resetAndLoad();
        }

        function setArea(type) {
            const urbanCodeInput = $('#urban_code');
            if (urbanCodeInput) {
                urbanCodeInput.value = type;
                urbanCodeInput.dispatchEvent(new Event('change'));
            }

            // Remove active class from all buttons
            $$('.area-btn').forEach(btn => {
                btn.classList.remove('active', 'filter-field-highlight');
            });

            // Add active class to selected button
            if (type === '') { addClass('#areaAll', 'active'); addClass('#areaAll', 'filter-field-highlight'); }
            if (type === '2') { addClass('#areaRural', 'active'); addClass('#areaRural', 'filter-field-highlight'); }
            if (type === '1') { addClass('#areaUrban', 'active'); addClass('#areaUrban', 'filter-field-highlight'); }
        }

        // ================= UI HELPERS =================

        function toggleSidebar() {
            const sidebar = $('#filterSidebar');
            const toggleBtn = $('#toggleSidebar');
            const showSidebarBtn = $('#showSidebar');

            if (!sidebar || !toggleBtn || !showSidebarBtn) return;

            sidebarCollapsed = !sidebarCollapsed;

            if (sidebarCollapsed) {
                sidebar.style.width = '0';
                sidebar.style.overflow = 'hidden';
                sidebar.style.opacity = '0';

                toggleBtn.innerHTML = '<svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
                toggleBtn.setAttribute('title', 'Expand sidebar');
                showSidebarBtn.classList.remove('hidden');
            } else {
                sidebar.style.width = '320px';
                sidebar.style.overflow = 'visible';
                sidebar.style.opacity = '1';

                toggleBtn.innerHTML = '<svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>';
                toggleBtn.setAttribute('title', 'Collapse sidebar');
                showSidebarBtn.classList.add('hidden');
            }
        }

        function expandSidebar() {
            const sidebar = $('#filterSidebar');
            const toggleBtn = $('#toggleSidebar');
            const showSidebarBtn = $('#showSidebar');

            if (!sidebar || !toggleBtn || !showSidebarBtn) return;

            sidebar.style.width = '320px';
            sidebar.style.overflow = 'visible';
            sidebar.style.opacity = '1';

            toggleBtn.innerHTML = '<svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>';
            toggleBtn.setAttribute('title', 'Collapse sidebar');
            showSidebarBtn.classList.add('hidden');
            sidebarCollapsed = false;
        }

        function toggleMobileSidebar() {
            const sidebar = $('#filterSidebar');
            const overlay = $('#sidebarOverlay');

            if (!sidebar || !overlay) return;

            if (sidebar.classList.contains('mobile-open')) {
                closeMobileSidebar();
            } else {
                sidebar.classList.add('mobile-open');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileSidebar() {
            const sidebar = $('#filterSidebar');
            const overlay = $('#sidebarOverlay');

            if (sidebar) sidebar.classList.remove('mobile-open');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotifications = $$('.custom-notification');
            existingNotifications.forEach(notif => notif.remove());

            let bgClass = 'bg-indigo-600';
            let icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';

            if (type === 'warning') {
                bgClass = 'bg-yellow-500';
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
            }
            if (type === 'error') {
                bgClass = 'bg-red-500';
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            }
            if (type === 'success') {
                bgClass = 'bg-green-500';
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            }

            const notificationHTML = `
                                                                                                                                                        <div class="custom-notification fixed top-6 right-6 px-6 py-4 rounded-xl shadow-2xl z-[100] transform transition-all duration-300 ${bgClass} text-white flex items-center gap-3 max-w-md" style="opacity: 0; transform: translateX(100px);">
                                                                                                                                                            ${icon}
                                                                                                                                                            <span class="flex-1">${message}</span>
                                                                                                                                                            <button class="ml-2 text-white hover:text-gray-200 transition-colors" onclick="this.parentElement.remove()">
                                                                                                                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                                                                                                                </svg>
                                                                                                                                                            </button>
                                                                                                                                                        </div>
                                                                                                                                                    `;

            document.body.insertAdjacentHTML('beforeend', notificationHTML);

            const notification = document.body.lastElementChild;

            // Auto-slide in
            setTimeout(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translateX(0)';
            }, 10);

            // Auto-dismiss
            setTimeout(() => {
                if (notification && notification.parentElement) {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100px)';
                    setTimeout(() => {
                        if (notification && notification.parentElement) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 4000);
        }

        function viewDetails(beneficiaryId) {
            showNotification(`Opening details for beneficiary ${beneficiaryId}`, 'info');
            console.log('View details for:', beneficiaryId);
        }
    </script>
@endpush