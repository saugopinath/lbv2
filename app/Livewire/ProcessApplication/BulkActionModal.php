<?php

namespace App\Livewire\ProcessApplication;

use App\Helpers\CheckAuthHelper;
use App\Helpers\SchemeCapacityHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Codemaster;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class BulkActionModal extends Component
{
    public bool $bulkActionModal = false;

    public array $selectedRows = [];

    public string $bulkActionType = '';

    public ?int $reason = null;

    public string $remark = '';

    public array $reasons = [];

    public array $availableActions = [];

    public $filter_data = [];

    public int $currentUserId;

    public $applicationId;

    public $entryType;

    public $sameLabelRoleId;

    public $nextLabelRoleId;

    public $schemeId;

    public string $bulkActionTypeLabel = 'Select Operation';

    #[On('openBulkActionModal')]
    public function openModal(array $selectedIds, WorkflowService $workflowService)
    {
        $select_lgd = session('lgd_session');
        // dd($select_lgd);
        if (! empty($select_lgd['district_id'])) {
            $this->filter_data['created_by_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
        }
        if (! empty($select_lgd['block_id'])) {
            $this->filter_data['created_by_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
        }
        if (! empty($select_lgd['subdivision_id'])) {
            $this->filter_data['created_by_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }

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
        // $validated = $this->validate([
        //     'bulkActionType' => 'required|in:V,A,R,T',
        //     'reason' => in_array($this->bulkActionType, ['R', 'T']) ? 'required' : 'nullable',
        //     'remark' => in_array($this->bulkActionType, ['R', 'T']) ? 'required|string|max:255' : 'nullable',
        // ]);

        $validated = $this->validate([
            'bulkActionType' => 'required|in:V,A,R,T',

            'reason' => in_array($this->bulkActionType, ['R', 'T'])
                ? 'required'
                : 'nullable',

            'remark' => in_array($this->bulkActionType, ['R', 'T', 'A', 'V'])
                ? 'required|string|max:255'
                : 'nullable',
        ]);

        // ✅ CAPACITY CHECK START
        // dd($this->nextLabelRoleId,$this->sameLabelRoleId);
        // $result = null;

        // if ($this->bulkActionType === 'V') {

        //     $result = SchemeCapacityHelper::check(
        //         $this->schemeId,
        //         $this->nextLabelRoleId,
        //         $this->filter_data
        //     );
        // }

        // if ($this->bulkActionType === 'A') {

        //     $result = SchemeCapacityHelper::check(
        //         $this->schemeId,
        //         $this->nextLabelRoleId,
        //         $this->filter_data
        //     );
        // }
        // dd($result);
        // if (is_array($result)) {

        //     $msg = "{$result['model']} capacity full. 
        //     Total: {$result['total']} 
        //     Processed: {$result['processed']} 
        //     Remaining: {$result['remaining']}";

        //     $this->dispatch('toastr', [
        //         'type' => 'error',
        //         'message' => $msg,
        //     ]);
        //     return;
        // }

        // ✅ CAPACITY CHECK END
        $approverRoleId = Codemaster::getIdByCode(23);
        $ids = (array) $this->applicationId;
        if ($this->bulkActionType === 'V') {
            foreach ($ids as $id) {
                if (! $this->checkCapacity()) {
                    return;
                }
                DB::beginTransaction();
                try {
                    BeneficiaryPersonalDetail::where('application_id', $id)->where($this->filter_data)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                    ]);
                    $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $id)->value('beneficiary_id');
                    $AcceptRejectInfo = new AcceptRejectInfo;
                    $AcceptRejectInfo->application_id = $id;
                    $AcceptRejectInfo->beneficiary_id = $beneficiary_id;
                    $AcceptRejectInfo->ip_address = request()->ip();
                    $AcceptRejectInfo->scheme_id = $this->schemeId;
                    $AcceptRejectInfo->user_id = Auth::id();
                    $AcceptRejectInfo->browser = request()->header('User-Agent');
                    $AcceptRejectInfo->model_name = null;
                    $AcceptRejectInfo->op_type = $approverRoleId;
                    $AcceptRejectInfo->revert_reason_cause_id = null;
                    $AcceptRejectInfo->revert_reason_remarks = $validated['remark'];
                    $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $id)
                        ->latest('id')
                        ->value('id') ?? null;
                    $AcceptRejectInfo->save();
                    DB::commit();
                    $this->dispatch('toastr', [
                        'type' => 'success',
                        'message' => 'Application verified successfully!',
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        } elseif ($this->bulkActionType === 'A') {
            foreach ($ids as $id) {
                if (! $this->checkCapacity()) {
                    return;
                }
                DB::beginTransaction();
                try {
                    BeneficiaryPersonalDetail::where('application_id', $id)->where($this->filter_data)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                        'is_clean' => 1,
                    ]);
                    $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $id)->value('beneficiary_id');
                    $AcceptRejectInfo = new AcceptRejectInfo;
                    $AcceptRejectInfo->application_id = $id;
                    $AcceptRejectInfo->beneficiary_id = $beneficiary_id;
                    $AcceptRejectInfo->ip_address = request()->ip();
                    $AcceptRejectInfo->scheme_id = $this->schemeId;
                    $AcceptRejectInfo->user_id = Auth::id();
                    $AcceptRejectInfo->browser = request()->header('User-Agent');
                    $AcceptRejectInfo->model_name = null;
                    $AcceptRejectInfo->op_type = Codemaster::getIdByCode(0);
                    $AcceptRejectInfo->revert_reason_cause_id = null;
                    $AcceptRejectInfo->revert_reason_remarks = $validated['remark'];
                    $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $id)
                        ->latest('id')
                        ->value('id') ?? null;
                    $AcceptRejectInfo->save();
                    DB::commit();
                    $this->dispatch('toastr', [
                        'type' => 'success',
                        'message' => 'Application approved successfully!',
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        } elseif ($this->bulkActionType === 'T') {

            if (CheckAuthHelper::isCommonApprover()) {
                $next_level_role_id = Codemaster::getIdByCode(22);
            }
            if (CheckAuthHelper::isCommmonVerifier()) {
                $next_level_role_id = Codemaster::getIdByCode(21);
            }
            foreach ($ids as $id) {
                DB::beginTransaction();
                try {
                    BeneficiaryPersonalDetail::where('application_id', $id)->where($this->filter_data)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                    ]);
                    $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $id)->value('beneficiary_id');
                    $AcceptRejectInfo = new AcceptRejectInfo;
                    $AcceptRejectInfo->application_id = $id;
                    $AcceptRejectInfo->beneficiary_id = $beneficiary_id;
                    $AcceptRejectInfo->ip_address = request()->ip();
                    $AcceptRejectInfo->scheme_id = $this->schemeId;
                    $AcceptRejectInfo->user_id = Auth::id();
                    $AcceptRejectInfo->browser = request()->header('User-Agent');
                    $AcceptRejectInfo->model_name = null;
                    $AcceptRejectInfo->op_type = $next_level_role_id;
                    $AcceptRejectInfo->revert_reason_cause_id = $validated['reason'];
                    $AcceptRejectInfo->revert_reason_remarks = $validated['remark'];
                    $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $id)
                        ->latest('id')
                        ->value('id') ?? null;
                    $AcceptRejectInfo->save();
                    BeneficiaryPersonalDetail::where('application_id', $id)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                    ]);
                    DB::commit();
                    $this->dispatch('toastr', [
                        'type' => 'warning',
                        'message' => 'Application reverted successfully!',
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
                    BeneficiaryPersonalDetail::where('application_id', $id)->where($this->filter_data)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                        'is_clean' => 10,
                    ]);
                    $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $id)->value('beneficiary_id');
                    $AcceptRejectInfo = new AcceptRejectInfo;
                    $AcceptRejectInfo->application_id = $id;
                    $AcceptRejectInfo->beneficiary_id = $beneficiary_id;
                    $AcceptRejectInfo->ip_address = request()->ip();
                    $AcceptRejectInfo->scheme_id = $this->schemeId;
                    $AcceptRejectInfo->user_id = Auth::id();
                    $AcceptRejectInfo->browser = request()->header('User-Agent');
                    $AcceptRejectInfo->model_name = null;
                    $AcceptRejectInfo->op_type = Codemaster::getIdByCode(-1);
                    $AcceptRejectInfo->revert_reason_cause_id = $validated['reason'];
                    $AcceptRejectInfo->revert_reason_remarks = $validated['remark'];
                    $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $id)
                        ->latest('id')
                        ->value('id') ?? null;
                    $AcceptRejectInfo->save();
                    DB::commit();
                    $this->dispatch('toastr', [
                        'type' => 'error',
                        'message' => 'Application rejected successfully!',
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        }

        // $this->bulkActionModal = false;

        // $this->reset(['bulkActionType', 'reason', 'remark', 'selectedRows', 'bulkActionTypeLabel']);

        // return redirect()->route('lb-application-list');
        $this->bulkActionModal = false;

        $this->reset(['bulkActionType', 'reason', 'remark', 'selectedRows', 'bulkActionTypeLabel']);

        return redirect()->route('lb-application-list', [
            'scheme_id' => Crypt::encryptString($this->schemeId),
        ]);
    }
    private function checkCapacity(): bool
    {
        $beneficiary = BeneficiaryPersonalDetail::find($this->applicationId);
        $bencreatAdd = [
            'created_by_dist_code' => $beneficiary->created_by_dist_code,
            'created_by_local_body_code' => $beneficiary->created_by_local_body_code,
            'creator' => $beneficiary->creator()
        ];
        $result = SchemeCapacityHelper::check(
            $this->schemeId,
            $this->nextLabelRoleId,
            $this->entryType,
            $bencreatAdd
        );
        if (!$result['is_processed']) {
            $msg = 'Capacity exceeded for ' . ($result['model'] ?? 'Scheme') .
                '! Available: ' . ($result['remaining_capacity'] ?? 0);
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => $msg,
            ]);
            return false;
        }
        return true;
    }
    public function render()
    {
        return view('livewire.process-application.bulk-action-modal');
    }
}
