<x-layouts.app>

    <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl md:rounded-2xl p-6 md:p-8">

            {{-- @can('view reports')  --}}
            @if(\App\Helpers\WorkFlowPermissionHelper::canViewReport())
            <form method="GET" action="{{ route('report.show') }}" class="space-y-6 md:space-y-8">
                <div class="space-y-6">
                    <!-- Scheme Selection -->
                    <div class="space-y-2">

                        <x-form.select name="scheme_id" label="Scheme" required>
                            <option value="">-- Select Scheme --</option>
                            @foreach ($scheme as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.select name="report_type" label="Report Type" required>
                            <option value="">-- Select Report Type --</option>
                            <option value="1">Partial Entry List</option>
                            <option value="2">Verified List</option>
                            <option value="3">Approved List</option>
                            <option value="4">Rejected List</option>
                            <option value="5">Reverted List</option>
                            <option value="6">Submitted List</option>
                        </x-form.select>

                        <div class="flex justify-end mt-4">
                            <div class="flex justify-end">
                                <x-button.primary type="submit"
                                    class="bg-blue-500 text-white whitespace-nowrap cursor-pointer"
                                    x-data
                                    x-on:click.prevent="
                                    Livewire.dispatch('showLoader');
                                    $el.form.submit();
                                ">
                                    GO
                                </x-button.primary>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            @else
            <div class="text-center py-10">
                <h2 class="text-xl font-semibold text-red-600 mb-2">
                    Oops! You don’t have permission to view reports.
                </h2>
                <p class="text-gray-600">
                    Please contact your administrator to get access to report viewing.
                </p>
            </div>
            {{-- @endcan  --}}
            @endif

        </div>
    </div>
</x-layouts.app>