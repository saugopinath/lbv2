<div class="p-4">
    @php
    use Illuminate\Support\Facades\Crypt;
    @endphp
    <h2 class="text-xl font-bold mb-4">Search Beneficiary</h2>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <!-- Select search type -->
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
            <x-form.input
                name="searchValue"
                id="searchValue"
                wire:model.defer="searchValue"
                label=" {{ $this->currentLabel }}"
                placeholder="Enter {{ $this->currentLabel }}"
                required />
        </div>
    </div>
    <button wire:click="search" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
    @if (session()->has('warning'))
    <p class="mt-4 text-red-500 font-medium">{{ session('warning') }}</p>
    @endif
    @if ($items)
    <div class="mt-6">
        @if(count($items) > 0)
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-500 text-white">
                    <tr>
                        <th class="px-4 py-2 text-center text-sm font-medium uppercase">Application ID</th>
                        <th class="px-4 py-2 text-center text-sm font-medium uppercase">Beneficiary ID</th>
                        <th class="px-4 py-2 text-center text-sm font-medium uppercase">Applicant Name</th>
                        <th class="px-4 py-2 text-center text-sm font-medium uppercase">Mobile</th>
                        <th class="px-4 py-2 text-center text-sm font-medium uppercase">Caste</th>
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
                        <td class="px-4 py-2 text-center text-sm text-gray-700">{{ $row['Caste_name'] }}</td>
                        <td class="px-4 py-2 text-center">
                            <form action="{{ route('caste-modification.edit') }}" method="GET">
                                <input type="hidden" name="application_id" value="{{ Crypt::encryptString($row['application_id']) }}">
                                <input type="hidden" name="beneficiary_id" value="{{ Crypt::encryptString($row['beneficiary_id']) }}">
                                <button type="submit" class="text-indigo-500 hover:text-indigo-800 font-medium">
                                    Caste Change
                                </button>
                            </form>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mt-4">
            No beneficiary found.
        </div>
        @endif
    </div>
    @endif

</div>