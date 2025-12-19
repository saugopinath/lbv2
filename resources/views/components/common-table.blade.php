{{-- components/common-table.blade.php --}}
@props([
'rows', 'columns' => [], 'page' => 1, 'perPage' => 20, 'totalRows' => null, 'totals' => null,
'sortField' => null, 'sortDirection' => null
])

<div class="space-y-4">
  <div class="flex items-center justify-between">
    <div>
      {{ $header ?? '' }}
    </div>
  </div>

  <div class="bg-white shadow rounded-md overflow-x-auto">
    <table class="min-w-full divide-y">
      <thead class="bg-gray-50">
        <tr class="text-xs text-gray-600 uppercase">
          <th class="px-4 py-3 text-left">#</th>

          {{-- render columns --}}
          @foreach($columns as $col)
          @php
          $label = $col['label'] ?? $col['field'] ?? '';
          $field = $col['field'] ?? null;
          $sortable = $col['sortable'] ?? false;
          @endphp

          <th class="px-4 py-3 text-left @if($sortable) cursor-pointer select-none @endif"
            @if($sortable)
            wire:click="$dispatch('commonTableSort', '{{ $field }}')"
            @endif>
            <div class="flex items-center gap-2">
              <span>{{ $label }}</span>
              @if($sortable && $sortField === $field)
              <small class="text-xs text-gray-500">({{ $sortDirection }})</small>
              @endif
            </div>
          </th>
          @endforeach
        </tr>
      </thead>

      <tbody class="divide-y text-sm bg-white">
        @forelse($rows as $idx => $row)
        <tr>
          <td class="px-4 py-3">
            {{ ($page - 1) * $perPage + $idx + 1 }}
          </td>

          @foreach($columns as $col)
          @php $field = $col['field'] ?? null; @endphp
          <td class="px-4 py-3">
            {{ data_get($row, $field) }}
          </td>
          @endforeach
        </tr>
        @empty
        <tr>
          <td colspan="{{ count($columns) + 1 }}" class="px-4 py-6 text-center text-gray-500">
            No records found.
          </td>
        </tr>
        @endforelse
      </tbody>

      @if($totals)
      <tfoot class="bg-gray-50 text-sm font-semibold">
        <tr>
          {{-- serial col empty --}}
          <td class="px-4 py-3"></td>

          {{-- district column shows TOTAL label (assumes first column is district-ish) --}}
          @php
          $firstField = $columns[0]['field'] ?? null;
          @endphp
          <td class="px-4 py-3">TOTAL</td>

          {{-- render totals for remaining columns in same order --}}
          @foreach($columns as $col)
          @php $field = $col['field'] ?? null; @endphp

          {{-- skip the first column because we've used it for the TOTAL label --}}
          @if($loop->first)
          @continue
          @endif

          <td class="px-4 py-3">
            {{ $totals[$field] ?? '' }}
          </td>
          @endforeach
        </tr>
      </tfoot>
      @endif
    </table>
  </div>

  <div class="flex items-center justify-between text-sm text-gray-600">

    <div class="text-sm text-gray-600">
      @if($totalRows !== null)
      Showing {{ is_countable($rows) ? count($rows) : $rows->count() }} of {{ $totalRows }}
      @endif
    </div>
  </div>
</div>