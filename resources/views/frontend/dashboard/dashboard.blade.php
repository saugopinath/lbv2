@php $hideSidebar = false; @endphp
@extends('frontend.layouts.dashboard')

@section('header_title', '| Dashboard')
@section('header_description', 'One Unbrella Scheme | Department of Finance | Government of West Bengal')


@push('head_scripts')
<!-- Optional: Add this CSS for subtle animations -->
<style>
    @keyframes subtle-glow {

        0%,
        100% {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        50% {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    }

    .stat-card:hover {
        animation: subtle-glow 2s ease-in-out infinite;
    }

    /* Premium Map Styles */
    .district {
        fill: rgba(243, 244, 246, 0.8);
        stroke: rgba(99, 102, 241, 0.5);
        stroke-width: 0.8;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .district:hover {
        fill: rgba(139, 92, 246, 0.4) !important;
        stroke: #4f46e5;
        stroke-width: 1.5;
        filter: drop-shadow(0 4px 6px rgba(99, 102, 241, 0.3));
    }

    .district.selected {
        fill: rgba(79, 70, 229, 0.9) !important;
        stroke: #312e81;
        stroke-width: 2;
        filter: drop-shadow(0 10px 15px rgba(79, 70, 229, 0.5));
    }

    .tooltip {
        position: fixed;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(8px);
        color: #fff;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 12px;
        pointer-events: none;
        display: none;
        z-index: 1000;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transform: translate(15px, -15px);
    }
</style>
@endpush

@push('head_scripts')
<meta name="map-district-count-url" content="{{ route('map.district.count') }}">
@endpush
@push('head_scripts')
<style>
    /* Premium Map Styles */
    .district {
        fill: rgba(255, 255, 255, 0.8);
        stroke: rgba(99, 102, 241, 0.5);
        stroke-width: 1;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .district:hover {
        fill: rgba(139, 92, 246, 0.4) !important;
        stroke: #4f46e5;
        stroke-width: 1.5;
        filter: drop-shadow(0 4px 6px rgba(99, 102, 241, 0.4));
    }

    .district.selected {
        fill: rgba(79, 70, 229, 0.9) !important;
        stroke: #312e81;
        stroke-width: 2;
        filter: drop-shadow(0 10px 15px rgba(79, 70, 229, 0.5));
    }

    .tooltip {
        position: fixed;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(8px);
        color: #fff;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 13px;
        pointer-events: none;
        display: none;
        z-index: 1000;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translate(15px, -15px);
        user-select: none;
        transition: opacity 0.2s ease;
    }

    .loading-spinner {
        border: 3px solid rgba(243, 244, 246, 0.5);
        border-top: 3px solid #4f46e5;
        border-radius: 50%;
        width: 44px;
        height: 44px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes blob {
        0% {
            transform: translate(0px, 0px) scale(1);
        }

        33% {
            transform: translate(30px, -50px) scale(1.1);
        }

        66% {
            transform: translate(-20px, 20px) scale(0.9);
        }

        100% {
            transform: translate(0px, 0px) scale(1);
        }
    }

    .animate-blob {
        animation: blob 7s infinite;
    }

    .animation-delay-2000 {
        animation-delay: 2s;
    }

    .animation-delay-4000 {
        animation-delay: 4s;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
</style>
@endpush
@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

    <!-- Total Approved (Till Date) -->
    <div
        class="stat-card rounded-2xl p-6 shadow-lg bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1 tracking-wide">
                    Total Members Applied
                </p>
                <h3 class="text-4xl font-bold text-gray-800 mt-2 stat-number" data-value="{{ $totalApproved }}">
                    0
                </h3>
            </div>
            <div
                class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-200/50">
                <i class="fas fa-users text-white text-2xl"></i>
            </div>
        </div>

    </div>

    <!-- Total Application Applied (Till Date) -->
    <div
        class="stat-card rounded-2xl p-6 shadow-lg bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1 tracking-wide">
                    Total Members Verified
                </p>
                <h3 class="text-4xl font-bold text-gray-800 mt-2 stat-number" data-value="{{ $totalApplied }}">
                    0
                </h3>
            </div>
            <div
                class="w-14 h-14 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center shadow-lg shadow-sky-200/50">
                <i class="fas fa-user-check text-white text-2xl"></i>
            </div>
        </div>

    </div>

    <!-- Total DBT Transfer (Current Month) -->
    <div
        class="stat-card rounded-2xl p-6 shadow-lg bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1 tracking-wide">
                    Total Members Approved
                </p>
                <h3 class="text-4xl font-bold text-gray-800 mt-2 stat-number" data-value="{{ $totalPayCurMonth }}"
                    data-money="true">
                    ₹ 0
                </h3>
            </div>
            <div
                class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-200/50">
                <i class="fas fa-check-double text-white text-2xl"></i>
            </div>
        </div>

    </div>

    <!-- Total DBT Transfer (Current Financial Year) -->
    <div
        class="stat-card rounded-2xl p-6 shadow-lg bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1 tracking-wide">
                    Total Members Rejected
                </p>
                <h3 class="text-4xl font-bold text-gray-800 mt-2 stat-number" data-value="{{ $totalPayCurYear }}"
                    data-money="true">
                    ₹ 0
                </h3>
            </div>
            <div
                class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center shadow-lg shadow-rose-200/50">
                <i class="fas fa-user-times text-white text-2xl"></i>
            </div>
        </div>

    </div>

</div>




<!-- ================= MAP SECTION ================= -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    <!-- Map Column -->
    <div class="lg:col-span-2">
        <div class="glass-card rounded-3xl p-5 h-[650px] relative flex flex-col">

            <div class="flex justify-between items-center mb-4 px-2 pb-2 border-b border-gray-100/50">
                <h2 class="font-bold text-gray-800 flex items-center gap-2 text-lg">
                    <i class="fa-solid fa-map-location-dot text-indigo-500"></i>
                    Geographic Distribution
                </h2>
                <div class="flex items-center gap-3">
                    <div class="flex bg-gray-100/50 rounded-lg p-1 border border-gray-200/50 gap-1">
                        <button id="zoom-out" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Zoom Out"><i class="fa-solid fa-minus"></i></button>
                        <button id="zoom-reset" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Reset Map"><i class="fa-solid fa-expand"></i></button>
                        <button id="zoom-in" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Zoom In"><i class="fa-solid fa-plus"></i></button>
                        <button id="reset-btn" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Reset Selection"><i class="fa-solid fa-arrows-rotate"></i></button>
                    </div>
                    <span class="text-[10px] font-bold text-indigo-400 uppercase bg-indigo-50 px-2 py-1 rounded-md border border-indigo-100">
                        SVG Interactive
                    </span>
                </div>
            </div>

            <!-- LOADER -->
            <div id="loading" class="flex-1 flex flex-col items-center justify-center">
                <div class="loading-spinner mb-4"></div>
                <span class="text-indigo-500 font-bold animate-pulse tracking-widest text-sm uppercase">
                    Fetching Data...
                </span>
            </div>

            <!-- SVG -->
            <div id="map-svg-wrapper" class="flex-1 hidden items-center justify-center overflow-hidden drop-shadow-sm">
                @include('frontend.maps.west_bengal')
            </div>

            <!-- TOOLTIP -->
        </div>
    </div>

    <!-- District Breakdown Column -->
    <div class="lg:col-span-1">
        <div class="glass-card rounded-3xl h-[650px] flex flex-col overflow-hidden">
            <div class="p-6 border-b border-gray-100/50 bg-white/40">
                <h3 class="text-lg font-bold flex items-center gap-2 text-gray-800">
                    <i class="fa-solid fa-chart-pie text-violet-500"></i>
                    District Breakdown
                </h3>
            </div>

            <div id="district-info" class="flex-1 flex flex-col items-center justify-center p-6 text-center">
                <div class="p-8 bg-white/60 shadow-inner rounded-full mb-6 border border-gray-100">
                    <i class="fa-solid fa-hand-pointer text-4xl text-indigo-200"></i>
                </div>
                <h4 class="font-black text-xl text-gray-800 tracking-tight">No Selection</h4>
                <p class="text-gray-500 mt-2 max-w-xs font-medium">
                    Click a district on the map to view detailed beneficiary insights
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <!-- Scheme Applications Chart -->    <!-- District-wise Beneficiaries -->
  <div class="chart-container">
        

        <div id="linechart" style="height: 350px;"></div>
    </div>


  


    <!-- Scheme Categories -->
    <div class="chart-container">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Age Cohort Distribution</h3>
                <p class="text-sm text-gray-500 mt-1">Age cohort distribution</p>
            </div>
            <span class="text-sm text-gray-500">5 Categories</span>
        </div>
        <div id="categoryChart" style="height: 350px;"></div>
    </div>




</div>




<!-- TOOLTIP -->
<div id="custom-tooltip" class="tooltip">
    <div id="tooltip-content"></div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        function formatIndianNumber(num) {
            return num.toLocaleString('en-IN');
        }

        function getIndianSuffix(num) {
            if (num >= 10000000) {
                return {
                    value: num / 10000000,
                    suffix: ' Cr'
                };
            }
            if (num >= 100000) {
                return {
                    value: num / 100000,
                    suffix: ' Lakh'
                };
            }
            return {
                value: num,
                suffix: ''
            };
        }

        function animateCounter(el, duration = 1500) {
            let target = parseFloat(el.dataset.value) || 0;
            let isMoney = el.dataset.money === 'true';
            let startTime = null;

            let suffixData = getIndianSuffix(target);
            let finalValue = suffixData.value;
            let suffix = suffixData.suffix;

            function step(timestamp) {
                if (!startTime) startTime = timestamp;

                let progress = Math.min((timestamp - startTime) / duration, 1);
                let current = finalValue * progress;

                let displayValue = suffix ?
                    current.toFixed(2) :
                    Math.floor(current);

                el.textContent =
                    (isMoney ? '₹ ' : '') +
                    formatIndianNumber(Number(displayValue)) +
                    suffix;

                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            }

            window.requestAnimationFrame(step);
        }

        document.querySelectorAll('.stat-number').forEach(function(el) {
            animateCounter(el);
        });

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const lastRefreshed = document.getElementById('lastRefreshed');
        if (lastRefreshed) {
            lastRefreshed.textContent = 'Last refreshed: ' + new Date().toLocaleString('en-IN');
            lastRefreshed.classList.remove('hidden');
        }

        const refreshSchemeStatus = document.getElementById('refreshSchemeStatus');
        if (refreshSchemeStatus) {
            refreshSchemeStatus.addEventListener('click', function() {
                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';

                fetch("{{ route('dashboard.refreshSchemeStatus') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(function() {
                        loadSchemeStatusTable();
                    })
                    .catch(function() {
                        alert('Failed to refresh materialized view');
                    })
                    .finally(function() {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh Data';
                    });
            });
        }

        loadSchemeStatusTable();

        function loadSchemeStatusTable() {
            const schemeStatusTbody = document.getElementById('schemeStatusTbody');
            if (!schemeStatusTbody) return;

            fetch("{{ route('dashboard.schemeStatusChart') }}", {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(function(response) {
                    let tbody = '';
                    let hasData = false;

                    response.forEach(function(row) {
                        hasData = true;

                        const schemeId = parseInt(row.scheme_id);
                        const entry = parseInt(row.entry_count) || 0;

                        let verified = 0,
                            approved = 0,
                            recommended = 0,
                            rejected = 0;

                        if ([8, 9].includes(schemeId)) {
                            approved = parseInt(row.approved_count) || 0;
                        } else if (schemeId === 17) {
                            verified = parseInt(row.verified_count) || 0;
                            approved = parseInt(row.approved_count) || 0;
                            recommended = parseInt(row.recomended_count) || 0;
                        } else {
                            verified = parseInt(row.verified_count) || 0;
                            approved = parseInt(row.approved_count) || 0;
                            recommended = parseInt(row.recomended_count) || 0;
                            rejected = parseInt(row.rejected_count) || 0;
                        }

                        tbody += `
                                                                            <tr class="hover:bg-gray-50">
                                                                                <td class="px-4 py-3 font-medium text-gray-800">${row.scheme_name}</td>
                                                                                <td class="px-4 py-3 text-right">${entry}</td>
                                                                                <td class="px-4 py-3 text-right">${verified}</td>
                                                                                <td class="px-4 py-3 text-right font-semibold text-green-600">${approved}</td>
                                                                                <td class="px-4 py-3 text-right">${recommended}</td>
                                                                                <td class="px-4 py-3 text-right text-red-500">${rejected}</td>
                                                                            </tr>
                                                                        `;
                    });

                    if (!hasData) {
                        tbody = `
                                                                            <tr>
                                                                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">No data available</td>
                                                                            </tr>
                                                                        `;
                    }

                    schemeStatusTbody.innerHTML = tbody;
                })
                .catch(function() {
                    schemeStatusTbody.innerHTML = `
                                                                        <tr>
                                                                            <td colspan="6" class="px-4 py-6 text-center text-red-500">Failed to load scheme status data</td>
                                                                        </tr>
                                                                    `;
                });
        }

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Create the chart if container exists
        const container = document.getElementById('container');
        if (container) {
            Highcharts.chart('container', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Browser market shares. January, 2022'
                },
                subtitle: {
                    text: 'Click the slices to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
                },

                accessibility: {
                    announceNewData: {
                        enabled: true
                    },
                    point: {
                        valueSuffix: '%'
                    }
                },

                plotOptions: {
                    pie: {
                        borderRadius: 5,
                        dataLabels: [{
                            enabled: true,
                            distance: 15,
                            format: '{point.name}'
                        }, {
                            enabled: true,
                            distance: '-30%',
                            filter: {
                                property: 'percentage',
                                operator: '>',
                                value: 5
                            },
                            format: '{point.y:.1f}%',
                            style: {
                                fontSize: '0.9em',
                                textOutline: 'none'
                            }
                        }],
                        states: {
                            inactive: {
                                opacity: 0.8
                            }
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: ' +
                        '<b>{point.y:.2f}%</b> of total<br/>'
                },

                series: [{
                    name: 'Browsers',
                    colorByPoint: true,
                    data: [{
                            name: 'Chrome',
                            y: 61.04,
                            drilldown: 'Chrome'
                        },
                        {
                            name: 'Safari',
                            y: 9.47,
                            drilldown: 'Safari'
                        },
                        {
                            name: 'Edge',
                            y: 9.32,
                            drilldown: 'Edge'
                        },
                        {
                            name: 'Firefox',
                            y: 8.15,
                            drilldown: 'Firefox'
                        },
                        {
                            name: 'Other',
                            y: 11.02,
                            drilldown: null
                        }
                    ]
                }],
                drilldown: {
                    series: [{
                            name: 'Chrome',
                            id: 'Chrome',
                            data: [
                                [
                                    'v97.0',
                                    36.89
                                ],
                                [
                                    'v96.0',
                                    18.16
                                ],
                                [
                                    'v95.0',
                                    0.54
                                ],
                                [
                                    'v94.0',
                                    0.7
                                ],
                                [
                                    'v93.0',
                                    0.8
                                ],
                                [
                                    'v92.0',
                                    0.41
                                ],
                                [
                                    'v91.0',
                                    0.31
                                ],
                                [
                                    'v90.0',
                                    0.13
                                ],
                                [
                                    'v89.0',
                                    0.14
                                ],
                                [
                                    'v88.0',
                                    0.1
                                ],
                                [
                                    'v87.0',
                                    0.35
                                ],
                                [
                                    'v86.0',
                                    0.17
                                ],
                                [
                                    'v85.0',
                                    0.18
                                ],
                                [
                                    'v84.0',
                                    0.17
                                ],
                                [
                                    'v83.0',
                                    0.21
                                ],
                                [
                                    'v81.0',
                                    0.1
                                ],
                                [
                                    'v80.0',
                                    0.16
                                ],
                                [
                                    'v79.0',
                                    0.43
                                ],
                                [
                                    'v78.0',
                                    0.11
                                ],
                                [
                                    'v76.0',
                                    0.16
                                ],
                                [
                                    'v75.0',
                                    0.15
                                ],
                                [
                                    'v72.0',
                                    0.14
                                ],
                                [
                                    'v70.0',
                                    0.11
                                ],
                                [
                                    'v69.0',
                                    0.13
                                ],
                                [
                                    'v56.0',
                                    0.12
                                ],
                                [
                                    'v49.0',
                                    0.17
                                ]
                            ]
                        },
                        {
                            name: 'Safari',
                            id: 'Safari',
                            data: [
                                [
                                    'v15.3',
                                    0.1
                                ],
                                [
                                    'v15.2',
                                    2.01
                                ],
                                [
                                    'v15.1',
                                    2.29
                                ],
                                [
                                    'v15.0',
                                    0.49
                                ],
                                [
                                    'v14.1',
                                    2.48
                                ],
                                [
                                    'v14.0',
                                    0.64
                                ],
                                [
                                    'v13.1',
                                    1.17
                                ],
                                [
                                    'v13.0',
                                    0.13
                                ],
                                [
                                    'v12.1',
                                    0.16
                                ]
                            ]
                        },
                        {
                            name: 'Edge',
                            id: 'Edge',
                            data: [
                                [
                                    'v97',
                                    6.62
                                ],
                                [
                                    'v96',
                                    2.55
                                ],
                                [
                                    'v95',
                                    0.15
                                ]
                            ]
                        },
                        {
                            name: 'Firefox',
                            id: 'Firefox',
                            data: [
                                [
                                    'v96.0',
                                    4.17
                                ],
                                [
                                    'v95.0',
                                    3.33
                                ],
                                [
                                    'v94.0',
                                    0.11
                                ],
                                [
                                    'v91.0',
                                    0.23
                                ],
                                [
                                    'v78.0',
                                    0.16
                                ],
                                [
                                    'v52.0',
                                    0.15
                                ]
                            ]
                        }
                    ]
                },

                navigation: {
                    breadcrumbs: {
                        buttonTheme: {
                            style: {
                                color: 'var(--highcharts-highlight-color-100)'
                            }
                        }
                    }
                }
            });
        }


        const applicationsChartContainer = document.getElementById('applicationsChart');
        if (applicationsChartContainer) {
            loadSchemeWiseApplications('all');
        }

        const schemeFilter = document.getElementById('schemeFilter');
        if (schemeFilter) {
            schemeFilter.addEventListener('change', function() {
                loadSchemeWiseApplications(this.value);
            });
        }

        function loadSchemeWiseApplications(days) {
            if (!document.getElementById('applicationsChart')) return;
            fetch("{{ route('dashboard.schemeWiseApplications') }}?days=" + days, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(function(response) {
                    if (!document.getElementById('applicationsChart')) return;
                    Highcharts.chart('applicationsChart', {
                        chart: {
                            type: 'column',
                            backgroundColor: 'transparent'
                        },
                        title: {
                            text: null
                        },
                        xAxis: {
                            categories: response.categories,
                            labels: {
                                rotation: -45,
                                style: {
                                    fontSize: '11px'
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Applications'
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        plotOptions: {
                            column: {
                                borderRadius: 8,
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        series: [{
                            name: 'Applications',
                            data: response.data,
                            colorByPoint: true,
                            colors: [
                                '#ec4899', '#3b82f6', '#10b981',
                                '#14b8a6', '#a855f7', '#84cc16',
                                '#f97316', '#0ea5e9', '#6366f1'
                            ]
                        }],
                        credits: {
                            enabled: false
                        }
                    });
                });
        }

        const dummyDistrictData = {
            "309": { name: "DARJEELING", count: 125000 },
            "702": { name: "KALIMPONG", count: 45000 },
            "314": { name: "JALPAIGURI", count: 215000 },
            "664": { name: "ALIPURDUAR", count: 182000 },
            "308": { name: "COOCHBEHAR", count: 324000 },
            "311": { name: "DINAJPUR UTTAR", count: 298000 },
            "310": { name: "DINAJPUR DAKSHIN", count: 242000 },
            "316": { name: "MALDAH", count: 415000 },
            "319": { name: "MURSHIDABAD", count: 752000 },
            "307": { name: "BIRBHUM", count: 385000 },
            "315": { name: "KMC", count: 456000 },
            "704": { name: "PASCHIM BARDHAMAN", count: 394000 },
            "306": { name: "PURBA BARDHAMAN", count: 482000 },
            "320": { name: "NADIA", count: 542000 },
            "305": { name: "BANKURA", count: 365000 },
            "321": { name: "PURULIA", count: 284000 },
            "312": { name: "HOOGHLY", count: 524000 },
            "313": { name: "HOWRAH", count: 492000 },
            "318": { name: "MEDINIPUR WEST", count: 468000 },
            "703": { name: "JHARGRAM", count: 142000 },
            "317": { name: "MEDINIPUR EAST", count: 512000 },
            "303": { name: "24 PARAGANAS NORTH", count: 894000 },
            "304": { name: "24 PARAGANAS SOUTH", count: 925000 }
        };

        function normalizeName(name) {
            return name ? name.toUpperCase().replace(/[^A-Z]/g, '') : '';
        }

        function getHeatmapColor(count, min, max) {
            if (count === 0) return 'rgba(243, 244, 246, 0.8)';
            let ratio = (count - min) / (max - min || 1);
            let h = 230 + ratio * 32;       // 230 (indigo) to 262 (violet)
            let s = 75 + ratio * 15;        // 75% to 90%
            let l = 90 - ratio * 40;        // 90% down to 50%
            return `hsl(${h}, ${s}%, ${l}%)`;
        }

        function showTooltip(e, name, count) {
            const tooltip = document.getElementById('custom-tooltip');
            document.getElementById('tooltip-content').innerHTML = `
                <div class="font-bold border-b border-gray-600/50 pb-1.5 mb-1.5 text-indigo-100 flex items-center justify-between gap-3">
                    <span>${name}</span>
                    <i class="fa-solid fa-map-pin text-[10px] text-indigo-400"></i>
                </div>
                <div class="text-indigo-200 text-xs font-medium">
                    Beneficiaries: <span class="text-white font-black ml-1">${count.toLocaleString('en-IN')}</span>
                </div>
            `;
            tooltip.style.display = 'block';
            moveTooltip(e);
        }

        function moveTooltip(e) {
            const tooltip = document.getElementById('custom-tooltip');
            tooltip.style.left = e.clientX + 'px';
            tooltip.style.top = e.clientY + 'px';
        }

        function hideTooltip() {
            document.getElementById('custom-tooltip').style.display = 'none';
        }

        let districtChartInstance = null;
        let districtData = {};

        function selectDistrict(path) {
            document.querySelectorAll('.district').forEach(el => el.classList.remove('selected'));
            path.classList.add('selected');
            
            const name = path.dataset.name;
            if (districtChartInstance) {
                const chartPoints = districtChartInstance.series[0].points;
                const targetPoint = chartPoints.find(p => normalizeName(p.category) === normalizeName(name));
                
                if (targetPoint) {
                    targetPoint.select(true, false);
                    
                    const container = document.getElementById('districtChart').parentNode;
                    const scrollPos = targetPoint.plotX + (districtChartInstance.plotTop || 0) - 100;
                    container.scrollTo({
                        top: scrollPos,
                        behavior: 'smooth'
                    });
                }
            }
        }

        function highlightMapPathByName(name) {
            document.querySelectorAll('.district').forEach(el => {
                el.classList.remove('selected');
                if (normalizeName(el.dataset.name) === normalizeName(name)) {
                    el.classList.add('selected');
                }
            });
        }

        loadDistrictWiseBeneficiaries();

  

        function loadAgeDistribution() {
            fetch("{{ route('dashboard.ageDistribution') }}", {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(function(data) {
                    Highcharts.chart('categoryChart', {
                        chart: {
                            type: 'pie',
                            backgroundColor: 'transparent'
                        },
                        title: {
                            text: 'Age Distribution'
                        },
                        plotOptions: {
                            pie: {
                                innerSize: '65%',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.y}',
                                    style: {
                                        fontSize: '12px'
                                    }
                                }
                            }
                        },
                        tooltip: {
                            pointFormat: '<b>{point.y}</b> beneficiaries ({point.percentage:.1f}%)'
                        },
                        series: [{
                            name: 'Beneficiaries',
                            data: [{
                                    name: '0–18',
                                    y: Number(data.age_0_18),
                                    color: '#38bdf8'
                                },
                                {
                                    name: '18–30',
                                    y: Number(data.age_18_30),
                                    color: '#6366f1'
                                },
                                {
                                    name: '30–45',
                                    y: Number(data.age_30_45),
                                    color: '#22c55e'
                                },
                                {
                                    name: '45–60',
                                    y: Number(data.age_45_60),
                                    color: '#f97316'
                                },
                                {
                                    name: '60+',
                                    y: Number(data.age_60_plus),
                                    color: '#ef4444'
                                }
                            ]
                        }],
                        credits: {
                            enabled: false
                        }
                    });
                })
                .catch(function() {
                    console.error('Failed to load age distribution data');
                });
        }

        loadAgeDistribution();

        const refreshAgeChart = document.getElementById('refreshAgeChart');
        if (refreshAgeChart) {
            refreshAgeChart.addEventListener('click', function() {
                loadAgeDistribution();
            });
        }

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        if (typeof Highcharts === 'undefined') {
            console.error('Highcharts is not loaded!');
            return;
        }

        fetch("{{ route('dashboard.fy.consolidated') }}?fin_year={{ $cur_fin_year }}", {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(function(response) {
                if (response.status !== 'success') {
                    console.error('Invalid response');
                    return;
                }

                if (document.getElementById('trendsChart')) {
                    Highcharts.chart('trendsChart', {
                        chart: {
                            type: 'areaspline',
                            backgroundColor: 'transparent'
                        },
                        title: {
                            text: null
                        },
                        xAxis: {
                            categories: [
                                'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
                                'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'
                            ]
                        },
                        yAxis: {
                            title: {
                                text: 'Payment Amount (₹)'
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        plotOptions: {
                            areaspline: {
                                fillColor: {
                                    linearGradient: {
                                        x1: 0,
                                        y1: 0,
                                        x2: 0,
                                        y2: 1
                                    },
                                    stops: [
                                        [0, 'rgba(102, 126, 234, 0.35)'],
                                        [1, 'rgba(102, 126, 234, 0.05)']
                                    ]
                                },
                                lineWidth: 3,
                                marker: {
                                    enabled: true,
                                    radius: 4
                                }
                            }
                        },
                        series: [{
                            name: 'DBT Payment',
                            data: response.series,
                            color: '#667eea'
                        }],
                        credits: {
                            enabled: false
                        }
                    });
                }
            })
            .catch(function() {
                console.error('Fetch failed');
            });

    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
       // Create the chart
Highcharts.chart('mispiechart', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'Caste wise Member Entry Stat'
    },

    accessibility: {
        announceNewData: {
            enabled: true
        },
        point: {
            valueSuffix: '%'
        }
    },

    plotOptions: {
        pie: {
            borderRadius: 5,
            dataLabels: [{
                enabled: true,
                distance: 15,
                format: '{point.name}'
            }, {
                enabled: true,
                distance: '-30%',
                filter: {
                    property: 'percentage',
                    operator: '>',
                    value: 5
                },
                format: '{point.y:.1f}%',
                style: {
                    fontSize: '0.9em',
                    textOutline: 'none'
                }
            }],
            states: {
                inactive: {
                    opacity: 0.8
                }
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: ' +
            '<b>{point.y:.2f}%</b> of total<br/>'
    },

    series: [
        {
            name: 'Caste',
            colorByPoint: true,
            data: [
                {
                    name: 'UR',
                    y: 61.04,
                    drilldown: 'UR'
                },
                {
                    name: 'UR-EWS',
                    y: 9.47,
                    drilldown: 'UR-EWS'
                },
                {
                    name: 'SC',
                    y: 9.32,
                    drilldown: 'SC'
                },
                {
                    name: 'ST',
                    y: 8.15,
                    drilldown: 'ST'
                },
                 {
                    name: 'OBC',
                    y: 8.15,
                    drilldown: 'OBC'
                },
                {
                    name: 'PVTG',
                    y: 11.02,
                    drilldown: null
                }
            ]
        }
    ],
    drilldown: {
        series: [
            {
                name: 'Chrome',
                id: 'Chrome',
                data: [
                    [
                        'v97.0',
                        36.89
                    ],
                    [
                        'v96.0',
                        18.16
                    ],
                    [
                        'v95.0',
                        0.54
                    ],
                    [
                        'v94.0',
                        0.7
                    ],
                    [
                        'v93.0',
                        0.8
                    ],
                    [
                        'v92.0',
                        0.41
                    ],
                    [
                        'v91.0',
                        0.31
                    ],
                    [
                        'v90.0',
                        0.13
                    ],
                    [
                        'v89.0',
                        0.14
                    ],
                    [
                        'v88.0',
                        0.1
                    ],
                    [
                        'v87.0',
                        0.35
                    ],
                    [
                        'v86.0',
                        0.17
                    ],
                    [
                        'v85.0',
                        0.18
                    ],
                    [
                        'v84.0',
                        0.17
                    ],
                    [
                        'v83.0',
                        0.21
                    ],
                    [
                        'v81.0',
                        0.1
                    ],
                    [
                        'v80.0',
                        0.16
                    ],
                    [
                        'v79.0',
                        0.43
                    ],
                    [
                        'v78.0',
                        0.11
                    ],
                    [
                        'v76.0',
                        0.16
                    ],
                    [
                        'v75.0',
                        0.15
                    ],
                    [
                        'v72.0',
                        0.14
                    ],
                    [
                        'v70.0',
                        0.11
                    ],
                    [
                        'v69.0',
                        0.13
                    ],
                    [
                        'v56.0',
                        0.12
                    ],
                    [
                        'v49.0',
                        0.17
                    ]
                ]
            },
            {
                name: 'Safari',
                id: 'Safari',
                data: [
                    [
                        'v15.3',
                        0.1
                    ],
                    [
                        'v15.2',
                        2.01
                    ],
                    [
                        'v15.1',
                        2.29
                    ],
                    [
                        'v15.0',
                        0.49
                    ],
                    [
                        'v14.1',
                        2.48
                    ],
                    [
                        'v14.0',
                        0.64
                    ],
                    [
                        'v13.1',
                        1.17
                    ],
                    [
                        'v13.0',
                        0.13
                    ],
                    [
                        'v12.1',
                        0.16
                    ]
                ]
            },
            {
                name: 'Edge',
                id: 'Edge',
                data: [
                    [
                        'v97',
                        6.62
                    ],
                    [
                        'v96',
                        2.55
                    ],
                    [
                        'v95',
                        0.15
                    ]
                ]
            },
            {
                name: 'Firefox',
                id: 'Firefox',
                data: [
                    [
                        'v96.0',
                        4.17
                    ],
                    [
                        'v95.0',
                        3.33
                    ],
                    [
                        'v94.0',
                        0.11
                    ],
                    [
                        'v91.0',
                        0.23
                    ],
                    [
                        'v78.0',
                        0.16
                    ],
                    [
                        'v52.0',
                        0.15
                    ]
                ]
            }
        ]
    },

    navigation: {
        breadcrumbs: {
            buttonTheme: {
                style: {
                    color: 'var(--highcharts-highlight-color-100)'
                }
            }
        }
    }
});
// Data retrieved https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature
Highcharts.chart('linechart', {
    title: {
        text: 'Member Entry in last 7 days'
    },

    accessibility: {
        point: {
            valueDescriptionFormat:
                '{xDescription}{separator}{value} million(s)'
        }
    },

    xAxis: {
        title: {
            text: 'Year'
        },
        categories: ['day1', 'day2', 'day3', 'day4', 'day5', 'day6', 'day7']
    },

    yAxis: {
        type: 'logarithmic',
        title: {
            text: 'Number of Members Entry (in thousands)'
        }
    },

    tooltip: {
        headerFormat: '<b>{series.name}</b><br />',
        pointFormat: '{point.y} million(s)'
    },

    series: [{
        name: 'Internet Users',
        data: [16, 361, 1018, 2025, 3192, 4673, 5200],
        color: 'var(--highcharts-color-1, #2caffe)'
    }]
});


    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let districtData = {};

        async function initMap() {
            try {
                const mapUrlMeta = document.querySelector('meta[name="map-district-count-url"]');
                if (!mapUrlMeta) {
                    console.warn('map-district-count-url meta tag not found');
                    return;
                }
                const response = await fetch(mapUrlMeta.content, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                });

                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }

                districtData = await response.json();

                document.getElementById('loading').style.display = 'none';
                const mapWrapper = document.getElementById('map-svg-wrapper');
                mapWrapper.classList.remove('hidden');
                mapWrapper.classList.add('flex');

                bindDistricts();
                updateStats();

            } catch (err) {
                console.error(err);
                document.getElementById('loading').innerHTML = `
                                    <div class="text-center">
                                        <i class="fa-solid fa-triangle-exclamation text-red-500 text-3xl mb-2"></i>
                                        <p class="text-red-600 font-bold">Failed to load district data</p>
                                    </div>
                                `;
            }
        }

        function bindDistricts() {
            document.querySelectorAll('.district').forEach(function(d) {
                const code = d.getAttribute('district-code');
                const name = d.dataset.name;
                const count = parseInt(districtData[code] || 0);

                d.dataset.count = count;
                d.dataset.name = name;
                setColor(d, count);

                d.addEventListener('mouseenter', e => showTooltip(e, name, count));
                d.addEventListener('mousemove', moveTooltip);
                d.addEventListener('mouseleave', hideTooltip);
                d.addEventListener('click', () => selectDistrict(d, code, name, count));
            });
        }

        function setColor(d, count) {
            let c = 'rgba(255, 255, 255, 0.7)';
            if (count > 500) c = 'rgba(79, 70, 229, 0.95)';
            else if (count > 200) c = 'rgba(99, 102, 241, 0.8)';
            else if (count > 50) c = 'rgba(129, 140, 248, 0.6)';
            else if (count > 0) c = 'rgba(199, 210, 254, 0.5)';
            d.style.fill = c;
        }

        function selectDistrict(d, code, name, count) {
            document.querySelectorAll('.district').forEach(el => el.classList.remove('selected'));
            d.classList.add('selected');

            const totalBeneficiaries = total();
            const pct = totalBeneficiaries > 0 ? ((count / totalBeneficiaries) * 100).toFixed(2) : 0;

            const infoEl = document.getElementById('district-info');
            infoEl.style.opacity = '0';
            infoEl.style.transition = 'opacity 0.15s';

            setTimeout(() => {
                infoEl.innerHTML = `
                                    <div class="w-full animate-fade-in">
                                        <div class="text-center mb-8">
                                            <span class="bg-indigo-100/50 text-indigo-700 text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest border border-indigo-200/50 shadow-sm">District Selected</span>
                                            <h4 class="text-3xl font-black text-gray-900 mt-5 tracking-tight">${name}</h4>
                                            <div class="w-16 h-1.5 bg-gradient-to-r from-indigo-500 to-purple-500 mx-auto mt-4 rounded-full"></div>
                                        </div>
                                        <div class="space-y-4">
                                            <div class="glass-card rounded-2xl p-6 text-center shadow-sm relative overflow-hidden">
                                                <div class="absolute -right-4 -top-4 opacity-[0.03] text-8xl"><i class="fa-solid fa-users"></i></div>
                                                <p class="text-gray-500 text-xs font-bold uppercase mb-2 tracking-wider">Total Beneficiaries</p>
                                                <p class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-br from-indigo-600 to-violet-600 tracking-tighter">${count.toLocaleString()}</p>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="glass-card rounded-2xl p-4 text-left shadow-sm">
                                                    <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider mb-1">State Share</p>
                                                    <p class="text-2xl font-black text-gray-800">${pct}%</p>
                                                </div>
                                                <div class="glass-card rounded-2xl p-4 text-left shadow-sm">
                                                    <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider mb-1">Status</p>
                                                    <p class="text-xl font-black text-emerald-500 truncate flex items-center gap-1.5">
                                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Active
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                infoEl.style.opacity = '1';
            }, 150);
        }

        function updateStats() {
            const t = total();
            const keys = Object.keys(districtData);
            const d = keys.length;
            const avg = d ? Math.round(t / d) : 0;

            let highest = {
                name: '-',
                count: 0
            };
            document.querySelectorAll('.district').forEach(function(el) {
                const c = parseInt(el.dataset.count || 0);
                if (c > highest.count) {
                    highest = {
                        name: el.dataset.name,
                        count: c
                    };
                }
            });

            const totalCountEl = document.getElementById('total-count');
            if (totalCountEl) totalCountEl.textContent = t.toLocaleString();

            const avgCountEl = document.getElementById('avg-count');
            if (avgCountEl) avgCountEl.textContent = avg.toLocaleString();

            const highestDistrictEl = document.getElementById('highest-district');
            if (highestDistrictEl) highestDistrictEl.textContent = highest.name;
        }

        function total() {
            return Object.values(districtData).reduce((a, b) => a + (parseInt(b) || 0), 0);
        }

        function showTooltip(e, name, count) {
            const tooltipContent = document.getElementById('tooltip-content');
            if (tooltipContent) {
                tooltipContent.innerHTML = `
                                    <div class="font-bold border-b border-gray-600/50 pb-1.5 mb-1.5 text-indigo-100 flex items-center justify-between gap-3">
                                        <span>${name}</span>
                                        <i class="fa-solid fa-map-pin text-[10px] text-indigo-400"></i>
                                    </div>
                                    <div class="text-indigo-200 text-xs font-medium">
                                        Beneficiaries: <span class="text-white font-black ml-1">${count.toLocaleString()}</span>
                                    </div>
                                `;
            }
            const customTooltip = document.getElementById('custom-tooltip');
            if (customTooltip) customTooltip.style.display = 'block';
            moveTooltip(e);
        }

        function moveTooltip(e) {
            const tooltip = document.getElementById('custom-tooltip');
            if (tooltip) {
                tooltip.style.left = e.clientX + 'px';
                tooltip.style.top = e.clientY + 'px';
            }
        }

        function hideTooltip() {
            const tooltip = document.getElementById('custom-tooltip');
            if (tooltip) tooltip.style.display = 'none';
        }

        const resetBtn = document.getElementById('reset-btn');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                document.querySelectorAll('.district').forEach(el => el.classList.remove('selected'));
                const infoEl = document.getElementById('district-info');
                if (infoEl) {
                    infoEl.innerHTML = `
                                        <div class="p-8 bg-white/60 shadow-inner rounded-full mb-6 border border-gray-100 animate-fade-in">
                                            <i class="fa-solid fa-hand-pointer text-4xl text-indigo-200"></i>
                                        </div>
                                        <h4 class="font-black text-xl text-gray-800 tracking-tight animate-fade-in" style="animation-delay: 0.1s">No Selection</h4>
                                        <p class="text-gray-500 mt-2 max-w-xs font-medium animate-fade-in" style="animation-delay: 0.2s">
                                            Click a district on the map to view detailed beneficiary insights
                                        </p>
                                    `;
                }
            });
        }

        // Zoom and Pan Logic
        let currentZoom = 1;
        let isDragging = false;
        let startX, startY, translateX = 0,
            translateY = 0;

        const zoomStep = 0.2;
        const minZoom = 0.5;
        const maxZoom = 4;
        const svgWrapper = document.getElementById('map-svg-wrapper');
        let svgElement = null;

        document.getElementById('zoom-in').addEventListener('click', () => {
            if (currentZoom < maxZoom) currentZoom += zoomStep;
            updateZoom();
        });

        document.getElementById('zoom-out').addEventListener('click', () => {
            if (currentZoom > minZoom) currentZoom -= zoomStep;
            updateZoom();
        });

        document.getElementById('zoom-reset').addEventListener('click', () => {
            currentZoom = 1;
            translateX = 0;
            translateY = 0;
            updateZoom();
        });

        svgWrapper.addEventListener('mousedown', (e) => {
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            svgWrapper.style.cursor = 'grabbing';
        });

        window.addEventListener('mouseup', () => {
            isDragging = false;
            svgWrapper.style.cursor = 'grab';
        });

        window.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            updateZoom();
        });

        // Scroll to zoom
        svgWrapper.addEventListener('wheel', (e) => {
            e.preventDefault();
            if (e.deltaY < 0) {
                if (currentZoom < maxZoom) currentZoom += zoomStep;
            } else {
                if (currentZoom > minZoom) currentZoom -= zoomStep;
            }
            updateZoom();
        }, {
            passive: false
        });

        function updateZoom() {
            if (!svgElement) svgElement = svgWrapper.querySelector('svg');
            if (svgElement) {
                svgElement.style.transform = `translate(${translateX}px, ${translateY}px) scale(${currentZoom})`;
                svgElement.style.transition = isDragging ? 'none' : 'transform 0.2s ease-out';
            }
        }

        svgWrapper.style.cursor = 'grab';

        initMap();
    });
</script>

@endpush