<x-layouts.app>
      <div class="flex-1 p-2 col-end-5 overflow-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
            <form method="GET" action="{{ route('report.show') }}">
                <x-form.select name="report_type" label="Report Type" required>
                    <option value="">-- Select Report Type --</option>
                    <option value="1">Partial Entry List</option>
                    <option value="2">Verified List</option>
                    <option value="3">Approved List</option>
                    <option value="4">Rejected List</option>
                    <option value="5">Reverted List</option>
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
