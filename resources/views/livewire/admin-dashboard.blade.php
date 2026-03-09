<div class="space-y-6 p-4">
    <!-- Admin Dashboard Header -->
    <div class="flex items-center space-x-3 mb-6">
        <div class="p-2 bg-indigo-100 rounded-lg">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white tracking-tight">Admin Dashboard</h1>
    </div>

    <!-- Summary Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- Total Users -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border-b-4 border-blue-500 hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-start mb-2">
                <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider">Total Active Users</span>
                <div class="p-1.5 bg-blue-50 rounded-lg">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_users']) }}</div>
        </div>

        <!-- Active Users -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border-b-4 border-emerald-500 hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-start mb-2">
                <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider">Online Users</span>
                <div class="p-1.5 bg-emerald-50 rounded-lg">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['online_users']) }}</div>
        </div>

        <!-- Avg Engagement -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border-b-4 border-amber-500 hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-start mb-2">
                <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider">Avg Engagement</span>
                <div class="p-1.5 bg-amber-50 rounded-lg">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white text-amber-600">{{ $stats['avg_engagement'] }}</div>
        </div>

        <!-- New Users -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border-b-4 border-purple-500 hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-start mb-2">
                <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider">New Users (30d)</span>
                <div class="p-1.5 bg-purple-50 rounded-lg">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">+{{ $stats['new_users_30d'] }}</div>
        </div>

        <!-- Modules -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border-b-4 border-sky-400 hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-start mb-2">
                <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider">Modules</span>
                <div class="p-1.5 bg-sky-50 rounded-lg">
                    <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">+{{ $stats['modules_onboarded'] }}</div>
        </div>

        <!-- Admins -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border-b-4 border-rose-500 hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-start mb-2">
                <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider">Admins</span>
                <div class="p-1.5 bg-rose-50 rounded-lg">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">+{{ $stats['total_admins'] }}</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Devices & Browsers (Side by Side) -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Device Chart -->
                <div>
                    <div class="flex items-center space-x-2 mb-6">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Device Login Data</h3>
                    </div>
                    <div class="relative h-64">
                        <canvas id="deviceChart"></canvas>
                    </div>
                </div>

                <!-- Browser Chart -->
                <div>
                    <div class="flex items-center space-x-2 mb-6">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Browser Agent Login Data</h3>
                    </div>
                    <div class="relative h-64">
                        <canvas id="browserChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hourly Visits Chart -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-2 mb-6">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Active User Login Data</h3>
            </div>
            <div class="relative h-64">
                <canvas id="activeUserChart"></canvas>
            </div>
        </div>
    </div>



    <!-- Daily User Activity Section -->
    <livewire:daily-user-activity />

    <!-- Chart Scripts -->
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Chart Defaults
            Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
            Chart.defaults.color = '#94a3b8';

            // Device Donut Chart
            new Chart(document.getElementById('deviceChart'), {
                type: 'doughnut',
                data: {
                    labels: @json($deviceData['labels']),
                    datasets: [{
                        data: @json($deviceData['data']),
                        backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    cutout: '70%'
                }
            });

            // Browser Donut Chart
            new Chart(document.getElementById('browserChart'), {
                type: 'doughnut',
                data: {
                    labels: @json($browserData['labels']),
                    datasets: [{
                        data: @json($browserData['data']),
                        backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#ff9f40'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    cutout: '70%'
                }
            });

            // Active User Line Chart
            new Chart(document.getElementById('activeUserChart'), {
                type: 'line',
                data: {
                    labels: @json($hourlyVisits['labels']),
                    datasets: [{
                        label: 'Visits',
                        data: @json($hourlyVisits['data']),
                        borderColor: '#4bc0c0',
                        backgroundColor: (context) => {
                            const chart = context.chart;
                            const {
                                ctx,
                                chartArea
                            } = chart;
                            if (!chartArea) return null;
                            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradient.addColorStop(0, 'rgba(75, 192, 192, 0)');
                            gradient.addColorStop(1, 'rgba(75, 192, 192, 0.2)');
                            return gradient;
                        },
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#4bc0c0',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.1)'
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</div>