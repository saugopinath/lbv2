<?php

namespace App\Livewire;

use App\Models\MasterTab;
use Livewire\Component;
use Illuminate\Support\Facades\File;

class DynamicForm extends Component
{
    public $schemeId;
    public $views = [];
    public $tabNames = [];

    public function mount($schemeId)
    {
        $this->loadScheme($schemeId);
    }

    private function loadScheme($schemeId)
    {
        $this->schemeId = $schemeId;
        $this->views = [];
        $this->tabNames = [];

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
        }
    }

    public function render()
    {
        return view('livewire.dynamic-form');
    }
}
