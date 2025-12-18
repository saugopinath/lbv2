<div class="p-4">
    @php
    use Illuminate\Support\Facades\Crypt;
    @endphp
    <h2 class="text-xl font-bold mb-4">Search Beneficiary</h2>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <x-form.select
                name="searchType" id="searchType" label="Search Applicant By" wire:model.live="searchType" placeholder="--Select Search Type--" required>
                <option value="">--Select Search Type--</option>
                @foreach($searchOptions as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </x-form.select>
        </div>

        <div>
            <x-form.input name="searchValue" id="searchValue" wire:model.defer="searchValue"
                label="{{ $this->currentLabel }}" placeholder="Enter {{ $this->currentLabel }}" required
                type="text"
                oninput="this.value = this.value.replace(/[^0-9]/g);"
                maxlength="{{ ($searchType == '3') ? 12 : (($searchType == '4') ? 10 : '') }}" />
        </div>
    </div>
    <x-button.loading-button action="search" text="Search"></x-button.loading-button>
    <x-alart />
    @if(count($items) > 0)
    <div class="mt-6">
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <h2 class="text-xl font-bold mb-2 pl-2">Search Beneficiary</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-500 text-white">
                    <tr>
                        <th class="px-4 py-2 text-center text-sm font-medium uppercase">Application ID</th>
                        <th class="px-4 py-2 text-center text-sm font-medium uppercase">Beneficiary ID</th>
                        <th class="px-4 py-2 text-center text-sm font-medium uppercase">Applicant Name</th>
                        <th class="px-4 py-2 text-center text-sm font-medium uppercase">Mobile</th>
                        <th class="px-4 py-2 text-center text-sm font-medium uppercase">Address</th>
                        <th class="px-4 py-2 text-center text-sm font-medium uppercase">Bank Details</th>
                        <th class="px-4 py-2 text-center text-sm font-medium uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($items as $row)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-4 py-2 text-center text-sm text-gray-700">{{ $row['application_id'] }}</td>
                        <td class="px-4 py-2 text-center text-sm text-gray-700">{{ $row['beneficiary_id'] }}</td>
                        <td class="px-4 py-2 text-center text-sm text-gray-700">{{ $row['applicant_name'] }}</td>
                        <td class="px-4 py-2 text-center text-sm text-gray-700">{{ $row['mobile_no'] }}</td>
                        <td class="px-4 py-2 text-center text-sm text-gray-700">
                            <div><span class="font-semibold">District:</span> {{ $row['district'] }}</div>
                            <div><span class="font-semibold">Block:</span> {{ $row['block'] }}</div>
                            <div><span class="font-semibold">GP:</span> {{ $row['panchayat'] }}</div>
                        </td>

                        <td class="px-4 py-2 text-center text-sm text-gray-700">
                            <div><span class="font-semibold">A/C No:</span> {{ $row['bank_account'] }}</div>
                            <div><span class="font-semibold">IFSC:</span> {{ $row['ifsc'] }}</div>
                        </td>
                        <td class="px-4 py-2">
                            <form action="{{ route('reject-approved-beneficiary.de-activate') }}" method="GET" class="flex justify-center">
                                <input type="hidden" name="application_id" value="{{ Crypt::encryptString($row['application_id']) }}">
                                <input type="hidden" name="beneficiary_id" value="{{ Crypt::encryptString($row['beneficiary_id']) }}">
                                <x-button.loading-spiner-button type="submit" text="De-Activate" lockPage="true" />
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    @endif

</div>
