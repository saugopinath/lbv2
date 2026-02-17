<div class="px-6 pt-4 shrink-0">
    <h2
        class="block p-3 bg-blue-50 border border-blue-200 text-2xl rounded-lg shadow-sm dark:bg-blue-900/30 dark:border-blue-800 text-blue-700 dark:text-blue-100 font-semibold mb-3">
        {{ $heading }}
    </h2>

    {{-- <livewire:dup-aadhaar-check />
    @if($aadhaarVerified) --}}

        <nav class="flex space-x-6 pl-6 pr-6 border-b border-gray-100">
            @foreach($views as $view)
                @php $tab = $tabs[$view] ?? null; @endphp

                <button wire:click="setActiveTab({{ $view }})" class="flex items-center gap-2 pb-2 text-sm font-medium
                                                                                {{ $activeTab == $view
                    ? 'border-indigo-600 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <x-entrytab-nav-link :active="$activeTab == $view" :icon="$tab?->tab_icon">
                        {{ $tab?->tab_name ?? 'Tab ' . $view }}
                    </x-entrytab-nav-link>
                </button>

            @endforeach

        </nav>
        @if($navMessage)
            <div class="mx-6 mt-3">
                <div class="relative px-5 py-4 rounded-xl shadow-sm
                                                                    {{ $navMessageType === 'success'
                    ? 'bg-green-500/10 border-l-4 border-green-500'
                    : 'bg-red-500/10 border-l-4 border-red-500' }}">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if($navMessageType === 'success')
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @else
                                <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            <p
                                class="text-sm font-medium {{ $navMessageType === 'success' ? 'text-green-800' : 'text-red-800' }}">
                                {{ $navMessage }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if($activeTab)
            <div class="p-4">

                @includeIf("schemes.scheme_{$schemeId}.{$activeTab}", ['schemeId' => $schemeId, 'applicationId' => $applicationId, 'form_preview' => $form_preview])
            </div>
            @if($ram == null)
                {{-- ACTION BUTTONS --}}
                <div class="flex justify-between mt-6">

                    {{-- LEFT --}}
                    <div>
                        @if(!$isFirst && $prevTab)
                            <button wire:click="setActiveTab({{ $prevTab }})" class="px-4 py-2 bg-gray-500 text-white rounded">
                                Previous
                            </button>
                        @endif
                    </div>

                    {{-- RIGHT --}}
                    <div class="flex gap-2">
                        @if(!$isLast && $nextTab)
                            <button wire:click="saveAndNext({{ $nextTab }})" class="px-4 py-2 bg-indigo-600 text-white rounded">
                                Save & Next
                            </button>
                        @else
                            <button wire:click="finalSubmit" class="px-4 py-2 bg-green-600 text-white rounded">
                                Submit
                            </button>
                        @endif
                    </div>

                </div>

            @endif
        @endif
        <livewire:final-submit-modal />
    {{-- @endif --}}
</div>