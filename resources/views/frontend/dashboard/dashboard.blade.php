@extends('frontend.layouts.dashboard')
@section('title', '| Dashboard')

@php
    $pending      = max(0, $totalApplied - $totalApproved);
    $approvalRate = $totalApplied > 0 ? round(($totalApproved / $totalApplied) * 100, 1) : 0;
@endphp

@push('styles')
<style>
    .kpi-grid   { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 20px; margin-bottom: 28px; }
    .chart-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 22px; margin-bottom: 28px; }
    @media (max-width: 1100px) { .chart-grid { grid-template-columns: 1fr; } }

    /* KPI Card */
    .kpi-card {
        background: #fff;
        border-radius: 18px;
        padding: 22px 20px 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 8px rgba(0,0,0,.05);
        transition: transform .25s ease, box-shadow .25s ease;
        display: flex; flex-direction: column; gap: 12px;
    }
    .kpi-card:hover { transform: translateY(-5px); box-shadow: 0 18px 40px rgba(0,0,0,.10); }
    .kpi-icon {
        width: 50px; height: 50px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: #fff;
        box-shadow: 0 6px 16px rgba(0,0,0,.15);
    }
    .kpi-label { font-size: 12px; color: #64748b; font-weight: 500; letter-spacing: .3px; }
    .kpi-value { font-size: 2.1rem; font-weight: 800; color: #0f172a; line-height: 1; }
    .kpi-tag   {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 600;
    }

    /* Chart container */
    .chart-box {
        background: #fff; border-radius: 16px;
        padding: 22px; border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
    }
    .chart-box-head { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; gap:10px; }
    .chart-box-head h3 { font-size:14.5px; font-weight:700; color:#1e293b; margin-bottom:3px; }
    .chart-box-head p  { font-size:12px; color:#94a3b8; }

    /* Status Table */
    .status-table-wrap { background:#fff; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.06); }
    .status-table-head { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid #f1f5f9; }
    .status-table-head h3 { font-size:15px; font-weight:700; color:#1e293b; }
    .status-table-head p  { font-size:12px; color:#94a3b8; margin-top:3px; }
    table.s-table { width:100%; border-collapse:collapse; font-size:13px; }
    table.s-table thead tr { background:#f8fafc; }
    table.s-table th { padding:11px 16px; font-weight:600; color:#475569; text-align:left; white-space:nowrap; }
    table.s-table th:not(:first-child) { text-align:right; }
    table.s-table td { padding:11px 16px; color:#374151; border-top:1px solid #f1f5f9; }
    table.s-table td:not(:first-child) { text-align:right; }
    table.s-table tbody tr:hover { background:#fafbff; }
    .badge-green { background:#dcfce7; color:#15803d; border-radius:6px; padding:2px 8px; font-weight:600; font-size:12px; }
    .badge-red   { background:#fee2e2; color:#dc2626; border-radius:6px; padding:2px 8px; font-weight:600; font-size:12px; }

    .btn-refresh {
        display:inline-flex; align-items:center; gap:6px;
        padding:7px 16px; border-radius:9px; font-size:13px; font-weight:600;
        background:#4f46e5; color:#fff; border:none; cursor:pointer;
        transition:background .2s;
    }
    .btn-refresh:hover { background:#4338ca; }
    .btn-refresh:disabled { opacity:.5; cursor:not-allowed; }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════
     KPI STAT CARDS  (6 cards)
════════════════════════════════════════ --}}
<div class="kpi-grid">

    {{-- Total Approved --}}
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669)">
            <i class="fas fa-check-double"></i>
        </div>
        <div>
            <p class="kpi-label">Total Approved</p>
            <div class="kpi-value stat-number" data-value="{{ $totalApproved }}">0</div>
        </div>
        <span class="kpi-tag" style="background:#dcfce7;color:#15803d">
            <i class="fas fa-shield-check" style="font-size:10px"></i> Till Date
        </span>
    </div>

    {{-- Total Applied --}}
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb)">
            <i class="fas fa-file-alt"></i>
        </div>
        <div>
            <p class="kpi-label">Total Applications</p>
            <div class="kpi-value stat-number" data-value="{{ $totalApplied }}">0</div>
        </div>
        <span class="kpi-tag" style="background:#dbeafe;color:#1d4ed8">
            <i class="fas fa-layer-group" style="font-size:10px"></i> Cumulative
        </span>
    </div>

    {{-- Pending --}}
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div>
            <p class="kpi-label">Pending / In-Process</p>
            <div class="kpi-value stat-number" data-value="{{ $pending }}">0</div>
        </div>
        <span class="kpi-tag" style="background:#fef9c3;color:#a16207">
            <i class="fas fa-clock" style="font-size:10px"></i> Awaiting action
        </span>
    </div>

    {{-- Approval Rate --}}
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9)">
            <i class="fas fa-percent"></i>
        </div>
        <div>
            <p class="kpi-label">Approval Rate</p>
            <div class="kpi-value" style="color:#7c3aed">{{ $approvalRate }}%</div>
        </div>
        <span class="kpi-tag" style="background:#ede9fe;color:#5b21b6">
            <i class="fas fa-chart-pie" style="font-size:10px"></i> Approved vs Applied
        </span>
    </div>

    {{-- DBT Current Month --}}
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,#f97316,#ea580c)">
            <i class="fas fa-rupee-sign"></i>
        </div>
        <div>
            <p class="kpi-label">DBT Transfer (This Month)</p>
            <div class="kpi-value stat-number" data-value="{{ $totalPayCurMonth }}" data-money="true">₹ 0</div>
        </div>
        <span class="kpi-tag" style="background:#ffedd5;color:#c2410c">
            <span style="width:7px;height:7px;border-radius:50%;background:#f97316;animation:livePulse 2s infinite;display:inline-block"></span>
            Current Month
        </span>
    </div>

    {{-- DBT Current FY --}}
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,#ec4899,#be185d)">
            <i class="fas fa-chart-line"></i>
        </div>
        <div>
            <p class="kpi-label">DBT Transfer (FY {{ $cur_fin_year }})</p>
            <div class="kpi-value stat-number" data-value="{{ $totalPayCurYear }}" data-money="true">₹ 0</div>
        </div>
        <span class="kpi-tag" style="background:#fce7f3;color:#9d174d">
            <i class="fas fa-calendar-check" style="font-size:10px"></i> Full Year
        </span>
    </div>

</div>

{{-- ═══════════════════════════════════════
     CHART ROW 1 — Applications & District
════════════════════════════════════════ --}}
<div class="chart-grid">
    @if(config('jblbConf.is_jb'))
        <div class="chart-box">
            <div class="chart-box-head">
                <div>
                    <h3>Scheme-wise Applications</h3>
                    <p>Approved beneficiaries per scheme</p>
                </div>
                <select id="schemeFilter" style="padding:6px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:12px;color:#374151">
                    <option value="all">All Time</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                </select>
            </div>
            <div id="applicationsChart" style="height:320px"></div>
        </div>
    @else
        <div class="chart-box">
            <div class="chart-box-head">
                <div>
                    <h3>Daily Application Trend</h3>
                    <p>Applications submitted in the last 30 days</p>
                </div>
                <span style="font-size:11px;color:#0ea5e9;background:#e0f2fe;padding:4px 10px;border-radius:100px;font-weight:600">
                    <i class="fas fa-chart-line"></i> Daily
                </span>
            </div>
            <div id="dailyChart" style="height:320px"></div>
        </div>
    @endif

    <div class="chart-box">
        <div class="chart-box-head">
            <div>
                <h3>District-wise Distribution</h3>
                <p>Approved beneficiaries by district</p>
            </div>
            <span style="font-size:11px;color:#94a3b8"><i class="fas fa-map-marker-alt"></i> Top 50</span>
        </div>
        <div style="height:320px;overflow-y:auto">
            <div id="districtChart"></div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     CHART ROW 2 — Monthly DBT + Age Cohort
════════════════════════════════════════ --}}
<div class="chart-grid">
    <div class="chart-box">
        <div class="chart-box-head">
            <div>
                <h3>Monthly DBT Payment Trend</h3>
                <p>FY {{ $cur_fin_year }} — all months</p>
            </div>
            <span style="font-size:11px;color:#6366f1;background:#eef2ff;padding:4px 10px;border-radius:100px;font-weight:600">
                <i class="fas fa-chart-area"></i> Area Chart
            </span>
        </div>
        <div id="trendsChart" style="height:320px"></div>
    </div>

    <div class="chart-box">
        <div class="chart-box-head">
            <div>
                <h3>Age Cohort Distribution</h3>
                <p>Beneficiaries grouped by age band</p>
            </div>
            <span style="font-size:11px;color:#94a3b8">5 bands</span>
        </div>
        <div id="categoryChart" style="height:320px"></div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     CHART ROW 3 — Demographics & Status
════════════════════════════════════════ --}}
<div class="chart-grid">
    <div class="chart-box">
        <div class="chart-box-head">
            <div>
                <h3>Gender Distribution</h3>
                <p>Overall applicant breakdown by gender</p>
            </div>
        </div>
        <div id="genderChart" style="height:360px"></div>
    </div>

    <div class="chart-box">
        <div class="chart-box-head">
            <div>
                <h3>Caste / Category Distribution</h3>
                <p>Applicants grouped by social category</p>
            </div>
        </div>
        <div id="casteChart" style="height:360px"></div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     CHART ROW 4 — Scheme Status & Approval
════════════════════════════════════════ --}}
<div class="chart-grid">
    @if(config('jblbConf.is_jb'))
        <div class="chart-box">
            <div class="chart-box-head">
                <div>
                    <h3>Scheme-wise Workflow Breakdown</h3>
                    <p>Entry · Verified · Approved · Rejected per scheme</p>
                </div>
            </div>
            <div id="schemeStackedChart" style="height:360px"></div>
        </div>
    @endif

    <div class="chart-box" @if(!config('jblbConf.is_jb')) style="grid-column: 1 / -1; max-width: 600px; margin: 0 auto; width: 100%;" @endif>
        <div class="chart-box-head">
            <div>
                <h3>Overall Approval Gauge</h3>
                <p>Percentage of applications that reached final approval</p>
            </div>
        </div>
        <div id="approvalGauge" style="height:360px"></div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     SCHEME STATUS TABLE
════════════════════════════════════════ --}}
<div class="status-table-wrap">
    <div class="status-table-head">
        <div>
            <h3>Scheme-wise Consolidated Status</h3>
            <p>Workflow status across all active schemes</p>
        </div>
        <div style="display:flex;align-items:center;gap:12px">
            <span id="lastRefreshed" style="font-size:11px;color:#94a3b8"></span>
            <button id="refreshSchemeStatus" class="btn-refresh">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table class="s-table">
            <thead>
                <tr>
                    <th>Scheme</th>
                    <th>Entry</th>
                    <th>Verified</th>
                    <th>Approved</th>
                    <th>Recommended</th>
                    <th>Rejected</th>
                </tr>
            </thead>
            <tbody id="schemeStatusTbody">
                <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:24px">Loading data…</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ── helpers ───────────────────────────────────────── */
function fmtIN(num) { return Number(num).toLocaleString('en-IN'); }
function indianSuffix(num) {
    if (num >= 1e7) return { v: num / 1e7, s: ' Cr' };
    if (num >= 1e5) return { v: num / 1e5, s: ' Lakh' };
    return { v: num, s: '' };
}
function animateCounter(el, dur) {
    dur = dur || 1500;
    var target  = parseFloat(el.dataset.value) || 0;
    var isMoney = el.dataset.money === 'true';
    var sd      = indianSuffix(target);
    var start   = null;
    (function step(ts) {
        if (!start) start = ts;
        var prog    = Math.min((ts - start) / dur, 1);
        var current = sd.v * prog;
        var display = sd.s ? current.toFixed(2) : Math.floor(current);
        el.textContent = (isMoney ? '₹ ' : '') + fmtIN(display) + sd.s;
        if (prog < 1) requestAnimationFrame(step);
    })(performance.now());
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.stat-number').forEach(function (el) { animateCounter(el); });

    /* Scheme status table */
    var lastRefEl = document.getElementById('lastRefreshed');
    if (lastRefEl) lastRefEl.textContent = 'Last refreshed: ' + new Date().toLocaleString('en-IN');

    document.getElementById('refreshSchemeStatus').addEventListener('click', function () {
        var btn = this; btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing…';
        fetch("{{ route('dashboard.refreshSchemeStatus') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        }).then(function () {
            loadTable(); loadSchemeStacked();
        }).catch(function () {
            alert('Failed to refresh');
        }).finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
        });
    });

    loadTable();
    function loadTable() {
        fetch("{{ route('dashboard.schemeStatusChart') }}", { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (rows) {
                var html = '';
                rows.forEach(function (row) {
                    var sid  = parseInt(row.scheme_id);
                    var entry= parseInt(row.entry_count) || 0;
                    var ver  = 0, appr = 0, rec = 0, rej = 0;
                    if ([8,9].includes(sid)) {
                        appr = parseInt(row.approved_count) || 0;
                    } else {
                        ver  = parseInt(row.verified_count)   || 0;
                        appr = parseInt(row.approved_count)   || 0;
                        rec  = parseInt(row.recomended_count) || 0;
                        rej  = parseInt(row.rejected_count)   || 0;
                    }
                    html += '<tr><td><strong>' + row.scheme_name + '</strong></td>'
                          + '<td>' + fmtIN(entry) + '</td>'
                          + '<td>' + fmtIN(ver) + '</td>'
                          + '<td><span class="badge-green">' + fmtIN(appr) + '</span></td>'
                          + '<td>' + fmtIN(rec) + '</td>'
                          + '<td><span class="badge-red">' + fmtIN(rej) + '</span></td></tr>';
                });
                document.getElementById('schemeStatusTbody').innerHTML = html || '<tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:24px">No data</td></tr>';
            }).catch(function () {
                document.getElementById('schemeStatusTbody').innerHTML = '<tr><td colspan="6" style="text-align:center;color:#ef4444;padding:24px">Failed to load</td></tr>';
            });
    }
});

/* ── Highcharts charts ─────────────────────────────── */
function waitForHighcharts(cb) {
    if (typeof window.Highcharts !== 'undefined') { cb(); }
    else { setTimeout(function () { waitForHighcharts(cb); }, 50); }
}

waitForHighcharts(function () {

    /* palette */
    var COLORS = ['#6366f1','#10b981','#f97316','#ec4899','#14b8a6','#a855f7','#84cc16','#0ea5e9','#f59e0b'];
    var HC = Highcharts;

    /* Shared chart defaults */
    HC.setOptions({
        chart: { style: { fontFamily: "'Inter', sans-serif" } },
        credits: { enabled: false },
        title: { text: null }
    });

    /* 1 ── Scheme-wise Applications (column) */
    function loadSchemeApps(days) {
        fetch("{{ route('dashboard.schemeWiseApplications') }}?days=" + days, { headers: { Accept: 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                HC.chart('applicationsChart', {
                    chart: { type: 'column', backgroundColor: 'transparent', animation: { duration: 700 } },
                    xAxis: { categories: res.categories, labels: { rotation: -35, style: { fontSize: '11px' } } },
                    yAxis: { title: { text: 'Applications' }, gridLineColor: '#f1f5f9' },
                    legend: { enabled: false },
                    plotOptions: { column: { borderRadius: 8, dataLabels: { enabled: true, style: { fontSize: '11px' } } } },
                    series: [{ name: 'Applications', data: res.data, colorByPoint: true, colors: COLORS }]
                });
            });
    }
    loadSchemeApps('all');
    document.getElementById('schemeFilter').addEventListener('change', function () { loadSchemeApps(this.value); });

    /* 2 ── District-wise (horizontal bar) */
    fetch("{{ route('dashboard.districtWiseBeneficiaries') }}", { headers: { Accept: 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            var h = Math.max(res.categories.length * 44, 350);
            HC.chart('districtChart', {
                chart: { type: 'bar', height: h, backgroundColor: 'transparent', animation: { duration: 800 } },
                xAxis: { categories: res.categories, title: { text: null } },
                yAxis: { title: { text: 'Beneficiaries' }, gridLineColor: '#f1f5f9' },
                legend: { enabled: false },
                plotOptions: { bar: { borderRadius: 6, dataLabels: { enabled: true } } },
                series: [{ name: 'Beneficiaries', data: res.data, color: { linearGradient: { x1:0,y1:0,x2:1,y2:0 }, stops: [[0,'#6366f1'],[1,'#a5b4fc']] } }]
            });
        });

    /* 3 ── Monthly DBT payment (area-spline) */
    fetch("{{ route('dashboard.fy.consolidated') }}?fin_year={{ $cur_fin_year }}", { headers: { Accept: 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.status !== 'success') return;
            HC.chart('trendsChart', {
                chart: { type: 'areaspline', backgroundColor: 'transparent', animation: { duration: 900 } },
                xAxis: { categories: ['Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar'] },
                yAxis: { title: { text: 'Payment (₹)' }, gridLineColor: '#f1f5f9' },
                legend: { enabled: false },
                tooltip: { valuePrefix: '₹ ', valueDecimals: 0 },
                plotOptions: {
                    areaspline: {
                        lineWidth: 3, marker: { enabled: true, radius: 4 },
                        fillColor: { linearGradient: { x1:0,y1:0,x2:0,y2:1 }, stops: [[0,'rgba(99,102,241,.35)'],[1,'rgba(99,102,241,.03)']] }
                    }
                },
                series: [{ name: 'DBT Payment', data: res.series, color: '#6366f1' }]
            });
        });

    /* 4 ── Age Cohort (donut pie) */
    fetch("{{ route('dashboard.ageDistribution') }}", { headers: { Accept: 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (d) {
            HC.chart('categoryChart', {
                chart: { type: 'pie', backgroundColor: 'transparent', animation: { duration: 800 } },
                tooltip: { pointFormat: '<b>{point.y}</b> beneficiaries ({point.percentage:.1f}%)' },
                plotOptions: { pie: { innerSize: '62%', borderWidth: 2, dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.y}', style: { fontSize: '11px', fontWeight: '600' } } } },
                series: [{
                    name: 'Beneficiaries',
                    data: [
                        { name: '0–18',  y: +d.age_0_18,   color: '#38bdf8' },
                        { name: '18–30', y: +d.age_18_30,  color: '#6366f1' },
                        { name: '30–45', y: +d.age_30_45,  color: '#22c55e' },
                        { name: '45–60', y: +d.age_45_60,  color: '#f97316' },
                        { name: '60+',   y: +d.age_60_plus,color: '#ef4444' }
                    ]
                }]
            });
        });

    /* 5 ── Scheme Status Stacked Column (reuses schemeStatusChart endpoint) */
    function loadSchemeStacked() {
        if (!document.getElementById('schemeStackedChart')) return;
        fetch("{{ route('dashboard.schemeStatusChart') }}", { headers: { Accept: 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (rows) {
                var cats   = [], entry = [], verified = [], approved = [], rejected = [];
                rows.forEach(function (row) {
                    cats.push(row.scheme_name);
                    entry.push(parseInt(row.entry_count) || 0);
                    verified.push(parseInt(row.verified_count) || 0);
                    approved.push(parseInt(row.approved_count) || 0);
                    rejected.push(parseInt(row.rejected_count) || 0);
                });
                HC.chart('schemeStackedChart', {
                    chart: { type: 'column', backgroundColor: 'transparent', animation: { duration: 800 } },
                    xAxis: { categories: cats, labels: { rotation: -35, style: { fontSize: '11px' } } },
                    yAxis: { stackLabels: { enabled: true }, title: { text: 'Count' }, gridLineColor: '#f1f5f9' },
                    legend: { enabled: true },
                    tooltip: { shared: true },
                    plotOptions: { column: { stacking: 'normal', borderRadius: 5, dataLabels: { enabled: false } } },
                    series: [
                        { name: 'Entry',    data: entry,    color: '#e0e7ff' },
                        { name: 'Verified', data: verified, color: '#a5b4fc' },
                        { name: 'Approved', data: approved, color: '#6366f1' },
                        { name: 'Rejected', data: rejected, color: '#f87171' }
                    ]
                });
            });
    }
    loadSchemeStacked();
    window._loadSchemeStacked = loadSchemeStacked; /* expose for refresh button */

    /* 6 ── Approval Rate Solid Gauge */
    HC.chart('approvalGauge', {
        chart: { type: 'solidgauge', backgroundColor: 'transparent', animation: { duration: 1000 } },
        pane: {
            center: ['50%','65%'], size: '90%', startAngle: -90, endAngle: 90,
            background: [{ backgroundColor: '#eef2ff', innerRadius: '60%', outerRadius: '100%', borderWidth: 0 }]
        },
        yAxis: { min: 0, max: 100, stops: [[0.33,'#fbbf24'],[0.66,'#6366f1'],[1,'#10b981']], lineWidth: 0, tickWidth: 0, minorTickInterval: null, labels: { y: 20, style: { fontSize: '12px', fontWeight:'600', color: '#374151' } } },
        plotOptions: { solidgauge: { innerRadius: '60%', dataLabels: { y: -40, borderWidth: 0, useHTML: true } } },
        tooltip: { enabled: false },
        series: [{
            name: 'Approval Rate',
            data: [{{ $approvalRate }}],
            dataLabels: {
                format: '<div style="text-align:center">'
                      + '<span style="font-size:3rem;font-weight:900;color:#1e293b">{{ $approvalRate }}%</span><br>'
                      + '<span style="font-size:13px;color:#64748b;font-weight:500">Approval Rate</span>'
                      + '</div>'
            }
        }]
    });

    /* 7 ── Gender Distribution (Pie Chart) */
    fetch("{{ route('dashboard.genderDistribution') }}", { headers: { Accept: 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var chartData = data.map(function(d) {
                var c = '#94a3b8';
                if (d.name.toLowerCase() === 'male' || d.name.toLowerCase() === 'm') c = '#3b82f6';
                if (d.name.toLowerCase() === 'female' || d.name.toLowerCase() === 'f') c = '#ec4899';
                if (d.name.toLowerCase() === 'others') c = '#8b5cf6';
                return { name: d.name, y: parseInt(d.y), color: c };
            });
            HC.chart('genderChart', {
                chart: { type: 'pie', backgroundColor: 'transparent', animation: { duration: 800 } },
                tooltip: { pointFormat: '<b>{point.y}</b> ({point.percentage:.1f}%)' },
                plotOptions: { pie: { innerSize: '40%', borderWidth: 2, dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f} %', style: { fontSize: '11px', fontWeight: '600' } } } },
                series: [{ name: 'Gender', data: chartData }]
            });
        });

    /* 8 ── Caste Distribution (Column) */
    fetch("{{ route('dashboard.casteDistribution') }}", { headers: { Accept: 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var categories = data.map(function(d) { return d.name; });
            var seriesData = data.map(function(d) { return parseInt(d.y); });
            HC.chart('casteChart', {
                chart: { type: 'column', backgroundColor: 'transparent', animation: { duration: 700 } },
                xAxis: { categories: categories, labels: { style: { fontSize: '12px' } } },
                yAxis: { title: { text: 'Applicants' }, gridLineColor: '#f1f5f9' },
                legend: { enabled: false },
                plotOptions: { column: { borderRadius: 6, dataLabels: { enabled: true, style: { fontSize: '11px' } } } },
                series: [{ name: 'Applicants', data: seriesData, colorByPoint: true, colors: ['#f59e0b', '#10b981', '#3b82f6', '#8b5cf6'] }]
            });
        });

    /* 9 ── Daily Application Trends (Line) */
    if (document.getElementById('dailyChart')) {
        fetch("{{ route('dashboard.dailyApplications') }}", { headers: { Accept: 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                HC.chart('dailyChart', {
                    chart: { type: 'areaspline', backgroundColor: 'transparent', animation: { duration: 800 } },
                    xAxis: { categories: res.categories, labels: { rotation: -45, style: { fontSize: '10px' }, step: 3 } },
                    yAxis: { title: { text: 'Applications' }, gridLineColor: '#f1f5f9' },
                    legend: { enabled: false },
                    tooltip: { valueSuffix: ' applications' },
                    plotOptions: {
                        areaspline: {
                            lineWidth: 3, marker: { enabled: false },
                            fillColor: { linearGradient: { x1:0,y1:0,x2:0,y2:1 }, stops: [[0,'rgba(14,165,233,.35)'],[1,'rgba(14,165,233,.03)']] }
                        }
                    },
                    series: [{ name: 'Daily Trends', data: res.data, color: '#0ea5e9' }]
                });
            });
    }

});
</script>
@endpush