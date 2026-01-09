
<div class="bg-white dark:bg-gray-800 shadow rounded-xl p-4">

    {{-- Header + Export Button (if passed from parent view) --}}
    <!-- @isset($header)
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
        {{ $header }}
    </h2>
    @endisset -->

    {{-- Helper Summary (Optional) --}}
    @if(!empty($helper['mode'] ?? null))
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-300">
        <strong> {{ $baseLabel }} {{ $header  }} {{ $helper['name'] }}</strong>
    </div>
    @endif

    {{-- No Data --}}
    @if(empty($rows))
    <p class="text-center text-gray-500 py-6">No data available.</p>
    @return
    @endif

    {{-- Dynamic Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

            {{-- Table Header --}}
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                        SL.No.
                    </th>

                    @foreach($columns as $col)
                    <th class="px-4 py-2 text-{{ $col['align'] }} text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $col['label'] }}
                    </th>
                    @endforeach
                </tr>
            </thead>

            {{-- Table Body --}}
           <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($rows as $index => $row)
                    @php $isOdd = $index % 2 === 1; @endphp
                    <tr class="{{ $isOdd ? 'bg-gray-50 dark:bg-gray-900' : 'bg-white dark:bg-gray-800' }} hover:bg-indigo-50 dark:hover:bg-indigo-900 transition-colors">
                        {{-- Serial --}}
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                            {{ $index + 1 }}
                        </td>

                        @foreach($columns as $col)
                            @php
                                $key = $col['key'];
                                $value = $row[$key] ?? '';
                                // Color map for badges
                                $badgeMap = [
                                  'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                  'verified' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                                  'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                  'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                  'reverted' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                  'total'=> 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
                                  
                                ];
                                $badgeCls = $badgeMap[$key] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                            @endphp

                            <td class="px-4 py-3 text-sm text-{{ $col['align'] }} text-gray-800 dark:text-gray-100">
                                @if($col['type'] === 'number' && in_array($key, ['pending','verified','approved','rejected','reverted','total']))
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-semibold {{ $badgeCls }}">
                                        {{ $value }}
                                    </span>
                              
                                @else
                                    <span class="block truncate">{{ $value }}</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach

                {{-- Totals row --}}
                <tr class="bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-900 dark:to-gray-800 font-semibold">
                    <td class="px-4 py-3 text-sm">Total</td>

                    @foreach($columns as $col)
                        <td class="px-4 py-3 text-sm text-{{ $col['align'] }} text-gray-800 dark:text-gray-100">
                            @if(isset($totals[$col['key']]))
                                @if(in_array($col['key'], ['pending','verified','approved','rejected','reverted','total']))
                                    {{-- totals with badge background --}}
                                    @php
                                        $k = $col['key'];
                                        $badgeCls = $badgeMap[$k] ?? 'bg-gray-200 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $badgeCls }} font-semibold">
                                        {{ $totals[$col['key']] }}
                                    </span>
                                @else
                                    <span class="font-semibold">{{ $totals[$col['key']] }}</span>
                                @endif
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

</div>