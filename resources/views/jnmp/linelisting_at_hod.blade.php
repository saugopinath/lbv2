<x-layouts.app>
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Janma Mrityu Death Cases in LB (These beneficiaries were de-activated as per death incidents
                    received from Janma Mrityu Portal.)
                    </h1>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="mb-6">
        <form action="{{ route('jnmpMarkedData') }}" method="post">
            @csrf

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-5">

                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Filter Here</h2>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">

                        @if ($districts)
                            <x-form.select name="district" label="Districts" required>
                                <option value="">--Choose District--</option>

                                @foreach ($districts as $dist)
                                    <option value="{{ $dist->lgd_code }}" {{ (string) $dist->lgd_code === (string) $district ? 'selected' : '' }}>
                                        {{ $dist->name }}
                                    </option>
                                @endforeach
                            </x-form.select>
                        @endif


                        <div class="flex justify-end mt-4">
                            <x-button.primary type="submit" class="bg-blue-500 text-white whitespace-nowrap">
                                GO
                            </x-button.primary>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>


    <!-- Data Table Section -->
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    List Of Beneficiaries
                </h2>

            </div>

            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">

                @if(!empty($district))
                    <livewire:jnmp-beneficiary-listing-details-data-table :district="$district" />
                @endif


            </div>

        </div>
    </div>



</x-layouts.app>