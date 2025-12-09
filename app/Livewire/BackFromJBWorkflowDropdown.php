<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Codemaster;
use App\Helpers\CheckAuthHelper;

class BackFromJBWorkflowDropdown extends Component
{
    public $types;
    public function render()
    {
        $codemasters = Codemaster::where('parent_short_code', 'back_from_jb')->get();
        $code = null;
        $id = null;
        $removeCodes = [];
        $removeShortNames = [];
        if (CheckAuthHelper::isCommmonVerifier()) {
            $code = 4401;
            $id = Codemaster::getIdByCode(4401);
        }
        elseif (CheckAuthHelper::isCommonApprover()) {
            $removeShortNames = ['pending'];
        }
        $updatedCollection = $codemasters->map(function ($item) use ($code, $id) {
            if (strtolower($item->short_name) === 'pending') {
                $item->id = $id;
                $item->name = 'PENDING';
                $item->short_name = 'pending';
                $item->parent_id = Codemaster::getIdByCode(440);
                $item->is_active = 1;
                $item->code = $code;
                $item->rank = null;
                $item->parent_short_code = 'back_from_jb';
            }
            return $item;
        });
        $filtered = $updatedCollection->reject(function ($item) use ($removeCodes, $removeShortNames) {
            return in_array($item->code, $removeCodes)
                || in_array(strtolower($item->short_name), array_map('strtolower', $removeShortNames));
        });
        $this->types = $filtered->values();
        return view('livewire.back-from-j-b-workflow-dropdown');
    }
}
