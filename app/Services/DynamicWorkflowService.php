<?php

namespace App\Services;

use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryBankDetail;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Codemaster;
use App\Models\DynamicWorkflowLabel;
use App\Models\DynamicWorkflowRequest;
use App\Models\workflowstepRolemapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DynamicWorkflowService
{
    private function getCurrentUserRoleId()
    {
        $lgd_session = session('lgd_session');
        if (!empty($lgd_session['role_id'])) {
            try {
                return (int) \Illuminate\Support\Facades\Crypt::decryptString($lgd_session['role_id']);
            } catch (\Exception $e) {
                return 0;
            }
        }
        return 0;
    }
    public function initiateRequest($moduleId, $refId, $schemeId, $oldData, $newData, $changedFields = [])
    {
        $roleId = $this->getCurrentUserRoleId();
        DB::beginTransaction();
        try {
            $firstStep = workflowstepRolemapping::where([
                'module_id' => $moduleId,
                'scheme_id' => $schemeId
            ])
                ->orderBy('rank', 'asc')
                ->orderBy('id', 'asc')
                ->first();

            if (!$firstStep) {
                throw new \Exception('You are not authorized to initiate this workflow or steps are not configured.');
            }

            $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $refId)->value('beneficiary_id');
            $parentId = AcceptRejectInfo::where('application_id', $refId)->latest('id')->value('id');
            $log = AcceptRejectInfo::create([
                'application_id' => $refId,
                'beneficiary_id' => $beneficiary_id,
                'scheme_id'      => $schemeId,
                'user_id'        => Auth::id(),
                'ip_address'     => request()->ip(),
                'browser'        => request()->userAgent(),
                'op_type'        => DynamicWorkflowLabel::getOpTypeId($firstStep->workflow_step_id),
                'model_name'     => optional($firstStep->module)->module_name ?? 'null',
                'parent_id'      => $parentId,
                'old_value'      => $oldData,
                'new_value'      => $newData,
            ]);

            $request = DynamicWorkflowRequest::create([
                'module_id'       => $moduleId,
                'ref_id'          => $refId,
                'scheme_id'       => $schemeId,
                'current_rank'    => $firstStep->next_label_role_id,
                'current_step_id' => $firstStep->workflow_step_id,
                'old_data'        => $oldData,
                'new_data'        => $newData,
                'changed_fields'  => $changedFields,
                'created_by'      => Auth::id(),
            ]);
            if ($log && $request) {
                DB::commit();
                return $request;
            } else {
                DB::rollBack();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function approve($requestId, $remark)
    {
        DB::beginTransaction();
        $roleId = $this->getCurrentUserRoleId();
        try {
            $request = DynamicWorkflowRequest::findOrFail($requestId);
            $currentStep = workflowstepRolemapping::where('module_id', $request->module_id)
                ->where('rank', $request->current_rank)
                ->where('role_id', $roleId)
                ->first();
            if (!$currentStep) {
                throw new \Exception('Step not found');
            }
            $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $request->ref_id)->value('beneficiary_id');
            $parentId = AcceptRejectInfo::where('application_id', $request->ref_id)->latest('id')->value('id');
            $AcceptRejectInfo = AcceptRejectInfo::create([
                'application_id' => $request->ref_id,
                'beneficiary_id' => $beneficiary_id,
                'scheme_id'      => $request->scheme_id,
                'user_id'        => Auth::id(),
                'ip_address'     => request()->ip(),
                'browser'        => request()->userAgent(),
                'op_type'        => DynamicWorkflowLabel::getOpTypeId($currentStep->workflow_step_id),
                'model_name'     => optional($request->module)->module_name ?? 'null',
                'parent_id'      => $parentId,
                'old_value'      => $request->old_data,
                'new_value'      => $request->new_data,
            ]);
            if ($currentStep->is_final_step) {
                $this->applyApprovedChanges($request);
                $request = DynamicWorkflowRequest::find($request->id);
                $UpdateRequest = $request->update([
                    'current_rank' => $currentStep->next_label_role_id,
                    'current_step_id' => $currentStep->workflow_step_id
                ]);
                $msg = ['message' => 'Finally Approved & Beneficiary Data Updated'];
            } else {
                $UpdateRequest = $request->update([
                    'current_rank' => $currentStep->next_label_role_id,
                    'current_step_id' => $currentStep->workflow_step_id
                ]);
                $msg = ['message' => 'Processed & Forwarded to the Next Step'];
            }
            if ($AcceptRejectInfo && $UpdateRequest) {
                DB::commit();
                return $msg;
            } else {
                DB::rollBack();
                return $msg = ['message' => 'Failed to process request'];
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return $msg = ['message' => 'Failed to process request'];
            // throw $e;
        }
    }
    public function revert($requestId, $remark = '')
    {
        DB::beginTransaction();
        try {
            $request = DynamicWorkflowRequest::findOrFail($requestId);
            $currentStep = $this->getStepForRank($request->module_id, $request->current_rank);

            // পেছনের র্যাঙ্ক-এ পাঠানো (same_label_role_id ব্যবহার করে)
            $prevStep = $this->getStepForRank(
                $request->module_id,
                $currentStep->same_label_role_id,
                'Revert target rank configuration missing.'
            );

            $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $request->ref_id)->value('beneficiary_id');
            $parentId = AcceptRejectInfo::where('application_id', $request->ref_id)->latest('id')->value('id');

            AcceptRejectInfo::create([
                'application_id' => $request->ref_id,
                'beneficiary_id' => $beneficiary_id,
                'scheme_id'      => $request->scheme_id,
                'user_id'        => Auth::id(),
                'ip_address'     => request()->ip(),
                'browser'        => request()->userAgent(),
                'op_type'        => $prevStep->rank,
                'model_name'     => optional($request->module)->module_name ?? 'null',
                'revert_reason_remarks' => $remark ?: "Reverted to previous step",
                'parent_id'      => $parentId,
                'old_value'      => $request->old_data,
                'new_value'      => $request->new_data,
            ]);

            $request->update([
                'current_rank' => $prevStep->rank,
                'current_step_id' => $prevStep->id,
            ]);

            DB::commit();
            return ['status' => 'reverted', 'message' => 'Request reverted to rank ' . $prevStep->rank];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function reject($requestId, $remark = '')
    {
        DB::beginTransaction();
        try {
            $request = DynamicWorkflowRequest::findOrFail($requestId);
            $currentStep = workflowstepRolemapping::where('module_id', $request->module_id)
                ->where('rank', $request->current_rank)
                ->first();
            $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $request->ref_id)->value('beneficiary_id');
            $parentId = AcceptRejectInfo::where('application_id', $request->ref_id)->latest('id')->value('id');
            AcceptRejectInfo::create([
                'application_id' => $request->ref_id,
                'beneficiary_id' => $beneficiary_id,
                'scheme_id'      => $request->scheme_id,
                'user_id'        => Auth::id(),
                'ip_address'     => request()->ip(),
                'browser'        => request()->userAgent(),
                'op_type'        => Codemaster::getIdByCode(-1),
                'model_name'     => optional($request->module)->module_name ?? 'null',
                'revert_reason_remarks' => $remark ?: "Rejected",
                'parent_id'      => $parentId,
                'old_value'      => $request->old_data,
                'new_value'      => $request->new_data,
            ]);

            $request->update([
                'current_rank' => -100,
                'current_step_id' => $currentStep->workflow_step_id
            ]);

            DB::commit();
            return ['status' => 'rejected', 'message' => 'Request rejected and closed.'];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function applyApprovedChanges(DynamicWorkflowRequest $request): void
    {
        $beneficiary = BeneficiaryPersonalDetail::where('application_id', $request->ref_id)->firstOrFail();
        $newData = $request->new_data ?? [];

        $personalData = [];

        if (array_key_exists('beneficiary_name', $newData)) {
            $personalData['beneficiary_name'] = $newData['beneficiary_name'];
        }

        if (array_key_exists('dob', $newData)) {
            $personalData['dob'] = $newData['dob'];
        }

        if (array_key_exists('age', $newData)) {
            $personalData['age'] = $newData['age'];
        }

        if (!empty($personalData)) {
            $beneficiary->update($personalData);
        }

        if (array_key_exists('mobile_no', $newData)) {
            $otherDetails = $beneficiary->other_details ?? [];
            if (is_string($otherDetails)) {
                $otherDetails = json_decode($otherDetails, true) ?? [];
            }
            $otherDetails['mobile_no'] = $newData['mobile_no'];
            $beneficiary->update(['other_details' => $otherDetails]);
        }

        $bankColumns = ['bank_ifsc', 'bank_name', 'bank_branch_name', 'bank_account_number'];
        $hasBankUpdate = collect($bankColumns)->contains(fn($column) => array_key_exists($column, $newData));

        if ($hasBankUpdate) {
            BeneficiaryBankDetail::updateOrCreate(
                ['application_id' => $beneficiary->application_id],
                [
                    'scheme_id' => $beneficiary->scheme_id,
                    'beneficiary_id' => $beneficiary->beneficiary_id,
                    'ifscode' => $newData['bank_ifsc'] ?? optional($beneficiary->bank)->ifscode,
                    'bankname' => $newData['bank_name'] ?? optional($beneficiary->bank)->bankname,
                    'bank_branch_name' => $newData['bank_branch_name'] ?? optional($beneficiary->bank)->bank_branch_name,
                    'bankaccountnumber' => $newData['bank_account_number'] ?? optional($beneficiary->bank)->bankaccountnumber,
                    // 'updated_by' => Auth::id(),
                ]
            );
        }
    }

    protected function getStepForRank(int $moduleId, ?int $rank, ?string $errorMessage = null): workflowstepRolemapping
    {
        if ($rank === null) {
            throw new \Exception($errorMessage ?? 'Workflow rank configuration missing.');
        }

        $step = workflowstepRolemapping::where('module_id', $moduleId)
            ->where('rank', $rank)
            ->orderBy('id', 'asc')
            ->first();

        if (! $step) {
            throw new \Exception($errorMessage ?? "Next rank ({$rank}) not found in configuration.");
        }

        return $step;
    }
}
