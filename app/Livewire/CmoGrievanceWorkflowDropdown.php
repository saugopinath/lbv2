<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Codemaster;

class CmoGrievanceWorkflowDropdown extends Component
{
    public $process_type, $types;
    public function submit()
    {
        $this->dispatch('processTypeChanged', $this->process_type);
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
            $id = 142;
            $removeCodes = [3306]; // Verifier removes this
        } elseif ($user->hasAnyRole(['Approver', 'Delegated Approver'])) {
            $code = 3302;
            $id = 143;
            $removeShortNames = ['marked_but_approval_pending']; // Approver removes this
        } elseif ($user->hasAnyRole(['HOD'])) {
            $code = 3303;
            $id = 144;
            $removeCodes = [3306, 3304, 3302]; // HOD removes all these
            $removeShortNames = ['marked_and_approved_but_yet_not_send_to_cmo'];
        }

        // Step 4: Update the 'pending' entry dynamically
        $updatedCollection = $codemasters->map(function ($item) use ($code, $id) {
            if (strtolower($item->short_name) === 'pending') {
                $item->id = $id;
                $item->name = 'PENDING';
                $item->short_name = 'pending';
                $item->parent_id = 141;
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
