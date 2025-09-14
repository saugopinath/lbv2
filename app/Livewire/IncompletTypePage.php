<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Codemaster;
use App\Helpers\ChechDupHelper;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryCommonList;
use Illuminate\Support\Facades\Crypt;
use App\Models\BeneficiaryTemEnclosure;
use App\Models\ApplicantIncompletDeatil;

class IncompletTypePage extends Component
{
    public $id, $page, $stage, $applicantInfo, $formData = [], $revertReasons = [], $user_id, $revert_reason_cause_id, $revert_reason_remarks, $aadhaarIssues = [], $mobileIssues = [], $sortedBankIssues = [], $ifscode, $bank_account_number, $bank_action;

    protected $listeners = ['trigger-update' => 'recivedupdateddata'];

    public function mount($id)
    {
        $this->stage = request()->query('stage');
        $this->id = $id;

        $select_lgd = session('lgd_session');
        $this->user_id = Crypt::decryptString($select_lgd['role_id']);

        $this->revertReasons = Codemaster::where('parent_id', Codemaster::getIdByCode(12))->get();

        $this->page = ApplicantIncompletDeatil::where('application_id', $id)
            ->with([
                'incompletType',
                'beneficiaryCommonList.enclosures',
                'beneficiaryCommonList.aadhaar',
                'beneficiaryCommonList.bank',
                'beneficiaryCommonList.beneficiaryPersonal.father',
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
                    $this->formData['new_mobile'][$item->application_id] = $decoded['mobile_no'];
                }
            }
        }

        $this->classifyIssues();
    }

    public function recivedupdateddata($data)
    {
        $this->ifscode = $data['ifscode'];
        $this->bank_account_number = $data['bank_account_number'];
        $this->bank_action = $data['bank_action'];
    }


    public function submit()
    {
        $this->checkduplicate();

        // create accept/reject request entry
        $request = AcceptRejectInfo::create([
            'application_id'         => $this->id,
            'beneficiary_id'         => $this->applicantInfo->beneficiary_id ?? null,
            'ip_address'             => request()->ip(),
            'user_id'                => $this->user_id,
            'browser'                => request()->header('User-Agent'),
            'model_name'             => 'ApplicantIncompleteDetail',
            'op_type'                => Codemaster::where('code', 245)->value('id'),
            'revert_reason_cause_id' => null,
            'revert_reason_remarks'  => null,
            'parent_id'              => null,
        ]);

        $bankIssues = $this->page->filter(fn($i) => in_array($i->incomplet_type, ['145', '146', '1411', '1412', '1413']));


        $hasDuplicateBank = $bankIssues->contains(fn($i) => $i->incomplet_type == '1411');

        foreach ($this->page as $item) {
            $typeCode = $item->incomplet_type ?? null;
            if (!$typeCode) continue;

            $jsonValue = [];

            // Aadhaar related
            if (in_array($typeCode, ['141', '149', '1414'])) {
                $jsonValue = [
                    'aadhaar_no'     => $this->formData['aadhar_modification'][$item->application_id] ?? null,
                    'application_id' => $this->id,
                ];
            }

            // Mobile related
            elseif (in_array($typeCode, ['142', '1410'])) {
                $jsonValue = [
                    'mobile_no'      => $this->formData['new_mobile'][$item->application_id] ?? null,
                    'application_id' => $this->id,
                ];
            }

            // Bank related
            elseif (in_array($typeCode, ['145', '146', '1411', '1412', '1413'])) {
                $jsonValue = [
                    'ifscode'        => $this->ifscode,
                    'bank_account_number' => $this->bank_account_number,
                ];

                if ($typeCode == '1411') {
                    $isActive = 1;
                } else {

                    $isActive = $hasDuplicateBank ? 0 : ($this->bank_action == 1 ? 1 : 0);
                }

                $item->update([
                    'new_value'             => $jsonValue,
                    'change_type'           => $this->bank_action ?? null,
                    'next_level_request_id' => 1,
                    'request_id'            => $request->id,
                    'is_active'             => $isActive,
                ]);

                continue;
            }

            if (!empty($jsonValue)) {
                $item->update([
                    'new_value'             => $jsonValue,
                    'change_type'           => $this->bank_action ?? null,
                    'next_level_request_id' => 1,
                    'request_id'            => $request->id,
                ]);
            }
        }

        session()->flash('success', 'Incomplete details updated successfully!');
        return redirect()->route('incomplete.types', ['stage' => 'verifier', 'id' => $this->id]);
    }


    public function approve()
    {
        // dd('ok');
        $previousId = AcceptRejectInfo::where('application_id', $this->id)
            ->orderByDesc('id')
            ->value('id');

        $request = AcceptRejectInfo::create([
            'application_id'         => $this->id,
            'beneficiary_id'         => $this->applicantInfo->beneficiary_id ?? null,
            'ip_address'             => request()->ip(),
            'user_id'                => $this->user_id,
            'browser'                => request()->header('User-Agent'),
            'model_name'             => 'ApplicantIncompleteDetail',
            'op_type'                => Codemaster::where('code', operator: 246)->value('id'),
            'revert_reason_cause_id' => null,
            'revert_reason_remarks'  => null,
            'parent_id'              => $previousId,
        ]);
        // dd($this->page);
        foreach ($this->page as $item) {

            $typeName = $item->incompletType->name ?? null;
            $typeId = $item->incomplet_type;
            // dd(  $typeName);
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
                    'mobile_no'      => $this->formData['new_mobile'][$item->application_id] ?? null,
                    'application_id' => $this->id,
                ];
            }

            // Bank related
            elseif (in_array($typeId, ['145', '146', '1411', '1412', '1413'])) {
                $jsonValue = [
                    'ifscode'          => $this->ifscode,
                    'bank_account_number'   => $this->bank_account_number,
                    'bank_action'      => $this->bank_action,
                    'application_id'   => $this->id,
                ];
            }

            if (!empty($jsonValue)) {
                $item->update([
                    'next_level_request_id' => 0,
                    'request_id'           => $request->id,
                ]);
            }
            $this->updateOriginalTable($item);
        }



        session()->flash('success', 'Approve details updated successfully!');
        return redirect()->route('incomplete.types', ['id' => $this->id]);
    }

    protected function updateOriginalTable($item)
    {
        $typeId = $item->incomplet_type;

        if (in_array($typeId, ['141', '149', '1414'])) {
            $newAadhaar = $item->new_value['aadhaar_no'];
        } elseif (in_array($typeId, ['142', '1410'])) {
            $newMobile = $item->new_value['mobile_no'];
        } else {
            $bank_action = $item->change_type;
            $newifscode = $item->new_value['ifscode'];
            $newBankAccountNumber = $item->new_value['bank_account_number'];
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
                        'aadhar_hash' => md5($newAadhaar),
                        'created_by'  => 1,
                    ]
                );

                BeneficiaryCommonList::where('sourceable_id', $this->id)
                    ->update([
                        'encoded_aadhar' => Crypt::encryptString($newAadhaar),
                    ]);
                break;

            case 142: // NO MOBILE NUMBER
            case 1410: // DUPLICATE MOBILE NUMBER
                $beneficiary->beneficiaryPersonal()->updateOrCreate(
                    ['application_id' => $this->id], // condition
                    [
                        'mobile_no'   => $newMobile,
                        'created_by'  => 1,
                        'is_faulty'   => true,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]
                );

                $beneficiary->faultyBeneficiaryPersonal()
                    ->where('application_id', $this->id)
                    ->delete();

                // if ($typeId == 142) {
                //     $beneficiary->faultyBeneficiaryPersonal()->updateOrCreate(
                //         ['application_id' => $this->id], // condition
                //         [
                //             'mobile_no'  => $newMobile,
                //             'created_by' => 1,
                //         ]
                //     );
                // }

                BeneficiaryCommonList::where('sourceable_id', $this->id)
                    ->update([
                        'mobile_no' => $newMobile,
                    ]);
                break;


            case 145: // NAME VALIDATION FAILED IN BANK
                $beneficiary->failedPaymentDetails()->update([
                    'acc_validated' => 2,
                ]);

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
                        'created_by'         => 1,
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
                                'updated_at'         => now(),
                            ]
                        );

                        $temp->delete();
                    }
                }

                $beneficiary->faultyBeneficiaryPersonal()
                    ->where('application_id', $this->id)
                    ->delete();

                $beneficiary->benPaymentDetails()->update([
                    'acc_validated' => 2,
                    'ben_status'    => 0,
                ]);

                BeneficiaryCommonList::where('sourceable_id', $this->id)
                    ->update([
                        'bank_account_number' => $newBankAccountNumber,
                    ]);
                break;

            case 1412: // Minor Mismatch(40% - 89%)
            case 1413: // Minor Mismatch(90% - 100%)
                $beneficiary->failedPaymentDetails()->update([
                    'acc_validated' => 2,
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


    public function checkduplicate()
    {
        $incompleteType = $this->page->first()->incompletType->name ?? null;

        if (!$incompleteType) {
            return true;
        }

        if (
            str_contains($incompleteType, 'DUPLICATE AADHAR NUMBER')
            || str_contains($incompleteType, 'NO AADHAR NUMBER')
            || str_contains($incompleteType, 'PDS MISMATCH')
        ) {

            $type = 'aadhaar';
            $value = $this->applicantInfo?->aadhaar?->aadhaar_no;
        } elseif (
            str_contains($incompleteType, 'NO MOBILE NUMBER')
            || str_contains($incompleteType, 'DUPLICATE MOBILE NUMBER')
        ) {

            $type = 'mobile';
            // dd($type);
            $value = $this->applicantInfo?->mobile_no;
            // dd($value);
        } elseif (
            str_contains($incompleteType, 'DUPLICATE BANK ACCOUNT NUMBER')
            || str_contains($incompleteType, 'NAME VALIDATION  FAILED IN BANK')
            || str_contains($incompleteType, 'ACCOUNT NUMBER VALIDATION  FAILED IN BANK')
            || str_contains($incompleteType, 'MINOR MISMATCH(40% - 89%)')
            || str_contains($incompleteType, 'MINOR MISMATCH(90% - 100%)')
        ) {

            $type = 'bank';
            $value = $this->bank_account_number;
        } else {
            return true;
        }

        $result = ChechDupHelper::checkDuplicate($type, $value ?? '', $incompleteType);

        if ($result !== true) {
            session()->flash('error', $result);
            throw new \Exception($result);
        }

        return true;
    }


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

        $this->aadhaarIssues = $aadhaarIssues;
        $this->mobileIssues = $mobileIssues;

        $this->sortedBankIssues = collect($bankIssues)->sortBy(
            fn($item) => array_search($item->incompletType->name, $bankPriority)
        )->values();
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
