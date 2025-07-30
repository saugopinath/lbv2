<?php

namespace App\Livewire;

use Livewire\Component;

class Entrytab extends Component
{
    public $currentTab = 'tab1';
    public $showTabs = false;
    public $tab2Enabled = false;
    protected $listeners = ['aadhaarChecked' => 'enableTabs', 'perDet' => 'enableTab2'];
    public function enableTabs()
    {
        $this->showTabs = true;
    }
    public function enableTab2()
    {
        $this->tab2Enabled = true;
        $this->currentTab = 'tab2';
    }
    public function render()
    {
        return view('livewire.entrytab');
    }
}
