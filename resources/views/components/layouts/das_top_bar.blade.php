    <header class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 shadow">
        <button @click="toggleSidebar" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>

        <div class="flex items-center space-x-4">
          <!-- Dark mode toggle -->
          <button @click="toggleMode(mode === 'dark' ? 'light' : 'dark')"
            class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700" aria-label="Toggle Dark Mode">
            <svg x-show="mode === 'light'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path
                d="M12 3v1M12 20v1M4.22 4.22l.7.7M17.66 17.66l.7.7M1 12h1M20 12h1M4.22 19.78l.7-.7M17.66 6.34l.7-.7M12 5a7 7 0 0 0 0 14a7 7 0 0 0 0-14z"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
            <svg x-show="mode === 'dark'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke-linecap="round" stroke-linejoin="round"
                stroke-width="2" />
            </svg>
          </button>

          <!-- Profile dropdown -->
          <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
              class="flex items-center space-x-2 p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <circle opacity="0.3" cx="12" cy="6" r="4" fill="currentColor"></circle>
                <ellipse cx="12" cy="17" rx="7" ry="4" fill="currentColor"></ellipse>
              </svg>
              <span x-show="$store.app.sidebar">Profile</span>
            </button>
            <div x-show="open" @click.outside="open = false"
              class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded shadow-lg z-10">
              <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Profile Details</a>
              <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                    {{ __('Sign Out') }}
                </x-dropdown-link>
            </form>
             
            </div>
          </div>
        </div>
      </header>