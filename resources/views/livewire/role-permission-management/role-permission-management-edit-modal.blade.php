<div>
    @if($isOpen)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full sm:w-11/12 md:w-3/4 lg:w-3/5 max-h-[90vh] flex flex-col overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <span>Edit Permissions:</span>
                            <span class="text-indigo-600 dark:text-indigo-400">{{ $roleName }}</span>
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Role ID: #{{ $roleId }}</p>
                    </div>
                    <button type="button" wire:click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-2xl font-semibold leading-none">&times;</button>
                </div>

                {{-- Controls: Search Bar & Copy from Role on Same Line --}}
                <div class="px-6 py-3 bg-slate-50 dark:bg-slate-800/60 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Search Bar --}}
                        <div class="relative flex-1 min-w-[200px]">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </div>
                            <input 
                                class="w-full pl-9 pr-10 py-1.5 text-xs rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-white placeholder-gray-400" 
                                placeholder="Search permissions by name..." 
                                type="text" 
                                wire:model.live.debounce.200ms="permissionSearch" 
                            />
                            @if (!empty($permissionSearch))
                                <button 
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors focus:outline-none" 
                                    title="Clear search" 
                                    type="button" 
                                    wire:click="$set('permissionSearch', '')"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- Separation Border --}}
                        <div class="hidden sm:block h-6 w-px bg-gray-300 dark:bg-slate-600"></div>

                        {{-- Copy from Role Section --}}
                        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Copy from Role:
                            </span>
                            <select 
                                class="text-xs rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-white py-1.5 pl-2.5 pr-7 min-w-[180px]"
                                wire:model="copyRoleId"
                            >
                                <option value="">-- Select Role --</option>
                                @foreach ($rolesList as $rId => $rName)
                                    <option value="{{ $rId }}">{{ $rName }}</option>
                                @endforeach
                            </select>
                            <button 
                                type="button" 
                                wire:click="copyRolePermissions"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 whitespace-nowrap"
                                title="Copy permissions of selected role"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                                <span>Copy</span>
                            </button>
                        </div>

                        {{-- Selected Count Badge --}}
                        @php
                            $selectedCount = count($selectedPermissions ?? []);
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 whitespace-nowrap">
                            {{ $selectedCount }} selected
                        </span>
                    </div>
                </div>

                {{-- Body (scrollable checkbox grid) --}}
                <div class="flex-1 overflow-y-auto px-6 py-4">
                    @php
                        $filteredPermissions = $this->getFilteredPermissions();
                    @endphp

                    @if(empty($filteredPermissions))
                        <div class="text-center py-8 text-xs text-gray-500 dark:text-gray-400">
                            No permissions match "<span class="font-medium text-gray-700 dark:text-gray-200">{{ $permissionSearch }}</span>"
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                            @foreach($filteredPermissions as $id => $name)
                                @php
                                    $strId = (string) $id;
                                    $isChecked = in_array($strId, array_map('strval', (array) $selectedPermissions), true);
                                @endphp
                                <label wire:key="perm-lbl-{{ $strId }}" 
                                       class="flex items-center space-x-2 p-2 rounded-lg border cursor-pointer text-xs transition-colors {{ $isChecked ? 'bg-indigo-50/80 border-indigo-200 text-indigo-950 dark:bg-indigo-950/40 dark:border-indigo-800 dark:text-indigo-200' : 'bg-white border-gray-200 hover:bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:hover:bg-slate-700/50 text-gray-700 dark:text-gray-300' }}">
                                    <input type="checkbox" 
                                           wire:key="perm-input-{{ $strId }}"
                                           wire:model.live="selectedPermissions" 
                                           value="{{ $strId }}" 
                                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer" />
                                    <span class="select-none flex-1 truncate {{ $isChecked ? 'font-semibold text-indigo-900 dark:text-indigo-100' : '' }}" title="{{ $name }}">
                                        {{ $name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="px-6 py-3 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/80 flex justify-end space-x-2">
                    <button type="button" wire:click="close"
                        class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 text-xs font-medium rounded-lg shadow-sm transition">
                        Cancel
                    </button>
                    <button type="button" wire:click="updateRolePermission"
                        class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        Update Permissions
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>