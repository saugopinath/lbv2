@extends('frontend.layouts.app-template')

@section('content')
@include('frontend.components.top-header')
@include('frontend.components.header')

@php
$baseColor = $department_json->ref_color ?? 'indigo';
$gradientColor = $department_json->ref_gradient_color ?? 'emerald';
@endphp

<style>
    /* Custom animations and styles */
    @keyframes gradientShift {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 0.3;
        }

        50% {
            transform: scale(1.05);
            opacity: 0.5;
        }

        100% {
            transform: scale(1);
            opacity: 0.3;
        }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }

    /* .animate-gradient {
        background-size: 200% 200%;
        animation: gradientShift 8s ease infinite;
    } */
    /* .animate-gradient {
        background-size: 200% 200%;
        animation: gradientShift 8s ease infinite;
    } */
    @keyframes gradientShift {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .animate-gradient {
        background-size: 200% 200%;
        animation: gradientShift 8s ease infinite;
    }

    .animate-float {
        animation: float 3s ease-in-out infinite;
    }

    .animate-pulse-slow {
        animation: pulse 3s ease-in-out infinite;
    }

    .card-animate {
        animation: slideInUp 0.6s ease-out forwards;
        opacity: 0;
    }

    .card-animate:nth-child(1) {
        animation-delay: 0.1s;
    }

    .card-animate:nth-child(2) {
        animation-delay: 0.2s;
    }

    .card-animate:nth-child(3) {
        animation-delay: 0.3s;
    }

    .card-animate:nth-child(4) {
        animation-delay: 0.4s;
    }

    .hover-lift {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
    }

    /* Modern scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .pl-13 {
        padding-left: 3.25rem;
    }

    @keyframes pulse-slow {

        0%,
        100% {
            opacity: 0.4;
            transform: scale(1);
        }

        50% {
            opacity: 0.7;
            transform: scale(1.05);
        }
    }

    .animate-pulse-slow {
        animation: pulse-slow 2s ease-in-out infinite;
    }

    /* Color-specific border utilities for Tailwind */
    .hover\:border-indigo-300:hover {
        border-color: #a5b4fc;
    }

    .hover\:border-emerald-300:hover {
        border-color: #6ee7b7;
    }

    .hover\:border-purple-300:hover {
        border-color: #d8b4fe;
    }

    .hover\:border-amber-300:hover {
        border-color: #fcd34d;
    }

    .hover\:border-rose-300:hover {
        border-color: #fda4af;
    }

    .hover\:border-cyan-300:hover {
        border-color: #67e8f9;
    }
</style>

<section id="wcd-department" class="max-w-8xl mx-auto px-4 py-8 font-poppins">

    {{-- Hero Header Card --}}
    {{-- Hero Header Card --}}
    <div class="group relative mb-12 overflow-hidden rounded-2xl shadow-xl transition-all duration-500 hover:shadow-2xl bg-{{ $baseColor }}-600">
        {{-- Perfectly looping animated gradient background --}}
        <div class="absolute inset-0 bg-gradient-to-r from-{{ $baseColor }}-400 via-{{ $gradientColor }}-500 to-{{ $baseColor }}-400 animate-gradient"></div>
        <div class="absolute inset-0 bg-black/20 backdrop-blur-[1px]"></div>

        {{-- Decorative patterns --}}
        <!-- <div class="absolute top-0 right-0 -mt-20 -mr-20 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div> -->

        <div class="relative px-6 py-8 md:px-12 md:py-8">
            <div class="flex flex-col items-center justify-between gap-8 md:flex-row">
                <div class="flex items-center gap-6">
                    {{-- Animated logo container --}}
                    <div class="relative">
                        <div class="absolute inset-0 rounded-full bg-white/30 animate-ping"></div>
                        <div class="relative flex h-24 w-24 items-center justify-center rounded-full bg-white/95 shadow-2xl transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <i class="fas fa-landmark text-4xl text-{{ $baseColor }}-600"></i>
                        </div>
                    </div>
                    <div class="text-white">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[10px] font-bold uppercase tracking-wider mb-2 border border-white/30">
                            <i class="fas fa-building text-xs"></i>
                            <span>Government of West Bengal</span>
                        </div>
                        <h1 class="text-3xl font-black tracking-tight md:text-5xl drop-shadow-[0_5px_15px_rgba(0,0,0,0.3)]">
                            {{ $department_json->department_name }}
                        </h1>
                    </div>
                </div>
                {{-- Tagline with glassmorphism --}}
                <div class="rounded-xl bg-white/20 backdrop-blur-md px-6 py-4 text-center shadow-lg ring-1 ring-white/30 transition-all duration-300 hover:bg-white/30 hover:scale-105">
                    <div class="text-xl font-bold text-white drop-shadow-md">{{ $department_json->tagline->line1 }}</div>
                    <div class="text-white/90 mt-1">{{ $department_json->tagline->line2 }}</div>
                    <div class="text-sm text-white/70 mt-1">{{ $department_json->tagline->line3 }}</div>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 rounded-2xl border border-white/20 pointer-events-none"></div>
        <div class="absolute top-4 left-4 h-12 w-12 border-t-2 border-l-2 border-white/30 rounded-tl-2xl"></div>
        <div class="absolute bottom-4 right-4 h-12 w-12 border-b-2 border-r-2 border-white/30 rounded-br-2xl"></div>
    </div>

    {{-- Tailwind Safelist (Hidden) --}}
    <div class="hidden bg-blue-600 bg-pink-600 bg-indigo-600 bg-green-600 bg-orange-600 bg-violet-600 bg-lime-600 bg-sky-600 bg-amber-600 bg-fuchsia-600 bg-rose-600 bg-emerald-600 bg-teal-600 from-blue-500 from-pink-500 from-indigo-500 from-green-500 from-orange-500 from-violet-500 from-lime-500 from-sky-500 from-amber-500 from-fuchsia-500 from-rose-500 from-emerald-500 from-teal-500 via-blue-600 via-pink-600 via-indigo-600 via-green-600 via-orange-600 via-violet-600 via-lime-600 via-sky-600 via-amber-600 via-fuchsia-600 via-rose-600 via-emerald-600 via-teal-600 to-blue-700 to-pink-700 to-indigo-700 to-green-700 to-orange-700 to-violet-700 to-lime-700 to-sky-700 to-amber-700 to-fuchsia-700 to-rose-700 to-emerald-700 to-teal-700 text-blue-600 text-pink-600 text-indigo-600 text-green-600 text-orange-600 text-violet-600 text-lime-600 text-sky-600 text-amber-600 text-fuchsia-600 text-rose-600 text-emerald-600 text-teal-600 border-blue-500 border-pink-500 border-indigo-500 border-green-500 border-orange-500 border-violet-500 border-lime-500 border-sky-500 border-amber-500 border-fuchsia-500 border-rose-500 border-emerald-500 border-teal-500 hover:bg-blue-100 hover:bg-pink-100 hover:bg-indigo-100 hover:bg-green-100 hover:bg-orange-100 hover:bg-violet-100 hover:bg-lime-100 hover:bg-sky-100 hover:bg-amber-100 hover:bg-fuchsia-100 hover:bg-rose-100 hover:bg-emerald-100 hover:bg-teal-100"></div>

    {{-- Quick Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <!-- Card 1 - Applied Beneficiaries -->
        <div class="card-animate group relative rounded-2xl p-5 text-center transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl bg-gradient-to-br from-blue-500 to-indigo-600 overflow-hidden cursor-default">
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/20 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="absolute -bottom-8 -left-8 h-24 w-24 rounded-full bg-white/10 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="relative z-10">
                <div class="mb-3 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-users text-2xl text-white"></i>
                </div>
                <div class="text-4xl font-black text-white" data-count="{{ $ben_count_all }}">0</div>
                <div class="mt-2 text-sm font-semibold text-white/90">Applied Beneficiaries</div>
                <div class="mt-1 text-xs text-white/70 flex items-center justify-center gap-1">
                    <i class="fas fa-arrow-up"></i> Total applications
                </div>
            </div>
        </div>

        <!-- Card 2 - Approved Beneficiaries -->
        <div class="card-animate group relative rounded-2xl p-5 text-center transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl bg-gradient-to-br from-emerald-500 to-teal-600 overflow-hidden cursor-default">
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/20 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="absolute -bottom-8 -left-8 h-24 w-24 rounded-full bg-white/10 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="relative z-10">
                <div class="mb-3 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle text-2xl text-white"></i>
                </div>
                <div class="text-4xl font-black text-white" data-count="{{ $ben_count_approved }}">0</div>
                <div class="mt-2 text-sm font-semibold text-white/90">Approved Beneficiaries</div>
                <div class="mt-1 text-xs text-white/70 flex items-center justify-center gap-1">
                    <i class="fas fa-check-circle"></i> Verified & approved
                </div>
            </div>
        </div>

        <!-- Card 3 - Schemes -->
        <div class="card-animate group relative rounded-2xl p-5 text-center transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl bg-gradient-to-br from-purple-500 to-pink-600 overflow-hidden cursor-default">
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/20 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="absolute -bottom-8 -left-8 h-24 w-24 rounded-full bg-white/10 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="relative z-10">
                <div class="mb-3 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-file-alt text-2xl text-white"></i>
                </div>
                <div class="text-4xl font-black text-white" data-count="{{ $onboard_scheme_count }}">0</div>
                <div class="mt-2 text-sm font-semibold text-white/90">Active Schemes</div>
                <div class="mt-1 text-xs text-white/70 flex items-center justify-center gap-1">
                    <i class="fas fa-running"></i> Running programs
                </div>
            </div>
        </div>

        <!-- Card 4 - Monthly Disbursement -->
        <div class="card-animate group relative rounded-2xl p-5 text-center transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl bg-gradient-to-br from-amber-500 to-orange-600 overflow-hidden cursor-default">
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/20 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="absolute -bottom-8 -left-8 h-24 w-24 rounded-full bg-white/10 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="relative z-10">
                <div class="mb-3 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-rupee-sign text-2xl text-white"></i>
                </div>
                <div class="text-4xl font-black text-white" data-count="{{ $total_disbrusment }}" data-type="money">0</div>
                <div class="mt-2 text-sm font-semibold text-white/90">Monthly Disbursement</div>
                <div class="mt-1 text-xs text-white/70 flex items-center justify-center gap-1">
                    <i class="fas fa-calendar-alt"></i> This month's payout
                </div>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- About Section --}}
            <div class="group rounded-2xl bg-white shadow-lg transition-all duration-500 hover:shadow-xl overflow-hidden relative border border-gray-100 hover-lift">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-{{ $baseColor }}-500 to-{{ $gradientColor }}-500"></div>

                <div class="p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="relative">
                            <div class="absolute inset-0 rounded-xl bg-{{ $baseColor }}-200 animate-pulse-slow"></div>
                            <div class="relative flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-{{ $baseColor }}-100 to-{{ $gradientColor }}-100">
                                <i class="fas fa-info-circle text-{{ $baseColor }}-600 text-xl"></i>
                            </div>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">About the Department</h2>
                    </div>

                    <p class="text-gray-600 leading-relaxed mb-6 text-justify">{{ $department_json->long }}</p>

                    <div class="rounded-xl p-6 bg-gradient-to-br from-{{ $baseColor }}-50 to-{{ $gradientColor }}-50 border border-{{ $baseColor }}-100">
                        <h3 class="font-bold mb-4 text-{{ $baseColor }}-800 flex items-center gap-2">
                            <i class="fas fa-eye"></i> Vision & Mission
                        </h3>
                        <div class="space-y-4">
                            @foreach($department_json->about->vision_mission as $vm)
                            <div class="flex items-start gap-3 transition-all duration-300 hover:translate-x-1">
                                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-{{ $baseColor }}-200">
                                    <i class="fas {{ $loop->first ? 'fa-bullseye' : 'fa-flag' }} text-{{ $baseColor }}-600 text-sm"></i>
                                </div>
                                <div>
                                    <strong class="text-gray-800">{{ $vm->title }}:</strong>
                                    <span class="text-gray-600">{{ $vm->text }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Key Functions Section --}}
            <div class="group rounded-2xl bg-white shadow-lg transition-all duration-500 hover:shadow-xl overflow-hidden relative border border-gray-100 hover-lift">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-{{ $baseColor }}-500 to-{{ $gradientColor }}-500"></div>

                <div class="p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-{{ $baseColor }}-100 to-{{ $gradientColor }}-100">
                            <i class="fas fa-tasks text-{{ $baseColor }}-600 text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Key Functions & Responsibilities</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-5">
                        @foreach($department_json->key_functions as $key_func)
                        <div class="group/function flex items-start gap-4 rounded-xl p-4 transition-all duration-300 hover:bg-{{ $baseColor }}-100 hover:shadow-md">
                            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-{{ $baseColor }}-200 to-{{ $gradientColor }}-200 shadow-sm transition-transform duration-300 group-hover/function:scale-110">
                                <i class="fas {{ $key_func->icon }} text-{{ $baseColor }}-700 text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-1">{{ $key_func->title }}</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">{{ $key_func->text }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Major Initiatives Section --}}
            <div class="group rounded-2xl bg-white shadow-lg transition-all duration-500 hover:shadow-xl overflow-hidden relative border border-gray-100 hover-lift">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-{{ $baseColor }}-500 to-{{ $gradientColor }}-500"></div>

                <div class="p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="relative">
                            <div class="absolute inset-0 rounded-xl bg-{{ $baseColor }}-200 animate-pulse-slow"></div>
                            <div class="relative flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-{{ $baseColor }}-100 to-{{ $gradientColor }}-100">
                                <i class="fas fa-star text-{{ $baseColor }}-600 text-xl"></i>
                            </div>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Major Initiatives & Achievements</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($department_json->major_initiatives as $index => $major)
                        @php
                        $initiativeColors = ['indigo', 'emerald', 'purple', 'amber', 'rose', 'cyan'];
                        $itemColor = $initiativeColors[$index % count($initiativeColors)];
                        @endphp
                        <div class="group/card relative rounded-xl p-5 bg-white border-2 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl overflow-hidden"
                            style="border-color: {{ ${$itemColor . 'Hex'} ?? '#e2e8f0' }};">

                            {{-- Animated gradient background - Always visible (not just on hover) --}}
                            <div class="absolute inset-0 bg-gradient-to-br from-{{ $itemColor }}-50 to-white opacity-100"></div>

                            {{-- Decorative corner icon - Always visible --}}
                            <div class="absolute -right-3 -top-3 h-12 w-12 rounded-full bg-{{ $itemColor }}-100 opacity-100 transition-all duration-300 group-hover/card:scale-150"></div>

                            <div class="relative z-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-{{ $itemColor }}-100 to-{{ $itemColor }}-200 shadow-sm transition-all duration-300 group-hover/card:scale-110 group-hover/card:shadow-md">
                                        <i class="fas {{ $major->icon }} text-{{ $itemColor }}-600 text-sm"></i>
                                    </div>
                                    <h3 class="font-semibold text-gray-800 leading-tight transition-colors duration-300 group-hover/card:text-{{ $itemColor }}-700">{{ $major->name }}</h3>
                                </div>
                                <p class="text-sm mt-3 text-gray-600 leading-relaxed pl-13 transition-colors duration-300 group-hover/card:text-gray-700">{{ $major->description }}</p>

                                {{-- Subtle indicator line - Always visible with full width --}}
                                <div class="mt-2 h-0.5 w-full bg-gradient-to-r from-{{ $itemColor }}-400 to-{{ $itemColor }}-600"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>


        </div>

        {{-- Right Column --}}
        <div class="space-y-8">

            {{-- Flagship Schemes --}}
            <div class="group rounded-2xl bg-white shadow-lg transition-all duration-500 hover:shadow-xl overflow-hidden relative border border-gray-100 hover-lift">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-orange-500"></div>

                <div class="p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-100 to-amber-200">
                            <i class="fas fa-trophy text-amber-600 text-lg"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Flagship Schemes</h2>
                    </div>
                    <div class="space-y-4">
                        @foreach($department_json->flagship_schemes as $flag)
                        <div class="pl-4 border-l-4 border-{{ $flag->color ?? 'indigo' }}-500 transition-all duration-300 hover:pl-5">
                            <h3 class="font-semibold text-gray-800">{{ $flag->name }}</h3>
                            <p class="text-gray-500 text-sm">{{ $flag->description }}</p>
                        </div>
                        @endforeach
                    </div>
                    <button id="viewAllSchemes"
                        class="group/btn mt-6 w-full relative overflow-hidden rounded-xl bg-{{ $baseColor }}-600 px-4 py-3 font-bold text-white transition-all duration-300 hover:bg-{{ $baseColor }}-700 hover:shadow-xl hover:-translate-y-0.5 active:scale-[0.98]">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <i class="fas fa-list-ul"></i> View All Schemes
                        </span>
                        {{-- Glossy effect --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:animate-[shimmer_1.5s_infinite]"></div>
                    </button>
                </div>
            </div>

            {{-- Organizational Structure --}}
            <div class="group rounded-2xl bg-white shadow-lg transition-all duration-500 hover:shadow-xl overflow-hidden relative border border-gray-100 hover-lift">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 to-purple-600"></div>

                <div class="p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-100 to-purple-200">
                            <i class="fas fa-sitemap text-purple-600 text-lg"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Organizational Structure</h2>
                    </div>
                    <ul class="space-y-3">
                        @foreach($department_json->orgnizational_structure as $org)
                        <li class="flex items-center gap-3 rounded-lg p-2 transition-all duration-200 hover:bg-{{ $baseColor }}-100 group/item">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-{{ $baseColor }}-100 transition-colors duration-200 group-hover/item:bg-{{ $baseColor }}-200">
                                <i class="fas {{ $org->icon }} text-{{ $baseColor }}-600 text-sm"></i>
                            </div>
                            <span class="text-gray-700 font-medium group-hover/item:text-{{ $baseColor }}-700 transition-colors">{{ $org->title }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="group rounded-2xl bg-white shadow-lg transition-all duration-500 hover:shadow-xl overflow-hidden relative border border-gray-100 hover-lift">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 to-emerald-500"></div>

                <div class="p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-100 to-emerald-200">
                            <i class="fas fa-address-card text-green-600 text-lg"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Contact Information</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="flex gap-3 transition-all duration-300 hover:translate-x-1">
                            <i class="fas fa-map-marker-alt mt-1 text-{{ $baseColor }}-600"></i>
                            <div>
                                <div class="font-semibold text-gray-800">Head Office</div>
                                <div class="text-sm text-gray-600">{{ $department_json->contact->address }}</div>
                            </div>
                        </div>
                        <div class="flex gap-3 transition-all duration-300 hover:translate-x-1">
                            <i class="fas fa-phone mt-1 text-{{ $baseColor }}-600"></i>
                            <div>
                                <div class="font-semibold text-gray-800">Helpline</div>
                                @foreach($department_json->contact->helplines as $help)
                                <div class="text-gray-600">{{ $help }}</div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex gap-3 transition-all duration-300 hover:translate-x-1">
                            <i class="fas fa-envelope mt-1 text-{{ $baseColor }}-600"></i>
                            <div>
                                <div class="font-semibold text-gray-800">Email</div>
                                <a href="mailto:{{ $department_json->contact->email }}" class="text-sm text-{{ $baseColor }}-600 hover:underline">{{ $department_json->contact->email }}</a>
                            </div>
                        </div>
                        <div class="flex gap-3 transition-all duration-300 hover:translate-x-1">
                            <i class="fas fa-globe mt-1 text-{{ $baseColor }}-600"></i>
                            <div>
                                <div class="font-semibold text-gray-800">Website</div>
                                <a href="{{ $department_json->contact->website }}" target="_blank" class="text-sm text-{{ $baseColor }}-600 hover:underline">{{ $department_json->contact->website }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Schemes Modal --}}
    <div id="schemesModal" style="display:none;" class="fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-[1000] transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-gradient-to-r from-{{ $baseColor }}-700 to-{{ $gradientColor }}-600 px-6 py-5 flex justify-between items-center rounded-t-2xl">
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-file-alt"></i> All Government Schemes
                </h2>
                <button id="closeModal" class="text-white/80 hover:text-white text-2xl transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($department_json->all_schemes ?? [] as $s)
                    @php $sColorName = $s->color ?? 'indigo'; @endphp
                    <div class="group/scheme rounded-xl border border-{{ $sColorName }}-200 bg-gradient-to-br from-{{ $sColorName }}-50 to-white p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-{{ $sColorName }}-500 text-white shadow-lg">
                                <i class="fas {{ $s->icon ?? 'fa-circle' }} text-lg"></i>
                            </div>
                            <h3 class="font-bold text-lg text-{{ $sColorName }}-700">{{ $s->name }}</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-3 leading-relaxed">{{ $s->description ?? '' }}</p>
                        <div class="flex flex-wrap gap-2 pt-2 border-t border-{{ $sColorName }}-100">
                            <span class="inline-flex items-center gap-1 rounded-full bg-{{ $sColorName }}-100 px-2 py-1 text-xs text-{{ $sColorName }}-700">
                                <i class="fas fa-gift"></i> {{ $s->benefit ?? 'Benefit' }}
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-{{ $sColorName }}-100 px-2 py-1 text-xs text-{{ $sColorName }}-700">
                                <i class="fas fa-users"></i> {{ $s->target_group ?? 'Target Group' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 col-span-full text-center py-12">No schemes available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</section>

@include('frontend.layouts.footer')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animated counter function
        function animateCounter(element, target, isMoney = false) {
            let current = 0;
            const duration = 1500;
            const steps = 60;
            const increment = target / steps;
            let step = 0;

            const timer = setInterval(() => {
                step++;
                current += increment;
                if (step >= steps) {
                    current = target;
                    clearInterval(timer);
                }
                if (isMoney) {
                    element.textContent = '₹' + Math.round(current).toLocaleString('en-IN');
                } else {
                    element.textContent = Math.round(current).toLocaleString('en-IN');
                }
            }, duration / steps);
        }

        // Initialize counters for stats cards
        const counters = document.querySelectorAll('[data-count]');
        counters.forEach(counter => {
            const target = parseFloat(counter.getAttribute('data-count')) || 0;
            const isMoney = counter.getAttribute('data-type') === 'money';
            animateCounter(counter, target, isMoney);
        });

        // Modal functionality
        const modal = document.getElementById('schemesModal');
        const viewAllBtn = document.getElementById('viewAllSchemes');
        const closeModalBtn = document.getElementById('closeModal');

        function openModal() {
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal() {
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        if (viewAllBtn) {
            viewAllBtn.addEventListener('click', openModal);
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeModal);
        }

        // Close modal on outside click
        window.addEventListener('click', function(e) {
            if (modal && e.target === modal) {
                closeModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
                closeModal();
            }
        });

        // Add animation to cards on scroll
        const animatedCards = document.querySelectorAll('.card-animate');

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        animatedCards.forEach(card => {
            observer.observe(card);
        });
    });
</script>
@endsection