
<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        {{--  @php
            dump($data);
        @endphp  --}}

        {{--  @livewire('filter.filter-lgd-master', $data)  --}}
    </div>

        <div class="flex flex-col gap-5 min-h-[calc(100vh-188px)] sm:min-h-[calc(100vh-204px)]">

            <div class="grid grid-cols-1 gap-5">


                @livewire('process-application.process-application-table')

            </div>
        </div>
</x-layouts.app>
