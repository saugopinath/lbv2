<x-layouts.app>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pr-6 pl-6">

            <!-- Left Section -->
            <div>
                <h1 class="text-2xl font-semibold text-indigo-800 dark:text-white">
                    {{ $header }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Manage your sections and fields
                </p>
            </div>

            <!-- Right Section -->
            <div class="flex items-center gap-3">

                <button
                    x-data
                    @click="$dispatch('open-modal')"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 
                   text-white px-4 py-2 rounded-lg shadow-sm transition duration-200">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>

                    Create Section
                </button>

                <x-form.back-button
                    :url="route('tab-field-manager', ['scheme_id' => request()->query('scheme_id')])" />

            </div>
        </div>

    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <livewire:create-assign-other-form-field.create-otherfrom-attribute :data="$data" />
    </div>
    <!-- Modal -->
    <livewire:section.create-section-form :data="$data" />
</x-layouts.app>