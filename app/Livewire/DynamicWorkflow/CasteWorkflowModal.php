<?php

namespace App\Livewire\DynamicWorkflow;

use App\Helpers\FormOptionHelper;
use App\Models\CasteModificationInfo;
use App\Models\Scheme;
use App\Models\workflowstepRolemapping;
use App\Services\CasteWorkflowService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class CasteWorkflowModal extends Component
{
    public $isOpen = false;

    public $selectedRequest = null;

    public $remark;

    public $SchemeName;

    public $button_status;

    public $selectedAction;

    public $stepLabel;

    public $actions = [];

    #[On('openCasteWorkflowModal')]
    public function openModal($requestId, $scheme_id, $module_id)
    {
        $this->selectedRequest = CasteModificationInfo::with(['module', 'step.label', 'step.role'])
            ->where('scheme_id', $scheme_id)
            ->where('module_id', $module_id)
            ->find($requestId);
        // dd($this->selectedRequest->toSql(), $this->selectedRequest->getBindings(), $requestId);
        if (! $this->selectedRequest) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Request not found',
            ]);

            return;
        }
        $step = workflowstepRolemapping::with(['label', 'role'])
            ->where('rank', $this->selectedRequest->current_rank)
            ->where('module_id', $this->selectedRequest->module_id)
            ->where('scheme_id', $this->selectedRequest->scheme_id)
            ->first();
        $this->SchemeName = Scheme::where('id', $scheme_id)->first()->name;
        if ($step) {
            $this->selectedRequest->setRelation('step', $step);
        }
        $this->button_status = ($step && $step->is_final_step == 1) ? 1 : 0;
        $this->stepLabel = $step->label->label_name ?? 'Approve';
        $this->actions = [
            'approve' => $this->stepLabel,
            'reject' => 'Reject',
            'revert' => 'Revert',
        ];
        $this->remark = null;
        $this->selectedAction = null;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->selectedRequest = null;
        $this->remark = null;
        $this->selectedAction = null;
        $this->stepLabel = null;
        $this->actions = [];
    }

    public function processAction($action)
    {
        // dd($action);
        $this->selectedAction = $action;
        $this->validate([
            'selectedAction' => 'required|in:approve,reject,revert',
            'remark' => 'required',
        ]);

        if (! $this->selectedRequest) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'No request selected',
            ]);

            return;
        }

        DB::beginTransaction();
        try {
            $service = new CasteWorkflowService;

            switch ($action) {
                case 'approve':
                    $result = $service->approve($this->selectedRequest->id, $this->remark);
                    break;
                case 'reject':
                    $result = $service->reject($this->selectedRequest->id, $this->remark);
                    break;
                case 'revert':
                    $result = $service->revert($this->selectedRequest->id, $this->remark);
                    break;
                default:
                    throw new \Exception('Invalid action');
            }

            DB::commit();

            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => $result['message'] ?? 'Action successful',
            ]);
            $this->closeModal();
            $this->dispatch('refreshDatatable');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function getGroupedChanges()
    {
        if (! $this->selectedRequest) {
            return [];
        }

        $newData = $this->selectedRequest->new_data ?? [];
        $oldData = $this->selectedRequest->old_data ?? [];
        $grouped = [];

        if (! empty($newData)) {
            $grouped['Caste Changes'] = [];
            foreach ($newData as $field => $newValue) {
                $oldVal = $oldData[$field] ?? 'N/A';
                $newVal = $newValue;

                if ($field == 'caste') {
                    $oldVal = FormOptionHelper::label('Caste', $oldVal);
                    $newVal = FormOptionHelper::label('Caste', $newVal);
                }

                $grouped['Caste Changes'][] = [
                    'label' => str_replace(['_', 'ifsc'], [' ', 'IFSC'], (string) $field),
                    'old' => $oldVal,
                    'new' => $newVal,
                ];
            }
        }

        return $grouped;
    }

    public function render()
    {
        return view('livewire.dynamic-workflow.caste-workflow-modal', [
            'groupedChanges' => $this->getGroupedChanges(),
        ]);
    }
}
