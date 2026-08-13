<?php

namespace App\Services;

use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\BeneficiaryTemEnclosure;
use App\Models\CasteModificationInfo;
use App\Models\Codemaster;
use App\Models\DynamicWorkflowLabel;
use App\Models\workflowstepRolemapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

// use App\Helpers\DuplicateChecker;

class CasteWorkflowService
{
    public $doc_type;

    public function __construct()
    {
        $this->doc_type = Codemaster::getIdByCode(162);
    }

    private function getCurrentUserRoleId()
    {
        $lgd_session = session('lgd_session');
        if (! empty($lgd_session['role_id'])) {
            try {
                return (int) Crypt::decryptString($lgd_session['role_id']);
            } catch (\Exception $e) {
                return 0;
            }
        }

        return 0;
    }

    // public function initiateRequest($moduleId, $refId, $schemeId, $oldData, $newData, $changedFields = [])
    // {
    //     // dd($moduleId, $refId, $schemeId, $oldData, $newData, $changedFields);
    //     $roleId = $this->getCurrentUserRoleId();
    //     DB::beginTransaction();
    //     try {
    //         $firstStep = workflowstepRolemapping::where([
    //             'module_id' => $moduleId,
    //             'scheme_id' => $schemeId,
    //             'role_id' => $roleId,
    //         ])
    //             ->orderBy('rank', 'asc')
    //             ->orderBy('id', 'asc')
    //             ->first();
    //         // dd($firstStep);
    //         if (! $firstStep) {
    //             // dd($firstStep);
    //             throw new \Exception('You are not authorized to initiate this workflow or steps are not configured.');
    //         }
    //         $optype = DynamicWorkflowLabel::getOpTypeId($firstStep->workflow_step_id);
    //         // dd($optype);
    //         $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $refId)->value('beneficiary_id');
    //         $parentId = AcceptRejectInfo::where('application_id', $refId)->latest('id')->value('id');
    //         $log = AcceptRejectInfo::create([
    //             'application_id' => $refId,
    //             'beneficiary_id' => $beneficiary_id,
    //             'scheme_id' => $schemeId,
    //             'user_id' => Auth::id(),
    //             'ip_address' => request()->ip(),
    //             'browser' => request()->userAgent(),
    //             'op_type' => $optype,
    //             'model_name' => optional($firstStep->module)->module_name ?? 'null',
    //             'parent_id' => $parentId,
    //             'old_value' => $oldData,
    //             'new_value' => $newData,
    //         ]);

    //         $request = CasteModificationInfo::create([
    //             'module_id' => $moduleId,
    //             'ref_id' => $refId,
    //             'scheme_id' => $schemeId,
    //             'current_rank' => $firstStep->next_level_role_id,
    //             'current_step_id' => $firstStep->workflow_step_id,
    //             'old_data' => $oldData,
    //             'new_data' => $newData,
    //             'changed_fields' => $changedFields,
    //             'created_by' => Auth::id(),
    //         ]);
    //         if ($log && $request) {
    //             DB::commit();

    //             return $request;
    //         } else {
    //             DB::rollBack();

    //             return false;
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

    public function approve($requestId, $remark)
    {
        DB::beginTransaction();
        $roleId = $this->getCurrentUserRoleId();
        try {
            $request = CasteModificationInfo::findOrFail($requestId);
            $currentStep = workflowstepRolemapping::where('module_id', $request->module_id)
                ->where('rank', $request->current_rank)
                ->where('role_id', $roleId)
                ->where('scheme_id', $request->scheme_id)
                ->first();

            if (! $currentStep) {
                throw new \Exception('Authorized workflow step not found for your role.');
            }

            $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $request->application_id)->value('beneficiary_id');
            $parentId = AcceptRejectInfo::where('application_id', $request->application_id)->latest('id')->value('id');

            $AcceptRejectInfo = AcceptRejectInfo::create([
                'application_id' => $request->application_id,
                'beneficiary_id' => $beneficiary_id,
                'scheme_id' => $request->scheme_id,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'browser' => request()->userAgent(),
                'op_type' => DynamicWorkflowLabel::getOpTypeId($currentStep->workflow_step_id),
                'model_name' => optional($request->module)->module_name ?? 'CasteModificationInfo',
                'revert_reason_remarks' => $remark ?: '',
                'parent_id' => $parentId,
                'old_value' => $request->old_data,
                'new_value' => $request->new_data,
            ]);

            if ($currentStep->is_final_step) {
                $applied = $this->applyApprovedChanges($request);

                if ($applied) {
                    $UpdateRequest = $request->update([
                        'current_rank' => $currentStep->next_level_role_id,
                        'current_step_id' => $currentStep->workflow_step_id,
                        'is_active' => false, 
                        'updated_by' => Auth::id(),
                        'updated_at' => now(),
                    ]);
                    $msg = ['status' => 'success', 'message' => 'Finally Approved & Beneficiary Data Updated'];
                } else {
                    throw new \Exception('Failed to update master beneficiary record.');
                }
            } else {
                $UpdateRequest = $request->update([
                    'current_rank' => $currentStep->next_level_role_id,
                    'current_step_id' => $currentStep->workflow_step_id,
                    'updated_by' => Auth::id(),
                ]);
                $msg = ['status' => 'success', 'message' => 'Processed & Forwarded to the Next Step'];
            }

            if ($AcceptRejectInfo && $UpdateRequest) {
                DB::commit();

                return $msg;
            } else {
                DB::rollBack();

                return ['status' => 'error', 'message' => 'Failed to update request status'];
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function revert($requestId, $remark = '')
    {
        DB::beginTransaction();
        try {
            $request = CasteModificationInfo::findOrFail($requestId);
            $currentStep = $this->getStepForRank($request->module_id, $request->current_rank);

            // $prevStep = $this->getStepForRank(
            //     $request->module_id,
            //     $currentStep->same_level_role_id,
            //     'Revert target rank configuration missing.'
            // );

            $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $request->application_id)->value('beneficiary_id');
            $parentId = AcceptRejectInfo::where('application_id', $request->application_id)->latest('id')->value('id');

            AcceptRejectInfo::create([
                'application_id' => $request->application_id,
                'beneficiary_id' => $beneficiary_id,
                'scheme_id' => $request->scheme_id,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'browser' => request()->userAgent(),
                'op_type' => Codemaster::getIdByCode($request->scheme_id),
                'model_name' => optional($request->module)->module_name ?? 'null',
                'revert_reason_remarks' => $remark ?: 'Reverted to previous step',
                'parent_id' => $parentId,
                'old_value' => $request->old_data,
                'new_value' => $request->new_data,
            ]);

            $request->update([
                'current_rank' => -($request->scheme_id),
            ]);

            DB::commit();

            return ['status' => 'reverted', 'message' => 'Application Reverted Successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // public function reject($requestId, $remark = '')
    // {
    //     DB::beginTransaction();
    //     try {
    //         $request = CasteModificationInfo::findOrFail($requestId);
    //         $currentStep = workflowstepRolemapping::where('module_id', $request->module_id)
    //             ->where('rank', $request->current_rank)
    //             ->first();
    //         $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $request->application_id)->value('beneficiary_id');
    //         $parentId = AcceptRejectInfo::where('application_id', $request->application_id)->latest('id')->value('id');
    //         AcceptRejectInfo::create([
    //             'application_id' => $request->application_id,
    //             'beneficiary_id' => $beneficiary_id,
    //             'scheme_id' => $request->scheme_id,
    //             'user_id' => Auth::id(),
    //             'ip_address' => request()->ip(),
    //             'browser' => request()->userAgent(),
    //             'op_type' => Codemaster::getIdByCode(-1),
    //             'model_name' => optional($request->module)->module_name ?? 'null',
    //             'revert_reason_remarks' => $remark ?: 'Rejected',
    //             'parent_id' => $parentId,
    //             'old_value' => $request->old_data,
    //             'new_value' => $request->new_data,
    //         ]);

    //         $request->update([
    //             'current_rank' => -100,
    //             'current_step_id' => $currentStep->workflow_step_id,
    //         ]);

    //         DB::commit();

    //         return ['status' => 'rejected', 'message' => 'Request rejected and closed.'];
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

    protected function applyApprovedChanges(CasteModificationInfo $request): bool
    {
        return DB::transaction(function () use ($request) {
            $application_id = $request->application_id;
            $scheme_id = $request->scheme_id;
            $beneficiary = BeneficiaryPersonalDetail::where('application_id', $application_id)->firstOrFail();

            if ($beneficiary) {
                // Update Caste Details
                $beneficiary->caste = $request->new_data['caste'] ?? $beneficiary->caste;
                $beneficiary->caste_cer_no = $request->new_data['caste_certificate_no'] ?? $beneficiary->caste_cer_no;

                $temp = BeneficiaryTemEnclosure::where('application_id', $application_id)
                    ->where('document_type', $this->doc_type)
                    ->where('scheme_id', $scheme_id)
                    ->first();

                if ($temp) {
                    $beneficiary->documents()->updateOrCreate(
                        [
                            'application_id' => $application_id,
                            'scheme_id' => $scheme_id,
                            'document_type' => $this->doc_type,
                        ],
                        [
                            'beneficiary_id' => $beneficiary->beneficiary_id,
                            'attched_document' => $temp->attched_document,
                            'document_extension' => $temp->document_extension,
                            'document_mime_type' => $temp->document_mime_type,
                            'ip_address' => request()->ip(),
                            'created_by' => Auth::id(),
                            'updated_at' => now(),
                        ]
                    );
                    $temp->delete();
                } else {
                    $existingDoc = $beneficiary->documents()
                        ->where('application_id', $application_id)
                        ->where('scheme_id', $scheme_id)
                        ->where('document_type', $this->doc_type)
                        ->first();

                    if ($existingDoc) {
                        $existingDoc->delete();
                    }
                }

                if ($beneficiary->save()) {
                    return true;
                } else {
                    throw new \Exception('Failed to update beneficiary personal details.');
                }
            } else {
                return false;
            }
        });
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
