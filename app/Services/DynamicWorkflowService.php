<?php

namespace App\Services;

use App\Models\BeneficiaryBankDetail;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\DynamicWorkflowRequest;
use App\Models\workflowstepRolemapping;
use Illuminate\Support\Facades\Auth;

class DynamicWorkflowService
{

    public function initiateRequest($moduleId, $refId, $oldData, $newData, $changedFields = [])
    {
        // 🔥 first step find
        $firstStep = workflowstepRolemapping::where('module_id', $moduleId)
            ->orderBy('rank', 'asc')
            ->orderBy('id', 'asc')
            ->first();
        if (!$firstStep) {
            throw new \Exception('Workflow steps not configured for this module.');
        }
        return DynamicWorkflowRequest::create([
            'module_id'       => $moduleId,
            'ref_id'          => $refId,
            'current_rank'    => $firstStep->next_label_role_id, // 🔥 workflow engine এর জন্য current_rank-এ next_label_role_id সেট করা হলো
            'current_step_id' => $firstStep->workflow_step_id, // 🔥 workflow engine এর জন্য current_step_id-এ workflow_step_id সেট করা হলো
            'old_data'        => $oldData,
            'new_data'        => $newData,
            'changed_fields'  => $changedFields,
            // ❌ NO STATUS
            // 'status' => ❌ remove
            'created_by'      => Auth::id(),
        ]);
    }


    public function approve($requestId, $remark)
    {
        // dd($requestId, $remark);
        $request = DynamicWorkflowRequest::findOrFail($requestId);

        $currentStep = workflowstepRolemapping::where('module_id', $request->module_id)
            ->where('rank', $request->current_rank)
            ->first();
        // dd($currentStep);
        if (!$currentStep) {
            throw new \Exception('Step not found');
        }
        if ($currentStep->is_final_step) {
            // dd($currentStep);
            // BeneficiaryPersonalDetail::where('application_id', $request->ref_id)
            //     ->update($request->new_data);
            // 👉 workflow শেষ → hide
            $this->applyApprovedChanges($request);
                // 👉 workflow শেষ → hide করার জন্য current_rank কে null করে দিচ্ছি
            $request = DynamicWorkflowRequest::find($request->id);
            // dd($request);
            // 👉 FINAL UPDATE
            // $request->update([
            //     'current_rank' => null
            // ]);
            $request->update([
                'current_rank' => $currentStep->next_label_role_id,
                'current_step_id' => $currentStep->workflow_step_id
            ]);
            // dd($update);
            return ['message' => 'Final Approved & Data Updated'];
        }
        $request->update([
            'current_rank' => $currentStep->next_label_role_id,
            'current_step_id' => $currentStep->workflow_step_id
        ]);
        return ['message' => 'Approved & Forwarded'];
    }
    public function revert($requestId, $remark = '')
    {
        $request = DynamicWorkflowRequest::findOrFail($requestId);
        $currentStep = $this->getStepForRank($request->module_id, $request->current_rank);

        // পেছনের র্যাঙ্ক-এ পাঠানো (same_label_role_id ব্যবহার করে)
        $prevStep = $this->getStepForRank(
            $request->module_id,
            $currentStep->same_label_role_id,
            'Revert target rank configuration missing.'
        );

        $request->update([
            'current_rank' => $prevStep->rank,
            'current_step_id' => $prevStep->id,
        ]);

        return ['status' => 'reverted', 'message' => 'Request reverted to rank ' . $prevStep->rank];
    }

    public function reject($requestId, $remark = '')
    {
        $request = DynamicWorkflowRequest::findOrFail($requestId);
         $currentStep = workflowstepRolemapping::where('module_id', $request->module_id)
            ->where('rank', $request->current_rank)
            ->first();
          $request->update([
                'current_rank' => -100, // 🔥 REJECTED STATE (workflow engine এর জন্য -100 র‍্যাঙ্ক ব্যবহার করা হলো)
                'current_step_id' => $currentStep->workflow_step_id
            ]);

        return ['status' => 'rejected', 'message' => 'Request rejected and closed.'];
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
                    'updated_by' => Auth::id(),
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
