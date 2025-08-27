<x-layouts.app>
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-700">
                Users
            </h2>

            <a href="{{ route('users.create') }}"
                class="bg-blue-500 text-white px-4 py-2 rounded-2xl shadow-md hover:bg-blue-600 whitespace-nowrap cursor-pointer">
                New Users
            </a>
        </div>
    </div>
    <div class="bg-white shadow-xl rounded-2xl ">
        <div>
            {{--  <livewire:office-masters />  --}}
        </div>
    </div>
</x-layouts.app>
