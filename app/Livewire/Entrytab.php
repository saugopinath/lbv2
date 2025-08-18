<?php

namespace App\Livewire;

use Livewire\Component;

class Entrytab extends Component
{
    public $currentTab, $application_id;
    public $showTabs = false;
    public $tab1Enabled = false;
    public $tab2Enabled = false;
    public $tab3Enabled = false;
    public $tab4Enabled = false;
    public $tab5Enabled = false;
    protected $listeners = ['aadhaarChecked' => 'enableTabs', 'perDet' => 'enableTab2', 'conDet' => 'enableTab3', 'bankDet' => 'enableTab4', 'encList' => 'enableTab5'];
    public function enableTabs()
    {
        $this->showTabs = true;
        $this->tab1Enabled = true;
        $this->currentTab = 'tab1';
    }
    public function enableTab2($application_id)
    {
        $this->tab2Enabled = true;
        $this->currentTab = 'tab2';
        $this->application_id = $application_id;
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
    public function enableTab5()
    {
        $this->tab5Enabled = true;
        $this->currentTab = 'tab5';
    }
    public function render()
    {
        return view('livewire.entrytab');
    }
}
