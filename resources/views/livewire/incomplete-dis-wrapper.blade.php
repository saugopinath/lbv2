<div class="space-y-6">

    <!-- Filter Card -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">

        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">
            Incomplete Details MIS Report
        </h2>

        <!-- Filters -->
        <div class="space-y-4">

            <livewire:filter-lgd-master :button_show="0" :wire:key="'filter-lgd'" />

            <livewire:incomplete-type :button_show="0" :wire:key="'incomplete-type'" />

            <!-- Action Buttons -->
            <div class="flex items-center gap-4">

                <x-button.primary
                    x-on:click="
                        Livewire.dispatch('showLoader');
                        $wire.search();
                    "
                    class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                    Search
                </x-button.primary>

                <x-button.primary 
                    wire:click="resetAll"
                    class="bg-green-500 text-white whitespace-nowrap cursor-pointer">
                    Reset
                </x-button.primary>

            </div>

        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6">
        <livewire:incomplete-mis-report-table />
    </div>

</div>
