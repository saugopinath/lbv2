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
            } catch (\Exception $e) {
                $userRoleId = 0;
            }
        }

        // ব্যাকআপ হিসেবে ম্যাপিং টেবিল থেকে নেয়া (যদি সেশনে না থাকে)
        if (!$userRoleId) {
            $userRoleId = (int) \App\Models\UserRoleSchemeOfficeMapping::where('user_id', Auth::id())
                ->where('is_active', 1)
                ->value('role_id') ?? 0;
        }

        // রিক্যুয়েস্টগুলো লোড করা—যেখানে বর্তমান ধাপটি ইউজারের রোলের সাথে ম্যাচ করে
        // এখানে কোনো 'pending' বা 'approved' শব্দ আমরা ব্যবহার করছি না
        $this->requests = DynamicWorkflowRequest::whereNotNull('current_step_id')
            /* ->whereHas('step', function ($query) use ($userRoleId) {
                $query->where('role_id', $userRoleId);
            }) */
            ->with(['module', 'step.label', 'step.role'])
            ->get();
    }

    public function viewDetails($requestId)
    {
        $this->selectedRequest = DynamicWorkflowRequest::with(['module', 'step'])->find($requestId);
    }

    public function processAction($action)
    {
        $this->validate(['remark' => 'required|min:5']);

        $request = $this->selectedRequest;
        $currentStep = $request->step;

        DB::beginTransaction();
        try {
            $service = new \App\Services\DynamicWorkflowService();

            if ($action == 'approve') {
                $result = $service->approve($request->id, $this->remark);
            } elseif ($action == 'revert') {
                $result = $service->revert($request->id, $this->remark);
            } elseif ($action == 'reject') {
                $result = $service->reject($request->id, $this->remark);
            }

            DB::commit();
            $this->dispatch('toast', 'success', $result['message'] ?? 'Action saved!');
            $this->selectedRequest = null;
            $this->loadRequests();
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            $this->dispatch('toast', 'error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dynamic-workflow.process-workflow');
    }
}
