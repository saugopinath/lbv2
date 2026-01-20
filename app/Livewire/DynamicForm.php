<?php

namespace App\Livewire;

use App\Models\MasterTab;
use Livewire\Component;
use Illuminate\Support\Facades\File;

class DynamicForm extends Component
{
    public $schemeId;

    public array $views = [];   // tab codes as STRING
    public $tabs;               // MasterTab collection
    public $activeTab = null;

    // navigation state
    public int $currentIndex = 0;
    public bool $isFirst = true;
    public bool $isLast  = false;
    public $prevTab = null;
    public $nextTab = null;

    public function mount($schemeId)
    {
        $this->loadScheme($schemeId);

        if (!empty($this->views)) {
            $this->activeTab = $this->views[0];
            $this->updateTabNavigation();
        }
    }

    public function setActiveTab($tabCode)
    {
        // 🔥 always keep as STRING
        $this->activeTab = (string) $tabCode;
        $this->updateTabNavigation();
    }

    public function saveAndNext($nextTab)
    {
        $this->setActiveTab($nextTab);
    }

    private function updateTabNavigation(): void
    {
        if (!$this->activeTab || empty($this->views)) {
            return;
        }

        // 🔥 FIX: string compare
        $index = array_search((string) $this->activeTab, $this->views);

        if ($index === false) {
            return;
        }

        $this->currentIndex = $index;
        $this->isFirst = ($index === 0);
        $this->isLast  = ($index === count($this->views) - 1);

        $this->prevTab = $this->views[$index - 1] ?? null;
        $this->nextTab = $this->views[$index + 1] ?? null;
    }

    private function loadScheme($schemeId)
    {
        $this->schemeId = $schemeId;
        $this->views = [];

        $path = resource_path("views/schemes/scheme_{$schemeId}");

        if (!File::exists($path)) {
            return;
        }

        // load blade files → STRING tab codes
        foreach (File::files($path) as $file) {
            $this->views[] = str_replace('.blade.php', '', $file->getFilename());
        }

        sort($this->views);

        $this->tabs = MasterTab::whereIn('tab_code', $this->views)
            ->get()
            ->keyBy('tab_code');
    }

    public function finalSubmit()
    {
        // final submit logic
        dd('FINAL SUBMIT WORKING');
    }

    public function render()
    {
        return view('livewire.dynamic-form');
    }
}
