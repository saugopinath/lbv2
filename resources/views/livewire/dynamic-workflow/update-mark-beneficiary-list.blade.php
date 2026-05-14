<div class="w-full space-y-6">
    {{-- Header Section --}}


    {{-- Single Filter Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <h1 class="text-xl font-bold text-indigo-700">{{ $moduleName }}</h1>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Step 1: Scheme --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Select Scheme</label>
                    <livewire:scheme-dropdown-new />
                </div>
                <div class="space-y-2">
                    <label for="step_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Select Application Type</label>
                    <div class="relative">
                        <x-form.select name="selectedStepId" label="Application Type" wire:model.live="selectedStepId" class="border rounded px-3 py-2 w-full"
                            required>
                            <option value="">-- Select --</option>
                            @foreach ($stepOptions as $id => $label)
                            <option value="{{ $id }}">
                                {{ $label }}
                            </option>
                            @endforeach
                        </x-form.select>
                    </div>
                </div>
            </div>

            {{-- Action Button --}}
            <div class="mt-6 pt-6 border-t border-gray-100 flex justify-center items-center gap-3">
                @if($schemeId)
                <button wire:click="changeScheme"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Reset</span>
                </button>
                @endif

                <button wire:click="confirmSearch"
                    @if(!$schemeId) disabled @endif
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200 active:scale-95 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-indigo-600 disabled:active:scale-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <span>Search</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Results Table Section --}}

    @if ($showTable)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">

        <div class="space-y-4 animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-6 bg-emerald-500 rounded-full"></div>
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">
                        Showing: <span class="text-indigo-600">{{ $selectedStepName }}</span> List for <span class="text-indigo-600">{{ $schemeName }}</span>
                    </h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <livewire:dynamic-workflow.dynamic-request-table
                    :moduleCode="$selectedModuleCode"
                    :schemeId="$schemeId"
                    :schemeModuleId="$selectedModuleId"
                    :selectedStepId="$confirmedStepId"
                    wire:key="{{ 'drt-' . $schemeId . '-' . $selectedModuleId . '-' . $confirmedStepId }}" />
            </div>
        </div>
    </div>
    @elseif($schemeId && !empty($stepOptions))
    <div class="py-12 flex flex-col items-center justify-center text-gray-400">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .415.162.798.425 1.081m2.732-1.157a3.75 3.75 0 013.382 2.232m-3.487-1.075a3.75 3.75 0 00-3.382 2.232m3.487-1.075c.465 0 .903.113 1.29.311m-1.29-.311a3.75 3.75 0 01-1.29 7.189m2.58 0a3.75 3.75 0 00-2.58-4.608M13.5 13.5l-3 3m0 0l-3-3m3 3V12" />
            </svg>
        </div>
        <p class="text-sm font-medium">Select a step and click search to view pending requests</p>
    </div>
    @elseif($schemeId && empty($stepOptions))
    <div class="p-8 rounded-2xl bg-amber-50 border border-amber-100 flex flex-col items-center text-center">
        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>
        <h4 class="font-bold text-amber-900 mb-1">No workflow steps found</h4>
        <p class="text-sm text-amber-700 max-w-md">There are no workflow steps configured for your role in this scheme for the selected module.</p>
    </div>
    @endif
</div>