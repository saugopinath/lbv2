<div>
    @if ($already)
        <div class="bg-white shadow rounded-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-xl font-bold text-indigo-700">
                    Assign Role to Steps
                </h1>
            </div>
            <livewire:assignworkflow-datatable />
            <livewire:openassignworkflow-modal />
        </div>
    @else
        <p class="text-sm font-semibold text-red-700 bg-red-100 px-3 py-1 rounded-lg inline-block">
            Please Create Workflow Steps
        </p>
    @endif
</div>
