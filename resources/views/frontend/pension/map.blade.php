@extends('frontend.layouts.app-template')

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
            /* 🔥 important */
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
            /* 👈 near cursor */
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
                            <h3 class="text-3xl font-bold mt-2" id="district-count">0</h3>
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
                        <div id="map-svg-wrapper" class="flex-1 hidden flex items-center justify-center overflow-hidden">
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



        $(document).ready(function () {
            initMap();
        });
        $(function () {
            let districtData = {};

            async function initMap() {
                try {
                    districtData = await $.ajax({
                        url: "{{ route('map.district.count') }}",
                        type: "POST",
                        dataType: "json",
                        data: {
                            _token: "{{ csrf_token() }}",
                            // optional filters if needed later
                            // year: 2025,
                            // scheme_id: 1
                        }
                    });

                    $('#loading').hide();
                    $('#map-svg-wrapper')
                        .removeClass('hidden')
                        .addClass('flex');

                    bindDistricts();
                    updateStats();

                } catch (xhr) {
                    console.error(xhr.responseText);

                    $('#loading').html(`
                                                    <div class="text-center">
                                                        <i class="fa-solid fa-triangle-exclamation text-red-500 text-3xl mb-2"></i>
                                                        <p class="text-red-600 font-bold">
                                                            Failed to load district data
                                                        </p>
                                                    </div>
                                                `);
                }
            }




            function bindDistricts() {
                $('.district').each(function () {
                    const $d = $(this);
                    const code = $d.attr('district-code');
                    const name = $d.data('name');
                    const count = parseInt(districtData[code] || 0);

                    $d.data({ count, name });
                    setColor($d, count);

                    $d.on('mouseenter', e => showTooltip(e, name, count));
                    $d.on('mousemove', moveTooltip);
                    $d.on('mouseleave', hideTooltip);
                    $d.on('click', () => selectDistrict($d, code, name, count));
                });
            }

            function setColor($d, count) {
                // Improved color ramp based on your logic
                // let c = '#e0e7ff'; // default
                if (count > 500) c = '#1e293b';
                else if (count > 200) c = '#334155';
                else if (count > 50) c = '#6366f1';
                $d.css('fill', c);
            }

            function selectDistrict($d, code, name, count) {
                $('.district').removeClass('selected');
                $d.addClass('selected');

                const totalBeneficiaries = total();
                const pct = totalBeneficiaries > 0 ? ((count / totalBeneficiaries) * 100).toFixed(2) : 0;

                // Restructured HTML for better Right-Panel UI
                $('#district-info').fadeOut(150, function () {
                    $(this).html(`
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
                                                                                                            `).fadeIn(300);
                });
            }

            function updateStats() {
                let t = total();
                let keys = Object.keys(districtData);
                let d = keys.length;
                let avg = d ? Math.round(t / d) : 0;

                let highest = { name: '-', count: 0 };
                $('.district').each(function () {
                    if ($(this).data('count') > highest.count) {
                        highest = {
                            name: $(this).data('name'),
                            count: $(this).data('count')
                        };
                    }
                });

                $('#total-count').text(t.toLocaleString());
                $('#district-count').text(d);
                $('#avg-count').text(avg.toLocaleString());
                $('#highest-district').text(highest.name);
            }

            function total() {
                return Object.values(districtData).reduce((a, b) => a + (parseInt(b) || 0), 0);
            }

            function showTooltip(e, name, count) {
                $('#tooltip-content').html(`
                                                                    <div class="font-bold border-b border-gray-700 pb-1 mb-1">
                                                                        ${name}
                                                                    </div>
                                                                    <div class="text-indigo-400">
                                                                        Beneficiaries:
                                                                        <span class="text-white">${count.toLocaleString()}</span>
                                                                    </div>
                                                                `);

                $('#custom-tooltip').show();
                moveTooltip(e); // 🔥 important
            }

            function moveTooltip(e) {
                $('#custom-tooltip').css({
                    left: e.clientX + 'px',
                    top: e.clientY + 'px'
                });
            }

            function hideTooltip() {
                $('#custom-tooltip').hide();
            }

            $('#reset-btn').on('click', () => {
                $('.district').removeClass('selected');
                $('#district-info').html(`
                                                                                                            <div class="p-8 bg-gray-50 rounded-full mb-4">
                                                                                                                <i class="fa-solid fa-hand-pointer text-4xl text-gray-300"></i>
                                                                                                            </div>
                                                                                                            <h4 class="text-gray-800 font-bold text-lg">No Selection</h4>
                                                                                                            <p class="text-gray-500 max-w-xs mt-2">
                                                                                                                Please click on a district within the map to view specific beneficiary statistics.
                                                                                                            </p>
                                                                                                        `);
            });

            initMap();
        });
    </script>
@endpush