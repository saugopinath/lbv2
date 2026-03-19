<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\BenDocs;
use App\Models\Beneficiary;
use App\Models\BeneficiaryBankDetail;
use App\Models\BeneficiaryEnclosure;
use App\Models\BeneficiaryJBLB;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\BenEntry;
use App\Models\BenPaymentDetailsJB;
use App\Models\BenPaymentDetailsLB;
use App\Models\BenTransactionDetailsJB;
use App\Models\BenTransactionDetailsLB;
use App\Models\Block;
use App\Models\District;
use App\Models\GP;
use App\Models\Scheme;
use App\Models\Taluka;
use App\Models\UrbanBody;
use App\Models\Ward;
use App\Models\DocumentType;
use App\Models\Municipality;
use App\Models\Panchayat;
use App\Models\Subdivision;
use App\Models\WorkflowStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class BeneficiaryTrackController extends Controller
{
    public function trackBeneficiary(Request $request)
    {


        // Initial Page Load
        $schemes = Scheme::where('is_active', 1)->get();
        $districts = District::all();

        // Count total accessible records via Scout
        $results = BeneficiaryPersonalDetail::search('*')->get()->count();

        // Fetch Location Data for Scripts
        $blocks = Block::select('id  as id', 'name as name', 'district_id')
            ->get();

        $subDistricts = Subdivision::select('id as id', 'name as name', 'district_id')
            ->get();

        $ulbs = Municipality::select('id as id', 'name as name', 'subdivision_id')
            ->get();

        $gps = Panchayat::select('id as id', 'name as name', 'block_id')
            ->get();

        $ulb_wards = Ward::select('id as id', 'name as name', 'municipality_id')
            ->get();

        return view('frontend.track-ben.ben-track', compact(
            'schemes',
            'districts',
            'results',
            'blocks',
            'subDistricts',
            'ulbs',
            'gps',
            'ulb_wards'
        ));
    }

    public function trackBeneficiaryData(Request $request)
    {
        try {

            if ($request->wantsJson()) {

                $limit = 100;
                $offset = (int) $request->get('offset', 0);
                $page = ($offset / $limit) + 1;

                $search = $request->search;

                // 🔥 Meilisearch query
                $scout = BeneficiaryPersonalDetail::search($search ?: '');

                // ✅ Filters (must match filterableAttributes exactly)

                if ($request->scheme) {
                    $scout->where('scheme_id', (int) $request->scheme);
                }

                if ($request->district) {
                    $scout->where('district_id', (int) $request->district);
                }

                if ($request->urban_code) {
                    $scout->where('rural_urban', $request->urban_code);
                }

                if ($request->block) {
                    $scout->where('blockurban', (int) $request->block);
                }

                if ($request->muncid) {
                    $scout->where('blockurban', (int) $request->muncid);
                }

                if ($request->gp_ward) {
                    $scout->where('gpward', (int) $request->gp_ward);
                }

                // ✅ Sort by application_id (must be sortable attribute)
                $beneficiaries = $scout
                    ->orderBy('application_id', 'desc')
                    ->paginate($limit, 'page', $page);

                $total = $beneficiaries->total();
                $html = '';

                foreach ($beneficiaries as $b) {

                    $data = $this->getBeneficiaryDetails($b);

                    $paymentUrl = URL::signedRoute('beneficiary.payment.history', ['id' => $data['applicationId']]);

                    $html .= view('frontend.track-ben.beneficiary-card', [
                        'status' => $data['status'],
                        'statusClass' => $data['statusClass'],
                        'applicationId' => $data['applicationId'],
                        'beneficiaryId' => $data['beneficiaryId'],
                        'name' => $data['name'],
                        'relation' => $data['relation'],
                        'relationName' => $data['relationName'],
                        'schemeName' => $data['schemeName'],
                        'location' => $data['location'],
                        'mobile' => $data['mobile'],
                        'paymentUrl' => $paymentUrl,
                    ])->render();
                }

                return response()->json([
                    'html' => $html,
                    'total' => $total,
                    'loaded' => $offset + count($beneficiaries->items())
                ]);
            }
        } catch (\Throwable $e) {

            return response()->json([
                'html' => '',
                'total' => 0,
                'loaded' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }


    public function trackBeneficiaryPaymentHistory($id)
    {
        $application_id = $id;

        $benPersonal = BeneficiaryPersonalDetail::where('application_id', $application_id)->first();
        if ($benPersonal->scheme_id == 20) {
            $paymentDetails = BenPaymentDetailsLB::where('ben_id', $benPersonal->beneficiary_id)->first();
            $transactionDetails = BenTransactionDetailsLB::where('ben_id', $benPersonal->beneficiary_id)->get();
            $ben_status = NULL;

            if ($paymentDetails->ben_status == 1) {
                $ben_status = 'Active';
            } else {
                $ben_status = 'Inactive';
            }
        } else {
            $paymentDetails = BenPaymentDetailsJB::where('ben_id', $benPersonal->beneficiary_id)->first();
            $transactionDetails = BenTransactionDetailsJB::where('ben_id', $benPersonal->beneficiary_id)->get();
            $ben_status = NULL;

            if ($paymentDetails->ben_status == 1) {
                $ben_status = 'Active';
            } else {
                $ben_status = 'Inactive';
            }
        }

        $benBankDetails = BeneficiaryBankDetail::where('application_id', $application_id)->first();

        $encryptIfsc = $this->maskValue($benBankDetails->ifscode);
        $encryptBankCode = $this->maskValue($benBankDetails->bankaccountnumber);
        $schemename = Scheme::where('id', $benPersonal->scheme_id)->first()->name;



        return view('frontend.track-ben.ben-payment-status', [
            'application_id' => $application_id,
            'beneficiary_id' => $benPersonal->beneficiary_id,
            'scheme_id' => $benPersonal->scheme_id,
            'paymentDetails' => $paymentDetails,
            'benPersonal' => $benPersonal,
            'benBankDetails' => $benBankDetails,
            'encryptIfsc' => $encryptIfsc,
            'encryptBankCode' => $encryptBankCode,
            'schemename' => $schemename,
            'ben_status' => $ben_status,
        ]);
    }


    public function getBeneficiaryDetails($b)
    {

        $returnData = [];

        // 🔥 No more DB queries if you include names in index
        $districtcode = $b->contact->district_id ?? NULL;
        $districtName = District::where('id', $districtcode)->first()->name ?? 'Unknown';
        $schemeName = Scheme::where('id', $b->scheme_id)->first()->name ?? 'Unknown';

        // $status = $b->next_level_role_id == 0 ? 'Approved' : 'Approval Pending';
        // $statusClass = $b->next_level_role_id == 0
        //     ? 'status-active'
        //     : 'status-pending';

        $status = NULL;
        $statusClass = NULL;
        if ($b->is_final == 0 && $b->next_level_role_id == NULL) {
            $status = 'Application Partial Entry';
            $statusClass = 'status-pending';
            $beneficiaryId = NULL;
        } elseif ($b->is_final == 1 && $b->next_level_role_id == 0) {
            $status = 'Application Final Submitted';
            $statusClass = 'status-active';
            $beneficiaryId = NULL;
        } elseif ($b->is_final == 1 && $b->next_level_role_id == WorkflowStep::where('scheme_id', $b->scheme_id)->where('rank', 2)->first()->next_level_roleid) {
            $status = 'Verified';
            $statusClass = 'status-active';
            $beneficiaryId = NULL;
        } elseif ($b->is_final == 1 && $b->next_level_role_id == WorkflowStep::where('scheme_id', $b->scheme_id)->where('rank', 3)->first()->next_level_roleid) {
            $status = 'Approved';
            $statusClass = 'status-active';
            $beneficiaryId = $b->beneficiary_id;
        } else {
            $status = 'Rejected';
            $statusClass = 'status-rejected';
            $beneficiaryId = NULL;
        }


        $relation = NULL;
        $relationName = NULL;
        if (!is_null($b->ben_father_name)) {
            $relation = 'Father';
            $relationName = $b->ben_father_name;
        } elseif (!is_null($b->ben_mother_name)) {
            $relation = 'Mother';
            $relationName = $b->ben_mother_name;
        } elseif (!is_null($b->ben_spouse_name)) {
            $relation = 'Spouse';
            $relationName = $b->ben_spouse_name;
        } else {
            $relation = 'N/A';
            $relationName = 'N/A';
        }

        $returnData['status'] = $status;
        $returnData['statusClass'] = $statusClass;
        $returnData['applicationId'] = $b->application_id;
        $returnData['name'] = $b->beneficiary_name ?? 'N/A';
        $returnData['relation'] = $relation;
        $returnData['relationName'] = $relationName;
        $returnData['schemeName'] = $schemeName;
        $returnData['location'] = $districtName . ', West Bengal';
        $returnData['mobile'] = $b->other_details['mobile_no'] ?? 'N/A';
        $returnData['beneficiaryId'] = $beneficiaryId;

        // $ben_profile_pic = BeneficiaryEnclosure::where('application_id', $b->application_id)->where('document_type', 103)->first();
        // $returnData['ben_profile_pic'] = NULL;

        return $returnData;
    }
    function maskValue($value)
    {
        $length = strlen($value);

        if ($length <= 6) {
            return str_repeat('X', $length); // fully masked if too short
        }

        return substr($value, 0, 3)
            . str_repeat('X', $length - 6)
            . substr($value, -3);
    }

    public function getStatusUTRAndErrorFun(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax() && !$request->isJson()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in form submit.');
            return response()->json($response, $statusCode);
        }
        try {
            $pension_id = $request->pension_id;
            $scheme_id = $request->schemeId;
            $fin_year = $request->fin_year;
            $lot_no = $request->lot_no;
            
            $lotObj = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')
                ->where('ben_id', $pension_id)
                ->where('scheme_id', $scheme_id)
                ->whereIn('failed_type', [3, 4, 5])
                ->first();
                
            if (!$lotObj) {
                return response()->json([
                    'status' => 0,
                    'msg' => 'No detailed error message found.',
                    'title' => 'Information'
                ]);
            }

            if ($lotObj->pmt_mode == 1) {
                $results = DB::connection('pgsql_paywrite')->select("SELECT remarks FROM payment.failed_payment_details WHERE ben_id = " . $pension_id . " AND scheme_id = " . $scheme_id . " AND lot_no = '" . $lot_no . "'");
                return response()->json([
                    'status' => 1,
                    'msg' => $results[0]->remarks ?? 'No remarks found',
                    'title' => 'Information'
                ]);
            } elseif ($lotObj->pmt_mode == 2) {
                $results = DB::connection('pgsql_paywrite')->select("SELECT ct.description FROM payment.failed_payment_details fp JOIN sbi.credit_transaction_code ct ON fp.status_code = ct.code WHERE ben_id = " . $pension_id . " AND scheme_id = " . $scheme_id . " AND lot_no = '" . $lot_no . "'");
                return response()->json([
                    'status' => 1,
                    'msg' => $results[0]->description ?? 'No description found',
                    'title' => 'Information'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'exception' => true,
                'exception_message' => 'Error! Please try again.',
            ], 400);
        }
    }
}
