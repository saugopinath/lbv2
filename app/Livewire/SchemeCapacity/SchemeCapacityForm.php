<?php

namespace App\Livewire\SchemeCapacity;

use Livewire\Component;
use App\Models\Scheme;
use App\Models\District;
use App\Models\Block;
use App\Models\Subdivision;
use App\Models\SchemeCapacity;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class SchemeCapacityForm extends Component
{
    public $capacity_type = 'full_scheme';
    public $action_type = ''; // Added action_type
    public $location_level = 'district';

    // Filters
    public $district_id;
    public $block_id;
    public $sub_district_id;
    public $location_scheme_id;
    public $appTypeOptions = [];

    // Data holders
    public $schemes = [];
    public $districts = [];
    public $blocks = [];
    public $subdivisions = [];
    public $schemes_data = [];
    public $locations_data = [];

    public function mount()
    {
        $this->schemes = Scheme::all();
        $this->districts = District::all();
        $this->appTypeOptions = [
            '1' => 'Entry',
            '2' => 'Verification',
            '3' => 'Approval',
        ];
        $this->resetSchemesData();
    }

    private function resetSchemesData()
    {
        foreach ($this->schemes as $index => $scheme) {
            $this->schemes_data[$index] = $this->fetchExistingCapacityData($scheme->id, Scheme::class, $scheme->id);
        }
    }

    public function updatedActionType($value)
    {
        $this->resetSchemesData();
        $this->loadLocationsData();
    }

    private function getEmptyRow()
    {
        return [
            'entry_type' => '0',
            'total_capacity' => '',
            'normal_capacity' => '',
            'ds_capacity' => '',
            'extra_condition' => '',
        ];
    }

    public function resetRowCapacities($dataArrayName, $index)
    {
        // if (isset($this->{$dataArrayName}[$index])) {
        //     $this->{$dataArrayName}[$index]['total_capacity'] = '';
        //     $this->{$dataArrayName}[$index]['normal_capacity'] = '';
        //     $this->{$dataArrayName}[$index]['ds_capacity'] = '';
        // }
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
        if (!$this->location_scheme_id || !$this->action_type) {
            $this->locations_data = [];
            return;
        }

        $locations = $this->getLocations();
        $this->locations_data = [];

        $modelType = match ($this->location_level) {
            'district' => District::class,
            'block' => Block::class,
            'sub_district' => Subdivision::class,
            default => null,
        };

        foreach ($locations as $index => $location) {
            $this->locations_data[$index] = $this->fetchExistingCapacityData(
                $this->location_scheme_id,
                $modelType,
                $location->id
            );
        }
    }

    private function fetchExistingCapacityData($schemeId, $modelType, $modelId)
    {
        if (empty($this->action_type)) {
            return $this->getEmptyRow();
        }

        $capacities = SchemeCapacity::where('scheme_id', $schemeId)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('action_type', $this->action_type)
            ->where('is_active', true)
            ->get();

        if ($capacities->isEmpty()) {
            return $this->getEmptyRow();
        }

        $row = $this->getEmptyRow();

        // If there's an 'Any' entry
        $anyEntry = $capacities->where('entry_type', 0)->first();
        if ($anyEntry) {
            $row['entry_type'] = '0';
            $row['total_capacity'] = $anyEntry->total_capacity;
            $row['extra_condition'] = $anyEntry->extra_condition;
            return $row;
        }

        // Check for Normal and DS
        $normalEntry = $capacities->where('entry_type', 1)->first();
        $dsEntry = $capacities->where('entry_type', 2)->first();

        if ($normalEntry && $dsEntry) {
            $row['entry_type'] = 'both';
            $row['normal_capacity'] = $normalEntry->total_capacity;
            $row['ds_capacity'] = $dsEntry->total_capacity;
            $row['extra_condition'] = $normalEntry->extra_condition; // Assume same for both
        } elseif ($normalEntry) {
            $row['entry_type'] = '1';
            $row['normal_capacity'] = $normalEntry->total_capacity;
            $row['extra_condition'] = $normalEntry->extra_condition;
        } elseif ($dsEntry) {
            $row['entry_type'] = '2';
            $row['ds_capacity'] = $dsEntry->total_capacity;
            $row['extra_condition'] = $dsEntry->extra_condition;
        }

        return $row;
    }

    private function getLocations()
    {
        if ($this->location_level === 'district') {
            return $this->district_id
                ? District::where('id', $this->district_id)->get()
                : District::all();
        }

        if ($this->location_level === 'block') {
            if (!$this->district_id) {
                return collect();
            }
            return Block::where('district_id', $this->district_id)
                ->when($this->block_id, fn($q) => $q->where('id', $this->block_id))
                ->get();
        }

        if ($this->location_level === 'sub_district') {
            if (!$this->district_id) {
                return collect();
            }
            return Subdivision::where('district_id', $this->district_id)
                ->when($this->sub_district_id, fn($q) => $q->where('id', $this->sub_district_id))
                ->get();
        }

        return collect();
    }

    /* ---------------- Helper Methods ---------------- */

    private function getCapacityTypeValue(): int
    {
        return match ($this->capacity_type) {
            'full_scheme' => 1,
            'location' => 2,
            default => 1
        };
    }

    private function validateCapacityData($data, $type)
    {
        $rules = [];
        $validationData = [];

        if ($type === 'both') {
            $rules = [
                'normal_capacity' => 'required|integer|min:1',
                'ds_capacity' => 'required|integer|min:1',
            ];
            $validationData = [
                'normal_capacity' => $data['normal_capacity'],
                'ds_capacity' => $data['ds_capacity']
            ];
        } elseif ($type === '1') {
            $rules = ['normal_capacity' => 'required|integer|min:1'];
            $validationData = ['normal_capacity' => $data['normal_capacity']];
        } elseif ($type === '2') {
            $rules = ['ds_capacity' => 'required|integer|min:1'];
            $validationData = ['ds_capacity' => $data['ds_capacity']];
        } elseif ($type === '0') {
            $rules = ['total_capacity' => 'required|integer|min:1'];
            $validationData = ['total_capacity' => $data['total_capacity']];
        } else {
            $rules = [
                'total_capacity' => 'required|integer|min:1',
            ];
            $validationData = ['total_capacity' => $data['total_capacity']];
        }

        if (!empty($rules)) {
            $validator = Validator::make($validationData, $rules);

            if ($validator->fails()) {
                $this->addError('validation', $validator->errors()->first());
                return false;
            }
        }

        return true;
    }

    /* ---------------- Save Methods ---------------- */

    private function saveCapacity($schemeId, $data, $modelType, $modelId, $capacityType)
    {
        if (empty($this->action_type)) {
            $this->addError('action_type', 'Action Type is required.');
            return false;
        }
        // Deactivate existing records for this combination
        // SchemeCapacity::where('scheme_id', $schemeId)
        //     ->where('model_type', $modelType)
        //     ->where('model_id', $modelId)
        //     ->where('action_type', $this->action_type)
        //     ->where('is_active', true)
        //     ->update(['is_active' => false]);
        SchemeCapacity::select('id')->where('scheme_id', $schemeId)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('action_type', $this->action_type)
            ->where('is_active', true)
            ->delete();

        if ($data['entry_type'] == 0) {

            if (!$this->validateCapacityData($data, '0')) {
                return false;
            }
            SchemeCapacity::create([
                'scheme_id' => $schemeId,
                'capacity_type' => $this->getCapacityTypeValue(),
                'action_type' => $this->action_type,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'entry_type' => 0,
                'total_capacity' => $data['total_capacity'],
                'extra_condition' => $data['extra_condition'] ?? null,
                'is_active' => true,
            ]);

            return true;
        }
        if ($data['entry_type'] === 'both') {
            // Validate both fields
            if (!$this->validateCapacityData($data, 'both')) {
                return false;
            }
            // Create Normal entry (entry_type = 1)
            SchemeCapacity::create([
                'scheme_id' => $schemeId,
                'capacity_type' => $this->getCapacityTypeValue(),
                'action_type' => $this->action_type,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'entry_type' => 1,
                'total_capacity' => $data['normal_capacity'],
                'extra_condition' => $data['extra_condition'] ?? null,
                'is_active' => true,
            ]);
            // Create DS entry (entry_type = 2)
            SchemeCapacity::create([
                'scheme_id' => $schemeId,
                'capacity_type' => $this->getCapacityTypeValue(),
                'action_type' => $this->action_type,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'entry_type' => 2,
                'total_capacity' => $data['ds_capacity'],
                'extra_condition' => $data['extra_condition'] ?? null,
                'is_active' => true,
            ]);

            return true;
        }

        if ($data['entry_type'] === '1' || $data['entry_type'] === '2') {
            // Validate single field
            if (!$this->validateCapacityData($data, $data['entry_type'])) {
                return false;
            }

            $field = $data['entry_type'] === '1' ? 'normal_capacity' : 'ds_capacity';
            $entryValue = $data['entry_type'] === '1' ? 1 : 2;

            SchemeCapacity::create([
                'scheme_id' => $schemeId,
                'capacity_type' => $this->getCapacityTypeValue(),
                'action_type' => $this->action_type,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'entry_type' => $entryValue,
                'total_capacity' => $data[$field],
                'extra_condition' => $data['extra_condition'] ?? null,
                'is_active' => true,
            ]);
            return true;
        }
        session()->flash('error', 'Please select Normal or DS entry type');
        return false;
    }

    public function saveScheme($schemeId, $index)
    {
        // dd($this->schemes_data[$index]);
        $saved = $this->saveCapacity(
            $schemeId,
            $this->schemes_data[$index],
            Scheme::class,
            $schemeId,
            'full_scheme'
        );

        if ($saved === true) {
            $this->schemes_data[$index] = $this->fetchExistingCapacityData($schemeId, Scheme::class, $schemeId);
            session()->flash('success', 'Scheme capacity saved successfully!');
        }
    }

    public function saveLocation($locationId, $index)
    {
        if (!$this->location_scheme_id) {
            $this->addError('location_scheme_id', 'Scheme is required.');
            return;
        }

        $modelType = match ($this->location_level) {
            'district' => District::class,
            'block' => Block::class,
            'sub_district' => Subdivision::class,
            default => null,
        };

        $saved = $this->saveCapacity(
            $this->location_scheme_id,
            $this->locations_data[$index],
            $modelType,
            $locationId,
            'location'
        );

        if ($saved === true) {
            $this->locations_data[$index] = $this->fetchExistingCapacityData(
                $this->location_scheme_id,
                $modelType,
                $locationId
            );
            session()->flash('success', 'Location capacity saved successfully!');
        }
    }
    public function updatedCapacityType()
    {
        $this->reset([
            'action_type',
            'location_scheme_id',
            'district_id',
            'block_id',
            'sub_district_id',
        ]);

        // Clear dependent data
        $this->blocks = [];
        $this->subdivisions = [];
        $this->locations_data = [];
    }
    // public function updatedLocationSchemeId()
    // {
    //     if ($this->location_scheme_id) {
    //         $this->loadAppTypeOptions();
    //     } else {
    //         $this->appTypeOptions = [];
    //         $this->action_type = ''; // Reset when scheme is deselected
    //     }

    //     if ($this->capacity_type === 'location' && $this->location_scheme_id) {
    //         $this->loadLocationsData();
    //     }
    // }
    public function updatedLocationSchemeId()
    {
        if ($this->capacity_type !== 'location') {
            return;
        }
        if ($this->location_scheme_id) {
            $this->loadLocationsData();
        }
    }

    // private function loadAppTypeOptions(): void
    // {
    //     $path = storage_path("app/final_schemes_formdata/scheme_{$this->location_scheme_id}.json");
    //     $options = [];

    //     if (File::exists($path)) {
    //         $json = json_decode(File::get($path), true);
    //         foreach ($json['tabs'] ?? [] as $tab) {
    //             foreach ($tab['fields'] ?? [] as $field) {
    //                 if (($field['field_name'] ?? '') === 'action_type') {
    //                     $options = $field['options'] ?? [];
    //                     break 2;
    //                 }
    //             }
    //         }
    //     }

    //     $this->appTypeOptions = $options;
    //     // Auto-select if only one option or reset if not in options
    //     if (!array_key_exists($this->action_type, $this->appTypeOptions)) {
    //         $this->action_type = '';
    //     }
    // }

    public function render()
    {
        return view('livewire.scheme-capacity.scheme-capacity-form', [
            'locations' => $this->getLocations()
        ]);
    }
}
