<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Header with gradient background -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5 sm:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        Global Dynamic Workflow Management
                    </h2>
                    <p class="text-indigo-100 text-sm mt-1">Configure and manage your workflow steps</p>
                </div>
                <div class="flex items-center gap-4">
                    <span
                        class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white font-medium text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        Step {{ $currentTab }} of {{ $totalTabs }}
                    </span>
                    <div class="w-32 h-2 bg-white/20 rounded-full overflow-hidden">
                        <div class="h-full bg-white rounded-full transition-all duration-500"
                            style="width: {{ ($currentTab / $totalTabs) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @if (session()->has('success'))
            <div class="mx-6 mt-6 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg p-4" role="alert">
                <div class="flex items-center text-emerald-700">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mx-6 mt-6 bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4" role="alert">
                <div class="flex items-center text-red-700">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            </div>
        @endif

        <div class="p-6 sm:p-8">
            <!-- Progress Steps -->
            <div class="relative mb-10">
                <div class="absolute top-5 left-0 w-full h-0.5 bg-gray-200"></div>
                <div class="relative flex justify-between">
                    <div class="flex flex-col items-center">
                        <div
                            class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentTab >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600' }} font-bold text-sm relative z-10">
                            @if ($currentTab > 1)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @else
                                1
                            @endif
                        </div>
                        <span
                            class="text-xs font-medium mt-2 {{ $currentTab == 1 ? 'text-indigo-600' : 'text-gray-500' }}">Module
                            Selection</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div
                            class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentTab >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600' }} font-bold text-sm relative z-10">
                            @if ($currentTab > 2)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @else
                                2
                            @endif
                        </div>
                        <span
                            class="text-xs font-medium mt-2 {{ $currentTab == 2 ? 'text-indigo-600' : 'text-gray-500' }}">Step
                            Configuration</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div
                            class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentTab >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600' }} font-bold text-sm relative z-10">
                            3</div>
                        <span
                            class="text-xs font-medium mt-2 {{ $currentTab == 3 ? 'text-indigo-600' : 'text-gray-500' }}">Final
                            Setup</span>
                    </div>
                </div>
            </div>

            <!-- TAB 1: MODULE SELECTION / CREATION -->
            @if ($currentTab == 1)
                <div class="space-y-8">
                    <div class="text-center mb-8">
                        <span
                            class="inline-flex items-center px-4 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-sm font-medium mb-3">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            Step 1: Setup
                        </span>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Select Scheme & Choose Module</h3>
                        <p class="text-gray-500">Choose your working context and define the module</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                            <div class="w-14 h-14 bg-indigo-50 rounded-xl flex items-center justify-center mb-4">
                                <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4">
                                    </path>
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-gray-900 mb-2">1. Select Target Scheme</h5>
                            <p class="text-sm text-gray-500 mb-4">Choose the scheme context for your workflow</p>
                            <select wire:model="selectedScheme"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all">
                                <option value="">-- Choose Scheme --</option>
                                @foreach ($schemes as $scheme)
                                    <option value="{{ $scheme->id }}">{{ $scheme->name }} (ID: {{ $scheme->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if ($selectedScheme)
                            <div
                                class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center">
                                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="text-lg font-bold text-gray-900">2. Module Configuration</h5>
                                        <p class="text-sm text-gray-500">Select existing or create new module</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Choose from Master
                                        Modules</label>
                                    <select wire:model="selectedModule"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all"
                                        @if ($isNewModule) disabled @endif>
                                        <option value="">-- Select Existing Module --</option>
                                        @foreach ($moduleList as $mod)
                                            <option value="{{ $mod->id }}">{{ $mod->module_name }}
                                                ({{ $mod->module_code }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <label class="flex items-center gap-3 mb-4 p-3 bg-gray-50 rounded-xl cursor-pointer">
                                    <input type="checkbox" wire:model.live="isNewModule"
                                        class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-700">Create a new module instead</span>
                                </label>

                                @if ($isNewModule)
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <h6 class="font-medium text-gray-900 mb-3">New Module Details</h6>
                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 mb-1">Module
                                                    Name</label>
                                                <input type="text" wire:model="newModuleName"
                                                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 @error('newModuleName') border-red-500 @enderror"
                                                    placeholder="e.g., Caste Correction">
                                                @error('newModuleName')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 mb-1">Module
                                                    Code</label>
                                                <input type="text" wire:model="newModuleCode"
                                                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 uppercase @error('newModuleCode') border-red-500 @enderror"
                                                    placeholder="e.g., CASTE_CORR">
                                                @error('newModuleCode')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-200">
                        <button type="button" wire:click="moveToNaming"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors shadow-lg hover:shadow-xl">
                            Continue to Step 2
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- TAB 2: STEP COUNT & NAMES -->
            @if ($currentTab == 2)
                <div class="space-y-8">
                    <div class="text-center mb-4">
                        <span
                            class="inline-flex items-center px-4 py-1.5 bg-sky-50 text-sky-700 rounded-full text-sm font-medium mb-3">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            Step 2: Configuration
                        </span>
                        <p class="text-gray-500">Set the number of steps and customize</p>
                    </div>

                    <div class="max-w-md mx-auto">
                        <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl border border-gray-200 p-8">
                            <label class="block text-sm font-medium text-gray-600 text-center mb-4">Number of
                                Processing Steps</label>
                            <div class="flex items-center justify-center gap-4">
                                <button wire:click="decrementStepCount"
                                    class="w-12 h-12 rounded-full border-2 border-gray-300 hover:border-indigo-600 text-gray-600 hover:text-indigo-600 flex items-center justify-center transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <span
                                    class="text-5xl font-bold text-indigo-600 min-w-[100px] text-center">{{ $stepCount }}</span>
                                <button wire:click="incrementStepCount"
                                    class="w-12 h-12 rounded-full border-2 border-gray-300 hover:border-indigo-600 text-gray-600 hover:text-indigo-600 flex items-center justify-center transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($stepNames as $index => $name)
                            <div
                                class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-3 mb-3">
                                    <span
                                        class="w-8 h-8 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-bold text-sm">{{ $index + 1 }}</span>
                                    <h6 class="font-medium text-gray-700">Step {{ $index + 1 }}</h6>
                                </div>
                                <input type="text" wire:model="stepNames.{{ $index }}"
                                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                                    placeholder="Enter step name..." value="{{ $name }}">
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <button type="button" wire:click="goBack"
                            class="inline-flex items-center px-6 py-3 border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-xl transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back
                        </button>
                        <button type="button" wire:click="moveToConfig"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors shadow-lg hover:shadow-xl">
                            Continue to Final Step
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- TAB 3: DETAILED CONFIGURATION -->
            @if ($currentTab == 3)
                <div class="space-y-8">
                    <div class="text-center mb-8">
                        <span
                            class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-700 rounded-full text-sm font-medium mb-3">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Step 3: Finalization
                        </span>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Finalize Workflow Configuration</h3>
                        <p class="text-gray-500">Assign roles and set final steps for each stage</p>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Rank</th>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Step Name</th>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Assign Roles</th>
                                        <th scope="col"
                                            class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Final Step</th>
                                        <th scope="col"
                                            class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Permission</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($finalSteps as $index => $cfg)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">#{{ $cfg['rank'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="text-sm font-medium text-gray-900">{{ $cfg['label'] }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div wire:key="workflow-step-roles-{{ $index }}">
                                                    <x-form.multiselect label="Assign Roles"
                                                        wire:model="finalSteps.{{ $index }}.role_ids"
                                                        :options="$roles" required />
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox"
                                                        wire:model="finalSteps.{{ $index }}.is_final"
                                                        class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                                </label>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div wire:key="workflow-step-permission-{{ $index }}"
                                                    class="w-64">
                                                    <x-form.multiselect label="Permissions"
                                                        wire:model="finalSteps.{{ $index }}.permissions"
                                                        :options="$permissionsList" allowCustom="true" required />
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary Card -->
                    <div class="bg-amber-50 rounded-xl border border-amber-200 p-5">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h6 class="font-semibold text-amber-800 mb-1">Workflow Summary</h6>
                                <p class="text-sm text-amber-700">
                                    You're creating a workflow with <span
                                        class="font-bold">{{ count($finalSteps) }}</span> steps.
                                    Please ensure all roles are assigned before saving.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <button type="button" wire:click="goBack"
                            class="inline-flex items-center px-6 py-3 border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-xl transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back
                        </button>
                        <button type="button" wire:click="saveWorkflow"
                            class="inline-flex items-center px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-colors shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                </path>
                            </svg>
                            Save Workflow Configuration
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
