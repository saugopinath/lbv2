<?php

namespace App\Services;

use App\Models\DynamicWorkflowRequest;
use App\Models\workflowstepRolemapping;
use App\Models\BeneficiaryPersonalDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DynamicWorkflowService
{
    /**
     * অপারেটর যখন প্রথম রিকোয়েস্ট সাবমিট করে
     */
    public function initiateRequest($moduleId, $refId, $oldData, $newData)
    {
        // DynamicWorkflowStep এর বদলে workflowstepRolemapping ব্যবহার করা হলো
        $firstStep = workflowstepRolemapping::where('module_id', $moduleId)
            ->orderBy('rank', 'asc')
            ->first();

        if (!$firstStep) {
            throw new \Exception("Workflow steps not configured for this module.");
        }
        return DynamicWorkflowRequest::create([
            'module_id' => $moduleId,
            'ref_id' => $refId,
            'current_rank' => $firstStep->rank,
            'current_step_id' => $firstStep->id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'status' => 'pending',
            'created_by' => Auth::id()
        ]);
    }

    /**
     * এপ্রুভ বা ফরওয়ার্ড একশন
     */
    public function approve($requestId, $remark = '')
    {
        $request = DynamicWorkflowRequest::findOrFail($requestId);
        $currentStep = workflowstepRolemapping::findOrFail($request->current_step_id);

        // যদি নেক্সট র‍্যাঙ্ক ০ হয়, তবে এটিই ফাইনাল স্টেপ হতে পারে অথবা কোড রিড করবে handles
        if ($currentStep->is_final_step || $currentStep->next_label_role_id == 0) {
            $beneficiary = BeneficiaryPersonalDetail::findOrFail($request->ref_id);
            $beneficiary->update($request->new_data);
            
            $request->update(['status' => DynamicWorkflowRequest::STATUS_APPROVED]);
            return ['status' => 'final', 'message' => 'Workflow completed and data updated.'];
        }

        // পরবর্তী র্যাঙ্ক-এ পাঠানো
        $nextStep = workflowstepRolemapping::where('module_id', $request->module_id)
            ->where('rank', $currentStep->next_label_role_id)
            ->first();

        if (!$nextStep) {
             throw new \Exception("Next rank (" . $currentStep->next_label_role_id . ") not found in configuration.");
        }

        $request->update([
            'current_rank' => $nextStep->rank,
            'current_step_id' => $nextStep->id
        ]);

        return ['status' => 'forwarded', 'message' => 'Request forwarded to rank ' . $nextStep->rank];
    }

    /**
     * রিভার্ট (পেছনের লেভেলে পাঠানো)
     */
    public function revert($requestId, $remark = '')
    {
        $request = DynamicWorkflowRequest::findOrFail($requestId);
        $currentStep = workflowstepRolemapping::findOrFail($request->current_step_id);

        // পেছনের র্যাঙ্ক-এ পাঠানো (same_label_role_id ব্যবহার করে)
        $prevStep = workflowstepRolemapping::where('module_id', $request->module_id)
            ->where('rank', $currentStep->same_label_role_id)
            ->first();

        if (!$prevStep) {
            throw new \Exception("Revert target rank configuration missing.");
        }

        $request->update([
            'current_rank' => $prevStep->rank,
            'current_step_id' => $prevStep->id
        ]);

        return ['status' => 'reverted', 'message' => 'Request reverted to rank ' . $prevStep->rank];
    }

    public function reject($requestId, $remark = '')
    {
        $request = DynamicWorkflowRequest::findOrFail($requestId);
        $request->update(['status' => 'rejected']);
        return ['status' => 'rejected', 'message' => 'Request rejected and closed.'];
    }
}
