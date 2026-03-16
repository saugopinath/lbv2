@extends('frontend.layouts.app-template')

@push('styles')
    <!-- Poppins font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')
    @php
        $colorMap = [
            'pink' => '#ec4899',
            'indigo' => '#6366f1',
            'green' => '#22c55e',
            'orange' => '#f97316',
            'violet' => '#8b5cf6',
            'lime' => '#84cc16',
            'sky' => '#0ea5e9',
            'amber' => '#f59e0b',
            'fuchsia' => '#d946ef',
            'rose' => '#f43f5e',
            'emerald' => '#10b981',
            'blue' => '#3b82f6',
            'teal' => '#14b8a6',
            'red' => '#ef4444',
            'yellow' => '#eab308'
        ];
        $refColor = $department_json->ref_color ?? 'indigo';
        $refGradientColor = $department_json->ref_gradient_color ?? 'blue';

        $baseColor = $colorMap[$refColor] ?? '#6366f1';
        $gradientColor = $colorMap[$refGradientColor] ?? '#3b82f6';
    @endphp

    <!-- Top headers -->
    @include('frontend.components.top-header')
    @include('frontend.components.header')

    <!-- Department Section -->
    <section id="wcd-department" class="max-w-7xl mx-auto px-4 py-12 font-poppins scrollbar-thin scrollbar-track-slate-100"
        style="scrollbar-color: {{ $baseColor }}80 #f1f5f9;">
        <!-- Header Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="text-white px-6 py-8"
                style="background: linear-gradient(to right, {{ $baseColor }}, {{ $gradientColor }});">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center space-x-6 mb-4 md:mb-0">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-child text-3xl" style="color: {{ $baseColor }}"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold">{{ $department_json->department_name }}</h1>
                            <p class="" style="color: {{ $baseColor }}33">Government of West Bengal</p>
                        </div>
                    </div>

                    <div class="bg-white bg-opacity-20 rounded-lg px-6 py-3 text-center space-y-1">
                        <div class="text-2xl font-bold">{{ $department_json->tagline->line1 }}</div>
                        <div class="" style="color: #ffffffcc">{{ $department_json->tagline->line2 }}</div>
                        <div class="text-sm" style="color: #ffffff99">{{ $department_json->tagline->line3 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Applied -->
            <div class="rounded-lg p-4 text-center border shadow-md"
                style="background: linear-gradient(to bottom right, {{ $baseColor }}0d, {{ $baseColor }}1a); border-color: {{ $baseColor }}33">
                <div class="text-2xl font-bold" style="color: {{ $baseColor }}" data-count="{{ $ben_count_all }}">0</div>
                <div class="text-gray-600 text-sm">Applied Beneficiaries</div>
            </div>

            <!-- Approved -->
            <div class="rounded-lg p-4 text-center border shadow-md"
                style="background: linear-gradient(to bottom right, {{ $baseColor }}0d, {{ $baseColor }}1a); border-color: {{ $baseColor }}33">
                <div class="text-2xl font-bold" style="color: {{ $baseColor }}" data-count="{{ $ben_count_approved }}">0
                </div>
                <div class="text-gray-600 text-sm">Approved Beneficiaries</div>
            </div>

            <!-- Schemes -->
            <div class="rounded-lg p-4 text-center border shadow-md"
                style="background: linear-gradient(to bottom right, {{ $baseColor }}0d, {{ $baseColor }}1a); border-color: {{ $baseColor }}33">
                <div class="text-2xl font-bold" style="color: {{ $baseColor }}" data-count="{{ $onboard_scheme_count }}">0
                </div>
                <div class="text-gray-600 text-sm">Schemes</div>
            </div>

            <!-- Monthly Disbursement (money type) -->
            <div class="rounded-lg p-4 text-center border shadow-md"
                style="background: linear-gradient(to bottom right, {{ $baseColor }}0d, {{ $baseColor }}1a); border-color: {{ $baseColor }}33">
                <div class="text-2xl font-bold" style="color: {{ $baseColor }}" data-count="{{ $total_disbrusment }}"
                    data-type="money">0</div>
                <div class="text-gray-600 text-sm">Monthly Disbursement</div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left column -->
            <div class="lg:col-span-2 space-y-6">

                <!-- About -->
                <div class="bg-white rounded-lg p-6 border-l-4 hover:-translate-y-1 hover:shadow-xl transition-all"
                    style="border-color: {{ $baseColor }}">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2" style="color: {{ $baseColor }}"></i>
                        About the Department
                    </h2>
                    <p class="text-gray-600 mb-4">{{ $department_json->long }}</p>

                    <div class="border rounded-lg p-4"
                        style="background-color: {{ $baseColor }}0d; border-color: {{ $baseColor }}33">
                        <h3 class="font-semibold mb-2" style="color: {{ $baseColor }}">Vision & Mission:</h3>
                        <ul class="space-y-2" style="color: {{ $baseColor }}">
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-bullseye mt-1" style="color: {{ $baseColor }}"></i>
                                <div>
                                    <strong>{{ $department_json->about->vision_mission[0]->title }}:</strong>
                                    <span class="text-gray-700">
                                        {{ $department_json->about->vision_mission[0]->text }}</span>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-flag mt-1" style="color: {{ $baseColor }}"></i>
                                <div>
                                    <strong>{{ $department_json->about->vision_mission[1]->title }}:</strong>
                                    <span class="text-gray-700">
                                        {{ $department_json->about->vision_mission[1]->text }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Key Functions -->
                <div class="bg-white rounded-lg p-6 border-l-4 hover:-translate-y-1 hover:shadow-xl transition-all"
                    style="border-color: {{ $baseColor }}">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-tasks mr-2" style="color: {{ $baseColor }}"></i>
                        Key Functions & Responsibilities
                    </h2>

                    <div class="space-y-4">
                        @foreach($department_json->key_functions as $key_func)
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                    style="background: linear-gradient(to bottom right, {{ $baseColor }}4d, {{ $baseColor }}1a); color: {{ $baseColor }}">
                                    <i class="fas {{ $key_func->icon }}"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $key_func->title }}</h3>
                                    <p class="text-gray-600">{{ $key_func->text }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Major Initiatives -->
                <div class="bg-white rounded-lg p-6 border-l-4 hover:-translate-y-1 hover:shadow-xl transition-all"
                    style="border-color: {{ $baseColor }}">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-star mr-2" style="color: {{ $baseColor }}"></i>
                        Major Initiatives & Achievements
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($department_json->major_initiatives as $major)
                            <div class="rounded-lg p-4" style="background-color: {{ $gradientColor }}0d">
                                <div class="flex items-center mb-2">
                                    <i class="fas {{ $major->icon }} mr-2" style="color: {{ $gradientColor }}"></i>
                                    <h3 class="font-semibold" style="color: {{ $gradientColor }}">{{ $major->name }}</h3>
                                </div>
                                <p class="text-sm" style="color: {{ $gradientColor }}">{{ $major->description }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right column -->
            <div class="space-y-6">

                <!-- Flagship schemes -->
                <div class="bg-white rounded-lg p-6 border-l-4 hover:-translate-y-1 hover:shadow-xl transition-all"
                    style="border-color: {{ $baseColor }}">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-trophy mr-2" style="color: {{ $baseColor }}"></i>
                        Flagship Schemes
                    </h2>

                    <div class="space-y-4">
                        @foreach($department_json->flagship_schemes as $flag)
                            @php
                                $flagColor = $colorMap[$flag->color ?? 'indigo'] ?? '#6366f1';
                            @endphp
                            <div class="pl-4 border-l-4" style="border-color: {{ $flagColor }}">
                                <h3 class="font-semibold text-gray-800">{{ $flag->name }}</h3>
                                <p class="text-gray-600 text-sm">{{ $flag->description }}</p>
                            </div>
                        @endforeach
                    </div>

                    <button id="viewAllSchemes" class="w-full mt-4 text-white py-2 rounded-lg font-semibold transition"
                        style="background-color: {{ $baseColor }}" onmouseover="this.style.opacity='0.9'"
                        onmouseout="this.style.opacity='1'">
                        <i class="fas fa-list mr-2"></i>View All Schemes
                    </button>
                </div>

                <!-- Organizational structure -->
                <div class="bg-white rounded-lg p-6 border-l-4 hover:-translate-y-1 hover:shadow-xl transition-all"
                    style="border-color: {{ $baseColor }}">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sitemap mr-2" style="color: {{ $baseColor }}"></i>
                        Organizational Structure
                    </h2>

                    <ul class="space-y-2 text-gray-600">
                        @foreach($department_json->orgnizational_structure as $org)
                            <li class="flex items-center space-x-2">
                                <i class="fas {{ $org->icon }}" style="color: {{ $baseColor }}"></i>
                                <span class="text-gray-700">{{ $org->title }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Contact -->
                <div class="rounded-lg p-6 border shadow-md"
                    style="border-color: {{ $baseColor }}33; background-color: {{ $baseColor }}0d">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-address-card mr-2" style="color: {{ $baseColor }}"></i>
                        Contact Information
                    </h2>

                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt mt-1" style="color: {{ $baseColor }}"></i>
                            <div>
                                <div class="font-semibold">Head Office</div>
                                <div class="text-sm" style="color: {{ $baseColor }}">
                                    {{ $department_json->contact->address }}</div>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <i class="fas fa-phone mt-1" style="color: {{ $baseColor }}"></i>
                            <div>
                                <div class="font-semibold">Helpline</div>
                                @foreach($department_json->contact->helplines as $help)
                                    <div style="color: {{ $baseColor }}">{{ $help }}</div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <i class="fas fa-envelope mt-1" style="color: {{ $baseColor }}"></i>
                            <div>
                                <div class="font-semibold">Email</div>
                                <div class="text-sm" style="color: {{ $baseColor }}">{{ $department_json->contact->email }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <i class="fas fa-globe mt-1" style="color: {{ $baseColor }}"></i>
                            <div>
                                <div class="font-semibold">Website</div>
                                <div class="text-sm" style="color: {{ $baseColor }}">
                                    {{ $department_json->contact->website }}</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal (hidden by default) -->
        <div id="schemesModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[1000]">
            <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 text-white px-6 py-4 flex justify-between items-center"
                    style="background: linear-gradient(to right, {{ $baseColor }}, {{ $gradientColor }});">
                    <h2 class="text-2xl font-bold">All Government Schemes</h2>
                    <button id="closeModal" class="text-white text-2xl" id="closeButton">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6">
                    <div class="grid gap-6 [grid-template-columns:repeat(auto-fill,minmax(300px,1fr))]">
                        <!-- Example Scheme Cards (use dynamic data similarly) -->
                        <div class="border rounded-lg p-4"
                            style="background-color: {{ $baseColor }}0d; border-color: {{ $baseColor }}33">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white mr-3"
                                    style="background-color: {{ $baseColor }}">
                                    <i class="fas fa-female"></i>
                                </div>
                                <h3 class="font-bold text-lg" style="color: {{ $baseColor }}">Lakshmir Bhandar</h3>
                            </div>
                            <p class="text-sm mb-3" style="color: {{ $baseColor }}">Monthly financial assistance of
                                ₹500-₹1000 to women heads of families</p>
                            <div class="flex justify-between text-xs" style="color: {{ $baseColor }}">
                                <span><i class="fas fa-rupee-sign mr-1"></i>₹500-1000/month</span>
                                <span><i class="fas fa-users mr-1"></i>Women</span>
                            </div>
                        </div>

                        <!-- Other sample cards (you can loop real data here) -->
                        @foreach($department_json->all_schemes ?? [] as $s)
                            @php
                                $sColor = $colorMap[$s->color ?? 'indigo'] ?? '#6366f1';
                            @endphp
                            <div class="border rounded-lg p-4"
                                style="background-color: {{ $sColor }}0d; border-color: {{ $sColor }}33">
                                <div class="flex items-center mb-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white mr-3"
                                        style="background-color: {{ $sColor }}">
                                        <i class="fas {{ $s->icon ?? 'fa-circle' }}"></i>
                                    </div>
                                    <h3 class="font-bold text-lg" style="color: {{ $sColor }}">{{ $s->name }}</h3>
                                </div>
                                <p class="text-sm mb-3" style="color: {{ $sColor }}">{{ $s->description ?? '' }}</p>
                                <div class="flex justify-between text-xs" style="color: {{ $sColor }}">
                                    <span>{{ $s->benefit ?? '' }}</span>
                                    <span>{{ $s->target_group ?? '' }}</span>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- Footer -->
    @include('frontend.layouts.footer')
@endsection

@push('scripts')
    <script>
        // Format numbers in Indian style with compact suffixes
        function formatCountCompact(num) {
            if (num >= 10000000) return (num / 10000000).toFixed(1).replace(/\.0$/, "") + "Cr";
            if (num >= 100000) return (num / 100000).toFixed(1).replace(/\.0$/, "") + "L";
            if (num >= 1000) return (num / 1000).toFixed(1).replace(/\.0$/, "") + "K";
            return num.toLocaleString('en-IN');
        }

        // Format money specifically (adds ₹ prefix)
        function formatMoneyCompact(num) {
            if (num >= 10000000) return "₹" + (num / 10000000).toFixed(1).replace(/\.0$/, "") + "Cr";
            if (num >= 100000) return "₹" + (num / 100000).toFixed(1).replace(/\.0$/, "") + "L";
            if (num >= 1000) return "₹" + (num / 1000).toFixed(1).replace(/\.0$/, "") + "K";
            return "₹" + num.toLocaleString('en-IN');
        }

        // Generic animate function safe for large numbers
        function animateCountElement(el, target, opts = {}) {
            const isMoney = !!opts.money;
            const duration = opts.duration || 1800; // ms
            let start = 0;
            // handle target 0 or NaN
            target = parseInt(target) || 0;
            if (target === 0) {
                el.textContent = isMoney ? formatMoneyCompact(0) : formatCountCompact(0);
                return;
            }

            // choose steps count (not too many updates if target huge)
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

        document.addEventListener('DOMContentLoaded', () => {
            // animate all data-count elements
            document.querySelectorAll('[data-count]').forEach(el => {
                const target = el.getAttribute('data-count');
                const type = el.getAttribute('data-type') || 'count';
                if (type === 'money') {
                    animateCountElement(el, target, { money: true });
                } else {
                    animateCountElement(el, target, { money: false });
                }
            });

            // Modal open/close
            const modal = document.getElementById('schemesModal');
            const openBtn = document.getElementById('viewAllSchemes');
            const closeBtn = document.getElementById('closeModal');

            if (openBtn && modal) {
                openBtn.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    // lock body scroll
                    document.documentElement.style.overflow = 'hidden';
                });
            }
            if (closeBtn && modal) {
                closeBtn.addEventListener('click', () => {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                    document.documentElement.style.overflow = '';
                });
            }
            // Close modal on backdrop click
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.remove('flex');
                        modal.classList.add('hidden');
                        document.documentElement.style.overflow = '';
                    }
                });
            }
        });
    </script>
@endpush