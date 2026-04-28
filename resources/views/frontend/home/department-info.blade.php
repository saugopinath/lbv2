@extends('frontend.layouts.app-template')

@section('content')
@include('frontend.components.top-header')
@include('frontend.components.header')

@php
$baseColor = $department_json->ref_color ?? 'indigo';
$gradientColor = $department_json->ref_gradient_color ?? 'emerald';
@endphp

<section id="wcd-department" class="max-w-7xl mx-auto px-4 py-12 font-poppins scrollbar-thin scrollbar-track-slate-100">

    {{-- Header Card --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="text-white px-6 py-8 bg-linear-to-r from-{{ $baseColor }}-500 to-{{ $gradientColor }}-700">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-center space-x-6 mb-4 md:mb-0">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-child text-3xl text-{{ $baseColor }}-600"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold">{{ $department_json->department_name }}</h1>
                        <p class="text-white/90">Government of West Bengal</p>
                    </div>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-lg px-6 py-3 text-center space-y-1">
                    <div class="text-2xl font-bold">{{ $department_json->tagline->line1 }}</div>
                    <div class="text-white/80">{{ $department_json->tagline->line2 }}</div>
                    <div class="text-sm text-white/60">{{ $department_json->tagline->line3 }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <!-- <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div
            class="rounded-lg p-4 text-center border shadow-md bg-linear-to-br from-{{ $baseColor }}-100 to-{{ $gradientColor }}-50 border-{{ $baseColor }}-200">
            <div class="text-2xl font-bold text-{{ $baseColor }}-800" data-count="{{ $ben_count_all }}">0</div>
            <div class="text-gray-600 text-sm">Applied Beneficiaries</div>
        </div>
        <div
            class="rounded-lg p-4 text-center border shadow-md bg-linear-to-br from-{{ $baseColor }}-100 to-{{ $gradientColor }}-50 border-{{ $baseColor }}-200">
            <div class="text-2xl font-bold text-{{ $baseColor }}-800" data-count="{{ $ben_count_approved }}">0</div>
            <div class="text-gray-600 text-sm">Approved Beneficiaries</div>
        </div>
        <div
            class="rounded-lg p-4 text-center border shadow-md bg-linear-to-br from-{{ $baseColor }}-100 to-{{ $gradientColor }}-50 border-{{ $baseColor }}-200">
            <div class="text-2xl font-bold text-{{ $baseColor }}-800" data-count="{{ $onboard_scheme_count }}">0</div>
            <div class="text-gray-600 text-sm">Schemes</div>
        </div>
        <div
            class="rounded-lg p-4 text-center border shadow-md bg-linear-to-br from-{{ $baseColor }}-100 to-{{ $gradientColor }}-50 border-{{ $baseColor }}-200">
            <div class="text-2xl font-bold text-{{ $baseColor }}-800" data-count="{{ $total_disbrusment }}"
                data-type="money">0</div>
            <div class="text-gray-600 text-sm">Monthly Disbursement</div>
        </div>
    </div> -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 - Applied Beneficiaries -->
        <div class="group relative rounded-xl p-5 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl bg-gradient-to-br from-blue-100 to-indigo-50 dark:from-blue-950/40 dark:to-indigo-950/40 border border-blue-200 dark:border-blue-800/50 overflow-hidden">
            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-blue-200/30 dark:bg-blue-500/10 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="text-3xl font-extrabold text-blue-700 dark:text-blue-300" data-count="{{ $ben_count_all }}">0</div>
                <div class="text-gray-600 dark:text-gray-300 text-sm font-medium mt-1">Applied Beneficiaries</div>
                <div class="mt-2 text-xs text-blue-500 dark:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity">↑ Total applications</div>
            </div>
        </div>

        <!-- Card 2 - Approved Beneficiaries -->
        <div class="group relative rounded-xl p-5 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl bg-gradient-to-br from-emerald-100 to-teal-50 dark:from-emerald-950/40 dark:to-teal-950/40 border border-emerald-200 dark:border-emerald-800/50 overflow-hidden">
            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-emerald-200/30 dark:bg-emerald-500/10 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="text-3xl font-extrabold text-emerald-700 dark:text-emerald-300" data-count="{{ $ben_count_approved }}">0</div>
                <div class="text-gray-600 dark:text-gray-300 text-sm font-medium mt-1">Approved Beneficiaries</div>
                <div class="mt-2 text-xs text-emerald-500 dark:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity">✓ Verified & approved</div>
            </div>
        </div>

        <!-- Card 3 - Schemes -->
        <div class="group relative rounded-xl p-5 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl bg-gradient-to-br from-purple-100 to-pink-50 dark:from-purple-950/40 dark:to-pink-950/40 border border-purple-200 dark:border-purple-800/50 overflow-hidden">
            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-purple-200/30 dark:bg-purple-500/10 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="text-3xl font-extrabold text-purple-700 dark:text-purple-300" data-count="{{ $onboard_scheme_count }}">0</div>
                <div class="text-gray-600 dark:text-gray-300 text-sm font-medium mt-1">Active Schemes</div>
                <div class="mt-2 text-xs text-purple-500 dark:text-purple-400 opacity-0 group-hover:opacity-100 transition-opacity">📋 Running programs</div>
            </div>
        </div>

        <!-- Card 4 - Monthly Disbursement -->
        <div class="group relative rounded-xl p-5 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl bg-gradient-to-br from-amber-100 to-orange-50 dark:from-amber-950/40 dark:to-orange-950/40 border border-amber-200 dark:border-amber-800/50 overflow-hidden">
            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-amber-200/30 dark:bg-amber-500/10 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="text-3xl font-extrabold text-amber-700 dark:text-amber-300" data-count="{{ $total_disbrusment }}" data-type="money">0</div>
                <div class="text-gray-600 dark:text-gray-300 text-sm font-medium mt-1">Monthly Disbursement</div>
                <div class="mt-2 text-xs text-amber-500 dark:text-amber-400 opacity-0 group-hover:opacity-100 transition-opacity">💰 This month's payout</div>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- About Section --}}
            <div
                class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border-l-4 border-{{ $baseColor }}-600 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-32 h-32 bg-{{ $baseColor }}-100 dark:bg-{{ $baseColor }}-900/20 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>

                <div class="relative z-10">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-5 flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-{{ $baseColor }}-100 dark:bg-{{ $baseColor }}-900/40 flex items-center justify-center mr-3">
                            <i class="fas fa-info-circle text-{{ $baseColor }}-600 dark:text-{{ $baseColor }}-400 text-lg"></i>
                        </div>
                        About the Department
                    </h2>

                    <p class="text-gray-600 dark:text-gray-300 mb-5 leading-relaxed">{{ $department_json->long }}</p>

                    <div class="rounded-xl p-5 bg-gradient-to-br from-{{ $baseColor }}-50 to-{{ $gradientColor }}-50 dark:from-{{ $baseColor }}-900/20 dark:to-{{ $gradientColor }}-900/20 border border-{{ $baseColor }}-100 dark:border-{{ $baseColor }}-800/50">
                        <h3 class="font-bold mb-3 text-{{ $baseColor }}-800 dark:text-{{ $baseColor }}-300 flex items-center">
                            <i class="fas fa-eye mr-2"></i> Vision & Mission:
                        </h3>
                        <div class="space-y-3">
                            @foreach($department_json->about->vision_mission as $vm)
                            <div class="flex items-start space-x-3 group/item">
                                <div class="w-8 h-8 rounded-lg bg-{{ $baseColor }}-200 dark:bg-{{ $baseColor }}-800/50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fas {{ $loop->first ? 'fa-bullseye' : 'fa-flag' }} text-{{ $baseColor }}-600 dark:text-{{ $baseColor }}-400 text-sm"></i>
                                </div>
                                <div>
                                    <strong class="text-gray-800 dark:text-gray-200">{{ $vm->title }}:</strong>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $vm->text }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Key Functions Section --}}
            <div
                class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border-l-4 border-{{ $baseColor }}-600 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 relative overflow-hidden">
                <div class="absolute bottom-0 right-0 w-40 h-40 bg-{{ $gradientColor }}-100 dark:bg-{{ $gradientColor }}-900/20 rounded-full -mb-20 -mr-20 group-hover:scale-150 transition-transform duration-500"></div>

                <div class="relative z-10">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-{{ $baseColor }}-100 dark:bg-{{ $baseColor }}-900/40 flex items-center justify-center mr-3">
                            <i class="fas fa-tasks text-{{ $baseColor }}-600 dark:text-{{ $baseColor }}-400 text-lg"></i>
                        </div>
                        Key Functions & Responsibilities
                    </h2>

                    <div class="grid grid-cols-1 gap-5">
                        @foreach($department_json->key_functions as $key_func)
                        <div class="flex items-start space-x-4 p-3 rounded-xl hover:bg-{{ $baseColor }}-50 dark:hover:bg-{{ $baseColor }}-900/20 transition-all duration-200 group/function">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br from-{{ $baseColor }}-200 to-{{ $baseColor }}-100 dark:from-{{ $baseColor }}-800/50 dark:to-{{ $baseColor }}-900/50 text-{{ $baseColor }}-700 dark:text-{{ $baseColor }}-300 shadow-sm group-hover/function:scale-110 transition-transform duration-200">
                                <i class="fas {{ $key_func->icon }} text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-1">{{ $key_func->title }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $key_func->text }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Major Initiatives Section --}}
            <div
                class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border-2 border-{{ $baseColor }}-200 dark:border-{{ $baseColor }}-400 border-l-4 border-l-{{ $baseColor }}-700 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-32 h-32 bg-{{ $baseColor }}-100 dark:bg-{{ $baseColor }}-900/20 rounded-full -ml-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>

                <div class="relative z-10">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-{{ $baseColor }}-100 dark:bg-{{ $baseColor }}-900/40 flex items-center justify-center mr-3">
                            <i class="fas fa-star text-{{ $baseColor }}-600 dark:text-{{ $baseColor }}-400 text-lg"></i>
                        </div>
                        Major Initiatives & Achievements
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($department_json->major_initiatives as $major)
                        <div class="group/card rounded-xl p-4 bg-gradient-to-br from-{{ $gradientColor }}-50 to-white dark:from-{{ $gradientColor }}-900/20 dark:to-gray-800/50 border border-{{ $gradientColor }}-100 dark:border-{{ $gradientColor }}-800/30 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <div class="flex items-start mb-3">
                                <div class="w-8 h-8 rounded-lg bg-{{ $baseColor }}-100 dark:bg-{{ $baseColor }}-900/40 flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas {{ $major->icon }} text-{{ $baseColor }}-700 dark:text-{{ $baseColor }}-400 text-sm"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200 leading-tight">{{ $major->name }}</h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 ml-11 leading-relaxed">{{ $major->description }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">

            {{-- Flagship Schemes --}}
            <div
                class="bg-white rounded-lg p-6 border-l-4 border-{{ $baseColor }}-600 hover:-translate-y-1 hover:shadow-xl transition-all">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-trophy mr-2 text-{{ $baseColor }}-600"></i>
                    Flagship Schemes
                </h2>
                <div class="space-y-4">
                    @foreach($department_json->flagship_schemes as $flag)
                    <div class="pl-4 border-l-4 border-{{ $flag->color ?? 'indigo' }}-500">
                        <h3 class="font-semibold text-gray-800">{{ $flag->name }}</h3>
                        <p class="text-gray-600 text-sm">{{ $flag->description }}</p>
                    </div>
                    @endforeach
                </div>
                <button id="viewAllSchemes"
                    class="w-full mt-4 text-white py-2 rounded-lg font-semibold transition bg-{{ $baseColor }}-700 hover:bg-{{ $baseColor }}-800">
                    <i class="fas fa-list mr-2"></i>View All Schemes
                </button>
            </div>

            {{-- Organizational Structure --}}
            <div
                class="bg-white rounded-lg p-6 border-l-4 border-{{ $baseColor }}-600 hover:-translate-y-1 hover:shadow-xl transition-all">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-sitemap mr-2 text-{{ $baseColor }}-600"></i>
                    Organizational Structure
                </h2>
                <ul class="space-y-2 text-gray-600">
                    @foreach($department_json->orgnizational_structure as $org)
                    <li class="flex items-center space-x-2">
                        <i class="fas {{ $org->icon }} text-{{ $baseColor }}-600"></i>
                        <span class="text-gray-700">{{ $org->title }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact --}}
            <div class="rounded-lg p-6 border shadow-md border-{{ $baseColor }}-200 bg-{{ $baseColor }}-50">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-address-card mr-2 text-{{ $baseColor }}-600"></i>
                    Contact Information
                </h2>
                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-map-marker-alt mt-1 text-{{ $baseColor }}-600"></i>
                        <div>
                            <div class="font-semibold text-gray-800">Head Office</div>
                            <div class="text-sm text-gray-600">{{ $department_json->contact->address }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-phone mt-1 text-{{ $baseColor }}-600"></i>
                        <div>
                            <div class="font-semibold text-gray-800">Helpline</div>
                            @foreach($department_json->contact->helplines as $help)
                            <div class="text-gray-600">{{ $help }}</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-envelope mt-1 text-{{ $baseColor }}-600"></i>
                        <div>
                            <div class="font-semibold text-gray-800">Email</div>
                            <div class="text-sm text-gray-600">{{ $department_json->contact->email }}</div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-globe mt-1 text-{{ $baseColor }}-600"></i>
                        <div>
                            <div class="font-semibold text-gray-800">Website</div>
                            <div class="text-sm text-gray-600">{{ $department_json->contact->website }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Schemes Modal --}}
    <div id="schemesModal" style="display:none;" class="fixed inset-0 bg-black/50 items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div
                class="sticky top-0 text-white px-6 py-4 flex justify-between items-center bg-linear-to-r from-{{ $baseColor }}-800 to-{{ $gradientColor }}-500">
                <h2 class="text-2xl font-bold">All Government Schemes</h2>
                <button id="closeModal" class="text-white text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="grid gap-6 [grid-template-columns:repeat(auto-fill,minmax(300px,1fr))]">
                    @forelse($department_json->all_schemes ?? [] as $s)
                    @php $sColorName = $s->color ?? 'indigo'; @endphp
                    <div class="border rounded-lg p-4 bg-{{ $sColorName }}-500/5 border-{{ $sColorName }}-500/20">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-white mr-3 bg-{{ $sColorName }}-500">
                                <i class="fas {{ $s->icon ?? 'fa-circle' }}"></i>
                            </div>
                            <h3 class="font-bold text-lg text-{{ $sColorName }}-600">{{ $s->name }}</h3>
                        </div>
                        <p class="text-sm mb-3 text-{{ $sColorName }}-700">{{ $s->description ?? '' }}</p>
                        <div class="flex justify-between text-xs text-{{ $sColorName }}-600">
                            <span>{{ $s->benefit ?? '' }}</span>
                            <span>{{ $s->target_group ?? '' }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 col-span-full text-center py-8">No schemes available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</section>

@include('frontend.layouts.footer')
@endsection

@push('scripts')
<script>
    function formatCountCompact(num) {
        if (num >= 10000000) return (num / 10000000).toFixed(1).replace(/\.0$/, '') + 'Cr';
        if (num >= 100000) return (num / 100000).toFixed(1).replace(/\.0$/, '') + 'L';
        if (num >= 1000) return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
        return num.toLocaleString('en-IN');
    }

    function formatMoneyCompact(num) {
        if (num >= 10000000) return '₹' + (num / 10000000).toFixed(1).replace(/\.0$/, '') + 'Cr';
        if (num >= 100000) return '₹' + (num / 100000).toFixed(1).replace(/\.0$/, '') + 'L';
        if (num >= 1000) return '₹' + (num / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
        return '₹' + num.toLocaleString('en-IN');
    }

    function animateCountElement(el, target, opts = {}) {
        const isMoney = !!opts.money;
        const duration = opts.duration || 1800;
        let start = 0;
        target = parseInt(target) || 0;
        if (target === 0) {
            el.textContent = isMoney ? formatMoneyCompact(0) : formatCountCompact(0);
            return;
        }
        const steps = Math.min(60, Math.max(20, Math.floor(duration / 50)));
        const increment = Math.ceil(target / steps);
        const stepTime = Math.max(10, Math.floor(duration / steps));
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                clearInterval(timer);
                el.textContent = isMoney ? formatMoneyCompact(target) : formatCountCompact(target);
                return;
            }
            el.textContent = isMoney ? formatMoneyCompact(start) : formatCountCompact(start);
        }, stepTime);
    }

    function openModal() {
        const modal = document.getElementById('schemesModal');
        modal.style.display = 'flex';
        document.documentElement.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('schemesModal');
        modal.style.display = 'none';
        document.documentElement.style.overflow = '';
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-count]').forEach(el => {
            const target = el.getAttribute('data-count');
            const type = el.getAttribute('data-type') || 'count';
            animateCountElement(el, target, {
                money: type === 'money'
            });
        });

        const modal = document.getElementById('schemesModal');
        const openBtn = document.getElementById('viewAllSchemes');
        const closeBtn = document.getElementById('closeModal');

        if (openBtn) openBtn.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);

        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        }

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal && modal.style.display === 'flex') closeModal();
        });
    });
</script>
@endpush