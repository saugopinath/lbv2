<div class="border border-gray-300 rounded-md">
    <div class="bg-blue-100 px-4 py-2 font-bold text-gray-700">
        Personal Details
    </div>
    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-y-2">
        <div>
            <strong>Swasthya Sathi Card No.:</strong> **********
        </div>
        <div>
            <strong>Aadhaar No.:</strong> {{$decrypted}}
        </div>
        <div>
            <strong>Name:</strong> {{ $applicant->full_name }}
        </div>
        <div>
            <strong>DOB:</strong> {{ \Carbon\Carbon::parse($applicant->dob)->format('d-m-Y') }}
        </div>
        <div>
            <strong>Age:</strong> {{ \Carbon\Carbon::parse($applicant->dob)->age }}
        </div>
        <div>
            <strong>Gender:</strong> Female
        </div>
        <div><strong>Caste:</strong> SC</div>
        <div><strong>SC/ST Certificate No.:</strong> dhhhh344</div>
    </div>
</div>