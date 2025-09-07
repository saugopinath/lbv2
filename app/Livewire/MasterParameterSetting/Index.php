<?php

namespace App\Livewire\MasterParameterSetting;

use App\Models\Codemaster;
use App\Models\Scheme;
use App\Models\SchemeValidationParameterSetting;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public $Schemes = [];
    public $Menus = [];
    public $SubMenus = [];
    public $Validation_parameters = null;
    public $selectedsetParameter = [];
    public $Parameters = [];
    public $selectedParameter = null;
    public $selectedMenu = null;
    public $selectedScheme = null;
    public $selectedSubMenu = null;
    public $min_score;
    public $max_score;
    public $visible_field;

    public $showModal = false;
    public $editMode = false;
    public $editSchemeId = null;
    public $editMasterCode = null;
    public $editselectedsetParameter = [];
    public $editselectedParameter = null;
    public $editselectedMenu = null;
    public $editselectedScheme = null;
    public $editselectedSubMenu = null;
    public $editmin_score;
    public $editmax_score;
    public $editSubMenus = [];
    // protected $listeners = ['edit-record'];

    public function mount()
    {
        $this->Schemes = Scheme::all();

        $this->Menus = Codemaster::whereNull('parent_id')
            ->whereIn('code', [18, 19])
            ->get();
        $this->visible_field = Codemaster::where('short_name', 'name_validation_failed')
            ->pluck('id')
            ->first();
        // dd($this->visible_field);
        $this->Validation_parameters = Codemaster::whereNull('parent_id')
    ->where('short_name', 'allowed_validation_parameters')
    ->first(); // remove pluck and get the full model
        $this->Parameters = Codemaster::where('parent_id', $this->Validation_parameters->id)->get();
        // dd($this->Parameters);
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
                'min_score' => $this->min_score !== '' ? $this->min_score : null,
                'max_score' => $this->max_score !== '' ? $this->max_score : null,
            ]);
        }

        $this->reset([
            'selectedScheme',
            'selectedMenu',
            'selectedSubMenu',
            'selectedsetParameter',
            'min_score',
            'max_score',
        ]);


        $this->dispatch('refreshDatatable');
        $this->dispatch('form-submitted');

        session()->flash('success', 'Parameters saved successfully!');
    }

    // public function loadEditForm($schemeId, $masterCode)
    // {
    //     // fetch all records for this group
    //     $records = SchemeValidationParameterSetting::where('scheme_id', $schemeId)
    //         ->where('master_code', $masterCode)
    //         ->get();

    //     if ($records->isEmpty()) {
    //         session()->flash('error', 'No records found to edit.');
    //         return;
    //     }

    //     $this->editMode = true;
    //     $this->editSchemeId = $schemeId;
    //     $this->editMasterCode = $masterCode;


    //     $this->selectedScheme = $schemeId;

    //     $submenu = Codemaster::find($masterCode);
    //     $this->selectedMenu = $submenu ? $submenu->parent_id : null;
    //     $this->SubMenus = Codemaster::where('parent_id', $this->selectedMenu)->get();
    //     $this->selectedSubMenu = $masterCode;
    //     $this->selectedsetParameter = $records->pluck('parameter_code')->map(fn($v) => (int)$v)->toArray();
    //     $this->min_score = $records->first()->min_score;
    //     $this->max_score = $records->first()->max_score;
    // }
    #[On('edit-record')]
    public function editRecord($scheme_id, $master_code)
    {
        // dd($scheme_id, $master_code);
        $records = SchemeValidationParameterSetting::where('scheme_id', $scheme_id)
            ->where('master_code', $master_code)
            ->get();
        // dd($records);

        if ($records->isEmpty()) {
            session()->flash('error', 'No records found to edit.');
            return;
        }

        //    $this->editMode = true;
        $this->editSchemeId = $scheme_id;
        $this->editMasterCode = $master_code;

        $this->editselectedScheme = $scheme_id;

        $submenu = Codemaster::find($master_code);

        $this->editselectedMenu = $submenu ? $submenu->parent_id : null;
        $this->editSubMenus = Codemaster::where('parent_id', $this->editselectedMenu)->get();
        $this->editselectedSubMenu = $master_code;
        $this->editselectedsetParameter = $records->pluck('parameter_code')->map(fn($v) => (int)$v)->toArray();
        $this->editmin_score = $records->first()->min_score;
        $this->editmax_score = $records->first()->max_score;

        $this->editMode = true;
        $this->showModal = true;
    }
    // Example: open a modal
    // $this->dispatchBrowserEvent('open-edit-modal', [
    //     'id' => $menu->id,
    //     'menu_name' => $menu->menu?->name,
    // ]);


    public function render()
    {
        return view('livewire.master-parameter-setting.index');
    }
}
