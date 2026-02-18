<x-layouts.app>
    <div class="flex-1 p-2 col-end-5 overflow-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">

            {{-- @can('view reports') --}}
            {{-- @if (\App\Helpers\WorkFlowPermissionHelper::canViewReport()) --}}
            <form method="GET" action="{{ route('report.show') }}">
                <x-form.select name="scheme" label="Scheme" required>
                    <option value="">-- Select Scheme --</option>
                    @foreach ($schemes as $scheme)
                        <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                    @endforeach
                </x-form.select>
                {{-- <x-form.select name="report_type" label="Report Type" required>
                        <option value="">-- Select Report Type --</option>
                        <option value="1">Partial Entry List</option>
                        <option value="2">Verified List</option>
                        <option value="3">Approved List</option>
                        <option value="4">Rejected List</option>
                        <option value="5">Reverted List</option>
                        <option value="6">Submitted List</option>
                    </x-form.select> --}}

                <x-form.select name="report_type" label="Report Type" required>
                    <option value="">-- Select Report Type --</option>
                    @foreach ($reporttypes as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                    {{-- @if (\App\Helpers\CheckAuthHelper::isCommmonVerifier()) --}}
                    {{-- Common Verifier --}}
                    {{-- <option value="2">Verified List</option>
                    <option value="4">Rejected List</option>
                    <option value="5">Reverted List</option>
                    <option value="3">Approved List</option> --}}

                    {{-- @elseif (\App\Helpers\CheckAuthHelper::isCommonApprover()) --}}
                    {{-- Common Approver --}}
                    {{-- <option value="3">Approved List</option>
                    <option value="4">Rejected List</option>
                    <option value="5">Reverted List</option> --}}

                    {{-- @else --}}
                    {{-- Default: Super Admin / Others --}}
                    {{-- <option value="1">Partial Entry List</option>
                    <option value="2">Verified List</option>
                    <option value="3">Approved List</option>
                    <option value="4">Rejected List</option>
                    <option value="5">Reverted List</option>
                    <option value="6">Submitted List</option> --}}
                    {{-- @endif --}}
                </x-form.select>


                <div class="flex justify-end mt-4">
                    <div class="flex justify-end">
                        <x-button.primary type="submit" class="bg-blue-500 text-white whitespace-nowrap cursor-pointer"
                            x-data
                            x-on:click.prevent="
                                        Livewire.dispatch('showLoader');
                                        $el.form.submit();
                                    ">
                            GO
                        </x-button.primary>
                    </div>
                </div>
            </form>
            {{-- @else --}}
            {{-- <div class="text-center py-10">
                    <h2 class="text-xl font-semibold text-red-600 mb-2">
                        Oops! You don’t have permission to view reports.
                    </h2>
                    <p class="text-gray-600">
                        Please contact your administrator to get access to report viewing.
                    </p>
                </div> --}}
            {{-- @endcan --}}
            {{-- @endif --}}

        </div>
    </div>
</x-layouts.app>
