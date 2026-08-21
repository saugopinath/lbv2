{{-- 
    Alpine Component Wrapper & Data Bridge
    - x-data="hybridFormGuard()": Initializes the main Alpine JS form guard component scoped to this container.
    - <script id="active-tab-rules">: Injects active tab Laravel validation rules as JSON. 
      This allows client-side Alpine logic to read server validation rules without extra API calls.
--}}
<div class="px-6 pt-4 shrink-0" x-data="hybridFormGuard()">

    {{-- Active tab validation rules injected as JSON for client-side evaluation --}}
    <script id="active-tab-rules" type="application/json">
        {!! json_encode($activeRules) !!}
    </script>

    <h2 class="block p-3 bg-blue-50 border border-blue-200 text-2xl rounded-lg shadow-sm dark:bg-blue-900/30 dark:border-blue-800 text-blue-700 dark:text-blue-100 font-semibold mb-3">
        {{ $heading }}
    </h2>
    @if (!$isEdit)
        <livewire:dup-aadhaar-check-v2 :scheme-id="$schemeId" />
    @endif
    @if ($aadhaarVerified)

        <nav class="flex space-x-6 pl-6 pr-6 border-b border-gray-100">
            @foreach ($views as $view)
                @php $tab = $tabs[$view] ?? null; @endphp

                <button class="flex items-center gap-2 pb-2 text-sm font-medium
                                                                                {{ $activeTab == $view ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}" wire:click="setActiveTab({{ $view }})" x-on:click="Livewire.dispatch('showLoader')">
                    <x-entrytab-nav-link :active="$activeTab == $view" :icon="$tab?->tab_icon">
                        {{ $tab?->tab_name ?? 'Tab ' . $view }}
                    </x-entrytab-nav-link>
                </button>
            @endforeach

        </nav>
        @if ($navMessage)
            <div class="mx-6 mt-3">
                <div class="relative px-5 py-4 rounded-xl shadow-sm
                                                                    {{ $navMessageType === 'success' ? 'bg-green-500/10 border-l-4 border-green-500' : 'bg-red-500/10 border-l-4 border-red-500' }}">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if ($navMessageType === 'success')
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill-rule="evenodd" />
                                    </svg>
                                </div>
                            @else
                                <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" fill-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium {{ $navMessageType === 'success' ? 'text-green-800' : 'text-red-800' }}">
                                {{ $navMessage }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @php
            /**
             * Normalizes backend server errors from Laravel's ViewErrorBag using level_name from $activeRules.
 */
$serverErrors = [];

if ($errors->any()) {
    foreach ($errors->keys() as $key) {
        $rawMessage = $errors->first($key);

        // 1. Resolve level_name from active rules array
        $ruleConfig = $activeRules[$key] ?? null;
        $levelName = is_array($ruleConfig) && !empty($ruleConfig['level_name']) ? $ruleConfig['level_name'] : ucwords(str_replace(['formData.', '_', '.'], ['', ' ', ' '], $key));

        // 2. Replace raw property path occurrences in Laravel's validation message with level_name
                    $rawField = str_replace('formData.', '', $key);
                    $searchPatterns = ['form data.' . $rawField, 'formData.' . $rawField, $key, $rawField];

                    $serverErrors[$key] = str_ireplace($searchPatterns, $levelName, $rawMessage);
                }
            }
            // Illuminate\Support\Facades\Log::info($serverErrors);
        @endphp

        <!-- Standalone Dynamic Error Banner Component -->
        <x-form-error :errors="$serverErrors" />

        @if ($activeTab)
            <div class="p-4">

                @includeIf("schemes.scheme_{$schemeId}.{$activeTab}", [
                    'schemeId' => $schemeId,
                    'applicationId' => $applicationId,
                    'form_preview' => $form_preview,
                ])
            </div>
            @if ($saveNext == null)
                {{-- ACTION BUTTONS --}}
                <div class="flex justify-between mt-6">

                    {{-- LEFT --}}
                    <div>
                        @if (!$isFirst && $prevTab)
                            <button class="px-4 py-2 bg-gray-500 text-white rounded" wire:click="setActiveTab({{ $prevTab }})" x-on:click="Livewire.dispatch('showLoader')">
                                Previous
                            </button>
                        @endif
                    </div>

                    {{-- RIGHT --}}
                    <div class="flex gap-2">
                        {{-- 
    Dynamic Navigation & Submission Controls
    - @if (!$isLast && $nextTab): Renders multi-step navigation for intermediate tabs.
      @click="processSaveAndNext(...)": Intercepts the click in Alpine to run client-side 
      validation before telling Livewire to advance to the next tab.
    - @else: Renders the final submission button on the last tab.
      @click="processFinalSubmit()": Intercepts the click in Alpine to enforce final tab 
      client-side validation before triggering the final Livewire submission.
--}}
                        @if (!$isLast && $nextTab)
                            <button @click="processSaveAndNext('{{ $nextTab }}')" class="px-4 py-2 bg-indigo-600 text-white rounded">
                                Save & Next
                            </button>
                        @else
                            <button @click="processFinalSubmit()" class="px-4 py-2 bg-green-600 text-white rounded">
                                Submit
                            </button>
                        @endif
                    </div>

                </div>

            @endif
        @endif
        <livewire:final-submit-modal />
    @endif
</div>
