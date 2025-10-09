<?php

namespace App\Livewire;

use App\Helpers\CheckAuthHelper;
use Livewire\Component;
use App\Models\CodeMaster;
use Illuminate\Support\Facades\Crypt;

class CasteModificationFilters extends Component
{
    public string $applicantStatus = '';
    public string $casteId = '';
    public array $statusOptions = [];
    public $casteOptions;

    protected $rules = [
        'applicantStatus' => 'required|string',
    ];
    public function mount(): void
    {
        $this->casteOptions = CodeMaster::where('code', 17)->first()
            ->children()
            ->pluck('name', 'id');

        $roleId = session('lgd_session') ? Crypt::decryptString(session('lgd_session.role_id')) : null;

        if (CheckAuthHelper::isVerifier()) {
            $this->statusOptions = [
                'PL'  => 'Pending List',
                'APL' => 'Approval Pending List',
                'AL'  => 'Approved List',
                'RL'  => 'Reverted List',
            ];
        } elseif (CheckAuthHelper::isOperator()) {
            $this->statusOptions = [
                'VPL' => 'Verification Pending List',
                'APL' => 'Approval Pending List',
                'AL'  => 'Approved List',
                'RL'  => 'Reverted List',
            ];
        } elseif (CheckAuthHelper::isApprover()) {
            $this->statusOptions = [
                'PL' => 'Pending List',
                'AL' => 'Approved List',
                'RL' => 'Reverted List',
            ];
        }
    }

    public function applyFilters()
    {
        try {

            $this->validate();
            $this->dispatch('filtersApplied', [
                'status' => $this->applicantStatus,
                'caste'  => $this->casteId,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->resettable();
            throw $e;
        }
    }


    public function resetFilters()
    {
        $this->resetValidation();
        $this->applicantStatus = '';
        $this->casteId = '';
        $this->dispatch('resetFilters');
    }
    public function resettable()
    {
        $this->dispatch('resettable');
    }

    public function render()
    {
        $this->dispatch('hideLoader');
        return view('livewire.caste-modification-filters');
    }
}
