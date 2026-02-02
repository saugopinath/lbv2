<?php

namespace App\Livewire;

use App\Models\Ifsccodemaster;
use App\Models\MasterTab;
use App\Models\UniqueAppBenId;
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
    public array $completedTabs = [];
    public bool $allTabsCompleted = false;
    public array $formData = [];

    protected $listeners = [
        'document-validation-passed' => 'goToNextTab',
        'document-validation-failed' => 'stayOnTab',
    ];

    /* ================= MOUNT ================= */

    public function mount($schemeId, $ram = null, $applicationId = null, $beneficiaryId = null)
    {
        $this->loadScheme($schemeId);

        if (!empty($this->views)) {
            $this->activeTab = (string) $this->views[0];
            $this->updateTabNavigation();
        }
        $this->schemeId = $schemeId;
        $this->ram = $ram;
        $this->applicationId = $applicationId;
        $this->beneficiaryId = $beneficiaryId;
        // dd($this->ram);
    }
    public function setActiveTab($tabCode)
    {
        $tabCode = (string) $tabCode;

        // 🔒 Block tab click until all completed
        if (!$this->allTabsCompleted) {

            // only allow current or completed tabs
            if (
                $tabCode !== $this->activeTab &&
                !in_array($tabCode, $this->completedTabs, true)
            ) {
                return;
            }
        }

        $this->activeTab = $tabCode;
        $this->updateTabNavigation();
    }

    /* ================== SAVE & NEXT ================== */
    public function saveAndNext($nextTab)
    {
        if ((string) $this->activeTab === '104') {
            $this->dispatch('check-documents-before-next', 'enclosure-list');
            return;
        }

        $rules = $this->getValidationRulesForActiveTab();
        if (!empty($rules)) {
            $this->validate($rules);
        }

        $this->ensureApplicationIds();
        $this->saveCurrentTabData();

        $this->markTabCompleted($this->activeTab);

        if ($nextTab) {
            $this->activeTab = (string) $nextTab;
            $this->updateTabNavigation();
        }
    }

    public function goToNextTab()
    {
        $this->saveCurrentTabData();

        if ($this->nextTab) {
            $this->setActiveTab($this->nextTab);
        }
    }
    public function stayOnTab()
    {
    }

    public function onDocumentTabPassed()
    {
        $this->markTabCompleted($this->activeTab);

        if ($this->nextTab) {
            $this->activeTab = (string) $this->nextTab;
            $this->updateTabNavigation();
        }
    }

    public function onDocumentTabFailed()
    {
    }
    /* ================== HELPERS ================== */
    private function markTabCompleted(string $tabCode): void
    {
        if (!in_array($tabCode, $this->completedTabs, true)) {
            $this->completedTabs[] = $tabCode;
        }

        if (count($this->completedTabs) === count($this->views)) {
            $this->allTabsCompleted = true;
        }
    }
    /* ================= FINAL SUBMIT ================= */

    public function finalSubmit()
    {
        dd('FINAL SUBMIT SUCCESS', $this->formData);
    }

    private function updateTabNavigation(): void
    {
        $index = array_search((string) $this->activeTab, $this->views, true);

        $this->currentIndex = $index;
        $this->isFirst = ($index === 0);
        $this->isLast = ($index === count($this->views) - 1);

        $this->prevTab = $this->views[$index - 1] ?? null;
        $this->nextTab = $this->views[$index + 1] ?? null;
    }
    private function saveCurrentTabData(): void
    {
        if (!$this->applicationId) {
            return;
        }

        $tab = DB::table('master_tabs')
            ->where('tab_code', $this->activeTab)
            ->first();

        if (!$tab || empty($tab->tab_model_name)) {
            return;
        }

        $modelClass = "App\\Models\\{$tab->tab_model_name}";
        if (!class_exists($modelClass)) {
            return;
        }

        $json = $this->getSchemeJson();

        $dbData = [
            'scheme_id' => $this->schemeId,
            'application_id' => $this->applicationId,
            'beneficiary_id' => $this->beneficiaryId,
        ];

        $otherDetails = []; // 🔥 JSON bucket

        foreach ($json['tabs'] ?? [] as $tabJson) {

            if ((string) $tabJson['tab_code'] !== (string) $this->activeTab) {
                continue;
            }

            foreach ($tabJson['fields'] ?? [] as $field) {

                $fieldName = $field['field_name'];

                if (!array_key_exists($fieldName, $this->formData)) {
                    continue;
                }

                // ✅ CASE-1: normal DB column
                if (!empty($field['db_column'])) {

                    $dbData[$field['db_column']] = $this->formData[$fieldName];
                }
                // ✅ CASE-2: save into other_details JSON
                else {

                    $otherDetails[$fieldName] = $this->formData[$fieldName];
                }
            }
        }
        // 🔥 attach JSON if exists
        if (!empty($otherDetails)) {
            $dbData['other_details'] = $otherDetails;
        }
        $beneficiatDetails = $modelClass::updateOrCreate(
            ['application_id' => $this->applicationId],
            $dbData
        );
        if ($beneficiatDetails) {
            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'Data saved successfully.',
            ]);
        } else {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Data not saved. Please try again.',
            ]);
        }
        // dd($beneficiatDetails);
    }
    private function ensureApplicationIds(): void
    {
        // dd($this->schemeId);
        if ($this->applicationId && $this->beneficiaryId) {
            return;
        }
        $row = UniqueAppBenId::create([
            'scheme_id' => $this->schemeId,
        ]);
        $beneficiary_id_obj = UniqueAppBenId::where('application_id', $row->application_id)->first();
        $this->applicationId = $row->application_id;
        $this->beneficiaryId = $beneficiary_id_obj->beneficiary_id;

        $this->formData['scheme_id'] = $this->schemeId;
        $this->formData['application_id'] = $this->applicationId;
        $this->formData['beneficiary_id'] = $this->beneficiaryId;
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
