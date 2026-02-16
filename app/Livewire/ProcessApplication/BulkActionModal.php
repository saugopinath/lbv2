<?php

namespace App\Livewire\ProcessApplication;

use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\FaultyBeneficiaryPersonal;
use Livewire\Component;
use App\Models\Codemaster;
use Livewire\Attributes\On;
use Masmerise\Toaster\Toaster;
use App\Models\BenRejectDetails;
use App\Models\BeneficiaryAadhaar;
use Illuminate\Support\Facades\DB;
use App\Models\BeneficiaryPersonal;
use App\Models\DraftBeneficiaryBank;
use Illuminate\Support\Facades\Auth;
use App\Models\DraftBeneficiaryContact;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryDeclaration;
use App\Models\ApplicantRejectRevertDetails;
use App\Models\DraftBeneficiaryRelationship;
use App\Models\AcceptRejectInfo;
use Illuminate\Support\Facades\Crypt;
use App\Services\WorkflowService;

class BulkActionModal extends Component
{
    public bool $bulkActionModal = false;
    public array $selectedRows = [];
    public string $bulkActionType = '';
    public ?int $reason = null;

    public string $remark = '';
    public array $reasons = [];
    public array $availableActions = [];
    public int $currentUserId;
    public $applicationId, $entryType;
    public $sameLabelRoleId, $nextLabelRoleId, $schemeId;
    public string $bulkActionTypeLabel = 'Select Operation';

    #[On('openBulkActionModal')]
    public function openModal(array $selectedIds = [], WorkflowService $workflowService)
    {
        $this->reset(['bulkActionType', 'reason', 'remark', 'availableActions', 'bulkActionTypeLabel']);
        $this->selectedRows = $selectedIds;
        // dd($this->selectedRows);
        $this->applicationId = $this->selectedRows['selectedIds']['application_id'];
        $this->entryType = $this->selectedRows['selectedIds']['entry_type'];
        $this->schemeId = $this->selectedRows['selectedIds']['schemeId'];
        // dd($this->entryType);
        $labelRoles = $workflowService->getLabelRoles($this->schemeId);
        if ($labelRoles) {
            $this->sameLabelRoleId = $labelRoles->same_label_role_id;
            $this->nextLabelRoleId = $labelRoles->next_label_role_id;
        }


        if ($this->entryType == 1) {
            // if ($this->entryType == Codemaster::getIdByCode(41)) {
            $entryType = 1;
        } elseif ($this->entryType == 2) {
            // } elseif ($this->entryType == Codemaster::getIdByCode(42)) {
            $entryType = 2;
        } else {
            $entryType = null;
        }

        if ($entryType) {
            if (WorkFlowPermissionHelper::canBulkActionAllow($entryType, 'verification') && CheckAuthHelper::isCommmonVerifier()) {
                $this->availableActions['V'] = 'Verify';
            }

            if (WorkFlowPermissionHelper::canBulkActionAllow($entryType, 'approver') && CheckAuthHelper::isCommonApprover()) {
                $this->availableActions['A'] = 'Approve';
            }

            if (WorkFlowPermissionHelper::canBulkActionAllow($entryType, 'reject') && CheckAuthHelper::isCommonWorkFlow2ndStep()) {
                $this->availableActions['R'] = 'Reject';
            }

            if (WorkFlowPermissionHelper::canBulkActionAllow($entryType, 'revert') && CheckAuthHelper::isCommonWorkFlow2ndStep()) {
                $this->availableActions['T'] = 'Revert';
            }
        }
        $this->bulkActionModal = true;
    }

    public function updatedBulkActionType($value, WorkflowService $workflowService)
    {
        if (in_array($value, ['R', 'T'])) {
            if ($value == 'T') {
                $this->nextLabelRoleId = $workflowService->getLabelRoles($this->schemeId, 1)->same_label_role_id;
            } elseif ($value == 'R') {
                $this->nextLabelRoleId = -100;
            }
            $this->reasons = Codemaster::where('parent_id', 12)
                ->orderBy('id', 'asc')
                ->pluck('name', 'id')
                ->toArray();
        } else {
            $this->reasons = [];
            $this->reason = null;
            $this->remark = '';
        }
    }

    public function performBulkAction()
    {


        $validated = $this->validate([
            'bulkActionType' => 'required|in:V,A,R,T',
            'reason' => in_array($this->bulkActionType, ['R', 'T']) ? 'required' : 'nullable',
            'remark' => in_array($this->bulkActionType, ['R', 'T']) ? 'required|string|max:255' : 'nullable',
        ]);

        $successMessage = 'Action performed successfully!';
        $approverRoleId = Codemaster::getIdByCode(23);
        $operatorRoleId = Codemaster::getIdByCode(21);
        $currentUserId = Auth::id();
        $select_lgd = session('lgd_session');
        $user_id = Crypt::decryptString($select_lgd['role_id']);


        // DB::transaction(function () use (&$successMessage, $approverRoleId, $operatorRoleId, $currentUserId) {
        // $ids = $this->applicationId;
        $ids = (array) $this->applicationId;
        // dd($ids);
        if ($this->bulkActionType === 'V') {
            foreach ($ids as $id) {
                // dd($id);
                DB::beginTransaction();
                try {
                    // dd($this->nextLabelRoleId);
                    //$DraftBeneficiaryPersonal = BeneficiaryPersonalDetail::find($id);
                    // dd($DraftBeneficiaryPersonal);
                    //$DraftBeneficiaryPersonal->next_level_role_id = $this->nextLabelRoleId;
                    // dd($DraftBeneficiaryPersonal);
                    //$DraftBeneficiaryPersonal->save();
                    // $AcceptRejectInfo = new AcceptRejectInfo;
                    // $AcceptRejectInfo->application_id = $DraftBeneficiaryPersonal->application_id;
                    // $AcceptRejectInfo->beneficiary_id = $DraftBeneficiaryPersonal->beneficiary_id;
                    // $AcceptRejectInfo->ip_address = request()->ip();
                    // $AcceptRejectInfo->user_id = Auth::id();
                    // $AcceptRejectInfo->browser = request()->header('User-Agent');
                    // $AcceptRejectInfo->model_name = null;
                    // $AcceptRejectInfo->op_type = $approverRoleId;
                    // $AcceptRejectInfo->revert_reason_cause_id = null;
                    // $AcceptRejectInfo->revert_reason_remarks = null;
                    // $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $id)
                    //     ->latest('id')
                    //     ->value('id') ?? null;
                    // $AcceptRejectInfo->save();
                    BeneficiaryPersonalDetail::where('application_id', $id)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                    ]);
                    DB::commit();
                    $this->dispatch('toastr', [
                        'type' => 'success',
                        'message' => 'Application verified successfully!'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        } elseif ($this->bulkActionType === 'A') {
            foreach ($ids as $id) {
                DB::beginTransaction();
                try {
                    BeneficiaryPersonalDetail::where('application_id', $id)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                    ]);
                    // $DraftBeneficiaryPersonal = BeneficiaryPersonalDetail::find($id);
                    // $DraftBeneficiaryPersonal->next_level_role_id = $this->nextLabelRoleId
                    // $DraftBeneficiaryPersonal->save();
                    // $AcceptRejectInfo = new AcceptRejectInfo;
                    // $AcceptRejectInfo->application_id = $DraftBeneficiaryPersonal->application_id;
                    // $AcceptRejectInfo->beneficiary_id = $DraftBeneficiaryPersonal->beneficiary_id;
                    // $AcceptRejectInfo->ip_address = request()->ip();
                    // $AcceptRejectInfo->user_id = Auth::id();
                    // $AcceptRejectInfo->browser = request()->header('User-Agent');
                    // $AcceptRejectInfo->model_name = null;
                    // $AcceptRejectInfo->op_type = Codemaster::getIdByCode(0);
                    // $AcceptRejectInfo->revert_reason_cause_id = null;
                    // $AcceptRejectInfo->revert_reason_remarks = null;
                    // $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $id)
                    //     ->latest('id')
                    //     ->value('id') ?? null;
                    // $AcceptRejectInfo->save();
                    DB::commit();
                    $this->dispatch('toastr', [
                        'type' => 'success',
                        'message' => 'Application approved successfully!'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        } elseif ($this->bulkActionType === 'T') {

            // $user = auth()->user();
            // if (CheckAuthHelper::isCommonApprover()) {
            //     // if ($user->hasAnyRole(['Approver', 'Delegated Approver'])) {
            //     $next_level_role_id = Codemaster::getIdByCode(22);
            // }
            // if (CheckAuthHelper::isCommmonVerifier()) {
            //     // if ($user->hasAnyRole(['Verifier', 'Delegated Verifier'])) {
            //     $next_level_role_id = Codemaster::getIdByCode(21);
            // }
            foreach ($ids as $id) {
                DB::beginTransaction();
                try {
                    BeneficiaryPersonalDetail::where('application_id', $id)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                    ]);
                    // $DraftBeneficiaryPersonal = BeneficiaryPersonalDetail::find($id);
                    // $DraftBeneficiaryPersonal->next_level_role_id = $this->nextLabelRoleId;
                    // $DraftBeneficiaryPersonal->save();
                    // $AcceptRejectInfo = new AcceptRejectInfo;
                    // $AcceptRejectInfo->application_id = $DraftBeneficiaryPersonal->application_id;
                    // $AcceptRejectInfo->beneficiary_id = $DraftBeneficiaryPersonal->beneficiary_id;
                    // $AcceptRejectInfo->ip_address = request()->ip();
                    // $AcceptRejectInfo->user_id = Auth::id();
                    // $AcceptRejectInfo->browser = request()->header('User-Agent');
                    // $AcceptRejectInfo->model_name = null;
                    // $AcceptRejectInfo->op_type = $next_level_role_id;
                    // $AcceptRejectInfo->revert_reason_cause_id = $validated['reason'];
                    // $AcceptRejectInfo->revert_reason_remarks = $validated['remark'];
                    // $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $id)
                    //     ->latest('id')
                    //     ->value('id') ?? null;
                    // $AcceptRejectInfo->save();
                    BeneficiaryPersonalDetail::where('application_id', $id)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                    ]);
                    DB::commit();
                    $this->dispatch('toastr', [
                        'type' => 'warning',
                        'message' => 'Application reverted successfully!'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        } elseif ($this->bulkActionType === 'R') {
            foreach ($ids as $id) {
                DB::beginTransaction();
                try {
                    // $DraftBeneficiaryPersonal = BeneficiaryPersonalDetail::find($id);
                    // $DraftBeneficiaryPersonal->next_level_role_id = $this->nextLabelRoleId;
                    // $DraftBeneficiaryPersonal->save();
                    // $AcceptRejectInfo = new AcceptRejectInfo;
                    // $AcceptRejectInfo->application_id = $DraftBeneficiaryPersonal->application_id;
                    // $AcceptRejectInfo->beneficiary_id = $DraftBeneficiaryPersonal->beneficiary_id;
                    // $AcceptRejectInfo->ip_address = request()->ip();
                    // $AcceptRejectInfo->user_id = Auth::id();
                    // $AcceptRejectInfo->browser = request()->header('User-Agent');
                    // $AcceptRejectInfo->model_name = null;
                    // $AcceptRejectInfo->op_type = Codemaster::getIdByCode(-1);
                    // $AcceptRejectInfo->revert_reason_cause_id = null;
                    // $AcceptRejectInfo->revert_reason_remarks = null;
                    // $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $id)
                    //     ->latest('id')
                    //     ->value('id') ?? null;
                    // $AcceptRejectInfo->save();
                    BeneficiaryPersonalDetail::where('application_id', $id)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                        'is_clean' => 10,
                    ]);
                    DB::commit();
                    $this->dispatch('toastr', [
                        'type' => 'error',
                        'message' => 'Application rejected successfully!'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        }
        // });
        // Toaster::success($successMessage);

        $this->bulkActionModal = false;

        $this->reset(['bulkActionType', 'reason', 'remark', 'selectedRows', 'bulkActionTypeLabel']);

        return redirect()->route('lb-application-list');
        // $this->dispatch('toaster-success', $successMessage);
        // $this->dispatch('actionPerformedAndRedirect');
    }

    public function render()
    {
        return view('livewire.process-application.bulk-action-modal');
    }
}
