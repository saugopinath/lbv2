<div>
    <div class="mb-6 pb-6 border-b border-gray-100 flex items-center justify-start gap-12 mt-4 ml-2">
        <label for="fin_year" class="text-[14px] font-bold text-gray-700">Which financial year you want to view payment status ?</label>
        <select wire:model.live="fin_year" id="fin_year" class="border border-gray-300 rounded px-2 py-1 text-sm bg-white shadow-sm w-44 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
            @foreach($available_years as $year)
                <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-6 text-[13px] tracking-wide font-bold space-y-1.5 ml-2">
        @if($ben_status == 'Active')
            <p class="text-green-800">Bank Account Status : Validation Success. Ready For Payment</p>
            <p class="text-green-800">Beneficiary Status : Active beneficiary</p>
        @else
            <p class="text-green-800">Bank Account Status : Inactive or Validation Pending</p>
            <p class="text-green-800">Beneficiary Status : {{ $ben_status ?? 'Inactive' }}</p>
        @endif
        <p class="text-[#00529B]">Bank A/c No : {{ $bank_code }}, IFSC : {{ $ifsc }}</p>
    </div>

    <div class="w-full border border-gray-100 rounded-sm">
        <livewire:frontend.track-ben.payment-status-table 
            :ben_id="$ben_id" 
            :scheme_id="$scheme_id" 
            :fin_year="$fin_year" 
            :key="$fin_year" 
        />
    </div>
</div>
