<?php

namespace App\Livewire\CasteManagement;

use App\Helpers\FormOptionHelper;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\BeneficiaryTemEnclosure;
use App\Models\CasteModificationInfo;
use App\Models\Codemaster;
use App\Models\DynamicWorkflowLabel;
use App\Models\DynamicWorkflowModule;
use App\Models\DynamicWorkflowSchemeModule;
use App\Models\WorkflowsteproleMapping;
use App\Services\DynamicWorkflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UpdateRevertCaste extends Component
{
    public ?int $modificationId = null;
    public $mainModuleId = null;
    public $RoleId = null;
    public $moduleSchemeId;
    public $oldData = [];
    public $newData = [];
    public $beneficiary = null;
    public $casteOptions = [];
    public $doctype = [];

    public function mount()
    {
        $appIdEnc = request()->query('application_id');
        $schemeEnc = request()->query('Scheme');

        if ($appIdEnc && $schemeEnc) {
            try {
                $applicationId = Crypt::decryptString($appIdEnc);
                $schemeId = Crypt::decryptString($schemeEnc);
                $this->moduleSchemeId = $schemeId;
                $this->selectBeneficiary($applicationId);
            } catch (\Exception $e) {
                // Ignore or handle
            }
        }

        $selectLgd = session('lgd_session');
        if (! empty($selectLgd['role_id'])) {
            $this->RoleId = Crypt::decryptString($selectLgd['role_id']);
        }
        $this->casteOptions = FormOptionHelper::get('Caste');
        $this->doctype = [Codemaster::getIdByCode(162)];
    }

    public function getIsSCSTProperty()
    {
        if (empty($this->newData['caste']) || empty($this->casteOptions)) {
            return false;
        }
        $scID = array_search('SC', $this->casteOptions);
        $stID = array_search('ST', $this->casteOptions);

        return in_array((int) $this->newData['caste'], array_filter([$scID, $stID]));
    }



    public function selectBeneficiary($appId)
    {
        $this->beneficiary = BeneficiaryPersonalDetail::where('application_id', $appId)->first();
        if (! $this->beneficiary) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Beneficiary not found']);

            return;
        }
        
        $this->moduleSchemeId = $this->beneficiary->scheme_id;

        $pendingRequest = CasteModificationInfo::where('application_id', $appId)
            ->where('scheme_id', $this->moduleSchemeId)
            ->latest()
            ->first();

        if ($pendingRequest) {
            $this->modificationId = $pendingRequest->id;
            $this->oldData = is_string($pendingRequest->old_data) ? json_decode($pendingRequest->old_data, true) : $pendingRequest->old_data;
            $this->newData = is_string($pendingRequest->new_data) ? json_decode($pendingRequest->new_data, true) : $pendingRequest->new_data;
            
            $sm = DynamicWorkflowSchemeModule::find($pendingRequest->module_id);
            if ($sm) {
                $this->mainModuleId = $sm->module_id;
            }
        } else {
            $this->oldData = [
                'caste' => $this->beneficiary->caste,
                'caste_certificate_no' => $this->beneficiary->caste_cer_no,
            ];
            $this->newData = $this->oldData;
        }
    }

    public function submitRequest(DynamicWorkflowService $workflowService)
    {
        if (! $this->beneficiary) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'No beneficiary selected!']);

            return;
        }
        $scID = array_search('SC', $this->casteOptions);
        $stID = array_search('ST', $this->casteOptions);
        $requiredCasteIds = array_filter([$scID, $stID]);
        if (in_array((int) $this->newData['caste'], $requiredCasteIds) && empty($this->newData['caste_certificate_no'])) {
            $this->addError('newData.caste_certificate_no', 'Caste certificate number is required for SC/ST.');

            return;
        }
        $sm = DynamicWorkflowSchemeModule::where('module_id', $this->mainModuleId)
            ->where('scheme_id', $this->moduleSchemeId)
            ->first();

        if (! $sm) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Steps are not configured for this scheme!']);

            return;
        }
        $firstStep = WorkflowsteproleMapping::where([
            'module_id' => $sm->id,
            'scheme_id' => $this->moduleSchemeId,
            'role_id' => $this->RoleId,
        ])
            ->orderBy('rank', 'asc')
            ->orderBy('id', 'asc')
            ->first();
        if (! $firstStep) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Not authorized to initiate workflow.']);

            return;
        }
        $uploadedDocsCount = BeneficiaryTemEnclosure::where('application_id', $this->beneficiary->application_id)
            ->where('scheme_id', $this->moduleSchemeId)
            ->whereIn('document_type', $this->doctype)
            ->count();

        if (in_array((int) $this->newData['caste'], $requiredCasteIds) && $uploadedDocsCount < 1) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Please upload the required caste document.']);

            return;
        }

        DB::beginTransaction();
        try {
            $optype = DynamicWorkflowLabel::getOpTypeId($firstStep->workflow_step_id);
            $logdetails = AcceptRejectInfo::create([
                'application_id' => $this->beneficiary->application_id,
                'beneficiary_id' => $this->beneficiary->beneficiary_id,
                'scheme_id' => $this->moduleSchemeId,
                'ip_address' => request()->ip(),
                'user_id' => Auth::id(),
                'browser' => request()->header('User-Agent'),
                'model_name' => request()->path(),
                'op_type' => $optype,
            ]);
            
            if ($this->modificationId) {
                $UpdateCaste = CasteModificationInfo::find($this->modificationId);
                $UpdateCaste->update([
                    'old_data' => $this->oldData,
                    'new_data' => $this->newData,
                    'caste_request_type' => $this->newData['caste'],
                    'next_level_requested_id' => $firstStep->next_label_role_id,
                    'request_id' => $logdetails->id,
                    'module_id' => $sm->id,
                    'current_step_id' => $firstStep->workflow_step_id,
                    'current_rank' => $firstStep->next_label_role_id,
                ]);
            } else {
                $UpdateCaste = CasteModificationInfo::create([
                    'application_id' => $this->beneficiary->application_id,
                    'beneficiary_id' => $this->beneficiary->beneficiary_id,
                    'scheme_id' => $this->moduleSchemeId,
                    'old_data' => $this->oldData,
                    'new_data' => $this->newData,
                    'caste_request_type' => $this->newData['caste'],
                    'next_level_requested_id' => $firstStep->next_label_role_id,
                    'request_id' => $logdetails->id,
                    'module_id' => $sm->id,
                    'current_step_id' => $firstStep->workflow_step_id,
                    'current_rank' => $firstStep->next_label_role_id,
                    'created_by' => Auth::id(),
                ]);
            }
            
            if ($logdetails && $UpdateCaste) {
                DB::commit();
                $this->dispatch('toastr', ['type' => 'success', 'message' => 'Modification request submitted successfully!']);

                return redirect()->route('caste-management');
            } else {
                DB::rollBack();
                $this->dispatch('toastr', ['type' => 'error', 'message' => 'Failed to submit modification request!']);

                return;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toastr', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.caste-management.update-revert-caste');
    }
}
