<div>
    <div class="border border-gray-300 rounded-md">
        <div class="bg-blue-100 px-4 py-2 font-bold text-gray-700">
            Self Declaration
        </div>
        <div class="p-4 text-justify text-gray-600 text-sm space-y-1.5">
            @if($resident)
            <div>
                I am a resident of West Bengal.
            </div>
            @endif
            @if($no_govt_salary)
            <div>
                I do not earn any monthly remuneration from any regular Government job.
            </div>
            @endif
            @if($info_true)
            <div>
                That all the information and documents submitted by me are correct / genuine. In case any of the information/ document is found to be false, penal action shall be taken against me and the benefit will be terminated.
            </div>
            @endif
            @if($aadhaar_consent)
            <div>
                I give consent to the use of the Aadhaar No. for authenticating my identity for social security pension (In case Aadhaar no. provided by the applicant).
            </div>
            @endif
        </div>
    </div>
</div>