<?php

namespace App\Livewire\CasteModification;

use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryPersonal;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\CasteModificationInfo;
use App\Models\Codemaster;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\BeneficiaryTemEnclosure;
use App\Models\CriticalChangeMaster;
use Illuminate\Support\Facades\DB;

class CasteModificationAction extends Component
{
    public $applicationId;
    public $roleId;
    public $action;
    public $remark;

    public $showModal = false;
    public $availableActions = [];
    public $heading = '';
    public $doc_type;
    public $scheme_id;
    public $filter_condition = [];
    protected $rules = [
        'action' => 'required|string',
    ];
    public function mount($applicationId, $scheme_id)
    {
        $this->scheme_id = $scheme_id;
        $this->applicationId = $applicationId;
        $this->roleId = CheckAuthHelper::getRoleId();
        $this->doc_type = Codemaster::getIdByCode(162); // Caste Certificate Document Type
        $select_lgd = session('lgd_session');
        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['created_by_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
        }
        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
        }
        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
        // Define heading & actions dynamically
        // if (CheckAuthHelper::isVerifier()) {
        //     $this->heading = "Process the Application : $this->applicationId";
        //     $this->availableActions = [
        //         '2202' => 'Verify',
        //         '2204' => 'Revert',
        //     ];
        // } elseif (CheckAuthHelper::isApprover()) {
        //     $this->heading = "Process the Application : $this->applicationId";
        //     $this->availableActions = [
        //         '2203' => 'Approve',
        //         '2204' => 'Revert',
        //     ];
        // }
        if (CheckAuthHelper::isVerifier()) {
            $this->heading = "Process the Application : $this->applicationId";
            if (WorkFlowPermissionHelper::canVerifyCastApplication()) {
                $this->availableActions['2202'] = 'Verify';
            }
            if (WorkFlowPermissionHelper::canRevertCastApplication()) {
                $this->availableActions['2204'] = 'Revert';
            }
        } elseif (CheckAuthHelper::isApprover()) {
            $this->heading = "Process the Application : $this->applicationId";
            if (WorkFlowPermissionHelper::canApproveCastApplication()) {
                $this->availableActions['2203'] = 'Approve';
            }
            if (WorkFlowPermissionHelper::canRevertCastApplication()) {
                $this->availableActions['2204'] = 'Revert';
            }
        }
    }

    public function openModal()
    {
        $this->reset('action');
        $this->showModal = true;
    }
    public function closeModal()
    {
        $this->reset('action');
        $this->resetValidation();
        $this->showModal = false;
    }
    public function getValidationRules(): array
    {
        $rules = [
            'action' => 'required|string|in:2202,2203,2204',
        ];
        // If action is 2204 (Revert), remark is required
        if ($this->action === '2204') {
            $rules['remark'] = 'required|string|min:3|max:500';
        } else {
            $rules['remark'] = 'nullable|string|max:500';
        }
        return $rules;
    }
    public function getValidationMessages(): array
    {
        return [
            'action.required' => 'Action is required.',
            'action.in' => 'Invalid action selected.',
            'remark.required' => 'Remark is required when reverting.',
            'remark.min' => 'Remark must be at least 3 characters.',
            'remark.max' => 'Remark cannot exceed 500 characters.',
        ];
    }

    public function submit()
    {
        // dd($this->remark);
        $this->validate(
            $this->getValidationRules(),
            $this->getValidationMessages()
        );
        $casteModification = CasteModificationInfo::where('application_id', $this->applicationId)->where('scheme_id', $this->scheme_id)->where('is_active', true)
            ->latest()
            ->first();
        if (!$casteModification) {
            session()->flash('error', 'Application not found!');
            return;
        }
        $mapping = [
            '2202' => Codemaster::getIdByCode(2202),
            '2203' => Codemaster::getIdByCode(2203),
            '2204' => Codemaster::getIdByCode(2204),
        ];
        $opTypeMapping = [
            '2202' => Codemaster::getIdByCode(2108), // Verify op_type
            '2203' => Codemaster::getIdByCode(2109), // Approve op_type
            '2204' => Codemaster::getIdByCode(2110), // Revert op_type
        ];
        if (!isset($mapping[$this->action])) {
            session()->flash('error', 'Invalid action selected!');
            return;
        }
        $previousId = AcceptRejectInfo::where('application_id', $this->applicationId)->where('scheme_id', $this->scheme_id)
            ->orderByDesc('id')
            ->value('id');
        $criticalChangeId = CriticalChangeMaster::getIdByCode(1);

        // Permission Validation Check
        $hasPermission = false;
        switch ($this->action) {
            case '2202': // Verify
                $hasPermission = CheckAuthHelper::isVerifier() && WorkFlowPermissionHelper::canVerifyCastApplication();
                break;
            case '2203': // Approve
                $hasPermission = CheckAuthHelper::isApprover() && WorkFlowPermissionHelper::canApproveCastApplication();
                break;
            case '2204': // Revert
                $hasPermission = (CheckAuthHelper::isVerifier() || CheckAuthHelper::isApprover()) && WorkFlowPermissionHelper::canRevertCastApplication();
                break;
        }
        if (!$hasPermission) {
            session()->flash('error', 'Oops! You do not have permission to perform this action.');
            $this->closeModal();
            return redirect()->route('caste-modification-list');
        }
        try {
            DB::beginTransaction();

            $casteSaved = false;
            $acceptSaved = false;
            $beneficiarySaved = true;
            $casteUpdated = true;
            $casteModification->next_level_requested_id = $mapping[$this->action];
            $casteModification->updated_by = Auth::id();
            $casteSaved = $casteModification->save(); // boolean
            $acceptReject = new AcceptRejectInfo();
            $acceptReject->application_id = $this->applicationId;
            $acceptReject->scheme_id = $this->scheme_id;
            $acceptReject->beneficiary_id = $casteModification->beneficiary_id;
            $acceptReject->ip_address = request()->ip();
            $acceptReject->user_id = Auth::id();
            $acceptReject->browser = request()->header('User-Agent');
            $acceptReject->model_name = class_basename(static::class) . '@' . __FUNCTION__;
            $acceptReject->op_type = $opTypeMapping[$this->action];
            $acceptReject->revert_reason_cause_id = null;
            $acceptReject->parent_id = $previousId;
            if ($this->action == '2203') {
                $acceptReject->critical_changes = $criticalChangeId;
                $acceptReject->old_value = json_encode($casteModification->old_data);
                $acceptReject->new_value = json_encode($casteModification->new_data);
            } else {
                $acceptReject->critical_changes = 0;
                $acceptReject->old_value = null;
                $acceptReject->new_value = null;
            }
            if ($this->action == '2204') {
                $acceptReject->revert_reason_remarks = $this->remark;
            } else {
                $acceptReject->revert_reason_remarks = null;
            }

            $acceptSaved = $acceptReject->save();
            if ($this->action == '2203') {
                // Update BeneficiaryPersonal
                $beneficiary = BeneficiaryPersonalDetail::where('application_id', $this->applicationId)->where('scheme_id', $this->scheme_id)->where($this->filter_condition)->first();

                if ($beneficiary) {
                    $beneficiary->caste = $casteModification->new_data['caste'] ?? $beneficiary->caste;
                    $beneficiary->caste_cer_no = $casteModification->new_data['caste_certificate_no'] ?? $beneficiary->caste_certificate_no;
                    $beneficiarySaved = $beneficiary->save();
                    // Move enclosure only when temp data  exists
                    $temp = BeneficiaryTemEnclosure::where('application_id', $this->applicationId)->where('document_type', $this->doc_type)->first();;
                    // 
                    // dd( $temp->toSql() , $temp->getBindings());
                    if ($temp) {
                        // dd($temp);
                        // dd($this->applicationId);
                        $beneficiary->documents()->updateOrCreate(
                            [
                                'application_id' => $this->applicationId,
                                'scheme_id' => $this->scheme_id
                            ],
                            [
                                'beneficiary_id' => $beneficiary->beneficiary_id,
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
                    } else {
                        $deleteprevious = $beneficiary->documents()->where('application_id', $this->applicationId)->where('scheme_id', $this->scheme_id)->where('document_type', $this->doc_type)->first();
                        // dd($deleteprevious);
                        if ($deleteprevious) {
                            $deleteprevious->delete();
                        }
                    }
                    // Update CasteModification  after applying changes
                    $casteModification->is_active = false;
                    $casteModification->updated_by = Auth::id();
                    $casteUpdated = $casteModification->save();
                } else {
                    $beneficiarySaved = false;
                }
            }
            // If action is approve  then require casteUpdated too, otherwise it's not required.
            $allOkay = $casteSaved && $acceptSaved && $beneficiarySaved;
            if ($this->action == '2203') {
                $allOkay = $allOkay && $casteUpdated;
            }
            // dd();
            // dump($allOkay);
            // dump($casteSaved, $acceptSaved, $beneficiarySaved, $casteUpdated, $allOkay);
            if ($allOkay) {
                DB::commit();
                session()->flash('success', "Application processed successfully!");
                return redirect()->route('caste-modification-list', ['retain_filters' => 1]);
            } else {
                DB::rollBack();
                session()->flash('error', 'Transaction failed. Some records were not saved.');
                return redirect()->route('caste-modification-list', ['retain_filters' => 1]);
            }
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();
            // Consider logging the exception: \Log::error($e);
            session()->flash('error', 'Something went wrong: ' . $e->getMessage());
            return redirect()->route('caste-modification-list', ['retain_filters' => 1]);
        }
    }

    public function render()
    {
        return view('livewire.caste-modification.caste-modification-action');
    }
}
