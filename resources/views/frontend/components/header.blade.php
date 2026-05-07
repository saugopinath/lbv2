<header class="bg-white shadow top-0 z-50">
    <div class="ms-2 me-2 md:ms-5 md:me-5 px-2 md:px-4 py-3">

        <!-- Top row -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

            <!-- Logo -->
            <div class="flex items-center justify-center md:justify-start gap-3">
                <img class="w-10 md:w-12 lg:w-12 h-auto" src="{{ asset('images/home/'.config('jblbConf.headerlogo')) }}"
                    alt="Biswa Bangla" />
                <img class="w-16 md:w-24 lg:w-24 h-auto" src="{{ asset('images/home/'.config('jblbConf.logo')) }}"
                    alt="{{ config('constants.jb') }}" />
                <div
                    class="text-xl md:text-xl font-semibold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent text-center md:text-left">
                    {{config('jblbConf.headLine')}} | {{config('jblbConf.deptName')}} <br>Government of West Bengal
                </div>
            </div>

            <!-- Icon Navigation -->
            <div class="overflow-x-auto">
                <div class="flex items-center gap-4 md:gap-8 px-2 md:px-4 py-2 min-w-max">

                    <!-- Home -->
                    <a href="{{ url('/') }}"
                        class="flex flex-col items-center text-gray-700 hover:text-indigo-600 transition">
                        <div
                            class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center bg-gray-100 rounded-full shadow-sm mb-1">
                            <i class="fa-solid fa-house-user text-sm md:text-lg"></i>
                        </div>
                        <span class="text-[10px] md:text-xs">Home</span>
                    </a>

                    <!-- Notification -->
                    <a href="{{ route('notifications') }}"
                        class="flex flex-col items-center text-gray-700 hover:text-indigo-600 transition">
                        <div
                            class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center bg-gray-100 rounded-full shadow-sm mb-1">
                            <i class="fa-solid fa-bell text-sm md:text-lg"></i>
                        </div>
                        <span class="text-[10px] md:text-xs">Notification</span>
                    </a>

                    <!-- Track -->
                    <a href="{{ route('track-beneficiary') }}"
                        class="flex flex-col items-center text-gray-700 hover:text-indigo-600 transition">
                        <div
                            class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center bg-gray-100 rounded-full shadow-sm mb-1">
                            <i class="fa-solid fa-magnifying-glass text-sm md:text-lg"></i>
                        </div>
                        <span class="text-[10px] md:text-xs text-center">Track</span>
                    </a>

                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="flex flex-col items-center text-gray-700 hover:text-indigo-600 transition">
                        <div
                            class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center bg-gray-100 rounded-full shadow-sm mb-1">
                            <i class="fa-solid fa-hand-holding-heart text-sm md:text-lg"></i>
                        </div>
                        <span class="text-[10px] md:text-xs text-center">Dashboard</span>
                    </a>

                    <!-- Portlet -->
                    <a href="{{ route('portlet') }}"
                        class="flex flex-col items-center text-gray-700 hover:text-indigo-600 transition">
                        <div
                            class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center bg-gray-100 rounded-full shadow-sm mb-1">
                            <i class="fa-solid fa-book-open text-sm md:text-lg"></i>
                        </div>
                        <span class="text-[10px] md:text-xs text-center">Portlet</span>
                    </a>

                    <!-- Login -->
                    <a href="{{ route('login') }}" target="_blank"
                        class="flex flex-col items-center text-gray-700 hover:text-indigo-600 transition">
                        <div
                            class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center bg-gray-100 rounded-full shadow-sm mb-1">
                            <i class="fa-solid fa-key text-sm md:text-lg"></i>
                        </div>
                        <span class="text-[10px] md:text-xs">Login</span>
                    </a>

                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-col md:flex-row md:items-end md:justify-end border-t pt-2 gap-2">
            <!-- Menu -->
            <div class="flex flex-wrap justify-center md:justify-end gap-3 md:gap-6 text-xs md:text-sm font-medium">
                <a href="#about" class="hover:text-indigo-600">About</a>
                <a href="#objectives" class="hover:text-indigo-600">Objectives</a>
                <a href="#guidelines" class="hover:text-indigo-600">Guidelines</a>
                <a href="#resources" class="hover:text-indigo-600">Resources</a>
                <a href="#contact" class="hover:text-indigo-600">Contact</a>
                <a href="#department" class="hover:text-indigo-600">Department</a>
                <a href="#scheme" class="hover:text-indigo-600">Schemes</a>
            </div>

        </nav>
    </div>
</header>