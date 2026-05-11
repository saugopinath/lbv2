<?php

namespace App\Livewire;

use App\Helpers\FormHelper;
use App\Models\AgeManagements;
use App\Models\BeneficiaryAadhaar;
use App\Models\Ifsccodemaster;
use App\Models\MasterTab;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DuplicateChecker;
use App\Models\UniqueAppBenId;
use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinalPreview extends Component
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
    public $ram, $form_preview;
    public $applicationId;
    public $beneficiaryId;
    public array $appTypeOptions = [];
    public array $formData = [];
    public $navMessage = null;
    public $navMessageType = 'success';
    public $showFinalModal = false;
    public $maxDate, $minDate, $minDOB, $maxDOB;
    public bool $isEdit = false;

    protected $listeners = [
        'document-validation-passed' => 'goToNextTab',
        'document-validation-failed' => 'stayOnTab',
    ];

    /* ================= MOUNT ================= */

    public function mount($schemeId, $ram = null, $applicationId = null, $beneficiaryId = null, $form_preview = null, $isEdit = false)
    {
        $this->isEdit = $isEdit;
        $this->loadAppTypeOptions();
        $this->loadScheme($schemeId);

        if (!empty($this->views)) {
            $this->activeTab = (string) $this->views[0];
            $this->updateTabNavigation();
        }
        $this->schemeId = $schemeId;
        $this->ram = $ram;
        $this->form_preview = $form_preview;
        $this->applicationId = $applicationId;
        $this->beneficiaryId = $beneficiaryId;
        $this->maxDate = Carbon::now()->format('Y-m-d');
        $this->minDate = Carbon::now()->subYears(2)->format('Y-m-d');
        $ageConfig = AgeManagements::where('scheme_id', $schemeId)->first();
        if ($ageConfig) {
            if ($ageConfig['max_age']) {
                $this->minDOB = now()->subYears($ageConfig['max_age'])->format('Y-m-d');
            }
            if ($ageConfig['min_age']) {
                $this->maxDOB = now()->subYears($ageConfig['min_age'])->format('Y-m-d');
            }
        }
    }
    private function loadAppTypeOptions(): void
    {
        $json = $this->getSchemeJson();

        $options = [];

        foreach ($json['tabs'] ?? [] as $tab) {
            foreach ($tab['fields'] ?? [] as $field) {

                if (($field['field_name'] ?? '') === 'application_type') {
                    $options = $field['options'] ?? [];
                    break 2; // stop loop
                }
            }
        }



        $this->appTypeOptions = $options;
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
        // dump($this->activeTab);
        // dump($nextTab);
        // dd($this->formData);

        if ((string) $this->activeTab === '104') {
            $this->dispatch('check-documents-before-next');
            return;
        }
        $rules = $this->getValidationRulesForActiveTab();
        // dd($rules);
        if (!empty($rules)) {
            $this->validate($rules);
        }
        // dd($this->formData);
        // dd('saveAndNext', $nextTab);
        $this->ensureApplicationIds();
        if (!$this->checkDuplicateEntries()) {
            return;
        }
        // dd($this->applicationId, $this->beneficiaryId);
        // dd('ff');
        $this->saveCurrentTabData();

        $this->setActiveTab($nextTab);
    }

    /* ================= DOCUMENT CALLBACK ================= */

    public function goToNextTab()
    {
        $this->saveCurrentTabData();

        if ($this->nextTab) {
            $this->setActiveTab($this->nextTab);
        }
    }

    public function stayOnTab()
    {
        // do nothing
    }


    public function finalSubmit()
    {
        if ((string) $this->activeTab === '104') {
            $this->dispatch('check-documents-before-next');
            return;
        } else {
            // dd($this->activeTab, $this->formData);
            $rules = $this->getValidationRulesForActiveTab();
            if (!empty($rules)) {
                $this->validate($rules);
            }
            $this->ensureApplicationIds();
            $this->saveCurrentTabData();
            $tabsData = $this->prepareTabsReviewData();
            if (!$this->checkDuplicateEntries()) {
                return;
            }
            $this->dispatch(
                'openFinalModal',
                applicationId: $this->applicationId,
                tabsData: $tabsData
            );
        }
    }

    private function prepareTabsReviewData()
    {
        $review = [];
        $json = $this->getSchemeJson();
        foreach ($json['tabs'] ?? [] as $tab) {
            $tabName = $tab['tab_name'] ?? 'Tab';
            foreach ($tab['fields'] ?? [] as $field) {
                if (!isset($field['field_name'])) {
                    continue;
                }
                $fieldName = $field['field_name'];
                if (!array_key_exists($fieldName, $this->formData)) {
                    continue;
                }
                $label = $field['level_name']
                    ?? ucfirst(str_replace('_', ' ', $fieldName));

                $value = FormHelper::resolveValue(
                    $field,
                    data_get($this->formData, $fieldName),
                    $this->formData
                );
                $review[$tabName][$label] = $value;
            }
        }

        return $review;
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
        $otherDetails = [];
        foreach ($json['tabs'] ?? [] as $tabJson) {

            if ((string) $tabJson['tab_code'] !== (string) $this->activeTab) {
                continue;
            }
            foreach ($tabJson['fields'] ?? [] as $field) {
                $fieldName = $field['field_name'];
                if (!array_key_exists($fieldName, $this->formData)) {
                    continue;
                }
                if (!empty($field['db_column']) && $field['db_column'] !== 'other_details') {
                    $dbData[$field['db_column']] = $this->formData[$fieldName];
                } elseif (!empty($field['db_column']) && $field['db_column'] == 'other_details') {
                    $otherDetails[$fieldName] = $this->formData[$fieldName];
                } else {
                    continue;
                }
            }
        }
        if (!empty($otherDetails)) {
            $dbData['other_details'] = $otherDetails;
        }
        $beneficiatDetails = $modelClass::updateOrCreate(
            ['application_id' => $this->applicationId],
            $dbData
        );
        if ($beneficiatDetails) {
            $this->navMessage = 'Application saved successfully! ID: ' . $this->applicationId;
            $this->navMessageType = 'success';
            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'Application created successfully' . 'application_id: ' . $this->applicationId,
            ]);
        } else {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Application not created. Please try again.',
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
        // dd($this->applicationId, $this->beneficiaryId);

        $this->formData['scheme_id'] = $this->schemeId;
        $this->formData['application_id'] = $this->applicationId;
        $this->formData['beneficiary_id'] = $this->beneficiaryId;
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
        // dd($json);
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

                // if (!empty($field['regex'])) {
                //     $fieldRules[] = 'regex:/' . $field['regex'] . '/';
                // }

                $rules["formData.{$field['field_name']}"] = $fieldRules;
            }
        }
        // dd($rules);

        return $rules;
    }
    protected function validationAttributes(): array
    {
        $json = $this->getSchemeJson();
        $attributes = [];

        foreach ($json['tabs'] ?? [] as $tab) {
            if ((string) $tab['tab_code'] !== (string) $this->activeTab) {
                continue;
            }

            foreach ($tab['fields'] ?? [] as $field) {
                if (!empty($field['field_name']) && !empty($field['level_name'])) {
                    $attributes["formData.{$field['field_name']}"] = $field['level_name'];
                }
            }
        }
        return $attributes;
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
    private function checkDuplicateEntries(): bool
    {
        if (!$this->applicationId) {
            $this->ensureApplicationIds();
        }
        $result = DuplicateChecker::check(
            $this->schemeId,
            $this->applicationId,
            $this->formData,
            $this->aadhaarPayload
        );
        if (is_array($result)) {
            $this->addError($result['field'], $result['message']);
            // $this->dispatch('toastr', [
            //     'type' => 'error',
            //     'message' => $result['message']
            // ]);
            return false;
        }
        return true;
    }
    /* ================= RENDER ================= */

    public function render()
    {
        return view('livewire.final-preview');
    }
}
