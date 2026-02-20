<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class Filterlgdmasternew extends Component
{
    public $formData = [];
    public $visible = [
        'district_dropdown' => 0,
        'rural_urban_dropdown' => 0,
        'subdivision_dropdown' => 0,
        'block_dropdown' => 0,
        'gp_ward_dropdown' => 0,
        'assembly_dropdown' => 0,
    ];
    public function mount()
    {
        $this->initializeForm();
        $session = session('lgd_session');
        if (!$session) return;
        try {
            $login_type = Crypt::decryptString($session['office_type_id']);
            if (!empty($session['district_id'])) {
                $this->formData['district_id'] = Crypt::decryptString($session['district_id']);
            }
            if (!empty($session['block_id'])) {
                $this->formData['blockurban'] = Crypt::decryptString($session['block_id']);
            }
            if ($login_type === '151') {
                $this->visible['district_dropdown'] = 1;
                $this->visible['assembly_dropdown'] = 1;
                $this->visible['rural_urban_dropdown'] = 1;
                $this->visible['block_dropdown'] = 1;
                $this->visible['gp_ward_dropdown'] = 1;
            }
            if ($login_type === '152') {
                $this->visible['assembly_dropdown'] = 1;
                $this->visible['rural_urban_dropdown'] = 1;
                $this->visible['block_dropdown'] = 1;
                $this->visible['gp_ward_dropdown'] = 1;
            }
            if ($login_type === '153') {
                $this->formData['rural_urban'] = 2;
                $this->visible['gp_ward_dropdown'] = 1;
            }
            if ($login_type === '154') {
                $this->visible['block_dropdown'] = 1;
                $this->visible['gp_ward_dropdown'] = 1;
                $this->formData['rural_urban'] = 1;
            }
        } catch (\Exception $e) {
            report($e);
        }
    }
    private function initializeForm()
    {
        $this->formData = [
            'district_id' => '',
            'assemblie' => '',
            'rural_urban' => '',
            'blockurban' => '',
            'gpward' => '',
        ];
    }
    public function filterData()
    {
        $payload = $this->formData;
        $this->dispatch('filter-applied', data: $payload);
    }
    public function resetFilters()
    {
        $this->initializeForm();
        $this->visible = [
            'district_dropdown' => 0,
            'rural_urban_dropdown' => 0,
            'subdivision_dropdown' => 0,
            'block_dropdown' => 0,
            'gp_ward_dropdown' => 0,
            'assembly_dropdown' => 0,
        ];
        $session = session('lgd_session');
        if ($session) {
            try {
                $login_type = Crypt::decryptString($session['office_type_id']);
                if ($login_type === '151') {
                    $this->visible['district_dropdown'] = 1;
                    $this->visible['assembly_dropdown'] = 1;
                    $this->visible['rural_urban_dropdown'] = 1;
                    $this->visible['block_dropdown'] = 1;
                    $this->visible['gp_ward_dropdown'] = 1;
                }
                if ($login_type === '152') {
                    $this->formData['district_id'] = Crypt::decryptString($session['district_id']);
                    $this->visible['assembly_dropdown'] = 1;
                    $this->visible['rural_urban_dropdown'] = 1;
                    $this->visible['block_dropdown'] = 1;
                    $this->visible['gp_ward_dropdown'] = 1;
                }
                if ($login_type === '153') {
                    $this->formData['district_id'] = Crypt::decryptString($session['district_id']);
                    $this->formData['blockurban'] = Crypt::decryptString($session['block_id']);
                    $this->formData['rural_urban'] = 2;
                    $this->visible['gp_ward_dropdown'] = 1;
                }
                if ($login_type === '154') {
                    $this->formData['district_id'] = Crypt::decryptString($session['district_id']);
                    $this->visible['block_dropdown'] = 1;
                    $this->visible['gp_ward_dropdown'] = 1;
                    $this->formData['rural_urban'] = 1;
                }
            } catch (\Exception $e) {
                report($e);
            }
        }
        $this->dispatch('filter-cleared');
    }
    public function render()
    {
        return view('livewire.filterlgdmasternew');
    }
}
