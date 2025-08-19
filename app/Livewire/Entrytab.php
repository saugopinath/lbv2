<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryContact;
use App\Models\DraftBeneficiaryBank;
use App\Models\BeneficiaryEnclosure;
use App\Models\DraftBeneficiaryDeclaration;

class Entrytab extends Component
{
    public $currentTab, $application_id;
    public $showTabs = false;
    public $tab1Enabled = false;
    public $tab2Enabled = false;
    public $tab3Enabled = false;
    public $tab4Enabled = false;
    public $tab5Enabled = false;
    protected $listeners = ['aadhaarChecked' => 'enableTabs', 'perDet' => 'enableTab2', 'conDet' => 'enableTab3', 'bankDet' => 'enableTab4', 'encList' => 'enableTab5', 'goPrevious' => 'previousTab'];
    public function mount($application_id = null)
    {
        $this->application_id = $application_id;
        if ($application_id) {
            $tabsData = [
                'tab1' => DraftBeneficiaryPersonal::where('application_id', $application_id)->exists(),
                'tab2' => DraftBeneficiaryContact::where('application_id', $application_id)->exists(),
                'tab3' => DraftBeneficiaryBank::where('application_id', $application_id)->exists(),
                'tab4' => BeneficiaryEnclosure::where('application_id', $application_id)->exists(),
                'tab5' => DraftBeneficiaryDeclaration::where('application_id', $application_id)->exists(),
            ];
            $foundNext = false;
            $lastCompletedTab = null;
            foreach ($tabsData as $key => $hasData) {
                if ($hasData) {
                    $this->{$key . 'Enabled'} = true;
                    $lastCompletedTab = $key;
                } elseif (!$foundNext) {
                    $this->{$key . 'Enabled'} = true;
                    $this->currentTab = $key;
                    $foundNext = true;
                } else {
                    $this->{$key . 'Enabled'} = false;
                }
            }
            if (!$foundNext && $lastCompletedTab) {
                $this->currentTab = $lastCompletedTab;
            }
            $this->showTabs = true;
        } 
        /*else {
            $this->tab1Enabled = true;
            $this->currentTab = 'tab1';
            $this->showTabs = true;
        }*/
    }
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
    public function previousTab()
    {
        switch ($this->currentTab) {
            case 'tab2':
                $this->currentTab = 'tab1';
                break;
            case 'tab3':
                $this->currentTab = 'tab2';
                break;
            case 'tab4':
                $this->currentTab = 'tab3';
                break;
            case 'tab5':
                $this->currentTab = 'tab4';
                break;
        }
    }
    public function render()
    {
        return view('livewire.entrytab');
    }
}
