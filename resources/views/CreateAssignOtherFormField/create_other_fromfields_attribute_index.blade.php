<x-layouts.app>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{$header}}</h1>
            <button
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 cursor-pointer"
                x-data
                @click="$dispatch('open-modal')">
                Create new Section
            </button>
            
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <livewire:create-assign-other-form-field.create-otherfrom-attribute :data="$data" />
    </div>
    <!-- Modal -->
    <livewire:section.create-section-form :data="$data" />
</x-layouts.app>