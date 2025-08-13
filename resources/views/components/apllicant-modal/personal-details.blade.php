<div class="border border-gray-300 rounded-md">
    <div class="bg-blue-100 px-4 py-2 font-bold text-gray-700">
        Personal Details
    </div>
    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-y-2">
        <div>
            <strong>Aadhaar No.:</strong> {{$decryptedAadhaar}}
        </div>
        @if($dsregno != null)
        <div>
            <strong>Duare Sarkar Registration No.:</strong> {{ $dsregno }}
        </div>
        <div>
            <strong>Duare Sarkar Date.:</strong> {{ $dsdate }}
        </div>
        @endif
        <div>
            <strong>Mobile No.:</strong> {{ $mobile }}
        </div>
        @if($email != null)
        <div>
            <strong>Email Id:</strong> {{ $email }}
        </div>
        @endif
        <div>
            <strong>Name:</strong> {{ $fname }}
        </div>
        <div>
            <strong>DOB:</strong> {{ $dob }}
        </div>
        <div>
            <strong>Age (as on {{$currentDate}}):</strong> {{ $age }}
        </div>
        <div>
            <strong>Father's Name:</strong> {{ $ffname }}
        </div>
        <div>
            <strong>Mother's Name:</strong> {{ $mfname }}
        </div>
        @if($email != null)

        <div>
            <strong>Spouce Name:</strong> {{ $sfname }}
        </div>

        @endif
        <div><strong>Caste:</strong> {{ $caste }}</div>
        @if($cascerno != null)
        <div><strong>SC/ST Certificate No.:</strong> {{ $cascerno }}</div>
        @endif
    </div>
</div>