<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <!-- Header -->
        <div class="bg-gradient-to-r from-cyan-600 to-blue-600 px-8 py-6">
            <h2 class="text-2xl font-bold text-white flex items-center">
                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                CSV To Excel Splitter
            </h2>
            <p class="text-cyan-100 mt-1">Upload a large CSV and split it into multiple Excel files (1,000,000 records each by default).</p>
        </div>

        <div class="p-8 space-y-8">
            <!-- Upload Section -->
            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 text-center">
                <div class="flex flex-col items-center">
                    <div class="mb-4 p-4 bg-white dark:bg-gray-800 rounded-full shadow-sm">
                        <svg class="w-10 h-10 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>

                    <label class="block">
                        <span class="sr-only">Choose CSV file</span>
                        <input type="file" wire:model.live="csvFile" accept=".csv"
                            class="block w-full text-sm text-gray-500 dark:text-gray-400
                            file:mr-4 file:py-2.5 file:px-6
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-cyan-50 file:text-cyan-700
                            hover:file:bg-cyan-100
                            dark:file:bg-gray-700 dark:file:text-cyan-400" />
                    </label>

                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Max size: 100MB (CSV only). Server limit might be 2MB.</p>

                    @error('csvFile')
                    <div class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div wire:loading wire:target="csvFile" class="mt-4 text-cyan-600 dark:text-cyan-400 text-sm font-medium">
                    <span class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-cyan-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading file...
                    </span>
                </div>
            </div>

            <!-- Configuration -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Chunk Size (Records per file)</label>
                    <input type="number" wire:model="chunkSize"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all outline-none"
                        placeholder="1000000">
                    @error('chunkSize')
                    <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>
                <div class="flex items-center">
                    <button wire:click="process"
                        wire:loading.attr="disabled"
                        @if(!$csvFile || $isProcessing) disabled @endif
                        class="w-full mt-7 bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg shadow-cyan-500/30 transform transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">

                        <div wire:loading.remove wire:target="process" class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Start Processing
                        </div>

                        <div wire:loading wire:target="process" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Preparing file...
                        </div>
                    </button>
                </div>
            </div>

            <!-- Processing State -->
            @if($isProcessing)
            <div class="space-y-4" wire:poll="processBatch">
                <div class="flex items-center justify-between text-sm font-medium">
                    <span class="text-cyan-600 dark:text-cyan-400 flex items-center">
                        <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing CSV (Batching)...
                    </span>
                    <span class="text-gray-500">{{ $progress }}% - {{ count($files) }} files generated</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-cyan-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progress }}%;"></div>
                </div>
            </div>
            @endif

            <!-- Errors -->
            @if($error)
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-start text-red-700 dark:text-red-400">
                <svg class="w-5 h-5 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium">{{ $error }}</span>
            </div>
            @endif

            <!-- Results -->
            @if(count($files) > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Generated Excel Files
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($files as $file)
                    <a href="{{ $file['url'] }}" target="_blank"
                        class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-cyan-400 dark:hover:border-cyan-500 hover:shadow-md transition-all group">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg group-hover:bg-green-100 dark:group-hover:bg-green-900/40 transition-colors">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 overflow-hidden">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $file['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Excel Spreadsheet</p>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Footer Info -->
        <div class="bg-gray-50 dark:bg-gray-900/50 px-8 py-4 border-t border-gray-100 dark:border-gray-700 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest font-semibold">Premium Data Utility • Processing Engine v1.0</p>
        </div>
    </div>
</div>