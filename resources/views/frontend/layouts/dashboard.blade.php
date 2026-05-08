<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <title>{{ config('jblbConf.title') }} | Dashboard @yield('title')</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- jQuery (before Highcharts) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Highcharts core + extras -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --indigo-600 : #4f46e5;
            --indigo-700 : #4338ca;
            --purple-600 : #7c3aed;
            --slate-900  : #0f172a;
            --slate-800  : #1e293b;
            --slate-100  : #f1f5f9;
            --white      : #ffffff;
            --header-h   : 76px;
        }

        html, body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            color: #1e293b;
        }

        /* ══ TOP HEADER ═══════════════════════════════════════ */
        .dash-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            padding: 0 32px;
            height: var(--header-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 24px rgba(15, 23, 42, 0.4);
        }

        .dash-header-left {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .dash-header-logo {
            height: 52px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(255,255,255,0.15));
        }

        .dash-header-divider {
            width: 1px;
            height: 38px;
            background: rgba(255,255,255,0.15);
        }

        .dash-header-title {
            display: flex;
            flex-direction: column;
        }

        .dash-header-title h1 {
            font-size: 17px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .dash-header-title p {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 3px;
            letter-spacing: 0.2px;
        }

        .dash-header-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* Live badge */
        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 14px;
            border-radius: 100px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            font-size: 12px;
            font-weight: 600;
            color: #34d399;
            letter-spacing: 0.3px;
        }

        .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            animation: livePulse 2s ease-in-out infinite;
        }

        @keyframes livePulse {
            0%, 100% { opacity: 1; transform: scale(1);   box-shadow: 0 0 0 0 rgba(16,185,129,0.5); }
            50%       { opacity: 0.8; transform: scale(1.15); box-shadow: 0 0 0 4px rgba(16,185,129,0); }
        }

        /* Date/time pill */
        .datetime-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 100px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            font-size: 12px;
            font-weight: 500;
            color: #cbd5e1;
        }

        /* Page breadcrumb strip */
        .breadcrumb-strip {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 32px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: #64748b;
        }

        .breadcrumb-strip a { color: #6366f1; text-decoration: none; font-weight: 500; }
        .breadcrumb-strip a:hover { text-decoration: underline; }
        .breadcrumb-strip .sep { color: #cbd5e1; }

        /* ══ PAGE BODY ════════════════════════════════════════ */
        .page-body {
            padding: 28px 32px 40px;
            max-width: 1700px;
            margin: 0 auto;
        }

        /* ══ SHARED CARD ══════════════════════════════════════ */
        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 1px 8px rgba(0,0,0,0.05);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 40px rgba(0,0,0,0.10);
        }

        .chart-container {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        .chart-container .chart-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 12px;
        }

        .chart-container .chart-header h3 {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 3px;
        }

        .chart-container .chart-header p {
            font-size: 12px;
            color: #94a3b8;
        }

        /* ══ Custom scrollbar ══════════════════════════════════ */
        ::-webkit-scrollbar          { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track    { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb    { background: #6366f1; border-radius: 10px; }

        /* ══ Responsive ════════════════════════════════════════ */
        @media (max-width: 768px) {
            .dash-header            { padding: 0 16px; }
            .breadcrumb-strip       { padding: 10px 16px; display: none; }
            .page-body              { padding: 16px; }
            .dash-header-divider    { display: none; }
            .dash-header-title h1   { font-size: 14px; }
            .datetime-pill          { display: none; }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @stack('head_scripts')
</head>

<body>

    <!-- ══ HEADER ═══════════════════════════════════════════ -->
    <header class="dash-header">
        <div class="dash-header-left">
            <img src="{{ asset('images/home/biswo_logo.png') }}"
                 class="dash-header-logo"
                 alt="{{ config('jblbConf.headLine') }} Logo">

            <div class="dash-header-divider"></div>

            <div class="dash-header-title">
                <h1>{{ config('jblbConf.title') }}</h1>
                <p>{{ config('jblbConf.indexName') }}</p>
            </div>
        </div>

        <div class="dash-header-right">
            <span class="datetime-pill" id="headerDatetime">
                <i class="fas fa-clock" style="font-size:11px;opacity:.7"></i>
                <span id="headerDatetimeText">—</span>
            </span>
            <span class="live-badge">
                <span class="live-dot"></span>
                Live Portal
            </span>
        </div>
    </header>

    <!-- ══ BREADCRUMB ════════════════════════════════════════ -->
    <div class="breadcrumb-strip">
        <a href="{{ route('/') }}"><i class="fas fa-home" style="margin-right:4px"></i>Home</a>
        <span class="sep">›</span>
        <span style="color:#1e293b;font-weight:600">Dashboard</span>
        @yield('breadcrumb')
    </div>

    <!-- ══ PAGE CONTENT ══════════════════════════════════════ -->
    <main class="page-body">
        @yield('content')
    </main>

    <!-- Datetime ticker -->
    <script>
        (function () {
            function tick() {
                var el = document.getElementById('headerDatetimeText');
                if (el) {
                    el.textContent = new Date().toLocaleString('en-IN', {
                        day: '2-digit', month: 'short', year: 'numeric',
                        hour: '2-digit', minute: '2-digit', second: '2-digit',
                        hour12: true
                    });
                }
            }
            tick();
            setInterval(tick, 1000);
        })();
    </script>

    @stack('scripts')
</body>

</html>