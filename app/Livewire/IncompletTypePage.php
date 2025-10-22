<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Codemaster;
use App\Helpers\ChechDupHelper;
use App\Models\BeneficiaryBank;
use App\Models\AcceptRejectInfo;
use Illuminate\Support\Facades\DB;
use App\Models\BeneficiaryCommonList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\BeneficiaryTemEnclosure;
use App\Models\ApplicantIncompletDeatil;
use Illuminate\Support\Facades\Route;

class IncompletTypePage extends Component
{
    public $id, $page, $stage, $applicantInfo, $formData = [], $revertReasons = [], $user_id, $revert_reason_cause_id, $revert_reason_remarks, $aadhaarIssues = [], $mobileIssues = [], $sortedBankIssues = [], $ifscode, $bank_account_number, $bank_action, $confirmbankaccountnumber;

    protected $listeners = ['trigger-update' => 'recivedupdateddata', 'validate-revert' => 'validateRevert', 'do-revert' => 'revert'];

    public function mount($id)
    {
        // $this->stage = request()->query('stage');
        // $this->id = $id;
        $this->id = decrypt($id);
        // dd($this->id);
        $this->stage = decrypt(request()->query('stage'));
        // dd($this->stage );
        $select_lgd = session('lgd_session');
        $this->user_id = Crypt::decryptString($select_lgd['role_id']);

        $this->revertReasons = Codemaster::where('parent_id', Codemaster::getIdByCode(12))->get();

        $this->page = ApplicantIncompletDeatil::where('application_id', $this->id)
            ->with([
                'incompletType',
                'beneficiaryCommonList.enclosures',
                'beneficiaryCommonList.aadhaar',
                'beneficiaryCommonList.bank',
                'beneficiaryCommonList.sourceable.bank',
                'beneficiaryCommonList.sourceable.father',
                'beneficiaryCommonList.panchayat',
                'beneficiaryCommonList.ward',
            ])->get();

        $this->applicantInfo = $this->page->first()?->beneficiaryCommonList;

        foreach ($this->page as $item) {

            if (!empty($item->new_value)) {
                $decoded = is_string($item->new_value) ? json_decode($item->new_value, true) : $item->new_value;

                if (isset($decoded['aadhaar_no'])) {
                    $this->formData['aadhar_modification'][$item->application_id] = $decoded['aadhaar_no'];
                }

                if (isset($decoded['mobile_no'])) {
                    $this->formData['dup_mobile'][$item->application_id] = $decoded['mobile_no'];
                }
            }
        }

        $this->classifyIssues();
    }

    public function recivedupdateddata($data)
    {
        // dd($data);
        $this->ifscode = $data['ifscode'];
        $this->bank_account_number = $data['bank_account_number'];
        $this->bank_action = $data['bank_action'];
        $this->confirmbankaccountnumber = $data['confirmbankaccountnumber'];
    }

    public function approve()
    {
        try {
            DB::beginTransaction();

            $opType = Codemaster::where('code', 2103)->value('id');
            $previousId = AcceptRejectInfo::where('application_id', $this->id)
                ->where('op_type', $opType)
                ->orderByDesc('id')
                ->value('id');

            // Create new approve request
            $request = AcceptRejectInfo::create([
                'application_id'         => $this->id,
                'beneficiary_id'         => $this->applicantInfo->beneficiary_id ?? null,
                'ip_address'             => request()->ip(),
                'user_id'                => $this->user_id,
                'browser'                => request()->header('User-Agent'),
                'model_name'             => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod(),
                'op_type'                => Codemaster::where('code', 2104)->value('id'),
                'revert_reason_cause_id' => null,
                'revert_reason_remarks'  => null,
                'parent_id'              => $previousId,
            ]);

            foreach ($this->page as $item) {
                $typeId = $item->incomplet_type;
                if (!$typeId) continue;

                $jsonValue = [];

                // Aadhaar related
                if (in_array($typeId, ['141', '149', '1414'])) {
                    $jsonValue = [
                        'aadhaar_no'     => $this->formData['aadhar_modification'][$item->application_id] ?? null,
                        'application_id' => $this->id,
                    ];
                }
                // Mobile related
                elseif (in_array($typeId, ['142', '1410'])) {
                    $jsonValue = [
                        'mobile_no'      => $this->formData['dup_mobile'][$item->application_id] ?? null,
                        'application_id' => $this->id,
                    ];
                }
                // Bank related
                elseif (in_array($typeId, ['145', '146', '1411', '1412', '1413'])) {
                    $jsonValue = [
                        'ifscode'                 => $this->ifscode,
                        'bank_account_number'     => $this->bank_account_number,
                        'confirmbankaccountnumber' => $this->confirmbankaccountnumber,
                        'bank_action'             => $this->bank_action,
                        'application_id'          => $this->id,
                    ];
                }

                // Update incomplete item
                if (!empty($jsonValue)) {
                    if ($item->is_active == 1) {
                        $item->update([
                            'next_level_request_id' => 2,
                            'request_id'            => $request->id,
                            'is_active'             => -1,
                        ]);
                    } elseif ($item->is_active == 0) {
                        $item->update([
                            'next_level_request_id' => 2,
                            'request_id'            => $request->id,
                        ]);
                    }
                }

                // Update main/original table
                $this->updateOriginalTable($item);
            }

            DB::commit();

            session()->flash('success', "Approve details updated successfully for Application ID: {$this->id}");
            return redirect()->route('incomplete.types', ['stage' => 'approver', 'id' => $this->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Approve Failed: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'application_id' => $this->id,
                'user_id' => $this->user_id,
            ]);

            return redirect()->back()
                ->with('error', 'Something went wrong while approving: ' . $e->getMessage())
                ->withInput();
        }
    }


    protected function updateOriginalTable($item)
    {
        // dd('ok');
        if ($item->is_active != -1) {
            return;
        }

        $typeId = $item->incomplet_type;

        if (in_array($typeId, ['141', '149', '1414'])) {
            $newAadhaar = $item->new_value['aadhaar_no'] ?? null;
        } elseif (in_array($typeId, ['142', '1410'])) {
            $newMobile = $item->new_value['mobile_no'] ?? null;
        } else {
            $bank_action = $item->change_type;
            $newifscode = $item->new_value['ifscode'] ?? null;
            $newBankAccountNumber = $item->new_value['bank_account_number'] ?? null;
        }

        $beneficiary = $item->beneficiaryCommonList;


        switch ($typeId) {
            case 141: // NO AADHAR NUMBER
            case 149: // DUPLICATE AADHAR NUMBER
            case 1414: // PDS Mismatch
                $beneficiary->aadhaar()->updateOrCreate(
                    ['application_id' => $this->id],
                    [
                        'encoded_aadhar' => Crypt::encryptString($newAadhaar),
                        'aadhar_hash'    => md5($newAadhaar),
                        'created_by'     => Auth::id(),
                    ]
                );

                $temp = BeneficiaryTemEnclosure::where('application_id', $this->id)->first();
                if ($temp) {
                    $beneficiary->enclosuresUpdated()->updateOrCreate(
                        ['application_id' => $this->id],
                        [
                            'attched_document'   => $temp->attched_document,
                            'document_type'      => $temp->document_type,
                            'document_extension' => $temp->document_extension,
                            'document_mime_type' => $temp->document_mime_type,
                            'ip_address'         => request()->ip(),
                            'created_by'         => Auth::id(),
                            'updated_at'         => now(),
                        ]
                    );

                    $temp->delete();
                }

                BeneficiaryCommonList::where('sourceable_id', $this->id)
                    ->update([
                        'encoded_aadhar' => Crypt::encryptString($newAadhaar),
                    ]);
                break;

            case 142: // NO MOBILE NUMBER
            case 1410: // DUPLICATE MOBILE NUMBER
                $beneficiary->beneficiaryPersonal()->updateOrCreate(
                    ['application_id' => $this->id],
                    [
                        'mobile_no'  => $newMobile,
                        'created_by' => Auth::id(),
                        'is_faulty'  => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                if ($typeId == 142) {
                    $beneficiary->faultyBeneficiaryPersonal()
                        ->where('application_id', $this->id)
                        ->delete();
                }

                BeneficiaryCommonList::where('sourceable_id', $this->id)
                    ->update([
                        'mobile_no' => $newMobile,
                    ]);
                break;

            case 145: // NAME VALIDATION FAILED IN BANK
                $beneficiary->failedPaymentDetails()->update([
                    'edited_status' => 2,
                    'failed_type'   => 3,
                    'accno'         => $newBankAccountNumber,
                    'ifsc'          => $newifscode,
                ]);

                if ($bank_action == 2 || $bank_action == 3) {
                    $temp = BeneficiaryTemEnclosure::where('application_id', $this->id)->first();

                    if ($temp) {
                        $beneficiary->enclosuresUpdated()->updateOrCreate(
                            ['application_id' => $this->id],
                            [
                                'attched_document'   => $temp->attched_document,
                                'document_type'      => $temp->document_type,
                                'document_extension' => $temp->document_extension,
                                'document_mime_type' => $temp->document_mime_type,
                                'ip_address'         => request()->ip(),
                                'created_by'         => Auth::id(),
                                'updated_at'         => now(),
                            ]
                        );

                        $temp->delete();
                    }
                }

                BeneficiaryCommonList::where('sourceable_id', $this->id)
                    ->update([
                        'bank_account_number' => $newBankAccountNumber,
                    ]);
                break;

            case 146: // ACCOUNT NUMBER VALIDATION FAILED IN BANK
            case 1411: // DUPLICATE BANK ACCOUNT NUMBER
                $beneficiary->bank()->updateOrCreate(
                    ['application_id' => $this->id],
                    [
                        'bank_account_number' => $newBankAccountNumber,
                        'ifsc'               => $newifscode,
                        'created_by'         => Auth::id(),
                    ]
                );

                if ($bank_action == 2 || $bank_action == 3) {
                    $temp = BeneficiaryTemEnclosure::where('application_id', $this->id)->first();

                    if ($temp) {
                        $beneficiary->enclosuresUpdated()->updateOrCreate(
                            ['application_id' => $this->id],
                            [
                                'attched_document'   => $temp->attched_document,
                                'document_type'      => $temp->document_type,
                                'document_extension' => $temp->document_extension,
                                'document_mime_type' => $temp->document_mime_type,
                                'ip_address'         => request()->ip(),
                                'created_by'         => Auth::id(),
                                'updated_at'         => now(),
                            ]
                        );

                        $temp->delete();
                    }
                    $beneficiary->benPaymentDetails()->update([
                        'acc_validated' => 0,
                        'last_accno'    => $newBankAccountNumber,
                        'last_ifsc'     => $newifscode,
                    ]);
                } else {
                    $beneficiary->benPaymentDetails()->update([
                        'acc_validated' => 2,
                    ]);
                }

                if ($typeId == 1411) {
                    $beneficiary->faultyBeneficiaryPersonal()
                        ->where('application_id', $this->id)
                        ->delete();
                }

                BeneficiaryCommonList::where('sourceable_id', $this->id)
                    ->update([
                        'bank_account_number' => $newBankAccountNumber,
                    ]);
                break;

            case 1412: // Minor Mismatch(40% - 89%)
            case 1413: // Minor Mismatch(90% - 100%)
                $beneficiary->failedPaymentDetails()->updateOrCreate([
                    'edited_status' => 2,
                    'failed_type'   => 3,
                    'accno'         => $newBankAccountNumber,
                    'ifsc'          => $newifscode,
                    'updated_at'    => now(),
                ]);
                BeneficiaryCommonList::where('sourceable_id', $this->id)
                    ->update([
                        'bank_account_number' => $newBankAccountNumber,
                    ]);
                break;

            default:
                break;
        }
    }

    public function validateRevert()
    {
        $this->validate([
            'revert_reason_cause_id' => 'required|exists:codemasters,id',
            'revert_reason_remarks'  => 'required|string|max:255',
        ], [
            'revert_reason_cause_id.required' => 'Please select a revert reason.',
            'revert_reason_cause_id.exists'   => 'Invalid revert reason selected.',
            'revert_reason_remarks.required'  => 'Remarks are required.',
            'revert_reason_remarks.max'       => 'Remarks cannot exceed 255 characters.',
        ]);

        $this->dispatch('confirm-revert');
    }
    public function revert()
    {
        // dd('ok');
        DB::beginTransaction();

        try {

            $opType = Codemaster::where('code', 2104)->value('id');
            $previousId = AcceptRejectInfo::where('application_id', $this->id)
                ->where('op_type', $opType)
                ->orderByDesc('id')
                ->value('id');

            $request = AcceptRejectInfo::create([
                'application_id'         => $this->id,
                'beneficiary_id'         => $this->applicantInfo->beneficiary_id ?? null,
                'ip_address'             => request()->ip(),
                'user_id'                => $this->user_id,
                'browser'                => request()->header('User-Agent'),
                'model_name'             => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod(),
                'op_type'                => Codemaster::where('code', 2105)->value('id'),
                'revert_reason_cause_id' => $this->revert_reason_cause_id,
                'revert_reason_remarks'  => $this->revert_reason_remarks,
                'parent_id'              => $previousId,
            ]);

            foreach ($this->page as $item) {
                $item->update([
                    'is_active'             => 1,
                    'new_value'             => null,
                    'next_level_request_id' => -50,
                    'request_id'            => $request->id,
                    'change_type'           => null,
                ]);
            }

            DB::commit();

            session()->flash('success', "Application reverted successfully for the Application ID: {$this->id}");
            return redirect()->route('incomplete.types', ['stage' => 'approver', 'id' => $this->id]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Revert failed for Application ID {$this->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Something went wrong while reverting the application. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    // public function revertVerify()
    // {
    //     // dd('ok');
    //     DB::beginTransaction();

    //     try {
    //         $previousId = AcceptRejectInfo::where('application_id', $this->id)
    //             ->orderByDesc('id')
    //             ->value('id');

    //         $request = AcceptRejectInfo::create([
    //             'application_id'         => $this->id,
    //             'beneficiary_id'         => $this->applicantInfo->beneficiary_id ?? null,
    //             'ip_address'             => request()->ip(),
    //             'user_id'                => $this->user_id,
    //             'browser'                => request()->header('User-Agent'),
    //             'model_name'             => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod(),
    //             'op_type'                => Codemaster::where('code', 2103)->value('id'),
    //             'revert_reason_cause_id' => null,
    //             'revert_reason_remarks'  => null,
    //             'parent_id'              => $previousId,
    //         ]);

    //         $allIssues  = $this->page;
    //         $bankIssues = $allIssues->filter(fn($i) => in_array($i->incomplet_type, ['145', '146', '1411', '1412', '1413']));
    //         $dupbankacc = $bankIssues->contains(fn($i) => $i->incomplet_type == '1411');

    //         foreach ($allIssues as $item) {
    //             $typeCode = $item->incomplet_type ?? null;
    //             if (!$typeCode) continue;

    //             $jsonValue = [];
    //             // dd($this->formData['aadhar_modification'][$item->application_id]);
    //             // Aadhaar related
    //             if (in_array($typeCode, ['141', '149', '1414'])) {

    //                 $jsonValue = [
    //                     'aadhaar_no'     => $this->formData['aadhar_modification'][$item->application_id] ?? null,
    //                     'application_id' => $this->id,
    //                 ];
    //                 // dd($jsonValue );
    //             }
    //             // Mobile related
    //             elseif (in_array($typeCode, ['142', '1410'])) {
    //                 $jsonValue = [
    //                     'mobile_no'      => $this->formData['dup_mobile'][$item->application_id] ?? null,
    //                     'application_id' => $this->id,
    //                 ];
    //             }
    //             // Bank related
    //             elseif (in_array($typeCode, ['145', '146', '1411', '1412', '1413'])) {
    //                 $jsonValue = [
    //                     'ifscode'             => $this->ifscode,
    //                     'bank_account_number' => $this->bank_account_number,
    //                     'confirmbankaccountnumber' => $this->confirmbankaccountnumber,
    //                 ];

    //                 $relatedIssues = $allIssues->whereIn('incomplet_type', ['145', '146', '1411', '1412', '1413']);

    //                 if ($relatedIssues->count() === 1) {
    //                     $isActive    = 1;
    //                     $updateValue = $jsonValue;
    //                 } else {
    //                     if ($typeCode == '1411') {
    //                         $isActive    = 1;
    //                         $updateValue = $jsonValue;
    //                     } else {
    //                         if ($dupbankacc) {
    //                             $isActive    = 0;
    //                             $updateValue = null;
    //                         } else {
    //                             $isActive    = ($this->bank_action == 1 ? 1 : 0);
    //                             $updateValue = $jsonValue;
    //                         }
    //                     }
    //                 }

    //                 $item->update([
    //                     'new_value'             => $updateValue,
    //                     'change_type'           => $this->bank_action ?? null,
    //                     'next_level_request_id' => 1,
    //                     'request_id'            => $request->id,
    //                     'is_active'             => $isActive,
    //                 ]);

    //                 continue;
    //             }

    //             if (!empty($jsonValue)) {
    //                 $item->update([
    //                     'new_value'             => $jsonValue,
    //                     'change_type'           => $this->bank_action ?? null,
    //                     'next_level_request_id' => 1,
    //                     'request_id'            => $request->id,
    //                 ]);
    //             }
    //         }

    //         DB::commit();

    //         session()->flash('success', "Revert details updated and Request sent to approver for Application ID: {$this->id}");
    //         return redirect()->route('incomplete.types', ['stage' => 'revert', 'id' => $this->id]);
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error("Revert Verify failed for Application ID {$this->id}: " . $e->getMessage(), [
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         session()->flash('error', 'Something went wrong while reverting. Please try again.');
    //         return redirect()->back()->withInput();
    //     }
    // }

    private function classifyIssues()
    {
        $aadhaarIssues = [];
        $mobileIssues = [];
        $bankIssues = [];

        $bankPriority = [
            'DUPLICATE BANK ACCOUNT NUMBER',
            'NAME VALIDATION  FAILED IN BANK',
            'ACCOUNT NUMBER VALIDATION  FAILED IN BANK',
            'MINOR MISMATCH(40% - 89%)',
            'MINOR MISMATCH(90% - 100%)',
        ];

        foreach ($this->page as $item) {
            $typeName = $item->incompletType->name;

            if (in_array($typeName, ['PDS MISMATCH', 'NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER'])) {
                $aadhaarIssues[] = $item;
            } elseif (in_array($typeName, ['NO MOBILE NUMBER', 'DUPLICATE MOBILE NUMBER'])) {
                $mobileIssues[] = $item;
            } elseif (in_array($typeName, $bankPriority)) {
                $bankIssues[] = $item;
            }
        }

        // Priority অনুযায়ী sort করা
        $sorted = collect($bankIssues)->sortBy(
            fn($item) => array_search($item->incompletType->name, $bankPriority)
        )->values();

        // ✅ Check করো — duplicate আছে কিনা
        $hasDuplicate = $sorted->contains(fn($item) => $item->incompletType->name === 'DUPLICATE BANK ACCOUNT NUMBER');

        // ✅ যদি duplicate থাকে তাহলে dupAction = 1, না থাকলে null
        $item->dupAction = $hasDuplicate ? 1 : null;

        // ✅ Assign to component properties
        $this->aadhaarIssues = $aadhaarIssues;
        $this->mobileIssues = $mobileIssues;
        $this->sortedBankIssues = $sorted;
    }


    public function render()
    {
        return view('livewire.incomplet-type-page', [
            'applicantInfo' => $this->applicantInfo,
            'aadhaarIssues' => $this->aadhaarIssues,
            'mobileIssues' => $this->mobileIssues,
            'sortedBankIssues' => $this->sortedBankIssues,
            'stage' => $this->stage,
            'revertReasons' => $this->revertReasons,
            'id' => $this->id,
        ]);
    }
}
