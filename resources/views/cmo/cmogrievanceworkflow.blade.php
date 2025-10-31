<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{$header}}</h1>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <h2 class="text-xl font-bold mb-4">Enter Filter Criteria</h2>
        <x-form.select name="process_type" label="Process Type" required>
            <option value="1">Pending</option>
            <option value="2">Marked but Approval Pending</option>
            <option value="3">Marked and Approved but Yet not send to CMO</option>
            <option value="4">Sent to Operator for New Entry</option>
            <option value="5">Marked and Approved and Send to CMO</option>
        </x-form.select>
    </div>
</x-layouts.app>