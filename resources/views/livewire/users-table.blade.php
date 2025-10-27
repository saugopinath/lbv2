<div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4" x-data="{
        confirmDelete(id) {
            if (confirm('Do you want to delete this user?')) {
                $wire.deleteUser(id)
            }
        }
    }">

    <!-- Top Controls: Search, Per Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex justify-end">
            <select wire:model.live="perPage"
                class="w-48 px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm">
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>

        <div class="flex justify-end w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <input type="text" wire:model.live="search" placeholder="Search..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm">
                <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto border rounded-lg shadow-sm">
        <table class="min-w-full text-sm text-gray-700 text-center">
            <thead class="bg-violet-800 text-xs uppercase py-3 text-white">
                <tr>
                    <th class="py-3">ID</th>
                    <th class="py-3">Name</th>
                    <th class="py-3">Mobile No</th>
                    <th class="py-3">Email</th>
                    <th class="py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white overflow-y-auto">
                @forelse($rows as $row)
                    <tr>
                        <td class="py-3">{{ $row->id ?? 'N/A' }}</td>
                        <td class="py-3">{{ $row->name ?? 'N/A' }}</td>
                        <td class="py-3">{{ $row->mobile_no ?? 'N/A' }}</td>
                        <td class="py-3">{{ $row->email ?? 'N/A' }}</td>
                        <td class="py-3">
                            <x-button.warning @click="confirmDelete({{ $row->id }})"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                Delete
                            </x-button.warning>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-3 text-gray-500">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col md:flex-row items-center justify-between mt-4 space-y-2 md:space-y-0">
        <div class="text-sm text-gray-600">
            Showing {{ $rows->firstItem() ?? 0 }} to {{ $rows->lastItem() ?? 0 }} of {{ $rows->total() }} entries
        </div>
        <div>{{ $rows->links('vendor.livewire.simple') }}</div>
    </div>
</div>
