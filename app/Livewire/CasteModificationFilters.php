<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CodeMaster;
use Illuminate\Support\Facades\Crypt;

class CasteModificationFilters extends Component
{
    public string $applicantStatus = '';
    public string $casteId = '';

    public array $statusOptions = [];
    public $casteOptions;

    public function mount(): void
    {
        // Fetch caste options from CodeMaster table
        $this->casteOptions = CodeMaster::where('code', 17)->first()
            ->children()
            ->pluck('name', 'id');


        // Role-based status options
        $roleId = session('lgd_session') ? Crypt::decryptString(session('lgd_session.role_id')) : null;

        if (in_array($roleId, [6,7])) { // Verifier 
            $this->statusOptions = [
                'PL'   => 'Pending List',
                'APL'    => 'Approval Pending',
                'AL'            => 'Approved ',
                'RL'            => 'Reverted',
                  
            ];
        } elseif (in_array($roleId, [8, 9])) { // Operator 
            $this->statusOptions = [
                'VPL' => 'Verification Pending',
                'APL'    => 'Approval Pending',
                
                'AL'            => 'Approved',
                'RL'            => 'Reverted',
            ];

        }elseif(in_array($roleId, [4, 5])) { // Approver
            $this->statusOptions = [
                'PL'   => 'Pending List',
                'AL'  => 'Approved',
                'RL'  => 'Reverted',
            ];
        }
    }

    public function applyFilters()
    {
        // dd('fefeve');
        // dd($this->applicantStatus);
        $this->dispatch('filtersApplied', [
            'status' => $this->applicantStatus,
            'caste'  => $this->casteId,
            // 'showTable' =>true,
        ]);
        // dump($this->casteId);
        // dd($this->applicantStatus);
    }

    public function resetFilters()
    {
        // dd('nsjfns');
        $this->applicantStatus = '';
        $this->casteId = '';
        $this->dispatch('filtersApplied', [
            'status' => '',
            'caste'  => '',
            // 'showTable' =>false,
        ]);
    }

    public function render()
    {
        return view('livewire.caste-modification-filters');
    }
}
