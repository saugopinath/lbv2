<?php

namespace App\Livewire\DynamicWorkflow;

use App\Models\DynamicWorkflowRequest;
use App\Models\workflowstepRolemapping;
use App\Models\DynamicWorkflowLog;
use App\Models\BeneficiaryPersonalDetail;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class
ProcessWorkflow extends Component
{
    public $requests = [];
    public $selectedRequest = null;
    public $remark;

    public function mount()
    {
        $this->loadRequests();
    }

    public function loadRequests()
{
    $lgd_session = session('lgd_session');
    $userRoleId = 0;

    if (!empty($lgd_session['role_id'])) {
        try {
            $userRoleId = (int) \Illuminate\Support\Facades\Crypt::decryptString($lgd_session['role_id']);
        } catch (\Exception $e) {}
    }

    if (!$userRoleId) {
        $userRoleId = (int) \App\Models\UserRoleSchemeOfficeMapping::where('user_id', Auth::id())
            ->where('is_active', 1)
            ->value('role_id') ?? 0;
    }

    // 🔥 user এর সব rank
    $userRanks = workflowstepRolemapping::where('role_id', $userRoleId)
        ->pluck('rank')
        ->toArray();

    if (empty($userRanks)) {
        $this->requests = [];
        return;
    }

    // ✅ ONLY current_rank দিয়ে filter
    $this->requests = DynamicWorkflowRequest::whereIn('current_rank', $userRanks)
        ->with(['module', 'step.label', 'step.role'])
        ->get();
}
    public function viewDetails($requestId)
    {
        $this->selectedRequest = DynamicWorkflowRequest::with(['module', 'step.label', 'step.role'])
            ->find($requestId);
    }

    public function processAction($action)
    {
        $this->validate([
            'remark' => 'required|min:5'
        ]);

        if (!$this->selectedRequest) {
            $this->dispatch('toast', 'error', 'No request selected');
            return;
        }

        DB::beginTransaction();
        try {
            $service = new \App\Services\DynamicWorkflowService();

            switch ($action) {
                case 'approve':
                    $result = $service->approve($this->selectedRequest->id, $this->remark);
                    break;

                case 'revert':
                    $result = $service->revert($this->selectedRequest->id, $this->remark);
                    break;

                case 'reject':
                    $result = $service->reject($this->selectedRequest->id, $this->remark);
                    break;

                default:
                    throw new \Exception('Invalid action');
            }

            DB::commit();

            $this->dispatch('toast', 'success', $result['message'] ?? 'Action successful');

            // 🔥 reset
            $this->selectedRequest = null;
            $this->remark = null;

            // 🔥 reload list
            $this->loadRequests();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', 'error', $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.dynamic-workflow.process-workflow');
    }
}
