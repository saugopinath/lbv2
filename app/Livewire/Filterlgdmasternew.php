<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class Filterlgdmasternew extends Component
{
    public $formData = [];
    public $visible = [];
    protected $defaultVisible = [
        'district_dropdown' => 0,
        'rural_urban_dropdown' => 0,
        'subdivision_dropdown' => 0,
        'block_dropdown' => 0,
        'gp_ward_dropdown' => 0,
        'assembly_dropdown' => 0,
    ];
    public function mount()
    {
        $this->resetFilters();
    }
    private function applySessionLogic()
    {
        $session = session('lgd_session');
        if (!$session) return;
        try {
            $login_type = Crypt::decryptString($session['office_type_id']);
            foreach (['district_id' => 'district_id', 'block_id' => 'blockurban'] as $key => $target) {
                if (!empty($session[$key])) $this->formData[$target] = Crypt::decryptString($session[$key]);
            }
            switch ($login_type) {
                case '151':
                    $this->visible = array_merge($this->visible, ['district_dropdown' => 1, 'assembly_dropdown' => 1, 'rural_urban_dropdown' => 1, 'block_dropdown' => 1, 'gp_ward_dropdown' => 1]);
                    break;
                case '152':
                    $this->visible = array_merge($this->visible, ['assembly_dropdown' => 1, 'rural_urban_dropdown' => 1, 'block_dropdown' => 1, 'gp_ward_dropdown' => 1]);
                    break;
                case '153':
                    $this->formData['rural_urban'] = 2;
                    $this->visible['gp_ward_dropdown'] = 1;
                    break;
                case '154':
                    $this->formData['rural_urban'] = 1;
                    $this->visible = array_merge($this->visible, ['block_dropdown' => 1, 'gp_ward_dropdown' => 1]);
                    break;
            }
        } catch (\Exception $e) {
            // dd($e);
        }
    }
    public function resetFilters()
    {
        $this->formData = ['district_id' => '', 'assemblie' => '', 'rural_urban' => '', 'blockurban' => '', 'gpward' => ''];
        $this->visible = $this->defaultVisible;
        $this->applySessionLogic();
        $this->dispatch('filter-cleared');
    }
    public function filterData()
    {
        $this->dispatch('filter-applied', data: $this->formData);
    }
    public function render()
    {
        return view('livewire.filterlgdmasternew');
    }
}
