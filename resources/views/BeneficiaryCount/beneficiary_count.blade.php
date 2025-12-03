<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{$header}}</h1>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">{{$header}}</h2>
            <div class="flex gap-2 items-center">
                <button wire:click="exportCsv" class="px-3 py-2 bg-green-600 text-white rounded text-sm">Export CSV</button>
            </div>
        </div>
        <x-common-table
            :rows="$rows"
            :columns="$columns"
            :total-rows="$totalRows"
            :totals="[
      'total' => $totals['total'] ?? null,
      'approved' => $totals['approved'] ?? null,
      'verified' => $totals['verified'] ?? null,
      'reverted'=> $totals['reverted'] ?? null,
      'rejected' => $totals['rejected'] ?? null
    ]">
            {{-- pagination slot --}}
            <!-- <x-slot name="pagination">
      <button wire:click="$set('page', max(1, $page - 1))" class="px-3 py-1 border rounded">Prev</button>
      <button wire:click="$set('page', $page + 1)" class="px-3 py-1 border rounded">Next</button>
    </x-slot> -->
        </x-common-table>
    </div>
</x-layouts.app>