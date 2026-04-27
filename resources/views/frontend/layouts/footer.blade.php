{{-- Footer floating icon and float animation styles are now in resources/css/app.css --}}

<footer class="footer-decoration bg-gray-800 text-gray-300 py-10 relative overflow-hidden">

    <!-- Decorative floating icons -->
    <i class="floating-icon icon-1 fas fa-leaf text-green-400"></i>
    <i class="floating-icon icon-2 fas fa-heart text-pink-400"></i>
    <i class="floating-icon icon-3 fas fa-star text-yellow-400"></i>
    <i class="floating-icon icon-4 fas fa-seedling text-emerald-400"></i>

    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <!-- Government Seals and Logos -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 pb-6 border-b border-gray-700">
            <div class="flex items-center space-x-6 mb-4 md:mb-0">
                <img src="{{asset('images/home/biswo.jpeg')}}" alt="West Bengal Government"
                    class="h-14 w-20 object-contain">
                <img src="{{asset('images/home/biswo_logo.png')}}" alt="West Bengal Government"
                    class="h-14 w-20 object-contain">
            </div>

            <!-- Important Links Bar -->
            <div class="flex flex-wrap justify-center gap-4 text-sm">
                <a href="#" class="hover:text-amber-400 transition-colors duration-300">Accessibility</a>
                <a href="#" class="hover:text-amber-400 transition-colors duration-300">Privacy Policy</a>
                <a href="#" class="hover:text-amber-400 transition-colors duration-300">Terms &amp; Conditions</a>
                <a href="#" class="hover:text-amber-400 transition-colors duration-300">Sitemap</a>
                <a href="#" class="hover:text-amber-400 transition-colors duration-300">Help</a>
            </div>
        </div>
        <!-- Main Footer Content -->
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <!-- Scheme Information -->
            <div>
                <h4 class="font-bold text-lg mb-4 text-amber-400 border-b border-amber-400 pb-2">
                    Jai Bangla | One Umbrella Scheme
                </h4>
                <p class="text-sm text-gray-300 mb-4">
                    A comprehensive Direct Benefit Transfer initiative by the Government of West Bengal for holistic
                    development and welfare.
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
                    <li>
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
                    </li>
                    <li>
                        <a href="#"
                            class="hover:text-amber-400 transition-all duration-300 flex items-center hover:translate-x-1">
                            <i class="fas fa-external-link-alt mr-2 text-xs text-gray-400"></i>
                            DBT Bharat
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="hover:text-amber-400 transition-all duration-300 flex items-center hover:translate-x-1">
                            <i class="fas fa-external-link-alt mr-2 text-xs text-gray-400"></i>
                            e-District West Bengal
                        </a>
                    </li>
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
                            <div class="text-gray-300">support@joybangla.wb.gov.in</div>
                        </div>
                    </div>
                    <div class="flex items-start hover:text-white transition-colors duration-300">
                        <i class="fas fa-clock mt-1 mr-3 text-amber-400 text-xs"></i>
                        <div>
                            <div class="font-semibold">Working Hours</div>
                            <div class="text-gray-300">10:00 AM - 6:00 PM</div>
                            <div class="text-xs text-gray-400">(Monday to Friday)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grievance Redressal -->
            <div>
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
            </div>
        </div>

        <!-- Additional Government Links -->
        <div class="bg-gray-700 rounded-lg p-4 mb-6">
            <h5 class="font-semibold text-center mb-5 text-amber-300 text-lg">
                Related Government Portals
            </h5>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center text-xs">

                <!-- WB State Portal -->
                <a href="https://wb.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300">
                    <img src="{{asset('images/home/biswo_logo.png')}}"
                        class="w-12 h-12 mb-3 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight">
                        WB State Portal
                    </span>
                </a>

                <!-- Finance Department -->
                <a href="https://finance.wb.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300">
                    <img src="{{asset('images/home/finance.jpeg')}}"
                        class="w-12 h-12 mb-3 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight">
                        Finance Department
                    </span>
                </a>

                <!-- Women & Child Development -->
                <a href="https://wcdsw.wb.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300">
                    <img src="{{asset('images/home/biswo_logo.png')}}"
                        class="w-12 h-12 mb-3 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight text-center">
                        Women &amp; Child Development &amp; Social Welfare
                    </span>
                </a>

                <!-- BSK -->
                <a href="https://bsk.wb.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300">
                    <img src="{{asset('images/home/bsk.jpeg')}}"
                        class="w-12 h-12 mb-3 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight">
                        Bangla Sahayata Kendra
                    </span>
                </a>

                <!-- DUARE SARKAR + APAS (two logos) -->
                <a href="https://ds.wb.gov.in/" target="_blank"
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
                </a>

                <!-- Tribal Dept -->
                <a href="https://adibasikalyan.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300">
                    <img src="{{asset('images/home/biswo.jpeg')}}"
                        class="w-12 h-12 mb-3 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight text-center">
                        Tribal Development Department
                    </span>
                </a>

                <!-- Backward Classes Welfare -->
                <a href="https://www.anagrasarkalyan.gov.in/" target="_blank"
                    class="group flex flex-col items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-900 transition-all duration-300">
                    <img src="{{asset('images/home/biswo_logo.png')}}"
                        class="w-12 h-12 mb-3 opacity-80 group-hover:opacity-100 transition-all object-contain">
                    <span class="text-gray-200 group-hover:text-amber-400 leading-tight text-center">
                        Backward Classes Welfare Department
                    </span>
                </a>

            </div>
        </div>



        <!-- Copyright and Disclaimer -->
        <div class="text-center pt-6 border-t border-gray-700">
            <div
                class="text-xs text-gray-400 mb-2 bg-gradient-to-r from-amber-400 via-pink-500 to-purple-500 bg-clip-text text-transparent font-semibold">
                © 2025 | <a href="www.wb.nic.in" target="_blank">National Informatics Centre | West Bengal State
                    Centre</a>
            </div>
            <div class="text-xs text-gray-500">
                Content owned and maintained by Finance Department, Government of West Bengal.<br>
                Designed, Developed and Hosted by
                <a href="#" class="text-amber-400 hover:text-amber-300 transition-colors">National Informatics
                    Centre</a>,
                West Bengal State Centre.
            </div>
            <div class="text-xs text-gray-500 mt-2">
                Last Updated: 01 January, 2025
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