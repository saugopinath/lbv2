<?php

namespace App\Livewire;

use Livewire\Component;

class Entrytab extends Component
{
    public $currentTab = 'tab1';
    public $showTabs = false;
    public $tab2Enabled = false;
    public $tab3Enabled = false;
    public $tab4Enabled = false;
    protected $listeners = ['aadhaarChecked' => 'enableTabs', 'perDet' => 'enableTab2', 'conDet' => 'enableTab3', 'bankDet' => 'enableTab4'];
    public function enableTabs()
    {
        $this->showTabs = true;
    }
    public function enableTab2()
    {
        $this->tab2Enabled = true;
        $this->currentTab = 'tab2';
    }
    public function enableTab3()
    {
        $this->tab3Enabled = true;
        $this->currentTab = 'tab3';
    }
    public function enableTab4()
    {
        $this->tab4Enabled = true;
        $this->currentTab = 'tab4';
    }
    public function render()
    {
        return view('livewire.entrytab');
    }
}
