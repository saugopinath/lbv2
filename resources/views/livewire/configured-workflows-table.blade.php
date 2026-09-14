<div class="bg-white shadow-xl rounded-2xl p-6 space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-indigo-700">Configured Workflows</h1>
            <p class="text-sm text-gray-500">Manage, edit, or delete existing configured scheme workflows.</p>
        </div>
        <a href="{{ route('define-workflow1') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New Workflow
        </a>
    </div>

    {{-- Top Controls: Search Bar & Per Page Dropdown --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="w-full md:w-80 relative">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Search Scheme Name, ID, Module..." 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <div class="flex items-center space-x-2 text-sm text-gray-600 self-end md:self-auto">
            <span>Show</span>
            <select wire:model.live="perPage" class="border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
            <span>entries</span>
        </div>
    </div>

    {{-- Configured Workflows Table --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Scheme ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Scheme Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Module Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Module Code</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
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
                            {{ $item->main_module_code ?? $item->module->module_code ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center space-x-2">
                            <a href="{{ route('define-workflow1', ['scheme_id' => \Illuminate\Support\Facades\Crypt::encryptString($item->scheme_id), 'module_id' => \Illuminate\Support\Facades\Crypt::encryptString($item->module_id)]) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-medium text-xs rounded-md shadow transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>

                            <button onclick="confirm('Are you sure you want to delete this configured workflow?') || event.stopImmediatePropagation()"
                                    wire:click="delete({{ $item->id }})" 
                                    class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-medium text-xs rounded-md shadow transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">
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
</div>
