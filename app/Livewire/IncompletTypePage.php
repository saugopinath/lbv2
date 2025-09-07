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
        'formData.aadhar.*'        => 'nullable|digits:12',
        'formData.new_aadhar.*'    => 'nullable|digits:12',
        'formData.new_bank_account.*' => 'nullable|digits_between:9,18',
        'formData.mobile.*'        => 'nullable|digits:10',
        'formData.new_mobile.*'    => 'nullable|digits:10',
        'formData.bank_name.*'     => 'nullable|string|max:255',
        'formData.bank_account.*'  => 'nullable|digits_between:9,18',
        'formData.mismatch_low.*'  => 'nullable|string|max:255',
        'formData.mismatch_high.*' => 'nullable|string|max:255',
        'formData.pds.*'           => 'nullable|string|max:255',
    ];

    protected $messages = [
        'formData.aadhar.*.digits' => 'Aadhaar number must be 12 digits.',
        'formData.mobile.*.digits' => 'Mobile number must be 10 digits.',
        'formData.new_bank_account.*.digits_between' => 'Bank account must be 9–18 digits.',
    ];

    public function mount($id)
    {
        $this->stage = request()->query('stage');

        $select_lgd = session('lgd_session');

        $this->user_id = Crypt::decryptString($select_lgd['role_id']);

        $this->id = $id;

        $revertReasons = Codemaster::getIdByCode(12);
        $this->revertReasons = Codemaster::where('parent_id', $revertReasons)->get();

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
            $type = $item->incompletType->name ?? null;
            if (!$type) continue;

            $map = [
                'NO AADHAR NUMBER'                   => 'aadhar',
                'DUPLICATE AADHAR NUMBER'            => 'new_aadhar',
                'DUPLICATE BANK ACCOUNT NUMBER'      => 'new_bank_account',
                'NO MOBILE NUMBER'                   => 'mobile',
                'NAME VALIDATION  FAILED IN BANK'    => 'bank_name',
                'ACCOUNT NUMBER VALIDATION  FAILED IN BANK' => 'bank_account',
                'DUPLICATE MOBILE NUMBER'            => 'new_mobile',
                'MINOR MISMATCH(40% - 89%)'          => 'mismatch_low',
                'MINOR MISMATCH(90% - 100%)'         => 'mismatch_high',
                'PDS MISMATCH'                       => 'pds',
            ];

            if (isset($map[$type]) && $item->new_value) {
                if (in_array($type, ['NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER', 'PDS MISMATCH'])) {
                    $this->formData[$map[$type]][$item->id] = Crypt::decryptString($item->new_value);
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

        foreach ($this->page as $item) {
            $type = $item->incompletType->name ?? null;
            if (!$type) continue;

            $newValue = null;

            $map = [
                'NO AADHAR NUMBER'                   => 'aadhar',
                'DUPLICATE AADHAR NUMBER'            => 'new_aadhar',
                'DUPLICATE BANK ACCOUNT NUMBER'      => 'new_bank_account',
                'NO MOBILE NUMBER'                   => 'mobile',
                'NAME VALIDATION  FAILED IN BANK'    => 'bank_name',
                'ACCOUNT NUMBER VALIDATION  FAILED IN BANK' => 'bank_account',
                'DUPLICATE MOBILE NUMBER'            => 'new_mobile',
                'MINOR MISMATCH(40% - 89%)'          => 'mismatch_low',
                'MINOR MISMATCH(90% - 100%)'         => 'mismatch_high',
                'PDS MISMATCH'                       => 'pds',
            ];

            if (isset($map[$type]) && isset($this->formData[$map[$type]][$item->id])) {
                $newValue = $this->formData[$map[$type]][$item->id];
            }

            if ($newValue) {
                if (in_array($type, ['NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER', 'PDS MISMATCH'])) {
                    $exists = $item->beneficiaryCommonList
                        ->aadhaar()
                        ->where('encoded_aadhar', md5($newValue))
                        ->exists();

                    if ($exists) {
                        $this->addError("formData.aadhar.{$item->id}", "This Aadhaar already exists in encoded form!");
                        continue;
                    }

                    $item->new_value = Crypt::encryptString($newValue);
                } elseif (in_array($type, ['DUPLICATE BANK ACCOUNT NUMBER', 'ACCOUNT NUMBER VALIDATION  FAILED IN BANK'])) {
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
                    'next_level_request_id' => 1,
                    'request_id'            => $request->id,
                ]);
                $this->updateOriginalTable($item);
            }
        }

        session()->flash('success', 'Incomplete details updated successfully!');
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
                    'account_holder_name' => $newValue,
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

        foreach ($this->page as $item) {
            $type = $item->incompletType->name ?? null;
            if (!$type) continue;

            $newValue = null;

            $map = [
                'NO AADHAR NUMBER'                   => 'aadhar',
                'DUPLICATE AADHAR NUMBER'            => 'new_aadhar',
                'DUPLICATE BANK ACCOUNT NUMBER'      => 'new_bank_account',
                'NO MOBILE NUMBER'                   => 'mobile',
                'NAME VALIDATION  FAILED IN BANK'    => 'bank_name',
                'ACCOUNT NUMBER VALIDATION  FAILED IN BANK' => 'bank_account',
                'DUPLICATE MOBILE NUMBER'            => 'new_mobile',
                'MINOR MISMATCH(40% - 89%)'          => 'mismatch_low',
                'MINOR MISMATCH(90% - 100%)'         => 'mismatch_high',
                'PDS MISMATCH'                       => 'pds',
            ];

            if (isset($map[$type]) && isset($this->formData[$map[$type]][$item->id])) {
                $newValue = $this->formData[$map[$type]][$item->id];
            }

            if ($newValue) {
                if (in_array($type, ['NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER', 'PDS MISMATCH'])) {
                    $exists = $item->beneficiaryCommonList
                        ->aadhaar()
                        ->where('encoded_aadhar', md5($newValue))
                        ->exists();

                    if ($exists) {
                        $this->addError("formData.aadhar.{$item->id}", "This Aadhaar already exists in encoded form!");
                        continue;
                    }

                    $item->new_value = Crypt::encryptString($newValue);
                } elseif (in_array($type, ['DUPLICATE BANK ACCOUNT NUMBER', 'ACCOUNT NUMBER VALIDATION  FAILED IN BANK'])) {
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
                    'next_level_request_id' => 2,
                    'request_id'            => $request->id,
                ]);
            }
        }

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
        return redirect()->route('incomplete.types', ['id' => $this->id]);
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

        foreach ($this->page as $item) {
            $type = $item->incompletType->name ?? null;
            if (!$type) continue;

            $newValue = null;

            $map = [
                'NO AADHAR NUMBER'                   => 'aadhar',
                'DUPLICATE AADHAR NUMBER'            => 'new_aadhar',
                'DUPLICATE BANK ACCOUNT NUMBER'      => 'new_bank_account',
                'NO MOBILE NUMBER'                   => 'mobile',
                'NAME VALIDATION  FAILED IN BANK'    => 'bank_name',
                'ACCOUNT NUMBER VALIDATION  FAILED IN BANK' => 'bank_account',
                'DUPLICATE MOBILE NUMBER'            => 'new_mobile',
                'MINOR MISMATCH(40% - 89%)'          => 'mismatch_low',
                'MINOR MISMATCH(90% - 100%)'         => 'mismatch_high',
                'PDS MISMATCH'                       => 'pds',
            ];

            if (isset($map[$type]) && isset($this->formData[$map[$type]][$item->id])) {
                $newValue = $this->formData[$map[$type]][$item->id];
            }

            if ($newValue) {
                if (in_array($type, ['NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER', 'PDS MISMATCH'])) {
                    $exists = $item->beneficiaryCommonList
                        ->aadhaar()
                        ->where('encoded_aadhar', md5($newValue))
                        ->exists();

                    if ($exists) {
                        $this->addError("formData.aadhar.{$item->id}", "This Aadhaar already exists in encoded form!");
                        continue;
                    }

                    $item->new_value = Crypt::encryptString($newValue);
                } elseif (in_array($type, ['DUPLICATE BANK ACCOUNT NUMBER', 'ACCOUNT NUMBER VALIDATION  FAILED IN BANK'])) {
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
                    'next_level_request_id' => 1,
                    'request_id'            => $request->id,
                ]);
            }
        }

        session()->flash('success', 'Revert details updated successfully!');
        return redirect()->route('incomplete.types', ['id' => $this->id]);
    }

    public function render()
    {
        return view('livewire.incomplet-type-page', [
            'page' => $this->page,
            'applicantInfo' => $this->applicantInfo,
        ]);
    }
}
