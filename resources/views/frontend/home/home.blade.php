    @extends('frontend.layouts.app-template')
    @section('title') | Home @endsection
    @section('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    @endsection

    @section('content')

    <!-- <body class="bg-gray-50 text-gray-800"> -->
    <!-- Top Accessibility Bar -->
    @include('frontend.components.top-header')

    <!-- Main Header -->
    @include('frontend.components.header')

    <!-- Hero Banner -->
    <section class="relative overflow-hidden h-[500px] md:h-[600px]">

        <div id="hero-carousel" class="relative h-full">



            @foreach ($data['home_image'] as $key => $value)
            @include('frontend.components.carousel-img', [
            'image' => $value['image'],
            'title' => $value['title'],
            'header' => $value['header']
            ])
            @endforeach
            <!-- Prev -->
            <button id="prevBtn"
                class="absolute left-5 top-1/2 -translate-y-1/2 z-50 bg-white/30 hover:bg-white/60 
                            text-gray-800 p-3 rounded-full backdrop-blur-md transition shadow">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <!-- Next -->
            <button id="nextBtn"
                class="absolute right-5 top-1/2 -translate-y-1/2 z-50 bg-white/30 hover:bg-white/60 
                            text-gray-800 p-3 rounded-full backdrop-blur-md transition shadow">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <!-- Indicators (Dynamic) -->
            <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-2 z-50">
                @foreach ($data['home_image'] as $k => $v)
                <button class="carousel-indicator w-3 h-3 rounded-full bg-white/40 hover:bg-white transition"
                    data-slide="{{ $k - 1 }}">
                </button>
                @endforeach
            </div>

        </div>
    </section>


    <!-- Noytification -->
    <!-- Modern Notification Marquee -->
    @include('frontend.components.notification-h')

    <!-- Statistics -->
    <section class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">

        <div>
            <div class="text-3xl font-bold text-indigo-600"
                id="deptCounter"
                data-target="{{ $total_dept }}">
                0
            </div>
            <p class="mt-2 text-sm">Total Department</p>
        </div>

        <div>
            <div class="text-3xl font-bold text-green-600"
                id="schemeCounter"
                data-target="{{ $total_schemes }}">
                0
            </div>
            <p class="mt-2 text-sm">Total Schemes</p>
        </div>

        <div>
            <div class="text-3xl font-bold text-amber-600"
                id="beneficiaryCounter"
                data-target="{{ $ben_count }}">
                0
            </div>
            <p class="mt-2 text-sm">Total Beneficiaries</p>
        </div>

        <div>
            <div class="text-3xl font-bold text-pink-600"
                id="disbursementCounter"
                data-target="{{ $monthly_disbursement }}">
                0
            </div>
            <p class="mt-2 text-sm">Monthly Disbursement</p>
        </div>

    </section>

    <section class="max-w-7xl mx-auto mb-12 grid md:grid-cols-3 gap-8 px-4">
        <!-- Scheme Rationale -->
        <div class="relative overflow-hidden bg-blue-50/80 p-8 rounded-2xl shadow-sm border border-blue-100 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <h3 id="about" class="font-bold text-xl text-blue-900">
                    Scheme Rationale
                </h3>
            </div>
            <p class="text-gray-700 leading-relaxed mb-6 text-sm">
                The scheme aims to consolidate various welfare initiatives
                of the Government of West Bengal under a single umbrella to ensure
                efficiency, transparency, and greater citizen access.
            </p>
            <!-- <a href="#" class="inline-flex items-center text-blue-600 font-semibold text-sm hover:underline group-hover:gap-2 transition-all">
                Read More <span class="ml-1">→</span>
            </a> -->
            <!-- Decorative Background Pattern -->
            <div class="absolute -bottom-5 -right-5 text-blue-300 opacity-20 pointer-events-none group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                <i class="fas fa-users text-9xl"></i>
            </div>
        </div>

        <!-- Objectives -->
        <div class="relative overflow-hidden bg-green-50/80 p-8 rounded-2xl shadow-sm border border-green-100 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 bg-green-600 rounded-full flex items-center justify-center shadow-lg shadow-green-200">
                    <i class="fas fa-bullseye text-white text-xl"></i>
                </div>
                <h3 id="objectives" class="font-bold text-xl text-green-900">
                    Objectives
                </h3>
            </div>
            <p class="text-gray-700 leading-relaxed mb-6 text-sm">
                The scheme provides financial, social, and developmental assistance
                for youth, women, elderly, and marginalized communities, ensuring
                holistic empowerment and inclusion.
            </p>
            <!-- <a href="#" class="inline-flex items-center text-green-600 font-semibold text-sm hover:underline group-hover:gap-2 transition-all">
                Read More <span class="ml-1">→</span>
            </a> -->
            <!-- Decorative Background Pattern -->
            <div class="absolute -bottom-5 -right-5 text-green-300 opacity-20 pointer-events-none group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                <i class="fas fa-bullseye text-9xl"></i>
            </div>
        </div>

        <!-- Scheme Design -->
        <div class="relative overflow-hidden bg-amber-50/80 p-8 rounded-2xl shadow-sm border border-amber-100 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 bg-amber-500 rounded-full flex items-center justify-center shadow-lg shadow-amber-200">
                    <i class="fas fa-cog text-white text-xl"></i>
                </div>
                <h3 id="guidelines" class="font-bold text-xl text-amber-900">
                    Scheme Design
                </h3>
            </div>
            <p class="text-gray-700 leading-relaxed mb-6 text-sm">
                jai bangla follows a transparent digital-first process with direct
                benefit transfers, robust grievance redressal, and integration with
                district-level monitoring systems.
            </p>
            <!-- <a href="#" class="inline-flex items-center text-amber-600 font-semibold text-sm hover:underline group-hover:gap-2 transition-all">
                Read More <span class="ml-1">→</span>
            </a> -->
            <!-- Decorative Background Pattern -->
            <div class="absolute -bottom-5 -right-5 text-amber-300 opacity-20 pointer-events-none group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                <i class="fas fa-cogs text-9xl"></i>
            </div>
    </section>
    <section class="max-w-7xl mx-auto mb-16 px-4">
        <div class="bg-gray-50/50 border border-gray-100 rounded-3xl p-6 md:p-8 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-4 divide-y md:divide-y-0 md:divide-x divide-gray-200">

                <!-- Pillar 1: Social Security -->
                <div class="flex items-center gap-4 px-4 py-4 md:py-0">
                    <div class="text-blue-600 text-3xl">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-blue-900 leading-tight">Social Security</h4>
                        <p class="text-xs text-gray-500 font-medium">pensions for the vulnerable</p>
                    </div>
                </div>

                <!-- Pillar 2: Marginalized Upliftment -->
                <div class="flex items-center gap-4 px-4 py-4 md:py-0">
                    <div class="text-green-600 text-3xl">
                        <i class="fas fa-users-line"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-green-900 leading-tight">Inclusive Support</h4>
                        <p class="text-xs text-gray-500 font-medium">SC & ST communities</p>
                    </div>
                </div>

                <!-- Pillar 3: Dignified Living -->
                <div class="flex items-center gap-4 px-4 py-4 md:py-0">
                    <div class="text-orange-500 text-3xl">
                        <i class="fas fa-wheelchair"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-orange-900 leading-tight">Dignified Living</h4>
                        <p class="text-xs text-gray-500 font-medium">empowering the disabled</p>
                    </div>
                </div>

                <!-- Pillar 4: Direct Empowerment -->
                <div class="flex items-center gap-4 px-4 py-4 md:py-0">
                    <div class="text-purple-600 text-3xl">
                        <i class="fas fa-vault"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-purple-900 leading-tight">Direct Benefits</h4>
                        <p class="text-xs text-gray-500 font-medium">seamless DBT transfers</p>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- Sliding Card Carousel -->
    <section id="department" class="bg-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8 text-indigo-700">
                Departments Involved
            </h2>

            <div id="card-carousel-wrapper" class="relative overflow-hidden">

                <div id="card-carousel" class="flex transition-transform duration-500 ease-in-out">

                    @foreach ($department as $dept)

                    @php
                    $json = $dept['json_data'];
                    @endphp

                    @include('frontend.components.department-h', [
                    'ref_color' => $json['ref_color'] ?? '',
                    'slug' => $dept['slug'] ?? '',
                    'name' => $dept['f_name'] ?? '',
                    'about' => $json['very_short'] ?? ''
                    ])

                    @endforeach

                </div>

                <!-- Navigation Buttons -->
                <button id="card-prev"
                    class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-md hover:bg-gray-100 transition z-50 cursor-pointer">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button id="card-next"
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-md hover:bg-gray-100 transition z-50 cursor-pointer">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <!-- Auto Indicators (count matches items dynamically) -->
                <div id="card-indicators" class="card-indicators flex justify-center mt-2 space-x-2"></div>

            </div>

        </div>
    </section>


    <!-- here make the section of department link when i click redirect to new pages -->
    <!-- Departments Section -->
    <section id="scheme" class="max-w-7xl mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-center mb-4 text-indigo-700">
            Schemes
        </h2>

        <div class="max-h-90 overflow-y-auto scroll-container bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($scheme_info as $info)
                @include('frontend.components.scheme-icon-h', [
                'name' => $info['scheme_name'],
                'icon' => $info['icon'],
                'color' => $info['ref_color'],
                'id' => $info['id'],
                'slug' => $info['slug']
                ])
                @endforeach

            </div>
        </div>
    </section>


    <!-- Footer -->
    @include('frontend.layouts.footer')
    <!-- </body> -->
    @endsection

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            /* ------------------------------------------
             *  COUNTER ANIMATION
             * ------------------------------------------ */

            function animateCounter(id, target, isMoney) {
                isMoney = !!isMoney;
                const el = document.getElementById(id);
                if (!el) return;

                let start = 0;
                const duration = 2000;
                const step = Math.max(10, duration / Math.max(1, target));

                const timer = setInterval(function() {
                    start += Math.ceil(target / (duration / step));

                    if (start >= target) {
                        clearInterval(timer);
                        el.textContent = formatNumber(target, isMoney);
                    } else {
                        el.textContent = formatNumber(start, isMoney);
                    }
                }, step);
            }

            function formatNumber(num, isMoney) {
                if (isMoney) {
                    if (num >= 10000000) {
                        return (num / 10000000).toFixed(1).replace(/\.0$/, "") + " Cr.";
                    }
                    try {
                        return Number(num).toLocaleString("en-IN");
                    } catch {
                        return num;
                    }
                }

                try {
                    return Number(num).toLocaleString("en-IN");
                } catch {
                    return num;
                }
            }

            const counters = [{
                    id: "deptCounter"
                },
                {
                    id: "schemeCounter"
                },
                {
                    id: "beneficiaryCounter"
                },
                {
                    id: "disbursementCounter",
                    money: true
                }
            ];

            counters.forEach(c => {
                const el = document.getElementById(c.id);
                if (el) {
                    animateCounter(
                        c.id,
                        parseInt(el.dataset.target || 0, 10),
                        c.money
                    );
                }
            });


            /* ------------------------------------------
             *  HERO BANNER CAROUSEL
             * ------------------------------------------ */

            const slides = document.querySelectorAll(".carousel-slide");
            const indicators = document.querySelectorAll(".carousel-indicator");
            const nextBtn = document.getElementById("nextBtn");
            const prevBtn = document.getElementById("prevBtn");

            if (slides.length) {
                let currentIndex = 0;
                const totalSlides = slides.length;
                let autoSlide;

                function showSlide(index) {
                    slides.forEach((slide, i) => {
                        slide.style.opacity = i === index ? "1" : "0";
                        slide.style.zIndex = i === index ? "10" : "0";
                    });

                    indicators.forEach((dot, i) => {
                        dot.classList.toggle("bg-white", i === index);
                        dot.classList.toggle("bg-white/40", i !== index);
                    });
                }

                function nextSlide() {
                    currentIndex = (currentIndex + 1) % totalSlides;
                    showSlide(currentIndex);
                }

                function prevSlide() {
                    currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                    showSlide(currentIndex);
                }

                function restartAutoplay() {
                    clearInterval(autoSlide);
                    autoSlide = setInterval(nextSlide, 5000);
                }

                nextBtn?.addEventListener("click", () => {
                    nextSlide();
                    restartAutoplay();
                });

                prevBtn?.addEventListener("click", () => {
                    prevSlide();
                    restartAutoplay();
                });

                indicators.forEach((dot, i) => {
                    dot.addEventListener("click", () => {
                        currentIndex = i;
                        showSlide(currentIndex);
                        restartAutoplay();
                    });
                });

                autoSlide = setInterval(nextSlide, 5000);
                showSlide(currentIndex);
            }



            /* ------------------------------------------
             *  CARD FLIP EFFECT
             * ------------------------------------------ */

            document.querySelectorAll(".card-inner").forEach(card => {
                card.addEventListener("mouseenter", () => {
                    card.style.transform = "rotateY(180deg)";
                });

                card.addEventListener("mouseleave", () => {
                    card.style.transform = "rotateY(0deg)";
                });
            });


            /* ------------------------------------------
             *  INFINITE RESPONSIVE CARD CAROUSEL
             * ------------------------------------------ */

            const wrapper = document.getElementById("card-carousel-wrapper");
            const carousel = document.getElementById("card-carousel");

            if (!wrapper || !carousel) return;

            function getVisible() {
                if (window.innerWidth < 640) return 1;
                if (window.innerWidth < 1024) return 2;
                return 3;
            }

            let visibleCards = getVisible();
            let currentIndex = visibleCards;

            const indicatorContainer = document.querySelector(".card-indicators");
            const originalCards = carousel.querySelectorAll(".original-item");
            const totalOriginal = originalCards.length;

            indicatorContainer.innerHTML = "";

            originalCards.forEach((_, i) => {
                const btn = document.createElement("button");
                btn.className = "card-indicator w-2.5 h-2.5 rounded-full bg-gray-300 transition-all duration-300 hover:bg-gray-400 cursor-pointer";
                if (i === 0) btn.classList.add("bg-indigo-600", "scale-125");
                indicatorContainer.appendChild(btn);
            });

            const indicators2 = indicatorContainer.querySelectorAll(".card-indicator");

            function updateIndicators() {
                let index = (currentIndex - visibleCards) % totalOriginal;
                if (index < 0) index += totalOriginal;

                indicators2.forEach((dot, i) => {
                    dot.classList.toggle("bg-indigo-600", i === index);
                    dot.classList.toggle("scale-125", i === index);
                    dot.classList.toggle("bg-gray-300", i !== index);
                });
            }

            const cloneFirst = Array.from(originalCards).slice(0, visibleCards).map(c => c.cloneNode(true));
            const cloneLast = Array.from(originalCards).slice(-visibleCards).map(c => c.cloneNode(true));

            cloneLast.forEach(c => carousel.prepend(c));
            cloneFirst.forEach(c => carousel.append(c));

            let allCards = carousel.querySelectorAll(".card-carousel-item");

            function updatePosition(skip = false) {
                const percent = -(100 / visibleCards) * currentIndex;
                carousel.style.transition = skip ? "none" : "transform 0.7s ease-out";
                carousel.style.transform = `translateX(${percent}%)`;

                if (skip) {
                    setTimeout(() => {
                        carousel.style.transition = "transform 0.7s ease-out";
                    }, 50);
                }

                updateIndicators();
            }

            function cardNextSlide() {
                currentIndex++;
                updatePosition();

                if (currentIndex >= allCards.length - visibleCards) {
                    setTimeout(() => {
                        currentIndex = visibleCards;
                        updatePosition(true);
                    }, 700);
                }
            }

            function cardPrevSlide() {
                currentIndex--;
                updatePosition();

                if (currentIndex < visibleCards) {
                    setTimeout(() => {
                        currentIndex = allCards.length - visibleCards * 2;
                        updatePosition(true);
                    }, 700);
                }
            }

            document.getElementById("card-next")?.addEventListener("click", cardNextSlide);
            document.getElementById("card-prev")?.addEventListener("click", cardPrevSlide);

            indicators2.forEach((dot, i) => {
                dot.addEventListener("click", () => {
                    currentIndex = visibleCards + i;
                    updatePosition();
                });
            });

            let autoSlide = setInterval(cardNextSlide, 4000);

            wrapper.addEventListener("mouseenter", () => clearInterval(autoSlide));
            wrapper.addEventListener("mouseleave", () => {
                autoSlide = setInterval(cardNextSlide, 4000);
            });

            window.addEventListener("resize", () => {
                if (getVisible() !== visibleCards) location.reload();
            });

            updatePosition(true);

        });
    </script>
    @endpush