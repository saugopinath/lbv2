@extends('frontend.layouts.dashboard')

@section('header_title', '| Dashboard')
@section('header_description', 'One Unbrella Scheme | Department of Finance | Government of West Bengal')


@push('styles')
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
                        Total Approved (Till Date)
                    </p>
                    <h3 class="text-4xl font-bold text-gray-800 mt-2 stat-number" data-value="{{ $totalApproved }}">
                        0
                    </h3>
                </div>
                <div
                    class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-green-200">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
            </div>
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2 text-sm">
                    <span
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 text-xs font-semibold border border-green-100">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <i class="fas fa-shield-check text-green-600"></i>
                        Verified & Approved
                    </span>
                </div>
            </div>
        </div>

        <!-- Total Application Applied (Till Date) -->
        <div
            class="stat-card rounded-2xl p-6 shadow-lg bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1 tracking-wide">
                        Total Applications Applied (Till Date)
                    </p>
                    <h3 class="text-4xl font-bold text-gray-800 mt-2 stat-number" data-value="{{ $totalApplied }}">
                        0
                    </h3>
                </div>
                <div
                    class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-200">
                    <i class="fas fa-file-alt text-white text-2xl"></i>
                </div>
            </div>
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2 text-sm">
                    <span
                        class="text-blue-600 font-semibold flex items-center gap-2 px-3 py-1.5 rounded-full bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100">
                        <i class="fas fa-layer-group text-blue-500"></i>
                        Cumulative count
                        <span class="text-blue-400 ml-1">•</span>
                        <span class="text-xs font-normal text-blue-500">All time</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Total DBT Transfer (Current Month) -->
        <div
            class="stat-card rounded-2xl p-6 shadow-lg bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1 tracking-wide">
                        Total DBT Transfer (Current Month)
                    </p>
                    <h3 class="text-4xl font-bold text-gray-800 mt-2 stat-number" data-value="{{ $totalPayCurMonth }}"
                        data-money="true">
                        ₹ 0
                    </h3>
                </div>
                <div
                    class="w-14 h-14 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center shadow-lg shadow-orange-200">
                    <i class="fas fa-rupee-sign text-white text-2xl"></i>
                </div>
            </div>
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2 text-sm">
                    <span
                        class="text-orange-600 font-semibold flex items-center gap-2 px-3 py-1.5 rounded-full bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-100">
                        <div class="relative">
                            <i class="fas fa-calendar-alt text-orange-500"></i>
                            <span class="absolute -top-1 -right-1 w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                        </div>
                        Current month
                        <span class="text-orange-400 ml-1">•</span>
                        <span class="text-xs font-normal text-orange-500">Active</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Total DBT Transfer (Current Financial Year) -->
        <div
            class="stat-card rounded-2xl p-6 shadow-lg bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1 tracking-wide">
                        Total DBT Transfer (Current FY)
                    </p>
                    <h3 class="text-4xl font-bold text-gray-800 mt-2 stat-number" data-value="{{ $totalPayCurYear }}"
                        data-money="true">
                        ₹ 0
                    </h3>
                </div>
                <div
                    class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-fuchsia-600 flex items-center justify-center shadow-lg shadow-purple-200">
                    <i class="fas fa-chart-line text-white text-2xl"></i>
                </div>
            </div>
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2 text-sm">
                    <span
                        class="text-purple-600 font-semibold flex items-center gap-2 px-3 py-1.5 rounded-full bg-gradient-to-r from-purple-50 to-fuchsia-50 border border-purple-100">
                        <i class="fas fa-calendar-check text-purple-500"></i>
                        Financial year total
                        <span class="text-purple-400 ml-1">•</span>
                        <span class="text-xs font-normal text-purple-500">FY 2023-24</span>
                    </span>
                </div>
            </div>
        </div>

    </div>




    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Scheme Applications Chart -->
        <div class="chart-container">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        Scheme-wise Applications
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Cumulative performance
                    </p>
                </div>

                <select id="schemeFilter"
                    class="py-2 px-3 pe-9 block border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Time</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                </select>

            </div>

            <div id="applicationsChart" style="height: 350px;"></div>
        </div>


        <!-- District-wise Beneficiaries -->
        <div class="chart-container">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        District-Wise Distribution
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        District Wise Distribution by Approved beneficiaries
                    </p>
                </div>
                <button
                    class="py-2 px-3 inline-flex items-center gap-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 hover:bg-gray-50">
                    <i class="fas fa-map-marker-alt"></i>
                    View Map
                </button>
            </div>

            <div class="relative" style="height: 350px; overflow-y: auto;">
                <div id="districtChart"></div>
            </div>



        </div>


        <div class="chart-container">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        Monthly DBT Payment Statistics
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Year {{ $cur_fin_year }} overview
                    </p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <i class="fas fa-info-circle"></i>
                    Consolidated Payments
                </div>
            </div>

            <div id="trendsChart" style="height: 350px;"></div>
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


    <div class="bg-white rounded-xl shadow mt-6">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Scheme-wise Consolidated Status
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    Consolidated workflow status across schemes
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span id="lastRefreshed" class="text-xs text-gray-500 hidden sm:inline">
                    Last refreshed: --
                </span>

                <button id="refreshSchemeStatus"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg
                                                                                                                                                       bg-blue-600 text-white hover:bg-blue-700
                                                                                                                                                       disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-sync-alt"></i>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm" id="schemeStatusTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Scheme</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Entry</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Verified</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Approved</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Recommended</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Rejected</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100" id="schemeStatusTbody">
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                            Loading data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>




@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            function formatIndianNumber(num) {
                return num.toLocaleString('en-IN');
            }

            function getIndianSuffix(num) {
                if (num >= 10000000) {
                    return { value: num / 10000000, suffix: ' Cr' };
                }
                if (num >= 100000) {
                    return { value: num / 100000, suffix: ' Lakh' };
                }
                return { value: num, suffix: '' };
            }

            function animateCounter($el, duration = 1500) {
                let target = parseFloat($el.data('value')) || 0;
                let isMoney = $el.data('money') === true;
                let startTime = null;

                let suffixData = getIndianSuffix(target);
                let finalValue = suffixData.value;
                let suffix = suffixData.suffix;

                function step(timestamp) {
                    if (!startTime) startTime = timestamp;

                    let progress = Math.min((timestamp - startTime) / duration, 1);
                    let current = finalValue * progress;

                    let displayValue = suffix
                        ? current.toFixed(2)
                        : Math.floor(current);

                    $el.text(
                        (isMoney ? '₹ ' : '') +
                        formatIndianNumber(Number(displayValue)) +
                        suffix
                    );

                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                }

                window.requestAnimationFrame(step);
            }

            $('.stat-number').each(function () {
                animateCounter($(this));
            });

        });
    </script>

    <script>
        $(document).ready(function () {

            $('#lastRefreshed')
                .text('Last refreshed: ' + new Date().toLocaleString('en-IN'))
                .removeClass('hidden');
            $('#refreshSchemeStatus').on('click', function () {

                let btn = $(this);
                btn.prop('disabled', true);
                btn.html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');

                $.ajax({
                    url: "{{ route('dashboard.refreshSchemeStatus') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function () {
                        loadSchemeStatusTable();
                    },
                    error: function () {
                        alert('Failed to refresh materialized view');
                    },
                    complete: function () {
                        btn.prop('disabled', false);
                        btn.html('<i class="fas fa-sync-alt"></i> Refresh Data');
                    }
                });
            });

            loadSchemeStatusTable();

            function loadSchemeStatusTable() {

                $.ajax({
                    url: '/chart/scheme-status',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {

                        let tbody = '';
                        let hasData = false;

                        $.each(response, function (index, row) {

                            hasData = true;

                            const schemeId = parseInt(row.scheme_id);

                            const entry = parseInt(row.entry_count) || 0;

                            let verified = 0,
                                approved = 0,
                                recommended = 0,
                                rejected = 0;

                            if ([8, 9].includes(schemeId)) {
                                approved = parseInt(row.approved_count) || 0;
                            }
                            else if (schemeId === 17) {
                                verified = parseInt(row.verified_count) || 0;
                                approved = parseInt(row.approved_count) || 0;
                                recommended = parseInt(row.recomended_count) || 0;
                            }
                            else {
                                verified = parseInt(row.verified_count) || 0;
                                approved = parseInt(row.approved_count) || 0;
                                recommended = parseInt(row.recomended_count) || 0;
                                rejected = parseInt(row.rejected_count) || 0;
                            }

                            tbody += `
                                                                                                                                                                                                    <tr class="hover:bg-gray-50">
                                                                                                                                                                                                        <td class="px-4 py-3 font-medium text-gray-800">
                                                                                                                                                                                                            ${row.scheme_name}
                                                                                                                                                                                                        </td>
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
                                                                                                                                                                                                        <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                                                                                                                                                                                            No data available
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                `;
                        }

                        $('#schemeStatusTbody').html(tbody);
                    },
                    error: function () {
                        $('#schemeStatusTbody').html(`
                                                                                                                                                                                                <tr>
                                                                                                                                                                                                    <td colspan="6" class="px-4 py-6 text-center text-red-500">
                                                                                                                                                                                                        Failed to load scheme status data
                                                                                                                                                                                                    </td>
                                                                                                                                                                                                </tr>
                                                                                                                                                                                            `);
                    }
                });
            }

        });
    </script>





    <script>
        $(document).ready(function () {
            loadSchemeWiseApplications('all');
            $('#schemeFilter').on('change', function () {
                let days = $(this).val();
                loadSchemeWiseApplications(days);
            });
            function loadSchemeWiseApplications(days) {
                $.ajax({
                    url: "{{ route('dashboard.schemeWiseApplications') }}",
                    method: "GET",
                    data: { days: days },
                    dataType: "json",
                    success: function (response) {
                        Highcharts.chart('applicationsChart', {
                            chart: {
                                type: 'column',
                                backgroundColor: 'transparent'
                            },
                            title: { text: null },
                            xAxis: {
                                categories: response.categories,
                                labels: {
                                    rotation: -45,
                                    style: { fontSize: '11px' }
                                }
                            },
                            yAxis: {
                                title: { text: 'Applications' }
                            },
                            legend: { enabled: false },
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
                            credits: { enabled: false }
                        });
                    }
                });
            }

            loadDistrictWiseBeneficiaries();
            function loadDistrictWiseBeneficiaries() {
                $.ajax({
                    url: "{{ route('dashboard.districtWiseBeneficiaries') }}",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {

                        const totalDistricts = response.categories.length; // 23
                        const barHeight = 45;
                        const minHeight = 350;

                        const chartHeight = Math.max(totalDistricts * barHeight, minHeight);

                        Highcharts.chart('districtChart', {
                            chart: {
                                type: 'bar',
                                height: chartHeight,
                                backgroundColor: 'transparent'
                            },
                            title: { text: null },
                            xAxis: {
                                categories: response.categories,
                                title: { text: null }
                            },
                            yAxis: {
                                title: { text: 'Beneficiaries' }
                            },
                            legend: { enabled: false },
                            plotOptions: {
                                bar: {
                                    borderRadius: 6,
                                    dataLabels: { enabled: true }
                                }
                            },
                            series: [{
                                name: 'Beneficiaries',
                                data: response.data,
                                color: {
                                    linearGradient: { x1: 0, y1: 0, x2: 1, y2: 0 },
                                    stops: [[0, '#667eea']]
                                }
                            }],
                            credits: { enabled: false }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                        alert('Failed to load district-wise beneficiary data');
                    }
                });
            }


            function loadAgeDistribution() {

                $.ajax({
                    url: '/age-distribution',
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {

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
                                data: [
                                    { name: '0–18', y: Number(data.age_0_18), color: '#38bdf8' },
                                    { name: '18–30', y: Number(data.age_18_30), color: '#6366f1' },
                                    { name: '30–45', y: Number(data.age_30_45), color: '#22c55e' },
                                    { name: '45–60', y: Number(data.age_45_60), color: '#f97316' },
                                    { name: '60+', y: Number(data.age_60_plus), color: '#ef4444' }
                                ]
                            }],

                            credits: {
                                enabled: false
                            }
                        });
                    },
                    error: function () {
                        console.error('Failed to load age distribution data');
                    }
                });
            }

            // Initial Load
            loadAgeDistribution();

            // Optional: Refresh button
            $('#refreshAgeChart').on('click', function () {
                loadAgeDistribution();
            });

        });
    </script>




    <script>
        document.addEventListener('DOMContentLoaded', function () {

            if (typeof Highcharts === 'undefined') {
                console.error('Highcharts is not loaded!');
                return;
            }

            $.ajax({
                url: "{{ route('dashboard.fy.consolidated') }}",
                type: "GET",
                data: {
                    fin_year: "{{ $cur_fin_year }}"
                },
                success: function (response) {

                    if (response.status !== 'success') {
                        console.error('Invalid response');
                        return;
                    }

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
                                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
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
                },
                error: function () {
                    console.error('AJAX failed');
                }
            });

        });
    </script>




@endpush