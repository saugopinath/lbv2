{{-- Footer floating icon and float animation styles are now in resources/css/app.css --}}

<footer class="footer-decoration bg-gray-800 text-gray-300 py-10 relative overflow-hidden">

    <!-- Decorative floating icons -->
    <i class="floating-icon icon-1 fas fa-solid fa-fan text-green-400/20"></i>
    <i class="floating-icon icon-2 fas fa-heart text-pink-400/20"></i>
    <i class="floating-icon icon-3 fas fa-star text-yellow-400/20"></i>
    <i class="floating-icon icon-4 fas fa-leaf text-emerald-400/20"></i>

    <!-- Government Skyline Illustration (Halka Visible) -->
    <div class="absolute bottom-0 left-0 w-full h-48 opacity-[0.12] pointer-events-none select-none overflow-hidden">
        <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 1200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Wavy Background Hills -->
            <path d="M0 200 C 300 120 600 220 900 150 C 1050 115 1150 180 1200 190 V 200 H 0 Z" fill="url(#hill-gradient-1)" />
            <path d="M0 200 C 200 150 500 100 800 180 C 1000 230 1100 150 1200 170 V 200 H 0 Z" fill="url(#hill-gradient-2)" />

            <!-- Skyline Buildings (Line Art) -->
            <g stroke="currentColor" stroke-width="0.8" class="text-amber-400/40">
                <!-- Victoria Memorial style dome -->
                <path d="M450 160 L450 140 Q450 120 470 120 L480 120 Q500 120 500 140 L500 160" />
                <path d="M470 120 Q475 100 480 120" />

                <!-- Howrah Bridge style structure -->
                <path d="M100 170 L130 130 L370 130 L400 170" />
                <path d="M130 130 Q250 80 370 130" />
                <path d="M160 130 L160 155 M190 130 L190 150 M220 130 L220 145 M250 130 L250 145 M280 130 L280 145 M310 130 L310 150 M340 130 L340 155" />

                <!-- Tall Building/Tower -->
                <path d="M600 160 L600 80 L630 80 L630 160" />
                <path d="M615 80 L615 60" />

                <!-- Clock Tower style -->
                <path d="M750 160 L750 100 L780 100 L780 160" />
                <circle cx="765" cy="115" r="5" />
                <path d="M750 100 L765 80 L780 100" />

                <!-- More abstract buildings -->
                <path d="M850 160 L850 120 L880 120 L880 160" />
                <path d="M920 160 L920 110 L940 90 L960 110 L960 160" />
                <path d="M1050 160 L1050 130 Q1075 110 1100 130 L1100 160" />
            </g>

            <defs>
                <linearGradient id="hill-gradient-1" x1="600" y1="120" x2="600" y2="200" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#10B981" stop-opacity="0.2" />
                    <stop offset="1" stop-color="#10B981" stop-opacity="0" />
                </linearGradient>
                <linearGradient id="hill-gradient-2" x1="600" y1="100" x2="600" y2="200" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#F59E0B" stop-opacity="0.1" />
                    <stop offset="1" stop-color="#F59E0B" stop-opacity="0" />
                </linearGradient>
            </defs>
        </svg>
    </div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <!-- Government Seals and Logos -->
        <div class="flex flex-col md:flex-row justify-end items-end mb-2 pb-2 border-b border-gray-700">
            <!-- Important Links Bar -->
            <div class="flex flex-wrap justify-center gap-2 text-sm">
                <a href="#" class="text-indigo-100 hover:text-white transition-colors duration-200">Legal Disclaimer</a>
                <span class="text-white/30">|</span>
                <a href="#" class="text-indigo-100 hover:text-white transition-colors duration-200">Privacy Policy</a>
                <span class="text-white/30">|</span>
                <a href="#" class="text-indigo-100 hover:text-white transition-colors duration-200">Terms &amp; Conditions</a>
                <span class="text-white/30">|</span>
                <a href="#" class="text-indigo-100 hover:text-white transition-colors duration-200">Copyright Policy</a>
                <span class="text-white/30">|</span>
                <a href="#" class="text-indigo-100 hover:text-white transition-colors duration-200">Hyperlink Policy</a>
            </div>
        </div>
        <!-- Main Footer Content -->
        <div class="grid md:grid-cols-3 gap-8 mb-8">
            <!-- Scheme Information -->
            <div>
                <h4 class="font-bold text-lg mb-4 text-amber-400 border-b border-amber-400 pb-2">
                    {{ config('jblbConf.headLine') }}
                </h4>
                <p class="text-sm text-gray-300 mb-4">
                    {{ config('jblbConf.footerDescription') }}
                </p>
                <div class="flex items-center text-sm text-gray-400">
                    <i class="fas fa-shield-alt mr-2 text-amber-400"></i>
                    <span>Official Government Portal</span>
                </div>
            </div>
            <!-- Important Links -->
            <div>
                <h4 class="font-bold text-lg mb-4 text-amber-400 border-b border-amber-400 pb-2">
                    Important Links
                </h4>
                <ul class="space-y-2 text-sm">
                    <!-- <li>
                        <a href="#"
                            class="hover:text-amber-400 transition-all duration-300 flex items-center hover:translate-x-1">
                            <i class="fas fa-external-link-alt mr-2 text-xs text-gray-400"></i>
                            West Bengal Government
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="hover:text-amber-400 transition-all duration-300 flex items-center hover:translate-x-1">
                            <i class="fas fa-external-link-alt mr-2 text-xs text-gray-400"></i>
                            Finance Department
                        </a>
                    </li> -->
                    <li>
                        <a href="#"
                            class="hover:text-amber-400 transition-all duration-300 flex items-center hover:translate-x-1">
                            <i class="fas fa-external-link-alt mr-2 text-xs text-gray-400"></i>
                            DBT Bharat
                        </a>
                    </li>
                    <!-- <li>
                        <a href="#"
                            class="hover:text-amber-400 transition-all duration-300 flex items-center hover:translate-x-1">
                            <i class="fas fa-external-link-alt mr-2 text-xs text-gray-400"></i>
                            e-District West Bengal
                        </a>
                    </li> -->
                </ul>
            </div>

            <!-- Contact & Helpline -->
            <div>
                <h4 class="font-bold text-lg mb-4 text-amber-400 border-b border-amber-400 pb-2">
                    Contact &amp; Support
                </h4>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start hover:text-white transition-colors duration-300">
                        <i class="fas fa-phone-alt mt-1 mr-3 text-amber-400 text-xs"></i>
                        <div>
                            <div class="font-semibold">Helpline</div>
                            <div class="text-gray-300">1800-345-XXXX</div>
                            <div class="text-xs text-gray-400">(Toll Free)</div>
                        </div>
                    </div>
                    <div class="flex items-start hover:text-white transition-colors duration-300">
                        <i class="fas fa-envelope mt-1 mr-3 text-amber-400 text-xs"></i>
                        <div>
                            <div class="font-semibold">Email</div>
                            <div class="text-gray-300">support@lakshmirBhandar.wb.gov.in</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grievance Redressal -->
            <!-- <div>
                <h4 class="font-bold text-lg mb-4 text-amber-400 border-b border-amber-400 pb-2">
                    Grievance Redressal
                </h4>
                <div class="space-y-3 text-sm">
                    <a href="#"
                        class="block bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white py-2 px-4 rounded text-center transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-comment-dots mr-2"></i>
                        Register Grievance
                    </a>
                    <a href="#"
                        class="block border border-amber-400 hover:bg-amber-400 hover:text-gray-900 text-white py-2 px-4 rounded text-center transition-all duration-300">
                        <i class="fas fa-search mr-2"></i>
                        Track Grievance Status
                    </a>
                    <div class="text-xs text-gray-400 mt-2">
                        Grievances will be resolved within 15 working days
                    </div>
                </div>
            </div> -->
        </div>

        <!-- Additional Government Links -->
        <!-- <div class="bg-gray-700 rounded-lg p-4 mb-6"> -->
        <!-- <h5 class="font-semibold text-center mb-5 text-amber-300 text-lg">
                Related Government Portals
            </h5> -->

        <!-- <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center text-xs"> -->

        <!-- WB State Portal -->
        <!-- <a href="https://wb.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300">
                    <img src="{{asset('images/home/biswo_logo.png')}}"
                        class="w-12 h-12 mb-3 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight">
                        WB State Portal
                    </span>
                </a> -->

        <!-- Finance Department -->
        <!-- <a href="https://finance.wb.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300">
                    <img src="{{asset('images/home/finance.jpeg')}}"
                        class="w-12 h-12 mb-3 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight">
                        Finance Department
                    </span>
                </a> -->

        <!-- Women & Child Development -->
        <!-- <a href="https://wcdsw.wb.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300">
                    <img src="{{asset('images/home/biswo_logo.png')}}"
                        class="w-12 h-12 mb-3 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight text-center">
                        Women &amp; Child Development &amp; Social Welfare
                    </span>
                </a> -->

        <!-- BSK -->
        <!-- <a href="https://bsk.wb.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300">
                    <img src="{{asset('images/home/bsk.jpeg')}}"
                        class="w-12 h-12 mb-3 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight">
                        Bangla Sahayata Kendra
                    </span>
                </a> -->

        <!-- DUARE SARKAR + APAS (two logos) -->
        <!-- <a href="https://ds.wb.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300 col-span-2 md:col-span-1">

                    <div class="flex items-center justify-center space-x-2 mb-3">
                        <img src="{{asset('images/home/portals/apas_logo.png')}}"
                            class="w-20 h-10 opacity-80 group-hover:opacity-100 transition-all object-contain">
                        <img src="{{asset('images/home/portals/ds_logo.png')}}"
                            class="w-20 h-10 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    </div>

                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight text-center">
                        Duare Sarkar<br> Aamar Para Amar Samadhan
                    </span>
                </a> -->

        <!-- Tribal Dept -->
        <!-- <a href="https://adibasikalyan.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300">
                    <img src="{{asset('images/home/biswo.jpeg')}}"
                        class="w-12 h-12 mb-3 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight text-center">
                        Tribal Development Department
                    </span>
                </a> -->

        <!-- Backward Classes Welfare -->
        <!-- <a href="https://www.anagrasarkalyan.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300">
                    <img src="{{asset('images/home/biswo_logo.png')}}"
                        class="w-12 h-12 mb-3 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight text-center">
                        Backward Classes Welfare Department
                    </span>
                </a> -->

        <!-- </div> -->
        <!-- </div> -->



        <!-- Copyright and Disclaimer -->
        <div class="text-center pt-6 border-t border-gray-700">
            <div
                class="text-xs text-gray-400 mb-2 bg-gradient-to-r from-amber-400 via-pink-500 to-purple-500 bg-clip-text text-transparent font-semibold">
                © 2025 | <a href="www.wb.nic.in" target="_blank">National Informatics Centre | West Bengal State
                    Centre</a>
            </div>
            <div class="text-xs text-gray-500">
                This site is designed by National Informatics Centre(NIC). Content, DATA, Process and Operation owned and maintained by {{
                    config('jblbConf.deptName')}}, Government of West Bengal. <br>
                <nav class="flex flex-wrap items-center justify-center gap-x-2 gap-y-2 text-sm md:text-xs">
                    <p>Best Viewed in Google Chrome</p>
                </nav>
            </div>
            <div class="text-xs text-gray-500 mt-2">
                Last Updated: 08 May, 2026
            </div>

            <!-- Visitor Counter (Optional) -->
            <div class="mt-4 flex justify-center items-center space-x-4 text-xs text-gray-500">
                <div class="flex items-center">
                    <i class="fas fa-eye mr-2 text-amber-400"></i>
                    <span>Visitors: 1,24,567</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-mobile-alt mr-2 text-amber-400"></i>
                    <span>Mobile Friendly</span>
                </div>
            </div>
        </div>
    </div>
</footer>