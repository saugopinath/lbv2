<x-layouts.app>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-700">
                OfficeMasters
            </h2>
           
                <a href="{{ route('office-masters') }}"
                    class="bg-blue-500 text-white px-4 py-2 rounded-2xl shadow-md hover:bg-blue-600 whitespace-nowrap cursor-pointer">
                    New OfficeMaster
                </a>
            
        </div>
    </div>
    <div class="bg-white shadow-xl rounded-2xl ">
        <div>
            <livewire:office-masters />
        </div>
    </div>
</x-layouts.app>
