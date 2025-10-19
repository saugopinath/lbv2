<div class="p-4">

    <h2 class="text-xl font-bold mb-4">Enter Beneficiary Details Here</h2>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <!-- Select search type -->
        <div>
            <x-form.select name="searchType" id="searchType" label="Search Applicant By" wire:model.live="searchType"
                placeholder="--Select Search Type--" required>
                <option value="">--Select Search Type--</option>
                @foreach ($searchOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </x-form.select>
        </div>

        <div>
            <x-form.input name="searchValue" id="searchValue" wire:model.defer="searchValue"
                label="{{ $this->currentLabel }}" placeholder="Enter {{ $this->currentLabel }}" required type="text"
                oninput="this.value = this.value.replace(/[^0-9]/g);"
                maxlength="{{ $searchType == '3' ? 12 : ($searchType == '4' ? 10 : '') }}" />
        </div>
    </div>
    <x-button.loading-button action="search" text="Search"></x-button.loading-button>
    <x-alart />
    @if (count($items) > 0)
        <div class="mt-6">
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-indigo-500 text-white">
                        <tr>
                            <th class="px-4 py-2 text-center text-sm font-medium uppercase">Application ID</th>
                            <th class="px-4 py-2 text-center text-sm font-medium uppercase">Beneficiary ID</th>
                            <th class="px-4 py-2 text-center text-sm font-medium uppercase">Applicant Name</th>
                            <th class="px-4 py-2 text-center text-sm font-medium uppercase">Mobile</th>
                            <th class="px-4 py-2 text-center text-sm font-medium uppercase">Address</th>
                            <th class="px-4 py-2 text-center text-sm font-medium uppercase">Banking Information</th>
                            <th class="px-4 py-2 text-center text-sm font-medium uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($items as $row)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-4 py-2 text-center text-sm text-gray-700">{{ $row['application_id'] }}
                                </td>
                                <td class="px-4 py-2 text-center text-sm text-gray-700">{{ $row['beneficiary_id'] }}
                                </td>
                                <td class="px-4 py-2 text-center text-sm text-gray-700">{{ $row['applicant_name'] }}
                                </td>
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

                                <td class="px-4 py-2 text-center text-sm text-gray-700">
                                    <form method="get" id="updateForm{{ $row['application_id'] }}">
                                        <!-- Hidden Inputs -->
                                        <input type="hidden" name="application_id"
                                            value="{{ Crypt::encryptString($row['application_id']) }}">
                                        <input type="hidden" name="beneficiary_id"
                                            value="{{ Crypt::encryptString($row['beneficiary_id']) }}">

                                        <div x-data="{ selected: '' }">
                                            <!-- Dropdown -->
                                            <x-form.select x-model="selected">
                                                <option value="">---Select---</option>
                                                <option value="bank">Update Bank Details</option>
                                                <option value="mobile">Update Mobile Number</option>
                                            </x-form.select>

                                            <div class="mt-2" x-show="selected !== ''">
                                                <x-button.primary
                                                    class="px-12 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold shadow-sm transition duration-200 ease-in-out"
                                                    @click.prevent="
                                                if(selected !== '') {
                                                    $el.closest('form').action='{{ url('bank-update/search-beneficiary') }}/'+selected;
                                                    $el.closest('form').submit();
                                                }
                                            ">
                                                    Edit
                                                </x-button.primary>
                                            </div>
                                        </div>
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
