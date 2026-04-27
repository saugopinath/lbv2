<div>
    <div class="mb-6 pb-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-start gap-4 sm:gap-8 mt-2 md:mt-4 ml-0 md:ml-2">
        <label for="fin_year" class="text-[14px] font-bold text-gray-700 flex items-center gap-2">
            <i class="fa-solid fa-calendar-days text-indigo-500"></i> Which financial year you want to view payment status?
        </label>
        <select wire:model.live="fin_year" id="fin_year"
            class="border border-gray-300 rounded-lg px-4 py-2 text-[14px] bg-white text-gray-800 shadow-sm w-full sm:w-56 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-all cursor-pointer">
            @foreach(config('constants.fin_year') as $year => $value)
                <option value="{{ $year }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>

    <div class="w-full border border-gray-100 rounded-sm">
        <livewire:frontend.track-ben.payment-status-table :ben_id="$ben_id" :scheme_id="$scheme_id"
            :fin_year="$fin_year" :key="$fin_year" />
    </div>
</div>