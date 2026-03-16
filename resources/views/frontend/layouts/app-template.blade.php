<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jai Bangla @yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        /* Soft floating + glow */
        @keyframes floatingGlow {
            0% {
                transform: translateY(0px) scale(1);
                filter: drop-shadow(0 0 10px rgba(255, 196, 0, 0.3));
            }

            50% {
                transform: translateY(-10px) scale(1.05);
                filter: drop-shadow(0 0 20px rgba(255, 196, 0, 0.6));
            }

            100% {
                transform: translateY(0px) scale(1);
                filter: drop-shadow(0 0 10px rgba(255, 196, 0, 0.3));
            }
        }

        .animate-floating-glow {
            animation: floatingGlow 2.5s ease-in-out infinite;
        }


        /* Ripple Glow Backdrop */
        @keyframes ripple {
            0% {
                transform: scale(0.4);
                opacity: 0.5;
            }

            70% {
                transform: scale(1.5);
                opacity: 0;
            }

            100% {
                opacity: 0;
            }
        }

        .animate-ripple {
            animation: ripple 2.2s ease-out infinite;
        }


        /* EXIT ANIMATION — Slide Up + Blur */
        @keyframes slideUpFade {
            0% {
                opacity: 1;
                filter: blur(0px);
                transform: translateY(0);
            }

            100% {
                opacity: 0;
                filter: blur(10px);
                transform: translateY(-40px);
            }
        }

        .hideLoader {
            animation: slideUpFade 0.7s ease forwards;
        }
    </style>

    <!-- Styles -->
    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-white text-black dark:bg-gray-900 dark:text-white">
    <!-- Fullscreen Modern Loader -->
    <div id="pageLoader"
        class="fixed inset-0 bg-gray-900 flex flex-col items-center justify-center z-[9999] overflow-hidden">

        <!-- Glow Ripple -->
        <div class="absolute w-40 h-40 bg-amber-500/20 rounded-full animate-ripple"></div>

        <!-- Logo with Floating + Glow -->
        <img src="{{ asset('images/home/biswo_logo.png') }}" class="w-24 h-24 relative animate-floating-glow"
            alt="Loading">

        <p class="text-gray-300 mt-6 text-sm tracking-widest animate-pulse">
            Initializing...
        </p>
    </div>



    @yield('content')


    <!-- Scripts-->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(function () {
                const loader = document.getElementById("pageLoader");

                if (!loader) return;

                loader.classList.add("hideLoader");

                setTimeout(function () {
                    loader.remove();
                }, 700);
            }, 800);
        });
    </script>

    @stack('scripts')
</body>

</html>