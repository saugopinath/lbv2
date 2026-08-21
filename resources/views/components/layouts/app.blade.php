<!DOCTYPE html>
<html lang="en" x-data x-init="$watch('$store.app.mode', mode => document.documentElement.classList.toggle('dark', mode === 'dark'))">

<head>
    <meta charset="UTF-8" />
    <meta content="IE=edge" http-equiv="X-UA-Compatible" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Premium Tailwind CSS Admin & Dashboard Template" name="description" />
    <meta content="Webonzer" name="author" />

    <!-- Site Title -->
    <title>{{ config('jblbConf.title') }}</title>

    <!-- Favicon Icon -->
    <link href="{{ asset('images/' . config('jblbConf.headerlogo')) }}" rel="shortcut icon">

    <!-- Styles -->
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js local -->
    <!-- <script src="{{ asset('js/alpine.min.js') }}" defer></script> -->
    <!-- Chart.js for Dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-[#def0f4] dark:bg-gray-900 text-black dark:text-white" x-data="$store.app">
    <!-- Main Layout -->
    <div class="flex h-screen overflow-hidden">
        <!-- Main Container -->
        <div class="flex flex-1 bg-[#def0f4] dark:bg-dark text-dark dark:text-white">

            <!-- Sidebar -->
            <x-layouts.das_side_menu />

            <!-- Content Area -->
            <div class="flex-1 flex flex-col">
                <!-- Top Bar -->
                <x-layouts.das_top_bar />
                <livewire:loader />
                <!-- Content -->
                <div class="flex-1 p-2 overflow-auto">
                    <!-- Main Content -->
                    <main class="p-2 space-y-2">
                        <x-flash-message position="top-right" width="w-80" />

                        {{ $slot }}
                    </main>
                </div>

                <!-- Footer -->
                <x-layouts.das_footer />
            </div>
        </div>
    </div>
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
    {{-- Client Side validation for the Application Form sub-module --}}
    <script src="{{ asset('js/form-validation.js') }}"></script>
    @livewireScripts
    @if (session()->has('toastr'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                window.dispatchEvent(new CustomEvent('toastr', {
                    detail: [@json(session('toastr'))]
                }));
            });
        </script>
    @endif
    @stack('scripts')
</body>

</html>
