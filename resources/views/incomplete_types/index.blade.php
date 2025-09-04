<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">
            Enter Beneficiary Details Here
        </h2>
        <livewire:incomplete-type />
    </div>
    <div class="bg-white shadow-xl rounded-2xl ">

        <div>
            <livewire:incomplet-type-table />
            {{--  <livewire:edit-incomplete-modal />  --}}

        </div>
    </div>
</x-layouts.app>
