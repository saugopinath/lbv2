<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-100 flex items-center justify-center p-4">

    <!-- Main Card - Exactly as your design -->
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-500 hover:scale-105">

        <!-- Top colored bar -->
        <div class="h-2 bg-gradient-to-r from-red-500 via-yellow-500 to-orange-500"></div>

        <div class="p-8">

            <!-- Session Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-14 h-14 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Session Info Header - Exactly as your image -->
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-4">
                Session Info
            </h1>

            <!-- Warning Message - Exactly as your image -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <p class="text-yellow-700 font-medium">
                    Your session has expired.
                </p>
            </div>

            <!-- Expiry Message - Exactly as your image -->
            <div class="text-center mb-8">
                <p class="text-gray-600 mb-2">You have been signed out due to</p>
                <div class="bg-gray-100 rounded-lg p-3 inline-block">
                    <span class="text-xl font-semibold text-orange-500">inactivity</span>
                </div>
            </div>

            <!-- Login Button - Exactly as your image but for re-login -->
            <a href="{{ route('login') }}"
                class="block w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold text-center py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-[1.02]">
                <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Login Again
                </span>
            </a>

            <!-- Session Info Footer -->
            <p class="text-center text-sm text-gray-500 mt-4">
                Session expired at: {{ $expired_at }}
            </p>
        </div>
    </div>
</body>

</html>