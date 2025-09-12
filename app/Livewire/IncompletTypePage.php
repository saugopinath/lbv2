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

    // protected $rules = [
    //     'formData.aadhar.*'        => 'nullable|digits:12',
    //     'formData.new_aadhar.*'    => 'nullable|digits:12',
    //     'formData.new_bank_account.*' => 'nullable|digits_between:9,18',
    //     'formData.mobile.*'        => 'nullable|digits:10',
    //     'formData.new_mobile.*'    => 'nullable|digits:10',
    //     'formData.bank_name.*'     => 'nullable|string|max:255',
    //     'formData.ifscode.*'       => 'nullable|string|size:11',
    //     'formData.mismatch_low.*'  => 'nullable|string|max:255',
    //     'formData.mismatch_high.*' => 'nullable|string|max:255',
    //     'formData.pds.*'           => 'nullable|digits:12',
    //     'formData.bank_action.*'   => 'nullable|in:1,2,3', // Added 3 for CHANGE
    // ];

    // protected $messages = [
    //     'formData.aadhar.*.digits' => 'Aadhaar number must be 12 digits.',
    //     'formData.new_aadhar.*.digits' => 'New Aadhaar number must be 12 digits.',
    //     'formData.new_bank_account.*.digits_between' => 'Bank account number must be 9–18 digits.',
    //     'formData.mobile.*.digits' => 'Mobile number must be 10 digits.',
    //     'formData.new_mobile.*.digits' => 'New mobile number must be 10 digits.',
    //     'formData.ifscode.*.size' => 'IFSC code must be 11 characters.',
    //     'formData.pds.*.digits' => 'PDS Aadhaar number must be 12 digits.',
    //     'formData.bank_action.*.in' => 'Invalid bank action selected.',
    // ];

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
// dd($this->page);
        $this->applicantInfo = $this->page->first()?->beneficiaryCommonList;

        // foreach ($this->page as $item) {
        //     $type = $item->incompletType->name ?? null;
        //     if (!$type) continue;

        //     $map = [
        //         'NO AADHAR NUMBER'                   => 'aadhar',
        //         'DUPLICATE AADHAR NUMBER'            => 'new_aadhar',
        //         'DUPLICATE BANK ACCOUNT NUMBER'      => 'new_bank_account',
        //         'NO MOBILE NUMBER'                   => 'mobile',
        //         'NAME VALIDATION FAILED IN BANK'     => 'bank_name',
        //         'ACCOUNT NUMBER VALIDATION FAILED IN BANK' => 'bank_account',
        //         'DUPLICATE MOBILE NUMBER'            => 'new_mobile',
        //         'MINOR MISMATCH(40% - 89%)'          => 'mismatch_low',
        //         'MINOR MISMATCH(90% - 100%)'         => 'mismatch_high',
        //         'PDS MISMATCH'                       => 'pds',
        //     ];

        //     if (isset($map[$type])) {
        //         if ($item->new_value) {
        //             $newValue = json_decode($item->new_value, true);
        //             if (in_array($type, ['NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER', 'PDS MISMATCH'])) {
        //                 $this->formData[$map[$type]][$item->id] = $newValue['new_value'] ? Crypt::decryptString($newValue['new_value']) : '';
        //             } elseif (in_array($type, ['DUPLICATE BANK ACCOUNT NUMBER', 'NAME VALIDATION FAILED IN BANK', 'ACCOUNT NUMBER VALIDATION FAILED IN BANK', 'MINOR MISMATCH(40% - 89%)', 'MINOR MISMATCH(90% - 100%)'])) {
        //                 // Check if this is a change action (3)
        //                 if (isset($newValue['bank_action']) && $newValue['bank_action'] === '3') {
        //                     // For change action, use the new values
        //                     $this->formData['new_bank_account'][$item->id] = $newValue['new_value']['account'] ?? $item->old_value['account_number'] ?? '';
        //                     $this->formData['ifscode'][$item->id] = $newValue['new_value']['ifscode'] ?? $item->old_value['ifsc'] ?? '';
        //                     $this->formData['bank_name'][$item->id] = $newValue['new_value']['bank_name'] ?? $item->old_value['bank_name'] ?? '';
        //                 } else {
        //                     // For keep same (1) or other actions, use old values
        //                     $this->formData['new_bank_account'][$item->id] = $item->old_value['account_number'] ?? '';
        //                     $this->formData['ifscode'][$item->id] = $item->old_value['ifsc'] ?? '';
        //                     $this->formData['bank_name'][$item->id] = $item->old_value['bank_name'] ?? '';
        //                 }
        //                 $this->formData['bank_action'][$item->id] = $newValue['bank_action'] ?? '1';
        //             } else {
        //                 $this->formData[$map[$type]][$item->id] = $newValue['new_value'] ?? '';
        //             }
        //         } else {
        //             if (in_array($type, ['DUPLICATE BANK ACCOUNT NUMBER', 'NAME VALIDATION FAILED IN BANK', 'ACCOUNT NUMBER VALIDATION FAILED IN BANK', 'MINOR MISMATCH(40% - 89%)', 'MINOR MISMATCH(90% - 100%)'])) {
        //                 // Default to old values for keep same
        //                 $this->formData['new_bank_account'][$item->id] = $item->old_value['account_number'] ?? '';
        //                 $this->formData['ifscode'][$item->id] = $item->old_value['ifsc'] ?? '';
        //                 $this->formData['bank_name'][$item->id] = $item->old_value['bank_name'] ?? '';
        //                 $this->formData['bank_action'][$item->id] = '1'; // Default to keep same
        //             } else {
        //                 $this->formData[$map[$type]][$item->id] = $item->old_value ?? '';
        //             }
        //         }
        //     }
        // }
    }

    // public function updateFormData($data)
    // {
    //     $id = $data['id'];
    //     $this->formData['ifscode'][$id] = $data['ifscode'];
    //     $this->formData['bank_name'][$id] = $data['bank_name'];
    //     $this->formData['new_bank_account'][$id] = $data['new_bank_account'];
    //     $this->formData['bank_action'][$id] = $data['bank_action'] ?? '1';
    // }

    // public function update()
    // {
    //      dd($this->all());
    //     // Validate all form data
    //     $this->validate();

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

    //     $bankIssues = collect($this->page)->filter(function ($item) {
    //         return in_array($item->incompletType->name, [
    //             'DUPLICATE BANK ACCOUNT NUMBER',
    //             'NAME VALIDATION FAILED IN BANK',
    //             'ACCOUNT NUMBER VALIDATION FAILED IN BANK',
    //             'MINOR MISMATCH(40% - 89%)',
    //             'MINOR MISMATCH(90% - 100%)',
    //         ]);
    //     });

    //     if ($bankIssues->isNotEmpty()) {
    //         foreach ($bankIssues as $item) {
    //             $bankAction = $this->formData['bank_action'][$item->id] ?? '1';
    //             $changeType = $bankAction; // 1 = KEEP SAME, 3 = CHANGE

    //             $jsonData = [
    //                 'old_value' => $item->old_value ?? null,
    //                 'new_value' => null,
    //                 'change_type' => (int)$changeType, // Store as integer in change_type column
    //             ];

    //             if ($changeType === '3') {
    //                 // CHANGE action - use new values and validate
    //                 $this->validate([
    //                     'formData.ifscode.' . $item->id => 'required|string|size:11',
    //                     'formData.new_bank_account.' . $item->id => 'required|digits_between:9,18',
    //                 ]);

    //                 $ifs = Ifsccodemaster::where('code', $this->formData['ifscode'][$item->id])
    //                     ->where('is_active', 1)
    //                     ->first();

    //                 if (!$ifs) {
    //                     $this->addError("formData.ifscode.{$item->id}", 'Invalid IFSC code.');
    //                     continue;
    //                 }

    //                 $accountExists = $item->beneficiaryCommonList
    //                     ->bank()
    //                     ->where('bank_account_number', $this->formData['new_bank_account'][$item->id])
    //                     ->where('application_id', '!=', $this->id)
    //                     ->exists();

    //                 if ($accountExists) {
    //                     $this->addError("formData.new_bank_account.{$item->id}", 'This bank account number already exists.');
    //                     continue;
    //                 }

    //                 // Set new bank data for CHANGE
    //                 $jsonData['new_value'] = [
    //                     'ifscode' => $this->formData['ifscode'][$item->id],
    //                     'bank_name' => $ifs->bank->name ?? '',
    //                     'branch_name' => $ifs->branch ?? '',
    //                     'account' => $this->formData['new_bank_account'][$item->id],
    //                 ];
    //             } else {
    //                 // KEEP SAME action - use old values
    //                 $jsonData['new_value'] = [
    //                     'ifscode' => $item->old_value['ifsc'] ?? '',
    //                     'bank_name' => $item->old_value['bank_name'] ?? '',
    //                     'branch_name' => $item->old_value['branch_name'] ?? '',
    //                     'account' => $item->old_value['account_number'] ?? '',
    //                 ];
    //             }

    //             $item->update([
    //                 'new_value' => json_encode($jsonData),
    //                 'change_type' => (int)$changeType, // Store in change_type column
    //                 'next_level_request_id' => 1,
    //                 'request_id' => $request->id,
    //             ]);
    //         }
    //     }

    //     // Handle non-bank issues (Aadhaar, Mobile, PDS)
    //     foreach ($this->page as $item) {
    //         $type = $item->incompletType->name ?? null;
    //         if (!$type || in_array($type, [
    //             'DUPLICATE BANK ACCOUNT NUMBER',
    //             'NAME VALIDATION FAILED IN BANK',
    //             'ACCOUNT NUMBER VALIDATION FAILED IN BANK',
    //             'MINOR MISMATCH(40% - 89%)',
    //             'MINOR MISMATCH(90% - 100%)',
    //         ])) {
    //             continue;
    //         }

    //         $jsonData = [
    //             'old_value' => $item->old_value ?? null,
    //             'new_value' => null,
    //             'change_type' => null, // Non-bank issues don't use change_type
    //         ];

    //         $map = [
    //             'NO AADHAR NUMBER'                   => 'aadhar',
    //             'DUPLICATE AADHAR NUMBER'            => 'new_aadhar',
    //             'NO MOBILE NUMBER'                   => 'mobile',
    //             'DUPLICATE MOBILE NUMBER'            => 'new_mobile',
    //             'PDS MISMATCH'                       => 'pds',
    //         ];

    //         $newValue = $this->formData[$map[$type]][$item->id] ?? null;

    //         if (in_array($type, ['NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER', 'PDS MISMATCH']) && $newValue) {
    //             $exists = $item->beneficiaryCommonList->aadhaar
    //                 ->aadhaar()
    //                 ->where('encoded_aadhar', md5($newValue))
    //                 ->where('application_id', '!=', $this->id)
    //                 ->exists();

    //             if ($exists) {
    //                 $this->addError("formData.{$map[$type]}.{$item->id}", 'This Aadhaar number already exists.');
    //                 continue;
    //             }
    //             $jsonData['new_value'] = Crypt::encryptString($newValue);
    //         } elseif (in_array($type, ['NO MOBILE NUMBER', 'DUPLICATE MOBILE NUMBER']) && $newValue) {
    //             $exists = $item->beneficiaryCommonList->beneficiaryPersonal
    //                 ->where('mobile_no', $newValue)
    //                 ->where('application_id', '!=', $this->id)
    //                 ->exists();

    //             if ($exists) {
    //                 $this->addError("formData.{$map[$type]}.{$item->id}", 'This mobile number already exists.');
    //                 continue;
    //             }
    //             $jsonData['new_value'] = $newValue;
    //         } else {
    //             $jsonData['new_value'] = $newValue ?? $item->old_value;
    //         }

    //         $item->update([
    //             'new_value' => json_encode($jsonData),
    //             'change_type' => null,
    //             'next_level_request_id' => 1,
    //             'request_id' => $request->id,
    //         ]);
    //     }

    //     session()->flash('success', 'Incomplete details updated successfully!');
    //     return redirect()->route('incomplete.types', [
    //         'stage' => 'verifier',
    //         'id' => $this->id,
    //     ]);
    // }

    public function render()
    {
        return view('livewire.incomplet-type-page', [
            'page' => $this->page,
            'applicantInfo' => $this->applicantInfo,
        ]);
    }
}
