<div>
    {{-- ─────────────────────────────────────────────────────────────────────
         Progress Stepper
    ───────────────────────────────────────────────────────────────────── --}}
    <div class="mb-8">
        <div class="flex items-center justify-center gap-0">

            {{-- Step 1: Scheme --}}
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shadow
                    {{ $step >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                    @if($step > 1)
                        <i class="fas fa-check text-xs"></i>
                    @else
                        1
                    @endif
                </div>
                <span class="mt-1 text-[11px] font-semibold {{ $step >= 1 ? 'text-indigo-700' : 'text-gray-400' }}">
                    Scheme
                </span>
            </div>

            @if(!$modulePreset)
            {{-- Step 2: App Type (only shown when module is NOT preset) --}}
            <div class="w-20 h-0.5 mb-4 {{ $step >= 2 ? 'bg-indigo-500' : 'bg-gray-200' }}"></div>
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shadow
                    {{ $step >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                    @if($step > 2)
                        <i class="fas fa-check text-xs"></i>
                    @else
                        2
                    @endif
                </div>
                <span class="mt-1 text-[11px] font-semibold {{ $step >= 2 ? 'text-indigo-700' : 'text-gray-400' }}">
                    App Type
                </span>
            </div>
            @endif

            {{-- Step 3 (or Step 2 in preset mode): Requests --}}
            <div class="w-20 h-0.5 mb-4 {{ $step >= 3 ? 'bg-indigo-500' : 'bg-gray-200' }}"></div>
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shadow
                    {{ $step >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                    {{ $modulePreset ? 2 : 3 }}
                </div>
                <span class="mt-1 text-[11px] font-semibold {{ $step >= 3 ? 'text-indigo-700' : 'text-gray-400' }}">
                    Requests
                </span>
            </div>

        </div>
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────
         STEP 1 — Scheme Selection
    ───────────────────────────────────────────────────────────────────── --}}
    @if($step === 1)
    <div class="max-w-lg mx-auto bg-white rounded-2xl shadow-xl border border-gray-100 p-8 animate__animated animate__fadeIn">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-layer-group text-indigo-600"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-800">Select Scheme</h3>
                <p class="text-xs text-gray-500">Choose the scheme you want to process requests for</p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                    Scheme <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select wire:model.live="selectedSchemeId"
                        id="schemeSelect"
                        class="w-full appearance-none border border-gray-300 rounded-xl px-4 py-3 pr-10 text-sm bg-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none transition">
                        <option value="">-- Choose Scheme --</option>
                        @foreach($schemes as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
                @error('selectedSchemeId')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if($selectedSchemeId && !$modulePreset && empty($moduleOptions))
            <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-xs text-amber-700 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                No application types are configured for your role in this scheme.
            </div>
            @endif

            @php
                $canProceed = $selectedSchemeId && ($modulePreset ? $selectedModuleId : !empty($moduleOptions));
                $nextLabel  = $modulePreset ? 'View Requests' : 'Next — Select Application Type';
            @endphp

            <button wire:click="confirmScheme"
                @if(!$canProceed) disabled @endif
                class="w-full py-3 rounded-xl text-sm font-bold uppercase tracking-wide transition active:scale-95
                    {{ $canProceed ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-200' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}">
                {{ $nextLabel }}
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ─────────────────────────────────────────────────────────────────────
         STEP 2 — Application Type (Module) Selection
    ───────────────────────────────────────────────────────────────────── --}}
    @if($step === 2)
    <div class="max-w-lg mx-auto bg-white rounded-2xl shadow-xl border border-gray-100 p-8 animate__animated animate__fadeIn">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-folder-open text-violet-600"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-800">Select Application Type</h3>
                <p class="text-xs text-gray-500">Choose the workflow module / operation type</p>
            </div>
        </div>

        <div class="space-y-4">
            {{-- Application type cards --}}
            <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                @foreach($moduleOptions as $smId => $moduleName)
                <label for="module_{{ $smId }}"
                    class="flex items-center gap-3 border rounded-xl px-4 py-3 cursor-pointer transition
                        {{ (int)$selectedModuleId === (int)$smId ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-300' : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50' }}">
                    <input type="radio"
                        id="module_{{ $smId }}"
                        wire:model="selectedModuleId"
                        value="{{ $smId }}"
                        class="accent-indigo-600 w-4 h-4 shrink-0">
                    <span class="text-sm font-semibold text-gray-800">{{ $moduleName }}</span>
                </label>
                @endforeach
            </div>

            @error('selectedModuleId')
            <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex gap-3 pt-2">
                <button wire:click="goBack"
                    class="flex-1 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition active:scale-95">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </button>
                <button wire:click="confirmModule"
                    @if(!$selectedModuleId) disabled @endif
                    class="flex-1 py-2.5 rounded-xl text-sm font-bold uppercase tracking-wide transition active:scale-95
                        {{ $selectedModuleId ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-200' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}">
                    View Requests <i class="fas fa-table ml-1"></i>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ─────────────────────────────────────────────────────────────────────
         STEP 3 — Rappasoft DataTable
    ───────────────────────────────────────────────────────────────────── --}}
    @if($step === 3)
    <div class="animate__animated animate__fadeIn">

        {{-- Header bar --}}
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-tasks text-indigo-600"></i>
                    Pending Requests
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Showing requests assigned to your role for
                    <span class="font-semibold text-indigo-700">
                        {{ $selectedModuleName ?? ($moduleOptions[$selectedModuleId] ?? $selectedModuleCode) }}
                    </span>
                </p>
            </div>
            <button wire:click="goBack"
                class="flex items-center gap-1.5 px-4 py-2 border border-gray-300 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition active:scale-95">
                <i class="fas fa-arrow-left text-xs"></i>
                {{ $modulePreset ? 'Change Scheme' : 'Change Module' }}
            </button>
        </div>

        {{-- DataTable --}}
        <livewire:dynamic-workflow.dynamic-request-table
            :moduleCode="$selectedModuleCode"
            :schemeId="$selectedSchemeId"
            :schemeModuleId="$selectedModuleId"
            wire:key="{{ 'drt-' . $selectedSchemeId . '-' . $selectedModuleId }}"
        />

        {{-- Modal --}}
        @livewire('dynamic-workflow.process-workflow-modal')

    </div>
    @endif

</div>
