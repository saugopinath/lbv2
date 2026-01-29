<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SectionLevelMaster;

class SectionLevelModal extends Component
{
    public $show = false;

    public $slType = '';        // 0 = Section, 1 = Label
    public $slName = '';
    public $slShortName = '';
    public $tabCode;
    public $schemeId;

    /* ========= OPEN / CLOSE ========= */

    protected $listeners = ['openSectionLevelModal' => 'open'];

    // public function open()
    // {
    //     $this->resetForm();
    //     $this->show = true;
    // }

    public function open($tabCode = null)
    {
        $this->resetForm();
        $this->tabCode = $tabCode;
        $this->show = true;
      
    }


    public function close()
    {
        $this->show = false;
    }

    /* ========= AUTO SHORT NAME ========= */

    public function updatedSlName($value)
    {
        $this->slShortName = strtolower(
            preg_replace('/[^a-zA-Z0-9]+/', '_', trim($value))
        );
    }

    /* ========= SAVE ========= */

    public function save()
    {
        $this->validate(
            [
                'slType' => 'required|in:0,1',
                'slName' => 'required|string|max:100',
                'slShortName' => 'required|string|max:100|unique:section_level_masters,section_level_short_name',
            ],
            [
                'slType.required' => 'Please select Section or Label',
                'slName.required' => 'Name is required',
                'slShortName.required' => 'This Short name is Required',
                'slShortName.unique' => 'This Short name already exists',
            ]
        );
       
        SectionLevelMaster::create([
            'scheme_id' => $this->schemeId,
            'section_level_name' => $this->slName,
            'section_level_short_name' => $this->slShortName,
            'section_level_code' => (int) $this->slType,
            'is_active' => true,
            'tab_code' => (int) $this->tabCode,
        ]);

        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Section saved successfully!'
        ]);

        $this->close();
    }

    /* ========= HELPERS ========= */

    private function resetForm()
    {
        $this->slType = '';
        $this->slName = '';
        $this->slShortName = '';
    }

    public function render()
    {
        return view('livewire.section-level-modal');
    }
}
