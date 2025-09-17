<?php

namespace App\Livewire;

use Livewire\Component;

class IncompleteSearchWrapper extends Component
{
    public array $filters = [
        'district_id'     => null,
        'rural_urban'     => null,
        'subdivision_id'  => null,
        'blockurban'      => null,
        'gp_ward'         => null,
        'incomplete_type' => null,
    ];

    public $revert = 'no'; // default
    public ?string $stage = null;

    protected $listeners = [
        'filtersApplied'       => 'updateGeoFilters',
        'filterIncompleteType' => 'updateIncompleteType',
    ];

    public function mount(?string $stage = null)
    {
        $this->stage = $stage;
    }

    public function updateGeoFilters(array $data)
    {
        $this->filters = array_merge($this->filters, $data);
    }

    public function updateIncompleteType($code)
    {
        // dd($code);
        $this->filters['incomplete_type'] = $code;
    }

    public function search()
    {
        // emit aggregated filters to table
        $this->dispatch('doSearch', $this->filters);
    }

    public function updatedRevert($value)
    {
        // dd($value);
        if ($value === 'yes') {
            $this->stage = 'revert';
        } else {
            $this->stage = session('default_stage') ?? 'verifier';
        }
    }

    public function resetAll()
    {
        $this->filters = [
            'district_id'     => null,
            'rural_urban'     => null,
            'subdivision_id'  => null,
            'blockurban'      => null,
            'gp_ward'         => null,
            'incomplete_type' => null,
        ];

        // tell children to reset their UI
        $this->dispatch('resetChildFilters');

        // notify table with empty filters
        $this->dispatch('doSearch', $this->filters);
    }

    public function render()
    {
        return view('livewire.incomplete-search-wrapper');
    }
}
