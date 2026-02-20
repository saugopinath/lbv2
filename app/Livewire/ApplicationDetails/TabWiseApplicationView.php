<?php

namespace App\Livewire\ApplicationDetails;

use Livewire\Component;
use App\Services\ApplicationTabWiseService;


class TabWiseApplicationView extends Component
{
    public $applicationId;
    public $schemeId;

    public array $tabs = [];
    public array $allowedTabCodes = [];

    protected ApplicationTabWiseService $tabService;

    public function boot(ApplicationTabWiseService $tabService)
    {
        $this->tabService = $tabService;
    }

    public function mount($id, $schemeId = null, $allowedTabCodes = [])
    {
        $this->applicationId = $id;
        $this->schemeId = $schemeId;
       
        if (is_string($allowedTabCodes)) {
            $allowedTabCodes = explode(',', $allowedTabCodes);
        }

        $this->allowedTabCodes = array_map('intval', $allowedTabCodes);
        
        // 🔥 only tab meta load
        $this->tabs = $this->tabService->getTabsMeta(
            $this->schemeId,
            $this->allowedTabCodes
        );
    }

    // 🔥 Single tab data load
    public function loadTab($index)
    {
        if (!isset($this->tabs[$index])) {
            return;
        }

        if ($this->tabs[$index]['loaded'] === true) {
            return;
        }

        $tab = &$this->tabs[$index];

        // component tab (doc etc)
        if ($tab['type'] === 'component') {
            $tab['loaded'] = true;
            return;
        }

        $tab['data'] = $this->tabService->getTabData(
            $this->schemeId,
            $this->applicationId,
            $tab['tab_code']
        );

        $tab['loaded'] = true;
    }
    public function render()
    {
        return view('livewire.application-details.tab-wise-application-view');
    }
}
