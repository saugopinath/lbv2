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
        --primary-soft: rgba($primaryRgb, 0.1);
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

    .text-custom {
        color: var(--primary-color);
    }

    .bg-custom {
        background-color: var(--primary-color);
    }

    .prose a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
    }

    .prose a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .hero-section {
            border-radius: 0 0 2rem 2rem;
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
            <div class="stat-card animate-up" style="animation-delay: 0.2s">
                <div class="icon-box mb-4"><i class="fas fa-calendar-alt"></i></div>
                <div class="text-2xl font-bold text-gray-800">{{ $scheme_json->quick_stats->eligibility_age ?? 'All Ages' }}</div>
                <div class="text-gray-500 font-medium">Eligible Age</div>
            </div>

            <!-- Caste/Criteria -->
            <div class="stat-card animate-up" style="animation-delay: 0.3s">
                <div class="icon-box mb-4"><i class="fas fa-users"></i></div>
                <div class="text-2xl font-bold text-gray-800">
                    @if(isset($scheme_id) && in_array($scheme_id, [1, 3, 19]))
                    {{ $scheme_json->quick_stats->eligibility_caste ?? 'All' }}
                    @elseif (isset($scheme_id) && $scheme_id == 2)
                    {{ $scheme_json->quick_stats->eligibility_criteria ?? 'Criteria' }}
                    @else
                    {{ $scheme_json->quick_stats->eligibility_criteria ?? 'Universal' }}
                    @endif
                </div>
                <div class="text-gray-500 font-medium">Criteria</div>
            </div>

            <!-- Beneficiaries -->
            <div class="stat-card animate-up" style="animation-delay: 0.4s">
                <div class="icon-box mb-4"><i class="fas fa-heart"></i></div>
                <div id="beneficiariesCounter" class="text-2xl font-bold text-gray-800" data-target="{{ $ben_count ?? 0 }}">0</div>
                <div class="text-gray-500 font-medium">Beneficiaries</div>
            </div>

            <!-- Allocation -->
            <div class="stat-card animate-up" style="animation-delay: 0.5s">
                <div class="icon-box mb-4"><i class="fas fa-chart-line"></i></div>
                <div id="allocationCounter" class="text-2xl font-bold text-gray-800" data-target="{{ $scheme_json->quick_stats->allocation ?? 0 }}">0</div>
                <div class="text-gray-500 font-medium">Monthly Outlay</div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-20">
        <!-- Left Side -->
        <div class="lg:col-span-2 space-y-8">
            <!-- About -->
            <div class="content-card animate-up" style="animation-delay: 0.6s">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: var(--primary-soft); color: var(--primary-color);">
                        <i class="fas fa-info-circle text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">About the Scheme</h2>
                </div>
                <div class="prose prose-slate max-w-none text-gray-700 leading-relaxed text-base md:text-lg">
                    {{ $scheme_json->about->long ?? 'No detailed description available.' }}
                </div>
            </div>

            <!-- Eligibility -->
            @if(isset($scheme_json->eligibility->eligibility_criteria->key) || isset($scheme_json->eligibility->criteria))
            <div class="content-card animate-up" style="animation-delay: 0.7s">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-check-double text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Eligible Cita</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(isset($scheme_json->eligibility->eligibility_criteria->key))
                    @foreach ($scheme_json->eligibility->eligibility_criteria->key as $criteria)
                    <div class="flex items-start gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-colors group">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-600 group-hover:bg-white group-hover:shadow-md transition-all">
                            <i class="{{ $criteria->icon ?? 'fas fa-check' }}"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-1">{{ $criteria->title ?? 'Criterion' }}</h3>
                            <p class="text-gray-500 text-sm leading-snug">{{ $criteria->description ?? '' }}</p>
                        </div>
                    </div>
                    @endforeach
                    @elseif(isset($scheme_json->eligibility->criteria))
                    @foreach((array)$scheme_json->eligibility->criteria as $key => $value)
                    @if(is_bool($value) && $value)
                    <div class="flex items-start gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-colors group">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-600 group-hover:bg-white group-hover:shadow-md transition-all">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-1">{{ ucwords(str_replace('_', ' ', $key)) }}</h3>
                            <p class="text-gray-500 text-sm leading-snug">Required for application.</p>
                        </div>
                    </div>
                    @endif
                    @endforeach
                    @endif
                </div>
            </div>
            @endif

            <!-- Required Documents -->
            @if(isset($scheme_json->required->documents) && count((array)$scheme_json->required->documents) > 0)
            <div class="content-card animate-up" style="animation-delay: 0.75s">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fas fa-file-invoice text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Required Documents</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach((array)$scheme_json->required->documents as $doc)
                    <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-gray-200 bg-gray-50/50">
                        <i class="fas fa-file-alt text-amber-500"></i>
                        <span class="text-gray-700 font-medium text-sm">{{ $doc }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Side -->
        <div class="space-y-8">
            <!-- Application Process -->
            @if(isset($scheme_json->workflow->steps))
            <div class="content-card animate-up" style="animation-delay: 0.8s">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: var(--primary-soft); color: var(--primary-color);">
                        <i class="fas fa-list-ol text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">How to Apply</h2>
                </div>

                <div class="space-y-8 relative ml-2">
                    <!-- Connecting Line -->
                    <div class="absolute left-[15px] top-[40px] bottom-[40px] w-[2px] bg-gray-100"></div>

                    @foreach($scheme_json->workflow->steps as $step)
                    <div class="flex items-start gap-6 relative z-10 group/step">
                        <div class="step-number shadow-md group-hover/step:scale-110 transition-transform">{{ $step->rank ?? $loop->iteration }}</div>
                        <div class="flex-1 pt-0.5">
                            <h3 class="font-bold text-gray-800 mb-1 group-hover/step:text-custom transition-colors leading-tight">{{ $step->name ?? 'Step' }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed">{{ $step->description ?? '' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-10">
                    <a href="#" class="cta-button w-full justify-center">
                        <i class="fas fa-download"></i>
                        Download Application Form
                    </a>
                </div>
            </div>
            @endif

            <!-- Support Card -->
            <div class="content-card text-white animate-up border-none overflow-hidden relative bg-gradient-to-br from-indigo-600 to-slate-800" style="animation-delay: 0.9s">
                <!-- Background decorative blur -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>

                <div class="relative z-10">
                    <!-- Header with icon -->
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">24/7 Support Center</span>
                        </div>
                    </div>

                    <!-- Main text -->
                    <h3 class="text-2xl font-bold mb-2 text-gray-700">Need Assistance?</h3>
                    <p class="text-indigo-600 mb-8 text-sm leading-relaxed">
                        Our support team is available around the clock to help you. Call us for any questions or concerns.
                    </p>

                    <!-- Call button -->
                    <a href="tel:1800" class="flex items-center justify-between font-bold text-lg bg-white text-indigo-600 p-4 rounded-2xl hover:scale-[1.02] active:scale-[0.98] transition-all group shadow-xl">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-phone-alt group-hover:rotate-12 transition-transform"></i>
                            1800-XXX-XXXX
                        </span>
                        <i class="fas fa-arrow-right text-sm opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                    </a>
                </div>
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

    document.addEventListener('DOMContentLoaded', () => {
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