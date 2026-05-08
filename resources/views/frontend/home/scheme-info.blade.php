@extends('frontend.layouts.app-template')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@php
$colorMap = [
'indigo' => ['hex' => '6366f1', 'rgb' => '99, 102, 241'],
'blue' => ['hex' => '3b82f6', 'rgb' => '59, 130, 246'],
'emerald' => ['hex' => '10b981', 'rgb' => '16, 185, 129'],
'green' => ['hex' => '22c55e', 'rgb' => '34, 197, 94'],
'orange' => ['hex' => 'f97316', 'rgb' => '249, 115, 22'],
'amber' => ['hex' => 'f59e0b', 'rgb' => '245, 158, 11'],
'red' => ['hex' => 'ef4444', 'rgb' => '239, 68, 68'],
'purple' => ['hex' => 'a855f7', 'rgb' => '168, 85, 247'],
'pink' => ['hex' => 'ec4899', 'rgb' => '236, 72, 153'],
'rose' => ['hex' => 'f43f5e', 'rgb' => '244, 63, 94'],
'slate' => ['hex' => '64748b', 'rgb' => '100, 116, 139'],
];

$refColor = $scheme_json->ref_color ?? 'indigo';
$colors = $colorMap[$refColor] ?? $colorMap['indigo'];
$primaryHex = $scheme_json->ref_color_hex ?? $colors['hex'];
$primaryRgb = $scheme_json->ref_color_rgb ?? $colors['rgb'];
@endphp
<style>
    :root {
        --primary-color: #{{ $primaryHex }};
        --primary-soft: rgba({{ $primaryRgb }}, 0.3);
    }

    body {
        font-family: 'Outfit', sans-serif;
        background-color: #f8fafc;
        color: #1e293b;
    }

    .hero-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1e293b 100%);
        position: relative;
        overflow: visible;
        /* Changed from hidden to prevent clipping children or overlapping siblings incorrectly */
        border-radius: 0 0 3rem 3rem;
        z-index: 10;
    }

    .hero-pattern {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.1;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-card:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: scale(1.05);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .scheme-badge {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .scheme-badge:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 1.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-bottom: 4px solid var(--primary-color);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 30px -5px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
    }

    .content-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.4s ease;
    }

    .content-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08);
    }

    .icon-box {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-soft);
        color: var(--primary-color);
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover .icon-box {
        transform: rotate(10deg) scale(1.1);
        background: var(--primary-color);
        color: white;
    }

    .hero-heading {
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .cta-button {
        background: var(--primary-color);
        color: white;
        padding: 1rem 2rem;
        border-radius: 1rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .cta-button:hover {
        transform: scale(1.02);
        filter: brightness(1.1);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .animate-up {
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .floating {
        animation: floating 3s ease-in-out infinite;
    }

    @keyframes floating {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    /* Premium Modern Tabs */
    .premium-tabs-nav {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 0.6rem;
        border-radius: 9999px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        width: fit-content;
        margin: 0 auto 3rem;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.08);
    }

    .premium-tab {
        padding: 0.8rem 1.8rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.8rem;
        color: #64748b;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .premium-tab i {
        font-size: 1rem;
        transition: transform 0.4s ease;
    }

    .premium-tab:hover {
        color: var(--primary-color);
        background: rgba(255, 255, 255, 0.5);
    }

    .premium-tab.active {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 10px 20px -5px var(--primary-soft);
    }

    .premium-tab.active i {
        transform: scale(1.1);
    }

    /* Premium Content Card */
    .premium-card {
        background: white;
        border-radius: 3.5rem;
        padding: 4.5rem;
        box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(241, 245, 249, 0.8);
        position: relative;
        overflow: hidden;
        min-height: 400px;
    }

    .mesh-gradient {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at 80% 20%, var(--primary-soft) 0%, transparent 40%),
            radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.8) 0%, transparent 40%);
        opacity: 0.6;
        pointer-events: none;
    }

    /* Elegant List Items */
    .elegant-list-item {
        background: white;
        padding: 1.5rem 2rem;
        border-radius: 1.5rem;
        margin-bottom: 1.25rem;
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: all 0.4s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }

    .elegant-list-item:hover {
        transform: translateX(10px) scale(1.01);
        border-color: var(--primary-color);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }

    .elegant-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 1rem;
        background: var(--primary-soft);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .elegant-list-item:hover .elegant-icon-box {
        background: var(--primary-color);
        color: white;
        transform: rotate(5deg);
    }

    .illustration-glow {
        position: absolute;
        right: -10%;
        bottom: -10%;
        width: 50%;
        opacity: 0.08;
        pointer-events: none;
        z-index: 0;
        filter: blur(20px);
    }

    @keyframes fadeInSlide {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>
@endpush

@section('content')
@include('frontend.components.top-header')
@include('frontend.components.header')

<main class="min-h-screen pb-20">
    <!-- Hero Section -->
    <section class="hero-section text-white pt-16 pb-32 px-4 mb-[-4rem]">
        <div class="hero-pattern"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex flex-col md:flex-row items-center gap-8 animate-up" style="animation-delay: 0.1s">
                <div class="w-24 h-24 md:w-32 md:h-32 bg-white/20 backdrop-blur-md rounded-3xl flex items-center justify-center border border-white/30 shadow-2xl floating">
                    @php
                    $displayIcon = $scheme_json->icon ?? $scheme_info->icon ?? 'fa-landmark';
                    if (is_string($displayIcon) && strpos($displayIcon, 'fa-') === 0 && strpos($displayIcon, 'fa-solid') === false && strpos($displayIcon, 'fas ') === false) {
                    $displayIcon = 'fas ' . $displayIcon;
                    }
                    @endphp
                    <i class="{{ $displayIcon }} text-4xl md:text-5xl text-white"></i>
                </div>

                <div class="text-center md:text-left flex-1">
                    <div class="flex flex-wrap justify-center md:justify-start gap-3 mb-4">
                        <span class="scheme-badge">{{ $department->f_name ?? 'Government of West Bengal' }}</span>
                        <span class="scheme-badge bg-white/10">Active Scheme</span>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight hero-heading">
                        {{ $scheme_info->scheme_name ?? 'Scheme Information' }}
                    </h1>
                    <p class="text-white/90 text-lg md:text-xl max-w-2xl leading-relaxed">
                        {{ $scheme_json->about->short ?? 'Empowering citizens through dedicated financial assistance and social welfare support.' }}
                    </p>
                </div>

                <div class="glass-card p-8 rounded-3xl text-center md:text-right min-w-[240px]">
                    @php
                    $moneyVal = $scheme_json->money ?? 0;
                    $moneyDisplay = is_numeric($moneyVal) ? number_format($moneyVal) : $moneyVal;
                    @endphp
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">₹{{ $moneyDisplay }}</div>
                    <div class="text-white/80 text-sm uppercase tracking-widest font-bold">Monthly Benefit</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Grid -->
    <section class="max-w-7xl mx-auto px-4 mb-12 relative z-20">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Age -->
            <div class="group stat-card bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 p-6 text-center border border-gray-100 animate-up" style="animation-delay: 0.2s">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-md group-hover:shadow-lg transition-all duration-300 group-hover:scale-110">
                    <i class="fas fa-calendar-alt text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-extrabold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                    {{ $scheme_json->quick_stats->eligibility_age ?? 'All Ages' }}
                </div>
                <div class="text-sm font-semibold text-gray-500 uppercase tracking-wide mt-2">Eligible Age</div>
                <div class="mt-3 h-1 w-12 bg-blue-500 rounded-full mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>

            <!-- Caste/Criteria -->
            <div class="group stat-card bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 p-6 text-center border border-gray-100 animate-up" style="animation-delay: 0.3s">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-md group-hover:shadow-lg transition-all duration-300 group-hover:scale-110">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <div class="text-xl font-extrabold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent line-clamp-2">
                    @if(isset($scheme_id) && in_array($scheme_id, [1, 3, 19]))
                    {{ $scheme_json->quick_stats->eligibility_caste ?? 'All' }}
                    @elseif (isset($scheme_id) && $scheme_id == 2)
                    {{ $scheme_json->quick_stats->eligibility_criteria ?? 'Criteria' }}
                    @else
                    {{ $scheme_json->quick_stats->eligibility_criteria ?? 'Universal' }}
                    @endif
                </div>
                <div class="text-sm font-semibold text-gray-500 uppercase tracking-wide mt-2">Criteria</div>
                <div class="mt-3 h-1 w-12 bg-purple-500 rounded-full mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>
            <!-- Allocation -->
            <div class="group stat-card bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 p-6 text-center border border-gray-100 animate-up" style="animation-delay: 0.5s">
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-md group-hover:shadow-lg transition-all duration-300 group-hover:scale-110">
                    <i class="fas fa-chart-line text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-extrabold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                    <span id="allocationCounter" data-target="{{ $scheme_json->quick_stats->allocation ?? 0 }}">0</span>
                    <span class="text-lg">₹</span>
                </div>
                <div class="text-sm font-semibold text-gray-500 uppercase tracking-wide mt-2">Monthly Outlay</div>
                <div class="mt-3 h-1 w-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-full mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>
            <div class="group stat-card bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 p-6 text-center border border-gray-100 animate-up" style="animation-delay: 0.4s">
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-md group-hover:shadow-lg transition-all duration-300 group-hover:scale-110">
                    <i class="fas fa-heart text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-extrabold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                    <span id="beneficiariesCounter" data-target="{{ $ben_count ?? 0 }}">0</span>
                    <span class="text-lg">+</span>
                </div>
                <div class="text-sm font-semibold text-gray-500 uppercase tracking-wide mt-2">Beneficiaries</div>
                <div class="mt-3 h-1 w-12 bg-green-500 rounded-full mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>

        </div>
    </section>

    <!-- Premium Tabbed Info Section -->
    <section class="max-w-7xl mx-auto px-4 mb-24 relative z-20">
        <!-- Pill-style Navigation -->
        <div class="premium-tabs-nav">
            <div id="tab-overview" class="premium-tab active" onclick="switchTab('overview')">
                <i class="fas fa-info-circle"></i> Overview
            </div>
            <div id="tab-eligibility" class="premium-tab" onclick="switchTab('eligibility')">
                <i class="fas fa-user-check"></i> Eligibility
            </div>
            <div id="tab-ineligibility" class="premium-tab" onclick="switchTab('ineligibility')">
                <i class="fas fa-user-times"></i> Ineligibility
            </div>
            <div id="tab-benefits" class="premium-tab" onclick="switchTab('benefits')">
                <i class="fas fa-gift"></i> Benefits
            </div>
            <div id="tab-implementation" class="premium-tab" onclick="switchTab('implementation')">
                <i class="fas fa-route"></i> Implementation
            </div>
            <div id="tab-instructions" class="premium-tab" onclick="switchTab('instructions')">
                <i class="fas fa-file-signature"></i> Instructions
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="premium-card">
            <div class="mesh-gradient"></div>
            <img src="{{ asset('images/jb_logo.png') }}" class="illustration-glow" alt="Decorative Illustration">
            <!-- Overview Panel -->
            <div id="panel-overview" class="tab-panel relative z-10 animate-up">
                <div class="max-w-4xl">
                    <div class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-6" style="background-color: var(--primary-soft); color: var(--primary-color);">
                        <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: var(--primary-color);"></span> Scheme Overview
                    </div>
                    <h2 class="text-4xl font-extrabold text-gray-900 mb-8 leading-tight">Empowering Citizens through <br><span style="color: var(--primary-color);">Dedicated Welfare</span></h2>
                    <div class="prose prose-slate prose-lg max-w-none text-gray-600 leading-relaxed space-y-4">
                        {!! nl2br(e($scheme_json->about->long ?? 'No detailed description available.')) !!}
                    </div>
                </div>
            </div>

            <!-- Eligibility Panel -->
            <div id="panel-eligibility" class="tab-panel hidden relative z-10">
                <div class="max-w-4xl">
                    <h2 class="text-3xl font-bold text-gray-800 mb-10">Who can Apply?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if(isset($scheme_json->eligibility->eligibility_criteria->key))
                        @foreach ($scheme_json->eligibility->eligibility_criteria->key as $criteria)
                        <div class="elegant-list-item" style="animation-delay: {{ $loop->index * 0.1 }}s">
                            <div class="elegant-icon-box"><i class="{{ $criteria->icon ?? 'fas fa-check' }}"></i></div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm mb-1 uppercase tracking-wider">{{ $criteria->title ?? '' }}</h4>
                                <p class="text-gray-500 text-sm">{{ $criteria->description ?? '' }}</p>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="elegant-list-item">
                            <div class="elegant-icon-box"><i class="fas fa-home"></i></div>
                            <div class="text-gray-700 font-medium">Permanent Resident of West Bengal</div>
                        </div>
                        @if(isset($scheme_json->eligibility->age))
                        <div class="elegant-list-item" style="animation-delay: 0.1s">
                            <div class="elegant-icon-box"><i class="fas fa-id-card"></i></div>
                            <div class="text-gray-700 font-medium">Age between {{ $scheme_json->eligibility->age->min ?? '0' }} - {{ $scheme_json->eligibility->age->max ?? '100' }} Years</div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ineligibility Panel -->
            <div id="panel-ineligibility" class="tab-panel hidden relative z-10">
                <div class="max-w-4xl">
                    <h2 class="text-3xl font-bold text-gray-800 mb-10">Exclusion Criteria</h2>
                    @php
                    $exclusionDesc = $scheme_json->eligibility->exclusion->description ?? 'Applicants receiving other government pensions or working in regular government jobs.';
                    $exclusions = explode('.', $exclusionDesc);
                    @endphp
                    <div class="space-y-4">
                        @foreach($exclusions as $ex)
                        @if(trim($ex))
                        <div class="elegant-list-item" style="animation-delay: {{ $loop->index * 0.1 }}s">
                            <div class="elegant-icon-box !bg-red-50 !text-red-500"><i class="fas fa-times"></i></div>
                            <div class="text-gray-600 font-medium">{{ trim($ex) }}.</div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Benefits Panel -->
            <div id="panel-benefits" class="tab-panel hidden relative z-10">
                <div class="max-w-4xl">
                    <h2 class="text-3xl font-bold text-gray-800 mb-10">Key Benefits</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if(isset($scheme_json->benefits))
                        @foreach($scheme_json->benefits as $benefit)
                        <div class="group/benefit p-8 rounded-[2.5rem] bg-gray-50 transition-all duration-500 border border-transparent hover:border-gray-200" onmouseover="this.style.backgroundColor='var(--primary-color)'" onmouseout="this.style.backgroundColor=''">
                            <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center mb-6 shadow-sm group-hover/benefit:scale-110 transition-transform" style="color: var(--primary-color);">
                                <i class="{{ $benefit->icon ?? 'fas fa-shield-heart' }} text-xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800 mb-3 group-hover/benefit:text-white transition-colors">{{ $benefit->title ?? '' }}</h4>
                            <p class="text-gray-500 text-sm leading-relaxed group-hover/benefit:text-white/80 transition-colors">{{ $benefit->description ?? '' }}</p>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Implementation Panel -->
            <div id="panel-implementation" class="tab-panel hidden relative z-10">
                <div class="max-w-4xl">
                    <h2 class="text-3xl font-bold text-gray-800 mb-10">Process Workflow</h2>
                    <div class="relative space-y-12 ml-6">
                        <div class="absolute left-[-24px] top-4 bottom-4 w-px bg-dashed bg-gray-200"></div>
                        @if(isset($scheme_json->workflow->steps))
                        @foreach($scheme_json->workflow->steps as $step)
                        <div class="relative flex items-start gap-8 group/step">
                            <div class="absolute left-[-36px] w-6 h-6 rounded-full bg-white border-4 z-10 group-hover/step:scale-125 transition-transform" style="border-color: var(--primary-color);"></div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 mb-2 uppercase tracking-widest text-xs" style="color: var(--primary-color);">{{ $step->name ?? 'Step' }}</h4>
                                <p class="text-gray-600 font-medium leading-relaxed">{{ $step->description ?? '' }}</p>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Instructions Panel -->
            <div id="panel-instructions" class="tab-panel hidden relative z-10">
                <div class="max-w-4xl">
                    <h2 class="text-3xl font-bold text-gray-800 mb-10">Documents Required</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if(isset($scheme_json->required->documents))
                        @foreach((array)$scheme_json->required->documents as $doc)
                        <div class="flex items-center gap-4 p-5 rounded-2xl bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-md transition-all">
                            <div class="w-10 h-10 rounded-xl bg-white text-orange-500 flex items-center justify-center shadow-sm">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <span class="text-gray-700 font-bold text-sm">{{ $doc }}</span>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    

        <!-- Scaled Support & Download Section -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 p-8 rounded-3xl shadow-lg flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden group" style="background-color: var(--primary-color); box-shadow: 0 10px 25px -5px var(--primary-soft);">

                <div class="relative z-10 text-white">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-white text-[10px] font-black uppercase tracking-[0.2em] mb-4 shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span> 24/7 Live Support
                    </div>
                    <h3 class="text-2xl font-black mb-2 tracking-tight">Technical Queries?</h3>
                    <p class="text-white/90 font-medium text-sm max-w-sm leading-relaxed">
                        Encountering issues with your application? Our dedicated helpdesk is ready to assist you.
                    </p>
                </div>

                <a href="tel:1800" class="relative z-10 bg-white px-6 py-4 rounded-2xl font-black text-xl flex items-center gap-4 hover:scale-105 active:scale-95 transition-all duration-300 shadow-xl group/btn border-2 border-transparent hover:border-white/50" style="color: var(--primary-color);">
                    <i class="fas fa-phone-alt animate-bounce text-xl"></i>
                    <div class="flex flex-col items-start leading-none text-gray-800">
                        <span class="text-[9px] uppercase tracking-widest opacity-60 mb-1 font-bold">Toll Free</span>
                        1800-XXX-XXXX
                    </div>
                </a>
            </div>

            <!-- Download Form Card -->
            <div class="bg-white p-8 rounded-3xl shadow-lg shadow-gray-200/50 flex flex-col items-center justify-center text-center border border-gray-100 group transition-all duration-500 relative overflow-hidden hover:border-gray-300">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500 relative" style="background-color: var(--primary-soft);">
                    <i class="fas fa-cloud-download-alt text-3xl relative z-10" style="color: var(--primary-color);"></i>
                </div>
                <h3 class="font-black text-xl text-gray-800 mb-1">Application Form</h3>
                <p class="text-gray-400 font-medium text-xs mb-6">Official PDF Document <br> (Size: 1.2 MB)</p>
                
                <a href="#" class="w-full py-3.5 rounded-xl bg-gray-900 text-white font-black text-sm transition-all duration-300 shadow-lg flex items-center justify-center gap-2 hover:opacity-90 hover:scale-105" style="box-shadow: 0 5px 15px -3px var(--primary-soft);">
                    <i class="fas fa-file-pdf"></i>
                    Get Form
                </a>
            </div>
        </div>
    </section>
</main>

@include('frontend.layouts.footer')
@endsection

@push('scripts')
<script>
    function formatIndianCompact(num) {
        if (typeof num !== 'number') num = parseInt(num) || 0;
        if (num >= 10000000) return (num / 10000000).toFixed(1) + "Cr+";
        if (num >= 100000) return (num / 100000).toFixed(1) + "L+";
        if (num >= 1000) return (num / 1000).toFixed(1) + "K+";
        return num;
    }

    function animateCounter(id, target, formatter) {
        let el = document.getElementById(id);
        if (!el) return;

        let targetNum = parseInt(target) || 0;
        if (targetNum === 0) {
            el.textContent = formatter(0);
            return;
        }

        let start = 0;
        let duration = 2000;
        let steps = 60;
        let increment = targetNum / steps;
        let current = 0;

        let timer = setInterval(() => {
            current += increment;
            if (current >= targetNum) {
                el.textContent = formatter(targetNum);
                clearInterval(timer);
            } else {
                el.textContent = formatter(Math.floor(current));
            }
        }, duration / steps);
    }

    function switchTab(tabId) {
        // Hide all panels
        document.querySelectorAll('.tab-panel').forEach(p => {
            p.classList.add('hidden');
            p.classList.remove('animate-up');
        });

        // Show selected panel
        const panel = document.getElementById('panel-' + tabId);
        if (panel) {
            panel.classList.remove('hidden');
            // Trigger reflow for animation
            void panel.offsetWidth;
            panel.classList.add('animate-up');
        }

        // Update tab buttons
        document.querySelectorAll('.premium-tab').forEach(b => b.classList.remove('active'));
        const activeTab = document.getElementById('tab-' + tabId);
        if (activeTab) activeTab.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', () => {
        // ... (existing counter code)
        const counters = [{
                id: 'beneficiariesCounter',
                formatter: formatIndianCompact
            },
            {
                id: 'allocationCounter',
                formatter: formatIndianCompact
            }
        ];

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    let target = entry.target.dataset.target;
                    let counterObj = counters.find(c => c.id === entry.target.id);
                    if (counterObj) {
                        animateCounter(entry.target.id, target, counterObj.formatter);
                    }
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        counters.forEach(c => {
            let el = document.getElementById(c.id);
            if (el) observer.observe(el);
        });
    });
</script>
@endpush