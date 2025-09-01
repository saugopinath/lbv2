<?php

namespace App\Livewire\MasterParameterSetting;

use App\Models\Codemaster;
use App\Models\Scheme;
use App\Models\SchemeValidationParameterSetting;
use Livewire\Component;

class Index extends Component
{
    public $Schemes = [];
    public $Menus = [];
    public $SubMenus = [];
    public $Validation_parameters = [];
     public $selectedsetParameter = [];
    public $Parameters = [];
    public $selectedParameter = null;
    public $selectedMenu = null;
    public $selectedScheme = null;
    public $selectedSubMenu = null;
    public $min_score;
    public $max_score;
    
    public function mount()
    {
        $this->Schemes = Scheme::all();

        $this->Menus = Codemaster::whereNull('parent_id')
            ->whereIn('code', [18, 19])
            ->get();
        $this->Validation_parameters = Codemaster::whereNull('parent_id')
            ->whereIn('short_name', ['name_validation_failed'])
            ->pluck('id');

        $this->Parameters = Codemaster::whereIn('parent_id', $this->Validation_parameters)->get();
    }
    public function updatedSelectedMenu($value)
    {
        $this->SubMenus = Codemaster::where('parent_id', $value)->get();
    }
          

  public function submit()
    {
        $this->validate([
            'selectedScheme'      => 'required',
           
        ]);

        foreach ($this->selectedsetParameter as $parameterId) {
            SchemeValidationParameterSetting::create([
                'scheme_id'          => $this->selectedScheme,
                'parameter_code'     => $parameterId,
                'master_code'        => $this->selectedSubMenu,
                'is_active'          => true,
                'from_affected_date' => now(),
                'to_affected_date'   => now(),
                'min_score'   => null,
                'max_score'   => null,
            ]);
        }

        $this->reset('selectedsetParameter');

        session()->flash('success', 'Parameters saved successfully!');
    }

    public function render()
    {
        return view('livewire.master-parameter-setting.index');
    }
}
