<div class="bg-white shadow-xl rounded-2xl p-6 space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-2">
                <div class="p-2 bg-indigo-50 text-indigo-700 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Permissions Information</h1>
                    <p class="text-sm text-gray-500">View detailed system permissions and document their specific roles & purposes with descriptions.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Read-Only System Keys
            </span>
        </div>
    </div>

    {{-- Filter Bar: Search, Parent Filter, Guard Filter, Reset Filters & Per Page --}}
    <div class="bg-gray-50/80 border border-gray-200 rounded-xl p-4 space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200/80 pb-3">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">Filters & Search</span>
                @if (!empty($search) || !empty($parentFilter) || !empty($guardFilter))
                    <span class="px-2 py-0.5 text-[11px] font-semibold bg-indigo-100 text-indigo-700 rounded-full">
                        Active Filters
                    </span>
                @endif
            </div>

            <!-- Reset Filters Button -->
            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-xs transition-colors cursor-pointer" 
                    title="Reset all filters" 
                    type="button" 
                    wire:click="resetFilters">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                Reset Filters
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 items-start">
            <!-- Search Input -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Search Keyword</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <input class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs bg-white shadow-xs" 
                           placeholder="Search Permission Key, Parent Group, Guard, or Description..." 
                           type="text" 
                           wire:model.live.debounce.300ms="search">
                </div>
            </div>

            <!-- Parent Group Filter -->
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Parent / Group</label>
                <select class="w-full border border-gray-300 rounded-lg pl-3 pr-8 py-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs bg-white cursor-pointer shadow-xs" wire:model.live="parentFilter">
                    <option value="">All Groups</option>
                    <option value="root">Top-Level Only (No Parent)</option>
                    @foreach ($parentList as $parentId => $parentName)
                        <option value="{{ $parentId }}">{{ $parentName }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Guard Filter -->
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Guard</label>
                <select class="w-full border border-gray-300 rounded-lg pl-3 pr-8 py-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs bg-white cursor-pointer shadow-xs" wire:model.live="guardFilter">
                    <option value="">All Guards</option>
                    @foreach ($guardList as $guard)
                        <option value="{{ $guard }}">{{ $guard }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-200/60 text-xs text-gray-500">
            <div>
                Showing <span class="font-semibold text-gray-800">{{ $permissions->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-800">{{ $permissions->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-800">{{ $permissions->total() }}</span> entries
            </div>
            <div class="flex items-center space-x-2">
                <span>Per Page:</span>
                <select class="border border-gray-300 rounded-lg pl-2.5 pr-7 py-1 text-xs focus:ring-indigo-500 focus:border-indigo-500 bg-white font-semibold cursor-pointer shadow-xs" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Permissions Table --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
            <thead class="bg-gray-100/80 text-gray-700 font-semibold text-xs uppercase tracking-wider">
                <tr>
                    <th scope="col" class="py-3 px-4 w-12 text-center">#</th>
                    <th scope="col" class="py-3 px-4 min-w-[200px]">Permission Key</th>
                    <th scope="col" class="py-3 px-4 min-w-[150px]">Parent / Group</th>
                    <th scope="col" class="py-3 px-4 w-24 text-center">Guard</th>
                    <th scope="col" class="py-3 px-4 min-w-[280px]">Description</th>
                    <th scope="col" class="py-3 px-4 w-28 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-700 font-normal">
                @forelse ($permissions as $index => $permission)
                    <tr class="hover:bg-indigo-50/40 transition-colors" wire:key="perm-row-{{ $permission->id }}">
                        {{-- Row Index --}}
                        <td class="py-3 px-4 text-center text-xs font-mono text-gray-400">
                            {{ ($permissions->currentPage() - 1) * $permissions->perPage() + $loop->iteration }}
                        </td>

                        {{-- Permission Name --}}
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-900 font-mono text-xs bg-slate-100 text-slate-800 px-2 py-1 rounded border border-slate-200">
                                    {{ $permission->name }}
                                </span>
                            </div>
                        </td>

                        {{-- Parent Group --}}
                        <td class="py-3 px-4 text-xs">
                            @if ($permission->parent)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $permission->parent->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                    Top-Level (Root)
                                </span>
                            @endif
                        </td>

                        {{-- Guard --}}
                        <td class="py-3 px-4 text-center text-xs font-mono">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $permission->guard_name }}
                            </span>
                        </td>

                        {{-- Description --}}
                        <td class="py-3 px-4 text-xs leading-relaxed">
                            @if (!empty($permission->description))
                                <div class="text-gray-800 line-clamp-3" title="{{ $permission->description }}">
                                    {{ $permission->description }}
                                </div>
                            @else
                                <span class="text-gray-400 italic flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-300 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    No description provided
                                </span>
                            @endif
                        </td>

                        {{-- Action: Edit Description --}}
                        <td class="py-3 px-4 text-center">
                            <button type="button" 
                                    wire:click="openEditModal({{ $permission->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 hover:text-indigo-800 border border-indigo-200 rounded-lg text-xs font-semibold shadow-xs transition-colors cursor-pointer"
                                    title="Edit Description">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-400">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium">No permissions matched your query.</span>
                                @if (!empty($search) || !empty($parentFilter) || !empty($guardFilter))
                                    <button type="button" wire:click="resetFilters" class="text-xs text-indigo-600 hover:underline">
                                        Clear active filters
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Footer --}}
    @if ($permissions->hasPages())
        <div class="pt-2">
            {{ $permissions->links() }}
        </div>
    @endif

    {{-- Edit Description Modal --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                     wire:click="closeEditModal"
                     aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal Panel --}}
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex items-center justify-between text-white">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 bg-white/10 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-white tracking-wide" id="modal-title">
                                Edit Permission Description
                            </h3>
                        </div>
                        <button type="button" 
                                wire:click="closeEditModal"
                                class="text-indigo-200 hover:text-white rounded-lg p-1 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Form Content --}}
                    <form wire:submit.prevent="updateDescription">
                        <div class="p-6 space-y-4">
                            
                            {{-- Read-only Information Card --}}
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 space-y-2 text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 font-medium">Permission Key:</span>
                                    <span class="font-mono font-bold text-gray-900 bg-white px-2 py-0.5 rounded border border-gray-200">{{ $editingPermissionName }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 font-medium">Parent / Group:</span>
                                    <span class="font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">{{ $editingPermissionParentName }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 font-medium">Guard:</span>
                                    <span class="font-mono font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">{{ $editingPermissionGuard }}</span>
                                </div>
                            </div>

                            {{-- Description Field --}}
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide">
                                        Description (3-4 Lines)
                                    </label>
                                    <span class="text-[11px] text-gray-400">
                                        {{ strlen($editDescription) }}/1000 chars
                                    </span>
                                </div>
                                <textarea wire:model="editDescription" 
                                          rows="4" 
                                          maxlength="1000"
                                          placeholder="Enter a clear explanation of what actions or features this permission authorizes..."
                                          class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs text-gray-800 bg-white transition shadow-inner @error('editDescription') border-red-400 bg-red-50/20 @else border-gray-300 @enderror"></textarea>
                                
                                @error('editDescription')
                                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror

                                <p class="mt-1 text-[11px] text-gray-500">
                                    Provide a concise explanation of what access or operation this permission grants in the system.
                                </p>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="bg-gray-50 px-6 py-3.5 flex items-center justify-end gap-2 border-t border-gray-200">
                            <button type="button" 
                                    wire:click="closeEditModal"
                                    class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 font-medium rounded-lg border border-gray-300 text-xs transition cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                                <svg wire:loading class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Save Description</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
