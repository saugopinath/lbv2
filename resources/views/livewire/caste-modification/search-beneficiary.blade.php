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
                :required="true" />
        </div>
    </div>
    <button wire:click="search" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
    @if ($items)
    <div class="mt-6">
        @if($items && count($items) > 0)
        <table class="table-auto border-collapse border w-full mt-4">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Application ID</th>
                    <th class="border px-4 py-2">Beneficiary ID</th>
                    <th class="border px-4 py-2">Applicant Name</th>
                    <th class="border px-4 py-2">Mobile</th>
                    <th class="border px-4 py-2">Caste</th>
                    <th class="border px-4 py-2">Action</th>

                </tr>
            </thead>
            <tbody>
                @foreach($items as $row)
                <tr>
                    <td class="border px-4 py-2">{{ $row['application_id'] }}</td>
                    <td class="border px-4 py-2">{{ $row['beneficiary_id'] }}</td>
                    <td class="border px-4 py-2">{{ $row['applicant_name'] }}</td>
                    <td class="border px-4 py-2">{{ $row['mobile_no'] }}</td>
                    <td class="border px-4 py-2">{{ $row['Caste_name'] }}</td>
                    <td class="border px-4 py-2">
                        <form action="{{ route('caste-modification.edit') }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="application_id" value="{{ Crypt::encryptString($row['application_id']) }}">
                            <input type="hidden" name="beneficiary_id" value="{{ Crypt::encryptString($row['beneficiary_id']) }}">
                            <button type="submit" class="text-blue-500 hover:underline bg-transparent border-0 cursor-pointer">
                                Caste Change
                            </button>
                        </form>

                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="mt-4 text-red-500">No beneficiary found.</p>
        @endif
    </div>
    @endif
</div>