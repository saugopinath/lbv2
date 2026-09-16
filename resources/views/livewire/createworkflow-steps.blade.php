<div>
    @if ($errors->any())
        <div class="p-3 bg-red-50 border border-red-200 rounded-lg my-2">
            <p class="text-xs font-bold text-red-700">Fields with Errors:</p>
            <ul class="list-disc list-inside text-xs text-red-600">
                @foreach ($errors->keys() as $key)
                    <li>{{ $key }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($originalrolerank)
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-6 space-y-6">
            <!-- Header Section -->
            <div class="pb-4 border-b border-gray-100">
                <h1 class="text-xl font-bold text-indigo-700">Create Workflow Steps</h1>
                <p class="text-xs text-gray-500 mt-1">Configure step counts and assignment rules for this workflow.</p>
            </div>

            <form class="space-y-6" wire:submit.prevent="save">
                @php
                    $isDisabled = false;
                    if ($already) {
                        $isDisabled = true;
                    }
                    $nameOnLabel = false;
                    $requiredStar = false;
                @endphp

                <!-- Step Count Field -->
                <div class="max-w-xs">
                    <x-form.input :disabled="$isDisabled" :label_placement="'left'" label="Number of Steps" max="9" name="noofSteps" placeholder="Eg: 3" required type="number" wire:model.live="noofSteps" x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '').slice(0,1);" x-on:keydown="
        const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'];
        const isNumber = /^[0-9]$/.test($event.key);
        // 1. Block non-numbers (except control keys)
        if (!isNumber && !allowedKeys.includes($event.key)) {
            $event.preventDefault();
            return;
        }" />
                </div>

                @if ($noofSteps > 0)
                    <hr class="border-gray-200 my-6">

                    <!-- Workflow Steps Container -->
                    <div class="space-y-4">
                        @foreach ($labels as $index => $value)
                            <div class="bg-gray-50/70 border border-gray-200 rounded-xl p-5 space-y-4" wire:key="step-config-{{ $index }}">
                                <!-- Step Badge Header -->
                                <div class="flex items-center gap-2 pb-2 border-b border-gray-200/60">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                    <h3 class="text-sm font-semibold text-gray-800">
                                        Step {{ $index + 1 }} Configuration
                                    </h3>
                                </div>

                                <!-- Assign Rules -->
                                <div class="space-y-2">
                                    <!-- Top Label -->
                                    <label class="block text-md font-medium text-black-800">
                                        Role Assignment <span class="text-red-700 font-bold leading-none">*</span>
                                    </label>

                                    <!-- Radio Options on Next Line -->
                                    <div class="flex flex-wrap items-center gap-6">
                                        <div>
                                            <x-form.input :label_placement="'right'" :required_star="$requiredStar" id="assignRule_1_{{ $index }}" label="Select from Existing" name="assignRule{{ $index }}" required type="radio" value="1" wire:model.live="assignRule.{{ $index }}" />
                                        </div>
                                        <div>
                                            <x-form.input :label_placement="'right'" :required_star="$requiredStar" id="assignRule_2_{{ $index }}" label="Create new Role" name="assignRule{{ $index }}" type="radio" value="2" wire:model.live="assignRule.{{ $index }}" />
                                        </div>
                                    </div>
                                </div>

                                @if ($existingRole[$index])
                                    <div class="grid gap-4 md:grid-cols-2" wire:key="workflow-step-roles-{{ $index }}">
                                        {{-- <x-form.select class="border rounded px-3 py-2 w-full" label="Role Selection" name="roleSelection{{ $index }}" required wire:model="roleSelection.{{ $index }}">
                                            <option value="">-- Select --</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role['id'] }}">
                                                    {{ $role['name'] }}
                                                </option>
                                            @endforeach
                                        </x-form.select> --}}

                                        <x-form.multiselect :options="$roles" label="Role Selection" name="roleSelection{{ $index }}[]" required wire:model.live="roleSelection.{{ $index }}" />
                                        {{-- @error("roleSelection.{$index}.role_ids")
                                            <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                                        @enderror --}}

                                    </div>
                                @elseif ($newRole[$index])
                                    <div class="grid gap-4">
                                        <div>
                                            <div class="flex items-center gap-2 mb-3">
                                                <h4 class="font-semibold text-black-800 dark:text-black-300">Permission Selection <span class="text-rose-500">*</span></h4>
                                            </div>
                                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 p-3 max-h-48 overflow-y-auto">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    @foreach ($permissionsList as $permission)
                                                        <x-form.input :label_placement="'right'" id="permissionsSelection_{{ $index }}_{{ $permission['id'] }}" label="{{ $permission['name'] }}" name="permissionsSelection{{ $index }}[]" type="checkbox" value="{{ $permission['id'] }}" wire:model.live="permissionsSelection.{{ $index }}.{{ $permission['id'] }}" />
                                                    @endforeach
                                                </div>
                                            </div>
                                            @error("permissionsSelection.{$index}")
                                                <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif

                                <!-- Label Input -->
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <x-form.input id="labels.{{ $index }}" label="Label Name {{ $index + 1 }}" name="labels.{{ $index }}" placeholder="Label Name {{ $index + 1 }}" required wire:model="labels.{{ $index }}" />
                                    </div>
                                </div>


                            </div>
                        @endforeach
                    </div>

                    <!-- Actions / Status -->
                    <div class="pt-2">
                        @if ($isDisabled)
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-green-800 bg-green-50 border border-green-200 rounded-lg">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill-rule="evenodd" />
                                </svg>
                                Already Done
                            </div>
                        @else
                            <button class="inline-flex items-center justify-center px-5 py-2.5 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-medium text-sm rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" type="submit">
                                Submit Workflow
                            </button>
                        @endif
                    </div>
                @endif
            </form>
        </div>
    @else
        <!-- Error Alert -->
        <div class="flex items-center gap-3 p-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded-xl" role="alert">
            <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path clip-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" fill-rule="evenodd" />
            </svg>
            <span class="font-medium">Please set the Original Role Rank Hierarchy first.</span>
        </div>
    @endif
</div>
