<?php

namespace App\Livewire;

use App\Models\MasterTab;
use Livewire\Component;
use Illuminate\Support\Facades\File;

class DynamicForm extends Component
{
    public $schemeId;

    public array $views = [];
    public $tabs;
    public $activeTab = null;

    public int $currentIndex = 0;
    public bool $isFirst = true;
    public bool $isLast = false;
    public $prevTab = null;
    public $nextTab = null;
    public array $formData = [];
    protected $listeners = [
        'document-validation-passed' => 'goToNextTab',
        'document-validation-failed' => 'stayOnTab',
    ];


    public function mount($schemeId)
    {
        $this->loadScheme($schemeId);

        if (!empty($this->views)) {
            $this->activeTab = $this->views[0];
            $this->updateTabNavigation();
        }
    }

    /* ---------------- TAB CONTROL ---------------- */

    public function setActiveTab($tabCode)
    {
        $this->activeTab = (string) $tabCode;
        $this->updateTabNavigation();
    }

    public function saveAndNext($nextTab)
    {

        if ((string) $this->activeTab === '104') {


            $this->dispatch('check-documents-before-next');

            return;
        }

        $rules = $this->getValidationRulesForActiveTab();

        if (!empty($rules)) {
            $this->validate($rules);
        }

        $this->setActiveTab($nextTab);
    }
    public function goToNextTab()
    {
        if ($this->nextTab) {
            $this->setActiveTab($this->nextTab);
        }
    }

    public function stayOnTab()
    {

    }

    public function finalSubmit()
    {
        // ✅ validate ALL tabs
        // $rules = $this->getValidationRulesForAllTabs();
        // $this->validate($rules);
// dd($this->activeTab);
        // 👉 Final save
        // Application::create($this->formData);

        dd('FINAL SUBMIT SUCCESS', $this->formData);
    }

    private function updateTabNavigation(): void
    {
        $index = array_search((string) $this->activeTab, $this->views);

        $this->currentIndex = $index;
        $this->isFirst = ($index === 0);
        $this->isLast = ($index === count($this->views) - 1);

        $this->prevTab = $this->views[$index - 1] ?? null;
        $this->nextTab = $this->views[$index + 1] ?? null;
    }

    /* ---------------- SCHEME LOAD ---------------- */

    private function loadScheme($schemeId)
    {
        $this->schemeId = $schemeId;
        $this->views = [];

        $path = resource_path("views/schemes/scheme_{$schemeId}");

        if (!File::exists($path)) {
            return;
        }

        foreach (File::files($path) as $file) {
            $this->views[] = str_replace('.blade.php', '', $file->getFilename());
        }

        sort($this->views);

        $this->tabs = MasterTab::whereIn('tab_code', $this->views)
            ->get()
            ->keyBy('tab_code');
    }

    /* ---------------- JSON HELPERS ---------------- */

    private function getSchemeJson(): array
    {
        $path = storage_path("app/final_schemes_formdata/scheme_{$this->schemeId}.json");

        if (!File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true);
    }

    private function getValidationRulesForActiveTab(): array
    {
        $json = $this->getSchemeJson();
        $rules = [];

        foreach ($json['tabs'] ?? [] as $tab) {

            if ((string) $tab['tab_code'] !== (string) $this->activeTab) {
                continue;
            }

            foreach ($tab['fields'] ?? [] as $field) {

                if (empty($field['field_name']) || empty($field['validation_rule'])) {
                    continue;
                }

                $fieldRules = explode('|', $field['validation_rule']);

                if (!empty($field['regex'])) {
                    $fieldRules[] = 'regex:/' . $field['regex'] . '/';
                }

                $rules["formData.{$field['field_name']}"] = $fieldRules;
            }
        }

        return $rules;
    }
    public function render()
    {
        return view('livewire.dynamic-form');
    }

}
