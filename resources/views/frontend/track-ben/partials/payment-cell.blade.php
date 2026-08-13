@php
    $lot_status = $row->{$prefix . '_lot_status'} ?? null;
    $validStatuses = ['G', 'P', 'S', 'F', 'H', 'M'];
    $monthlower = strtolower($month_label);
    $ifsc = '';
    $accno = '';

    if (in_array($lot_status, $validStatuses)) {
        $paymentLogArr = json_decode($row->payment_log, true) ?? [];
        $monthData = [];
        foreach ($paymentLogArr as $entry) {
            if (isset($entry[$monthlower])) {
                $monthData = $entry[$monthlower];
                break;
            }
        }
        
        $ifsc = isset($monthData[0]['ifsc']) ? $monthData[0]['ifsc'] : '';
        $rawAcc = isset($monthData[0]['accno']) ? $monthData[0]['accno'] : '';
        if ($rawAcc !== '') {
            $accno = str_repeat('•', max(0, strlen($rawAcc) - 4)) . substr($rawAcc, -4);
        }
    }
    
    // Determine the status label based on the existing logic
    $statusLabel = '-';
    $statusColor = 'bg-gray-100 text-gray-500 border-gray-200';
    $icon = '';
    
    if (in_array($lot_status, $validStatuses)) {
        if (in_array($lot_status, ['S', 'F', 'M'])) {
            $statusLabel = \Illuminate\Support\Facades\Config::get('constants.lot_status.' . $lot_status) ?? ($lot_status == 'S' ? 'Payment Success' : 'Failed');
            if ($lot_status == 'S') {
                $statusColor = 'bg-green-50 bg-opacity-80 text-green-700 border-green-200 shadow-sm';
                $icon = '<i class="fa-solid fa-circle-check mr-1 text-green-500"></i>';
            } else {
                $statusColor = 'bg-red-50 bg-opacity-80 text-red-700 border-red-200 shadow-sm';
                $icon = '<i class="fa-solid fa-circle-xmark mr-1 text-red-500"></i>';
            }
        } else {
            $statusLabel = 'Payment Under Process';
            $statusColor = 'bg-amber-50 bg-opacity-80 text-amber-700 border-amber-200 shadow-sm';
            $icon = '<i class="fa-solid fa-clock-rotate-left mr-1 text-amber-500"></i>';
        }
    }
@endphp

@if(in_array($lot_status, $validStatuses))
<div class="flex flex-col items-center justify-center space-y-2 p-1">
    <div class="inline-flex items-center justify-center px-2 py-1 rounded-md border text-[11px] font-medium tracking-wide {{ $statusColor }}">
        {!! $icon !!} {{ $statusLabel }}
    </div>
    
    @if($accno)
        <div class="flex flex-col items-center text-[10px] text-gray-500 bg-white border border-gray-100 rounded px-2 py-0.5 w-full shadow-sm">
            <span class="font-mono">{{ $ifsc }}</span>
            <span class="font-mono text-gray-700">{{ $accno }}</span>
        </div>
    @endif
    
    @if(in_array($lot_status, ['F', 'M']))
        @php
            $lotNoAttr = $prefix . '_lot_no';
            $lotNoVal = $row->{$lotNoAttr};
        @endphp
        <button type="button" 
            onclick="Livewire.dispatch('show-payment-error', { lot_no: '{{ $lotNoVal }}', ben_id: '{{ $row->ben_id }}', fin_year: '{{ $row->fin_year }}', scheme_id: '{{ $row->scheme_id }}' })"
            class="text-[10px] bg-red-100 hover:bg-red-200 text-red-700 py-0.5 px-2 rounded font-medium transition-colors border border-red-200 flex items-center gap-1 w-full justify-center">
            <i class="fa-solid fa-circle-info"></i> View Error
        </button>
    @endif
</div>
@else
<div class="text-gray-300 text-xs italic text-center">—</div>
@endif
