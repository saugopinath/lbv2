<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{$header}}</h1>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <h2 class="text-xl font-bold mb-4">Enter Filter Criteria</h2>
        <form action="{{ route('pullnewcmo') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <x-form.input type="date" name="from_date"
                        label="From Date"
                        placeholder="Enter Duare Sakar Registration Number"
                        required />
                </div>
                <div>
                    <x-form.input type="date" name="to_date"
                        label="To Date"
                        placeholder="Enter Duare Sakar Registration Number"
                        required />
                </div>
            </div>
            <x-button.primary type="submit">
                Import Data
            </x-button.primary>
        </form>
        @if ($inserted_id)
        <x-button.primary>
            Populate into LB Portal
        </x-button.primary>
        @endif
    </div>
</x-layouts.app>