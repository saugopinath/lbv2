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
            <div class="text-white px-6 py-8 bg-linear-to-r from-{{ $baseColor }}-800 to-{{ $gradientColor }}-500">
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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
        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Left Column --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- About --}}
                <div
                    class="bg-white rounded-lg p-6 border-l-4 border-{{ $baseColor }}-600 hover:-translate-y-1 hover:shadow-xl transition-all">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-{{ $baseColor }}-600"></i>
                        About the Department
                    </h2>
                    <p class="text-gray-600 mb-4">{{ $department_json->long }}</p>
                    <div class="border rounded-lg p-4 bg-{{ $baseColor }}-50 border-{{ $baseColor }}-100">
                        <h3 class="font-semibold mb-2 text-{{ $baseColor }}-800">Vision & Mission:</h3>
                        <ul class="space-y-2 text-{{ $baseColor }}-900">
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-bullseye mt-1 text-{{ $baseColor }}-600"></i>
                                <div>
                                    <strong
                                        class="text-gray-800">{{ $department_json->about->vision_mission[0]->title }}:</strong>
                                    <span
                                        class="text-gray-700">{{ $department_json->about->vision_mission[0]->text }}</span>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-flag mt-1 text-{{ $baseColor }}-600"></i>
                                <div>
                                    <strong
                                        class="text-gray-800">{{ $department_json->about->vision_mission[1]->title }}:</strong>
                                    <span
                                        class="text-gray-700">{{ $department_json->about->vision_mission[1]->text }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Key Functions --}}
                <div
                    class="bg-white rounded-lg p-6 border-l-4 border-{{ $baseColor }}-600 hover:-translate-y-1 hover:shadow-xl transition-all">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-tasks mr-2 text-{{ $baseColor }}-600"></i>
                        Key Functions & Responsibilities
                    </h2>
                    <div class="space-y-4">
                        @foreach($department_json->key_functions as $key_func)
                            <div class="flex items-start space-x-4">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center bg-linear-to-br from-{{ $baseColor }}-200 to-{{ $baseColor }}-100 text-{{ $baseColor }}-700">
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

                {{-- Major Initiatives --}}
                <div
                    class="bg-white rounded-lg p-6 border-l-4 border-{{ $baseColor }}-600 hover:-translate-y-1 hover:shadow-xl transition-all">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-star mr-2 text-{{ $baseColor }}-600"></i>
                        Major Initiatives & Achievements
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($department_json->major_initiatives as $major)
                            <div class="rounded-lg p-4 bg-{{ $gradientColor }}-100/50">
                                <div class="flex items-center mb-2">
                                    <i class="fas {{ $major->icon }} mr-2 text-{{ $baseColor }}-700"></i>
                                    <h3 class="font-semibold text-{{ $baseColor }}-800">{{ $major->name }}</h3>
                                </div>
                                <p class="text-sm text-gray-600">{{ $major->description }}</p>
                            </div>
                        @endforeach
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
                animateCountElement(el, target, { money: type === 'money' });
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