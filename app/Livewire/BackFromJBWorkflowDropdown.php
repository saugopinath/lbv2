<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Codemaster;
use App\Helpers\CheckAuthHelper;

class BackFromJBWorkflowDropdown extends Component
{
    public $types, $application_type;
    public array $filters = [
        'district_id'     => null,
        'rural_urban'     => null,
        'subdivision_id'  => null,
        'blockurban'      => null,
        'gp_ward'         => null,
        'application_type' => null,
    ];
    protected $listeners = [
        'filtersApplied'       => 'updateGeoFilters',
    ];
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
        } elseif (CheckAuthHelper::isCommonApprover()) {
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

    public function updateGeoFilters(array $data)
    {
        $this->filters = array_merge($this->filters, $data);
    }
    public function updatedApplicationType($code)
    {
        $this->filters['application_type'] = $code;
    }
    public function search()
    {
        $this->dispatch('doSearch', $this->filters);
    }
    public function resetAll()
    {
        $this->filters = [
            'district_id'     => null,
            'rural_urban'     => null,
            'subdivision_id'  => null,
            'blockurban'      => null,
            'gp_ward'         => null,
            'application_type' => null,
        ];
        $this->application_type = null;
        $this->dispatch('resetChildFilters');
        $this->dispatch('doSearch', $this->filters);
    }
}
