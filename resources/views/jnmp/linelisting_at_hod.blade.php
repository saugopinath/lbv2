<x-layouts.app>
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-md">
            <div class="p-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        Janma Mrityu Death Cases in LB
                    </h1>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    These beneficiaries were de-activated as per death incidents received from Janma Mrityu Portal.
                </p>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="mb-8">
        <form action="{{ route('jnmp-marked-data') }}" method="post">
            @csrf

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-md">
                <div class="p-6">
                    <!-- Filter Header -->
                    <div class="flex items-center mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 mr-3">
                            <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Filter Records</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select district to view beneficiaries</p>
                        </div>
                    </div>

                    <!-- Filter Content -->
                    <div class="space-y-6">
                        @if ($districts)
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        District
                                    </span>
                                </label>
                                <select name="district" required
                                    class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200 appearance-none cursor-pointer">
                                    <option value="" class="text-gray-400">-- Choose District --</option>
                                    @foreach ($districts as $dist)
                                        <option value="{{ $dist->lgd_code }}" 
                                            {{ (string) $dist->lgd_code === (string) $district ? 'selected' : '' }}
                                            class="py-2 hover:bg-blue-50 dark:hover:bg-gray-600">
                                            {{ $dist->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-2">
                            <x-button.primary type="submit"
                                class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                GO
                            </x-button.primary>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table Section -->
    @if(!empty($district))
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 animate-fade-in">
            <div class="p-6">
                <!-- Table Header -->
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                            List of Beneficiaries
                        </h2>                        
                    </div>
                    <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                            District: {{ $districts->where('lgd_code', $district)->first()->name ?? 'Selected' }}
                        </span>
                    </div>
                </div>

                <!-- Livewire Table Component -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <livewire:jnmp-beneficiary-listing-details-data-table :district="$district" />
                </div>
            </div>
        </div>      
    @endif

</x-layouts.app>