
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Bank Name</p>
        <p class="font-semibold text-gray-800">{{ $bankname }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Branch Name</p>
        <p class="font-semibold text-gray-800">{{ $bankbranchname }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">Account Number</p>
        <p class="font-semibold text-gray-800">{{ $bankaccountnumber }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
        <p class="text-xs text-gray-500">IFSC Code</p>
        <p class="font-semibold text-gray-800">{{ $ifscode }}</p>
    </div>
</div>
