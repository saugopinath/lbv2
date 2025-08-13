<x-layouts.app>

        <!-- Success Toast -->
        <!-- <div id="toast-success" class="flex items-left w-full sm:w-1/2 md:w-1/3 lg:w-1/4 p-4 text-sm text-green-700 bg-green-700 rounded-lg shadow dark:bg-green-800 dark:text-green-200" role="alert">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 bg-green-500 rounded-lg dark:bg-green-700">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="sr-only">Success icon</span>
            </div>
            <div class="ms-3">Item moved successfully.</div>
            <button type="button" onclick="document.getElementById('toast-success').remove()" class="ms-auto text-gray-400 hover:text-gray-900 dark:hover:text-white p-1.5 rounded focus:outline-none">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7L1 13" />
                </svg>
            </button>
        </div> -->

        <!-- Danger Toast -->
        <!-- <div id="toast-danger" class="flex items-center w-full max-w-xs p-4 text-sm text-red-700 bg-white rounded-lg shadow dark:bg-red-800 dark:text-red-200" role="alert">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 bg-red-100 rounded-lg dark:bg-red-700">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
                </svg>
                <span class="sr-only">Error icon</span>
            </div>
            <div class="ms-3">Item has been deleted.</div>
            <button type="button" onclick="document.getElementById('toast-danger').remove()" class="ms-auto text-gray-400 hover:text-gray-900 dark:hover:text-white p-1.5 rounded focus:outline-none">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7L1 13" />
                </svg>
            </button>
        </div> -->

        <!-- Dashboard Cards -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Welcome to the Dashboard </h1>
            <p class="text-gray-600 dark:text-gray-300">
                @php
                $lgd_session = session('lgd_session');
                foreach($lgd_session as $k=>$v){
                         echo $k."=".Crypt::decryptString($v)."<br/>";
                  }
                
              @endphp
            </p>
        </div>

        
</x-layouts.app>
