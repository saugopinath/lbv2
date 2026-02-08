<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Exception;

class AgeManagement extends Component
{
    public $schemeId;
    public $minage, $maxage;
    public $isspecial = 'no';
    public $specialcaseOptions = [];
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
    }

    public function save()
    {
        $rules = [
            'minage' => 'required|integer',
            'maxage' => 'required|integer',
        ];

        if ($this->isspecial === 'yes') {
            $rules['selectedSpecialCases.*.case_id'] = 'required|distinct';
            $rules['selectedSpecialCases.*.min'] = 'required|integer';
            $rules['selectedSpecialCases.*.max'] = 'required|integer';
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
            DB::commit();
            $this->dispatch('toastr', ['type' => 'success', 'message' => 'Saved Successfully!']);
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('toastr', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.age-management');
    }
}
