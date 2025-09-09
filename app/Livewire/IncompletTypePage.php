<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Codemaster;
use App\Models\AcceptRejectInfo;
use App\Models\ApplicantIncompletDeatil;
use Illuminate\Support\Facades\Crypt;

class IncompletTypePage extends Component
{
    public $id;
    public $page;
    public $stage;
    public $applicantInfo;
    public $formData = [];
    public $revertReasons = [];
    public $user_id;

    public $revert_reason_cause_id;
    public $revert_reason_remarks;

    protected $rules = [
        'formData.aadhar_modification.*' => 'nullable|digits:12',
        'formData.new_bank_account.*'    => 'nullable|digits_between:9,18',
        'formData.mobile.*'              => 'nullable|digits:10',
        'formData.new_mobile.*'          => 'nullable|digits:10',
        'formData.bank_name.*'           => 'nullable|string|max:255',
        'formData.bank_account.*'        => 'nullable|digits_between:9,18',
        'formData.mismatch_low.*'        => 'nullable|string|max:255',
        'formData.mismatch_high.*'       => 'nullable|string|max:255',
        'formData.pds.*'                 => 'nullable|string|max:255',
    ];

    protected $messages = [
        'formData.aadhar_modification.*.digits' => 'Aadhaar number must be 12 digits.',
        'formData.mobile.*.digits'              => 'Mobile number must be 10 digits.',
        'formData.new_bank_account.*.digits_between' => 'Bank account must be 9–18 digits.',
    ];

    public function mount($id)
    {
        $this->stage = request()->query('stage');
        $select_lgd  = session('lgd_session');
        $this->user_id = Crypt::decryptString($select_lgd['role_id']);
        $this->id = $id;
        $revertReasons       = Codemaster::getIdByCode(12);
        $this->revertReasons = Codemaster::where('parent_id', $revertReasons)->get();

        $this->page = ApplicantIncompletDeatil::where('application_id', $id)
            ->with([
                'incompletType',
                'beneficiaryCommonList.enclosures',
                'beneficiaryCommonList.aadhaar',
                'beneficiaryCommonList.bank.ifscbranch.bankMaster',
                'beneficiaryCommonList.beneficiaryPersonal.father',
                'beneficiaryCommonList.panchayat',
                'beneficiaryCommonList.ward',
            ])->get();

            // dd($this->page);
        $this->applicantInfo = $this->page->first()?->beneficiaryCommonList;

        foreach ($this->page as $item) {
            $type = $item->incompletType->name ?? null;
            if (!$type) continue;

            $map = $this->getFieldMap();

            if (isset($map[$type]) && $item->new_value) {
                if ($map[$type] === 'aadhar_modification') {
                    $this->formData['aadhar_modification'][$item->id] = Crypt::decryptString($item->new_value);
                } else {
                    $this->formData[$map[$type]][$item->id] = $item->new_value;
                }
            }
        }
    }

    public function submit()
    {
        $this->validate();

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

        $this->processItems($request, 1);

        session()->flash('success', 'Incomplete details updated successfully!');
        return redirect()->route('incomplete.types', ['stage' => 'verifier', 'id' => $this->id]);
    }

    public function approve()
    {
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

        $this->processItems($request, 2, true);

        session()->flash('success', 'Approve details updated successfully!');
        return redirect()->route('incomplete.types', ['id' => $this->id]);
    }

    public function revert()
    {
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
            'op_type'                => Codemaster::where('code', 247)->value('id'),
            'revert_reason_cause_id' => $this->revert_reason_cause_id,
            'revert_reason_remarks'  => $this->revert_reason_remarks,
            'parent_id'              => $previousId,
        ]);

        foreach ($this->page as $item) {
            $item->update([
                'next_level_request_id' => -50,
                'request_id'            => $request->id,
            ]);
        }

        session()->flash('success', 'Application reverted successfully!');
        return redirect()->route('incomplete.types', ['stage' => 'approver', 'id' => $this->id]);
    }

    public function revertVerify()
    {
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
            'op_type'                => Codemaster::where('code', 245)->value('id'),
            'revert_reason_cause_id' => null,
            'revert_reason_remarks'  => null,
            'parent_id'              => $previousId,
        ]);

        $this->processItems($request, 1);

        session()->flash('success', 'Revert details updated successfully!');
        return redirect()->route('incomplete.types', ['stage' => 'revert', 'id' => $this->id]);
    }

    protected function processItems($request, $nextLevel, $approve = false)
    {
        foreach ($this->page as $item) {
            $type = $item->incompletType->name ?? null;
            if (!$type) continue;

            $map = $this->getFieldMap();
            $newValue = null;

            if (isset($map[$type]) && isset($this->formData[$map[$type]][$item->id])) {
                $newValue = $this->formData[$map[$type]][$item->id];
            }

            if ($newValue) {
                if ($map[$type] === 'aadhar_modification') {
                    $exists = $item->beneficiaryCommonList
                        ->aadhaar()
                        ->where('encoded_aadhar', md5($newValue))
                        ->exists();

                    if ($exists) {
                        $this->addError("formData.aadhar_modification.{$item->id}", "This Aadhaar already exists in encoded form!");
                        continue;
                    }
                    $item->new_value = Crypt::encryptString($newValue);
                } elseif ($map[$type] === 'new_bank_account' || $map[$type] === 'bank_account') {
                    $exists = $item->beneficiaryCommonList
                        ->bank()
                        ->where('bank_account_number', $newValue)
                        ->exists();

                    if ($exists) {
                        $this->addError("formData.bank_account.{$item->id}", "This Bank Account already exists!");
                        continue;
                    }
                    $item->new_value = $newValue;
                } else {
                    $item->new_value = $newValue;
                }

                $item->update([
                    'new_value'             => $item->new_value,
                    'next_level_request_id' => $nextLevel,
                    'request_id'            => $request->id,
                ]);

                if ($approve) {
                    $this->updateOriginalTable($item);
                }
            }
        }
    }

    protected function getFieldMap()
    {
        return [
            'NO AADHAR NUMBER'                   => 'aadhar_modification',
            'DUPLICATE AADHAR NUMBER'            => 'aadhar_modification',
            'PDS MISMATCH'                       => 'aadhar_modification',
            'DUPLICATE BANK ACCOUNT NUMBER'      => 'new_bank_account',
            'NO MOBILE NUMBER'                   => 'mobile',
            'NAME VALIDATION  FAILED IN BANK'    => 'bank_name',
            'ACCOUNT NUMBER VALIDATION  FAILED IN BANK' => 'bank_account',
            'DUPLICATE MOBILE NUMBER'            => 'new_mobile',
            'MINOR MISMATCH(40% - 89%)'          => 'mismatch_low',
            'MINOR MISMATCH(90% - 100%)'         => 'mismatch_high',
        ];
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
                if ($beneficiary->aadhaar) {
                    $beneficiary->aadhaar()->update([
                        'encoded_aadhar' => md5($newValue),
                        'aadhaar_number' => Crypt::encryptString($newValue),
                    ]);
                }
                break;

            case 142: // NO MOBILE NUMBER
            case 1410: // DUPLICATE MOBILE NUMBER
                if ($beneficiary->beneficiaryPersonal) {
                    $beneficiary->beneficiaryPersonal()->update([
                        'mobile' => $newValue,
                    ]);
                }
                if ($typeId == 142 && $beneficiary->faultyBeneficiaryPersonal) {
                    $beneficiary->faultyBeneficiaryPersonal()->update([
                        'mobile' => $newValue,
                    ]);
                }
                break;

            case 145: // NAME VALIDATION FAILED IN BANK
                if ($beneficiary->failedPaymentDetails) {
                    $beneficiary->failedPaymentDetails()->update([
                        'account_holder_name' => $newValue,
                    ]);
                }
                break;

            case 146: // ACCOUNT NUMBER VALIDATION FAILED IN BANK
            case 1411: // DUPLICATE BANK ACCOUNT NUMBER
                if ($beneficiary->bank) {
                    $beneficiary->bank()->update([
                        'bank_account_number' => $newValue,
                    ]);
                }
                if ($beneficiary->faultyBeneficiaryBank) {
                    $beneficiary->faultyBeneficiaryBank()->update([
                        'bank_account_number' => $newValue,
                    ]);
                }
                if ($beneficiary->benPaymentDetails) {
                    $beneficiary->benPaymentDetails()->update([
                        'bank_account_number' => $newValue,
                    ]);
                }
                break;

            case 1412: // Minor Mismatch(40% - 89%)
            case 1413: // Minor Mismatch(90% - 100%)
                if ($beneficiary->failedPaymentDetails) {
                    $beneficiary->failedPaymentDetails()->update([
                        'mismatch_details' => $newValue,
                    ]);
                }
                break;

            default:
                break;
        }
    }

    public function render()
    {
        return view('livewire.incomplet-type-page', [
            'page'          => $this->page,
            'applicantInfo' => $this->applicantInfo,
        ]);
    }
}
