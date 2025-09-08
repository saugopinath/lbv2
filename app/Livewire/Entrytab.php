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
    public $currentTab, $application_id, $aadhaarData;
    public $showTabs = false;
    public $tab1Enabled = false;
    public $tab2Enabled = false;
    public $tab3Enabled = false;
    public $tab4Enabled = false;
    public $tab5Enabled = false;
    public $tabMessages = [];
    // public bool $openModal = false;
    public bool $showModal = false;
    protected $listeners = [
        'aadhaarChecked' => 'enableTabs',
        'perDet' => 'enableTab2',
        'conDet' => 'enableTab3',
        'bankDet' => 'enableTab4',
        'encList' => 'enableTab5',
        'goPrevious' => 'previousTab',
        'tabMessage' => 'handleTabMessage',
        'selfDec' => 'openModalForApplicant',
        'modalClosed' => 'handleModalClosed'
    ];
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
    }
    public function updatedCurrentTab($value)
    {
        // $this->openModal = false;
        $this->showModal = false;
    }
    public function enableTabs($data)
    {
        $this->showTabs = true;
        $this->tab1Enabled = true;
        $this->currentTab = 'tab1';
        $this->aadhaarData = $data;
    }
    public function enableTab2($data)
    {
        $this->tab1Enabled = true;
        $this->tab2Enabled = true;
        $this->currentTab = 'tab2';
        $this->application_id = $data['application_id'] ?? $this->application_id;
        if (!empty($data['message'])) {
            $this->tabMessages['tab2'] = $data['message'];
        }
    }
    public function enableTab3($data)
    {
        $this->tab1Enabled = true;
        $this->tab2Enabled = true;
        $this->tab3Enabled = true;
        $this->currentTab = 'tab3';
        if (!empty($data['message'])) {
            $this->tabMessages['tab3'] = $data['message'];
        }
    }
    public function enableTab4($data)
    {
        $this->tab1Enabled = true;
        $this->tab2Enabled = true;
        $this->tab3Enabled = true;
        $this->tab4Enabled = true;
        $this->currentTab = 'tab4';
        if (!empty($data['message'])) {
            $this->tabMessages['tab4'] = $data['message'];
        }
    }
    public function enableTab5($data)
    {
        $this->tab1Enabled = true;
        $this->tab2Enabled = true;
        $this->tab3Enabled = true;
        $this->tab4Enabled = true;
        $this->tab5Enabled = true;
        $this->currentTab = 'tab5';
        if (!empty($data['message'])) {
            $this->tabMessages['tab5'] = $data['message'];
        }
    }
    public function openModalForApplicant()
    {
        // $this->openModal = true;
        $this->showModal = true;
        $this->dispatch('openApplicantModal');
    }
    public function handleModalClosed()
    {
        $this->showModal = false;
    }
    public function previousTab()
    {
        $tabOrder = ['tab1', 'tab2', 'tab3', 'tab4', 'tab5'];
        $currentIndex = array_search($this->currentTab, $tabOrder);
        if ($currentIndex > 0) {
            $this->currentTab = $tabOrder[$currentIndex - 1];
        }
    }
    public function clearTabMessage($tab)
    {
        if (!empty($this->tabMessages[$tab])) {
            unset($this->tabMessages[$tab]);
        }
    }
    public function render()
    {
        $tabs = [
            'tab1' => [
                'label' => 'Personal Details',
                'component' => 'personal-details',
                'enabled' => $this->tab1Enabled,
                'icon' => 'M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z'
            ],
            'tab2' => [
                'label' => 'Contact Details',
                'component' => 'contact-details',
                'enabled' => $this->tab2Enabled,
                'icon' => 'M14.25 9.75v-4.5m0 4.5h4.5m-4.5 0 6-6m-3 18c-8.284 0-15-6.716-15-15V4.5A2.25 2.25 0 0 1 4.5 2.25h1.372c.516 0 .966.351 1.091.852l1.106 4.423c.11.44-.054.902-.417 1.173l-1.293.97a1.062 1.062 0 0 0-.38 1.21 12.035 12.035 0 0 0 7.143 7.143c.441.162.928-.004 1.21-.38l.97-1.293a1.125 1.125 0 0 1 1.173-.417l4.423 1.106c.5.125.852.575.852 1.091V19.5a2.25 2.25 0 0 1-2.25 2.25h-2.25Z'
            ],
            'tab3' => [
                'label' => 'Bank Account Details',
                'component' => 'bank-details',
                'enabled' => $this->tab3Enabled,
                'icon' => 'M2 10L12 3l10 7v2H2v-2zm1 3h2v6H3v-6zm4 0h2v6H7v-6zm4 0h2v6h-2v-6zm4 0h2v6h-2v-6zm4 0h2v6h-2v-6zM2 20h20v1H2v-1z'
            ],
            'tab4' => [
                'label' => 'Enclosure List',
                'component' => 'enclosure-list',
                'enabled' => $this->tab4Enabled,
                'icon' => 'm18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13'
            ],
            'tab5' => [
                'label' => 'Self Declaration',
                'component' => 'self-declaration',
                'enabled' => $this->tab5Enabled,
                'icon' => 'M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z'
            ],
        ];
        return view('livewire.entrytab', [
            'tabs' => $tabs
        ]);
    }
}
