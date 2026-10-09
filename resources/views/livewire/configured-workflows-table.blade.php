<div class="bg-white shadow-xl rounded-2xl p-6 space-y-6" 
     x-data="{ 
         showDeleteModal: false, 
         deleteId: null, 
         deleteSchemeName: '', 
         deleteModuleName: '',
         openDeleteModal(id, schemeName, moduleName) {
             this.deleteId = id;
             this.deleteSchemeName = schemeName;
             this.deleteModuleName = moduleName;
             this.showDeleteModal = true;
         },
         closeDeleteModal() {
             this.showDeleteModal = false;
             this.deleteId = null;
         }
     }">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-indigo-700">Configured Workflows</h1>
            <p class="text-sm text-gray-500">Manage, edit, or delete existing configured scheme workflows.</p>
        </div>
        <a class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow transition flex items-center gap-2" href="{{ route('define-workflow1') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
            Create New Workflow
        </a>
    </div>

    {{-- Filter Bar: Search, Scheme, Module, Status, Reset Filters & Per Page --}}
    <div class="bg-gray-50/80 border border-gray-200 rounded-xl p-4 space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200/80 pb-3">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">Filters & Search</span>
                @if (!empty($search) || !empty($selectedSchemes) || !empty($selectedModules) || !empty($statusFilter))
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

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 items-start">
            <!-- Search Input -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Search Keyword</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <input class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs bg-white shadow-xs" placeholder="Search Scheme Name, ID, Module..." type="text" wire:model.live.debounce.300ms="search">
                </div>
            </div>

            <!-- Scheme Multi-Select Filter -->
            <div>
                <x-form.multiselect :options="$schemesList" labelClass="block text-xs font-medium text-gray-500 uppercase mb-1" label="Scheme" placeholder="All Schemes" wire:model.live="selectedSchemes" />
            </div>

            <!-- Module Multi-Select Filter -->
            <div>
                <x-form.multiselect :options="$modulesList" labelClass="block text-xs font-medium text-gray-500 uppercase mb-1" label="Module" placeholder="All Modules" wire:model.live="selectedModules" />
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
                Showing <span class="font-semibold text-gray-800">{{ $configuredWorkflows->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-800">{{ $configuredWorkflows->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-800">{{ $configuredWorkflows->total() }}</span> entries
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

    {{-- Configured Workflows Table --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider" scope="col">#</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider" scope="col">Scheme ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider" scope="col">Scheme Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider" scope="col">Module Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider" scope="col">Module Code</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider" scope="col">Workflow Type</th>
                    {{-- <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider" scope="col">Applications</th> --}}
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider" scope="col">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @forelse ($configuredWorkflows as $index => $item)
                    <tr class="hover:bg-indigo-50/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">
                            {{ $configuredWorkflows->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono font-semibold text-indigo-600">
                            {{ $item->scheme_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                            {{ $item->scheme->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                            {{ $item->module->module_name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-600 bg-gray-50 rounded px-2 py-0.5 inline-block my-3">
                            {{ $item->main_module_code ?? ($item->module->module_code ?? 'N/A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $type = strtolower($item->module->workflow_type ?? 'normal');
                                $badgeClass = match($type) {
                                    'rural' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'urban' => 'bg-sky-50 text-sky-700 border-sky-200',
                                    default => 'bg-gray-50 text-gray-700 border-gray-200',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                {{ ucfirst($type) }}
                            </span>
                        </td>
                        {{-- <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if (($item->applications_count ?? 0) > 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 gap-1.5 shadow-2xs" title="{{ $item->applications_count }} application(s) present in queue">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    {{ $item->applications_count }} in Queue
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-slate-400 bg-slate-50 border border-slate-200">
                                    0 Entries
                                </span>
                            @endif
                        </td> --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if ($item->is_disabled)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Disabled
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center space-x-2">
                            @if ($item->is_disabled)
                                <button class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs rounded-md shadow transition" wire:click="toggleDisable({{ $item->id }})">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Enable
                                </button>
                            @else
                                <button class="inline-flex items-center px-3 py-1.5 bg-slate-600 hover:bg-slate-700 text-white font-medium text-xs rounded-md shadow transition" wire:click="toggleDisable({{ $item->id }})">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Disable
                                </button>
                            @endif

                            <a class="inline-flex items-center px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-medium text-xs rounded-md shadow transition" href="{{ route('define-workflow1', ['scheme_id' => \Illuminate\Support\Facades\Crypt::encryptString($item->scheme_id), 'module_id' => \Illuminate\Support\Facades\Crypt::encryptString($item->module_id)]) }}">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                Edit
                            </a>

                            <button class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-medium text-xs rounded-md shadow transition cursor-pointer" type="button" @click.stop="openDeleteModal({{ $item->id }}, '{{ addslashes($item->scheme->name ?? ('Scheme #' . $item->scheme_id)) }}', '{{ addslashes($item->module->module_name ?? ($item->main_module_code ?? 'N/A')) }}')">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-6 py-8 text-center text-gray-500 italic" colspan="8">
                            No configured workflows found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="pt-4 border-t border-gray-200">
        {{ $configuredWorkflows->links() }}
    </div>

    {{-- Delete Confirmation Premium Tailwind CSS Modal --}}
    <div aria-labelledby="modal-title" 
         aria-modal="true" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog"
         x-cloak 
         x-show="showDeleteModal"
         @keydown.escape.window="closeDeleteModal">
        
        <!-- Backdrop with Blur -->
        <div @click="closeDeleteModal" 
             aria-hidden="true" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
             x-show="showDeleteModal" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"></div>

        <!-- Center Modal Content -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div @click.stop
                 class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100" 
                 x-show="showDeleteModal" 
                 x-transition:enter="transition ease-out duration-200" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="transition ease-in duration-150" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Modal Header with Soft Danger Accent -->
                <div class="bg-gradient-to-r from-red-50 via-rose-50/50 to-transparent p-6 border-b border-rose-100/80 flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-500 to-rose-600 text-white flex items-center justify-center shadow-md shadow-red-500/20 ring-4 ring-red-100 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 leading-tight" id="modal-title">
                                Delete Configured Workflow
                            </h3>
                            <p class="text-xs text-rose-600 font-medium mt-0.5">
                                Permanent action &bull; Configuration will be purged
                            </p>
                        </div>
                    </div>

                    <!-- Close X Button -->
                    <button @click="closeDeleteModal" class="text-slate-400 hover:text-slate-600 hover:bg-white/80 rounded-xl p-1.5 transition-colors cursor-pointer" title="Close modal" type="button">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-4">
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Are you sure you want to permanently delete this workflow configuration? The scheme will no longer be linked to this module.
                    </p>

                    <!-- Scheme & Module Details Card -->
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 space-y-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                Scheme Name
                            </span>
                            <span class="font-bold text-slate-800 bg-white px-2.5 py-1 rounded-lg border border-slate-200/60 shadow-2xs" x-text="deleteSchemeName"></span>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-1.5 border-t border-slate-200/60">
                            <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                Target Module
                            </span>
                            <span class="font-bold text-indigo-700 font-mono bg-indigo-50/80 px-2.5 py-1 rounded-lg border border-indigo-100 shadow-2xs" x-text="deleteModuleName"></span>
                        </div>
                    </div>

                    <!-- Warning Callout Banner -->
                    <div class="bg-amber-50/90 border-l-4 border-amber-500 rounded-xl p-3.5 flex items-start gap-3 text-amber-900">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        <div class="text-xs space-y-1">
                            <p class="font-bold text-amber-900">Consequences of Deletion</p>
                            <p class="text-amber-800 leading-normal">
                                All configured workflow steps, step-role mappings, duplicate check settings, age management rules, and auto-generated custom roles will be permanently deleted.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Modal Actions Footer -->
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
                    <button @click="closeDeleteModal" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:border-slate-400 transition shadow-xs cursor-pointer" type="button">
                        Cancel
                    </button>
                    <button @click="$wire.delete(deleteId); closeDeleteModal();" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-xl text-xs font-semibold transition shadow-md shadow-red-500/20 hover:shadow-red-500/30 gap-2 cursor-pointer active:scale-95" type="button">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        <span>Yes, Delete Workflow</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
