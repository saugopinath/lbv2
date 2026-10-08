<div class="bg-white shadow-xl rounded-2xl p-6 space-y-6" x-data="{
    showDeleteModal: false,
    deleteId: null,
    deleteSchemeName: '',
    openDeleteModal(id, schemeName) {
        this.deleteId = id;
        this.deleteSchemeName = schemeName;
        this.showDeleteModal = true;
    },
    closeDeleteModal() {
        this.showDeleteModal = false;
        this.deleteId = null;
    }
}">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-indigo-700">Schemes Management</h1>
            <p class="text-sm text-gray-500">Create, edit, toggle status, or manage government schemes.</p>
        </div>
        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow transition flex items-center gap-2 cursor-pointer" type="button" wire:click="openCreateModal">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
            <span>Add New Scheme</span>
        </button>
    </div>

    {{-- Filter Bar: Search, Department, Status, Reset Filters & Per Page --}}
    <div class="bg-gray-50/80 border border-gray-200 rounded-xl p-4 space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200/80 pb-3">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">Filters & Search</span>
                @if (!empty($search) || !empty($selectedDepartments) || !empty($statusFilter))
                    <span class="px-2 py-0.5 text-[11px] font-semibold bg-indigo-100 text-indigo-700 rounded-full">
                        Active Filters
                    </span>
                @endif
            </div>

            <!-- Reset Filters Button -->
            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-xs transition-colors cursor-pointer" title="Reset all filters" type="button" wire:click="resetFilters">
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
                    <input class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs bg-white shadow-xs" placeholder="Search Scheme Name, ID, Short Name, Department..." type="text" wire:model.live.debounce.300ms="search">
                </div>
            </div>

            <!-- Department Multi-Select Filter -->
            <div>
                <x-form.multiselect :options="$departmentsList" labelClass="block text-xs font-medium text-gray-500 uppercase mb-1" label="Department" placeholder="All Departments" wire:model.live="selectedDepartments" />
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Status</label>
                <select class="w-full border border-gray-300 rounded-lg pl-3 pr-8 py-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs bg-white cursor-pointer shadow-xs" wire:model.live="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="active">Active Only</option>
                    <option value="disabled">Disabled Only</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-200/60 text-xs text-gray-500">
            <div>
                Showing <span class="font-semibold text-gray-800">{{ $schemes->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-800">{{ $schemes->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-800">{{ $schemes->total() }}</span> entries
            </div>
            <div class="flex items-center space-x-2">
                <span>Per Page:</span>
                <select class="border border-gray-300 rounded-lg pl-2.5 pr-7 py-1 text-xs focus:ring-indigo-500 focus:border-indigo-500 bg-white font-semibold cursor-pointer shadow-xs" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="overflow-x-auto border border-gray-200 rounded-xl">
        <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
            <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-semibold">
                <tr>
                    <th class="px-4 py-3" scope="col">ID</th>
                    <th class="px-4 py-3" scope="col">Scheme Name</th>
                    <th class="px-4 py-3" scope="col">Short Name</th>
                    <th class="px-4 py-3" scope="col">Department</th>
                    <th class="px-4 py-3" scope="col">Description</th>
                    <th class="px-4 py-3 text-center" scope="col">Status</th>
                    <th class="px-4 py-3 text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                @forelse ($schemes as $scheme)
                    <tr class="hover:bg-slate-50 transition" wire:key="scheme-row-{{ $scheme->id }}">
                        <td class="px-4 py-3 font-mono font-medium text-gray-500">
                            #{{ $scheme->id }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900">
                            {{ $scheme->name }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-mono text-[11px] font-semibold border border-gray-200">
                                {{ $scheme->short_name }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $scheme->Department?->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs truncate" title="{{ $scheme->description }}">
                            {{ $scheme->description ?: '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($scheme->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-700">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-red-500 rounded-full"></span> Disabled
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                            {{-- Edit Button --}}
                            <button class="inline-flex items-center px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-medium text-xs rounded-md shadow-xs transition cursor-pointer" title="Edit Scheme" type="button" wire:click="openEditModal({{ $scheme->id }})">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                Edit
                            </button>

                            {{-- Enable / Disable Status Toggle --}}
                            @if ($scheme->is_active)
                                <button class="inline-flex items-center px-2.5 py-1.5 bg-slate-600 hover:bg-slate-700 text-white font-medium text-xs rounded-md shadow-xs transition cursor-pointer" title="Disable Scheme" type="button" wire:click="toggleStatus({{ $scheme->id }})">
                                    <svg class="w-3.5 h-3.5 mr-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Disable
                                </button>
                            @else
                                <button class="inline-flex items-center px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs rounded-md shadow-xs transition cursor-pointer" title="Enable Scheme" type="button" wire:click="toggleStatus({{ $scheme->id }})">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Enable
                                </button>
                            @endif

                            {{-- Delete Button --}}
                            {{-- <button type="button"
                                    @click="openDeleteModal({{ $scheme->id }}, '{{ addslashes($scheme->name) }}')"
                                    class="inline-flex items-center px-2.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs rounded-md shadow-xs transition cursor-pointer"
                                    title="Delete Scheme">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                Delete
                            </button> --}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-6 py-8 text-center text-gray-400" colspan="7">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <span class="text-sm font-medium text-gray-500">No schemes found matching your criteria.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    @if ($schemes->hasPages())
        <div class="pt-2">
            {{ $schemes->links() }}
        </div>
    @endif

    {{-- Modal: Create Scheme --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" x-cloak x-transition>
            <div class="w-full max-w-lg bg-white rounded-xl shadow-xl p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-xl font-bold text-gray-800">Add New Scheme</h3>
                    <button class="text-gray-400 hover:text-gray-600 text-2xl leading-none" type="button" wire:click="closeCreateModal">&times;</button>
                </div>

                <form class="space-y-4" wire:submit.prevent="saveScheme">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheme Name <span class="text-red-600">*</span></label>
                        <input class="mt-1 w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('createName') border-red-500 @enderror" placeholder="Eg: TEXTILE PENSION" type="text" wire:model="createName">
                        @error('createName')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheme Short-Name <span class="text-red-600">*</span></label>
                        <input class="mt-1 w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('createShortName') border-red-500 @enderror" placeholder="Eg: textile" type="text" wire:model="createShortName">
                        @error('createShortName')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department <span class="text-red-600">*</span></label>
                        <select class="mt-1 w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('createDepartmentId') border-red-500 @enderror" wire:model="createDepartmentId">
                            <option value="">-- Select Department --</option>
                            @foreach ($departmentsList as $dId => $dName)
                                <option value="{{ $dId }}">{{ $dName }}</option>
                            @endforeach
                        </select>
                        @error('createDepartmentId')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheme Description</label>
                        <textarea class="mt-1 w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('createDescription') border-red-500 @enderror" placeholder="Optional scheme description..." rows="2" wire:model="createDescription"></textarea>
                        @error('createDescription')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800" type="button" wire:click="closeCreateModal">
                            Cancel
                        </button>
                        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-xs transition" type="submit">
                            Save Scheme
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal: Edit Scheme --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" x-cloak x-transition>
            <div class="w-full max-w-lg bg-white rounded-xl shadow-xl p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-xl font-bold text-gray-800">Edit Scheme #{{ $editingSchemeId }}</h3>
                    <button class="text-gray-400 hover:text-gray-600 text-2xl leading-none" type="button" wire:click="closeEditModal">&times;</button>
                </div>

                <form class="space-y-4" wire:submit.prevent="updateScheme">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheme Name <span class="text-red-600">*</span></label>
                        <input class="mt-1 w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('editName') border-red-500 @enderror" type="text" wire:model="editName">
                        @error('editName')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheme Short-Name <span class="text-red-600">*</span></label>
                        <input class="mt-1 w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('editShortName') border-red-500 @enderror" type="text" wire:model="editShortName">
                        @error('editShortName')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department <span class="text-red-600">*</span></label>
                        <select class="mt-1 w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('editDepartmentId') border-red-500 @enderror" wire:model="editDepartmentId">
                            <option value="">-- Select Department --</option>
                            @foreach ($departmentsList as $dId => $dName)
                                <option value="{{ $dId }}">{{ $dName }}</option>
                            @endforeach
                        </select>
                        @error('editDepartmentId')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheme Description</label>
                        <textarea class="mt-1 w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('editDescription') border-red-500 @enderror" rows="2" wire:model="editDescription"></textarea>
                        @error('editDescription')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status <span class="text-red-600">*</span></label>
                        <select class="mt-1 w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('editIsActive') border-red-500 @enderror" wire:model="editIsActive">
                            <option value="1">Active</option>
                            <option value="0">Disabled</option>
                        </select>
                        @error('editIsActive')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800" type="button" wire:click="closeEditModal">
                            Cancel
                        </button>
                        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-xs transition" type="submit">
                            Update Scheme
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal: Delete Confirmation --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" style="display: none;" x-cloak x-show="showDeleteModal" x-transition>
        <div @click.away="closeDeleteModal()" class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 space-y-4">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
            </div>
            <div class="text-center space-y-2">
                <h3 class="text-lg font-bold text-gray-900">Delete Scheme</h3>
                <p class="text-xs text-gray-500">
                    Are you sure you want to delete scheme <span class="font-semibold text-gray-800" x-text="deleteSchemeName"></span>?
                </p>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-2.5 text-[11px] text-amber-800 text-left">
                    <span class="font-bold">Protected Action:</span> If this scheme is associated with workflows, user mappings, or applications, deletion will be blocked automatically.
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button @click="closeDeleteModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition" type="button">
                    Cancel
                </button>
                <button @click="$wire.delete(deleteId); closeDeleteModal();" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition" type="button">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>
