<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('jblbConf.title') }} Portal | Dashboard @yield('title')</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>


    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        :root {
            --primary: #1e40af;
            --secondary: #7c3aed;
            --accent: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
        }

        body {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            background-attachment: fixed;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .scheme-card {
            background: white;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .scheme-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .scheme-card:hover::before {
            transform: scaleX(1);
        }

        .scheme-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(102, 126, 234, 0.2);
        }

        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-link {
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 60%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
            border-radius: 0 4px 4px 0;
        }

        .sidebar-link:hover::before,
        .sidebar-link.active::before {
            width: 4px;
        }

        .sidebar-link.active {
            background: rgba(102, 126, 234, 0.15);
            color: #a5b4fc;
        }

        .chart-container {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .7;
            }
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            border-radius: 9999px;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            border-radius: 10px;
            background-color: #667eea;
        }

        ::-webkit-scrollbar-thumb:hover {
            /* background: linear-gradient(135deg, #764ba2 0%, #667eea 100%); */
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head_scripts')
</head>

<body>
    <!-- Sidebar -->
    @if(!isset($hideSidebar) || !$hideSidebar)
        @include('frontend.dashboard.components.sidebar')
    @endif

    <!-- Main Content -->
    <div class="relative min-h-screen w-full {{ (isset($hideSidebar) && $hideSidebar) ? '' : 'lg:ps-64' }} bg-gray-50">

        <div class="p-4 sm:p-6 space-y-6">

            <!-- Header -->
            <div class="glass-effect rounded-2xl shadow-xl p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

                    <!-- Left Section -->
                    <div class="flex items-center gap-4">
                        <!-- Mobile Sidebar Toggle -->
                        <button type="button" class="lg:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100"
                            data-hs-overlay="#application-sidebar">
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <!-- Logo + Title -->
                        <div class="flex items-center gap-4">
                            

                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold gradient-text leading-tight">
                                    {{ config('jblbConf.title') }}
                                    <!-- <span class="block sm:inline">@yield('header_title')</span> -->
                                </h1>
                               
                            </div>
                        </div>
                    </div>

                    <!-- Right Section -->
                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex items-center gap-2 py-2 px-4 rounded-full text-sm font-medium bg-green-100 text-green-700">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Report Generation Time: <span id="report-gen-time">{{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i:s A') }}</span>
                        </span>
                    </div>

                </div>
            </div>

            <!-- Page Content -->
            <div class="space-y-6">
                @yield('content')
            </div>

        </div>
    </div>

    <!-- Sidebar Active Link Handler & Live Clock -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.sidebar-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    document.querySelectorAll('.sidebar-link').forEach(el => el.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Live clock for Report Generation Time
            const timeEl = document.getElementById('report-gen-time');
            if (timeEl) {
                function updateClock() {
                    const now = new Date();
                    const dd = String(now.getDate()).padStart(2, '0');
                    const mm = String(now.getMonth() + 1).padStart(2, '0');
                    const yyyy = now.getFullYear();
                    let hours = now.getHours();
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
                    const hh = String(hours).padStart(2, '0');
                    timeEl.textContent = `${dd}-${mm}-${yyyy} ${hh}:${minutes}:${seconds} ${ampm}`;
                }
                updateClock();
                setInterval(updateClock, 1000);
            }
        });
    </script>

    @stack('scripts')
</body>

</html>