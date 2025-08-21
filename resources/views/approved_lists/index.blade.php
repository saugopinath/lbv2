<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        <div>
            <h2>{{ $header }}</h2>
        </div>

        <table class="min-w-full divide-y divide-gray-200 text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Application ID</th>
                    <th>Beneficiary ID</th>
                    <th>Name</th>
                    <th>Mobile Number</th>
                    <th>Account Number</th>
                    <th>Branch Name</th>
                    <th>IFSC Code</th>
                    <th>Bank Name</th>
                    <th>Model name</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 dark:text-gray-400">
                @foreach ($lists as $list)
                <tr>
                    <td>{{ $list->id }}</td>
                    <td>{{ $list->sourceable->application_id ?? 'N/A' }}</td>
                    <td>{{ $list->sourceable->beneficiary_id ?? 'N/A' }}</td>
                    <td>{{ $list->sourceable->full_name ?? 'N/A' }}</td>
                    <td>{{ $list->sourceable->mobile_no ?? 'N/A' }}</td>
                    <td>{{ $list->sourceable->bank->bank_account_number ?? 'N/A' }}</td>
                    <td>{{ $list->sourceable->bank->ifscMaster->branch ?? 'N/A' }}</td>
                    <td>{{ $list->sourceable->bank->ifsc ?? 'N/A' }}</td>
                    <td>{{ $list->sourceable->bank->ifscMaster->bankmaster->name ?? 'N/A' }}</td>
                    <td>{{ class_basename($list->sourceable_type) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>