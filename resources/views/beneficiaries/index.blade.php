<x-layouts.app>
    <div class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
            <form method="GET" action="{{ route('report.show') }}">
                <x-form.select name="report_type" label="Report Type" required>
                    <option value="">-- Select Report Type --</option>
                    <option value="partial">Partial Entry List</option>
                    <option value="verified">Verified List</option>
                    <option value="approved">Approved List</option>
                    <option value="rejected">Rejected List</option>
                    <option value="reverted">Reverted List</option>
                </x-form.select>
                <div class="flex justify-end mt-4">
                    <x-button.primary type="submit" class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                        GO
                    </x-button.primary>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
