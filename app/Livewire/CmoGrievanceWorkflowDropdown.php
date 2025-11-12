<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Codemaster;
use App\Models\District;

class CmoGrievanceWorkflowDropdown extends Component
{
    public $process_type, $types, $districts, $district;
    public function submit()
    {
        $this->dispatch('processTypeChanged', ['process_type' => $this->process_type, 'district' => $this->district]);
    }
    public function render()
    {
        $user = auth()->user();

        // Step 1: Get all codemasters with parent_short_code = 'redressed_status'
        $codemasters = Codemaster::where('parent_short_code', 'redressed_status')->get();

        // Step 2: Set defaults
        $code = null;
        $id = null;
        $removeCodes = [];
        $removeShortNames = [];

        // Step 3: Role-based configuration
        if ($user->hasAnyRole(['Verifier', 'Delegated Verifier'])) {
            $code = 3301;
            $id = Codemaster::getIdByCode(3301);
            $removeCodes = [3306]; // Verifier removes this
        } elseif ($user->hasAnyRole(['Approver', 'Delegated Approver'])) {
            $code = 3302;
            $id = Codemaster::getIdByCode(3302);
            $removeShortNames = ['marked_but_approval_pending']; // Approver removes this
        } elseif ($user->hasAnyRole(['HOD'])) {
            $this->districts = District::all();
            $code = 3303;
            $id = Codemaster::getIdByCode(3303);
            $removeCodes = [3306, 3304, 3302]; // HOD removes all these
            $removeShortNames = ['marked_and_approved_but_yet_not_send_to_cmo'];
        }

        // Step 4: Update the 'pending' entry dynamically
        $updatedCollection = $codemasters->map(function ($item) use ($code, $id) {
            if (strtolower($item->short_name) === 'pending') {
                $item->id = $id;
                $item->name = 'PENDING';
                $item->short_name = 'pending';
                $item->parent_id = Codemaster::getIdByCode(330);
                $item->is_active = 1;
                $item->code = $code;
                $item->rank = null;
                $item->parent_short_code = 'redressed_status';
            }
            return $item;
        });

        // Step 5: Filter out unwanted items
        $filtered = $updatedCollection->reject(function ($item) use ($removeCodes, $removeShortNames) {
            return in_array($item->code, $removeCodes)
                || in_array(strtolower($item->short_name), array_map('strtolower', $removeShortNames));
        });

        // Step 6: Set final collection
        $this->types = $filtered->values();
        return view('livewire.cmo-grievance-workflow-dropdown');
    }
}
