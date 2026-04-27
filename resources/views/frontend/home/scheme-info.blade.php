@extends('frontend.layouts.app-template')

@push('styles')
    <style>
        .link-hover-effect {
            position: relative;
            transition: all 0.3s ease;
        }

        .link-hover-effect::after {
            content: "";
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #f59e0b;
            transition: width 0.3s ease;
        }

        .link-hover-effect:hover::after {
            width: 100%;
        }

        .contact-icon {
            display: inline-block;
            width: 24px;
            text-align: center;
            margin-right: 8px;
            color: #f59e0b;
        }

        .copyright {
            position: relative;
        }

        .copyright::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        }

        .scheme-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* 🔥 Responsive Fix */
        @media (max-width: 640px) {
            h1 {
                font-size: 1.5rem !important;
                line-height: 1.3;
            }

            .scheme-card {
                border-left-width: 3px !important;
            }

            .benefit-icon {
                width: 28px;
                height: 28px;
            }
        }
    </style>
@endpush

@section('content')

    @include('frontend.components.top-header')
    @include('frontend.components.header')

    <section id="pension-scheme" class="max-w-7xl mx-auto px-4 py-12">

        @php
            $baseColor = $scheme_json->ref_color;
        @endphp

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="text-white px-6 py-8 bg-linear-to-r from-{{ $baseColor }}-800 to-{{ $baseColor }}-600">

                <div class="flex flex-col md:flex-row items-center md:items-start justify-between gap-4">

                    <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-3">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas {{ $scheme_json->icon }} text-2xl text-{{ $baseColor }}-600"></i>
                        </div>

                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold">{{ $scheme_info->scheme_name }}</h1>
                            <p class="text-white/80">{{ $department->f_name }}</p>
                        </div>
                    </div>

                    <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2 text-center">
                        <div class="text-xl sm:text-2xl font-bold">₹{{ $scheme_json->money }}/month</div>
                        <div class="text-sm text-white/80">Financial Assistance</div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

            <div class="bg-white rounded-lg shadow-md p-4 text-center">
                <div class="text-xl sm:text-2xl font-bold break-words text-{{ $baseColor }}-700">
                    {{ $scheme_json->quick_stats->eligibility_age }}
                </div>
                <div class="text-gray-600 text-sm">Eligibility Age</div>
            </div>

            @if(in_array($scheme_id, [1, 3, 19]))
                <div class="bg-white rounded-lg shadow-md p-4 text-center">
                    <div class="text-xl sm:text-2xl font-bold break-words text-{{ $baseColor }}-700">
                        {{ $scheme_json->quick_stats->eligibility_caste }}
                    </div>
                    <div class="text-gray-600 text-sm">Eligibility Caste</div>
                </div>
            @elseif ($scheme_id == 2)
                <div class="bg-white rounded-lg shadow-md p-4 text-center">
                    <div class="text-xl sm:text-2xl font-bold break-words text-{{ $baseColor }}-700">
                        {{ $scheme_json->quick_stats->eligibility_criteria }}
                    </div>
                    <div class="text-gray-600 text-sm">Eligibility Criteria</div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-4 text-center">
                    <div class="text-gray-600 text-sm">Direct Transfer</div>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-md p-4 text-center">
                <div id="beneficiariesCounter" class="text-xl sm:text-2xl font-bold text-{{ $baseColor }}-700"
                    data-target="{{ $ben_count }}">0</div>
                <div class="text-gray-600 text-sm">Beneficiaries</div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 text-center">
                <div id="allocationCounter" class="text-xl sm:text-2xl font-bold text-{{ $baseColor }}-700"
                    data-target="{{ $scheme_json->quick_stats->allocation }}">0</div>
                <div class="text-gray-600 text-sm">Monthly Allocation</div>
            </div>

        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT -->
            <div class="lg:col-span-2 space-y-6">

                <div class="scheme-card bg-white rounded-lg shadow-md p-4 sm:p-6 border-l-4 border-{{ $baseColor }}-600">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-{{ $baseColor }}-600"></i> About the Scheme
                    </h2>
                    <p class="text-gray-600 mb-4">{{ $scheme_json->about->long }}</p>
                </div>

                <div class="scheme-card bg-white rounded-lg shadow-md p-4 sm:p-6 border-l-4 border-{{ $baseColor }}-600">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-clipboard-check mr-2 text-{{ $baseColor }}-600"></i> Eligibility Criteria
                    </h2>

                    @foreach ($scheme_json->eligibility->eligibility_criteria->key as $criteria)
                        <div class="flex items-start gap-3 mb-3">
                            <div
                                class="benefit-icon w-8 h-8 rounded-full flex items-center justify-center text-white bg-{{ $baseColor }}-600">
                                <i class="{{ $criteria->icon }}"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $criteria->title }}</h3>
                                <p class="text-gray-600 text-sm">{{ $criteria->description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

            <!-- RIGHT -->
            <div class="space-y-6">

                <div class="scheme-card bg-white rounded-lg shadow-md p-4 sm:p-6 border-l-4 border-{{ $baseColor }}-600">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-file-alt mr-2 text-{{ $baseColor }}-600"></i> Application Process
                    </h2>

                    @foreach($scheme_json->workflow->steps as $step)
                        <div class="flex items-start gap-3 mb-3">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center bg-{{ $baseColor }}-100 text-{{ $baseColor }}-700 font-bold">
                                {{$step->rank}}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">{{$step->name}}</h3>
                                <p class="text-gray-600 text-sm">{{$step->description}}</p>
                            </div>
                        </div>
                    @endforeach

                    <button
                        class="w-full text-white py-3 px-4 rounded-lg font-semibold text-sm sm:text-base bg-{{ $baseColor }}-600 hover:bg-{{ $baseColor }}-700 transition shadow-lg">
                        Download Application Form
                    </button>

                </div>

            </div>

        </div>

    </section>

    @include('frontend.layouts.footer')
@endsection

@push('scripts')
    <script>
        function formatIndianCompact(num) {
            if (num >= 10000000) return (num / 10000000).toFixed(1) + "Cr+";
            if (num >= 100000) return (num / 100000).toFixed(1) + "L+";
            if (num >= 1000) return (num / 1000).toFixed(1) + "K+";
            return num;
        }

        function animateCounter(id, target, formatter) {
            let el = document.getElementById(id);
            if (!el) return;
            let start = 0;
            let timer = setInterval(() => {
                start += Math.ceil(target / 50);
                if (start >= target) { el.textContent = formatter(target); clearInterval(timer); }
                else el.textContent = formatter(start);
            }, 30);
        }

        document.addEventListener('DOMContentLoaded', () => {
            let b = document.getElementById('beneficiariesCounter');
            if (b) animateCounter('beneficiariesCounter', parseInt(b.dataset.target), formatIndianCompact);

            let a = document.getElementById('allocationCounter');
            if (a) animateCounter('allocationCounter', parseInt(a.dataset.target), formatIndianCompact);
        });
    </script>
@endpush