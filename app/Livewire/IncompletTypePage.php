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
    protected $listeners = ['trigger-update' => 'update'];
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

    public function update()
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

        $map = [
            'NO AADHAR NUMBER'                          => 'aadhar',
            'DUPLICATE AADHAR NUMBER'                   => 'new_aadhar',
            'DUPLICATE BANK ACCOUNT NUMBER'             => 'new_bank_account',
            'NO MOBILE NUMBER'                          => 'mobile',
            'NAME VALIDATION  FAILED IN BANK'           => 'bank_name',
            'ACCOUNT NUMBER VALIDATION  FAILED IN BANK' => 'bank_account',
            'DUPLICATE MOBILE NUMBER'                   => 'new_mobile',
            'MINOR MISMATCH(40% - 89%)'                 => 'mismatch_low',
            'MINOR MISMATCH(90% - 100%)'                => 'mismatch_high',
            'PDS MISMATCH'                              => 'pds',
        ];

        $changedBankComponent = null;
        $action = '1';
        $sharedBankData = null;
        $bankIssues = collect($this->page)->filter(fn($item) => in_array($item->incompletType->name, [
            'DUPLICATE BANK ACCOUNT NUMBER',
            'NAME VALIDATION  FAILED IN BANK',
            'ACCOUNT NUMBER VALIDATION  FAILED IN BANK',
            'MINOR MISMATCH(40% - 89%)',
            'MINOR MISMATCH(90% - 100%)',
        ]));

        if ($bankIssues->isNotEmpty()) {
            // foreach ($bankIssues as $item) {
            //     $componentName = match ($item->incompletType->name) {
            //         'DUPLICATE BANK ACCOUNT NUMBER' => 'incomplete.dup-bank',
            //         'NAME VALIDATION FAILED IN BANK' => 'incomplete.bank-name-fail',
            //         'ACCOUNT NUMBER VALIDATION FAILED IN BANK' => 'incomplete.bank-account-fail',
            //         'MINOR MISMATCH(40% - 89%)' => 'incomplete.mismatch-low',
            //         'MINOR MISMATCH(90% - 100%)' => 'incomplete.mismatch-high',
            //     };
            //     $componentKey = match ($item->incompletType->name) {
            //         'DUPLICATE BANK ACCOUNT NUMBER' => 'dup-' . $item->id,
            //         'NAME VALIDATION FAILED IN BANK' => 'name-' . $item->id,
            //         'ACCOUNT NUMBER VALIDATION FAILED IN BANK' => 'account-' . $item->id,
            //         'MINOR MISMATCH(40% - 89%)' => 'mismatch-low-' . $item->id,
            //         'MINOR MISMATCH(90% - 100%)' => 'mismatch-high-' . $item->id,
            //     };
            //     $bankComponent = app('livewire')->getInstance($componentName, $componentKey);
            //     if ($bankComponent->bank_action === '2') {
            //         $changedBankComponent = $bankComponent;
            //         $action = '2';
            //         break;
            //     }
            // }

    foreach ($bankIssues as $item) {
        $action = $this->formData['bank_action'][$item->id] ?? null;

        if ($action === '2') {
            $changedBankComponent = $item;
            $action = '2';
            break;
        }
    }



            if ($changedBankComponent) {
                if (empty($changedBankComponent->ifscode) || strlen($changedBankComponent->ifscode) !== 11) {
                    $changedBankComponent->addError('ifscode', 'IFSC code must be 11 characters.');
                    return;
                }

                if (empty($changedBankComponent->new_bank_account) || strlen($changedBankComponent->new_bank_account) < 9 || strlen($changedBankComponent->new_bank_account) > 18) {
                    $changedBankComponent->addError('new_bank_account', 'Bank account number must be 9–18 digits.');
                    return;
                }

                $ifs = Ifsccodemaster::where('code', $changedBankComponent->ifscode)
                    ->where('is_active', 1)
                    ->first();

                if (!$ifs) {
                    $changedBankComponent->addError('ifscode', 'Invalid IFSC code.');
                    return;
                }

                $accountExists = $bankIssues->first()->beneficiaryCommonList
                    ->bank()
                    ->where('bank_account_number', $changedBankComponent->new_bank_account)
                    ->where('application_id', '!=', $this->id)
                    ->exists();

                if ($accountExists) {
                    $changedBankComponent->addError('new_bank_account', 'This bank account number already exists.');
                    return;
                }

                $sharedBankData = [
                    'ifscode' => $changedBankComponent->ifscode,
                    'bank_name' => $ifs->bank->name ?? '',
                    'branch_name' => $ifs->branch ?? '',
                    'account' => $changedBankComponent->new_bank_account,
                ];
            }

            foreach ($bankIssues as $item) {
                $jsonData = [
                    'old_value' => $item->old_value ?? null,
                    'new_value' => null,
                    'bank_action' => $action,
                ];

                if ($action === '2') {
                    $jsonData['new_value'] = $sharedBankData;
                } else {
                    $jsonData['new_value'] = $item->old_value ?? null;
                }

                $item->update([
                    'new_value' => json_encode($jsonData),
                    'change_type' => $action,
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
