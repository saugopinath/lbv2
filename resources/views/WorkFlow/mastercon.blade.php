<x-layouts.app>
<div class="bg-white shadow-xl rounded-2xl p-6 space-y-4">

    @foreach($steps as $step)

        <a href="{{ route($step['route']) }}"
           class="block border rounded-xl p-4 transition
           {{ request()->routeIs($step['route']) ? 'border-purple-600 bg-purple-50' : 'border-gray-200 hover:bg-gray-50' }}">

            <div class="flex items-start space-x-4">

                <div class="w-10 h-10 flex items-center justify-center rounded-full
                    {{ request()->routeIs($step['route']) ? 'bg-purple-600 text-white' : 'bg-gray-300 text-white' }}">
                    {{ $step['step'] }}
                </div>

                <div>
                    <h3 class="font-semibold text-lg">
                        {{ $step['title'] }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ $step['description'] }}
                    </p>
                </div>

            </div>
        </a>

    @endforeach

</div>
</x-layouts.app>