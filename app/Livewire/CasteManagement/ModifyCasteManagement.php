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

class ModifyCasteManagement extends Component
{
    public bool $schemeData = false;

    public bool $showTable = false;

    public ?int $schemeId = null;

    public ?string $schemeName = null;

    public ?string $moduleCode = null;

    public ?string $moduleName = null;

    public ?int $selectedModuleId = null;   // This will store the scheme_module_id

    public ?string $selectedModuleCode = null;

    public ?string $selectedModuleName = null;

    public ?int $selectedStepId = null;

    public ?int $confirmedStepId = null;

    public ?string $selectedStepName = null;

    public ?string $stage = null;

    public $mainModuleId = null;

    public array $stepOptions = []; // Changed from moduleOptions to stepOptions

    public ?int $userRoleId = null;

    public $filter_condition = [];

    public $requestModuleCode = null;

    public $RoleId = null;

    public $currentRoleId = null;

    public $moduleSchemeId;

    public $oldData = [];

    public $newData = [];

    public $items = [];

    public $beneficiary = null;

    public $showFields = false;

    public $casteOptions = [];

    public $doctype = [];

    protected $listeners = [
        'beneficiary-search' => 'handleSearch',
        'reset-beneficiary-search' => 'resetSearch',
    ];

    public function mount($moduleCode = null, $moduleName = null, $mainModuleId = null)
    {
        if ($moduleCode) {
            $this->moduleCode = $moduleCode;
        }
        if ($moduleName) {
            $this->moduleName = $moduleName;
        }
        if ($mainModuleId) {
            $this->mainModuleId = $mainModuleId;
        }
        $selectLgd = session('lgd_session');
        $this->requestModuleCode = $moduleCode;
        $this->currentRoleId = Crypt::decryptString($selectLgd['role_id']);
        if (! empty($selectLgd['district_id'])) {
            $this->filter_condition['created_by_dist_code'] = Crypt::decryptString($selectLgd['district_id']);
        }
        if (! empty($selectLgd['block_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($selectLgd['block_id']);
        }
        if (! empty($selectLgd['subdivision_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($selectLgd['subdivision_id']);
        }
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

    public function handleSearch($data)
    {
        // নতুন search হলে আগের beneficiary form reset করো
        $this->beneficiary = null;
        $this->showFields = false;
        $this->reset(['oldData', 'newData']);

        if (empty($data['results'])) {
            $this->items = [];
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'No matching approved beneficiary found.',
            ]);

            return;
        }
        $applicationIds = collect($data['results'])->pluck('application_id')->toArray();
        $this->moduleSchemeId = $data['results'][0]['scheme_id'];

        $Mainmodule = DynamicWorkflowModule::where('module_code', $this->requestModuleCode)->first();
        if (! $Mainmodule) {
            abort(404, 'Module not found');
        }
        $module = DynamicWorkflowSchemeModule::where('module_id', $Mainmodule->id)->where('scheme_id', $this->moduleSchemeId)->first();
        if (! $module) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Steps are not configured for this scheme!',
            ]);

            return;
        }
        $firstStep = WorkflowsteproleMapping::where([
            'module_id' => $module->id,
            'scheme_id' => $this->moduleSchemeId,
            'role_id' => $this->RoleId,
        ])
            ->orderBy('rank', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        if (! $firstStep) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'You are not authorized to initiate this workflow or steps are not configured.',
            ]);

            return;
        }

        $SubmittedRequest = CasteModificationInfo::where('application_id', $applicationIds)
            ->where('scheme_id', $this->moduleSchemeId)
            ->where('module_id', $module->id)
            ->whereNotIn('current_rank', [-1, 0])
            ->get();

        if ($SubmittedRequest->count() > 0) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Request already Pending!',
            ]);

            return;
        }

        $this->items = BeneficiaryPersonalDetail::query()
            ->select(['application_id', 'beneficiary_id', 'scheme_id', 'beneficiary_name', 'caste', 'caste_cer_no', 'other_details'])
            ->with([
                'contact:beneficiary_id,application_id,scheme_id,district_id,rural_urban,blockurban,gpward',
                'bank:beneficiary_id,application_id,scheme_id,bankaccountnumber,ifscode',
            ])
            ->whereIn('application_id', $applicationIds)
            ->get()
            ->map(fn ($item) => [
                'application_id' => $item->application_id,
                'beneficiary_id' => $item->beneficiary_id,
                'applicant_name' => $item->beneficiary_name,
                'caste_name' => FormOptionHelper::label('Caste', $item->caste),
                'caste_no' => $item->caste_cer_no,
                'mobile_no' => $item->other_details['mobile_no'] ?? '-',
                'address' => optional($item->contact)->getFullAddress() ?? 'N/A',
                'bank_account' => optional($item->bank)->bankaccountnumber ?? '-',
                'ifsc' => optional($item->bank)->ifscode ?? '-',
                'scheme_id' => $item->scheme_id,
            ])->toArray();
    }

    public function resetSearch()
    {
        $this->items = [];
        $this->beneficiary = null;
        $this->showFields = false;
        $this->reset(['oldData', 'newData']);
    }

    public function selectBeneficiary($appId)
    {
        $this->beneficiary = BeneficiaryPersonalDetail::where('application_id', $appId)->first();
        if (! $this->beneficiary) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Beneficiary not found']);

            return;
        }
        $this->items = [];  // table hide করো
        $this->moduleSchemeId = $this->beneficiary->scheme_id;
        $this->showFields = true;

        $this->oldData = [
            'caste' => $this->beneficiary->caste,
            'caste_certificate_no' => $this->beneficiary->caste_cer_no,
        ];
        $this->newData = $this->oldData;
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
        return view('livewire.caste-management.modify-caste-management');
    }
}
