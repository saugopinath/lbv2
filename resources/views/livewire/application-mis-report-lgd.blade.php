<div>
  <div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-semibold">MIS Report</h2>
    <div class="flex items-center gap-2">
      <label class="text-sm text-gray-600">Per page</label>
      <select wire:model="perPage" class="border rounded px-2 py-1">
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="50">50</option>
      </select>
    </div>
  </div>

  {{-- render your common table component --}}
  @component('components.common-table', [
      'rows' => $rows,
      'columns' => $columns,
      'page' => $page,
      'perPage' => $perPage,
      'totalRows' => $totalRows,
      'totals' => $totals
  ])
  @endcomponent

  <div class="mt-3 flex items-center justify-between">
    <div class="text-sm text-gray-600">Page {{ $page }}</div>
    <div class="space-x-2">
      <button wire:click="$set('page', max(1, $page - 1))" class="px-3 py-1 bg-gray-100 rounded">Prev</button>
      <button wire:click="$set('page', $page + 1)" class="px-3 py-1 bg-gray-100 rounded">Next</button>
    </div>
  </div>
</div>

