<?php

namespace App\Livewire;

use App\Models\Codemaster;
use Livewire\Attributes\On;
use Livewire\Component;

class IncompleteSearchWrapper extends Component
{
    public array $filters = [
        'district_id' => null,
        'rural_urban' => null,
        'subdivision_id' => null,
        'blockurban' => null,
        'gpward' => null,
        'incomplete_type' => null,
        'application_id' => null,
        'applicant_name' => null,
        'mobile_no' => null,
    ];

    public $revert = 'no';
    public ?string $stage = null;
    public ?int $schemeId = null;

    #[On('filter-applied')]
    public function filterApplied($filters)
    {
        $this->filters = array_merge($this->filters, $filters);
    }

    public function mount(?int $schemeId = null, ?string $stage = null)
    {
        $this->schemeId = $schemeId;
        $this->stage = $stage;
    }

    public function updateGeoFilters(array $data)
    {
        $this->filters = array_merge($this->filters, $data);
    }

    #[On('filterIncompleteType')]
    public function filterIncompleteType($code)
    {
        $this->filters['incomplete_type'] = $code;
    }

    public function search()
    {
        $this->dispatch('doSearch', $this->filters);
    }

    public function updatedRevert($value)
    {
        if ($value === 'yes') {
            $this->stage = 'revert';
        } else {
            $this->stage = session('default_stage') ?? 'verifier';
        }
    }

    public function clearSingleFilter(string $key)
    {
        if (array_key_exists($key, $this->filters)) {
            $this->filters[$key] = null;
            if ($key === 'incomplete_type') {
                $this->dispatch('resetChildFilters');
            }
            $this->dispatch('doSearch', $this->filters);
        }
    }

    public function resetAll()
    {
        $this->filters = [
            'district_id' => null,
            'rural_urban' => null,
            'subdivision_id' => null,
            'blockurban' => null,
            'gpward' => null,
            'incomplete_type' => null,
            'application_id' => null,
            'applicant_name' => null,
            'mobile_no' => null,
        ];

        // tell children to reset their UI
        $this->dispatch('resetChildFilters');

        // notify table with empty filters
        $this->dispatch('doSearch', $this->filters);
    }

    public function getActiveFiltersProperty(): array
    {
        $active = [];

        if (!empty($this->filters['application_id'])) {
            $active['application_id'] = [
                'label' => 'App ID',
                'value' => $this->filters['application_id'],
            ];
        }

        if (!empty($this->filters['applicant_name'])) {
            $active['applicant_name'] = [
                'label' => 'Name',
                'value' => $this->filters['applicant_name'],
            ];
        }

        if (!empty($this->filters['mobile_no'])) {
            $active['mobile_no'] = [
                'label' => 'Mobile',
                'value' => $this->filters['mobile_no'],
            ];
        }

        if (!empty($this->filters['incomplete_type'])) {
            $typeName = Codemaster::where('code', $this->filters['incomplete_type'])->value('name') ?? $this->filters['incomplete_type'];
            $active['incomplete_type'] = [
                'label' => 'Issue',
                'value' => $typeName,
            ];
        }

        return $active;
    }

    public function render()
    {
        return view('livewire.incomplete-search-wrapper', [
            'activeFilters' => $this->activeFilters,
        ]);
    }
}
