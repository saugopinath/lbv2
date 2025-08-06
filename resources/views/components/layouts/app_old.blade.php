<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Premium Tailwind CSS Admin & Dashboard Template" />
    <meta name="author" content="Webonzer" />
    <!-- Site Tiltle -->
    <title>Lakshmir Bhandar | Government of West Bengal</title>
    <!-- Favicon Icon -->
    <link rel="shortcut icon" href="{{asset('images/biswofab.ico')}}">
    <!-- Style Css -->
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="main" class="font-inter text-base antialiased font-medium relative vertical" :class="[ $store.app.sidebar ? 'toggle-sidebar' : '', $store.app.fullscreen ? 'full' : '',$store.app.mode]">

    <!-- Start Layout -->
    <div class="bg-white dark:bg-dark text-dark dark:text-white">

        <!-- Start Menu Sidebar Olverlay -->
        <div x-cloak class="fixed inset-0 bg-dark/90 dark:bg-white/5 backdrop-blur-sm z-40 lg:hidden" :class="{'hidden' : !$store.app.sidebar}" @click="$store.app.toggleSidebar()"></div>
        <!-- End Menu Sidebar Olverlay -->

        <!-- Start Main Content -->
        <div class="main-container flex mx-auto">
            <!-- Start Sidebar -->
           
            <!-- End sidebar -->

            <!-- Start Content Area -->
            <div class="main-content flex-1">
                <!-- Start Topbar -->
              
                <!-- End Topbar -->

                <!-- Start Content -->
                <div class="h-[calc(100vh-60px)] relative overflow-y-auto overflow-x-hidden p-5 sm:p-7 space-y-5">
                    <!-- Start All Card -->
                    {{ $slot }}
                    <!-- End All Card -->

                    <!-- Start Footer -->
                    <footer class="bg-white dark:bg-dark dark:border-gray/20 border-2 border-lightgray/10 p-5 rounded-lg flex flex-wrap justify-center gap-3 sm:justify-between items-center">
                        <p class="font-semibold">
                            &copy;
                            <script>
                                var year = new Date(); document.write(year.getFullYear());
                            </script>
                            DashHub
                        </p>
                        <ul class="sm:flex items-center text-dark dark:text-white gap-4 sm:gap-[30px] font-semibold hidden">
                            <li><a href="javascirpt:;" class="hover:text-primary transition-all duration-300 cursor-pointer">About</a></li>
                            <li><a href="javascirpt:;" class="hover:text-primary transition-all duration-300 cursor-pointer">Support</a></li>
                            <li><a href="javascirpt:;" class="hover:text-primary transition-all duration-300 cursor-pointer">Contact Us</a></li>
                        </ul>
                    </footer>
                    <!-- End Footer -->

                </div>
                <!-- End Content -->
            </div>
            <!-- End Content Area -->
        </div>
    </div>
    <!-- End Layout -->

    <!-- All javascirpt -->
    <!-- Alpine js -->
    

    <!-- Custom js -->

    @livewireScripts
</body>


</html>