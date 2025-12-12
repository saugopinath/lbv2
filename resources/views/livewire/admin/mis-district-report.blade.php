<div class="p-4">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold">MIS — District-wise Beneficiary Report</h2>

    <div class="flex gap-2 items-center">
      <!-- <input wire:model.debounce.300ms="search" type="text" placeholder="Search district id..."
             class="px-3 py-2 border rounded text-sm" /> -->

      <!-- <select wire:model="perPage" class="px-3 py-2 border rounded text-sm">
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="50">50</option>
      </select> -->

      <button wire:click="exportCsv" class="px-3 py-2 bg-green-600 text-white rounded text-sm">Export CSV</button>
    </div>
  </div>

  @php
    $columns = [
      ['label' => 'District', 'field' => 'district_display', 'sortable' => false],
      ['label' => 'Total', 'field' => 'total', 'sortable' => false],
      ['label' => 'Approved', 'field' => 'approved', 'sortable' => false],
      ['label' => 'Verified', 'field' => 'verified', 'sortable' => false],
      ['label' => 'Reject', 'field' => 'Reject', 'sortable' => false],
    ];
  @endphp

  <x-common-table
    :rows="$rows"
    :columns="$columns"
    :total-rows="$totalRows"
    :totals="[
      'total' => $totals['total'] ?? 0,
      'approved' => $totals['approved'] ?? 0,
      'verified' => $totals['verified'] ?? 0
      '
    ]"
  >
    {{-- pagination slot --}}
    <!-- <x-slot name="pagination">
      <button wire:click="$set('page', max(1, $page - 1))" class="px-3 py-1 border rounded">Prev</button>
      <button wire:click="$set('page', $page + 1)" class="px-3 py-1 border rounded">Next</button>
    </x-slot> -->
  </x-common-table>
</div> 