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
                    Total Members Applied
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
                class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-green-200">
                <i class="fas fa-check-circle text-white text-2xl"></i>
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
                class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-green-200">
                <i class="fas fa-check-circle text-white text-2xl"></i>
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
                class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-green-200">
                <i class="fas fa-check-circle text-white text-2xl"></i>
            </div>
        </div>

    </div>

</div>




<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <!-- Scheme Applications Chart -->



    <!-- District-wise Beneficiaries -->
    <div class="chart-container">
        <div class="flex justify-between items-center mb-6">
            
             @include('frontend.maps.west_bengal')
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

        document.getElementById('refreshSchemeStatus').addEventListener('click', function() {
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

        loadSchemeStatusTable();

        function loadSchemeStatusTable() {
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

                    document.getElementById('schemeStatusTbody').innerHTML = tbody;
                })
                .catch(function() {
                    document.getElementById('schemeStatusTbody').innerHTML = `
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

    // Create the chart
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

    series: [
        {
            name: 'Browsers',
            colorByPoint: true,
            data: [
                {
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


        loadSchemeWiseApplications('all');

        document.getElementById('schemeFilter').addEventListener('change', function() {
            loadSchemeWiseApplications(this.value);
        });

        function loadSchemeWiseApplications(days) {
            fetch("{{ route('dashboard.schemeWiseApplications') }}?days=" + days, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(function(response) {
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

        loadDistrictWiseBeneficiaries();

        function loadDistrictWiseBeneficiaries() {
            fetch("{{ route('dashboard.districtWiseBeneficiaries') }}", {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(function(response) {
                    const totalDistricts = response.categories.length;
                    const barHeight = 45;
                    const minHeight = 350;
                    const chartHeight = Math.max(totalDistricts * barHeight, minHeight);

                    Highcharts.chart('districtChart', {
                        chart: {
                            type: 'bar',
                            height: chartHeight,
                            backgroundColor: 'transparent'
                        },
                        title: {
                            text: null
                        },
                        xAxis: {
                            categories: response.categories,
                            title: {
                                text: null
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Beneficiaries'
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 6,
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        series: [{
                            name: 'Beneficiaries',
                            data: response.data,
                            color: {
                                linearGradient: {
                                    x1: 0,
                                    y1: 0,
                                    x2: 1,
                                    y2: 0
                                },
                                stops: [
                                    [0, '#667eea']
                                ]
                            }
                        }],
                        credits: {
                            enabled: false
                        }
                    });
                })
                .catch(function(err) {
                    console.error(err);
                    alert('Failed to load district-wise beneficiary data');
                });
        }

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
            })
            .catch(function() {
                console.error('Fetch failed');
            });

    });
</script>

@endpush