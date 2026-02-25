<?php

namespace App\Livewire\SchemeCapacity;

use Livewire\Component;
use App\Models\Scheme;
use App\Models\District;
use App\Models\Block;
use App\Models\Subdivision;
use App\Models\SchemeCapacity;

class SchemeCapacityForm extends Component
{
    public $capacity_type = 'full_scheme';
    public $location_level = 'district';

    // Filters
    public $district_id;
    public $block_id;
    public $sub_district_id;
    public $location_scheme_id;

    // Data holders
    public $schemes = [];
    public $districts = [];
    public $blocks = [];
    public $subdivisions = [];
    public $schemes_data = [];
    public $locations_data = [];

    protected $listeners = ['refreshLocations' => 'loadLocationsData'];

    public function mount()
    {
        $this->schemes = Scheme::all();
        $this->districts = District::all();
        $this->resetSchemesData();
    }

    private function resetSchemesData()
    {
        foreach ($this->schemes as $index => $scheme) {
            $this->schemes_data[$index] = $this->getEmptyRow();
        }
    }

    private function getEmptyRow()
    {
        return [
            'entry_type' => '0',
            'total_capacity' => '',
            'normal_capacity' => '',
            'ds_capacity' => '',
        ];
    }

    /* ---------------- Location Handling ---------------- */

    public function setLocationLevel($level)
    {
        $this->location_level = $level;
        $this->resetLocationFilters();
        $this->loadLocationsData();
    }

    private function resetLocationFilters()
    {
        $this->district_id = null;
        $this->block_id = null;
        $this->sub_district_id = null;
        $this->blocks = [];
        $this->subdivisions = [];
    }

    public function updatedDistrictId($districtId)
    {
        $this->block_id = null;
        $this->sub_district_id = null;

        if ($this->location_level === 'block') {
            $this->blocks = Block::where('district_id', $districtId)->get();
        }

        if ($this->location_level === 'sub_district') {
            $this->subdivisions = Subdivision::where('district_id', $districtId)->get();
        }

        $this->loadLocationsData();
    }

    public function updatedBlockId()
    {
        $this->loadLocationsData();
    }

    public function updatedSubDistrictId()
    {
        $this->loadLocationsData();
    }

    private function loadLocationsData()
    {
        $locations = $this->getLocations();
        $this->locations_data = array_fill(0, $locations->count(), $this->getEmptyRow());
    }

    private function getLocations()
    {
        return match ($this->location_level) {
            'district' => $this->district_id
                ? District::where('id', $this->district_id)->get()
                : District::all(),
            'block' => Block::when($this->district_id, fn($q) => $q->where('district_id', $this->district_id))
                ->when($this->block_id, fn($q) => $q->where('id', $this->block_id))
                ->get(),
            'sub_district' => Subdivision::when($this->district_id, fn($q) => $q->where('district_id', $this->district_id))
                ->when($this->sub_district_id, fn($q) => $q->where('id', $this->sub_district_id))
                ->get(),
            default => collect()
        };
    }

    /* ---------------- Save Methods ---------------- */

    private function saveCapacity($schemeId, $data, $modelType, $modelId, $capacityType)
    {
        $map = [
            '0' => ['field' => 'total_capacity', 'entry' => 0],
            '1' => ['field' => 'normal_capacity', 'entry' => 1],
            '2' => ['field' => 'ds_capacity', 'entry' => 2],
        ];

        if ($data['entry_type'] === 'both') {
            $this->validate([
                'normal_capacity' => 'required|integer|min:1',
                'ds_capacity' => 'required|integer|min:1',
            ]);

            foreach ([1 => 'normal_capacity', 2 => 'ds_capacity'] as $entry => $field) {
                SchemeCapacity::create([
                    'scheme_id' => $schemeId,
                    'capacity_type' => $capacityType,
                    'model_type' => $modelType,
                    'model_id' => $modelId,
                    'entry_type' => $entry,
                    'total_capacity' => $data[$field],
                ]);
            }
        } else {
            $field = $map[$data['entry_type']]['field'];
            $this->validate([$field => 'required|integer|min:1']);

            SchemeCapacity::create([
                'scheme_id' => $schemeId,
                'capacity_type' => $capacityType,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'entry_type' => $map[$data['entry_type']]['entry'],
                'total_capacity' => $data[$field],
            ]);
        }
    }

    public function saveScheme($schemeId, $index)
    {
        $this->saveCapacity(
            $schemeId,
            $this->schemes_data[$index],
            Scheme::class,
            $schemeId,
            'full_scheme'
        );

        $this->schemes_data[$index] = $this->getEmptyRow();
        session()->flash('success', 'Scheme capacity saved successfully!');
    }

    public function saveLocation($level, $locationId, $index)
    {
        if (!$this->location_scheme_id) {
            $this->addError('location_scheme_id', 'Scheme is required.');
            return;
        }

        $modelType = match ($level) {
            'district' => District::class,
            'block' => Block::class,
            'sub_district' => Subdivision::class,
            default => null,
        };

        $this->saveCapacity(
            $this->location_scheme_id,
            $this->locations_data[$index],
            $modelType,
            $locationId,
            'location'
        );

        $this->locations_data[$index] = $this->getEmptyRow();
        session()->flash('success', 'Location capacity saved successfully!');
    }

    public function render()
    {
        if ($this->capacity_type === 'location' && $this->location_scheme_id) {
            $this->loadLocationsData();
        }

        return view('livewire.scheme-capacity.scheme-capacity-form');
    }
}
