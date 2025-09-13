<?php

namespace App\Livewire;

use App\Helpers\ChechDupHelper;
use Livewire\Component;
use App\Models\Codemaster;
use App\Models\AcceptRejectInfo;
use Illuminate\Support\Facades\Crypt;
use App\Models\ApplicantIncompletDeatil;

class IncompletTypePage extends Component
{
    public $id, $page, $stage, $applicantInfo, $formData = [], $revertReasons = [], $user_id, $revert_reason_cause_id, $revert_reason_remarks, $aadhaarIssues = [], $mobileIssues = [], $sortedBankIssues = [], $ifscode, $new_bank_account, $bank_action;

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
                // 1️⃣ Decode: যদি string হয়, decode করো, যদি array হয়, 그대로 use করো
                $decoded = is_string($item->new_value) ? json_decode($item->new_value, true) : $item->new_value;

                // 2️⃣ Aadhaar field
                if (isset($decoded['aadhaar_no'])) {
                    $this->formData['aadhar_modification'][$item->application_id] = $decoded['aadhaar_no'];
                }

                // 3️⃣ Mobile field
                if (isset($decoded['mobile_no'])) {
                    $this->formData['new_mobile'][$item->application_id] = $decoded['mobile_no'];
                }

                // 4️⃣ Bank field
                if (isset($decoded['account_number'])) {
                    $this->new_bank_account = $decoded['account_number']; // edit mode prefill
                }
                if (isset($decoded['ifscode'])) {
                    $this->ifscode = $decoded['ifscode']; // edit mode prefill
                }

                if (isset($item->change_type)) {
                    $this->bank_action = $item->change_type; // এটা dropdown এ selected দেখাবে
                }
            }
        }


        $this->classifyIssues();
    }

    public function recivedupdateddata($data)
    {
        $this->ifscode = $data['ifscode'];
        $this->new_bank_account = $data['bank_account_number'];
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

        // Pre-collect all bank issues to handle is_active logic
        $bankIssues = $this->page->filter(fn($i) => in_array($i->incompletType->name, [
            'DUPLICATE BANK ACCOUNT NUMBER',
            'NAME VALIDATION  FAILED IN BANK',
            'ACCOUNT NUMBER VALIDATION  FAILED IN BANK',
            'MINOR MISMATCH(40% - 89%)',
            'MINOR MISMATCH(90% - 100%)',
        ]));

        $hasDuplicateBank = $bankIssues->contains(fn($i) => $i->incompletType->name === 'DUPLICATE BANK ACCOUNT NUMBER');

        foreach ($this->page as $item) {
            $typeName = $item->incompletType->name ?? null;
            if (!$typeName) continue;

            $jsonValue = [];

            // Aadhaar related
            if (in_array($typeName, ['PDS MISMATCH', 'NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER'])) {
                $jsonValue = [
                    'aadhaar_no'     => $this->formData['aadhar_modification'][$item->application_id] ?? null,
                    'application_id' => $this->id,
                ];
            }

            // Mobile related
            elseif (in_array($typeName, ['NO MOBILE NUMBER', 'DUPLICATE MOBILE NUMBER'])) {
                $jsonValue = [
                    'mobile_no'      => $this->formData['new_mobile'][$item->application_id] ?? null,
                    'application_id' => $this->id,
                ];
            }

            // Bank related
            elseif (in_array($typeName, [
                'DUPLICATE BANK ACCOUNT NUMBER',
                'NAME VALIDATION  FAILED IN BANK',
                'ACCOUNT NUMBER VALIDATION  FAILED IN BANK',
                'MINOR MISMATCH(40% - 89%)',
                'MINOR MISMATCH(90% - 100%)',
            ])) {
                $jsonValue = [
                    'ifscode'        => $this->ifscode,
                    'account_number' => $this->new_bank_account,
                    'bank_action'    => $this->bank_action,
                    'application_id' => $this->id,
                ];

                // Determine is_active
                if ($typeName === 'DUPLICATE BANK ACCOUNT NUMBER') {
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

                continue; // Skip the default update below since we already updated
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
            'op_type'                => Codemaster::where('code', 246)->value('id'),
            'revert_reason_cause_id' => null,
            'revert_reason_remarks'  => null,
            'parent_id'              => $previousId,
        ]);

        foreach ($this->page as $item) {
            $typeName = $item->incompletType->name ?? null;
            // dd(  $typeName);
            if (!$typeName) continue;

            $jsonValue = [];

            // Aadhaar related
            if (in_array($typeName, ['PDS MISMATCH', 'NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER'])) {
                $jsonValue = [
                    'aadhaar_no'     => $this->formData['aadhar_modification'][$item->application_id] ?? null,
                    'application_id' => $this->id,
                ];
            }

            // Mobile related
            elseif (in_array($typeName, ['NO MOBILE NUMBER', 'DUPLICATE MOBILE NUMBER'])) {
                $jsonValue = [
                    'mobile_no'      => $this->formData['new_mobile'][$item->application_id] ?? null,
                    'application_id' => $this->id,
                ];
            }

            // Bank related
            elseif (in_array($typeName, [
                'DUPLICATE BANK ACCOUNT NUMBER',
                'NAME VALIDATION  FAILED IN BANK',
                'ACCOUNT NUMBER VALIDATION  FAILED IN BANK',
                'MINOR MISMATCH(40% - 89%)',
                'MINOR MISMATCH(90% - 100%)',
            ])) {
                $jsonValue = [
                    'ifscode'          => $this->ifscode,
                    'account_number'   => $this->new_bank_account,
                    'bank_action'      => $this->bank_action,
                    'application_id'   => $this->id,
                ];
            }

            if (!empty($jsonValue)) {
                $item->update([
                    'new_value'            => $jsonValue,
                    'change_type'          => $this->bank_action ?? null,
                    'next_level_request_id' => 0,
                    'request_id'           => $request->id,
                ]);
                $this->updateOriginalTable($item);
            }
        }


        session()->flash('success', 'Approve details updated successfully!');
        return redirect()->route('incomplete.types', ['id' => $this->id]);
    }

    protected function updateOriginalTable($item)
    {
        $typeId = $item->incomplet_type_id;
        $newValue = $item->new_value;
        $beneficiary = $item->beneficiaryCommonList;

        switch ($typeId) {
            case 141: // NO AADHAR NUMBER
            case 149: // DUPLICATE AADHAR NUMBER
            case 1414: // PDS Mismatch
                $beneficiary->aadhaar()->update([
                    'encoded_aadhar' => md5($newValue),
                    'aadhaar_number' => Crypt::encryptString($newValue),
                ]);
                break;

            case 142: // NO MOBILE NUMBER
            case 1410: // DUPLICATE MOBILE NUMBER
                $beneficiary->beneficiaryPersonal()->update([
                    'mobile' => $newValue,
                ]);

                if ($typeId == 142) {
                    $beneficiary->faultyBeneficiaryPersonal()->update([
                        'mobile' => $newValue,
                    ]);
                }
                break;

            case 145: // NAME VALIDATION FAILED IN BANK
                $beneficiary->failedPaymentDetails()->update([
                    'acc_validation' => 0,
                ]);
                break;

            case 146: // ACCOUNT NUMBER VALIDATION FAILED IN BANK
            case 1411: // DUPLICATE BANK ACCOUNT NUMBER
                $beneficiary->bank()->update([
                    'bank_account_number' => $newValue,
                ]);

                $beneficiary->faultyBeneficiaryBank()->update([
                    'bank_account_number' => $newValue,
                ]);
                $beneficiary->benPaymentDetails()->update([
                    'bank_account_number' => $newValue,
                ]);
                break;

            case 1412: // Minor Mismatch(40% - 89%)
            case 1413: // Minor Mismatch(90% - 100%)
                $beneficiary->failedPaymentDetails()->update([
                    'mismatch_details' => $newValue,
                ]);
                break;

            default:
                break;
        }
    }

    // public function submit()
    // {
    //     $this->checkduplicate();

    //     // create accept/reject request entry
    //     $request = AcceptRejectInfo::create([
    //         'application_id'         => $this->id,
    //         'beneficiary_id'         => $this->applicantInfo->beneficiary_id ?? null,
    //         'ip_address'             => request()->ip(),
    //         'user_id'                => $this->user_id,
    //         'browser'                => request()->header('User-Agent'),
    //         'model_name'             => 'ApplicantIncompleteDetail',
    //         'op_type'                => Codemaster::where('code', 245)->value('id'),
    //         'revert_reason_cause_id' => null,
    //         'revert_reason_remarks'  => null,
    //         'parent_id'              => null,
    //     ]);

    //     foreach ($this->page as $item) {
    //         $typeName = $item->incompletType->name ?? null;
    //         // dd(  $typeName);
    //         if (!$typeName) continue;

    //         $jsonValue = [];

    //         // Aadhaar related
    //         if (in_array($typeName, ['PDS MISMATCH', 'NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER'])) {
    //             $jsonValue = [
    //                 'aadhaar_no'     => $this->formData['aadhar_modification'][$item->application_id] ?? null,
    //                 'application_id' => $this->id,
    //             ];
    //         }

    //         // Mobile related
    //         elseif (in_array($typeName, ['NO MOBILE NUMBER', 'DUPLICATE MOBILE NUMBER'])) {
    //             $jsonValue = [
    //                 'mobile_no'      => $this->formData['new_mobile'][$item->application_id] ?? null,
    //                 'application_id' => $this->id,
    //             ];
    //         }

    //         // Bank related
    //         elseif (in_array($typeName, [
    //             'DUPLICATE BANK ACCOUNT NUMBER',
    //             'NAME VALIDATION  FAILED IN BANK',
    //             'ACCOUNT NUMBER VALIDATION  FAILED IN BANK',
    //             'MINOR MISMATCH(40% - 89%)',
    //             'MINOR MISMATCH(90% - 100%)',
    //         ])) {
    //             $jsonValue = [
    //                 'ifscode'          => $this->ifscode,
    //                 'account_number'   => $this->new_bank_account,
    //                 'bank_action'      => $this->bank_action,
    //                 'application_id'   => $this->id,
    //             ];
    //         }

    //         if (!empty($jsonValue)) {
    //             $item->update([
    //                 'new_value'            => $jsonValue,
    //                 'change_type'          => $this->bank_action ?? null,
    //                 'next_level_request_id' => 1,
    //                 'request_id'           => $request->id,
    //             ]);
    //         }
    //     }

    //     session()->flash('success', 'Incomplete details updated successfully!');
    //     return redirect()->route('incomplete.types', ['stage' => 'verifier', 'id' => $this->id]);
    // }

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
            $value = $this->applicantInfo?->mobile_no;
        } elseif (
            str_contains($incompleteType, 'DUPLICATE BANK ACCOUNT NUMBER')
            || str_contains($incompleteType, 'NAME VALIDATION  FAILED IN BANK')
            || str_contains($incompleteType, 'ACCOUNT NUMBER VALIDATION  FAILED IN BANK')
            || str_contains($incompleteType, 'MINOR MISMATCH(40% - 89%)')
            || str_contains($incompleteType, 'MINOR MISMATCH(90% - 100%)')
        ) {

            $type = 'bank';
            $value = $this->new_bank_account;
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
