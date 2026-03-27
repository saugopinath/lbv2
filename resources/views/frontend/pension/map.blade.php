@extends('frontend.layouts.app-template')

@push('meta')
    <meta name="map-district-count-url" content="{{ route('map.district.count') }}">
@endpush

@push('styles')
    <style>
        /* SVG Map Styles */
        .district {
            fill: #f8fafc;
            stroke: #6366f1;
            stroke-width: 0.8;
            cursor: pointer;
            transition: all .3s ease-in-out;
        }

        .district:hover {
            fill: #c7d2fe !important;
            stroke: #4338ca;
            stroke-width: 1.5;
        }

        .district.selected {
            fill: #4f46e5 !important;
            stroke: #1e1b4b;
            stroke-width: 2;
        }

        .tooltip {
            position: fixed;
            background: rgba(15, 23, 42, 0.95);
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            pointer-events: none;
            display: none;
            z-index: 1000;
            box-shadow: 0 4px 6px rgb(0 0 0 / 10%);
            transform: translate(12px, -12px);
            user-select: none;
        }

        .loading-spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #4f46e5;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@section('content')

    @include('frontend.components.top-header')
    @include('frontend.components.header')

    <div class="bg-gray-50 min-h-screen py-10 px-4">
        <div class="max-w-7xl mx-auto">

            <!-- PAGE HEADER -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900">
                        West Bengal <span class="text-indigo-600">District Map</span>
                    </h1>
                    <p class="text-gray-500 mt-1 font-medium">
                        Interactive Pension Beneficiary Distribution
                    </p>
                </div>

                <button id="reset-btn"
                    class="flex items-center gap-2 px-6 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:text-indigo-600 transition shadow-sm font-semibold">
                    <i class="fa-solid fa-arrows-rotate"></i> Reset Map
                </button>
            </div>

            <!-- ================= GRID LAYOUT ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-3 lg:grid-rows-[auto_650px_auto] gap-8" style="
                                                                                                                grid-template-areas:
                                                                                                                    'cards cards cards'
                                                                                                                    'map map info'
                                                                                                                    'full full full';
                                                                                                            ">

                <!-- ================= STATS CARDS ================= -->
                <div style="grid-area: cards;">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-lg">
                            <p class="text-indigo-100 text-sm uppercase font-semibold">
                                Total Beneficiaries
                            </p>
                            <h3 class="text-3xl font-bold mt-2" id="total-count">0</h3>
                        </div>

                        <div class="bg-white rounded-2xl p-6 border shadow-sm">
                            <p class="text-gray-500 text-sm uppercase font-semibold">
                                Total Districts
                            </p>
                            <h3 class="text-3xl font-bold mt-2" id="district-count">{{ $districtCount }}</h3>
                        </div>

                        <div class="bg-white rounded-2xl p-6 border shadow-sm">
                            <p class="text-gray-500 text-sm uppercase font-semibold">
                                Highest District
                            </p>
                            <h3 class="text-xl font-bold mt-3 truncate" id="highest-district">-</h3>
                        </div>

                        <div class="bg-white rounded-2xl p-6 border shadow-sm">
                            <p class="text-gray-500 text-sm uppercase font-semibold">
                                Avg / District
                            </p>
                            <h3 class="text-3xl font-bold mt-2" id="avg-count">0</h3>
                        </div>
                    </div>
                </div>

                <!-- ================= MAP SECTION ================= -->
                <div style="grid-area: map;">
                    <div class="bg-white rounded-3xl shadow-sm border p-4 h-[650px] relative flex flex-col">

                        <div class="flex justify-between items-center mb-4 px-2">
                            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                                <i class="fa-solid fa-map-location-dot text-indigo-600"></i>
                                Geographic Distribution
                            </h2>
                            <span class="text-xs font-bold text-gray-400 uppercase">
                                SVG Interactive
                            </span>
                        </div>

                        <!-- LOADER -->
                        <div id="loading" class="flex-1 flex flex-col items-center justify-center">
                            <div class="loading-spinner mb-4"></div>
                            <span class="text-gray-400 font-medium animate-pulse">
                                Fetching Data...
                            </span>
                        </div>

                        <!-- SVG -->
                        <div id="map-svg-wrapper" class="flex-1 hidden items-center justify-center overflow-hidden">
                            @include('frontend.maps.west_bengal')
                        </div>

                        <!-- TOOLTIP -->
                        <div id="custom-tooltip" class="tooltip">
                            <div id="tooltip-content"></div>
                        </div>
                    </div>
                </div>

                <!-- ================= DISTRICT INFO ================= -->
                <div style="grid-area: info;">
                    <div class="bg-white rounded-3xl shadow-sm border h-[650px] flex flex-col overflow-hidden">

                        <div class="p-6 border-b bg-gray-50">
                            <h3 class="text-lg font-bold flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-indigo-600"></i>
                                District Breakdown
                            </h3>
                        </div>

                        <div id="district-info" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                            <div class="p-8 bg-gray-50 rounded-full mb-4">
                                <i class="fa-solid fa-hand-pointer text-4xl text-gray-300"></i>
                            </div>
                            <h4 class="font-bold text-lg">No Selection</h4>
                            <p class="text-gray-500 mt-2 max-w-xs">
                                Click a district on the map to view details
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ================= FULL DATA (OPTIONAL) ================= -->
                <div style="grid-area: full;">
                    <div class="bg-white rounded-3xl shadow-sm border p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">
                            District Full Data
                        </h3>
                        <p class="text-gray-500 text-sm">
                            Reserved for charts / tables / future expansion
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('frontend.layouts.footer')
@endsection

@push('scripts')
    <script>



        document.addEventListener('DOMContentLoaded', function () {
            let districtData = {};

            async function initMap() {
                try {
                    const response = await fetch(document.querySelector('meta[name="map-district-count-url"]').content, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                document.querySelectorAll('.district').forEach(function (d) {
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
                let c = '#e0e7ff';
                if (count > 500) c = '#3730a3';
                else if (count > 200) c = '#4f46e5';
                else if (count > 50) c = '#818cf8';
                else if (count > 0) c = '#c7d2fe';
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
                                    <div class="w-full">
                                        <div class="text-center mb-8">
                                            <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest">District Selected</span>
                                            <h4 class="text-3xl font-black text-gray-900 mt-4">${name}</h4>
                                            <div class="w-12 h-1 bg-indigo-500 mx-auto mt-4 rounded-full"></div>
                                        </div>
                                        <div class="space-y-4">
                                            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                                                <p class="text-gray-500 text-xs font-bold uppercase mb-1">Total Beneficiaries</p>
                                                <p class="text-4xl font-black text-indigo-600">${count.toLocaleString()}</p>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-left">
                                                    <p class="text-gray-500 text-[10px] font-bold uppercase">State Share</p>
                                                    <p class="text-xl font-bold text-gray-800">${pct}%</p>
                                                </div>
                                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-left">
                                                    <p class="text-gray-500 text-[10px] font-bold uppercase">Status</p>
                                                    <p class="text-xl font-bold text-green-600 truncate">Active</p>
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

                let highest = { name: '-', count: 0 };
                document.querySelectorAll('.district').forEach(function (el) {
                    const c = parseInt(el.dataset.count || 0);
                    if (c > highest.count) {
                        highest = { name: el.dataset.name, count: c };
                    }
                });

                document.getElementById('total-count').textContent = t.toLocaleString();
                document.getElementById('avg-count').textContent = avg.toLocaleString();
                document.getElementById('highest-district').textContent = highest.name;
            }

            function total() {
                return Object.values(districtData).reduce((a, b) => a + (parseInt(b) || 0), 0);
            }

            function showTooltip(e, name, count) {
                document.getElementById('tooltip-content').innerHTML = `
                                <div class="font-bold border-b border-gray-700 pb-1 mb-1">${name}</div>
                                <div class="text-indigo-400">
                                    Beneficiaries: <span class="text-white">${count.toLocaleString()}</span>
                                </div>
                            `;
                document.getElementById('custom-tooltip').style.display = 'block';
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

            document.getElementById('reset-btn').addEventListener('click', () => {
                document.querySelectorAll('.district').forEach(el => el.classList.remove('selected'));
                document.getElementById('district-info').innerHTML = `
                                <div class="p-8 bg-gray-50 rounded-full mb-4">
                                    <i class="fa-solid fa-hand-pointer text-4xl text-gray-300"></i>
                                </div>
                                <h4 class="text-gray-800 font-bold text-lg">No Selection</h4>
                                <p class="text-gray-500 max-w-xs mt-2">
                                    Please click on a district within the map to view specific beneficiary statistics.
                                </p>
                            `;
            });

            initMap();
        });
    </script>
@endpush