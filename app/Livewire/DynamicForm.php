<?php

namespace App\Livewire;

use App\Helpers\DuplicateChecker;
use App\Helpers\FormHelper;
use App\Helpers\SchemeCapacityHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\AcceptRejectInfo;
use App\Models\AgeManagements;
use App\Models\BeneficiaryAadhaar;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Ifsccodemaster;
use App\Models\MasterTab;
use App\Models\UniqueAppBenId;
use App\Models\WorkflowsteproleMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Throwable;
use App\Services\WorkflowService;

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

    public $form_preview;

    public array $completedTabs = [];

    public bool $allTabsCompleted = false;

    public $applicationId;

    public $beneficiaryId;

    public $navMessage = null;

    public $navMessageType = 'success';

    public $showFinalModal = false;

    public array $formData = [];

    public bool $aadhaarVerified = false;

    public $aadhaarPayload = [];

    public $filter_data = [];

    public $schemeName;

    public array $appTypeOptions = [];

    public $heading = '';

    public $maxDate;

    public $minDate;

    public $minDOB;

    public $maxDOB;

    public $isEdit = false;

    public $actionType = 0;

    protected $listeners = [
        'document-validation-passed' => 'onDocumentTabPassed',
        'document-validation-failed' => 'onDocumentTabFailed',
        'aadhaarChecked' => 'onAadhaarChecked',
        'aadhaarCheckedReset' => 'onAadhaarCheckedReset',
    ];
    /* ================= MOUNT ================= */

    public function mount($schemeId = null, $schemeName = null, $ram = null, $applicationId = null, $beneficiaryId = null, $form_preview = null)
    {

        if (! WorkFlowPermissionHelper::canCreateEntry()) {
            abort(403, 'You are not authorized to create entry.');
        }
        $this->loadAppTypeOptions();
        $this->loadScheme($schemeId);

        if (! empty($this->views)) {
            $this->activeTab = (string) $this->views[0];
            $this->updateTabNavigation();
        }
        $this->schemeId = $schemeId;
        $this->schemeName = $schemeName;
        $this->heading = 'Government Of West Bengal ' . $this->schemeName . ' Scheme';
        $this->ram = $ram;
        $this->form_preview = $form_preview;
        $this->applicationId = $applicationId;
        $this->beneficiaryId = $beneficiaryId;

        // ✅ EDIT MODE DETECTION
        if ($this->applicationId) {
            $this->aadhaarVerified = true;
            $this->isEdit = true;
            $this->loadExistingApplication();
        }

        // ✅ SET ACTIVE TAB CORRECTLY
        if (! empty($this->views)) {
            $this->setInitialActiveTab();
        }

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
        // ,WorkflowService $workflowService
        // $map = WorkflowsteproleMapping::getMinMaxWorkflowStep($this->schemeId)['min'];
        // $labelRoles = $workflowService->getLabelRoles($map);
        // if ($labelRoles) {
        //     $this->actionType = $labelRoles->next_label_role_id;
        // }
        $select_lgd = session('lgd_session');
        // dd($select_lgd);
        if (! empty($select_lgd['district_id'])) {
            $this->filter_data['created_by_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (! empty($select_lgd['block_id'])) {
            $this->filter_data['created_by_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if (! empty($select_lgd['subdivision_id'])) {
            $this->filter_data['created_by_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
    }

    private function checkCapacity(): bool
    {

        // dd($this->actionType);
        if ($this->isEdit || !empty($this->applicationId)) {
            return true;
        }
        $result = SchemeCapacityHelper::check(
            $this->schemeId,
            $this->actionType,
            $this->formData['application_type']
        );
        if (!$result['is_processed']) {
            $msg = 'Capacity exceeded for ' . ($result['model'] ?? 'Scheme') .
                '! Available: ' . ($result['remaining_capacity'] ?? 0);
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => $msg,
            ]);
            return false;
        }
        return true;
    }

    private function loadExistingApplication(): void
    {
        foreach ($this->views as $tabCode) {

            $tab = MasterTab::where('tab_code', $tabCode)->first();

            if (! $tab || empty($tab->tab_model_name)) {
                continue;
            }

            $modelClass = "App\\Models\\{$tab->tab_model_name}";

            if (! class_exists($modelClass)) {
                continue;
            }

            $record = $modelClass::where('application_id', $this->applicationId)->first();

            if (! $record) {
                continue;
            }

            $this->completedTabs[] = (string) $tabCode;

            $data = $record->toArray();

            /**
             * ----------------------------------------------------
             * Build Confirm Mapping (Based On SAME Rule)
             * ----------------------------------------------------
             */
            $confirmMap = [];

            // IMPORTANT: Set correct tab context
            $this->activeTab = $tabCode;

            $rules = $this->getValidationRulesForActiveTab();

            foreach ($rules as $fieldKey => $ruleSet) {

                $ruleParts = is_array($ruleSet)
                    ? $ruleSet
                    : explode('|', $ruleSet);

                foreach ($ruleParts as $rule) {

                    if (! is_string($rule)) {
                        continue;
                    }

                    if (str_starts_with($rule, 'same:')) {

                        $originalField = str_replace(
                            'formData.',
                            '',
                            substr($rule, 5)
                        );

                        $confirmField = str_replace(
                            'formData.',
                            '',
                            $fieldKey
                        );

                        $confirmMap[$originalField] = $confirmField;
                    }
                }
            }

            /**
             * ----------------------------------------------------
             * Populate Form Data
             * ----------------------------------------------------
             */
            foreach ($data as $key => $value) {

                if ($key === 'other_details') {

                    if (is_string($value)) {
                        $value = json_decode($value, true);
                    }

                    if (is_array($value)) {
                        foreach ($value as $jsonKey => $jsonValue) {

                            $this->formData[$jsonKey] = $jsonValue;

                            if (isset($confirmMap[$jsonKey])) {
                                $this->formData[$confirmMap[$jsonKey]] = $jsonValue;
                            }
                        }
                    }

                    continue;
                }

                $this->formData[$key] = $value;

                if (isset($confirmMap[$key])) {
                    $this->formData[$confirmMap[$key]] = $value;
                }
            }
        }

        if (count($this->completedTabs) === count($this->views)) {
            $this->allTabsCompleted = true;
        }
    }

    private function setInitialActiveTab(): void
    {
        // If edit mode
        if (! empty($this->completedTabs)) {

            $remainingTabs = array_diff($this->views, $this->completedTabs);

            if (! empty($remainingTabs)) {
                $this->activeTab = (string) reset($remainingTabs);
            } else {
                // All tabs completed
                $this->activeTab = (string) end($this->views);
            }
        } else {
            // New entry
            $this->activeTab = (string) $this->views[0];
        }

        $this->updateTabNavigation();
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
        if (! WorkFlowPermissionHelper::canNormalEntryAllow()) {
            unset($options[1]);
        }
        if (! WorkFlowPermissionHelper::canDuareSarkarEntryAllow()) {
            unset($options[2]);
        }
        $this->appTypeOptions = $options;
    }

    public function updatedFormDataAppType($value)
    {
        if (! array_key_exists($value, $this->appTypeOptions)) {
            $this->addError('formData.app_type', 'Unauthorized application type.');
            $this->formData['app_type'] = null;
        }
    }

    public function onAadhaarCheckedReset()
    {
        $this->aadhaarVerified = false;
        $this->aadhaarPayload = [];
        $this->applicationId = null;
        $this->beneficiaryId = null;
        $this->formData = [];
        $this->completedTabs = [];
        $this->allTabsCompleted = false;
        if (! empty($this->views)) {
            $this->activeTab = (string) $this->views[0];
            $this->updateTabNavigation();
        }
    }

    public function onAadhaarChecked($data)
    {
        $this->aadhaarVerified = true;
        $this->aadhaarPayload = [
            'encoded' => $data['encoded'],
            'hash' => $data['hash'],
        ];

        $this->navMessage = null;
        $this->navMessageType = 'success';
        $this->applicationId = null;
        $this->beneficiaryId = null;
        $this->formData = [];
        $this->completedTabs = [];
        $this->allTabsCompleted = false;
        if (! empty($this->views)) {
            $this->activeTab = (string) $this->views[0];
            $this->updateTabNavigation();
        }
    }

    public function setActiveTab($tabCode)
    {
        $tabCode = (string) $tabCode;

        if (! $this->allTabsCompleted) {
            if (
                $tabCode !== $this->activeTab &&
                ! in_array($tabCode, $this->completedTabs, true)
            ) {
                return;
            }
        }

        $this->activeTab = $tabCode;
        $this->updateTabNavigation();
    }

    public function saveAndNext($nextTab)
    {
        if ((string) $this->activeTab === '104') {
            $this->dispatch('check-documents-before-next');

            return;
        }
        $rules = $this->getValidationRulesForActiveTab();
        if (! empty($rules)) {
            $this->validate($rules);
        }
        // Capacity check
        if (! $this->checkCapacity()) {
            return;
        }
        $this->ensureApplicationIds();

        if (! $this->checkDuplicateEntries()) {
            return;
        }

        $saved = $this->saveCurrentTabData();

        if ($saved !== true) {
            return;
        }
        $this->markTabCompleted($this->activeTab);

        if ($nextTab) {
            $this->activeTab = (string) $nextTab;
            $this->updateTabNavigation();
        }
    }
    // public function onDocumentTabPassed()
    // {
    //     $this->markTabCompleted($this->activeTab);
    //     if ($this->nextTab) {
    //         $this->activeTab = (string) $this->nextTab;
    //         $this->updateTabNavigation();
    //     }
    // }

    public function onDocumentTabPassed()
    {
        $this->markTabCompleted($this->activeTab);

        // If this is last tab → open modal
        if ($this->isLast) {

            $tabsData = $this->prepareTabsReviewData();

            $this->dispatch(
                'openFinalModal',
                applicationId: $this->applicationId,
                tabsData: $tabsData,
                schemeId: $this->schemeId
            );

            return;
        }

        // Otherwise go next tab
        if ($this->nextTab) {
            $this->activeTab = (string) $this->nextTab;
            $this->updateTabNavigation();
        }
    }

    public function onDocumentTabFailed() {}

    private function markTabCompleted(string $tabCode): void
    {
        if (! in_array($tabCode, $this->completedTabs, true)) {
            $this->completedTabs[] = $tabCode;
        }
        if (count($this->completedTabs) === count($this->views)) {
            $this->allTabsCompleted = true;
        }
    }
    // public function finalSubmit()
    // {
    //     if ((string) $this->activeTab === '104') {
    //         $this->dispatch('check-documents-before-next');
    //         return;
    //     } else {
    //         $rules = $this->getValidationRulesForActiveTab();
    //         if (!empty($rules)) {
    //             $this->validate($rules);
    //         }
    //         // dd($rules);
    //         $this->ensureApplicationIds();
    //         $this->saveCurrentTabData();
    //     }
    //     $tabsData = $this->prepareTabsReviewData();
    //     if (!$this->checkDuplicateEntries()) {
    //         return;
    //     }
    //     $this->dispatch(
    //         'openFinalModal',
    //         applicationId: $this->applicationId,
    //         tabsData: $tabsData,
    //         schemeId: $this->schemeId
    //     );
    // }

    public function finalSubmit()
    {
        // Last tab check
        if (! $this->isLast) {
            return;
        }
        if (! $this->checkCapacity()) {
            return;
        }
        // If last tab is document tab
        if ((string) $this->activeTab === '104') {
            $this->dispatch('check-documents-before-next');

            return;
        }

        // Normal validation
        $rules = $this->getValidationRulesForActiveTab();

        if (! empty($rules)) {
            $this->validate($rules);
        }

        $this->ensureApplicationIds();
        $this->saveCurrentTabData();

        $tabsData = $this->prepareTabsReviewData();

        if (! $this->checkDuplicateEntries()) {
            return;
        }

        // Open final modal
        $this->dispatch(
            'openFinalModal',
            applicationId: $this->applicationId,
            tabsData: $tabsData,
            schemeId: $this->schemeId
        );
    }

    /* ================= REVIEW DATA ================= */
    private function prepareTabsReviewData()
    {
        $review = [];
        $json = $this->getSchemeJson();

        foreach ($json['tabs'] ?? [] as $tab) {

            $tabCode = (string) ($tab['tab_code'] ?? '');
            $tabName = $tab['tab_name'] ?? 'Tab';

            if (! $tabCode) {
                continue;
            }

            if (! isset($review[$tabCode])) {
                $review[$tabCode] = [
                    'tab_code' => $tabCode,
                    'tab_name' => $tabName,
                    'fields' => [],
                ];
            }
            if ($tabCode === '104') {
                continue;
            }
            foreach ($tab['fields'] ?? [] as $field) {

                if (empty($field['field_name'])) {
                    continue;
                }
                $fieldName = $field['field_name'];

                if (! array_key_exists($fieldName, $this->formData)) {
                    continue;
                }

                $label = $field['level_name']
                    ?? ucfirst(str_replace('_', ' ', $fieldName));

                $value = FormHelper::resolveValue(
                    $field,
                    data_get($this->formData, $fieldName),
                    $this->formData
                );
                $review[$tabCode]['fields'][$label] = $value;
            }
        }

        return $review;
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

    private function saveCurrentTabData(): bool
    {
        if (! $this->applicationId) {
            return false;
        }
        $tab = DB::table('master_tabs')
            ->where('tab_code', $this->activeTab)
            ->first();
        if (! $tab || empty($tab->tab_model_name)) {
            return false;
        }
        $modelClass = "App\\Models\\{$tab->tab_model_name}";
        if (! class_exists($modelClass)) {
            return false;
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
                if (! array_key_exists($fieldName, $this->formData)) {
                    continue;
                }
                if (! empty($field['db_column']) && $field['db_column'] !== 'other_details') {
                    $dbData[$field['db_column']] = $this->formData[$fieldName];
                } elseif (! empty($field['db_column']) && $field['db_column'] == 'other_details') {
                    $otherDetails[$fieldName] = $this->formData[$fieldName];
                } else {
                    continue;
                }
            }
        }
        if (! empty($otherDetails)) {
            $dbData['other_details'] = $otherDetails;
        }
        $model = new $modelClass;
        $tableName = $model->getTable();
        $columns = Cache::remember(
            "Schema_columns_$tableName",
            86400,
            fn() => Schema::getColumnListing($tableName)
        );

        $extraFields = [
            'created_by_dist_code' => $this->filter_data['created_by_dist_code'] ?? null,
            'created_by_local_body_code' => $this->filter_data['created_by_local_body_code'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];
        // dd($extraFields);
        foreach ($extraFields as $column => $value) {
            if (in_array($column, $columns)) {
                $dbData[$column] = $value;
            }
        }
        $dbData = array_intersect_key(
            $dbData,
            array_flip($columns)
        );
        if (! $this->checkDuplicateEntries()) {
            return false;
        }

        DB::beginTransaction();
        try {
            $existingRecord = $modelClass::where('application_id', $this->applicationId)->first();
            if ($existingRecord) {
                $updated = $modelClass::where('application_id', $this->applicationId)
                    ->where('scheme_id', $this->schemeId)
                    ->update($dbData);
                if ($updated) {
                    $this->navMessage = 'Application updated successfully! ID: ' . $this->applicationId;
                    $this->navMessageType = 'success';
                    $this->dispatch('toastr', [
                        'type' => 'success',
                        'message' => 'Application updated successfully. Application ID: ' . $this->applicationId,
                    ]);
                    DB::commit();

                    return true;
                }
            } else {
                $created = $modelClass::create($dbData);
                if ($this->isFirst) {
                    // $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $this->applicationId)->value('beneficiary_id');
                    if ($this->aadhaarVerified && ! empty($this->aadhaarPayload) && $created) {
                        BeneficiaryAadhaar::create(
                            [
                                'application_id' => $this->applicationId,
                                'beneficiary_id' => $this->beneficiaryId,
                                'scheme_id' => $this->schemeId,
                                'aadhar_hash' => $this->aadhaarPayload['hash'],
                                'encoded_aadhar' => $this->aadhaarPayload['encoded'],
                                'encode_key' => null,
                                'aadhar_vault' => $this->aadhaarPayload['hash'],
                            ]
                        );
                    }

                    $AcceptRejectInfo = new AcceptRejectInfo;
                    $AcceptRejectInfo->application_id = $this->applicationId;
                    $AcceptRejectInfo->beneficiary_id = $this->beneficiaryId;
                    $AcceptRejectInfo->ip_address = request()->ip();
                    $AcceptRejectInfo->scheme_id = $this->schemeId;
                    $AcceptRejectInfo->user_id = Auth::id();
                    $AcceptRejectInfo->browser = request()->header('User-Agent');
                    $AcceptRejectInfo->model_name = null;
                    $AcceptRejectInfo->op_type = 1;
                    $AcceptRejectInfo->revert_reason_cause_id = null;
                    $AcceptRejectInfo->revert_reason_remarks = null;
                    $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $this->applicationId)
                        ->latest('id')
                        ->value('id') ?? null;
                    $AcceptRejectInfo->save();
                }

                if ($created) {
                    if ($this->isFirst) {
                        if ($AcceptRejectInfo) {
                            DB::commit();
                            $this->navMessage = 'Application created successfully! ID: ' . $this->applicationId;
                            $this->navMessageType = 'success';

                            $this->dispatch('toastr', [
                                'type' => 'success',
                                'message' => 'Application created successfully. Application ID: ' . $this->applicationId,
                            ]);

                            return true;
                        } else {
                            DB::rollBack();
                            $this->dispatch('toastr', [
                                'type' => 'error',
                                'message' => 'Application not created. Please try again.',
                            ]);

                            return false;
                        }
                    } else {
                        DB::commit();
                        $this->navMessage = 'Application created successfully! ID: ' . $this->applicationId;
                        $this->navMessageType = 'success';
                        $this->dispatch('toastr', [
                            'type' => 'success',
                            'message' => 'Application created successfully. Application ID: ' . $this->applicationId,
                        ]);

                        return true;
                    }
                } else {
                    DB::rollBack();
                    $this->dispatch('toastr', [
                        'type' => 'error',
                        'message' => 'Application not created. Please try again.',
                    ]);

                    return false;
                }
            }
        } catch (Throwable $e) {
            // dd($e);
            DB::rollBack();
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Something went wrong while saving data. Please try again.',
            ]);

            return false;
        }

        return false;
    }

    private function ensureApplicationIds(): void
    {
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
        if (strlen($value) !== 11) {
            $this->formData['bankname'] = '';
            $this->formData['bank_branch_name'] = '';

            return;
        } else {
            $ifsc = strtoupper($value);
            $this->formData['ifscode'] = $ifsc;
            $ifs = Ifsccodemaster::with('bankmaster')
                ->where('code', $ifsc)
                ->where('is_active', 1)
                ->first();
        }
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

        if (! File::exists($path)) {
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

        if (! File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true);
    }

    private function getValidationRulesForActiveTab(): array
    {
        $json = $this->getSchemeJson();
        $rules = [];
        $ageConfig = AgeManagements::where('scheme_id', $this->schemeId)->first();
        foreach ($json['tabs'] ?? [] as $tab) {
            if ((string) $tab['tab_code'] !== (string) $this->activeTab) {
                continue;
            }
            foreach ($tab['fields'] ?? [] as $field) {
                $fieldName = $field['field_name'];
                $fieldRules = explode('|', $field['validation_rule'] ?? '');
                if ($field['field_type'] === 'checkbox') {

                    $fieldRules = array_map(function ($rule) {

                        return $rule === 'required'
                            ? 'accepted'
                            : $rule;
                    }, $fieldRules);
                }
                if ($fieldName === 'age' && $ageConfig) {
                    $fieldRules = array_filter($fieldRules, function ($rule) {
                        $r = trim($rule);

                        return ! str_starts_with($r, 'min:') &&
                            ! str_starts_with($r, 'max:') &&
                            $r !== 'integer' &&
                            $r !== 'numeric';
                    });
                    $fieldRules[] = 'integer';
                    if (! is_null($ageConfig->min_age)) {
                        $fieldRules[] = "min:{$ageConfig->min_age}";
                    }
                    if (! is_null($ageConfig->max_age)) {
                        $fieldRules[] = "max:{$ageConfig->max_age}";
                    }
                }
                if ($fieldName === 'dob' && $ageConfig) {
                    $fieldRules = array_filter($fieldRules, function ($rule) {
                        $r = trim($rule);

                        return ! str_starts_with($r, 'after_or_equal:') &&
                            ! str_starts_with($r, 'before_or_equal:');
                    });
                    if (! is_null($ageConfig->max_age)) {
                        $minDate = now()->subYears($ageConfig->max_age)->format('Y-m-d');
                        $fieldRules[] = "after_or_equal:{$minDate}";
                    }
                    if (! is_null($ageConfig->min_age)) {
                        $maxDate = now()->subYears($ageConfig->min_age)->format('Y-m-d');
                        $fieldRules[] = "before_or_equal:{$maxDate}";
                    }
                }

                $rules["formData.{$fieldName}"] = array_values(array_filter($fieldRules));
            }
        }

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
                if (! empty($field['field_name']) && ! empty($field['level_name'])) {
                    $attributes["formData.{$field['field_name']}"] = $field['level_name'];
                }
            }
        }

        return $attributes;
    }

    protected function messages(): array
    {
        return [

            'formData.*.accepted' => 'Please confirm: :attribute.',
            'formData.*.required' => ':attribute is required.',
            'formData.*.regex' => 'Invalid format for :attribute.',
            'formData.*.numeric' => ':attribute must be a number.',
            'formData.*.date' => ':attribute must be a date.',
            'formData.*.required_if' => ':attribute is required.',
            'formData.*.required_unless' => ':attribute is required.',
        ];
    }

    public function updatedFormDataDob($value)
    {
        if (! empty($value)) {
            $this->formData['age'] = Carbon::parse($value)->age;
        } else {
            $this->formData['age'] = null;
        }
    }

    private function checkDuplicateEntries(): bool
    {
        if (! $this->applicationId) {
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
        return view('livewire.dynamic-form');
    }
}
