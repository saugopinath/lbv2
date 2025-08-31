<?php

namespace App\Livewire\ProcessApplication;
use Livewire\Component;
use App\Models\Codemaster;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DraftBeneficiaryPersonal;

class BulkActionModal extends Component
{
    public bool $bulkActionModal = false;
    public array $selectedRows = [];
    public string $bulkActionType = '';
    public string $reason = '';
    public string $remark = '';
    public array $reasons = [];
    public array $availableActions = [];
    public string $bulkActionTypeLabel = 'Select Operation';

    #[On('openBulkActionModal')]
    public function openModal(array $selectedIds = [])
    {
        $this->reset(['bulkActionType', 'reason', 'remark', 'availableActions', 'bulkActionTypeLabel']);
        $this->selectedRows = $selectedIds;

        $user = Auth::user();

        if ($user->hasRole(['Verifier', 'Delegated Verifier'])) {

            $this->availableActions = [
                'V' => 'Verify',
                'R' => 'Reject',
                'T' => 'Revert',
            ];
        } elseif ($user->hasRole(['Approver', 'Delegated Approver'])) {

            $this->availableActions = [
                'A' => 'Approve',
                'R' => 'Reject',
                'T' => 'Revert',
            ];
        }


        $this->bulkActionModal = true; //render again
    }

    public function updatedBulkActionType($value)
    {
        if (in_array($value, ['R', 'T'])) {
            $this->reasons = Codemaster::where('parent_id', 12)
                ->orderBy('id', 'asc')
                ->pluck('name', 'id')
                ->toArray();
        } else {
            $this->reasons = [];
            $this->reason = '';
            $this->remark = '';
        }
    }





    public function performBulkAction()
    {
        $this->validate([
            'bulkActionType' => 'required|in:V,A,R,T',
            'reason' => in_array($this->bulkActionType, ['R', 'T']) ? 'required' : 'nullable',
            'remark' => 'nullable|string|max:255',
        ]);

        $successMessage = 'Action performed successfully!';
        DB::transaction(function () {


            $approverRoleId = Codemaster::getIdByCode(23);

            if ($this->bulkActionType === 'V') {

                DraftBeneficiaryPersonal::whereIn('id', $this->selectedRows)
                    ->update(['next_level_role_id' => $approverRoleId]);

            }
            // } else ($this->bulkActionType === 'A') {


            // }
        });


        $this->bulkActionModal = false;
        // $this->reset(['bulkActionType', 'reason', 'remark', 'selectedRows']);


        // $this->dispatch('notify', 'Action performed successfully!');
        // $this->dispatch('refreshTable'); // Rappasoft tables listen for this event
        $this->dispatch('actionPerformedAndRedirect');
    }
    public function render()
    {
        return view('livewire.process-application.bulk-action-modal');
    }
}