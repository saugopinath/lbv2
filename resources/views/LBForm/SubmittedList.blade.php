<x-layouts.app>


    <div class="flex flex-col gap-5 min-h-[calc(100vh-188px)] sm:min-h-[calc(100vh-204px)]">
        <div class="grid grid-cols-1 gap-5">

   @livewire('filter.filter-lgd-master', ['login_type' => $login_type])
            @livewire('process-application.process-application-table')

        </div>
    </div>
</x-layouts.app>
