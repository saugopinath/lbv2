<!DOCTYPE html>
<html lang="en" x-data
      x-init="$watch('$store.app.mode', mode => document.documentElement.classList.toggle('dark', mode === 'dark'))">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Premium Tailwind CSS Admin & Dashboard Template" />
    <meta name="author" content="Webonzer" />

    <!-- Site Title -->
    <title>Lakshmir Bhandar | Government of West Bengal</title>

    <!-- Favicon Icon -->
    <link rel="shortcut icon" href="{{ asset('images/biswofab.ico') }}">

    <!-- Styles -->
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js local -->
    <!-- <script src="{{ asset('js/alpine.min.js') }}" defer></script> -->
</head>

<body x-data="$store.app" class="bg-[#def0f4] dark:bg-gray-900 text-black dark:text-white">
    <!-- Main Layout -->
    <div class="flex h-screen overflow-hidden">
        <!-- Main Container -->
        <div class="flex flex-1 bg-[#def0f4] dark:bg-dark text-dark dark:text-white">

            <!-- Sidebar -->
            <x-layouts.das_side_menu/>

            <!-- Content Area -->
            <div class="flex-1 flex flex-col">
                <!-- Top Bar -->
                <x-layouts.das_top_bar/>
                
                <!-- Content -->
                <div class="flex-1 p-2 overflow-auto">
                <!-- Main Content -->
                 <main class="p-2 space-y-2">
                    {{ $slot }}
                </main>
                </div>

                <!-- Footer -->
                <x-layouts.das_footer/>
            </div>
        </div>
    </div>

    <!-- Alpine Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('app', {
                sidebar: true,
                toggleSidebar() {
                    this.sidebar = !this.sidebar;
                    this.activeMenu = null;
                },
                mode: 'light',
                toggleMode(mode) {
                    this.mode = mode;
                }
            });
        });
    </script>

    <!-- Livewire Scripts -->
    @livewireScripts
    @stack('scripts')
</body>
</html>
