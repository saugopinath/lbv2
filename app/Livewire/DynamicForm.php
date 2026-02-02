<?php

namespace App\Livewire;

use App\Models\Ifsccodemaster;
use App\Models\MasterTab;
use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

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
    public $ram;

    public array $formData = [];

    protected $listeners = [
        'document-validation-passed' => 'goToNextTab',
        'document-validation-failed' => 'stayOnTab',
    ];

    /* ================= MOUNT ================= */

    public function mount($schemeId,$ram = null)
    {
        
        $this->loadScheme($schemeId);

        if (!empty($this->views)) {
            $this->activeTab = (string) $this->views[0];
            $this->updateTabNavigation();
        }
        $this->schemeId = $schemeId;
        $this->ram = $ram;

        // dd($this->ram);
    }

    /* ================= TAB CONTROL ================= */

    public function setActiveTab($tabCode)
    {
        $this->activeTab = (string) $tabCode;
        $this->updateTabNavigation();
    }

    /* ================= SAVE & NEXT ================= */

    public function saveAndNext($nextTab)
    {
        // 🔥 Document tab
        if ((string) $this->activeTab === '104') {
            $this->dispatch('check-documents-before-next');
            return;
        }

        // ✅ JSON based validation
        $rules = $this->getValidationRulesForActiveTab();
        if (!empty($rules)) {
            $this->validate($rules);
        }

        // ✅ Save data tab-wise
        $this->saveCurrentTabData();

        // 👉 Go next
        $this->setActiveTab($nextTab);
    }

    /* ================= DOCUMENT CALLBACK ================= */

    public function goToNextTab()
    {
        // 🔥 save document tab model data
        $this->saveCurrentTabData();

        if ($this->nextTab) {
            $this->setActiveTab($this->nextTab);
        }
    }

    public function stayOnTab()
    {
        // do nothing
    }

    /* ================= FINAL SUBMIT ================= */

    public function finalSubmit()
    {
        // optional: final validation / flag update
        dd('FINAL SUBMIT SUCCESS', $this->formData);
    }

    /* ================= TAB NAVIGATION ================= */

    private function updateTabNavigation(): void
    {
        $index = array_search((string) $this->activeTab, $this->views, true);

        $this->currentIndex = $index;
        $this->isFirst = ($index === 0);
        $this->isLast = ($index === count($this->views) - 1);

        $this->prevTab = $this->views[$index - 1] ?? null;
        $this->nextTab = $this->views[$index + 1] ?? null;
    }

    /* ================= DB SAVE LOGIC ================= */

    private function saveCurrentTabData(): void
    {
        // get tab config from master_tabs
        $tab = DB::table('master_tabs')
            ->where('tab_code', $this->activeTab)
            ->first();

        if (!$tab || empty($tab->tab_model_name)) {
            return;
        }

        // Build model class
        $modelClass = "App\\Models\\{$tab->tab_model_name}";

        if (!class_exists($modelClass)) {
            return;
        }

        // application_id (adjust if your key is different)
        $applicationId = $this->formData['application_id'] ?? null;
        // dd('', $this->formData);
        // insert or update
        try {
            $modelClass::updateOrCreate(
                [
                    'application_id' => $applicationId,
                ],
                $this->formData
            );
        } catch (\Exception $e) {
            dd($e);
        }
    }
   public function updatedFormDataIfscode($value)
    {
        $ifsc = strtoupper($value);

        // normalize value
        $this->formData['ifscode'] = $ifsc;

        if (strlen($ifsc) !== 11) {
            $this->formData['bankname'] = '';
            $this->formData['bank_branch_name'] = '';
            return;
        }

        $ifs = Ifsccodemaster::with('bankmaster')
            ->where('code', $ifsc)
            ->where('is_active', 1)
            ->first();

        if ($ifs) {
            $this->formData['bankname'] = $ifs->bankmaster->name ?? '';
            $this->formData['bank_branch_name'] = $ifs->branch ?? '';
        } else {
            $this->formData['bankname'] = '';
            $this->formData['bank_branch_name'] = '';

            $this->addError(
                'formData.ifscode',
                'This IFSC code is not registered.'
            );
        }
    }
    /* ================= SCHEME LOAD ================= */

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

    /* ================= JSON HELPERS ================= */

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

    /* ================= RENDER ================= */

    public function render()
    {
        return view('livewire.dynamic-form');
    }
}
