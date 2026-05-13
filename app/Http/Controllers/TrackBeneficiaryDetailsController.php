<?php

namespace App\Http\Controllers;

use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryEnclosure;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Block;
use App\Models\District;
use App\Models\Municipality;
use App\Models\Panchayat;
use App\Models\Scheme;
use App\Models\Subdivision;
use App\Models\Ward;
use App\Models\BenFailedPaymentDetailsJB;
use App\Models\BenFailedPaymentDetailsLB;
use App\Models\BenPaymentDetailsJB;
use App\Models\BenPaymentDetailsLB;
use App\Models\BenTransactionDetailsJB;
use App\Models\BenTransactionDetailsLB;
use App\Models\BeneficiaryBankDetail;
use App\Models\Codemaster;
use App\Models\Ifsccodemaster;
use App\Models\WorkflowsteproleMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TrackBeneficiaryDetailsController extends Controller
{
    public function TrackBeneficiaryDetails()
    {
        $header = 'Track Beneficiary Details';
        return view('TrackBeneficiaryDetails.track_beneficiary_details_view', compact('header'));
    }
    public function BeneficiaryDetailslogs(Request $request)  // dd($id);
    { 
        $id = $request->id;
        $applicationId = Crypt::decryptString($id);
        // dd($applicationId);
        $benPersonal = BeneficiaryPersonalDetail::where('application_id', $applicationId)->first();
        if (empty($benPersonal)) {
            return redirect()->back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Beneficiary details not found.'
                ]
            ]);
        }
        $schemeId = $benPersonal->scheme_id;
        $schemename = Scheme::where('id', $schemeId)->first()->name;
        $benDetailsData = $this->getBeneficiaryDetails($benPersonal);
        $status = $benDetailsData['status'];
        $statusClass = $benDetailsData['statusClass'];
        $ben_profile_pic = $benDetailsData['ben_profile_pic'];
        // dd($ben_profile_pic);
        $activityLogData = $this->benActivityLog($applicationId);
        return view('TrackBeneficiaryDetails.track_beneficiary_log_details', [
            'benPersonal' => $benPersonal,
            'application_id' => $applicationId,
            'scheme_id' => $schemeId,
            'schemename' => $schemename,
            'status' => $status,
            'statusClass' => $statusClass,
            'activityLogData' => $activityLogData,
            'ben_profile_pic' => $ben_profile_pic,
        ]);
    }
    public function getBeneficiaryDetails($b)
    {
        $returnData = [];
        $districtcode = $b->contact->district_id ?? NULL;
        $districtName = District::where('id', $districtcode)->first()->name ?? 'Unknown';
        $schemeName = Scheme::where('id', $b->scheme_id)->first()->name ?? 'Unknown';
        // $status = $b->next_level_role_id == 0 ? 'Approved' : 'Approval Pending';
        // $statusClass = $b->next_level_role_id == 0
        //     ? 'status-active'
        //     : 'status-pending';
        $status = NULL;
        $statusClass = NULL;
        // $nextlevelRoleVer = WorkflowsteproleMapping::where('scheme_id', $b->scheme_id)->where('module_id', Null)->where('rank', 3)->value('next_level_role_id');
        // dd($nextlevelRoleVer);
        if ($b->is_final == 0 && $b->next_level_role_id == NULL) {
            $status = 'Application Partial Entry';
            $statusClass = 'status-pending';
            $statusColor = 'yellow';
            $beneficiaryId = NULL;
        } elseif ($b->is_final == 1 && $b->next_level_role_id == 0) {
            $status = 'Application Final Submitted';
            $statusClass = 'status-active';
            $statusColor = 'orange';
            $beneficiaryId = NULL;
        } elseif ($b->is_final == 1 && $b->next_level_role_id == WorkflowsteproleMapping::where('scheme_id', $b->scheme_id)->where('module_id', Null)->where('rank', 2)->value('next_label_role_id')) {
            $status = 'Verified';
            $statusClass = 'status-active';
            $statusColor = 'blue';
            $beneficiaryId = NULL;
        } elseif ($b->is_final == 1 && $b->next_level_role_id == WorkflowsteproleMapping::where('scheme_id', $b->scheme_id)->where('module_id', Null)->where('rank', 3)->value('next_label_role_id')) {
            $status = 'Approved';
            $statusClass = 'status-active';
            $statusColor = 'green';
            $beneficiaryId = $b->beneficiary_id;
        } else {
            $status = 'Rejected';
            $statusClass = 'status-rejected';
            $statusColor = 'red';
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
        $ben_profile_pic = $b->enclosers()
            ->where('document_type', 103)
            ->first()?->toArray() ?? [];
        // dd($ben_profile_pic);

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
        $returnData['statusColor'] = $statusColor;
        $returnData['ben_profile_pic'] = $ben_profile_pic;

        // $ben_profile_pic = BeneficiaryEnclosure::where('application_id', $b->application_id)->where('document_type', 103)->first();
        // $returnData['ben_profile_pic'] = NULL;

        return $returnData;
    }
    public function benActivityLog($application_id)
    {
        $benPersonal = BeneficiaryPersonalDetail::where('application_id', $application_id)->first();
        $activityLog = AcceptRejectInfo::where('application_id', $application_id)->get();

        $activityLogData = [];
        foreach ($activityLog as $key => $value) {
            $activityLogData[$key]['operation'] = Codemaster::where('id', $value->op_type)->first()->name;
            $activityLogData[$key]['action_date'] = $value->created_at->format('d-m-Y H:i:s');
            $activityLogData[$key]['action_by'] = $value->user->name ?? 'System';
            $activityLogData[$key]['revert_reason_remarks'] = $value->revert_reason_remarks;
        }
        return $activityLogData;
    }

    public function BeneficiaryPaymentHistory(Request $request)
    {
        $id = $request->id;
        $ben_status = NULL;
        $ben_status_color = NULL;
        $ben_status_reason = NULL;
        $application_id = Crypt::decryptString($id);

        $benPersonal = BeneficiaryPersonalDetail::where('application_id', $application_id)->first();
        // dd($benPersonal);
        if (empty($benPersonal)) {
            return redirect()->back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Beneficiary details not found.'
                ]
            ]);
        }
        if ($benPersonal->scheme_id == 20) {
            $paymentDetails = BenPaymentDetailsLB::where('ben_id', $benPersonal->beneficiary_id)->first();
            $transactionDetails = BenTransactionDetailsLB::where('ben_id', $benPersonal->beneficiary_id)->get();
        } else {
            $paymentDetails = BenPaymentDetailsJB::where('ben_id', $benPersonal->beneficiary_id)->first();
            $transactionDetails = BenTransactionDetailsJB::where('ben_id', $benPersonal->beneficiary_id)->get();
        }
        // dd($paymentDetails);
        if (empty($paymentDetails)) {
            return redirect()->back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Payment details not found.'
                ]
            ]);
        }
        if ($benPersonal->scheme_id == 20) {
            $paymentDetails = BenPaymentDetailsLB::where('ben_id', $benPersonal->beneficiary_id)->first();
            $transactionDetails = BenTransactionDetailsLB::where('ben_id', $benPersonal->beneficiary_id)->get();


            if ($paymentDetails->ben_status == 1) {
                $ben_status = 'Active';
                $ben_status_color = 'green';
            } else {
                $ben_status = 'Inactive';
                $ben_status_color = 'red';
                if ($paymentDetails->ben_status == 0) {
                    $ben_status_reason = 'Duplicate Aadhar Beneficiary';
                } elseif ($paymentDetails->ben_status == 9) {
                    $ben_status_reason = 'DOB or name or ss_card is null';
                } elseif ($paymentDetails->ben_status == 77) {
                    $ben_status_reason = 'Duplicate faulty beneficiary with lot not generated';
                } elseif ($paymentDetails->ben_status == 88) {
                    $ben_status_reason = 'Temporary disabled for MCC';
                } elseif ($paymentDetails->ben_status == -30) {
                    $ben_status_reason = 'The beneficiary which are rejected but still getting payment in the payment server';
                } elseif ($paymentDetails->ben_status == -94) {
                    $ben_status_reason = 'Deactivated Due To Death As Per Janma Mrityu Portal';
                } elseif ($paymentDetails->ben_status == -96) {
                    $ben_status_reason = 'Ready For Correction From DDO End';
                } elseif ($paymentDetails->ben_status == -97) {
                    $ben_status_reason = 'Duplicate Bank Account And IFSC';
                } elseif ($paymentDetails->ben_status == -98) {
                    $ben_status_reason = 'Duplicate Bank Account Beneficiary';
                } elseif ($paymentDetails->ben_status == -99) {
                    $ben_status_reason = 'Deactivate Stop Beneficiary';
                } elseif ($paymentDetails->ben_status == -102) {
                    $ben_status_reason = 'Caste category change';
                } elseif ($paymentDetails->ben_status == -400) {
                    $ben_status_reason = 'Application rejected due to major mismatch account';
                } else {
                    $ben_status_reason = 'Invalid Beneficiary';
                }
            }
        } else {
            $paymentDetails = BenPaymentDetailsJB::where('ben_id', $benPersonal->beneficiary_id)->first();
            $transactionDetails = BenTransactionDetailsJB::where('ben_id', $benPersonal->beneficiary_id)->get();


            if ($paymentDetails->ben_status == 1) {
                $ben_status = 'Active';
                $ben_status_color = 'green';
            } else {
                $ben_status = 'Inactive';
                if ($paymentDetails->ben_status == 2) {
                    $ben_status_reason = 'Deactivated Due To Death As Per Janma Mrityu Portal';
                    $ben_status_color = 'red';
                } elseif ($paymentDetails->ben_status == 3) {
                    $ben_status_reason = 'Pause Payment';
                    $ben_status_color = 'red';
                } elseif ($paymentDetails->ben_status == 5) {
                    $ben_status_reason = 'Payment suspended between OAP & WP Duplicate';
                    $ben_status_color = 'red';
                } else {
                    $ben_status_reason = 'Deactivated';
                    $ben_status_color = 'red';
                }
            }
        }

        $benBankDetails = BeneficiaryBankDetail::where('application_id', $application_id)->first();

        $encryptIfsc = $this->maskValue($benBankDetails->ifscode);
        $bankName = Ifsccodemaster::with('bankMaster')->where('code', $benBankDetails->ifscode)->first()->bankMaster->name;
        $bankBranch = Ifsccodemaster::where('code', $benBankDetails->ifscode)->first()->branch;
        $encryptBankCode = $this->maskValue($benBankDetails->bankaccountnumber);
        $schemename = Scheme::where('id', $benPersonal->scheme_id)->first()->name;

        $acc_validation_txt = NULL;
        $acc_validation_txt_1 = NULL;
        $acc_validation_txt_2 = NULL;
        $acc_validation_txt_name_1 = NULL;
        $acc_validation_txt_name_2 = NULL;
        $acc_validation_txt_name = NULL;
        $acc_validation_icon = NULL;
        $acc_validation_class = NULL;
        $acc_validation_color = NULL;


        if ($benPersonal->scheme_id == 20) {
            if ($paymentDetails->ben_status == 1) {
                $acc_validation_icon = 'fa-solid fa-circle-check';
                $acc_validation_color = 'emerald';
                if ($paymentDetails->acc_validated == 0) {
                    $acc_validation_txt_1 = 'Ready for account validation';
                } elseif ($paymentDetails->acc_validated == 1) {
                    $acc_validation_txt_1 = 'Account Validation Lot Generated';
                } elseif ($paymentDetails->acc_validated == 2) {
                    $acc_validation_txt_1 = 'Validation Success.';
                    $acc_validation_txt_2 = 'Ready For Payment';
                } elseif ($paymentDetails->acc_validated == 3) {
                    $acc_validation_txt_1 = 'Validation Failed.';
                    $acc_validation_txt_2 = 'Please Update Bank Details';
                    $acc_validation_icon = 'fa-solid fa-circle-xmark';
                    $acc_validation_color = 'red';

                    $BenFailedPaymentDetailsLB = BenFailedPaymentDetailsLB::where('ben_id', $benPersonal->beneficiary_id)->orderBy('created_at', 'desc')->wherein('edited_status', [0, 1, 2])->first();
                    if ($BenFailedPaymentDetailsLB->failed_type == 1) {
                        $acc_validation_txt_name_1 = 'Account Validation Failed';
                    } elseif ($BenFailedPaymentDetailsLB->failed_type == 3) {
                        $acc_validation_txt_name_1 = 'Name Validation Failed';
                        $acc_validation_txt_name_2 = $BenFailedPaymentDetailsLB->matching_score;
                    } else {
                        $acc_validation_txt_name_1 = NULL;
                    }
                } elseif ($paymentDetails->acc_validated == 4) {
                    $acc_validation_txt_1 = 'Payment Transaction Failed.';
                    $acc_validation_txt_2 = 'Please Update Bank Details';
                    $acc_validation_icon = 'fa-solid fa-circle-xmark';
                    $acc_validation_color = 'red';
                } else {
                    $acc_validation_txt = NULL;
                    $acc_validation_txt_1 = NULL;
                    $acc_validation_txt_2 = NULL;
                    $acc_validation_icon = NULL;
                    $acc_validation_class = NULL;
                    $acc_validation_color = NULL;
                    $acc_validation_txt_name_1 = NULL;
                    $acc_validation_txt_name_2 = NULL;
                }

                $acc_validation_txt = $acc_validation_txt_1 . $acc_validation_txt_2;
            } else {

                $acc_validation_txt = 'Inactive Beneficiary';
                $acc_validation_icon = 'fa-solid fa-circle-xmark';
                $acc_validation_color = 'red';
            }
        } else {
            if ($paymentDetails->ben_status == 1) {
                $acc_validation_icon = 'fa-solid fa-circle-check';
                $acc_validation_color = 'emerald';
                $acc_validation_txt_2 = 'Ready For Payment';

                if (in_array($benPersonal->scheme_id, [2, 10, 11, 13])) {
                    if ($paymentDetails->acc_validated == 0) {
                        $acc_validation_txt_1 = 'Ready for account validation';
                        $acc_validation_txt_2 = NULL;
                    } elseif ($paymentDetails->acc_validated == 1) {
                        $acc_validation_txt_1 = 'Account Validation Lot Generated';
                        $acc_validation_txt_2 = NULL;
                    } elseif ($paymentDetails->acc_validated == 2) {
                        $acc_validation_txt_1 = 'Validation Success.';
                    } elseif (in_array($benPersonal->scheme_id, [2, 10, 11, 13]) && in_array($paymentDetails->acc_validated, [3, 4])) {
                        if (in_array($paymentDetails->acc_validated, [3, 4]) && (in_array($benPersonal->scheme_id, [2, 10, 11]))) {
                            $acc_validation_txt_1 = 'Validation Failed.';
                            $acc_validation_txt_2 = 'Please Update Bank Details';
                            $acc_validation_icon = 'fa-solid fa-circle-xmark';
                            $acc_validation_color = 'red';
                            if ($paymentDetails->acc_validated == 3) {
                                $acc_validation_txt_name_1 = 'Account Validation Failed';
                            } elseif ($paymentDetails->acc_validated == 4) {
                                $acc_validation_txt_name_1 = 'Name Validation Failed';
                                if (in_array($benPersonal->scheme_id, [2, 10, 11])) {
                                    $BenFailedPaymentDetailsLB = BenFailedPaymentDetailsJB::where('ben_id', $benPersonal->beneficiary_id)->where('failed_type', 2)->orderBy('created_at', 'desc')->wherein('edited_status', [0, 1, 2])->first();
                                    $acc_validation_txt_name_2 = $BenFailedPaymentDetailsLB->matching_score;
                                } else {
                                    $acc_validation_txt_name_2 = NULL;
                                }
                            }
                        } elseif (in_array($paymentDetails->acc_validated, [3, 4]) && ($paymentDetails->scheme_id == 13)) {
                            $acc_validation_txt_1 = 'Validation Failed.';
                            $acc_validation_txt_2 = 'Please Update Bank Details';
                            $acc_validation_icon = 'fa-solid fa-circle-xmark';
                            $acc_validation_color = 'red';
                        }
                    }
                }

                if (in_array($paymentDetails->pay_validated, [3, 4, 5])) {
                    $acc_validation_txt_1 = 'Payment Transaction Failed.';
                    $acc_validation_txt_2 = 'Please Update Bank Details';
                    $acc_validation_icon = 'fa-solid fa-circle-xmark';
                    $acc_validation_color = 'red';
                }
                $acc_validation_txt = $acc_validation_txt_1 . $acc_validation_txt_2;
            } else {

                $acc_validation_txt = 'Inactive Beneficiary';
                $acc_validation_icon = 'fa-solid fa-circle-xmark';
                $acc_validation_color = 'red';
            }
        }

        $acc_validated = array();
        $acc_validated['txt'] = $acc_validation_txt;
        $acc_validated['icon'] = $acc_validation_icon;
        $acc_validated['color'] = $acc_validation_color;
        $acc_validated['txt_name_1'] = $acc_validation_txt_name_1;
        $acc_validated['txt_name_2'] = $acc_validation_txt_name_2;
        // dd($acc_validated);
        $benPayComment = $this->benPayComment($benPersonal, $paymentDetails);
        // dd($benPersonal, $paymentDetails);

        return view('TrackBeneficiaryDetails.track_beneficiary_payment_log_details', [
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
            'ben_status_reason' => $ben_status_reason,
            'ben_status_color' => $ben_status_color,
            'acc_validated' => $acc_validated,
            'benPayComment' => $benPayComment,
            'bankName' => $bankName,
            'bankBranch' => $bankBranch
        ]);
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

    public function benPayComment($benPersonlDetails, $benPayDetail)
    {
        $response = [];
        $response['comment'] = NULL;
        $response['color'] = 'green';

        if ($benPersonlDetails->scheme_id == 20) {
            if ($benPayDetail->ben_status == 1) {
                if (in_array($benPayDetail->acc_validated, [3, 4])) {
                    $response['comment'] = 'Contact with District (Approver) Office for Bank Details Updation';
                }
            }
            if ($benPayDetail->ben_status == 9) {
                $response['comment'] = 'Contact with District (Approver) or Department (HOD) Office for Bank Details Updation';
            }
            if ($benPayDetail->ben_status == 0) {
                $response['comment'] = 'Contact with Block / Sub-Divisional Office for Bank Details Updation';
            }
            if (in_array($benPayDetail->ben_status, [-400, -99, -98, -97])) {
                $response['comment'] = 'Contact with District (Approver) or Department (HOD) Office for Bank Details Updation';
            }
            if (in_array($benPayDetail->ben_status, [-94])) {
                $response['comment'] = 'Contact with District (Approver) Office for Beneficiary Re-activation';
            }
        } else {
            if ($benPayDetail->ben_status == 1) {
                if (in_array($benPersonlDetails->scheme_id, [2, 10, 11, 13]) && in_array($benPayDetail->acc_validated, [3, 4])) {
                    if (in_array($benPayDetail->acc_validated, [3, 4]) && (in_array($benPayDetail->scheme_id, [2, 10, 11]))) {
                        $response['comment'] = 'Contact with District (Approver) Office for Bank Details Updation';
                    }
                    if (in_array($benPayDetail->acc_validated, [3, 4]) && ($benPayDetail->scheme_id == 13)) {
                        $response['comment'] = 'Contact with Block / Sub-Divisional Office for Bank Details Updation';
                    }
                }

                if (in_array($benPersonlDetails->scheme_id, [2, 10, 11]) && in_array($benPayDetail->pay_validated, [3, 4, 5])) {
                    if (in_array($benPersonlDetails->scheme_id, [2, 10, 11])) {
                        $response['comment'] = 'Contact with District (Approver) Office for Bank Details Updation';
                    }
                    if (in_array($benPersonlDetails->scheme_id, [1, 5, 6, 7, 19])) {
                        $response['comment'] = 'Contact with Block / Sub-Divisional Office for Bank Details Updation';
                    }
                    if (in_array($benPersonlDetails->scheme_id, [3])) {
                        $response['comment'] = 'Contact with Block / Sub-Divisional Office for Bank Details Updation';
                    }
                    if (in_array($benPersonlDetails->scheme_id, [13])) {
                        $response['comment'] = 'Contact with Block / Sub-Divisional Office for Bank Details Updation';
                    }
                    if (in_array($benPersonlDetails->scheme_id, [8, 9])) {
                        $response['comment'] = 'Contact with District (Approver) Office for Bank Details Updation';
                    }
                    if (in_array($benPersonlDetails->scheme_id, [17])) {
                        $response['comment'] = 'Contact with Block / Sub-Divisional Office for Bank Details Updation';
                    }
                }
            } elseif ($benPayDetail->ben_status == 2) {
                if ($benPersonlDetails->scheme_id == 13) {
                    $response['comment'] = 'Contact with Block / Sub-Divisional Office for Beneficiary Re-activation';
                } else {
                    $response['comment'] = 'Contact with District (Approver) Office for Beneficiary Re-activation';
                }
            } elseif ($benPayDetail->ben_status == 3 || $benPayDetail->ben_status == 5) {
                $response['comment'] = 'Contact with Block / Sub-Divisional Office for Bank Details Updation';
            } else {
                $response['comment'] = NULL;
            }
        }

        return $response;
    }
}
