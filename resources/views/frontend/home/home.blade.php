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


                <!-- Info Sections -->
                <section class="max-w-7xl mx-auto mb-8 grid md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 id="about" class="font-semibold text-lg text-indigo-600">
                            Scheme Rationale
                        </h3>
                        <p class="mt-3 text-sm">
                            The jai bangla scheme aims to consolidate various welfare initiatives
                            of the Government of West Bengal under a single umbrella to ensure
                            efficiency, transparency, and greater citizen access.
                        </p>
                        <a href="#" class="text-indigo-600 text-sm mt-3 inline-block">Read More →</a>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 id="objectives" class="font-semibold text-lg text-green-600">
                            Objectives
                        </h3>
                        <p class="mt-3 text-sm">
                            The scheme provides financial, social, and developmental assistance
                            for youth, women, elderly, and marginalized communities, ensuring
                            holistic empowerment and inclusion.
                        </p>
                        <a href="#" class="text-green-600 text-sm mt-3 inline-block">Read More →</a>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 id="guidelines" class="font-semibold text-lg text-amber-600">
                            Scheme Design
                        </h3>
                        <p class="mt-3 text-sm">
                            jai bangla follows a transparent digital-first process with direct
                            benefit transfers, robust grievance redressal, and integration with
                            district-level monitoring systems.
                        </p>
                        <a href="#" class="text-amber-600 text-sm mt-3 inline-block">Read More →</a>
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
                                class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-md hover:bg-gray-100 transition">
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <button id="card-next"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-md hover:bg-gray-100 transition">
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <!-- Auto Indicators (count matches items dynamically) -->
                            <div id="card-indicators" class="card-indicators flex justify-center mt-6 space-x-2"></div>

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
document.addEventListener("DOMContentLoaded", function () {

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

        const timer = setInterval(function () {
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

    const counters = [
        { id: "deptCounter" },
        { id: "schemeCounter" },
        { id: "beneficiaryCounter" },
        { id: "disbursementCounter", money: true }
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
     *  SIMPLE CARD CAROUSEL
     * ------------------------------------------ */

    const cardCarousel = document.getElementById("card-carousel");
    const cards = document.querySelectorAll(".card-carousel-item");
    const cardPrev = document.getElementById("card-prev");
    const cardNext = document.getElementById("card-next");
    const cardIndicators = document.querySelectorAll(".card-indicator");

    if (cardCarousel && cards.length) {
        let currentCard = 0;
        const visibleCards = 3;
        const totalCards = cards.length;
        const maxSteps = Math.max(0, totalCards - visibleCards);

        function updateCardCarousel() {
            const translateX = -(100 / visibleCards) * currentCard;
            cardCarousel.style.transform = `translateX(${translateX}%)`;

            cardIndicators.forEach((dot, i) => {
                dot.classList.toggle("bg-indigo-600", i === currentCard);
                dot.classList.toggle("bg-gray-300", i !== currentCard);
            });
        }

        cardNext?.addEventListener("click", () => {
            currentCard = currentCard < maxSteps ? currentCard + 1 : 0;
            updateCardCarousel();
        });

        cardPrev?.addEventListener("click", () => {
            currentCard = currentCard > 0 ? currentCard - 1 : maxSteps;
            updateCardCarousel();
        });

        cardIndicators.forEach((dot, i) => {
            dot.addEventListener("click", () => {
                currentCard = i;
                updateCardCarousel();
            });
        });

        updateCardCarousel();
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
    const originalCards = document.querySelectorAll(".original-item");
    const totalOriginal = originalCards.length;

    indicatorContainer.innerHTML = "";

    originalCards.forEach((_, i) => {
        const btn = document.createElement("button");
        btn.className = "card-indicator w-3 h-3 rounded-full bg-gray-300";
        if (i === 0) btn.classList.add("bg-indigo-600");
        indicatorContainer.appendChild(btn);
    });

    const indicators2 = indicatorContainer.querySelectorAll(".card-indicator");

    function updateIndicators() {
        let index = (currentIndex - visibleCards) % totalOriginal;
        if (index < 0) index += totalOriginal;

        indicators2.forEach((dot, i) => {
            dot.classList.toggle("bg-indigo-600", i === index);
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

    function nextSlide() {
        currentIndex++;
        updatePosition();

        if (currentIndex >= allCards.length - visibleCards) {
            setTimeout(() => {
                currentIndex = visibleCards;
                updatePosition(true);
            }, 700);
        }
    }

    function prevSlide() {
        currentIndex--;
        updatePosition();

        if (currentIndex < visibleCards) {
            setTimeout(() => {
                currentIndex = allCards.length - visibleCards * 2;
                updatePosition(true);
            }, 700);
        }
    }

    document.getElementById("card-next")?.addEventListener("click", nextSlide);
    document.getElementById("card-prev")?.addEventListener("click", prevSlide);

    indicators2.forEach((dot, i) => {
        dot.addEventListener("click", () => {
            currentIndex = visibleCards + i;
            updatePosition();
        });
    });

    let autoSlide = setInterval(nextSlide, 4000);

    wrapper.addEventListener("mouseenter", () => clearInterval(autoSlide));
    wrapper.addEventListener("mouseleave", () => {
        autoSlide = setInterval(nextSlide, 4000);
    });

    window.addEventListener("resize", () => {
        if (getVisible() !== visibleCards) location.reload();
    });

    updatePosition(true);

});
</script>
@endpush
