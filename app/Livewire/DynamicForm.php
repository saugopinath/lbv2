<?php

namespace App\Livewire;

use App\Models\MasterTab;
use App\Models\SchemeAttachedDocMappings;
use Livewire\Component;
use Illuminate\Support\Facades\File;

class DynamicForm extends Component
{
    public $schemeId;
    public $views = [];
    public $tabNames = [];
    public $tabDocs = [];
    public $activeTab, $tabs;
    public function mount($schemeId)
    {
        $this->loadScheme($schemeId);
        $this->activeTab = $this->views[0] ?? null;
    }
    public function setActiveTab($tabCode)
    {
        $this->activeTab = $tabCode;
    }
    private function loadScheme($schemeId)
    {
        $this->schemeId = $schemeId;
        $this->views = [];
        $this->tabNames = [];
        $this->tabDocs = [];

        $path = resource_path("views/schemes/scheme_{$schemeId}");

        if (File::exists($path)) {

            $files = File::files($path);
            $views = [];
            foreach ($files as $file) {
                $name = $file->getFilename();
                $name = str_replace('.blade.php', '', $name);
                $views[] = $name;
            }

            sort($views);

            $this->views = $views;

            $this->tabNames = MasterTab::whereIn('tab_code', $this->views)
                ->pluck('tab_name', 'tab_code')
                ->toArray();

            $this->tabs = MasterTab::whereIn('tab_code', $this->views)
                ->get()
                ->keyBy('tab_code');
        }
    }

    public function render()
    {
        return view('livewire.dynamic-form');
    }
}
