<div class="p-4">
    @php
    use Illuminate\Support\Facades\Crypt;
    @endphp
    <h2 class="text-xl font-bold mb-4">Search Beneficiary</h2>
    @livewire('beneficiary-search', [
    'isApproved' => true,
    'isShownScheme' => true,
    'excludeFields' => ['beneficiary_name','bank_account_number']
    ])
    <x-alart />

    @if(count($items) > 0)
    <div class="mt-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <!-- Header Section -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-blue-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800">
                        Search Beneficiary Results
                    </h2>
                </div>
            </div>
            <!-- Table Container with Horizontal Scroll -->
            <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <!-- Table Header -->
                    <thead class="bg-gradient-to-r from-indigo-600 to-indigo-700">
                        <tr>
                            <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Application ID</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Beneficiary ID</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Applicant Name</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Mobile</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Address</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Bank Details</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($items as $index => $row)
                        <tr class="hover:bg-indigo-50 transition-all duration-200 ease-in-out {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                            <td class="px-5 py-4 text-center">
                                <span class="text-sm font-medium text-indigo-700 bg-indigo-50 px-2 py-1 rounded-lg">{{ $row['application_id'] }}</span>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span class="text-sm text-gray-700 font-mono">{{ $row['beneficiary_id'] }}</span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $row['applicant_name'] }}</span>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center text-sm text-gray-700">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    {{ $row['mobile_no'] }}
                                </div>
                            </td>

                            <td class="px-5 py-4 max-w-xs">
                                <div class="text-sm text-gray-700 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                    {!! $row['address'] !!}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="bg-blue-50 rounded-lg p-2 border border-blue-100">
                                    <div class="text-xs text-gray-600 flex items-center mb-1">
                                        <span class="font-semibold">A/C:</span>
                                        <span class="font-mono text-indigo-600 p-1">{{ $row['bank_account'] }}</span>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <span class="font-semibold">IFSC:</span>
                                        <span class="font-mono text-indigo-600 p-1">{{ $row['ifsc'] }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <form action="{{ route('reject-approved-beneficiary.BeneficiaryDetails') }}" method="GET" class="inline-block">
                                    <input type="hidden" name="application_id" value="{{ Crypt::encryptString($row['application_id']) }}">
                                    <input type="hidden" name="beneficiary_id" value="{{ Crypt::encryptString($row['beneficiary_id']) }}">
                                    <input type="hidden" name="scheme_id" value="{{ Crypt::encryptString($row['scheme_id']) }}">
                                    <button type="submit" class="group relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-medium rounded-lg hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                        <svg class="w-4 h-4 mr-2 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                        De-Activate
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer Section -->
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-sm text-gray-600 flex items-center justify-between">
                <div>
                    Showing <span class="font-semibold">{{ count($items) }}</span> results
                </div>
                <div class="flex space-x-1">
                    <!-- Pagination can be added here if needed -->
                </div>
            </div>
        </div>
    </div>
    @endif

</div>