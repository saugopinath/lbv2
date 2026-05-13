<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\AgeManagements;

class AgeManagement extends Component
{
    public $schemeId;
    public $minage, $maxage;
    public $isspecial = 'no';
    public $specialcaseOptions;
    public $selectedSpecialCases = [];

    public function mount($schemeId)
    {
        $this->schemeId = $schemeId;
        $this->specialcaseOptions = collect([
            '1' => 'Handicapped',
            '2' => 'Widow',
        ])->map(function ($name, $id) {
            return (object) ['id' => $id, 'name' => $name];
        });
        $record = AgeManagements::where('scheme_id', $this->schemeId)->first();

        if ($record) {
            $this->minage = $record->min_age;
            $this->maxage = $record->max_age;
            $this->isspecial = $record->is_special ? 'yes' : 'no';
            if ($record->special_case) {
                $data = is_array($record->special_case)
                    ? $record->special_case
                    : json_decode($record->special_case, true);

                foreach ($data as $id => $values) {
                    $this->selectedSpecialCases[] = [
                        'case_id' => (string)$id,
                        'min'     => $values['min'],
                        'max'     => $values['max'],
                    ];
                }
            }
        }
    }

    public function updatedIsspecial($value)
    {
        if ($value === 'yes' && empty($this->selectedSpecialCases)) {
            $this->addSpecialCase();
        } elseif ($value === 'no') {
            $this->selectedSpecialCases = [];
        }
    }

    public function getAvailableOptions($currentIndex)
    {
        $selectedIds = collect($this->selectedSpecialCases)
            ->forget($currentIndex)
            ->pluck('case_id')
            ->filter()
            ->values()
            ->toArray();
        return $this->specialcaseOptions->filter(function ($option) use ($selectedIds) {
            return !in_array($option->id, $selectedIds);
        });
    }

    public function addSpecialCase()
    {
        if (count($this->selectedSpecialCases) < $this->specialcaseOptions->count()) {
            $this->selectedSpecialCases[] = [
                'case_id' => '',
                'min' => '',
                'max' => ''
            ];
        } else {
            $this->dispatch('toastr', ['type' => 'warning', 'message' => 'All special cases already added!']);
        }
    }

    public function removeSpecialCase($index)
    {
        unset($this->selectedSpecialCases[$index]);
        $this->selectedSpecialCases = array_values($this->selectedSpecialCases);
        if (empty($this->selectedSpecialCases)) {
            $this->isspecial = 'no';
        }
    }
    public function save()
    {
        $rules = [
            'minage' => 'nullable|integer',
            'maxage' => 'nullable|integer',
        ];

        if ($this->isspecial === 'yes') {
            $rules['selectedSpecialCases.*.case_id'] = 'required|distinct';
            $rules['selectedSpecialCases.*.min'] = 'nullable|integer';
            $rules['selectedSpecialCases.*.max'] = 'nullable|integer';
        }
        $customMessages = [
            'minage.*' => 'General Min Age is Required',
            'maxage.*' => 'General Max Age is Required',
            'selectedSpecialCases.*.case_id.*' => 'Please Choose a Value',
            'selectedSpecialCases.*.min.*' => 'Special Min Age is Required',
            'selectedSpecialCases.*.max.*' => 'Special Max Age is Required',
        ];
        $this->validate($rules, $customMessages);
        DB::beginTransaction();
        try {
            $jsonContent = null;
            if ($this->isspecial === 'yes') {
                $jsonContent = [];
                foreach ($this->selectedSpecialCases as $item) {
                    $jsonContent[$item['case_id']] = [
                        'min' => $item['min'],
                        'max' => $item['max'],
                    ];
                }
            }
            $old = AgeManagements::where('scheme_id', $this->schemeId)->first();
            if ($old) {
                $old->delete();
            }
            $age = new AgeManagements();
            $age->scheme_id = $this->schemeId;
            $age->min_age = $this->minage ?: null;
            $age->max_age = $this->maxage ?: null;
            $age->is_special = $this->isspecial === 'yes';
            $age->special_case = $jsonContent ? json_encode($jsonContent) : null;

            $age->save();
            DB::commit();
            $this->dispatch('toastr', ['type' => 'success', 'message' => 'Saved Successfully!']);
        } catch (Exception $e) {
            // dd($e->getMessage());
            DB::rollBack();
            $this->dispatch('toastr', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.age-management');
    }
}
