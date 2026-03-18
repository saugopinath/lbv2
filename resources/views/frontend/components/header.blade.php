<header class="bg-white shadow sticky top-0 z-50">
  <div class="ms-5 me-5 px-4 py-3">
    <!-- Top row with branding -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div>
          <img class="w-1/3 h-1/2" src="{{ asset('images/home/header_logo.png') }}" alt="jai bangla" />
          <!-- <p class="text-xs text-gray-500">Government of West Bengal</p> -->
        </div>
      </div>

      <div class="flex items-center gap-4">
        <!-- Icon Navigation Bar -->
        <div class="flex items-center gap-8 px-4 py-2">

          <!-- Home -->
          <a href="{{ url('/') }}" class="flex flex-col items-center text-gray-700 hover:text-indigo-600 transition">
            <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full shadow-sm mb-1">
              <i class="fa-solid fa-house-user text-lg"></i>
            </div>
            <span class="text-xs">Home</span>
          </a>

          <!-- Notification -->
          <a href="{{ route('notifications') }}"
            class="flex flex-col items-center text-gray-700 hover:text-indigo-600 transition">
            <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full shadow-sm mb-1">
              <i class="fa-solid fa-bell text-lg"></i>
            </div>
            <span class="text-xs">Notification</span>
          </a>

          <!-- Track Application -->
          <a href="{{ route('track-applicant') }}"
            class="flex flex-col items-center text-gray-700 hover:text-indigo-600 transition">
            <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full shadow-sm mb-1">
              <i class="fa-solid fa-magnifying-glass text-lg"></i>
            </div>
            <span class="text-xs">Track Application</span>
          </a>

          <!-- Visual Dashboard -->
          <a href="{{ route('dashboard') }}"
            class="flex flex-col items-center text-gray-700 hover:text-indigo-600 transition">
            <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full shadow-sm mb-1">
              <i class="fa-solid fa-hand-holding-heart text-lg"></i>
            </div>
            <span class="text-xs">Visual Dashboard</span>
          </a>

          <!-- District Portlet -->
          <a href="{{ route('portlet') }}"
            class="flex flex-col items-center text-gray-700 hover:text-indigo-600 transition">
            <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full shadow-sm mb-1">
              <i class="fa-solid fa-book-open text-lg"></i>
            </div>
            <span class="text-xs">District Portlet</span>
          </a>

          <!-- Login -->
          <a href="{{ route('login') }}" target="_blank"
            class="flex flex-col items-center text-gray-700 hover:text-indigo-600 transition">
            <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full shadow-sm mb-1">
              <i class="fa-solid fa-key text-lg"></i>
            </div>
            <span class="text-xs">Login</span>
          </a>

        </div>
      </div>

    </div>

    <!-- Navigation -->
    <nav class="flex items-center justify-between border-t pt-2">
      <div
        class="text-sm font-semibold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent animate-pulse">
        <span>Jai Bangla | One Umbrella Scheme | Department of Finance | Government of West Bengal</span>
      </div>
      <div class="flex gap-6 text-sm font-medium relative">
        <a href="#about" class="hover:text-indigo-600">About</a>
        <a href="#objectives" class="hover:text-indigo-600">Objectives</a>
        <a href="#guidelines" class="hover:text-indigo-600">Guidelines</a>
        <a href="#resources" class="hover:text-indigo-600">Resources</a>
        <a href="#contact" class="hover:text-indigo-600">Contact</a>
        <a href="#department" class="hover:text-indigo-600">Department</a>
        <a href="#scheme" class="hover:text-indigo-600">Schemes</a>

        <!-- Department Dropdown -->
        <!-- <div class="relative group">
                      <button class="hover:text-indigo-600 flex items-center gap-1">
                        Department
                        <svg
                          class="w-4 h-4 mt-[2px] transition-transform duration-200 group-hover:rotate-180"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="2"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19 9l-7 7-7-7"
                          />
                        </svg>
                      </button>
                      <div
                        class="absolute hidden group-hover:block bg-white shadow-lg rounded-lg py-2 mt-2 w-48 border border-gray-100"
                      >
                        <a
                          href="#dept1"
                          class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                          >Agriculture</a
                        >
                        <a
                          href="#dept2"
                          class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                          >Education</a
                        >
                        <a
                          href="#dept3"
                          class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                          >Health</a
                        >
                        <a
                          href="#dept4"
                          class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                          >Finance</a
                        >
                        <a
                          href="#dept5"
                          class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                          >Rural Development</a
                        >
                      </div>
                    </div> -->

        <!-- Schemes Dropdown -->
        <!-- <div class="relative group">
                      <button class="hover:text-indigo-600 flex items-center gap-1">
                        Schemes
                        <svg
                          class="w-4 h-4 mt-[2px] transition-transform duration-200 group-hover:rotate-180"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="2"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19 9l-7 7-7-7"
                          />
                        </svg>
                      </button>
                      <div
                        class="absolute hidden group-hover:block bg-white shadow-lg rounded-lg py-2 mt-2 w-30 border border-gray-100"
                      >
                        <a
                          href="#scheme1"
                          class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                          >Jai
                        </a>
                        <a
                          href="#scheme2"
                          class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                          >Kanya</a
                        >
                        <a
                          href="#scheme3"
                          class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                          >Swasthya</a
                        >
                        <a
                          href="#scheme4"
                          class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                          >Aikyas</a
                        >
                        <a
                          href="#scheme5"
                          class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                          >Krishak</a
                        >
                      </div>
                    </div> -->
      </div>
    </nav>
  </div>
</header>