@extends('frontend.layouts.app-template')

@push('meta')
<meta name="map-district-count-url" content="{{ route('map.district.count') }}">
@endpush

@push('styles')
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

@include('frontend.components.top-header')
@include('frontend.components.header')

<div class="relative min-h-screen py-10 px-4 overflow-hidden bg-gradient-to-br from-blue-50 via-indigo-50/50 to-purple-50">
    <!-- Animated Background Blobs -->
    <div class="absolute top-0 -left-4 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-[80px] opacity-40 animate-blob pointer-events-none"></div>
    <div class="absolute top-0 -right-4 w-96 h-96 bg-indigo-300 rounded-full mix-blend-multiply filter blur-[80px] opacity-40 animate-blob animation-delay-2000 pointer-events-none"></div>
    <div class="absolute -bottom-8 left-40 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-[80px] opacity-40 animate-blob animation-delay-4000 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto relative z-10">

        <!-- PAGE HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600"> West Bengal District Map</span>
                </h1>
                <p class="text-gray-500 mt-2 font-medium text-lg">
                    Interactive Pension Beneficiary Distribution
                </p>
            </div>

            <button id="reset-btn"
                class="flex items-center gap-2 px-6 py-2.5 glass-card text-gray-700 rounded-xl hover:bg-white/90 hover:text-indigo-600 hover:shadow-md transition-all font-bold">
                <i class="fa-solid fa-arrows-rotate"></i> Reset Map
            </button>
        </div>

        <!-- ================= GRID LAYOUT ================= -->
        <div class="grid grid-cols-1 lg:grid-cols-3 lg:grid-rows-[auto_650px_auto] gap-8" style="
            grid-template-areas:                                                                                                                    
            'cards cards cards'
            'map map info'
            'full full full';">
            <!-- ================= STATS CARDS ================= -->
            <div style="grid-area: cards;">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                    <!-- Card 1: Total Beneficiaries -->
                    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-2xl p-6 text-white shadow-xl shadow-indigo-200/50 relative overflow-hidden group border border-indigo-400">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10 group-hover:scale-150 transition-transform duration-700"></div>
                        <p class="text-indigo-100 text-xs uppercase font-bold tracking-wider">Total Beneficiaries</p>
                        <h3 class="text-4xl font-black mt-2 tracking-tight" id="total-count">0</h3>
                    </div>

                    <!-- Card 2: Total Districts -->
                    <div class="bg-gradient-to-br from-sky-600 to-blue-800 rounded-2xl p-6 text-white shadow-xl shadow-blue-200/50 relative overflow-hidden group border border-sky-400">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10 group-hover:scale-150 transition-transform duration-700"></div>
                        <p class="text-sky-100 text-xs uppercase font-bold tracking-wider">Total Districts</p>
                        <h3 class="text-4xl font-black mt-2 tracking-tight" id="district-count">{{ $districtCount }}</h3>
                    </div>

                    <!-- Card 3: Highest Beneficiary -->
                    <div class="bg-gradient-to-br from-emerald-600 to-teal-800 rounded-2xl p-6 text-white shadow-xl shadow-emerald-200/50 relative overflow-hidden group border border-emerald-400">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10 group-hover:scale-150 transition-transform duration-700"></div>
                        <p class="text-emerald-100 text-xs uppercase font-bold tracking-wider">Max. Applied Zone</p>
                        <h3 class="text-2xl font-black mt-2 truncate tracking-tight" id="highest-district">-</h3>
                    </div>

                    <!-- Card 4: Avg / District -->
                    <div class="bg-gradient-to-br from-amber-600 to-orange-800 rounded-2xl p-6 text-white shadow-xl shadow-orange-200/50 relative overflow-hidden group border border-amber-400">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10 group-hover:scale-150 transition-transform duration-700"></div>
                        <p class="text-amber-100 text-xs uppercase font-bold tracking-wider">Avg / District</p>
                        <h3 class="text-4xl font-black mt-2 tracking-tight" id="avg-count">0</h3>
                    </div>

                </div>
            </div>

            <!-- ================= MAP SECTION ================= -->
            <div style="grid-area: map;">
                <div class="glass-card rounded-3xl p-5 h-[650px] relative flex flex-col">

                    <div class="flex justify-between items-center mb-4 px-2 pb-2 border-b border-gray-100/50">
                        <h2 class="font-bold text-gray-800 flex items-center gap-2 text-lg">
                            <i class="fa-solid fa-map-location-dot text-indigo-500"></i>
                            Geographic Distribution
                        </h2>
                        <div class="flex items-center gap-3">
                            <div class="flex bg-gray-100/50 rounded-lg p-1 border border-gray-200/50">
                                <button id="zoom-out" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Zoom Out"><i class="fa-solid fa-minus"></i></button>
                                <button id="zoom-reset" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Reset Map"><i class="fa-solid fa-expand"></i></button>
                                <button id="zoom-in" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Zoom In"><i class="fa-solid fa-plus"></i></button>
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

            <!-- ================= DISTRICT INFO ================= -->
            <div style="grid-area: info;">
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

            <!-- ================= FULL DATA (OPTIONAL) ================= -->
            <div style="grid-area: full;">
                <div class="glass-card rounded-3xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-table-list text-indigo-500"></i>
                        District Full Data
                    </h3>
                    <p class="text-gray-500 font-medium">
                        Reserved for advanced charts, tables, or future expansion modules.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- TOOLTIP -->
<div id="custom-tooltip" class="tooltip">
    <div id="tooltip-content"></div>
</div>

@include('frontend.layouts.footer')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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

            document.getElementById('total-count').textContent = t.toLocaleString();
            document.getElementById('avg-count').textContent = avg.toLocaleString();
            document.getElementById('highest-district').textContent = highest.name;
        }

        function total() {
            return Object.values(districtData).reduce((a, b) => a + (parseInt(b) || 0), 0);
        }

        function showTooltip(e, name, count) {
            document.getElementById('tooltip-content').innerHTML = `
                                <div class="font-bold border-b border-gray-600/50 pb-1.5 mb-1.5 text-indigo-100 flex items-center justify-between gap-3">
                                    <span>${name}</span>
                                    <i class="fa-solid fa-map-pin text-[10px] text-indigo-400"></i>
                                </div>
                                <div class="text-indigo-200 text-xs font-medium">
                                    Beneficiaries: <span class="text-white font-black ml-1">${count.toLocaleString()}</span>
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
                                <div class="p-8 bg-white/60 shadow-inner rounded-full mb-6 border border-gray-100 animate-fade-in">
                                    <i class="fa-solid fa-hand-pointer text-4xl text-indigo-200"></i>
                                </div>
                                <h4 class="font-black text-xl text-gray-800 tracking-tight animate-fade-in" style="animation-delay: 0.1s">No Selection</h4>
                                <p class="text-gray-500 mt-2 max-w-xs font-medium animate-fade-in" style="animation-delay: 0.2s">
                                    Click a district on the map to view detailed beneficiary insights
                                </p>
                            `;
        });

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