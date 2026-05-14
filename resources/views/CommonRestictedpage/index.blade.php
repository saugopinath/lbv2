<x-layouts.app>
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full space-y-8 bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transform transition-all hover:scale-[1.02] duration-300">
            
            {{-- Top Banner Graphic --}}
            <div class="relative h-48 bg-gradient-to-br from-red-500 via-rose-500 to-pink-600 flex items-center justify-center overflow-hidden">
                {{-- Decorative background elements --}}
                <div class="absolute inset-0 bg-white/10" style="clip-path: polygon(0 0, 100% 0, 100% 100%, 0 80%);"></div>
                <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/20 rounded-full blur-2xl"></div>
                <div class="absolute top-4 left-4 w-16 h-16 bg-white/20 rounded-full blur-xl"></div>
                
                {{-- Icon with pulsing effect --}}
                <div class="relative z-10 bg-white/20 p-5 rounded-2xl backdrop-blur-md shadow-lg border border-white/30 animate-pulse">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>
            
            {{-- Content Section --}}
            <div class="p-8 pt-6 pb-10 text-center space-y-6 relative">
                {{-- Heading --}}
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight mb-2">Access Restricted</h2>
                    <p class="text-sm font-semibold tracking-wide text-rose-500 uppercase">Authorization Required</p>
                </div>
                
                {{-- Error Message Box --}}
                <div class="relative overflow-hidden rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 p-6 shadow-inner">
                    <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                    <p class="text-lg text-red-800 dark:text-red-300 font-medium leading-relaxed">
                        {{ $header ?? 'Oops! You do not have permission to view this page.' }}
                    </p>
                </div>
                
                {{-- Action Buttons --}}
                <div class="pt-4 flex flex-col sm:flex-row justify-center gap-4">
                    <button onclick="window.history.back()" class="inline-flex items-center justify-center px-6 py-3 border border-gray-200 dark:border-gray-700 shadow-sm text-base font-medium rounded-xl text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-2 -ml-1 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Go Back
                    </button>

                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-500/30 transform hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</x-layouts.app>