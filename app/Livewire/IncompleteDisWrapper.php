<?php

namespace App\Livewire;

use Livewire\Component;

class IncompleteDisWrapper extends Component
{
    public array $filters = [
        'district_id'     => null,
        'rural_urban'     => null,
        'subdivision_id'  => null,
        'blockurban'      => null,
        'gp_ward'         => null,
        'incomplete_type' => null,
    ];

    protected $listeners = [
        'filtersApplied'       => 'updateGeoFilters',       // from filter-lgd-master
        'filterIncompleteType' => 'updateIncompleteType',   // from incomplete-type
    ];

    /**
     * Update LGD filters (district, sub-division, block, gp)
     */
    public function updateGeoFilters(array $data)
    {
        $this->filters = array_merge($this->filters, $data);
        $this->dispatch('doSearch', $this->filters);   // auto update table
    }

    /**
     * Update incomplete-type filter
     */
    public function updateIncompleteType($code)
    {
        $this->filters['incomplete_type'] = $code;
        $this->dispatch('doSearch', $this->filters);   // auto update table
    }

    /**
     * Search button (manual search)
     */
    public function search()
    {
        $this->dispatch('showLoader');
        $this->dispatch('doSearch', $this->filters);
    }

    /**
     * Reset everything
     */
    public function resetAll()
    {
        // Reset filter array
        $this->filters = [
            'district_id'     => null,
            'rural_urban'     => null,
            'subdivision_id'  => null,
            'blockurban'      => null,
            'gp_ward'         => null,
            'incomplete_type' => null,
        ];

        // Tell child components to reset UI filter values
        $this->dispatch('resetChildFilters');

        // Tell Table to refresh with empty filters
        $this->dispatch('doSearch', $this->filters);
    }

    public function render()
    {
        return view('livewire.incomplete-dis-wrapper');
    }
}
