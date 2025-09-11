<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Codemaster;
use App\Models\AcceptRejectInfo;
use App\Models\ApplicantIncompletDeatil;
use App\Models\Ifsccodemaster;
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

    protected $listeners = ['trigger-update' => 'update', 'update-form-data' => 'updateFormData'];

    protected $rules = [
        'formData.aadhar.*'        => 'nullable|digits:12',
        'formData.new_aadhar.*'    => 'nullable|digits:12',
        'formData.new_bank_account.*' => 'nullable|digits_between:9,18',
        'formData.mobile.*'        => 'nullable|digits:10',
        'formData.new_mobile.*'    => 'nullable|digits:10',
        'formData.bank_name.*'     => 'nullable|string|max:255',
        'formData.ifscode.*'       => 'nullable|string|size:11',
        'formData.mismatch_low.*'  => 'nullable|string|max:255',
        'formData.mismatch_high.*' => 'nullable|string|max:255',
        'formData.pds.*'           => 'nullable|digits:12',
        'formData.bank_action.*'   => 'nullable|in:1,2',
    ];

    protected $messages = [
        'formData.aadhar.*.digits' => 'Aadhaar number must be 12 digits.',
        'formData.new_aadhar.*.digits' => 'New Aadhaar number must be 12 digits.',
        'formData.new_bank_account.*.digits_between' => 'Bank account number must be 9–18 digits.',
        'formData.mobile.*.digits' => 'Mobile number must be 10 digits.',
        'formData.new_mobile.*.digits' => 'New mobile number must be 10 digits.',
        'formData.ifscode.*.size' => 'IFSC code must be 11 characters.',
        'formData.pds.*.digits' => 'PDS Aadhaar number must be 12 digits.',
        'formData.bank_action.*.in' => 'Invalid bank action selected.',
    ];

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
            $type = $item->incompletType->name ?? null;
            if (!$type) continue;

            $map = [
                'NO AADHAR NUMBER'                   => 'aadhar',
                'DUPLICATE AADHAR NUMBER'            => 'new_aadhar',
                'DUPLICATE BANK ACCOUNT NUMBER'      => 'new_bank_account',
                'NO MOBILE NUMBER'                   => 'mobile',
                'NAME VALIDATION FAILED IN BANK'     => 'bank_name',
                'ACCOUNT NUMBER VALIDATION FAILED IN BANK' => 'bank_account',
                'DUPLICATE MOBILE NUMBER'            => 'new_mobile',
                'MINOR MISMATCH(40% - 89%)'          => 'mismatch_low',
                'MINOR MISMATCH(90% - 100%)'         => 'mismatch_high',
                'PDS MISMATCH'                       => 'pds',
            ];

            if (isset($map[$type])) {
                if ($item->new_value) {
                    $newValue = json_decode($item->new_value, true);
                    if (in_array($type, ['NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER', 'PDS MISMATCH'])) {
                        $this->formData[$map[$type]][$item->id] = $newValue['new_value'] ? Crypt::decryptString($newValue['new_value']) : '';
                    } elseif (in_array($type, ['DUPLICATE BANK ACCOUNT NUMBER', 'NAME VALIDATION FAILED IN BANK', 'ACCOUNT NUMBER VALIDATION FAILED IN BANK', 'MINOR MISMATCH(40% - 89%)', 'MINOR MISMATCH(90% - 100%)'])) {
                        $this->formData['new_bank_account'][$item->id] = $newValue['new_value']['account'] ?? $item->old_value['account_number'] ?? '';
                        $this->formData['ifscode'][$item->id] = $newValue['new_value']['ifscode'] ?? $item->old_value['ifsc'] ?? '';
                        $this->formData['bank_name'][$item->id] = $newValue['new_value']['bank_name'] ?? $item->old_value['bank_name'] ?? '';
                        $this->formData['bank_action'][$item->id] = $newValue['bank_action'] ?? '';
                    } else {
                        $this->formData[$map[$type]][$item->id] = $newValue['new_value'] ?? '';
                    }
                } else {
                    if (in_array($type, ['DUPLICATE BANK ACCOUNT NUMBER', 'NAME VALIDATION FAILED IN BANK', 'ACCOUNT NUMBER VALIDATION FAILED IN BANK', 'MINOR MISMATCH(40% - 89%)', 'MINOR MISMATCH(90% - 100%)'])) {
                        $this->formData['new_bank_account'][$item->id] = $item->old_value['account_number'] ?? '';
                        $this->formData['ifscode'][$item->id] = $item->old_value['ifsc'] ?? '';
                        $this->formData['bank_name'][$item->id] = $item->old_value['bank_name'] ?? '';
                        $this->formData['bank_action'][$item->id] = '';
                    } else {
                        $this->formData[$map[$type]][$item->id] = $item->old_value ?? '';
                    }
                }
            }
        }
    }

    public function updateFormData($data)
    {
        $id = $data['id'];
        $this->formData['ifscode'][$id] = $data['ifscode'];
        $this->formData['bank_name'][$id] = $data['bank_name'];
        $this->formData['new_bank_account'][$id] = $data['new_bank_account'];
        $this->formData['bank_action'][$id] = $data['bank_action'] ?? '';
    }

    public function update()
    {
        // Validate all form data
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

        $bankIssues = collect($this->page)->filter(function ($item) {
            return in_array($item->incompletType->name, [
                'DUPLICATE BANK ACCOUNT NUMBER',
                'NAME VALIDATION FAILED IN BANK',
                'ACCOUNT NUMBER VALIDATION FAILED IN BANK',
                'MINOR MISMATCH(40% - 89%)',
                'MINOR MISMATCH(90% - 100%)',
            ]);
        });

        $sharedBankData = null;
        $action = '1';
        $changedBankIssueId = null;

        if ($bankIssues->isNotEmpty()) {
            foreach ($bankIssues as $item) {
                $componentName = match ($item->incompletType->name) {
                    'DUPLICATE BANK ACCOUNT NUMBER' => 'incomplete.dup-bank',
                    'NAME VALIDATION FAILED IN BANK' => 'incomplete.bank-name-fail',
                    'ACCOUNT NUMBER VALIDATION FAILED IN BANK' => 'incomplete.bank-account-fail',
                    'MINOR MISMATCH(40% - 89%)' => 'incomplete.mismatch-low',
                    'MINOR MISMATCH(90% - 100%)' => 'incomplete.mismatch-high',
                };
                $componentKey = match ($item->incompletType->name) {
                    'DUPLICATE BANK ACCOUNT NUMBER' => 'dup-' . $item->id,
                    'NAME VALIDATION FAILED IN BANK' => 'name-' . $item->id,
                    'ACCOUNT NUMBER VALIDATION FAILED IN BANK' => 'account-' . $item->id,
                    'MINOR MISMATCH(40% - 89%)' => 'mismatch-low-' . $item->id,
                    'MINOR MISMATCH(90% - 100%)' => 'mismatch-high-' . $item->id,
                };
                $bankComponent = app('livewire')->getInstance($componentName, $componentKey);
                if ($bankComponent->bank_action === '2') {
                    $action = '2';
                    $changedBankIssueId = $item->id;
                    break;
                }
            }

            // If any issue has "CHANGE", validate and set shared bank data
            if ($action === '2' && $changedBankIssueId) {
                $this->validate([
                    'formData.ifscode.' . $changedBankIssueId => 'required|string|size:11',
                    'formData.new_bank_account.' . $changedBankIssueId => 'required|digits_between:9,18',
                ]);

                $ifs = Ifsccodemaster::where('code', $this->formData['ifscode'][$changedBankIssueId])
                    ->where('is_active', 1)
                    ->first();

                if (!$ifs) {
                    $this->addError("formData.ifscode.{$changedBankIssueId}", 'Invalid IFSC code.');
                    return;
                }

                $accountExists = $bankIssues->first()->beneficiaryCommonList
                    ->bank()
                    ->where('bank_account_number', $this->formData['new_bank_account'][$changedBankIssueId])
                    ->where('application_id', '!=', $this->id)
                    ->exists();

                if ($accountExists) {
                    $this->addError("formData.new_bank_account.{$changedBankIssueId}", 'This bank account number already exists.');
                    return;
                }

                $sharedBankData = [
                    'ifscode' => $this->formData['ifscode'][$changedBankIssueId],
                    'bank_name' => $ifs->bank->name ?? '',
                    'branch_name' => $ifs->branch ?? '',
                    'account' => $this->formData['new_bank_account'][$changedBankIssueId],
                ];
            }

            // Update all bank issues with the same data
            foreach ($bankIssues as $item) {
                $bankAction = $this->formData['bank_action'][$item->id] ?? '';
                $jsonData = [
                    'old_value' => $item->old_value ?? null,
                    'new_value' => null,
                    'change_type' => $bankAction ?: '1', // Default to '1' if empty
                ];

                if ($action === '2' && $bankAction === '2') {
                    $jsonData['new_value'] = $sharedBankData;
                } else {
                    $jsonData['new_value'] = [
                        'ifscode' => $item->old_value['ifsc'] ?? '',
                        'bank_name' => $item->old_value['bank_name'] ?? '',
                        'branch_name' => $item->old_value['branch_name'] ?? '',
                        'account' => $item->old_value['account_number'] ?? '',
                    ];
                }

                $item->update([
                    'new_value' => json_encode($jsonData),
                    'change_type' => $bankAction ? (int)$bankAction : null,
                    'next_level_request_id' => 1,
                    'request_id' => $request->id,
                ]);
            }
        }

        foreach ($this->page as $item) {
            $type = $item->incompletType->name ?? null;
            if (!$type || in_array($type, [
                'DUPLICATE BANK ACCOUNT NUMBER',
                'NAME VALIDATION FAILED IN BANK',
                'ACCOUNT NUMBER VALIDATION FAILED IN BANK',
                'MINOR MISMATCH(40% - 89%)',
                'MINOR MISMATCH(90% - 100%)',
            ])) {
                continue;
            }

            $jsonData = [
                'old_value' => $item->old_value ?? null,
                'new_value' => null,
            ];

            $map = [
                'NO AADHAR NUMBER'                   => 'aadhar',
                'DUPLICATE AADHAR NUMBER'            => 'new_aadhar',
                'NO MOBILE NUMBER'                   => 'mobile',
                'DUPLICATE MOBILE NUMBER'            => 'new_mobile',
                'PDS MISMATCH'                       => 'pds',
            ];

            $newValue = $this->formData[$map[$type]][$item->id] ?? null;

            if (in_array($type, ['NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER', 'PDS MISMATCH']) && $newValue) {
                $exists = $item->beneficiaryCommonList->aadhaar
                    ->aadhaar()
                    ->where('encoded_aadhar', md5($newValue))
                    ->where('application_id', '!=', $this->id)
                    ->exists();

                if ($exists) {
                    $this->addError("formData.{$map[$type]}.{$item->id}", 'This Aadhaar number already exists.');
                    continue;
                }
                $jsonData['new_value'] = Crypt::encryptString($newValue);
            } elseif (in_array($type, ['NO MOBILE NUMBER', 'DUPLICATE MOBILE NUMBER']) && $newValue) {
                $exists = $item->beneficiaryCommonList->beneficiaryPersonal
                    ->where('mobile_no', $newValue)
                    ->where('application_id', '!=', $this->id)
                    ->exists();

                if ($exists) {
                    $this->addError("formData.{$map[$type]}.{$item->id}", 'This mobile number already exists.');
                    continue;
                }
                $jsonData['new_value'] = $newValue;
            } else {
                $jsonData['new_value'] = $newValue ?? $item->old_value;
            }

            $item->update([
                'new_value' => json_encode($jsonData),
                'change_type' => null,
                'next_level_request_id' => 1,
                'request_id' => $request->id,
            ]);
        }

        session()->flash('success', 'Incomplete details updated successfully!');
        return redirect()->route('incomplete.types', [
            'stage' => 'verifier',
            'id' => $this->id,
        ]);
    }

    public function render()
    {
        return view('livewire.incomplet-type-page', [
            'page' => $this->page,
            'applicantInfo' => $this->applicantInfo,
        ]);
    }
}
