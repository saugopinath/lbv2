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
            <div class="pb-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-indigo-700">Create & Edit Workflow Steps</h1>
                    <p class="text-xs text-gray-500 mt-1">Configure step counts and assignment rules for this workflow.</p>
                </div>
                @if ($isEdit)
                    <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        Edit Mode
                    </span>
                @elseif ($already)
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        View Mode
                    </span>
                @endif
            </div>

            <form class="space-y-6" wire:submit.prevent="save">
                @php
                    $isDisabled = false;
                    if ($already && !$isEdit) {
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
                                            <x-form.input :disabled="$isDisabled" :label_placement="'right'" :required_star="$requiredStar" id="assignRule_1_{{ $index }}" label="Select from Existing" name="assignRule{{ $index }}" required type="radio" value="1" wire:model.live="assignRule.{{ $index }}" />
                                        </div>
                                        <div>
                                            <x-form.input :disabled="$isDisabled" :label_placement="'right'" :required_star="$requiredStar" id="assignRule_2_{{ $index }}" label="Create new Role" name="assignRule{{ $index }}" type="radio" value="2" wire:model.live="assignRule.{{ $index }}" />
                                        </div>
                                    </div>
                                </div>

                                @if ($existingRole[$index])
                                    <div class="grid gap-4 md:grid-cols-2" wire:key="workflow-step-roles-{{ $index }}">
                                        <x-form.multiselect :disabled="$isDisabled" :options="$roles" label="Role Selection" name="roleSelection{{ $index }}[]" required wire:model.live="roleSelection.{{ $index }}" />
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
                                                        <x-form.input :disabled="$isDisabled" :label_placement="'right'" id="permissionsSelection_{{ $index }}_{{ $permission['id'] }}" label="{{ $permission['name'] }}" name="permissionsSelection{{ $index }}[]" type="checkbox" value="{{ $permission['id'] }}" wire:model.live="permissionsSelection.{{ $index }}.{{ $permission['id'] }}" />
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
                                        <x-form.input :disabled="$isDisabled" id="labels.{{ $index }}" label="Label Name {{ $index + 1 }}" name="labels.{{ $index }}" placeholder="Label Name {{ $index + 1 }}" required wire:model="labels.{{ $index }}" />
                                    </div>
                                </div>

                                <!-- Optional Specific User Selection Checkbox & Trigger Button -->
                                <div class="pt-2 space-y-3">
                                    <div class="flex items-center gap-2">
                                        <input @if ($isDisabled) disabled @endif class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" id="assignSpecificUsers_{{ $index }}" type="checkbox" wire:model.live="assignSpecificUsers.{{ $index }}">
                                        <label class="text-sm font-medium text-gray-700 cursor-pointer" for="assignSpecificUsers_{{ $index }}">
                                            Assign to Specific Users (Optional)
                                        </label>
                                    </div>

                                    @if (!empty($assignSpecificUsers[$index]))
                                        <div class="flex items-center gap-3 pl-6">
                                            <button @if ($isDisabled) disabled @endif class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500" type="button" wire:click="openUserModal({{ $index }})">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                </svg>
                                                {{ ($assignRule[$index] ?? '1') == '2' ? 'Select Users to Assign Role' : 'Select the Users to give access to' }}
                                            </button>
                                            @php
                                                $selectedCount = count($selectedUserIdsByStep[$index] ?? []);
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $selectedCount > 0 ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $selectedCount }} {{ Str::plural('user', $selectedCount) }} selected
                                            </span>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <!-- Actions / Status -->
                    <div class="pt-2 flex items-center gap-3">
                        @if ($isDisabled)
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-green-800 bg-green-50 border border-green-200 rounded-lg">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill-rule="evenodd" />
                                </svg>
                                Already Configured (View Mode)
                            </div>
                        @else
                            <button class="inline-flex items-center justify-center px-5 py-2.5 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-medium text-sm rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" type="submit">
                                {{ $isEdit ? 'Update Workflow Steps' : 'Submit Workflow' }}
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

    <!-- User Selection Modal -->
    @if ($showUserModal && $activeStepForUserModal !== null)
        <div aria-labelledby="modal-title" aria-modal="true" class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-4 sm:p-6" role="dialog">
            <!-- Modal Backdrop with Blur -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeUserModal"></div>

            <!-- Modal Box with Expanded Dimensions & Responsive Styling -->
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-6xl h-[90vh] max-h-[900px] flex flex-col overflow-hidden border border-gray-200 z-10 my-auto">
                <!-- Header -->
                <div class="flex-none bg-indigo-700 text-white px-6 py-4 flex items-center justify-between border-b border-indigo-800">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        <h3 class="text-base font-bold">Select Users for Step {{ $activeStepForUserModal + 1 }} Configuration</h3>
                    </div>
                    <button class="text-indigo-200 hover:text-white transition-colors focus:outline-none p-1 rounded-lg hover:bg-indigo-600" type="button" wire:click="closeUserModal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </button>
                </div>

                <!-- Body & Filters -->
                <div class="flex-1 flex flex-col min-h-0 p-5 space-y-4 overflow-visible bg-white">
                    <!-- Combined Filter & Selection Control Panel -->
                    <div class="flex-none bg-slate-50/70 rounded-xl border border-slate-200/80 shadow-sm relative z-30">
                        <!-- Upper Action & Stats Bar -->
                        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-2.5 bg-slate-100/60 border-b border-slate-200/70 rounded-t-xl">
                            <!-- Left: Selected Users Count Badge -->
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200/60 text-xs font-semibold">
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Selected Users: <span class="font-bold text-indigo-900">{{ count($selectedUserIdsByStep[$activeStepForUserModal] ?? []) }}</span>
                                </span>
                            </div>

                            <!-- Right: Mass Selection & Reset Buttons -->
                            <div class="flex flex-wrap items-center gap-2">
                                @php
                                    $modalUsersList = $this->modalUsers;
                                    $pageUserIds = $modalUsersList !== 'NO_ROLE_SELECTED' && $modalUsersList ? $modalUsersList->pluck('id')->map(fn($id) => (string) $id)->toArray() : [];
                                @endphp

                                <!-- Select All Filtered Records Button -->
                                <button @if ($modalUsersList === 'NO_ROLE_SELECTED') disabled @endif class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors" type="button" wire:click="selectAllFilteredUsers">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Select All Filtered Records
                                </button>

                                <!-- Clear Selection Button -->
                                <button class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200/80 text-xs font-semibold rounded-lg transition-colors" type="button" wire:click="clearStepUserSelection">
                                    Clear Selection
                                </button>

                                <div class="h-4 border-r border-slate-300 mx-0.5 hidden sm:block"></div>

                                <!-- Reset Filters Button -->
                                <button class="px-3 py-1.5 bg-white hover:bg-slate-200/60 text-slate-700 border border-slate-300/80 text-xs font-semibold rounded-lg transition-colors flex items-center justify-center gap-1.5 shadow-2xs" title="Clear all filter selections" type="button" wire:click="resetModalFilters">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Reset Filters
                                </button>
                            </div>
                        </div>

                        <!-- Lower Filter Inputs Grid -->
                        <div class="p-3.5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 items-start">
                                <!-- Search Input -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Search User</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <input class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm" placeholder="Search Name, Email, Mobile..." type="text" wire:model.live="modalSearch">
                                    </div>
                                </div>

                                <!-- Role Multi-Select Filter -->
                                <div>
                                    <x-form.multiselect :options="$roles" labelClass="block text-xs font-medium text-gray-500 uppercase mb-1" label="Role" placeholder="Select Role" wire:model.live="modalRoles" />
                                </div>

                                <!-- Office Type Multi-Select Filter -->
                                <div>
                                    <x-form.multiselect :options="$officeTypesList" labelClass="block text-xs font-medium text-gray-500 uppercase mb-1" label="Office Type" placeholder="Select Office Type" wire:model.live="modalOfficeTypes" />
                                </div>

                                <!-- Office Multi-Select Filter -->
                                <div>
                                    <x-form.multiselect :options="$officesList" labelClass="block text-xs font-medium text-gray-500 uppercase mb-1" label="Office" placeholder="Select Office" wire:model.live="modalOffices" />
                                </div>

                                <!-- Scheme Multi-Select Filter -->
                                <div>
                                    <x-form.multiselect :options="$schemesList" labelClass="block text-xs font-medium text-gray-500 uppercase mb-1" label="Scheme" placeholder="Select Scheme" wire:model.live="modalSchemes" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Users Content Area -->
                    <div class="flex-1 flex flex-col justify-between min-h-0">
                        @php
                            $modalUsersList = $this->modalUsers;
                        @endphp

                        {{-- COMMENTED FOR BACKWARD COMPATIBILITY: NO_ROLE_SELECTED mode lock warning is removed so modal filters remain flexible
                        @if ($modalUsersList === 'NO_ROLE_SELECTED')
                            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-amber-50/70 rounded-xl border border-amber-200 text-amber-800 space-y-2">
                                <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <h4 class="font-bold text-base">Select Role First</h4>
                                <p class="text-xs text-amber-700 max-w-md">
                                    In "Select from Existing" mode, users are filtered based on the role(s) selected for this step. Please select at least one role in the step configuration above.
                                </p>
                            </div>
                        @else
                        --}}

                        @php
                            $pageUserIds = $modalUsersList && $modalUsersList !== 'NO_ROLE_SELECTED' ? $modalUsersList->pluck('id')->map(fn($id) => (string) $id)->toArray() : [];
                            $currentSelectedForStep = array_map('strval', $selectedUserIdsByStep[$activeStepForUserModal] ?? []);
                            $isAllVisibleSelected = count($pageUserIds) > 0 && count(array_diff($pageUserIds, $currentSelectedForStep)) === 0;
                        @endphp

                        <!-- Users Scrollable Table -->
                        <div class="flex-1 min-h-0 overflow-y-auto rounded-xl border border-gray-200 bg-white">
                            <table class="w-full table-fixed divide-y divide-gray-200 text-xs">
                                <thead class="bg-gray-50 sticky top-0 z-10">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-bold text-gray-700 w-12 bg-gray-50" scope="col">
                                            <input {{ $isAllVisibleSelected ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer" type="checkbox" wire:change="toggleSelectAllVisible({{ json_encode($pageUserIds) }})" wire:key="th-chk-step-{{ $activeStepForUserModal }}-{{ $isAllVisibleSelected ? '1' : '0' }}-{{ count($selectedUserIdsByStep[$activeStepForUserModal] ?? []) }}">
                                        </th>
                                        <th class="px-4 py-3 text-left font-bold text-gray-700 bg-gray-50 w-2/5" scope="col">Name</th>
                                        <th class="px-4 py-3 text-left font-bold text-gray-700 bg-gray-50 w-2/5" scope="col">Email</th>
                                        <th class="px-4 py-3 text-left font-bold text-gray-700 bg-gray-50 w-1/5" scope="col">Mobile Number</th>
                                        {{-- COMMENTED FOR BACKWARD COMPATIBILITY: Multi-value columns hidden as users can have multiple Roles/Offices/Office Types
                                            <th scope="col" class="px-4 py-3 text-left font-bold text-gray-700 w-2/5 bg-gray-50">Role</th>
                                            <th scope="col" class="px-4 py-3 text-left font-bold text-gray-700 w-1/5 bg-gray-50">Office Type</th>
                                            --}}
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($modalUsersList as $u)
                                        @php
                                            $uIdStr = (string) $u->id;
                                            $isSelected = in_array($uIdStr, $currentSelectedForStep, true);

                                            $rolesList = $u->mappedRoles
                                                ->pluck('name')
                                                ->merge($u->RoleSchemeOfficeMappings->pluck('Role.name')->filter())
                                                ->merge($u->roles->pluck('name'))
                                                ->filter()
                                                ->unique()
                                                ->implode(', ');

                                            if (empty($rolesList)) {
                                                $rolesList = 'N/A';
                                            }

                                            $officeTypeName = $u->RoleSchemeOfficeMappings->pluck('office.officeType.name')->filter()->unique()->implode(', ');
                                            if (empty($officeTypeName)) {
                                                $officeTypeName = 'N/A';
                                            }
                                        @endphp
                                        <tr class="{{ $isSelected ? 'bg-indigo-50/40' : 'hover:bg-gray-50' }}">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input {{ $isSelected ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer" type="checkbox" wire:change="toggleSelectUser('{{ $u->id }}')" wire:key="row-chk-step-{{ $activeStepForUserModal }}-usr-{{ $u->id }}-{{ $isSelected ? '1' : '0' }}">
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900 truncate" title="{{ $u->name }}">
                                                <div class="truncate font-semibold">{{ $u->name }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-600 truncate" title="{{ $u->email }}">
                                                <span class="truncate block">{{ $u->email ?: 'N/A' }}</span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-600 truncate" title="{{ $u->mobile_no }}">
                                                <span class="truncate block">{{ $u->mobile_no ?: 'N/A' }}</span>
                                            </td>
                                            {{-- COMMENTED FOR BACKWARD COMPATIBILITY: Single value Role & Office Type columns
                                                <td class="px-4 py-3 whitespace-nowrap text-gray-600 truncate" title="{{ $rolesList }}">
                                                    <span class="truncate block">{{ $rolesList }}</span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-gray-600 truncate" title="{{ $officeTypeName }}">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 truncate">
                                                        {{ $officeTypeName }}
                                                    </span>
                                                </td>
                                                --}}
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="px-4 py-12 text-center text-gray-500 text-xs" colspan="4">
                                                No active users found matching your filters.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <!-- Footer with Left Pagination & Right Apply Selection -->
                <div class="flex-none bg-gray-50 px-6 py-3 flex flex-wrap items-center justify-between gap-3 border-t border-gray-200">
                    <!-- Left side: Pagination & Per Page -->
                    <div class="flex flex-wrap items-center gap-3">
                        @if ($modalUsersList && $modalUsersList !== 'NO_ROLE_SELECTED')
                            <div class="flex items-center gap-1.5 text-xs text-gray-600 font-medium @if ($modalUsersList->hasPages()) pr-3 border-r border-gray-300/60 @endif">
                                <span>Per Page: </span>
                                <select class="pl-2.5 pr-7 py-1 border border-gray-300 rounded-lg text-xs focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm font-semibold cursor-pointer" wire:model.live="modalPageLimit">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                </select>
                                <span> results</span>
                            </div>
                            <!-- Pagination Links -->
                            <div class="flex-none text-xs mr-1 [&_p]:mr-1 [&_p]:pr-1 [&_div.sm\:flex]:gap-1 [&_div.sm\:justify-between]:gap-1">
                                {{ $modalUsersList->links() }}
                            </div>
                        @endif
                    </div>

                    <!-- Right side: Apply Button -->
                    <button class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs rounded-lg shadow-sm transition-colors focus:outline-none" type="button" wire:click="closeUserModal">
                        Apply Selection
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
