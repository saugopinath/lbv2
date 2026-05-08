<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\Block;
use App\Models\District;
use App\Models\Municipality;
use App\Models\Panchayat;
use App\Models\Scheme;
use App\Models\Subdivision;
use App\Models\Ward;
use Illuminate\Http\Request;

class TrackBeneficiaryDetailsController extends Controller
{
    public function TrackBeneficiaryDetails()
    {
        $header = 'Track Beneficiary Details';
        return view('TrackBeneficiaryDetails.track_beneficiary_details_view', compact('header'));
    }
}
