<div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
    @if (session('success'))
        <div class="bg-green-500 text-white px-4 py-2 rounded shadow mb-2">
            {{ session('success') }}
        </div>
    @endif

    <div>
        @if ($this->activeFilters)
            <div x-data="{ show: true }" x-show="show" x-transition
                class="mb-6 p-4 border rounded-lg bg-gray-100 shadow-sm">
                <div class="flex flex-wrap gap-4 text-sm">
                    <p class="text-gray-700">{{ $this->activeFilters }}</p>
                </div>
            </div>
        @endif
    </div>


    <!-- Top Controls: Search + Per Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex justify-end">
            <select wire:model="perPage" class="w-48 px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm">
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>

        <div class="flex justify-end w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <input type="text" wire:model="search" placeholder="Search..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm">
                <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto border rounded-lg shadow-sm mt-3">
        <table class="min-w-full text-sm text-gray-700 text-center">
            <thead class="bg-violet-800 text-xs uppercase text-white">
                <tr>
                    <th class="py-3 px-2">Application ID</th>
                    <th class="py-3 px-2">Name</th>
                    <th class="py-3 px-2">Father's Name</th>
                    <th class="py-3 px-2">Incomplete Type</th>
                    <th class="py-3 px-2">Address</th>
                    @if ($stage === 'revert')
                        <th class="py-3 px-2">Revert Reason</th>
                        <th class="py-3 px-2">Revert Remarks</th>
                    @endif
                    <th class="py-3 px-2">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($rows as $row)
                    <tr>
                        <!-- Application ID -->
                        <td class="py-3 px-2">{{ $row->application_id ?? 'N/A' }}</td>

                        <!-- Applicant Name -->
                        <td class="py-3 px-2">
                            {{ $row['beneficiaryCommonList']['beneficiaryPersonal']->full_name ?? 'N/A' }}
                        </td>

                        <!-- Father's Name -->
                        <td class="py-3 px-2">
                            {{ $row['beneficiaryCommonList']['beneficiaryPersonal']->father?->first()?->full_name ?? 'N/A' }}
                        </td>

                        <!-- Incomplete Type -->
                        <td class="py-3 px-2">{!! $row->incomplete_types_names ?? 'N/A' !!}</td>

                        <!-- Address -->
                        <td class="py-3 px-2">
                            @php $common = $row->beneficiaryCommonList; @endphp
                            @if ($common?->block_id && $common?->panchayat)
                                {{ $common->panchayat->name }}
                            @elseif ($common?->sub_division_id && $common?->ward)
                                {{ $common->ward->name }}
                            @else
                                N/A
                            @endif
                        </td>

                        @if ($stage === 'revert')
                            <!-- Revert Reason -->
                            <td class="py-3 px-2">
                                {{ $row['acceptRejectInfo']['revertReason']->name ?? 'N/A' }}
                            </td>

                            <!-- Revert Remarks -->
                            <td class="py-3 px-2">
                                {{ $row['acceptRejectInfo']->revert_reason_remarks ?? 'N/A' }}
                            </td>
                        @endif
                        <!-- Action -->
                        <td class="py-3 px-2">
                            @if ($stage === 'approver')
                                {{-- Approve Button --}}
                                <x-button.primary
                                    href="{{ route('incomplet-type.view', ['id' => $row->application_id, 'stage' => $stage]) }}">
                                    Approve
                                </x-button.primary>
                            @elseif ($stage === 'revert')
                                {{-- Revert Button --}}
                                <x-button.primary
                                    href="{{ route('incomplet-type.view', ['id' => $row->application_id, 'stage' => $stage]) }}">
                                    Update Revert
                                </x-button.primary>
                            @else
                                {{-- Verifier Update Button --}}
                                <x-button.primary
                                    href="{{ route('incomplet-type.view', ['id' => $row->application_id, 'stage' => $stage]) }}">
                                    Update
                                </x-button.primary>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-3 text-gray-500">No records found.</td>
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
        <div>
            {{ $rows->links('vendor.livewire.simple') }}
        </div>
    </div>
</div>
